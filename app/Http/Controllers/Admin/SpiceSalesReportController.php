<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{SpiceSale, SpiceSaleItem};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpiceSalesReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->get('from');
        $to   = $request->get('to');

        $salesQuery = SpiceSale::with('shop:id,name,phone_number')
            ->orderByDesc('sale_date');

        if ($from && $to) {
            $salesQuery->whereBetween('sale_date', [$from, $to]);
        }

        $sales = $salesQuery->get([
            'id', 'shop_id', 'sale_date',
            'total_amount', 'received_amount', 'pending_amount',
        ]);

        $grandTotal    = $sales->sum('total_amount');
        $grandReceived = $sales->sum('received_amount');
        $grandPending  = $sales->sum('pending_amount');

        // Revenue split by spice type — the spice equivalent of salt's
        // Dalla/Thaila/Package breakdown. Scoped through the parent sale so
        // line items never outlive their sale in these totals.
        $byType = SpiceSaleItem::query()
            ->join('spice_types', 'spice_types.id', '=', 'spice_sale_items.spice_type_id')
            ->whereHas('sale', function ($s) use ($from, $to) {
                if ($from && $to) {
                    $s->whereBetween('sale_date', [$from, $to]);
                }
            })
            ->select([
                'spice_types.id as type_id',
                'spice_types.title as type_name',
                DB::raw('COUNT(*) as count'),
                DB::raw('COALESCE(SUM(spice_sale_items.quantity),0) as packets'),
                DB::raw('COALESCE(SUM(spice_sale_items.total_kg),0) as total_kg'),
                DB::raw('COALESCE(SUM(spice_sale_items.sub_total),0) as total'),
            ])
            ->groupBy('spice_types.id', 'spice_types.title')
            ->orderByDesc('total')
            ->get();

        $shopQuery = SpiceSale::query()
            ->join('shops', 'shops.id', '=', 'spice_sales.shop_id')
            ->select([
                'spice_sales.shop_id',
                'shops.name as shop_name',
                'shops.phone_number as shop_phone',
                DB::raw('COUNT(*) as count'),
                DB::raw('COALESCE(SUM(spice_sales.total_amount),0) as total'),
                DB::raw('COALESCE(SUM(spice_sales.received_amount),0) as received'),
                DB::raw('COALESCE(SUM(spice_sales.pending_amount),0) as pending'),
            ])
            ->groupBy('spice_sales.shop_id', 'shops.name', 'shops.phone_number')
            ->orderByDesc('total');

        if ($from && $to) {
            $shopQuery->whereBetween('spice_sales.sale_date', [$from, $to]);
        }

        $salesByShop = $shopQuery->get();

        return view('admin.spice-sales.report', compact(
            'sales', 'salesByShop', 'byType',
            'from', 'to',
            'grandTotal', 'grandReceived', 'grandPending',
        ));
    }
}
