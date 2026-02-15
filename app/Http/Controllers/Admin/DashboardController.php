<?php
namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\{Sale, Purchase, Shop, Expense};

class DashboardController extends Controller
{
    public function index() {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd   = Carbon::now()->endOfMonth();

        /* -------------------------
         * MONTH SALES
         * ------------------------ */

        $monthSalesTotal = Sale::whereBetween('sale_date', [$monthStart, $monthEnd])
            ->sum('total_amount');

        /* -------------------------
         * Total SALES
         * ------------------------ */

        $totalSales = Sale::sum('total_amount');

        /* -------------------------
         * TOTAL SHOPS (TILL DATE)
         * ------------------------ */
        $totalShops = Shop::count();

        /* -------------------------
         * THIS MONTH PURCHASES
         * ------------------------ */
        $monthPurchasesTotal = Purchase::whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('grand_total');

        /* -------------------------
         * Total MONTH PURCHASES
         * ------------------------ */
        $PurchasesTotal = Purchase::sum('grand_total');

        /* -------------------------
        * MONTH EXPENSES
        * ------------------------ */
        $monthExpensesTotal = Expense::whereBetween('expense_date', [$monthStart, $monthEnd])
            ->sum('amount');

        /* -------------------------
        * TOTAL EXPENSES (TILL DATE)
        * ------------------------ */
        $totalExpenses = Expense::sum('amount');

        /* -------------------------
         * PROFIT / LOSS
         * ------------------------ */
        $profitLoss = $monthSalesTotal - ($monthPurchasesTotal + $monthExpensesTotal);
        $totalProfitLoss = $totalSales - ($PurchasesTotal + $totalExpenses);

        return view('admin.dashboard.index', compact(
            'totalShops',
            'monthSalesTotal',
            'monthPurchasesTotal',
            'monthExpensesTotal',
            'profitLoss',
            'totalSales',
            'PurchasesTotal',
            'totalExpenses',
            'totalProfitLoss'
        ));
    }
}
