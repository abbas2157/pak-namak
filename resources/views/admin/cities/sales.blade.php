@extends('admin.layout.app')
@section('title', 'City Sales — ' . $city->name)

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">{{ $city->name }} <small class="text-muted" style="font-size:14px;">City Sales Report</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.cities.index') }}">Cities</a></li>
                    <li class="breadcrumb-item active">{{ $city->name }}</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end gap-2">
                <a href="{{ route('admin.cities.index') }}"
                   class="btn btn-light px-3" style="border-radius:8px;border:1px solid #d1d5db;">
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
                <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #4e73df !important;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Sales / کل فروخت</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ $sales->count() }}</div>
                                <small class="text-muted">transactions</small>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:46px;height:46px;background:rgba(78,115,223,.12);">
                                <i class="fas fa-file-invoice" style="color:#4e73df;font-size:18px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #1cc88a !important;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Revenue / کل آمدن</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($totalRevenue, 0) }}</div>
                                <small class="text-muted">PKR</small>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:46px;height:46px;background:rgba(28,200,138,.12);">
                                <i class="fas fa-chart-line" style="color:#1cc88a;font-size:18px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #36b9cc !important;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Received / وصول شدہ</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($totalReceived, 0) }}</div>
                                <small class="text-muted">PKR paid</small>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:46px;height:46px;background:rgba(54,185,204,.12);">
                                <i class="fas fa-check-circle" style="color:#36b9cc;font-size:18px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #e74a3b !important;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Pending / Udhaar / باقی</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($totalPending, 0) }}</div>
                                <small class="text-muted">PKR outstanding</small>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:46px;height:46px;background:rgba(231,74,59,.12);">
                                <i class="fas fa-clock" style="color:#e74a3b;font-size:18px;"></i>
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
                        <h6 class="mb-0 font-weight-bold" style="color:#4e73df;">
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
                            <table class="table mb-0" id="shopBreakdownTable" style="font-size:13.5px;">
                                <thead>
                                    <tr style="background:#f8f9fc;border-bottom:2px solid #e3e6f0;">
                                        <th class="pl-3 py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Shop / دکان</th>
                                        <th class="py-3 text-center text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Sales / فروخت</th>
                                        <th class="py-3 text-right text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Total (PKR) / کل</th>
                                        <th class="py-3 text-right text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Received / وصول</th>
                                        <th class="py-3 text-right text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Pending / باقی</th>
                                        <th class="py-3 text-center text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($shopStats as $stat)
                                        <tr style="border-bottom:1px solid #f0f0f0;">
                                            <td class="pl-3 py-3 align-middle">
                                                <span class="font-weight-bold d-block" style="color:#2d3748;">
                                                    {{ $stat->shop->name ?? '—' }}
                                                </span>
                                                @if($stat->shop->phone_number ?? false)
                                                    <small class="text-muted">
                                                        <i class="fas fa-phone mr-1" style="font-size:10px;"></i>{{ $stat->shop->phone_number }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td class="py-3 align-middle text-center">
                                                <span class="badge" style="background:#e8f0fe;color:#4e73df;font-size:12px;padding:5px 10px;border-radius:20px;">
                                                    {{ $stat->count }}
                                                </span>
                                            </td>
                                            <td class="py-3 align-middle text-right">
                                                <span class="font-weight-bold" style="color:#2d3748;">
                                                    {{ number_format($stat->total, 0) }}
                                                </span>
                                            </td>
                                            <td class="py-3 align-middle text-right">
                                                <span style="color:#1cc88a;font-weight:600;">
                                                    {{ number_format($stat->received, 0) }}
                                                </span>
                                            </td>
                                            <td class="py-3 align-middle text-right">
                                                @if($stat->pending > 0)
                                                    <span class="font-weight-bold" style="color:#e74a3b;">
                                                        {{ number_format($stat->pending, 0) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="py-3 align-middle text-center">
                                                <a href="{{ route('admin.sales.by_shop', ['shop_id' => $stat->shop->id]) }}"
                                                   class="btn btn-sm"
                                                   style="background:#e8f0fe;color:#4e73df;border:1px solid #c3d3f7;border-radius:6px;"
                                                   title="View Shop Sales">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <i class="fas fa-store fa-3x mb-3 d-block" style="color:#d1d5db;"></i>
                                                <p class="text-muted mb-0">No sales found for this period.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($shopStats->count() > 0)
                                <tfoot>
                                    <tr style="background:#f8f9fc;border-top:2px solid #e3e6f0;">
                                        <td class="pl-3 py-3 font-weight-bold" colspan="2" style="color:#2d3748;">Totals / کل</td>
                                        <td class="py-3 text-right font-weight-bold" style="color:#2d3748;font-size:15px;">
                                            {{ number_format($totalRevenue, 0) }}
                                        </td>
                                        <td class="py-3 text-right font-weight-bold" style="color:#1cc88a;font-size:15px;">
                                            {{ number_format($totalReceived, 0) }}
                                        </td>
                                        <td class="py-3 text-right font-weight-bold" style="color:#e74a3b;font-size:15px;">
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
                        <h6 class="mb-0 font-weight-bold" style="color:#4e73df;">
                            <i class="fas fa-calendar-alt mr-2"></i>Filter by Month / مہینے کے مطابق فلٹر
                        </h6>
                    </div>
                    <div class="card-body py-3">
                        <form method="GET">
                            <select name="month" class="form-control mb-2"
                                    onchange="this.form.submit()"
                                    style="border-radius:8px;border-color:#d1d5db;">
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
                               class="btn btn-block btn-sm mt-1"
                               style="background:#f3f4f6;color:#6b7280;border-radius:8px;border:1px solid #d1d5db;">
                                <i class="fas fa-times mr-1"></i> Show All Time
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Payment Summary --}}
                @if($sales->count() > 0)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold" style="color:#1cc88a;">
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
                                <span style="font-size:13px;color:#374151;"><strong>Received / وصول شدہ</strong></span>
                                <span style="font-size:12px;color:#1cc88a;font-weight:600;">
                                    {{ number_format($totalReceived, 0) }} <small class="text-muted">({{ $recPct }}%)</small>
                                </span>
                            </div>
                            <div class="progress" style="height:7px;border-radius:10px;background:#f0f0f0;">
                                <div class="progress-bar" style="width:{{ $recPct }}%;background:#1cc88a;border-radius:10px;"></div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between mb-1">
                                <span style="font-size:13px;color:#374151;"><strong>Pending / باقی</strong></span>
                                <span style="font-size:12px;color:#e74a3b;font-weight:600;">
                                    {{ number_format($totalPending, 0) }} <small class="text-muted">({{ $penPct }}%)</small>
                                </span>
                            </div>
                            <div class="progress" style="height:7px;border-radius:10px;background:#f0f0f0;">
                                <div class="progress-bar" style="width:{{ $penPct }}%;background:#e74a3b;border-radius:10px;"></div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-3 mt-2"
                             style="border-top:2px solid #f0f0f0;">
                            <span class="font-weight-bold" style="color:#374151;">Grand Total / کل مجموعہ</span>
                            <span class="font-weight-bold" style="color:#2d3748;font-size:15px;">
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
                        <h6 class="mb-0 font-weight-bold" style="color:#e74a3b;">
                            <i class="fas fa-trophy mr-2"></i>Top Shops / سرفہرست دکانیں
                        </h6>
                    </div>
                    <div class="card-body py-3 px-3">
                        @php $grandTotal = $shopStats->sum('total'); @endphp
                        @foreach($shopStats->take(5) as $stat)
                            @php $pct = $grandTotal > 0 ? round(($stat->total / $grandTotal) * 100) : 0; @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span style="font-size:13px;color:#374151;font-weight:600;">{{ $stat->shop->name ?? '—' }}</span>
                                    <span style="font-size:12px;color:#374151;font-weight:600;">
                                        {{ number_format($stat->total, 0) }}
                                        <small class="text-muted ml-1">{{ $pct }}%</small>
                                    </span>
                                </div>
                                <div class="progress" style="height:5px;border-radius:10px;background:#f0f0f0;">
                                    <div class="progress-bar" style="width:{{ $pct }}%;background:#4e73df;border-radius:10px;"></div>
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
    $('#shopBreakdownTable').DataTable({
        paging: false,
        searching: false,
        ordering: true,
        info: false,
        autoWidth: false,
        order: [[2, 'desc']],
        columnDefs: [{ orderable: false, targets: [5] }],
    });
});
</script>
@endsection
