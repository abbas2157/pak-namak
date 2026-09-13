<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Month-by-month comparison of what sold, broken down by product size.
 *
 * Salt:   Dalla (Mann) · Thaila by bag size (5/10/50 kg) · Package by packet gram.
 * Spices: each spice type by packet gram (Chilli 200g, Turmeric 1kg, ...).
 *
 * Both reports share one matrix shape (see buildGroup()) so the Blade partial
 * admin.reports._monthly_matrix can render either without knowing which
 * module it is looking at.
 */
class MonthlyComparisonReportController extends Controller
{
    /** Widest range a single page will render — 24 columns is already a stretch on print. */
    private const MAX_MONTHS = 24;

    public function salt(Request $request)
    {
        [$months, $start, $end] = $this->monthRange($request);

        $dalla = $this->monthlyRows(
            'sale_dallas', 'sales', 'sale_id',
            $start, $end,
            sizeExpr: "'dalla'", qtyExpr: 'quantity_mann', kgExpr: 'quantity_kg',
        );

        $thaila = $this->monthlyRows(
            'sale_thailas', 'sales', 'sale_id',
            $start, $end,
            sizeExpr: 'bag_size_kg', qtyExpr: 'quantity', kgExpr: 'total_kg',
        );

        // Packet count (bundle_size × bundle_quantity) rather than bundle count:
        // a 10-pack and a 20-pack bundle of the same gram would otherwise be
        // summed as equal units.
        $package = $this->monthlyRows(
            'sale_packages', 'sales', 'sale_id',
            $start, $end,
            sizeExpr: 'packet_gram', qtyExpr: 'bundle_size * bundle_quantity', kgExpr: 'total_kg',
        );

        $groups = [
            $this->buildGroup('Dalla', 'ڈلا', 'Mann', $dalla, $months, fn () => 'Dalla (Bulk)'),
            $this->buildGroup('Thaila', 'تھیلا', 'Bags', $thaila, $months, fn ($kg) => "{$kg} KG Thaila"),
            $this->buildGroup('Package', 'پیکج', 'Packets', $package, $months, fn ($g) => "{$g} G Package"),
        ];

        return view('admin.sales.monthly-comparison', [
            'months' => $months,
            'groups' => $groups,
            ...$this->overall($groups, $months),
            'from' => array_key_first($months),
            'to' => array_key_last($months),
        ]);
    }

    public function spices(Request $request)
    {
        [$months, $start, $end] = $this->monthRange($request);

        $rows = DB::table('spice_sale_items')
            ->join('spice_sales', 'spice_sales.id', '=', 'spice_sale_items.spice_sale_id')
            ->join('spice_types', 'spice_types.id', '=', 'spice_sale_items.spice_type_id')
            ->whereBetween('spice_sales.sale_date', [$start, $end])
            ->selectRaw("DATE_FORMAT(spice_sales.sale_date, '%Y-%m') as ym")
            ->selectRaw('spice_types.id as type_id, spice_types.title as type_name')
            ->selectRaw('spice_sale_items.packet_gram as size')
            ->selectRaw('COALESCE(SUM(spice_sale_items.quantity), 0) as qty')
            ->selectRaw('COALESCE(SUM(spice_sale_items.total_kg), 0) as kg')
            ->selectRaw('COALESCE(SUM(spice_sale_items.sub_total), 0) as total')
            ->groupBy('ym', 'spice_types.id', 'spice_types.title', 'spice_sale_items.packet_gram')
            ->get();

        // One matrix group per spice type, one row per packet size inside it.
        $groups = $rows->groupBy('type_id')
            ->sortBy(fn ($r) => $r->first()->type_name)
            ->map(fn ($typeRows) => $this->buildGroup(
                $typeRows->first()->type_name, '', 'Packets', $typeRows, $months,
                fn ($g) => $this->gramLabel((int) $g),
            ))
            ->values()
            ->all();

        return view('admin.spice-sales.monthly-comparison', [
            'months' => $months,
            'groups' => $groups,
            ...$this->overall($groups, $months),
            'from' => array_key_first($months),
            'to' => array_key_last($months),
        ]);
    }

    /* ───────────────────────── helpers ───────────────────────── */

    /**
     * Resolve ?from=YYYY-MM&to=YYYY-MM into an ordered ym => label map plus the
     * inclusive date bounds to query with. Defaults to the last six months.
     *
     * @return array{0: array<string,string>, 1: string, 2: string}
     */
    private function monthRange(Request $request): array
    {
        $parse = fn ($v) => preg_match('/^\d{4}-\d{2}$/', (string) $v)
            ? Carbon::createFromFormat('Y-m', $v)->startOfMonth()
            : null;

        $to = $parse($request->get('to')) ?? Carbon::now()->startOfMonth();
        $from = $parse($request->get('from')) ?? $to->copy()->subMonths(5);

        if ($from->gt($to)) {
            [$from, $to] = [$to, $from];
        }
        if ($from->diffInMonths($to) >= self::MAX_MONTHS) {
            $from = $to->copy()->subMonths(self::MAX_MONTHS - 1);
        }

        $months = [];
        for ($m = $from->copy(); $m->lte($to); $m->addMonth()) {
            $months[$m->format('Y-m')] = $m->format('M Y');
        }

        return [$months, $from->toDateString(), $to->copy()->endOfMonth()->toDateString()];
    }

