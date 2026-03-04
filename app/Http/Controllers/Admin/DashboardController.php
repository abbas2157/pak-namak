<?php
namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\{Sale, Purchase, Shop, Expense};

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Selected month (format: 2024-10)
        $selectedMonth = $request->get('month');

        if ($selectedMonth) {
            $monthStart = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
            $monthEnd   = Carbon::createFromFormat('Y-m', $selectedMonth)->endOfMonth();
        } else {
            $monthStart = Carbon::now()->startOfMonth();
            $monthEnd   = Carbon::now()->endOfMonth();
            $selectedMonth = Carbon::now()->format('Y-m');
        }

        /* -------------------------
        * MONTH SALES
        * ------------------------ */
        $monthSalesTotal = Sale::whereBetween('sale_date', [$monthStart, $monthEnd])
            ->sum('total_amount');

        $totalSales = Sale::sum('total_amount');

        /* -------------------------
        * TOTAL SHOPS
        * ------------------------ */
        $totalShops = Shop::count();

        /* -------------------------
        * MONTH PURCHASES
        * ------------------------ */
        $monthPurchasesTotal = Purchase::whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('grand_total');

        $PurchasesTotal = Purchase::sum('grand_total');

        /* -------------------------
        * MONTH EXPENSES
        * ------------------------ */
        $monthExpensesTotal = Expense::whereBetween('expense_date', [$monthStart, $monthEnd])
            ->sum('amount');

        $totalExpenses = Expense::sum('amount');

        /* -------------------------
        * PROFIT / LOSS
        * ------------------------ */
        $profitLoss = $monthSalesTotal - ($monthPurchasesTotal + $monthExpensesTotal);
        $totalProfitLoss = $totalSales - ($PurchasesTotal + $totalExpenses);

        // Generate months list from Oct 2025 till now
        $months = [];
        $startDate = Carbon::create(2025, 10, 1);
        $current = $startDate->copy();

        while ($current <= Carbon::now()) {
            $months[] = [
                'value' => $current->format('Y-m'),
                'label' => $current->format('F Y')
            ];
            $current->addMonth();
        }

        return view('admin.dashboard.index', compact(
            'totalShops',
            'monthSalesTotal',
            'monthPurchasesTotal',
            'monthExpensesTotal',
            'profitLoss',
            'totalSales',
            'PurchasesTotal',
            'totalExpenses',
            'totalProfitLoss',
            'months',
            'selectedMonth'
        ));
    }
}
