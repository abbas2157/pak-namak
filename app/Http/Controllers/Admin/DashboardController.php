<?php
namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\{Sale, Purchase, Shop, Expense, SaleDalla, SaleThaila, SalePackage, EmployeeSalary, Vendor, Employee};
use Illuminate\Support\Facades\DB;

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
        * MONTH SALARIES
        * ------------------------ */
        $monthSalaryTotal = EmployeeSalary::whereYear('month', $monthStart->year)
            ->whereMonth('month', $monthStart->month)
            ->sum('amount');

        $totalSalaryPaid = EmployeeSalary::sum('amount');

        /* -------------------------
        * PENDING (UDHAAR)
        * ------------------------ */
        $totalPending = Sale::sum('pending_amount');
        $monthPending = Sale::whereBetween('sale_date', [$monthStart, $monthEnd])->sum('pending_amount');

        /* -------------------------
        * COUNTS
        * ------------------------ */
        $monthSalesCount  = Sale::whereBetween('sale_date', [$monthStart, $monthEnd])->count();
        $totalSalesCount  = Sale::count();
        $activeShopsCount = Shop::where('status', 'active')->count();
        $totalVendors     = Vendor::count();
        $workingEmployees = Employee::where('status', 'working')->count();

        /* -------------------------
        * MONTH PRODUCT BREAKDOWN
        * ------------------------ */
        $monthDallaTotal = SaleDalla::join('sales', 'sales.id', '=', 'sale_dallas.sale_id')
            ->whereBetween('sales.sale_date', [$monthStart, $monthEnd])
            ->sum('sale_dallas.sub_total');

        $monthThailaTotal = SaleThaila::join('sales', 'sales.id', '=', 'sale_thailas.sale_id')
            ->whereBetween('sales.sale_date', [$monthStart, $monthEnd])
            ->sum('sale_thailas.sub_total');

        $monthPackageTotal = SalePackage::join('sales', 'sales.id', '=', 'sale_packages.sale_id')
            ->whereBetween('sales.sale_date', [$monthStart, $monthEnd])
            ->sum('sale_packages.sub_total');

        /* -------------------------
        * PROFIT / LOSS (sales - purchases - expenses - salaries)
        * ------------------------ */
        $profitLoss = $monthSalesTotal - ($monthPurchasesTotal + $monthExpensesTotal + $monthSalaryTotal);
        $totalProfitLoss = $totalSales - ($PurchasesTotal + $totalExpenses + $totalSalaryPaid);

        /* -------------------------
        * TOP SHOPS + TOP MONTHS + BEST NAMAK TYPE
        * ------------------------ */
        $topShops = Sale::query()
            ->join('shops', 'shops.id', '=', 'sales.shop_id')
            ->select(
                'sales.shop_id',
                'shops.name as shop_name',
                'shops.phone_number as shop_phone_number',
                'shops.address as shop_address',
                DB::raw('SUM(sales.total_amount) as total')
            )
            ->groupBy('sales.shop_id', 'shops.name', 'shops.phone_number', 'shops.address')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Top 5 months by sales amount (group by Y-m)
        $topDays = Sale::query()
            ->select(
                DB::raw("DATE_FORMAT(sale_date, '%Y-%m') as day"),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy(DB::raw("DATE_FORMAT(sale_date, '%Y-%m')"))
            ->orderByDesc('total')
            ->limit(5)
            ->get();



        // Determine which namak type sells more: dalla vs thailas vs packages
        $topDalla = SaleDalla::query()
            ->whereHas('sale', function ($q) use ($monthStart, $monthEnd) {
            })
            ->sum('sub_total');

        $topThailas = SaleThaila::query()
            ->whereHas('sale', function ($q) use ($monthStart, $monthEnd) {
            })
            ->sum('sub_total');

        $topPackages = SalePackage::query()
            ->whereHas('sale', function ($q) use ($monthStart, $monthEnd) {
            })
            ->sum('sub_total');

        $namakBest = 'dallas';
        $namakBestValue = $topDalla;
        if ($topThailas > $namakBestValue) {
            $namakBestValue = $topThailas;
            $namakBest = 'thailas';
        }
        if ($topPackages > $namakBestValue) {
            $namakBestValue = $topPackages;
            $namakBest = 'packages';
        }

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
            'monthSalaryTotal',
            'profitLoss',
            'totalSales',
            'PurchasesTotal',
            'totalExpenses',
            'totalSalaryPaid',
            'totalProfitLoss',
            'totalPending',
            'monthPending',
            'monthSalesCount',
            'totalSalesCount',
            'activeShopsCount',
            'totalVendors',
            'workingEmployees',
            'monthDallaTotal',
            'monthThailaTotal',
            'monthPackageTotal',
            'months',
            'selectedMonth',
            'topShops',
            'topDays',
            'namakBest',
            'namakBestValue'
        ));
    }
}
