<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Sale, Shop, SaleDalla, SaleThaila, SalePackage};
use Illuminate\Http\Request;

class ShopSalesController extends Controller
{
    public function index(Request $request)
    {
        $shopId        = $request->get('shop_id');
        $selectedMonth = $request->get('month');

        $shops = Shop::orderBy('name')->get(['id', 'name', 'owner_name', 'phone_number', 'city', 'status']);

        $shop          = null;
        $sales         = collect();
        $months        = collect();
        $dallaStats    = null;
        $thailaStats   = null;
        $packageStats  = null;
        $totalRevenue  = 0;
        $totalReceived = 0;
        $totalPending  = 0;

        if ($shopId) {
            $shop = Shop::findOrFail($shopId);

            $query = Sale::with(['dalla', 'thailas', 'packages'])
                ->where('shop_id', $shop->id);

            if ($selectedMonth) {
                [$year, $month] = explode('-', $selectedMonth);
                $query->whereYear('sale_date', $year)->whereMonth('sale_date', $month);
            }

            $sales = $query->orderByDesc('sale_date')->get();

            $totalRevenue  = $sales->sum('total_amount');
            $totalReceived = $sales->sum('received_amount');
            $totalPending  = $sales->sum('pending_amount');

            $months = Sale::where('shop_id', $shop->id)
                ->whereNotNull('sale_date')
                ->selectRaw("DATE_FORMAT(sale_date,'%Y-%m') as value, DATE_FORMAT(sale_date,'%M %Y') as label")
                ->groupBy('value', 'label')
                ->orderByDesc('value')
                ->get();

            $dallaStats = SaleDalla::join('sales', 'sales.id', '=', 'sale_dallas.sale_id')
                ->where('sales.shop_id', $shop->id)
                ->when($selectedMonth, function ($q) use ($selectedMonth) {
                    [$y, $m] = explode('-', $selectedMonth);
                    $q->whereYear('sales.sale_date', $y)->whereMonth('sales.sale_date', $m);
                })
                ->selectRaw('COUNT(*) as count, COALESCE(SUM(sale_dallas.sub_total),0) as total, COALESCE(SUM(sale_dallas.quantity_kg),0) as total_kg')
                ->first();

            $thailaStats = SaleThaila::join('sales', 'sales.id', '=', 'sale_thailas.sale_id')
                ->where('sales.shop_id', $shop->id)
                ->when($selectedMonth, function ($q) use ($selectedMonth) {
                    [$y, $m] = explode('-', $selectedMonth);
                    $q->whereYear('sales.sale_date', $y)->whereMonth('sales.sale_date', $m);
                })
                ->selectRaw('COUNT(*) as count, COALESCE(SUM(sale_thailas.sub_total),0) as total, COALESCE(SUM(sale_thailas.total_kg),0) as total_kg')
                ->first();

            $packageStats = SalePackage::join('sales', 'sales.id', '=', 'sale_packages.sale_id')
                ->where('sales.shop_id', $shop->id)
                ->when($selectedMonth, function ($q) use ($selectedMonth) {
                    [$y, $m] = explode('-', $selectedMonth);
                    $q->whereYear('sales.sale_date', $y)->whereMonth('sales.sale_date', $m);
                })
                ->selectRaw('COUNT(*) as count, COALESCE(SUM(sale_packages.sub_total),0) as total, COALESCE(SUM(sale_packages.total_kg),0) as total_kg')
                ->first();
        }

        return view('admin.shops.sales2', compact(
            'shops', 'shop', 'sales',
            'dallaStats', 'thailaStats', 'packageStats',
            'totalRevenue', 'totalReceived', 'totalPending',
            'months', 'selectedMonth'
        ));
    }
}
