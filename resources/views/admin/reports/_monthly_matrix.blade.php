{{--
    Month × size comparison matrix. Shared by the salt and spice reports.

    Expects:  $months      ['2026-04' => 'Apr 2026', ...]
              $groups      see MonthlyComparisonReportController::buildGroup()
              $monthTotals [ym => ['kg' => , 'total' => ]]
              $grandKg, $grandTotal
              $routeName   route to reload with ?from&to (month inputs)
              $title, $urdu, $emptyMessage
--}}

@php
    $monthCount   = count($months);
    $maxMonthTotal = max(array_map(fn ($t) => $t['total'], $monthTotals) ?: [0]) ?: 1;
    $hasData      = $grandTotal > 0 || $grandKg > 0;
    $fmtQty = fn ($v) => number_format($v, fmod($v, 1) == 0 ? 0 : 2);
@endphp

{{-- ── FILTER + METRIC TOGGLE ──────────────────────────── --}}
<div class="card border-0 shadow-sm mb-4 card-pn no-print">
    <div class="card-body py-3">
        <div class="row align-items-end">
            <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                <label class="pn-label text-uppercase font-weight-bold text-muted">From Month / آغاز مہینہ</label>
                <input type="month" class="form-control fc-pn" id="from" value="{{ $from }}">
            </div>
            <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                <label class="pn-label text-uppercase font-weight-bold text-muted">To Month / اختتام مہینہ</label>
                <input type="month" class="form-control fc-pn" id="to" value="{{ $to }}">
            </div>
            <div class="col-md-4 mb-2 mb-md-0">
                <button class="btn btn-primary px-4 btn-modal-save" onclick="applyFilter()">
                    <i class="fas fa-search mr-1"></i> Compare / موازنہ کریں
                </button>
                <a href="{{ route($routeName) }}" class="btn btn-outline-secondary btn-modal-cancel ml-2">
                    <i class="fas fa-undo mr-1"></i> Last 6 months
                </a>
            </div>
            <div class="col-md-4 text-md-right">
                <label class="pn-label text-uppercase font-weight-bold text-muted d-block">Show / دکھائیں</label>
                <div class="btn-group btn-group-sm" role="group" id="metricToggle">
                    <button type="button" class="btn btn-primary" data-metric="total">Revenue (PKR)</button>
                    <button type="button" class="btn btn-outline-primary" data-metric="qty">Quantity</button>
                    <button type="button" class="btn btn-outline-primary" data-metric="kg">Weight (KG)</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── SUMMARY TILES ────────────────────────────────────── --}}
