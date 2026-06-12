@extends('admin.layout.app')
@section('title', 'Sales Report')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Sales Report <small class="text-muted">فروخت رپورٹ</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Sales Report</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end gap-2 no-print">
                <button class="btn btn-outline-secondary btn-modal-cancel" onclick="window.print()">
                    <i class="fas fa-print mr-1"></i> Print / PDF
                </button>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        {{-- ── DATE FILTER ──────────────────────────────── --}}
        <div class="card border-0 shadow-sm mb-4 card-pn no-print">
            <div class="card-body py-3">
                <div class="row align-items-end">
                    <div class="col-md-3 mb-2 mb-md-0">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">From Date / آغاز تاریخ</label>
                        <input type="date" class="form-control fc-pn" id="from" value="{{ $from }}">
                    </div>
                    <div class="col-md-3 mb-2 mb-md-0">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">To Date / اختتام تاریخ</label>
                        <input type="date" class="form-control fc-pn" id="to" value="{{ $to }}">
                    </div>
                    <div class="col-md-3 mb-2 mb-md-0">
                        <button class="btn btn-primary px-4 btn-modal-save" onclick="applyFilter()">
                            <i class="fas fa-search mr-1"></i> Apply Filter / فلٹر لگائیں
                        </button>
                        @if($from || $to)
                            <a href="{{ route('admin.sales.report') }}" class="btn btn-outline-secondary btn-modal-cancel ml-2">
                                <i class="fas fa-times mr-1"></i> Clear
                            </a>
                        @endif
                    </div>
                    <div class="col-md-3 text-muted">
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
                <div class="card border-0 shadow-sm h-100 card-pn pn-bl-blue">
                    <div class="card-body py-3 px-4">
                        <div class="pn-form-col-lbl text-uppercase">Total Revenue / کل آمدن</div>
                        <div class="pn-stat-num-lg text-c-blue2">{{ number_format($grandTotal, 0) }}</div>
                        <div class="text-c-muted2">PKR across {{ $sales->count() }} invoices</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100 card-pn pn-bl-teal">
                    <div class="card-body py-3 px-4">
                        <div class="pn-form-col-lbl text-uppercase">Received / وصول شدہ</div>
                        <div class="pn-stat-num-lg text-c-teal">{{ number_format($grandReceived, 0) }}</div>
                        <div class="text-c-muted2">
                            {{ $grandTotal > 0 ? number_format(($grandReceived/$grandTotal)*100,1) : 0 }}% collected
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100 card-pn pn-bl-red">
                    <div class="card-body py-3 px-4">
                        <div class="pn-form-col-lbl text-uppercase">Pending اُدھار</div>
                        <div class="pn-stat-num-lg text-c-orange">{{ number_format($grandPending, 0) }}</div>
                        <div class="text-c-muted2">
                            {{ $grandTotal > 0 ? number_format(($grandPending/$grandTotal)*100,1) : 0 }}% outstanding
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100 card-pn bg-light">
                    <div class="card-body py-3 px-4">
                        <div class="pn-form-col-lbl text-uppercase">Product Breakdown / مصنوعات کی تفصیل</div>
                        <div class="mt-1">
                            <div class="d-flex justify-content-between text-muted">
                                <span class="text-primary font-weight-bold">Dalla</span>
                                <span>PKR {{ number_format($dallaStats->total ?? 0, 0) }}</span>
                            </div>
                            <div class="d-flex justify-content-between text-muted">
                                <span class="text-success font-weight-bold">Thaila</span>
                                <span>PKR {{ number_format($thailaStats->total ?? 0, 0) }}</span>
                            </div>
                            <div class="d-flex justify-content-between text-muted">
                                <span class="text-warning font-weight-bold">Package</span>
                                <span>PKR {{ number_format($packageStats->total ?? 0, 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── SALES BY SHOP ──────────────────────────────── --}}
        <div class="card border-0 shadow-sm mb-4 card-pn">
            <div class="card-header border-0 py-3 ch-blue">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="pn-form-col-lbl text-uppercase">Summary</div>
                        <h6 class="mb-0 font-weight-bold text-c-blue2">Sales by Shop / دکان کے مطابق فروخت</h6>
                    </div>
                    <span class="badge badge-primary bdg-md">
                        {{ $salesByShop->count() }} shops
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                @php $maxShopTotal = $salesByShop->max('total') ?: 1; @endphp
                <table class="table table-sm mb-0 pn-table pn-table-font" id="salesByShopTable">
                    <thead>
                        <tr>
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
                            <tr class="pn-table-row">
                                <td class="pl-3 text-muted pn-stat-sub">{{ $i + 1 }}</td>
                                <td>
                                    <div class="font-weight-bold">{{ $row->shop_name ?? '—' }}</div>
                                    @if($row->shop_phone)
                                        <div class="text-muted pn-stat-sub">
                                            <i class="fas fa-phone icon-9"></i> {{ $row->shop_phone }}
                                        </div>
                                    @endif
                                    <div class="progress breakdown-prog-3 mt-1" style="width:120px;">
                                        <div class="progress-bar pbar pbar-blue"
                                             style="--w:{{ ($row->total / $maxShopTotal) * 100 }}%"></div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light border">{{ $row->count }}</span>
                                </td>
                                <td class="text-right font-weight-bold text-c-blue2">
                                    {{ number_format($row->total, 0) }}
                                </td>
                                <td class="text-right text-c-teal font-weight-bold">
                                    {{ number_format($row->received, 0) }}
                                </td>
                                <td class="text-right pr-3">
                                    @if($row->pending > 0)
                                        <span class="text-c-orange font-weight-bold">{{ number_format($row->pending, 0) }}</span>
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
                        <tr class="pn-total-row">
                            <td colspan="2" class="pl-3">Total / کل</td>
                            <td class="text-center">{{ $sales->count() }}</td>
                            <td class="text-right text-c-blue2 font-weight-bold">{{ number_format($grandTotal, 0) }}</td>
                            <td class="text-right text-c-teal font-weight-bold">{{ number_format($grandReceived, 0) }}</td>
                            <td class="text-right pr-3 text-c-orange font-weight-bold">{{ number_format($grandPending, 0) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{-- ── ALL SALES TABLE ──────────────────────────────── --}}
        <div class="card border-0 shadow-sm card-pn">
            <div class="card-header border-0 py-3 ch-teal">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="pn-form-col-lbl text-uppercase">Detail</div>
                        <h6 class="mb-0 font-weight-bold text-c-teal">All Sales / تمام فروخت</h6>
                    </div>
                    <span class="badge badge-success bdg-md">
                        {{ $sales->count() }} records
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0 pn-table pn-table-font" id="salesReportTable">
                    <thead>
                        <tr>
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
                            <tr class="pn-table-row">
                                <td class="pl-3 text-muted pn-stat-sub">{{ $i + 1 }}</td>
                                <td>
                                    <div class="font-weight-bold">
                                        {{ $sale->shop->name ?? '—' }}
                                    </div>
                                    @if($sale->shop?->phone_number)
                                        <div class="text-muted pn-stat-sub">
                                            <i class="fas fa-phone icon-9"></i> {{ $sale->shop->phone_number }}
                                        </div>
                                    @endif
                                </td>
                                <td class="pn-table-font">
                                    {{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') : '—' }}
                                </td>
                                <td class="text-right font-weight-bold text-c-blue2">
                                    {{ number_format($sale->total_amount, 0) }}
                                </td>
                                <td class="text-right text-c-teal font-weight-bold">
                                    {{ number_format($sale->received_amount, 0) }}
                                </td>
                                <td class="text-right">
                                    @if($sale->pending_amount > 0)
                                        <span class="text-c-orange font-weight-bold">{{ number_format($sale->pending_amount, 0) }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center pr-3 no-print">
                                    <a href="{{ route('admin.sales.receipt', ['id' => $sale->id]) }}"
                                       class="btn btn-sm btn-pn btn-act-print" target="_blank">
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
