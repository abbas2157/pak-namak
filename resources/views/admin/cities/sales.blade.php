@extends('admin.layout.app')
@section('title', 'City Sales — ' . $city->name)

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">{{ $city->name }} <small class="text-muted">City Sales Report</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.cities.index') }}">Cities</a></li>
                    <li class="breadcrumb-item active">{{ $city->name }}</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end gap-2">
                <a href="{{ route('admin.cities.index') }}"
                   class="btn btn-light px-3 btn-modal-cancel">
                    <i class="fas fa-arrow-left mr-1"></i> All Cities
                </a>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        {{-- ===== STATS ===== --}}
        <div class="row mb-3">
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100 pn-bl-blue">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Sales / کل فروخت</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ $sales->count() }}</div>
                                <small class="text-muted">transactions</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-blue">
                                <i class="fas fa-file-invoice"></i>
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
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Revenue / کل آمدن</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($totalRevenue, 0) }}</div>
                                <small class="text-muted">PKR</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-teal">
                                <i class="fas fa-chart-line"></i>
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
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Received / وصول شدہ</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($totalReceived, 0) }}</div>
                                <small class="text-muted">PKR paid</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-cyan">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100 pn-bl-red">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Pending / Udhaar / باقی</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($totalPending, 0) }}</div>
                                <small class="text-muted">PKR outstanding</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-red">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== MAIN CONTENT + SIDEBAR ===== --}}
        <div class="row">

            {{-- SHOP BREAKDOWN TABLE --}}
            <div class="col-lg-8 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                        <h6 class="mb-0 font-weight-bold text-c-blue2">
                            <i class="fas fa-store mr-2"></i>Sales by Shop / دکان وار فروخت
                        </h6>
                        @if($selectedMonth)
                            <small class="text-muted">
                                {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y') }}
                            </small>
                        @else
                            <small class="text-muted">All time</small>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0 pn-table pn-table-font" id="shopBreakdownTable">
                                <thead>
                                    <tr>
                                        <th class="pl-3 py-3 text-uppercase">Shop / دکان</th>
                                        <th class="py-3 text-center text-uppercase">Sales / فروخت</th>
                                        <th class="py-3 text-right text-uppercase">Total (PKR) / کل</th>
                                        <th class="py-3 text-right text-uppercase">Received / وصول</th>
                                        <th class="py-3 text-right text-uppercase">Pending / باقی</th>
                                        <th class="py-3 text-center text-uppercase">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($shopStats as $stat)
                                        <tr class="pn-table-row">
                                            <td class="pl-3 py-3 align-middle">
                                                <span class="font-weight-bold d-block text-c-dark">
                                                    {{ $stat->shop->name ?? '—' }}
                                                </span>
                                                @if($stat->shop->phone_number ?? false)
                                                    <small class="text-muted">
                                                        <i class="fas fa-phone mr-1 icon-10"></i>{{ $stat->shop->phone_number }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td class="py-3 align-middle text-center">
                                                <span class="badge bdg-blue bdg-md">
                                                    {{ $stat->count }}
                                                </span>
                                            </td>
                                            <td class="py-3 align-middle text-right">
                                                <span class="font-weight-bold text-c-dark">
                                                    {{ number_format($stat->total, 0) }}
                                                </span>
                                            </td>
                                            <td class="py-3 align-middle text-right">
                                                <span class="text-c-teal font-weight-bold">
                                                    {{ number_format($stat->received, 0) }}
                                                </span>
                                            </td>
                                            <td class="py-3 align-middle text-right">
                                                @if($stat->pending > 0)
                                                    <span class="font-weight-bold text-c-red">
                                                        {{ number_format($stat->pending, 0) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="py-3 align-middle text-center">
                                                <a href="{{ route('admin.sales.by_shop', ['shop_id' => $stat->shop->id]) }}"
                                                   class="btn btn-sm btn-pn btn-act-view"
                                                   title="View Shop Sales">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <i class="fas fa-store fa-3x mb-3 d-block text-muted"></i>
                                                <p class="text-muted mb-0">No sales found for this period.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($shopStats->count() > 0)
                                <tfoot>
                                    <tr class="pn-total-row">
                                        <td class="pl-3 py-3 font-weight-bold text-c-dark" colspan="2">Totals / کل</td>
                                        <td class="py-3 text-right font-weight-bold text-c-dark pn-stat-num-sm">
                                            {{ number_format($totalRevenue, 0) }}
                                        </td>
                                        <td class="py-3 text-right font-weight-bold text-c-teal pn-stat-num-sm">
                                            {{ number_format($totalReceived, 0) }}
                                        </td>
                                        <td class="py-3 text-right font-weight-bold text-c-red pn-stat-num-sm">
                                            {{ number_format($totalPending, 0) }}
                                        </td>
                                        <td></td>
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
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-c-blue2">
                            <i class="fas fa-calendar-alt mr-2"></i>Filter by Month / مہینے کے مطابق فلٹر
                        </h6>
                    </div>
                    <div class="card-body py-3">
                        <form method="GET">
                            <select name="month" class="form-control mb-2 fc-pn"
                                    onchange="this.form.submit()">
                                <option value="">All Time</option>
                                @foreach($months as $m)
                                    <option value="{{ $m->value }}" {{ $selectedMonth == $m->value ? 'selected' : '' }}>
                                        {{ $m->label }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                        @if($selectedMonth)
                            <a href="{{ route('admin.cities.sales', $city->id) }}"
                               class="btn btn-block btn-sm mt-1 btn-clear-filter">
                                <i class="fas fa-times mr-1"></i> Show All Time
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Payment Summary --}}
                @if($sales->count() > 0)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-c-teal">
                            <i class="fas fa-wallet mr-2"></i>Payment Summary / ادائیگی کا خلاصہ
                        </h6>
                    </div>
                    <div class="card-body py-3 px-3">
                        @php
                            $recPct = $totalRevenue > 0 ? round(($totalReceived / $totalRevenue) * 100) : 0;
                            $penPct = $totalRevenue > 0 ? round(($totalPending  / $totalRevenue) * 100) : 0;
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="breakdown-lbl"><strong>Received / وصول شدہ</strong></span>
                                <span class="breakdown-lbl-val">
                                    <span class="breakdown-lbl-num text-c-teal">{{ number_format($totalReceived, 0) }}</span>
                                    <small class="text-muted">({{ $recPct }}%)</small>
                                </span>
                            </div>
                            <div class="progress breakdown-prog-7">
                                <div class="progress-bar pbar pbar-teal" style="--w:{{ $recPct }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="breakdown-lbl"><strong>Pending / باقی</strong></span>
                                <span class="breakdown-lbl-val">
                                    <span class="breakdown-lbl-num text-c-red">{{ number_format($totalPending, 0) }}</span>
                                    <small class="text-muted">({{ $penPct }}%)</small>
                                </span>
                            </div>
                            <div class="progress breakdown-prog-7">
                                <div class="progress-bar pbar pbar-red" style="--w:{{ $penPct }}%"></div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-3 mt-2 breakdown-grand-total-bar">
                            <span class="breakdown-lbl font-weight-bold">Grand Total / کل مجموعہ</span>
                            <span class="text-c-dark pn-stat-num-sm font-weight-bold">
                                PKR {{ number_format($totalRevenue, 0) }}
                            </span>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Top Shops --}}
                @if($shopStats->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-c-red">
                            <i class="fas fa-trophy mr-2"></i>Top Shops / سرفہرست دکانیں
                        </h6>
                    </div>
                    <div class="card-body py-3 px-3">
                        @php $grandTotal = $shopStats->sum('total'); @endphp
                        @foreach($shopStats->take(5) as $stat)
                            @php $pct = $grandTotal > 0 ? round(($stat->total / $grandTotal) * 100) : 0; @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="breakdown-lbl font-weight-bold">{{ $stat->shop->name ?? '—' }}</span>
                                    <span class="breakdown-lbl-val">
                                        <span class="breakdown-lbl-num">{{ number_format($stat->total, 0) }}</span>
                                        <small class="text-muted ml-1">{{ $pct }}%</small>
                                    </span>
                                </div>
                                <div class="progress breakdown-prog-5">
                                    <div class="progress-bar pbar pbar-blue" style="--w:{{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </div>

    </div>
</section>
@endsection

@section('scripts')
<script>
$(function () {
    // DataTables throws on an empty (colspan "no records") table when
    // columnDefs targets a specific column index — only initialize when
    // there are real rows to enhance.
    if ($('#shopBreakdownTable tbody tr').not(':has(td[colspan])').length > 0) {
        $('#shopBreakdownTable').DataTable({
            paging: false,
            searching: false,
            ordering: true,
            info: false,
            autoWidth: false,
            order: [[2, 'desc']],
            columnDefs: [{ orderable: false, targets: [5] }],
        });
    }
});
</script>
@endsection