    /**
     * Sum a salt line-item table per (month, size). $sizeExpr is a raw SQL
     * expression so Dalla — which has no size column — can pass a literal.
     */
    private function monthlyRows(
        string $table, string $saleTable, string $fk,
        string $start, string $end,
        string $sizeExpr, string $qtyExpr, string $kgExpr,
    ) {
        return DB::table($table)
            ->join($saleTable, "{$saleTable}.id", '=', "{$table}.{$fk}")
            ->whereBetween("{$saleTable}.sale_date", [$start, $end])
            ->selectRaw("DATE_FORMAT({$saleTable}.sale_date, '%Y-%m') as ym")
            ->selectRaw("{$sizeExpr} as size")
            ->selectRaw("COALESCE(SUM({$qtyExpr}), 0) as qty")
            ->selectRaw("COALESCE(SUM({$kgExpr}), 0) as kg")
            ->selectRaw("COALESCE(SUM({$table}.sub_total), 0) as total")
            ->groupBy('ym', DB::raw($sizeExpr))
            ->get();
    }

    /**
     * Pivot flat (ym, size, qty, kg, total) rows into the matrix the view
     * renders: one row per size, one cell per month, plus row / column totals.
     */
    private function buildGroup(string $title, string $urdu, string $unit, $rows, array $months, callable $label): array
    {
        $zero = fn () => ['qty' => 0.0, 'kg' => 0.0, 'total' => 0.0];

        $bySize = [];
        foreach ($rows as $r) {
            $bySize[$r->size][$r->ym] = [
                'qty' => (float) $r->qty,
                'kg' => (float) $r->kg,
                'total' => (float) $r->total,
            ];
        }
        ksort($bySize, SORT_NUMERIC);

        $colTotals = array_map($zero, $months);
        $grand = $zero();
        $outRows = [];

        foreach ($bySize as $size => $cellsBySize) {
            $cells = [];
            $rowTotal = $zero();
            foreach ($months as $ym => $_) {
                $c = $cellsBySize[$ym] ?? $zero();
                $cells[$ym] = $c;
                foreach ($c as $k => $v) {
                    $rowTotal[$k] += $v;
                    $colTotals[$ym][$k] += $v;
                    $grand[$k] += $v;
                }
            }
            $outRows[] = [
                'label' => $label($size),
                'size' => $size,
                'cells' => $cells,
                'total' => $rowTotal,
                'best' => $this->bestMonth($cells),
            ];
        }

        return [
            'title' => $title,
            'urdu' => $urdu,
            'unit' => $unit,
            'rows' => $outRows,
            'totals' => $colTotals,
            'grand' => $grand,
        ];
    }

    /**
     * Peak month per metric for a row (['total' => ym, 'qty' => ym, 'kg' => ym])
     * — the view highlights whichever matches the metric being shown.
     */
    private function bestMonth(array $cells): array
    {
        $best = ['total' => null, 'qty' => null, 'kg' => null];
        foreach ($cells as $ym => $c) {
            foreach ($best as $k => $bestYm) {
                if ($c[$k] > 0 && ($bestYm === null || $c[$k] > $cells[$bestYm][$k])) {
                    $best[$k] = $ym;
                }
            }
        }

        return $best;
    }

    /**
     * Cross-group column totals. Quantity is deliberately omitted — Mann,
     * bags and packets don't add up to anything meaningful.
     */
    private function overall(array $groups, array $months): array
    {
        $monthTotals = array_map(fn () => ['kg' => 0.0, 'total' => 0.0], $months);
        $grandKg = 0.0;
        $grandTotal = 0.0;

        foreach ($groups as $g) {
            foreach ($g['totals'] as $ym => $t) {
                $monthTotals[$ym]['kg'] += $t['kg'];
                $monthTotals[$ym]['total'] += $t['total'];
            }
            $grandKg += $g['grand']['kg'];
            $grandTotal += $g['grand']['total'];
        }

        return compact('monthTotals', 'grandKg', 'grandTotal');
    }

    private function gramLabel(int $grams): string
    {
        return $grams >= 1000
            ? rtrim(rtrim(number_format($grams / 1000, 2, '.', ''), '0'), '.').' KG'
            : "{$grams} G";
    }
}
