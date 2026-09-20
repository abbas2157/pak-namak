<?php
namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\{Sale, Purchase, Shop, Expense, SaleDalla, SaleThaila, SalePackage, EmployeeSalary, Vendor, Employee, Order, City, CashLedger, Asset, Production, SpiceProduction, SpiceSale, SpicePurchase, SpiceOrder, PackagingPurchase};
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
        * MONTH PURCHASES (all purchases, salt + spice — investment-flagged
        * ones still count here and in the P&L below; "investment" is just
        * a category tag, tracked separately on the Investments page, not
        * an exclusion from the operating numbers)
        * ------------------------ */
        $monthSaltPurchasesTotal = Purchase::whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('grand_total');
        $monthSpicePurchasesTotal = SpicePurchase::whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('grand_total');
        $monthPurchasesTotal = $monthSaltPurchasesTotal + $monthSpicePurchasesTotal;

        $saltPurchasesTotal  = Purchase::sum('grand_total');
        $spicePurchasesTotal = SpicePurchase::sum('grand_total');
        $PurchasesTotal = $saltPurchasesTotal + $spicePurchasesTotal;

        /* -------------------------
        * MONTH EXPENSES (operating only, same reasoning)
        *
        * Packaging purchases (empty thaila bags / packets) are a consumable
        * operating cost rather than resellable stock, so they're counted
        * here with expenses instead of in the Purchases bucket above.
        * ------------------------ */
        $monthPackagingTotal = PackagingPurchase::whereBetween('purchase_date', [$monthStart, $monthEnd])
            ->where('is_investment', false)
            ->sum('grand_total');

        $packagingTotal = PackagingPurchase::where('is_investment', false)->sum('grand_total');

        $monthExpensesTotal = Expense::whereBetween('expense_date', [$monthStart, $monthEnd])
            ->where('is_investment', false)
            ->sum('amount')
            + $monthPackagingTotal;

        $totalExpenses = Expense::where('is_investment', false)->sum('amount') + $packagingTotal;

        /* -------------------------
        * MONTH / TOTAL INVESTMENT (capital put into the business via
        * investment-flagged expenses, assets, and purchases)
        * ------------------------ */
        $monthInvestmentTotal =
            Expense::whereBetween('expense_date', [$monthStart, $monthEnd])->where('is_investment', true)->sum('amount')
            + Purchase::whereBetween('created_at', [$monthStart, $monthEnd])->where('is_investment', true)->sum('grand_total')
            + SpicePurchase::whereBetween('created_at', [$monthStart, $monthEnd])->where('is_investment', true)->sum('grand_total')
            + PackagingPurchase::whereBetween('purchase_date', [$monthStart, $monthEnd])->where('is_investment', true)->sum('grand_total')
            + Asset::whereBetween('purchase_date', [$monthStart, $monthEnd])->where('is_investment', true)->get()
                ->sum(fn ($a) => $a->quantity * $a->purchase_price);

        $totalInvestment =
            Expense::where('is_investment', true)->sum('amount')
            + Purchase::where('is_investment', true)->sum('grand_total')
            + SpicePurchase::where('is_investment', true)->sum('grand_total')
            + PackagingPurchase::where('is_investment', true)->sum('grand_total')
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
        * raw salt + raw spice — a real operating cost, subtracted from profit
        * below; salt and spice production are separate modules, combined here
        * the same way sales/purchases are)
        * ------------------------ */
        $monthProductionCost = Production::whereBetween('production_date', [$monthStart, $monthEnd])
            ->sum('electricity_fuel_cost')
            + SpiceProduction::whereBetween('production_date', [$monthStart, $monthEnd])
            ->sum('electricity_fuel_cost');

        $totalProductionCost = Production::sum('electricity_fuel_cost')
            + SpiceProduction::sum('electricity_fuel_cost');

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
        // Both product lines count toward a shop's ranking — salt-only totals
        // buried shops that buy mostly spices.
        $saltByShop  = Sale::query()->groupBy('shop_id')->pluck(DB::raw('SUM(total_amount)'), 'shop_id');
        $spiceByShop = SpiceSale::query()->groupBy('shop_id')->pluck(DB::raw('SUM(total_amount)'), 'shop_id');

        $totalsByShop = collect($saltByShop)
            ->mapWithKeys(fn ($total, $shopId) => [$shopId => (float) $total])
            ->mergeRecursive(collect($spiceByShop)->mapWithKeys(fn ($total, $shopId) => [$shopId => (float) $total]))
            ->map(fn ($total) => is_array($total) ? array_sum($total) : (float) $total)
            ->sortDesc()
            ->take(5);

        $topShopRecords = Shop::whereIn('id', $totalsByShop->keys())->get()->keyBy('id');

        $topShops = $totalsByShop->map(fn ($total, $shopId) => (object) [
            'shop_id'           => $shopId,
            'shop_name'         => $topShopRecords[$shopId]->name ?? 'Unknown Shop',
            'shop_phone_number' => $topShopRecords[$shopId]->phone_number ?? null,
            'shop_address'      => $topShopRecords[$shopId]->address ?? null,
            'total'             => $total,
        ])->values();

        // Top 5 months by sales amount (group by Y-m), salt + spices
        $saltByMonth = Sale::query()
            ->groupBy(DB::raw("DATE_FORMAT(sale_date, '%Y-%m')"))
            ->pluck(DB::raw('SUM(total_amount)'), DB::raw("DATE_FORMAT(sale_date, '%Y-%m')"));
        $spiceByMonth = SpiceSale::query()
            ->groupBy(DB::raw("DATE_FORMAT(sale_date, '%Y-%m')"))
            ->pluck(DB::raw('SUM(total_amount)'), DB::raw("DATE_FORMAT(sale_date, '%Y-%m')"));

        $topDays = collect($saltByMonth)->keys()
            ->merge(collect($spiceByMonth)->keys())
            ->unique()
            ->filter()
            ->map(fn ($month) => (object) [
                'day'   => $month,
                'total' => (float) ($saltByMonth[$month] ?? 0) + (float) ($spiceByMonth[$month] ?? 0),
            ])
            ->sortByDesc('total')
            ->take(5)
            ->values();



        /* -------------------------
        * CITY & AREA SALES BREAKDOWN
        * ------------------------ */
        // Run the same aggregate over each product line's table, then merge —
        // a city's turnover is salt plus spices, not salt alone.
        $cityRowsFor = fn (string $model, string $table) => $model::query()
            ->join('shops', 'shops.id', '=', "{$table}.shop_id")
            ->join('cities', 'cities.id', '=', 'shops.city_id')
            ->whereBetween("{$table}.sale_date", [$monthStart, $monthEnd])
            ->select(
                'cities.id as city_id',
                'cities.name as city_name',
                DB::raw("SUM({$table}.total_amount) as total"),
                DB::raw("SUM({$table}.pending_amount) as pending"),
                DB::raw("SUM({$table}.received_amount) as received"),
                DB::raw("COUNT({$table}.id) as count")
            )
            ->groupBy('cities.id', 'cities.name')
            ->get();

        $citySales = $cityRowsFor(Sale::class, 'sales')
            ->concat($cityRowsFor(SpiceSale::class, 'spice_sales'))
            ->groupBy('city_id')
            ->map(fn ($rows) => (object) [
                'city_id'   => $rows->first()->city_id,
                'city_name' => $rows->first()->city_name,
                'total'     => $rows->sum('total'),
                'pending'   => $rows->sum('pending'),
                'received'  => $rows->sum('received'),
                'count'     => $rows->sum('count'),
            ])
            ->sortByDesc('total')
            ->values();

        $areaRowsFor = fn (string $model, string $table) => $model::query()
            ->join('shops', 'shops.id', '=', "{$table}.shop_id")
            ->join('areas', 'areas.id', '=', 'shops.area_id')
            ->join('cities', 'cities.id', '=', 'areas.city_id')
            ->whereBetween("{$table}.sale_date", [$monthStart, $monthEnd])
            ->select(
                'areas.id as area_id',
                'areas.name as area_name',
                'cities.name as city_name',
                DB::raw("SUM({$table}.total_amount) as total"),
                DB::raw("SUM({$table}.pending_amount) as pending"),
                DB::raw("SUM({$table}.received_amount) as received"),
                DB::raw("COUNT({$table}.id) as count")
            )
            ->groupBy('areas.id', 'areas.name', 'cities.name')
            ->get();

        $areaSales = $areaRowsFor(Sale::class, 'sales')
            ->concat($areaRowsFor(SpiceSale::class, 'spice_sales'))
            ->groupBy('area_id')
            ->map(fn ($rows) => (object) [
                'area_id'   => $rows->first()->area_id,
                'area_name' => $rows->first()->area_name,
                'city_name' => $rows->first()->city_name,
                'total'     => $rows->sum('total'),
                'pending'   => $rows->sum('pending'),
                'received'  => $rows->sum('received'),
                'count'     => $rows->sum('count'),
            ])
            ->sortByDesc('total')
            ->take(5)
            ->values();

        /* -------------------------
        * INACTIVE SHOPS (no sale in last 30 days)
        * ------------------------ */
        // A shop that only buys spices is still an active customer — checking
        // salt sales alone put regular spice buyers on the chase list.
        $inactiveCutoff = Carbon::now()->subDays(30)->toDateString();

        $inactiveShops = Shop::where('status', 'active')
            ->whereDoesntHave('sales', fn ($q) => $q->where('sale_date', '>=', $inactiveCutoff))
            ->whereDoesntHave('spiceSales', fn ($q) => $q->where('sale_date', '>=', $inactiveCutoff))
            ->with('cityRecord', 'area')
            ->withMax('sales', 'sale_date')
            ->withMax('spiceSales', 'sale_date')
            ->get()
            ->each(function (Shop $shop) {
                // The view reads sales_max_sale_date — make it the true last
                // trading date across both product lines.
                $shop->sales_max_sale_date = max(
                    $shop->sales_max_sale_date,
                    $shop->spice_sales_max_sale_date
                );
            })
            ->sortBy('sales_max_sale_date')
            ->values();

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
