<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Day-by-day sheets for one month — one for sales, one for production —
 * salt and spices side by side on each. Every calendar day is listed
 * (quiet days included) so gaps in the month are visible at a glance, and
 * every total is compared with the previous month.
 *
 * Packages are counted in packets (bundle size × bundle count) on both the
 * sales and production side — same reasoning as MonthlyComparisonReportController.
 */
class DailyReportController extends Controller
{
    private const SALES_KEYS = [
        'salt_bills', 'salt_amount', 'salt_received', 'dalla', 'thaila', 'packets',
        'spice_bills', 'spice_amount', 'spice_received', 'spice_packets', 'spice_kg',
        'total_amount', 'total_bills',
    ];

    private const PRODUCTION_KEYS = [
        'sp_batches', 'sp_raw', 'sp_finished', 'sp_thaila', 'sp_packets', 'sp_kg',
        'spp_batches', 'spp_raw', 'spp_finished', 'spp_packets', 'spp_kg',
        'total_finished', 'total_packets',
    ];

    public function sales(Request $request)
    {
        $ctx = $this->context($request, self::SALES_KEYS, fn ($r) => $r['salt_bills'] || $r['spice_bills']);

        $ctx['highLow'] = [
            'salt' => $this->highLow($ctx['rows'], fn ($r) => $r['salt_amount']),
            'spice' => $this->highLow($ctx['rows'], fn ($r) => $r['spice_amount']),
            'total' => $this->highLow($ctx['rows'], fn ($r) => $r['total_amount']),
        ];
        $ctx['hiDay'] = $ctx['highLow']['total']['high']['date'] ?? null;
        $ctx['loDay'] = $ctx['highLow']['total']['low']['date'] ?? null;

        $ctx['charts'] = [
            'days' => $ctx['rows']->map(fn ($r) => $r['date']->format('j'))->values()->all(),
            'salt_amount' => $ctx['rows']->pluck('salt_amount')->values()->all(),
            'spice_amount' => $ctx['rows']->pluck('spice_amount')->values()->all(),
            'salt_bills' => $ctx['rows']->pluck('salt_bills')->values()->all(),
            'spice_bills' => $ctx['rows']->pluck('spice_bills')->values()->all(),
        ] + $this->running($ctx['rows'], $ctx['prevRows'], 'total_amount');

        return view('admin.reports.daily-sales', $ctx);
    }

    public function production(Request $request)
    {
        $ctx = $this->context($request, self::PRODUCTION_KEYS, fn ($r) => $r['sp_batches'] || $r['spp_batches']);

        $ctx['highLow'] = [
            'salt' => $this->highLow($ctx['rows'], fn ($r) => $r['sp_finished']),
            'spice' => $this->highLow($ctx['rows'], fn ($r) => $r['spp_finished']),
            'total' => $this->highLow($ctx['rows'], fn ($r) => $r['total_finished']),
        ];
        $ctx['hiDay'] = $ctx['highLow']['total']['high']['date'] ?? null;
        $ctx['loDay'] = $ctx['highLow']['total']['low']['date'] ?? null;

        $ctx['charts'] = [
            'days' => $ctx['rows']->map(fn ($r) => $r['date']->format('j'))->values()->all(),
            'sp_finished' => $ctx['rows']->pluck('sp_finished')->values()->all(),
            'spp_finished' => $ctx['rows']->pluck('spp_finished')->values()->all(),
            'sp_thaila' => $ctx['rows']->pluck('sp_thaila')->values()->all(),
            'sp_packets' => $ctx['rows']->pluck('sp_packets')->values()->all(),
            'spp_packets' => $ctx['rows']->pluck('spp_packets')->values()->all(),
        ] + $this->running($ctx['rows'], $ctx['prevRows'], 'total_finished');

        return view('admin.reports.daily-production', $ctx);
    }

    /** Everything both reports share: month, rows for it and the month before, totals, % changes. */
    private function context(Request $request, array $keys, callable $isActive): array
    {
        $month = $request->month ? Carbon::createFromFormat('Y-m', $request->month) : now();
        $prevMonth = $month->copy()->subMonthNoOverflow();

        $rows = $this->rowsFor($month);
        $prevRows = $this->rowsFor($prevMonth);

        $totals = collect($keys)->mapWithKeys(fn ($k) => [$k => $rows->sum($k)]);
        $prevTotals = collect($keys)->mapWithKeys(fn ($k) => [$k => $prevRows->sum($k)]);

        $activeDays = $rows->filter($isActive)->count();
        $prevActiveDays = $prevRows->filter($isActive)->count();

        // % change vs previous month; null when there is nothing to compare against.
        $pct = fn ($cur, $prev) => $prev > 0 ? round(($cur - $prev) / $prev * 100, 1) : null;
        $changes = collect($keys)->mapWithKeys(fn ($k) => [$k => $pct((float) $totals[$k], (float) $prevTotals[$k])]);
        $changes['active_days'] = $pct($activeDays, $prevActiveDays);

        return compact('month', 'prevMonth', 'rows', 'prevRows', 'totals', 'prevTotals', 'changes', 'activeDays')
            + ['months' => $this->availableMonths(), 'isActive' => $isActive];
    }

