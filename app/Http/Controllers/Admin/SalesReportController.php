<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Sale, SaleDalla, SaleThaila, SalePackage};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->get('from');
        $to   = $request->get('to');

        /* ── All Sales ───────────────────────────────── */
        $salesQuery = Sale::with('shop:id,name,phone_number')
            ->orderByDesc('sale_date');

        if ($from && $to) {
            $salesQuery->whereBetween('sale_date', [$from, $to]);
        }

        $sales = $salesQuery->get([
            'id', 'shop_id', 'sale_date',
            'total_amount', 'received_amount', 'pending_amount',
        ]);

        /* ── Grand totals ────────────────────────────── */
        $grandTotal    = $sales->sum('total_amount');
        $grandReceived = $sales->sum('received_amount');
        $grandPending  = $sales->sum('pending_amount');

        /* ── Product type aggregates ─────────────────── */
        $dallaAgg = SaleDalla::query()->select([
            DB::raw('COUNT(*) as count'),
            DB::raw('COALESCE(SUM(sub_total),0) as total'),
        ]);
        $thailaAgg = SaleThaila::query()->select([
            DB::raw('COUNT(*) as count'),
            DB::raw('COALESCE(SUM(sub_total),0) as total'),
        ]);
        $packageAgg = SalePackage::query()->select([
            DB::raw('COUNT(*) as count'),
            DB::raw('COALESCE(SUM(sub_total),0) as total'),
        ]);

        if ($from && $to) {
            foreach ([$dallaAgg, $thailaAgg, $packageAgg] as $q) {
                $q->whereHas('sale', fn($s) => $s->whereBetween('sale_date', [$from, $to]));
            }
        }

        $dallaStats   = $dallaAgg->first();
        $thailaStats  = $thailaAgg->first();
        $packageStats = $packageAgg->first();

        /* ── Sales by Shop (with name) ───────────────── */
        $shopQuery = Sale::query()
            ->join('shops', 'shops.id', '=', 'sales.shop_id')
            ->select([
                'sales.shop_id',
                'shops.name as shop_name',
                'shops.phone_number as shop_phone',
                DB::raw('COUNT(*) as count'),
                DB::raw('COALESCE(SUM(sales.total_amount),0) as total'),
                DB::raw('COALESCE(SUM(sales.received_amount),0) as received'),
                DB::raw('COALESCE(SUM(sales.pending_amount),0) as pending'),
            ])
            ->groupBy('sales.shop_id', 'shops.name', 'shops.phone_number')
            ->orderByDesc('total');

        if ($from && $to) {
            $shopQuery->whereBetween('sales.sale_date', [$from, $to]);
        }

        $salesByShop = $shopQuery->get();

        return view('admin.sales.report', compact(
            'sales', 'salesByShop',
            'from', 'to',
            'dallaStats', 'thailaStats', 'packageStats',
            'grandTotal', 'grandReceived', 'grandPending',
        ));
    }

    public function pdfAll(Request $request)
    {
        return $this->index($request);
    }
}