<div class="row mb-3">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100 card-pn pn-bl-blue">
            <div class="card-body py-3 px-4">
                <div class="pn-form-col-lbl text-uppercase">Total Revenue / کل آمدن</div>
                <div class="pn-stat-num-lg text-c-blue2">{{ number_format($grandTotal, 0) }}</div>
                <div class="text-c-muted2">PKR · {{ reset($months) }} – {{ end($months) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100 card-pn pn-bl-teal">
            <div class="card-body py-3 px-4">
                <div class="pn-form-col-lbl text-uppercase">Total Weight / کل وزن</div>
                <div class="pn-stat-num-lg text-c-teal">{{ number_format($grandKg, 0) }}</div>
                <div class="text-c-muted2">KG across {{ $monthCount }} {{ Str::plural('month', $monthCount) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100 card-pn pn-bl-red">
            <div class="card-body py-3 px-4">
                <div class="pn-form-col-lbl text-uppercase">Avg per Month / ماہانہ اوسط</div>
                <div class="pn-stat-num-lg text-c-orange">{{ number_format($grandTotal / max($monthCount, 1), 0) }}</div>
                <div class="text-c-muted2">PKR per month</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100 card-pn bg-light">
            <div class="card-body py-3 px-4">
                <div class="pn-form-col-lbl text-uppercase">Best Month / بہترین مہینہ</div>
                @php
                    $bestYm = null;
                    foreach ($monthTotals as $ym => $t) {
                        if ($t['total'] > 0 && ($bestYm === null || $t['total'] > $monthTotals[$bestYm]['total'])) $bestYm = $ym;
                    }
                @endphp
                @if($bestYm)
                    <div class="pn-stat-num-lg text-c-blue2">{{ $months[$bestYm] }}</div>
                    <div class="text-c-muted2">PKR {{ number_format($monthTotals[$bestYm]['total'], 0) }}</div>
                @else
                    <div class="text-muted mt-2">No sales in this range</div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── CHARTS ───────────────────────────────────────────── --}}
{{-- Data + rendering live in _monthly_matrix_scripts; the metric toggle above
     drives both these charts and the matrix table below (the table view twin). --}}
@if($hasData)
<div class="row" id="mm-charts">
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm h-100 card-pn">
            <div class="card-header border-0 py-3 ch-blue">
                <div class="pn-form-col-lbl text-uppercase">Bar Graph</div>
                <h6 class="mb-0 font-weight-bold text-c-blue2">
                    <span data-chart-title="stacked">Revenue</span> by Month, stacked by {{ $groupNoun }} / ماہانہ گراف
                </h6>
                <div class="text-c-muted2 pn-stat-sub mm-unit-note" data-unit-note hidden>
                    Quantity units differ by {{ $groupNoun }} (Mann · bags · packets), so this graph shows revenue.
                </div>
            </div>
            <div class="card-body">
                <div class="mm-chart-box"><canvas id="chartStacked"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100 card-pn">
            <div class="card-header border-0 py-3 ch-blue">
                <div class="pn-form-col-lbl text-uppercase">Share</div>
                <h6 class="mb-0 font-weight-bold text-c-blue2">
                    <span data-chart-title="donut">Revenue</span> share by {{ $groupNoun }} / حصہ
                </h6>
            </div>
            <div class="card-body">
                <div class="mm-chart-box mm-chart-box-sm"><canvas id="chartDonut"></canvas></div>
                <ul class="list-unstyled mb-0 mt-3 mm-legend" id="donutLegend"></ul>
            </div>
        </div>
    </div>

    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm h-100 card-pn">
            <div class="card-header border-0 py-3 ch-teal">
                <div class="d-flex justify-content-between align-items-start flex-wrap">
                    <div>
                        <div class="pn-form-col-lbl text-uppercase">Trend</div>
                        <h6 class="mb-0 font-weight-bold text-c-teal">
                            <span data-chart-title="line">Revenue</span> trend by size / سائز کا رجحان
                        </h6>
                    </div>
                    <div class="btn-group btn-group-sm mt-2 mt-sm-0 no-print" role="group" id="lineGroupToggle"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="mm-chart-box"><canvas id="chartLine"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100 card-pn">
            <div class="card-header border-0 py-3 ch-teal">
                <div class="pn-form-col-lbl text-uppercase">Ranking</div>
                <h6 class="mb-0 font-weight-bold text-c-teal">
                    Sizes by <span data-chart-title="rank">revenue</span>, whole period / درجہ بندی
                </h6>
            </div>
            <div class="card-body">
                <div class="mm-chart-box" id="rankBox"><canvas id="chartRank"></canvas></div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ── MONTH TOTALS STRIP ───────────────────────────────── --}}
