<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleDalla;
use App\Models\SaleThaila;
use App\Models\SalePackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->get('from'); // Y-m-d
        $to = $request->get('to');   // Y-m-d

        $salesQuery = Sale::query();

        if ($from && $to) {
            $salesQuery->whereBetween('sale_date', [$from, $to]);
        }

        $sales = $salesQuery
            ->orderByDesc('sale_date')
            ->get(['id', 'shop_id', 'sale_date', 'total_amount']);

        // Namak totals (Filtered by Sale date)
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
            $dallaAgg->whereHas('sale', function ($q) use ($from, $to) {
                $q->whereBetween('sale_date', [$from, $to]);
            });

            $thailaAgg->whereHas('sale', function ($q) use ($from, $to) {
                $q->whereBetween('sale_date', [$from, $to]);
            });

            $packageAgg->whereHas('sale', function ($q) use ($from, $to) {
                $q->whereBetween('sale_date', [$from, $to]);
            });
        }

        $dallaStats = $dallaAgg->first();
        $thailaStats = $thailaAgg->first();
        $packageStats = $packageAgg->first();

        // Sales totals by shop (Count + Total price)
        $shopAgg = Sale::query()
            ->select([
                'shop_id',
                DB::raw('COUNT(*) as count'),
                DB::raw('COALESCE(SUM(total_amount),0) as total'),
            ])
            ->groupBy('shop_id');

        if ($from && $to) {
            $shopAgg->whereBetween('sale_date', [$from, $to]);
        }

        $salesByShop = $shopAgg->orderByDesc('total')->get();


        return view('admin.sales.report', [
            'sales' => $sales,
            'salesByShop' => $salesByShop,
            'from' => $from,
            'to' => $to,
            'dallaStats' => $dallaStats,
            'thailaStats' => $thailaStats,
            'packageStats' => $packageStats,
        ]);

    }

    public function pdfAll(Request $request)
    {
        // PDF method uses browser print/export (per user choice).
        // We render the same report view; UI has a Print button.
        return $this->index($request);
    }
}