    /** One row per calendar day of the month (up to today for the current month). */
    private function rowsFor(Carbon $month): Collection
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $range = [$start->toDateString(), $end->toDateString()];

        $saltSales = DB::table('sales')
            ->whereBetween('sale_date', $range)
            ->selectRaw('sale_date as d, COUNT(*) as bills, COALESCE(SUM(total_amount), 0) as amount, COALESCE(SUM(received_amount), 0) as received')
            ->groupBy('sale_date')->get()->keyBy('d');

        $dalla = $this->saltLine('sale_dallas', 'COALESCE(SUM(quantity_mann), 0)', $range);
        $thaila = $this->saltLine('sale_thailas', 'COALESCE(SUM(quantity), 0)', $range);
        $packets = $this->saltLine('sale_packages', 'COALESCE(SUM(bundle_size * bundle_quantity), 0)', $range);

        $spiceSales = DB::table('spice_sales')
            ->whereBetween('sale_date', $range)
            ->selectRaw('sale_date as d, COUNT(*) as bills, COALESCE(SUM(total_amount), 0) as amount, COALESCE(SUM(received_amount), 0) as received')
            ->groupBy('sale_date')->get()->keyBy('d');

        $spicePackets = DB::table('spice_sale_items')
            ->join('spice_sales', 'spice_sales.id', '=', 'spice_sale_items.spice_sale_id')
            ->whereBetween('spice_sales.sale_date', $range)
            ->selectRaw('spice_sales.sale_date as d, COALESCE(SUM(spice_sale_items.quantity), 0) as qty, COALESCE(SUM(spice_sale_items.total_kg), 0) as kg')
            ->groupBy('spice_sales.sale_date')->get()->keyBy('d');

        $saltProd = DB::table('productions')
            ->whereBetween('production_date', $range)
            ->selectRaw('production_date as d, COUNT(*) as batches, COALESCE(SUM(raw_salt_used), 0) as raw, COALESCE(SUM(finished_salt), 0) as finished')
            ->groupBy('production_date')->get()->keyBy('d');

        $saltProdItems = DB::table('production_items')
            ->join('productions', 'productions.id', '=', 'production_items.production_id')
            ->whereBetween('productions.production_date', $range)
            ->selectRaw('productions.production_date as d')
            ->selectRaw("COALESCE(SUM(CASE WHEN product_type = 'thaila' THEN quantity ELSE 0 END), 0) as thaila")
            ->selectRaw("COALESCE(SUM(CASE WHEN product_type = 'package' THEN quantity * bundle_size ELSE 0 END), 0) as packets")
            ->selectRaw('COALESCE(SUM(quantity_kg), 0) as kg')
            ->groupBy('productions.production_date')->get()->keyBy('d');

        $spiceProd = DB::table('spice_productions')
            ->whereBetween('production_date', $range)
            ->selectRaw('production_date as d, COUNT(*) as batches, COALESCE(SUM(raw_spice_used), 0) as raw, COALESCE(SUM(finished_spice), 0) as finished')
            ->groupBy('production_date')->get()->keyBy('d');

        $spiceProdItems = DB::table('spice_production_items')
            ->join('spice_productions', 'spice_productions.id', '=', 'spice_production_items.spice_production_id')
            ->whereBetween('spice_productions.production_date', $range)
            ->selectRaw('spice_productions.production_date as d, COALESCE(SUM(quantity), 0) as packets, COALESCE(SUM(quantity_kg), 0) as kg')
            ->groupBy('spice_productions.production_date')->get()->keyBy('d');

