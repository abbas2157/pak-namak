@extends('admin.layout.app')
@section('title', $shop ? 'Sales — ' . $shop->name : 'Sales by Shop')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Sales by Shop <small class="text-muted">دکان کی فروخت</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.shops.index') }}">Shops</a></li>
                    <li class="breadcrumb-item active">{{ $shop ? $shop->name : 'Select Shop' }}</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end gap-2">
                @if($shop)
                    <a href="{{ route('admin.sales.create') }}"
                       class="btn btn-success px-3 btn-modal-save mr-2">
                        <i class="fas fa-plus mr-1"></i> New Sale / نئی فروخت
                    </a>
                @endif
                <a href="{{ route('admin.shops.index') }}"
                   class="btn btn-light px-3 btn-modal-cancel">
                    <i class="fas fa-arrow-left mr-1"></i> All Shops / تمام دکانیں
                </a>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        {{-- ===== SHOP SELECTOR ===== --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <form method="GET" class="d-flex align-items-center flex-wrap">
                    <label class="pn-label text-uppercase font-weight-bold text-muted mb-0 mr-2">
                        <i class="fas fa-store mr-1"></i> Select Shop / دکان منتخب کریں
                    </label>
                    <select name="shop_id" class="form-control fc-pn mr-2" style="max-width:280px;"
                            onchange="this.form.submit()">
                        <option value="">— Choose a shop —</option>
                        @foreach($shops as $s)
                            <option value="{{ $s->id }}" {{ $shop && $shop->id == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}{{ $s->city ? ' ('.$s->city.')' : '' }}
                                {{ $s->status === 'inactive' ? '— Inactive' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @if($shop && $selectedMonth)
                        <input type="hidden" name="month" value="{{ $selectedMonth }}">
                    @endif
                    @if(!$shop)
                        <span class="text-muted">Choose a shop above to view its sales history.</span>
                    @endif
                </form>
            </div>
        </div>

        @if(!$shop)
            {{-- No shop selected — show all shops quick-select cards --}}
            <div class="row">
                @foreach($shops as $s)
                <div class="col-md-4 col-sm-6 mb-3">
                    <a href="{{ route('admin.sales.by_shop', ['shop_id' => $s->id]) }}"
                       class="card border-0 shadow-sm h-100 text-decoration-none pn-bl-blue">
                        <div class="card-body py-3 px-3">
                            <div class="font-weight-bold text-c-dark">{{ $s->name }}</div>
                            @if($s->owner_name)
                                <small class="text-muted d-block"><i class="fas fa-user mr-1"></i>{{ $s->owner_name }}</small>
                            @endif
                            @if($s->phone_number)
                                <small class="text-muted d-block"><i class="fas fa-phone mr-1"></i>{{ $s->phone_number }}</small>
                            @endif
                            @if($s->city)
                                <small class="text-muted d-block"><i class="fas fa-map-marker-alt mr-1"></i>{{ $s->city }}</small>
                            @endif
                            <div class="mt-2">
                                @if($s->status === 'active')
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-warning">Inactive</span>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        @else

        {{-- ===== SHOP INFO CARD ===== --}}
        <div class="card border-0 shadow-sm mb-4 pn-bl-blue">
            <div class="card-body py-3 px-4">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="pn-icon pn-icon-md pni-blue">
                            <i class="fas fa-store"></i>
                        </div>
                    </div>
                    <div class="col">
                        <div class="font-weight-bold text-c-dark h5 mb-0">{{ $shop->name }}</div>
                        <div class="d-flex flex-wrap mt-1">
                            @if($shop->owner_name)
                                <small class="text-muted mr-3"><i class="fas fa-user mr-1"></i>{{ $shop->owner_name }}</small>
                            @endif
                            @if($shop->phone_number)
                                <small class="text-muted mr-3"><i class="fas fa-phone mr-1"></i>{{ $shop->phone_number }}</small>
                            @endif
                            @if($shop->city)
                                <small class="text-muted mr-3"><i class="fas fa-map-marker-alt mr-1"></i>{{ $shop->city }}</small>
                            @endif
                            @if($shop->address)
                                <small class="text-muted mr-3"><i class="fas fa-home mr-1"></i>{{ $shop->address }}</small>
                            @endif
                        </div>
                    </div>
                    <div class="col-auto">
                        @if($shop->status === 'active')
                            <span class="badge badge-success px-3 py-2">Active</span>
                        @else
                            <span class="badge badge-warning px-3 py-2">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

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
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Pending / Udhaar / باقی اُدھار</div>
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

        {{-- ===== TABLE + SIDEBAR ===== --}}
        <div class="row">

            {{-- TABLE --}}
            <div class="col-lg-8 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h6 class="mb-0 font-weight-bold text-c-blue2">
                                <i class="fas fa-table mr-2"></i>Sale Transactions / فروخت کی فہرست
                            </h6>
                            @if($selectedMonth)
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y') }}
                                </small>
                            @else
                                <small class="text-muted">All time</small>
                            @endif
                        </div>
                        <span class="badge bdg-blue bdg-md">
                            {{ $sales->count() }} records
                        </span>
                    </div>
                    <div class="card-body p-2">
                        <div class="table-responsive">
                            <table class="table mb-0 pn-table pn-table-font" id="shopSalesTable">
                                <thead>
                                    <tr>
                                        <th class="pl-3 py-3 text-uppercase">Date / تاریخ</th>
                                        <th class="py-3 text-uppercase">Types / اقسام</th>
                                        <th class="py-3 text-right text-uppercase">Total / کل</th>
                                        <th class="py-3 text-right text-uppercase">Received / وصول</th>
                                        <th class="py-3 text-right text-uppercase">Pending / باقی</th>
                                        <th class="py-3 text-center text-uppercase">Actions / اقدامات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sales as $sale)
                                        <tr id="row_{{ $sale->id }}" class="pn-table-row">
                                            <td class="pl-3 py-3 align-middle">
                                                <span class="font-weight-bold d-block text-c-dark">
                                                    {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M') }}
                                                </span>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($sale->sale_date)->format('Y') }}</small>
                                            </td>
                                            <td class="py-3 align-middle text-nowrap">
                                                @if($sale->dalla)
                                                    <span class="badge bdg-blue bdg-sm mr-1">Dalla</span>
                                                @endif
                                                @if($sale->thailas->count())
                                                    <span class="badge bdg-teal bdg-sm mr-1">Thaila</span>
                                                @endif
                                                @if($sale->packages->count())
                                                    <span class="badge bdg-yellow bdg-sm">Packages</span>
                                                @endif
                                            </td>
                                            <td class="py-3 align-middle text-right">
                                                <span class="font-weight-bold text-c-dark">
                                                    {{ number_format($sale->total_amount, 0) }}
                                                </span>
                                            </td>
                                            <td class="py-3 align-middle text-right">
                                                <span class="text-c-teal font-weight-bold">
                                                    {{ number_format($sale->received_amount, 0) }}
                                                </span>
                                            </td>
                                            <td class="py-3 align-middle text-right">
                                                @if($sale->pending_amount > 0)
                                                    <span class="font-weight-bold text-c-red">
                                                        {{ number_format($sale->pending_amount, 0) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="py-3 align-middle text-center text-nowrap">
                                                <button class="btn btn-sm btn-pn btn-act-view viewBtn mr-1"
                                                        data-id="{{ $sale->id }}"
                                                        title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <a href="{{ route('admin.sales.receipt', ['id' => $sale->id]) }}"
                                                   target="_blank"
                                                   class="btn btn-sm btn-pn btn-act-print"
                                                   title="Print Receipt">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                @if(!empty($sale->bill_image))
                                                    <a href="{{ asset($sale->bill_image) }}" target="_blank"
                                                       class="btn btn-sm btn-pn btn-act-bill ml-1"
                                                       title="View Bill Image">
                                                        <i class="fas fa-image"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <i class="fas fa-file-invoice fa-3x mb-3 d-block text-muted"></i>
                                                <p class="text-muted mb-0">No sales found for this period.</p>
                                                <a href="{{ route('admin.sales.create') }}" class="btn btn-sm btn-primary mt-3">
                                                    <i class="fas fa-plus mr-1"></i> Create Sale
                                                </a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($sales->count() > 0)
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
                            <input type="hidden" name="shop_id" value="{{ $shop->id }}">
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
                            <a href="{{ route('admin.sales.by_shop', ['shop_id' => $shop->id]) }}"
                               class="btn btn-block btn-sm mt-1 btn-clear-filter">
                                <i class="fas fa-times mr-1"></i> Show All Time
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Product Breakdown --}}
                @php
                    $grandBreak = ($dallaStats->total ?? 0) + ($thailaStats->total ?? 0) + ($packageStats->total ?? 0);
                @endphp
                @if($grandBreak > 0)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-c-red">
                            <i class="fas fa-chart-pie mr-2"></i>Product Breakdown / مصنوعات کی تفصیل
                        </h6>
                    </div>
                    <div class="card-body py-3 px-3">
                        @foreach([
                            ['Dalla',    $dallaStats->total   ?? 0, $dallaStats->total_kg   ?? 0, $dallaStats->count   ?? 0, 'text-c-blue2', 'pbar-blue',   'fa-tint'],
                            ['Thaila',   $thailaStats->total  ?? 0, $thailaStats->total_kg  ?? 0, $thailaStats->count  ?? 0, 'text-c-teal',  'pbar-teal',   'fa-shopping-bag'],
                            ['Packages', $packageStats->total ?? 0, $packageStats->total_kg ?? 0, $packageStats->count ?? 0, 'text-c-warn',  'pbar-yellow', 'fa-box'],
                        ] as [$label, $amount, $kg, $cnt, $clr, $pbarCls, $icon])
                            @php $pct = $grandBreak > 0 ? round(($amount / $grandBreak) * 100) : 0; @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="breakdown-lbl">
                                        <i class="fas {{ $icon }} mr-1 {{ $clr }} icon-fw14"></i>
                                        <strong>{{ $label }}</strong>
                                        <small class="text-muted ml-1">{{ $cnt }} txn</small>
                                    </span>
                                    <span class="breakdown-lbl-val">
                                        <span class="breakdown-lbl-num">{{ number_format($amount, 0) }}</span>
                                        <span class="text-muted ml-1">{{ $pct }}%</span>
                                    </span>
                                </div>
                                <div class="progress breakdown-bar">
                                    <div class="progress-bar pbar {{ $pbarCls }}" style="--w:{{ $pct }}%"></div>
                                </div>
                                @if($kg > 0)
                                    <small class="text-muted">{{ number_format($kg, 0) }} KG total</small>
                                @endif
                            </div>
                        @endforeach
                        <div class="d-flex justify-content-between align-items-center pt-2 mt-1 breakdown-grand-total-bar">
                            <span class="breakdown-lbl font-weight-bold">Grand Total / کل مجموعہ</span>
                            <span class="text-c-red pn-stat-num-sm font-weight-bold">
                                PKR {{ number_format($grandBreak, 0) }}
                            </span>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Payment Summary --}}
                @if($sales->count() > 0)
                <div class="card border-0 shadow-sm">
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
                            <div class="progress breakdown-bar-md">
                                <div class="progress-bar pbar pbar-teal" style="--w:{{ $recPct }}%"></div>
                            </div>
                        </div>
                        <div class="mb-1">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="breakdown-lbl"><strong>Pending / باقی</strong></span>
                                <span class="breakdown-lbl-val">
                                    <span class="breakdown-lbl-num text-c-red">{{ number_format($totalPending, 0) }}</span>
                                    <small class="text-muted">({{ $penPct }}%)</small>
                                </span>
                            </div>
                            <div class="progress breakdown-bar-md">
                                <div class="progress-bar pbar pbar-red" style="--w:{{ $penPct }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

            </div>{{-- /sidebar --}}
        </div>

        @endif {{-- end if $shop --}}

    </div>
</section>

{{-- ===== SALE DETAIL MODAL ===== --}}
<div class="modal fade" id="saleDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 modal-pn">
            <div class="modal-header border-0 text-white px-4 py-3">
                <h5 class="modal-title"><i class="fas fa-file-invoice mr-2"></i>Sale Details / فروخت کی تفصیل</h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body px-4 py-4" id="saleDetailBody">
                <div class="text-center py-5">
                    <span class="spinner-border text-c-blue2"></span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(function () {

    @if($shop)
    $('#shopSalesTable').DataTable({
        paging: true,
        pageLength: 15,
        lengthChange: false,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        responsive: true,
        order: [[0, 'desc']],
        columnDefs: [{ orderable: false, targets: [1, 5] }],
        language: {
            search: '',
            searchPlaceholder: 'Search sales...',
            info: 'Showing _START_–_END_ of _TOTAL_',
            paginate: { previous: '‹', next: '›' }
        }
    });
    @endif

    // View sale detail
    $(document).on('click', '.viewBtn', function () {
        const id = $(this).data('id');
        $('#saleDetailBody').html('<div class="text-center py-5"><span class="spinner-border text-c-blue2"></span></div>');
        $('#saleDetailModal').modal('show');
        $.get(APP_URL + '/sales/' + id, function (html) {
            $('#saleDetailBody').html(html);
        }).fail(function () {
            $('#saleDetailBody').html('<p class="text-danger text-center py-4">Could not load sale details.</p>');
        });
    });

});
</script>
@endsection
