<?php
namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\{Sale, Purchase, Shop, Expense, SaleDalla, SaleThaila, SalePackage, EmployeeSalary, Vendor, Employee, Order, City, CashLedger, Asset, Production, SpiceSale, SpicePurchase, SpiceOrder};
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
        * MONTH SALES (salt + spices combined, since overall business
        * profit should reflect both product lines together)
        * ------------------------ */
        $monthSaltSalesTotal = Sale::whereBetween('sale_date', [$monthStart, $monthEnd])
            ->sum('total_amount');
        $monthSpiceSalesTotal = SpiceSale::whereBetween('sale_date', [$monthStart, $monthEnd])
            ->sum('total_amount');
        $monthSalesTotal = $monthSaltSalesTotal + $monthSpiceSalesTotal;

        $totalSaltSales  = Sale::sum('total_amount');
        $totalSpiceSales = SpiceSale::sum('total_amount');
        $totalSales = $totalSaltSales + $totalSpiceSales;

        /* -------------------------
        * TOTAL SHOPS
        * ------------------------ */
        $totalShops = Shop::count();

        /* -------------------------
        * MONTH PURCHASES (operating only — investment-flagged purchases
        * are capital expenditure, excluded from the P&L below)
        * ------------------------ */
        $monthSaltPurchasesTotal = Purchase::whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('is_investment', false)
            ->sum('grand_total');
        $monthSpicePurchasesTotal = SpicePurchase::whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('is_investment', false)
            ->sum('grand_total');
        $monthPurchasesTotal = $monthSaltPurchasesTotal + $monthSpicePurchasesTotal;

        $saltPurchasesTotal  = Purchase::where('is_investment', false)->sum('grand_total');
        $spicePurchasesTotal = SpicePurchase::where('is_investment', false)->sum('grand_total');
        $PurchasesTotal = $saltPurchasesTotal + $spicePurchasesTotal;

        /* -------------------------
        * MONTH EXPENSES (operating only, same reasoning)
        * ------------------------ */
        $monthExpensesTotal = Expense::whereBetween('expense_date', [$monthStart, $monthEnd])
            ->where('is_investment', false)
            ->sum('amount');

        $totalExpenses = Expense::where('is_investment', false)->sum('amount');

        /* -------------------------
        * MONTH / TOTAL INVESTMENT (capital put into the business via
        * investment-flagged expenses, assets, and purchases)
        * ------------------------ */
        $monthInvestmentTotal =
            Expense::whereBetween('expense_date', [$monthStart, $monthEnd])->where('is_investment', true)->sum('amount')
            + Purchase::whereBetween('created_at', [$monthStart, $monthEnd])->where('is_investment', true)->sum('grand_total')
            + SpicePurchase::whereBetween('created_at', [$monthStart, $monthEnd])->where('is_investment', true)->sum('grand_total')
            + Asset::whereBetween('purchase_date', [$monthStart, $monthEnd])->where('is_investment', true)->get()
                ->sum(fn ($a) => $a->quantity * $a->purchase_price);

        $totalInvestment =
            Expense::where('is_investment', true)->sum('amount')
            + Purchase::where('is_investment', true)->sum('grand_total')
            + SpicePurchase::where('is_investment', true)->sum('grand_total')
            + Asset::where('is_investment', true)->get()->sum(fn ($a) => $a->quantity * $a->purchase_price);

        /* -------------------------
        * MONTH SALARIES
        * ------------------------ */
        $monthSalaryTotal = EmployeeSalary::whereYear('month', $monthStart->year)
            ->whereMonth('month', $monthStart->month)
            ->sum('amount');

        $totalSalaryPaid = EmployeeSalary::sum('amount');

        /* -------------------------
        * MONTH / TOTAL PRODUCTION COST (electricity/fuel spent processing
        * raw salt — a real operating cost, subtracted from profit below)
        * ------------------------ */
        $monthProductionCost = Production::whereBetween('production_date', [$monthStart, $monthEnd])
            ->sum('electricity_fuel_cost');

        $totalProductionCost = Production::sum('electricity_fuel_cost');

        /* -------------------------
        * PENDING (UDHAAR)
        * ------------------------ */
        $totalSaltPending  = Sale::sum('pending_amount');
        $totalSpicePending = SpiceSale::sum('pending_amount');
        $totalPending = $totalSaltPending + $totalSpicePending;

        $monthSaltPending  = Sale::whereBetween('sale_date', [$monthStart, $monthEnd])->sum('pending_amount');
        $monthSpicePending = SpiceSale::whereBetween('sale_date', [$monthStart, $monthEnd])->sum('pending_amount');
        $monthPending = $monthSaltPending + $monthSpicePending;

        /* -------------------------
        * COUNTS
        * ------------------------ */
        $monthSalesCount  = Sale::whereBetween('sale_date', [$monthStart, $monthEnd])->count();
        $totalSalesCount  = Sale::count();

        $monthSpiceSalesCount = SpiceSale::whereBetween('sale_date', [$monthStart, $monthEnd])->count();
        $totalSpiceSalesCount = SpiceSale::count();

        $activeShopsCount = Shop::where('status', 'active')->count();
        $totalVendors     = Vendor::count();
        $workingEmployees = Employee::where('status', 'working')->count();

        /* -------------------------
        * ORDERS
        * ------------------------ */
        $pendingOrdersCount   = Order::where('status', 'pending')->count();
        $confirmedOrdersCount = Order::where('status', 'confirmed')->count();
        $totalOrdersCount     = Order::count();
        $recentPendingOrders  = Order::with(['shop', 'items'])
            ->where('status', 'pending')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        /* -------------------------
        * SPICE ORDERS
        * ------------------------ */
        $pendingSpiceOrdersCount   = SpiceOrder::where('status', 'pending')->count();
        $confirmedSpiceOrdersCount = SpiceOrder::where('status', 'confirmed')->count();
        $totalSpiceOrdersCount     = SpiceOrder::count();
        $recentPendingSpiceOrders  = SpiceOrder::with(['shop', 'items.spiceType'])
            ->where('status', 'pending')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

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
        * PROFIT / LOSS (sales - purchases - expenses - salaries - production costs)
        * ------------------------ */
        $profitLoss = $monthSalesTotal - ($monthPurchasesTotal + $monthExpensesTotal + $monthSalaryTotal + $monthProductionCost);
        $totalProfitLoss = $totalSales - ($PurchasesTotal + $totalExpenses + $totalSalaryPaid + $totalProductionCost);

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



        /* -------------------------
        * CITY & AREA SALES BREAKDOWN
        * ------------------------ */
        $citySales = Sale::query()
            ->join('shops', 'shops.id', '=', 'sales.shop_id')
            ->join('cities', 'cities.id', '=', 'shops.city_id')
            ->whereBetween('sales.sale_date', [$monthStart, $monthEnd])
            ->select(
                'cities.id as city_id',
                'cities.name as city_name',
                DB::raw('SUM(sales.total_amount) as total'),
                DB::raw('SUM(sales.pending_amount) as pending'),
                DB::raw('SUM(sales.received_amount) as received'),
                DB::raw('COUNT(sales.id) as count')
            )
            ->groupBy('cities.id', 'cities.name')
            ->orderByDesc('total')
            ->get();

        $areaSales = Sale::query()
            ->join('shops', 'shops.id', '=', 'sales.shop_id')
            ->join('areas', 'areas.id', '=', 'shops.area_id')
            ->join('cities', 'cities.id', '=', 'areas.city_id')
            ->whereBetween('sales.sale_date', [$monthStart, $monthEnd])
            ->select(
                'areas.id as area_id',
                'areas.name as area_name',
                'cities.name as city_name',
                DB::raw('SUM(sales.total_amount) as total'),
                DB::raw('SUM(sales.pending_amount) as pending'),
                DB::raw('SUM(sales.received_amount) as received'),
                DB::raw('COUNT(sales.id) as count')
            )
            ->groupBy('areas.id', 'areas.name', 'cities.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        /* -------------------------
        * INACTIVE SHOPS (no sale in last 30 days)
        * ------------------------ */
        $inactiveShops = Shop::where('status', 'active')
            ->whereDoesntHave('sales', function ($q) {
                $q->where('sale_date', '>=', Carbon::now()->subDays(30)->toDateString());
            })
            ->with('cityRecord', 'area')
            ->withMax('sales', 'sale_date')
            ->orderBy('sales_max_sale_date')
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

        $cashBalance = CashLedger::currentBalance();

        return view('admin.dashboard.index', compact(
            'cashBalance',
            'totalShops',
            'monthSalesTotal',
            'monthPurchasesTotal',
            'monthExpensesTotal',
            'monthSalaryTotal',
            'monthInvestmentTotal',
            'totalInvestment',
            'monthProductionCost',
            'totalProductionCost',
            'profitLoss',
            'totalSales',
            'PurchasesTotal',
            'totalExpenses',
            'totalSalaryPaid',
            'totalProfitLoss',
            'totalPending',
            'monthPending',
            'totalSaltPending',
            'totalSpicePending',
            'monthSaltPending',
            'monthSpicePending',
            'monthSalesCount',
            'totalSalesCount',
            'monthSpiceSalesCount',
            'totalSpiceSalesCount',
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
            'namakBestValue',
            'pendingOrdersCount',
            'confirmedOrdersCount',
            'totalOrdersCount',
            'recentPendingOrders',
            'pendingSpiceOrdersCount',
            'confirmedSpiceOrdersCount',
            'totalSpiceOrdersCount',
            'recentPendingSpiceOrders',
            'citySales',
            'areaSales',
            'inactiveShops',
            'monthSaltSalesTotal',
            'monthSpiceSalesTotal',
            'totalSaltSales',
            'totalSpiceSales',
            'monthSaltPurchasesTotal',
            'monthSpicePurchasesTotal',
            'saltPurchasesTotal',
            'spicePurchasesTotal',
        ));
    }
}
