@extends('admin.layout.app')
@section('title', 'Investments')

@php
$sourceIcons = [
    'Expense'         => 'fa-receipt',
    'Salt Purchase'   => 'fa-truck',
    'Spice Purchase'  => 'fa-pepper-hot',
    'Asset'           => 'fa-tools',
];
$sourceBadge = [
    'Expense'         => 'badge-danger',
    'Salt Purchase'   => 'badge-warning',
    'Spice Purchase'  => 'badge-success',
    'Asset'           => 'badge-primary',
];
$sourceBarClass = [
    'Expense'         => 'pbar-red',
    'Salt Purchase'   => 'pbar-orange',
    'Spice Purchase'  => 'pbar-teal',
    'Asset'           => 'pbar-blue',
];
@endphp

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-12">
                <h1 class="m-0">Investments <small class="text-muted pn-stat-sub">سرمایہ کاری</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Investments</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        {{-- ===== STAT CARDS ===== --}}
        <div class="row mb-3">
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100 pn-bl-blue">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Investment / کل سرمایہ کاری</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($grandTotal, 0) }}</div>
                                <small class="text-muted">PKR</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-blue">
                                <i class="fas fa-piggy-bank"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100 pn-bl-cyan">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Records / اندراجات</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ $items->count() }}</div>
                                <small class="text-muted">entries</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-cyan">
                                <i class="fas fa-list-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100 pn-bl-yellow">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Largest Investment / سب سے بڑی سرمایہ کاری</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($items->max('amount') ?? 0, 0) }}</div>
                                <small class="text-muted">PKR</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-yellow">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100 pn-bl-teal">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Top Source / سب سے بڑا ذریعہ</div>
                                <div class="h5 mb-0 font-weight-bold text-dark pn-stat-num-sm">{{ $sourceTotals->keys()->first() ?? '-' }}</div>
                                <small class="text-muted">highest spend</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-teal">
                                <i class="fas fa-tags"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== INVESTMENT ITEMS + SIDEBAR ===== --}}
        <div class="row">

            <div class="col-lg-8 mb-3">
                <div class="card border-0 shadow-sm card-pn">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h6 class="mb-0 font-weight-bold text-c-blue2">
                                <i class="fas fa-table mr-2"></i>Investment Records / سرمایہ کاری کی فہرست
                            </h6>
                            @if($selectedMonth)
                                <small class="text-muted">{{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y') }}</small>
                            @else
                                <small class="text-muted">All time</small>
                            @endif
                        </div>
                        <span class="badge pn-bdg pn-bdg-blue">{{ $items->count() }} records</span>
                    </div>
                    <div class="card-body p-2">
                        <div class="table-responsive">
                            <table class="table mb-0 pn-table pn-table-font">
                                <thead>
                                    <tr>
                                        <th class="pl-4">Date / تاریخ</th>
                                        <th>Source / ذریعہ</th>
                                        <th>Details / تفصیل</th>
                                        <th class="text-right">Amount / رقم</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($items as $item)
                                        <tr>
                                            <td class="pl-4 align-middle">
                                                <span class="font-weight-bold d-block pn-text-heading">
                                                    {{ \Carbon\Carbon::parse($item->date)->format('d M') }}
                                                </span>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($item->date)->format('Y') }}</small>
                                            </td>
                                            <td class="align-middle">
                                                <span class="badge {{ $sourceBadge[$item->source_label] ?? 'badge-secondary' }}">
                                                    <i class="fas {{ $sourceIcons[$item->source_label] ?? 'fa-tag' }} mr-1"></i>{{ $item->source_label }}
                                                </span>
                                            </td>
                                            <td class="align-middle col-desc">
                                                <span class="font-weight-bold d-block">{{ $item->label }}</span>
                                                @if($item->sub)
                                                    <small class="text-muted text-truncate d-block">{{ $item->sub }}</small>
                                                @endif
                                            </td>
                                            <td class="align-middle text-right">
                                                <span class="font-weight-bold text-c-red pn-stat-num-sm">
                                                    {{ number_format($item->amount, 0) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <i class="fas fa-piggy-bank fa-3x mb-3 d-block icon-fade"></i>
                                                <p class="text-muted mb-0">No investment-flagged records for this period.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($items->count() > 0)
                                <tfoot>
                                    <tr class="pn-total-row">
                                        <td class="pl-4 py-3 font-weight-bold pn-text-heading" colspan="3">Grand Total / کل مجموعہ</td>
                                        <td class="py-3 text-right font-weight-bold text-c-red pn-stat-num-sm">
                                            {{ number_format($grandTotal, 0) }}
                                        </td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SIDEBAR --}}
            <div class="col-lg-4">

                {{-- Month Filter --}}
                <div class="card border-0 shadow-sm mb-3 card-pn">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-c-blue2">
                            <i class="fas fa-calendar-alt mr-2"></i>Filter by Month / مہینے کے مطابق فلٹر
                        </h6>
                    </div>
                    <div class="card-body py-3">
                        <form method="GET">
                            <select name="month" class="form-control mb-2 fc-pn" onchange="this.form.submit()">
                                <option value="">All Time</option>
                                @foreach($months as $m)
                                    <option value="{{ $m['value'] }}" {{ $selectedMonth == $m['value'] ? 'selected' : '' }}>
                                        {{ $m['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                        @if($selectedMonth)
                            <a href="{{ route('admin.investments.index') }}" class="btn btn-block btn-sm btn-pn btn-clear-filter mt-1">
                                <i class="fas fa-times mr-1"></i> Show All Time
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Breakdown by source --}}
                @if($sourceTotals->count())
                <div class="card border-0 shadow-sm card-pn">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-c-red">
                            <i class="fas fa-chart-pie mr-2"></i>Breakdown / ذریعہ وار تفصیل
                        </h6>
                    </div>
                    <div class="card-body py-3 px-3">
                        @foreach($sourceTotals as $label => $total)
                            @php $pct = $grandTotal > 0 ? round(($total / $grandTotal) * 100) : 0; @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="breakdown-item">
                                        <i class="fas {{ $sourceIcons[$label] ?? 'fa-tag' }} mr-1 icon-fw14"></i>
                                        <strong>{{ $label }}</strong>
                                    </span>
                                    <span class="breakdown-amount">
                                        <span class="breakdown-val">{{ number_format($total, 0) }}</span>
                                        <span class="text-muted ml-1">{{ $pct }}%</span>
                                    </span>
                                </div>
                                <div class="progress breakdown-bar">
                                    <div class="progress-bar pbar {{ $sourceBarClass[$label] ?? 'pbar-grey' }}" style="--w:{{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                        <div class="d-flex justify-content-between align-items-center pt-2 mt-1 breakdown-grand-total-bar">
                            <span class="font-weight-bold pn-text-heading">Grand Total / کل مجموعہ</span>
                            <span class="font-weight-bold text-c-red pn-stat-num-sm">PKR {{ number_format($grandTotal, 0) }}</span>
                        </div>
                    </div>
                </div>
                @endif

            </div>{{-- /sidebar --}}
        </div>

    </div>
</section>
@endsection
