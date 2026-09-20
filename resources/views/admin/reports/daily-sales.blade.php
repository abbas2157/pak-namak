@extends('admin.layout.app')
@section('title', 'Daily Sales Report')


@php
// ▲ / ▼ badge vs previous month: icon + sign + label so direction is never colour-alone.
// (Defined here, not in a partial — an @include's variables don't leak to the parent.)
$delta = function ($key) use ($changes, $prevMonth) {
    $pct = $changes[$key] ?? null;
    $m = $prevMonth->format('M');
    if ($pct === null) {
        return '<span class="delta delta-na" title="No data in '.$prevMonth->format('M Y').'">— vs '.$m.'</span>';
    }
    if ($pct == 0) {
        return '<span class="delta delta-flat"><i class="fas fa-minus mr-1"></i>same as '.$m.'</span>';
    }
    $up = $pct > 0;

    return '<span class="delta '.($up ? 'delta-up' : 'delta-down').'"><i class="fas '.($up ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down').' mr-1"></i>'
        .abs($pct).'% '.($up ? 'high' : 'low').' vs '.$m.'</span>';
};
$n0 = fn ($v) => $v ? number_format($v, 0) : '<span class="text-muted">—</span>';
$n1 = fn ($v) => $v ? number_format($v, 1) : '<span class="text-muted">—</span>';
@endphp

@section('content')
@include('admin.reports._daily_header', [
    'title' => 'Daily Sales Report', 'urdu' => 'روزانہ فروخت رپورٹ',
    'siblingRoute' => 'admin.reports.daily_production', 'siblingLabel' => 'Production Report', 'siblingIcon' => 'fa-industry',
])

<section class="content">
    <div class="container-fluid">

        {{-- ── MONTH TOTALS (vs previous month) ───────── --}}
        <div class="row mb-3">
            <div class="col-xl col-md-4 col-sm-6 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-teal">
                    <div class="card-body py-3 px-4">
                        <div class="pn-stat-lbl">Total Sales / کل فروخت</div>
                        <div class="pn-stat-num-md text-c-teal">{{ number_format($totals['total_amount'], 0) }}</div>
                        <div class="pn-stat-sub">PKR · {{ $totals['total_bills'] }} bills</div>
                        {!! $delta('total_amount') !!}
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-blue">
                    <div class="card-body py-3 px-4">
                        <div class="pn-stat-lbl">Salt Sales / نمک فروخت</div>
                        <div class="pn-stat-num-md text-c-blue2">{{ number_format($totals['salt_amount'], 0) }}</div>
                        <div class="pn-stat-sub">PKR · {{ $totals['salt_bills'] }} bills · {{ number_format($totals['salt_received'], 0) }} received</div>
                        {!! $delta('salt_amount') !!}
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-red">
                    <div class="card-body py-3 px-4">
                        <div class="pn-stat-lbl">Spice Sales / مصالحہ فروخت</div>
                        <div class="pn-stat-num-md text-c-red">{{ number_format($totals['spice_amount'], 0) }}</div>
                        <div class="pn-stat-sub">PKR · {{ $totals['spice_bills'] }} bills · {{ number_format($totals['spice_received'], 0) }} received</div>
                        {!! $delta('spice_amount') !!}
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-yellow">
                    <div class="card-body py-3 px-4">
                        <div class="pn-stat-lbl">Salt Units / نمک مقدار</div>
                        <div class="pn-stat-num-md text-c-warn">{{ number_format($totals['thaila'], 0) }} <small class="text-muted">thaila</small></div>
                        <div class="pn-stat-sub">{{ number_format($totals['dalla'], 1) }} mann dalla · {{ number_format($totals['packets'], 0) }} packets</div>
                        {!! $delta('thaila') !!}
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100">
                    <div class="card-body py-3 px-4">
                        <div class="pn-stat-lbl">Selling Days / فروخت کے دن</div>
                        <div class="pn-stat-num-md text-muted">{{ $activeDays }} <small class="text-muted">/ {{ $rows->count() }}</small></div>
                        <div class="pn-stat-sub">days with at least one bill</div>
                        {!! $delta('active_days') !!}
                    </div>
                </div>
            </div>
        </div>

        {{-- ── HIGH / LOW DAYS ────────────────────────── --}}
        <div class="row mb-4">
            <div class="col-lg-4 mb-3">
                @include('admin.reports._daily_highlow', ['x' => $highLow['total'], 'label' => 'All Sales', 'urdu' => 'کل فروخت', 'unit' => 'PKR', 'icon' => 'fa-cash-register', 'cls' => 'text-c-teal', 'noun' => 'sales'])
            </div>
            <div class="col-lg-4 mb-3">
                @include('admin.reports._daily_highlow', ['x' => $highLow['salt'], 'label' => 'Salt Sales', 'urdu' => 'نمک', 'unit' => 'PKR', 'icon' => 'fa-cube', 'cls' => 'text-c-blue2', 'noun' => 'salt sales'])
            </div>
            <div class="col-lg-4 mb-3">
                @include('admin.reports._daily_highlow', ['x' => $highLow['spice'], 'label' => 'Spice Sales', 'urdu' => 'مصالحہ', 'unit' => 'PKR', 'icon' => 'fa-pepper-hot', 'cls' => 'text-c-red', 'noun' => 'spice sales'])
            </div>
        </div>

        {{-- ── CHARTS ─────────────────────────────────── --}}
        <div class="row mb-4">
            <div class="col-lg-7 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100">
                    <div class="card-body py-3 px-4">
                        <h6 class="font-weight-bold mb-0">Daily Sales <small class="text-muted">PKR · salt + spice per day</small></h6>
                        <div class="chart-box"><canvas id="salesChart"></canvas></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100">
                    <div class="card-body py-3 px-4">
                        <h6 class="font-weight-bold mb-0">Bills per Day <small class="text-muted">salt + spice</small></h6>
                        <div class="chart-box"><canvas id="billsChart"></canvas></div>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-3">
                <div class="card card-pn border-0 shadow-sm">
                    <div class="card-body py-3 px-4">
                        <h6 class="font-weight-bold mb-0">
                            Running Sales Total — {{ $month->format('M Y') }} vs {{ $prevMonth->format('M Y') }}
                            <small class="text-muted">PKR, cumulative by day of month · above the dashed line means ahead of last month</small>
                        </h6>
                        <div class="chart-box chart-box-wide"><canvas id="cumChart"></canvas></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── DAILY SHEET ────────────────────────────── --}}
        <div class="card card-pn border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3 pb-0 px-4 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 font-weight-bold"><i class="fas fa-calendar-day mr-2 text-c-teal"></i>{{ $month->format('F Y') }} — sales day by day</h6>
                <small class="text-muted">Packages counted in packets (bundle size × bundles).</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                <table class="table table-sm table-bordered pn-table pn-table-font mb-0 daily-report-table">
                    <thead>
                        <tr class="text-center">
                            <th rowspan="2" class="pl-3 align-middle text-left">Date / تاریخ</th>
                            <th colspan="6" class="grp-salt">Salt Sales / نمک فروخت</th>
                            <th colspan="5" class="grp-spice">Spice Sales / مصالحہ فروخت</th>
                            <th colspan="2" class="grp-total">Total / کل</th>
                        </tr>
                        <tr class="text-right small">
                            <th class="grp-salt">Bills</th>
                            <th class="grp-salt">Amount</th>
                            <th class="grp-salt">Received</th>
                            <th class="grp-salt">Dalla (Mann)</th>
                            <th class="grp-salt">Thaila</th>
                            <th class="grp-salt">Packets</th>
                            <th class="grp-spice">Bills</th>
                            <th class="grp-spice">Amount</th>
                            <th class="grp-spice">Received</th>
                            <th class="grp-spice">Packets</th>
                            <th class="grp-spice">KG</th>
                            <th class="grp-total">Bills</th>
                            <th class="grp-total pr-3">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($rows as $r)
                        <tr class="text-right {{ $isActive($r) ? '' : 'quiet-day' }} {{ $r['date']->isSunday() ? 'sunday' : '' }} {{ $hiDay && $r['date']->isSameDay($hiDay) ? 'day-high' : '' }} {{ $loDay && $r['date']->isSameDay($loDay) ? 'day-low' : '' }}">
                            <td class="pl-3 text-left font-weight-bold">{{ $r['date']->format('d M') }} <small class="text-muted">{{ $r['date']->format('D') }}</small></td>
                            <td>{!! $n0($r['salt_bills']) !!}</td>
                            <td class="font-weight-bold text-c-blue2">{!! $n0($r['salt_amount']) !!}</td>
                            <td>{!! $n0($r['salt_received']) !!}</td>
                            <td>{!! $n1($r['dalla']) !!}</td>
                            <td>{!! $n0($r['thaila']) !!}</td>
                            <td>{!! $n0($r['packets']) !!}</td>
                            <td>{!! $n0($r['spice_bills']) !!}</td>
                            <td class="font-weight-bold text-c-red">{!! $n0($r['spice_amount']) !!}</td>
                            <td>{!! $n0($r['spice_received']) !!}</td>
                            <td>{!! $n0($r['spice_packets']) !!}</td>
                            <td>{!! $n1($r['spice_kg']) !!}</td>
                            <td>{!! $n0($r['total_bills']) !!}</td>
                            <td class="pr-3 font-weight-bold text-c-teal">{!! $n0($r['total_amount']) !!}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="pn-total-row font-weight-bold text-right">
                            <td class="pl-3 text-left">Total / کل</td>
                            <td>{{ number_format($totals['salt_bills']) }}</td>
                            <td class="text-c-blue2">{{ number_format($totals['salt_amount'], 0) }}</td>
                            <td>{{ number_format($totals['salt_received'], 0) }}</td>
                            <td>{{ number_format($totals['dalla'], 1) }}</td>
                            <td>{{ number_format($totals['thaila'], 0) }}</td>
                            <td>{{ number_format($totals['packets'], 0) }}</td>
                            <td>{{ number_format($totals['spice_bills']) }}</td>
                            <td class="text-c-red">{{ number_format($totals['spice_amount'], 0) }}</td>
                            <td>{{ number_format($totals['spice_received'], 0) }}</td>
                            <td>{{ number_format($totals['spice_packets'], 0) }}</td>
                            <td>{{ number_format($totals['spice_kg'], 1) }}</td>
                            <td>{{ number_format($totals['total_bills']) }}</td>
                            <td class="pr-3 text-c-teal">{{ number_format($totals['total_amount'], 0) }}</td>
                        </tr>
                    </tfoot>
                </table>
                </div>
            </div>
        </div>

    </div>
</section>

@include('admin.reports._daily_styles')
@endsection

@section('scripts')
@include('admin.reports._daily_chart_setup')
<script>
(function () {
    const C = window.DailyCharts; if (!C) return;
    const D = @json($charts);

    C.stackedBars('salesChart', 'PKR', D.days, [
        { label: 'Salt',  data: D.salt_amount,  color: C.PAL.salt },
        { label: 'Spice', data: D.spice_amount, color: C.PAL.spice },
    ]);
    C.stackedBars('billsChart', 'Bills', D.days, [
        { label: 'Salt',  data: D.salt_bills,  color: C.PAL.salt },
        { label: 'Spice', data: D.spice_bills, color: C.PAL.spice },
    ]);
    C.runningLine('cumChart', 'PKR (running total)', @json($month->format('M Y')), @json($prevMonth->format('M Y')), D);
})();
</script>
@endsection