        $rows = [];
        $lastDay = $end->isFuture() && $end->isSameMonth(now()) ? now()->endOfDay() : $end;
        for ($day = $start->copy(); $day->lte($lastDay); $day->addDay()) {
            $d = $day->toDateString();
            $row = [
                'date' => $day->copy(),
                'salt_bills' => (int) ($saltSales[$d]->bills ?? 0),
                'salt_amount' => (float) ($saltSales[$d]->amount ?? 0),
                'salt_received' => (float) ($saltSales[$d]->received ?? 0),
                'dalla' => (float) ($dalla[$d]->qty ?? 0),
                'thaila' => (float) ($thaila[$d]->qty ?? 0),
                'packets' => (float) ($packets[$d]->qty ?? 0),
                'spice_bills' => (int) ($spiceSales[$d]->bills ?? 0),
                'spice_amount' => (float) ($spiceSales[$d]->amount ?? 0),
                'spice_received' => (float) ($spiceSales[$d]->received ?? 0),
                'spice_packets' => (float) ($spicePackets[$d]->qty ?? 0),
                'spice_kg' => (float) ($spicePackets[$d]->kg ?? 0),
                'sp_batches' => (int) ($saltProd[$d]->batches ?? 0),
                'sp_raw' => (float) ($saltProd[$d]->raw ?? 0),
                'sp_finished' => (float) ($saltProd[$d]->finished ?? 0),
                'sp_thaila' => (float) ($saltProdItems[$d]->thaila ?? 0),
                'sp_packets' => (float) ($saltProdItems[$d]->packets ?? 0),
                'sp_kg' => (float) ($saltProdItems[$d]->kg ?? 0),
                'spp_batches' => (int) ($spiceProd[$d]->batches ?? 0),
                'spp_raw' => (float) ($spiceProd[$d]->raw ?? 0),
                'spp_finished' => (float) ($spiceProd[$d]->finished ?? 0),
                'spp_packets' => (float) ($spiceProdItems[$d]->packets ?? 0),
                'spp_kg' => (float) ($spiceProdItems[$d]->kg ?? 0),
            ];
            $row['total_amount'] = $row['salt_amount'] + $row['spice_amount'];
            $row['total_bills'] = $row['salt_bills'] + $row['spice_bills'];
            $row['total_finished'] = $row['sp_finished'] + $row['spp_finished'];
            $row['total_packets'] = $row['sp_packets'] + $row['spp_packets'];
            $rows[] = $row;
        }

        return collect($rows);
    }

    /**
     * Best and worst day for a metric, plus each one's share of the month.
     * Days at zero are skipped for the low so a quiet day doesn't always "win".
     */
    private function highLow(Collection $rows, callable $metric): ?array
    {
        $scored = $rows->map(fn ($r) => ['date' => $r['date'], 'value' => (float) $metric($r)])
            ->filter(fn ($r) => $r['value'] > 0)
            ->values();

        if ($scored->isEmpty()) {
            return null;
        }

        $total = $scored->sum('value');
        $share = fn ($r) => $r + ['share' => $total > 0 ? round($r['value'] / $total * 100, 1) : 0];

        return [
            'high' => $share($scored->sortByDesc('value')->first()),
            'low' => $share($scored->sortBy('value')->first()),
            'average' => round($total / $scored->count(), 0),
        ];
    }

    /** Cumulative series for this month and the previous one on a shared day-of-month axis. */
    private function running(Collection $rows, Collection $prevRows, string $key): array
    {
        $cumulative = function (Collection $r) use ($key) {
            $sum = 0;

            return $r->map(function ($row) use (&$sum, $key) {
                return $sum += $row[$key];
            })->values()->all();
        };

        return [
            'cumulative_days' => range(1, max($rows->count(), $prevRows->count())),
            'cumulative' => $cumulative($rows),
            'cumulative_prev' => $cumulative($prevRows),
        ];
    }

    private function saltLine(string $table, string $qtyExpr, array $range)
    {
        return DB::table($table)
            ->join('sales', 'sales.id', '=', "$table.sale_id")
            ->whereBetween('sales.sale_date', $range)
            ->selectRaw("sales.sale_date as d, $qtyExpr as qty")
            ->groupBy('sales.sale_date')->get()->keyBy('d');
    }

    /** Months that have any sale or production, newest first, always including the current one. */
    private function availableMonths()
    {
        $dates = collect()
            ->merge(DB::table('sales')->pluck('sale_date'))
            ->merge(DB::table('spice_sales')->pluck('sale_date'))
            ->merge(DB::table('productions')->pluck('production_date'))
            ->merge(DB::table('spice_productions')->pluck('production_date'))
            ->filter()
            ->map(fn ($d) => Carbon::parse($d)->format('Y-m'))
            ->push(now()->format('Y-m'))
            ->unique()
            ->sortDesc()
            ->values();

        return $dates->mapWithKeys(fn ($ym) => [$ym => Carbon::createFromFormat('Y-m', $ym)->format('F Y')]);
    }
}
