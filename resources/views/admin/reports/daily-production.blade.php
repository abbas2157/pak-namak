@extends('admin.layout.app')
@section('title', 'Daily Production Report')


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
    'title' => 'Daily Production Report', 'urdu' => 'روزانہ پیداوار رپورٹ',
    'siblingRoute' => 'admin.reports.daily_sales', 'siblingLabel' => 'Sales Report', 'siblingIcon' => 'fa-cash-register',
])

<section class="content">
    <div class="container-fluid">

        {{-- ── MONTH TOTALS (vs previous month) ───────── --}}
        <div class="row mb-3">
            <div class="col-xl col-md-4 col-sm-6 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-teal">
                    <div class="card-body py-3 px-4">
                        <div class="pn-stat-lbl">Total Produced / کل پیداوار</div>
                        <div class="pn-stat-num-md text-c-teal">{{ number_format($totals['total_finished'], 0) }}</div>
                        <div class="pn-stat-sub">KG finished · {{ $totals['sp_batches'] + $totals['spp_batches'] }} batches</div>
                        {!! $delta('total_finished') !!}
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-blue">
                    <div class="card-body py-3 px-4">
                        <div class="pn-stat-lbl">Salt Finished / تیار نمک</div>
                        <div class="pn-stat-num-md text-c-blue2">{{ number_format($totals['sp_finished'], 0) }}</div>
                        <div class="pn-stat-sub">KG · from {{ number_format($totals['sp_raw'], 0) }} KG raw · {{ $totals['sp_batches'] }} batches</div>
                        {!! $delta('sp_finished') !!}
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-blue">
                    <div class="card-body py-3 px-4">
                        <div class="pn-stat-lbl">Salt Packed / نمک پیکنگ</div>
                        <div class="pn-stat-num-md text-c-blue2">{{ number_format($totals['sp_thaila'], 0) }} <small class="text-muted">thaila</small></div>
                        <div class="pn-stat-sub">{{ number_format($totals['sp_packets'], 0) }} packets · {{ number_format($totals['sp_kg'], 0) }} KG packed</div>
                        {!! $delta('sp_thaila') !!}
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-red">
                    <div class="card-body py-3 px-4">
                        <div class="pn-stat-lbl">Spice Finished / تیار مصالحہ</div>
                        <div class="pn-stat-num-md text-c-red">{{ number_format($totals['spp_finished'], 0) }}</div>
                        <div class="pn-stat-sub">KG · {{ number_format($totals['spp_packets'], 0) }} packets · {{ $totals['spp_batches'] }} batches</div>
                        {!! $delta('spp_finished') !!}
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100">
                    <div class="card-body py-3 px-4">
                        <div class="pn-stat-lbl">Production Days / پیداواری دن</div>
                        <div class="pn-stat-num-md text-muted">{{ $activeDays }} <small class="text-muted">/ {{ $rows->count() }}</small></div>
                        <div class="pn-stat-sub">days with at least one batch</div>
                        {!! $delta('active_days') !!}
                    </div>
                </div>
            </div>
        </div>

        {{-- ── HIGH / LOW DAYS ────────────────────────── --}}
        <div class="row mb-4">
            <div class="col-lg-4 mb-3">
                @include('admin.reports._daily_highlow', ['x' => $highLow['total'], 'label' => 'All Production', 'urdu' => 'کل پیداوار', 'unit' => 'KG', 'icon' => 'fa-industry', 'cls' => 'text-c-teal', 'noun' => 'production'])
            </div>
            <div class="col-lg-4 mb-3">
                @include('admin.reports._daily_highlow', ['x' => $highLow['salt'], 'label' => 'Salt Production', 'urdu' => 'نمک', 'unit' => 'KG', 'icon' => 'fa-cube', 'cls' => 'text-c-blue2', 'noun' => 'salt production'])
            </div>
            <div class="col-lg-4 mb-3">
                @include('admin.reports._daily_highlow', ['x' => $highLow['spice'], 'label' => 'Spice Production', 'urdu' => 'مصالحہ', 'unit' => 'KG', 'icon' => 'fa-pepper-hot', 'cls' => 'text-c-red', 'noun' => 'spice production'])
            </div>
        </div>

        {{-- ── CHARTS ─────────────────────────────────── --}}
        <div class="row mb-4">
            <div class="col-lg-6 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100">
                    <div class="card-body py-3 px-4">
                        <h6 class="font-weight-bold mb-0">Daily Production <small class="text-muted">KG finished · salt + spice per day</small></h6>
                        <div class="chart-box"><canvas id="prodChart"></canvas></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100">
                    <div class="card-body py-3 px-4">
                        <h6 class="font-weight-bold mb-0">Packets Packed <small class="text-muted">salt + spice per day</small></h6>
                        <div class="chart-box"><canvas id="packetsChart"></canvas></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100">
                    <div class="card-body py-3 px-4">
                        <h6 class="font-weight-bold mb-0">Salt Thaila Packed <small class="text-muted">bags per day</small></h6>
                        <div class="chart-box"><canvas id="thailaChart"></canvas></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100">
                    <div class="card-body py-3 px-4">
                        <h6 class="font-weight-bold mb-0">
                            Running Production — {{ $month->format('M Y') }} vs {{ $prevMonth->format('M Y') }}
                            <small class="text-muted">KG, cumulative by day</small>
                        </h6>
                        <div class="chart-box"><canvas id="cumChart"></canvas></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── DAILY SHEET ────────────────────────────── --}}
        <div class="card card-pn border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3 pb-0 px-4 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 font-weight-bold"><i class="fas fa-calendar-day mr-2 text-c-teal"></i>{{ $month->format('F Y') }} — production day by day</h6>
                <small class="text-muted">Packages counted in packets (bundle size × bundles).</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                <table class="table table-sm table-bordered pn-table pn-table-font mb-0 daily-report-table">
                    <thead>
                        <tr class="text-center">
                            <th rowspan="2" class="pl-3 align-middle text-left">Date / تاریخ</th>
                            <th colspan="6" class="grp-salt">Salt Production / نمک پیداوار</th>
                            <th colspan="5" class="grp-spice">Spice Production / مصالحہ پیداوار</th>
                            <th colspan="2" class="grp-total">Total / کل</th>
                        </tr>
                        <tr class="text-right small">
                            <th class="grp-salt">Batches</th>
                            <th class="grp-salt">Raw KG</th>
                            <th class="grp-salt">Finished KG</th>
                            <th class="grp-salt">Thaila</th>
                            <th class="grp-salt">Packets</th>
                            <th class="grp-salt">Packed KG</th>
                            <th class="grp-spice">Batches</th>
                            <th class="grp-spice">Raw KG</th>
                            <th class="grp-spice">Finished KG</th>
                            <th class="grp-spice">Packets</th>
                            <th class="grp-spice">Packed KG</th>
                            <th class="grp-total">Finished KG</th>
                            <th class="grp-total pr-3">Packets</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($rows as $r)
                        <tr class="text-right {{ $isActive($r) ? '' : 'quiet-day' }} {{ $r['date']->isSunday() ? 'sunday' : '' }} {{ $hiDay && $r['date']->isSameDay($hiDay) ? 'day-high' : '' }} {{ $loDay && $r['date']->isSameDay($loDay) ? 'day-low' : '' }}">
                            <td class="pl-3 text-left font-weight-bold">{{ $r['date']->format('d M') }} <small class="text-muted">{{ $r['date']->format('D') }}</small></td>
                            <td>{!! $n0($r['sp_batches']) !!}</td>
                            <td>{!! $n0($r['sp_raw']) !!}</td>
                            <td class="font-weight-bold text-c-blue2">{!! $n0($r['sp_finished']) !!}</td>
                            <td>{!! $n0($r['sp_thaila']) !!}</td>
                            <td>{!! $n0($r['sp_packets']) !!}</td>
                            <td>{!! $n0($r['sp_kg']) !!}</td>
                            <td>{!! $n0($r['spp_batches']) !!}</td>
                            <td>{!! $n0($r['spp_raw']) !!}</td>
                            <td class="font-weight-bold text-c-red">{!! $n0($r['spp_finished']) !!}</td>
                            <td>{!! $n0($r['spp_packets']) !!}</td>
                            <td>{!! $n1($r['spp_kg']) !!}</td>
                            <td class="font-weight-bold text-c-teal">{!! $n0($r['total_finished']) !!}</td>
                            <td class="pr-3">{!! $n0($r['total_packets']) !!}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="pn-total-row font-weight-bold text-right">
                            <td class="pl-3 text-left">Total / کل</td>
                            <td>{{ number_format($totals['sp_batches']) }}</td>
                            <td>{{ number_format($totals['sp_raw'], 0) }}</td>
                            <td class="text-c-blue2">{{ number_format($totals['sp_finished'], 0) }}</td>
                            <td>{{ number_format($totals['sp_thaila'], 0) }}</td>
                            <td>{{ number_format($totals['sp_packets'], 0) }}</td>
                            <td>{{ number_format($totals['sp_kg'], 0) }}</td>
                            <td>{{ number_format($totals['spp_batches']) }}</td>
                            <td>{{ number_format($totals['spp_raw'], 0) }}</td>
                            <td class="text-c-red">{{ number_format($totals['spp_finished'], 0) }}</td>
                            <td>{{ number_format($totals['spp_packets'], 0) }}</td>
                            <td>{{ number_format($totals['spp_kg'], 1) }}</td>
                            <td class="text-c-teal">{{ number_format($totals['total_finished'], 0) }}</td>
                            <td class="pr-3">{{ number_format($totals['total_packets'], 0) }}</td>
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

    C.stackedBars('prodChart', 'KG', D.days, [
        { label: 'Salt',  data: D.sp_finished,  color: C.PAL.salt },
        { label: 'Spice', data: D.spp_finished, color: C.PAL.spice },
    ]);
    C.stackedBars('packetsChart', 'Packets', D.days, [
        { label: 'Salt',  data: D.sp_packets,  color: C.PAL.salt },
        { label: 'Spice', data: D.spp_packets, color: C.PAL.spice },
    ]);
    C.singleBars('thailaChart', 'Bags', D.days, 'Thaila', D.sp_thaila, C.PAL.salt);
    C.runningLine('cumChart', 'KG (running total)', @json($month->format('M Y')), @json($prevMonth->format('M Y')), D);
})();
</script>
@endsection