@if($hasData)
<div class="card border-0 shadow-sm mb-4 card-pn">
    <div class="card-header border-0 py-3 ch-blue">
        <div class="pn-form-col-lbl text-uppercase">Overview</div>
        <h6 class="mb-0 font-weight-bold text-c-blue2">Revenue by Month / ماہانہ آمدن</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0 pn-table pn-table-font">
                <thead>
                    <tr>
                        <th class="pl-3">Month / مہینہ</th>
                        <th class="text-right">Weight (KG)</th>
                        <th class="text-right">Revenue (PKR)</th>
                        <th class="text-right pr-3">vs Previous / گزشتہ سے</th>
                        <th class="mm-bar-col"></th>
                    </tr>
                </thead>
                <tbody>
                    @php $prev = null; @endphp
                    @foreach($monthTotals as $ym => $t)
                        <tr class="pn-table-row">
                            <td class="pl-3 font-weight-bold">{{ $months[$ym] }}</td>
                            <td class="text-right">{{ number_format($t['kg'], 2) }}</td>
                            <td class="text-right font-weight-bold text-c-blue2">{{ number_format($t['total'], 0) }}</td>
                            <td class="text-right pr-3">
                                @if($prev !== null && $prev > 0)
                                    @php $delta = (($t['total'] - $prev) / $prev) * 100; @endphp
                                    <span class="font-weight-bold {{ $delta >= 0 ? 'text-c-teal' : 'text-c-orange' }}">
                                        <i class="fas fa-arrow-{{ $delta >= 0 ? 'up' : 'down' }} icon-9"></i>
                                        {{ number_format(abs($delta), 1) }}%
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="mm-bar-col">
                                <div class="progress breakdown-prog-3">
                                    <div class="progress-bar pbar pbar-blue" style="--w:{{ ($t['total'] / $maxMonthTotal) * 100 }}%"></div>
                                </div>
                            </td>
                        </tr>
                        @php $prev = $t['total']; @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="pn-total-row">
                        <td class="pl-3">Total / کل</td>
                        <td class="text-right font-weight-bold">{{ number_format($grandKg, 2) }}</td>
                        <td class="text-right font-weight-bold text-c-blue2">{{ number_format($grandTotal, 0) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif

{{-- ── SIZE × MONTH MATRIX ──────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-4 card-pn">
    <div class="card-header border-0 py-3 ch-teal">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <div class="pn-form-col-lbl text-uppercase">Comparison</div>
                <h6 class="mb-0 font-weight-bold text-c-teal">{{ $title }} <small class="text-muted">{{ $urdu }}</small></h6>
            </div>
            <span class="badge badge-success bdg-md">{{ $monthCount }} {{ Str::plural('month', $monthCount) }}</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0 pn-table pn-table-font mm-matrix show-total" id="matrix">
                <thead>
                    <tr>
                        <th class="pl-3 mm-sticky">Size / سائز</th>
                        @foreach($months as $ym => $label)
                            <th class="text-right mm-month">{{ $label }}</th>
                        @endforeach
                        <th class="text-right mm-total-col">Total / کل</th>
                        <th class="text-right pr-3 mm-total-col">Avg / اوسط</th>
                    </tr>
                </thead>

                @forelse($groups as $g)
                    @continue(empty($g['rows']))
                    <tbody>
                        <tr class="mm-group-row">
                            <td class="pl-3 mm-sticky" colspan="{{ $monthCount + 3 }}">
                                <span class="font-weight-bold text-uppercase">{{ $g['title'] }}</span>
                                @if($g['urdu']) <span class="text-muted">{{ $g['urdu'] }}</span> @endif
                                <span class="badge badge-light border ml-2 mm-unit-badge">qty in {{ $g['unit'] }}</span>
                            </td>
                        </tr>

                        @foreach($g['rows'] as $row)
                            <tr class="pn-table-row">
                                <td class="pl-3 mm-sticky font-weight-bold">{{ $row['label'] }}</td>
                                @foreach($row['cells'] as $ym => $c)
                                    @php
                                        $bestCls = implode(' ', array_keys(array_filter($row['best'], fn ($b) => $b === $ym)));
                                    @endphp
                                    <td class="text-right mm-cell {{ $bestCls ? 'best-' . str_replace(' ', ' best-', $bestCls) : '' }} {{ $c['total'] == 0 && $c['qty'] == 0 ? 'mm-empty' : '' }}">
                                        <span class="m-total">{{ $c['total'] > 0 ? number_format($c['total'], 0) : '—' }}</span>
                                        <span class="m-qty">{{ $c['qty'] > 0 ? $fmtQty($c['qty']) : '—' }}</span>
                                        <span class="m-kg">{{ $c['kg'] > 0 ? number_format($c['kg'], 2) : '—' }}</span>
                                    </td>
                                @endforeach
                                <td class="text-right mm-total-col font-weight-bold">
                                    <span class="m-total text-c-blue2">{{ number_format($row['total']['total'], 0) }}</span>
                                    <span class="m-qty">{{ $fmtQty($row['total']['qty']) }}</span>
                                    <span class="m-kg">{{ number_format($row['total']['kg'], 2) }}</span>
                                </td>
                                <td class="text-right pr-3 mm-total-col text-muted">
                                    <span class="m-total">{{ number_format($row['total']['total'] / $monthCount, 0) }}</span>
                                    <span class="m-qty">{{ $fmtQty($row['total']['qty'] / $monthCount) }}</span>
                                    <span class="m-kg">{{ number_format($row['total']['kg'] / $monthCount, 2) }}</span>
                                </td>
                            </tr>
                        @endforeach

                        <tr class="pn-total-row mm-subtotal">
                            <td class="pl-3 mm-sticky">{{ $g['title'] }} Total</td>
                            @foreach($g['totals'] as $ym => $t)
                                <td class="text-right">
                                    <span class="m-total">{{ $t['total'] > 0 ? number_format($t['total'], 0) : '—' }}</span>
                                    <span class="m-qty">{{ $t['qty'] > 0 ? $fmtQty($t['qty']) : '—' }}</span>
                                    <span class="m-kg">{{ $t['kg'] > 0 ? number_format($t['kg'], 2) : '—' }}</span>
                                </td>
                            @endforeach
                            <td class="text-right mm-total-col text-c-blue2">
                                <span class="m-total">{{ number_format($g['grand']['total'], 0) }}</span>
                                <span class="m-qty">{{ $fmtQty($g['grand']['qty']) }}</span>
                                <span class="m-kg">{{ number_format($g['grand']['kg'], 2) }}</span>
                            </td>
                            <td class="text-right pr-3 mm-total-col text-muted">
                                <span class="m-total">{{ number_format($g['grand']['total'] / $monthCount, 0) }}</span>
                                <span class="m-qty">{{ $fmtQty($g['grand']['qty'] / $monthCount) }}</span>
                                <span class="m-kg">{{ number_format($g['grand']['kg'] / $monthCount, 2) }}</span>
                            </td>
                        </tr>
                    </tbody>
                @empty
                @endforelse

                @if($hasData)
                    <tfoot>
                        <tr class="pn-total-row mm-grand">
                            <td class="pl-3 mm-sticky">Grand Total / کل میزان</td>
                            @foreach($monthTotals as $ym => $t)
                                <td class="text-right">
                                    <span class="m-total text-c-blue2">{{ number_format($t['total'], 0) }}</span>
                                    <span class="m-qty text-muted" title="Mann, bags and packets are not added together">—</span>
                                    <span class="m-kg">{{ number_format($t['kg'], 2) }}</span>
                                </td>
                            @endforeach
                            <td class="text-right mm-total-col text-c-blue2">
                                <span class="m-total">{{ number_format($grandTotal, 0) }}</span>
                                <span class="m-qty text-muted">—</span>
                                <span class="m-kg">{{ number_format($grandKg, 2) }}</span>
                            </td>
                            <td class="text-right pr-3 mm-total-col text-muted">
                                <span class="m-total">{{ number_format($grandTotal / $monthCount, 0) }}</span>
                                <span class="m-qty">—</span>
                                <span class="m-kg">{{ number_format($grandKg / $monthCount, 2) }}</span>
                            </td>
                        </tr>
                    </tfoot>
                @else
                    <tbody>
                        <tr><td colspan="{{ $monthCount + 3 }}" class="text-center py-4 text-muted">{{ $emptyMessage }}</td></tr>
                    </tbody>
                @endif
            </table>
        </div>
        <div class="px-3 py-2 text-muted pn-stat-sub">
            <span class="mm-legend-best mr-1"></span> highest month for that size &nbsp;·&nbsp;
            Revenue in PKR · Quantity in the unit shown on each group · Weight in KG
        </div>
    </div>
</div>
