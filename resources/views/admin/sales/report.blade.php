@extends('admin.layout.app')
@section('title', 'Sales Report')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Sales Report <small class="text-muted" style="font-size:14px;">فروخت رپورٹ</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Sales Report</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end gap-2 no-print">
                <button class="btn btn-outline-secondary" onclick="window.print()" style="border-radius:8px;">
                    <i class="fas fa-print mr-1"></i> Print / PDF
                </button>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        {{-- ── DATE FILTER ──────────────────────────────── --}}
        <div class="card border-0 shadow-sm mb-4 no-print" style="border-radius:10px;">
            <div class="card-body py-3">
                <div class="row align-items-end">
                    <div class="col-md-3 mb-2 mb-md-0">
                        <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">From Date / آغاز تاریخ</label>
                        <input type="date" class="form-control" id="from" value="{{ $from }}" style="border-radius:8px;">
                    </div>
                    <div class="col-md-3 mb-2 mb-md-0">
                        <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">To Date / اختتام تاریخ</label>
                        <input type="date" class="form-control" id="to" value="{{ $to }}" style="border-radius:8px;">
                    </div>
                    <div class="col-md-3 mb-2 mb-md-0">
                        <button class="btn btn-primary px-4" onclick="applyFilter()" style="border-radius:8px;">
                            <i class="fas fa-search mr-1"></i> Apply Filter / فلٹر لگائیں
                        </button>
                        @if($from || $to)
                            <a href="{{ route('admin.sales.report') }}" class="btn btn-outline-secondary ml-2" style="border-radius:8px;">
                                <i class="fas fa-times mr-1"></i> Clear
                            </a>
                        @endif
                    </div>
                    <div class="col-md-3 text-muted" style="font-size:12px;">
                        @if($from || $to)
                            Showing: <strong>{{ $from ?? '—' }}</strong> to <strong>{{ $to ?? '—' }}</strong>
                            &nbsp;·&nbsp; <strong>{{ $sales->count() }}</strong> sales
                        @else
                            Showing all sales &nbsp;·&nbsp; <strong>{{ $sales->count() }}</strong> total
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ── GRAND TOTALS ──────────────────────────────── --}}
        <div class="row mb-3">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius:10px;border-left:4px solid #4e73df!important;">
                    <div class="card-body py-3 px-4">
                        <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.8px;color:#b0b7c3;">Total Revenue / کل آمدن</div>
                        <div style="font-size:22px;font-weight:800;color:#4e73df;">{{ number_format($grandTotal, 0) }}</div>
                        <div style="font-size:11px;color:#b0b7c3;">PKR across {{ $sales->count() }} invoices</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius:10px;border-left:4px solid #1cc88a!important;">
                    <div class="card-body py-3 px-4">
                        <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.8px;color:#b0b7c3;">Received / وصول شدہ</div>
                        <div style="font-size:22px;font-weight:800;color:#1cc88a;">{{ number_format($grandReceived, 0) }}</div>
                        <div style="font-size:11px;color:#b0b7c3;">
                            {{ $grandTotal > 0 ? number_format(($grandReceived/$grandTotal)*100,1) : 0 }}% collected
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius:10px;border-left:4px solid #e65100!important;">
                    <div class="card-body py-3 px-4">
                        <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.8px;color:#b0b7c3;">Pending اُدھار</div>
                        <div style="font-size:22px;font-weight:800;color:#e65100;">{{ number_format($grandPending, 0) }}</div>
                        <div style="font-size:11px;color:#b0b7c3;">
                            {{ $grandTotal > 0 ? number_format(($grandPending/$grandTotal)*100,1) : 0 }}% outstanding
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius:10px;background:#f8f9fc;">
                    <div class="card-body py-3 px-4">
                        <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.8px;color:#b0b7c3;">Product Breakdown / مصنوعات کی تفصیل</div>
                        <div class="mt-1">
                            <div class="d-flex justify-content-between" style="font-size:12px;">
                                <span class="text-primary font-weight-bold">Dalla</span>
                                <span>PKR {{ number_format($dallaStats->total ?? 0, 0) }}</span>
                            </div>
                            <div class="d-flex justify-content-between" style="font-size:12px;">
                                <span class="text-success font-weight-bold">Thaila</span>
                                <span>PKR {{ number_format($thailaStats->total ?? 0, 0) }}</span>
                            </div>
                            <div class="d-flex justify-content-between" style="font-size:12px;">
                                <span class="text-warning font-weight-bold">Package</span>
                                <span>PKR {{ number_format($packageStats->total ?? 0, 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── SALES BY SHOP ──────────────────────────────── --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius:10px;">
            <div class="card-header border-0 py-3" style="background:#f0f4ff;border-radius:10px 10px 0 0;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.8px;color:#b0b7c3;">Summary</div>
                        <h6 class="mb-0 font-weight-bold" style="color:#4e73df;">Sales by Shop / دکان کے مطابق فروخت</h6>
                    </div>
                    <span class="badge badge-primary" style="border-radius:20px;font-size:12px;">
                        {{ $salesByShop->count() }} shops
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                @php $maxShopTotal = $salesByShop->max('total') ?: 1; @endphp
                <table class="table table-sm mb-0" id="salesByShopTable">
                    <thead>
                        <tr style="background:#f8f9fc;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:#b0b7c3;">
                            <th class="pl-3">#</th>
                            <th>Shop / دکان</th>
                            <th class="text-center">Invoices / بل</th>
                            <th class="text-right">Total (PKR) / کل</th>
                            <th class="text-right">Received / وصول</th>
                            <th class="text-right pr-3">Pending / باقی</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salesByShop as $i => $row)
                            <tr>
                                <td class="pl-3 text-muted" style="font-size:12px;">{{ $i + 1 }}</td>
                                <td>
                                    <div class="font-weight-bold" style="font-size:13px;">{{ $row->shop_name ?? '—' }}</div>
                                    @if($row->shop_phone)
                                        <div class="text-muted" style="font-size:11px;">
                                            <i class="fas fa-phone" style="font-size:9px;"></i> {{ $row->shop_phone }}
                                        </div>
                                    @endif
                                    <div class="progress mt-1" style="height:3px;width:120px;border-radius:3px;">
                                        <div class="progress-bar bg-primary"
                                             style="width:{{ ($row->total / $maxShopTotal) * 100 }}%;border-radius:3px;"></div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light border" style="font-size:12px;">{{ $row->count }}</span>
                                </td>
                                <td class="text-right font-weight-bold" style="color:#4e73df;">
                                    {{ number_format($row->total, 0) }}
                                </td>
                                <td class="text-right" style="color:#1cc88a;font-weight:600;">
                                    {{ number_format($row->received, 0) }}
                                </td>
                                <td class="text-right pr-3">
                                    @if($row->pending > 0)
                                        <span style="color:#e65100;font-weight:600;">{{ number_format($row->pending, 0) }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted">No sales found for the selected period.</td></tr>
                        @endforelse
                    </tbody>
                    @if($salesByShop->count() > 1)
                    <tfoot>
                        <tr style="background:#f8f9fc;font-weight:700;font-size:13px;">
                            <td colspan="2" class="pl-3">Total / کل</td>
                            <td class="text-center">{{ $sales->count() }}</td>
                            <td class="text-right" style="color:#4e73df;">{{ number_format($grandTotal, 0) }}</td>
                            <td class="text-right" style="color:#1cc88a;">{{ number_format($grandReceived, 0) }}</td>
                            <td class="text-right pr-3" style="color:#e65100;">{{ number_format($grandPending, 0) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{-- ── ALL SALES TABLE ──────────────────────────────── --}}
        <div class="card border-0 shadow-sm" style="border-radius:10px;">
            <div class="card-header border-0 py-3" style="background:#f0fff8;border-radius:10px 10px 0 0;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.8px;color:#b0b7c3;">Detail</div>
                        <h6 class="mb-0 font-weight-bold" style="color:#1cc88a;">All Sales / تمام فروخت</h6>
                    </div>
                    <span class="badge badge-success" style="border-radius:20px;font-size:12px;">
                        {{ $sales->count() }} records
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0" id="salesReportTable">
                    <thead>
                        <tr style="background:#f8f9fc;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:#b0b7c3;">
                            <th class="pl-3">#</th>
                            <th>Shop / دکان</th>
                            <th>Date / تاریخ</th>
                            <th class="text-right">Total / کل</th>
                            <th class="text-right">Received / وصول</th>
                            <th class="text-right">Pending / باقی</th>
                            <th class="text-center pr-3 no-print">Action / اقدام</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $i => $sale)
                            <tr>
                                <td class="pl-3 text-muted" style="font-size:12px;">{{ $i + 1 }}</td>
                                <td>
                                    <div class="font-weight-bold" style="font-size:13px;">
                                        {{ $sale->shop->name ?? '—' }}
                                    </div>
                                    @if($sale->shop?->phone_number)
                                        <div class="text-muted" style="font-size:11px;">
                                            <i class="fas fa-phone" style="font-size:9px;"></i> {{ $sale->shop->phone_number }}
                                        </div>
                                    @endif
                                </td>
                                <td style="font-size:13px;">
                                    {{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') : '—' }}
                                </td>
                                <td class="text-right font-weight-bold" style="color:#4e73df;font-size:13px;">
                                    {{ number_format($sale->total_amount, 0) }}
                                </td>
                                <td class="text-right" style="color:#1cc88a;font-weight:600;font-size:13px;">
                                    {{ number_format($sale->received_amount, 0) }}
                                </td>
                                <td class="text-right" style="font-size:13px;">
                                    @if($sale->pending_amount > 0)
                                        <span style="color:#e65100;font-weight:600;">{{ number_format($sale->pending_amount, 0) }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center pr-3 no-print">
                                    <a href="{{ route('admin.sales.receipt', ['id' => $sale->id]) }}"
                                       class="btn btn-sm btn-outline-primary" target="_blank"
                                       style="border-radius:6px;font-size:11px;">
                                        <i class="fas fa-print mr-1"></i> Receipt
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4 text-muted">No sales found for the selected period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>
@endsection

@section('scripts')
<style>
    @media print {
        .no-print { display: none !important; }
        .main-sidebar, .main-header, .content-header { display: none !important; }
        .content-wrapper { margin-left: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #ddd !important; }
    }
</style>
<script>
    function applyFilter() {
        const from = document.getElementById('from').value;
        const to   = document.getElementById('to').value;
        const params = new URLSearchParams();
        if (from) params.set('from', from);
        if (to)   params.set('to', to);
        const query = params.toString();
        window.location.href = '{{ route('admin.sales.report') }}' + (query ? '?' + query : '');
    }

    $(function () {
        $('#salesReportTable').DataTable({
            paging: true,
            pageLength: 25,
            lengthChange: false,
            searching: true,
            ordering: true,
            order: [[2, 'desc']],
            info: true,
            autoWidth: false,
            responsive: true,
            columnDefs: [{ orderable: false, targets: [6] }],
        });

        $('#salesByShopTable').DataTable({
            paging: false,
            searching: false,
            ordering: true,
            info: false,
            autoWidth: false,
        });
    });
</script>
@endsection
