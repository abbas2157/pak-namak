<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopSalesController extends Controller
{
    public function index(Request $request)
    {
        $shopId = $request->get('shop_id');

        $shop = null;
        $sales = collect();

        if ($shopId) {
            $shop = Shop::findOrFail($shopId);

            $sales = Sale::query()
                ->where('shop_id', $shop->id)
                ->orderByDesc('sale_date')
                ->get(['id', 'shop_id', 'sale_date', 'total_amount']);

            // Dalla / Thailas / Packages totals for this shop
            $dallaStats = \App\Models\SaleDalla::query()
                ->join('sales', 'sales.id', '=', 'sale_dallas.sale_id')
                ->where('sales.shop_id', $shop->id)
                ->selectRaw('COUNT(*) as count, COALESCE(SUM(sub_total),0) as total')
                ->first();

            $thailaStats = \App\Models\SaleThaila::query()
                ->join('sales', 'sales.id', '=', 'sale_thailas.sale_id')
                ->where('sales.shop_id', $shop->id)
                ->selectRaw('COUNT(*) as count, COALESCE(SUM(sub_total),0) as total')
                ->first();

            $packageStats = \App\Models\SalePackage::query()
                ->join('sales', 'sales.id', '=', 'sale_packages.sale_id')
                ->where('sales.shop_id', $shop->id)
                ->selectRaw('COUNT(*) as count, COALESCE(SUM(sub_total),0) as total')
                ->first();
        }

        return view('admin.shops.sales2', compact('shop', 'sales', 'dallaStats', 'thailaStats', 'packageStats'));
    }
}



