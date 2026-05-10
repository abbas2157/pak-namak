@extends('admin.layout.app')
@section('title', $shop ? 'Sales — ' . $shop->name : 'Sales by Shop')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Sales by Shop</h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.shops.index') }}">Shops</a></li>
                    <li class="breadcrumb-item active">{{ $shop ? $shop->name : 'Select Shop' }}</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end gap-2">
                @if($shop)
                    <a href="{{ route('admin.sales.create') }}"
                       class="btn btn-success px-3" style="border-radius:8px;">
                        <i class="fas fa-plus mr-1"></i> New Sale
                    </a>
                @endif
                <a href="{{ route('admin.shops.index') }}"
                   class="btn btn-light px-3" style="border-radius:8px;border:1px solid #d1d5db;">
                    <i class="fas fa-arrow-left mr-1"></i> All Shops
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
                <form method="GET" class="d-flex align-items-center flex-wrap" style="gap:12px;">
                    <label class="text-uppercase font-weight-bold text-muted mb-0" style="font-size:11px;letter-spacing:.5px;white-space:nowrap;">
                        <i class="fas fa-store mr-1"></i> Select Shop
                    </label>
                    <select name="shop_id" class="form-control" style="max-width:280px;border-radius:8px;border-color:#d1d5db;"
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
                        <span class="text-muted" style="font-size:13px;">Choose a shop above to view its sales history.</span>
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
                       class="card border-0 shadow-sm h-100 text-decoration-none"
                       style="border-left:4px solid #4e73df !important; transition:box-shadow .2s;">
                        <div class="card-body py-3 px-3">
                            <div class="font-weight-bold" style="color:#2d3748;font-size:15px;">{{ $s->name }}</div>
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
                                    <span class="badge badge-success" style="font-size:10px;">Active</span>
                                @else
                                    <span class="badge badge-warning" style="font-size:10px;">Inactive</span>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        @else

        {{-- ===== SHOP INFO CARD ===== --}}
        <div class="card border-0 shadow-sm mb-4" style="border-left:4px solid #4e73df !important;">
            <div class="card-body py-3 px-4">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:52px;height:52px;background:rgba(78,115,223,.12);">
                            <i class="fas fa-store" style="color:#4e73df;font-size:22px;"></i>
                        </div>
                    </div>
                    <div class="col">
                        <div class="font-weight-bold" style="color:#2d3748;font-size:18px;">{{ $shop->name }}</div>
                        <div class="d-flex flex-wrap mt-1" style="gap:14px;">
                            @if($shop->owner_name)
                                <small class="text-muted"><i class="fas fa-user mr-1"></i>{{ $shop->owner_name }}</small>
                            @endif
                            @if($shop->phone_number)
                                <small class="text-muted"><i class="fas fa-phone mr-1"></i>{{ $shop->phone_number }}</small>
                            @endif
                            @if($shop->city)
                                <small class="text-muted"><i class="fas fa-map-marker-alt mr-1"></i>{{ $shop->city }}</small>
                            @endif
                            @if($shop->address)
                                <small class="text-muted"><i class="fas fa-home mr-1"></i>{{ $shop->address }}</small>
                            @endif
                        </div>
                    </div>
                    <div class="col-auto">
                        @if($shop->status === 'active')
                            <span class="badge badge-success px-3 py-2" style="font-size:12px;">Active</span>
                        @else
                            <span class="badge badge-warning px-3 py-2" style="font-size:12px;">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== STATS ===== --}}
        <div class="row mb-3">
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #4e73df !important;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Sales</div>
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
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Revenue</div>
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
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Received</div>
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
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Pending / Udhaar</div>
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

        {{-- ===== TABLE + SIDEBAR ===== --}}
        <div class="row">

            {{-- TABLE --}}
            <div class="col-lg-8 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h6 class="mb-0 font-weight-bold" style="color:#4e73df;">
                                <i class="fas fa-table mr-2"></i>Sale Transactions
                            </h6>
                            @if($selectedMonth)
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y') }}
                                </small>
                            @else
                                <small class="text-muted">All time</small>
                            @endif
                        </div>
                        <span class="badge" style="background:#e8f0fe;color:#4e73df;font-size:12px;padding:5px 10px;border-radius:20px;">
                            {{ $sales->count() }} records
                        </span>
                    </div>
                    <div class="card-body p-2">
                        <div class="table-responsive">
                            <table class="table mb-0" id="shopSalesTable" style="font-size:13.5px;">
                                <thead>
                                    <tr style="background:#f8f9fc;border-bottom:2px solid #e3e6f0;">
                                        <th class="pl-3 py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Date</th>
                                        <th class="py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Types</th>
                                        <th class="py-3 text-right text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Total</th>
                                        <th class="py-3 text-right text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Received</th>
                                        <th class="py-3 text-right text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Pending</th>
                                        <th class="py-3 text-center text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sales as $sale)
                                        <tr id="row_{{ $sale->id }}" style="border-bottom:1px solid #f0f0f0;">
                                            <td class="pl-3 py-3 align-middle">
                                                <span class="font-weight-bold d-block" style="color:#2d3748;">
                                                    {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M') }}
                                                </span>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($sale->sale_date)->format('Y') }}</small>
                                            </td>
                                            <td class="py-3 align-middle" style="white-space:nowrap;">
                                                @if($sale->dalla)
                                                    <span class="badge mr-1" style="background:#e8f0fe;color:#4e73df;font-size:10px;padding:3px 7px;border-radius:20px;">Dalla</span>
                                                @endif
                                                @if($sale->thailas->count())
                                                    <span class="badge mr-1" style="background:#d4edda;color:#155724;font-size:10px;padding:3px 7px;border-radius:20px;">Thaila</span>
                                                @endif
                                                @if($sale->packages->count())
                                                    <span class="badge" style="background:#fff3cd;color:#856404;font-size:10px;padding:3px 7px;border-radius:20px;">Packages</span>
                                                @endif
                                            </td>
                                            <td class="py-3 align-middle text-right">
                                                <span class="font-weight-bold" style="color:#2d3748;">
                                                    {{ number_format($sale->total_amount, 0) }}
                                                </span>
                                            </td>
                                            <td class="py-3 align-middle text-right">
                                                <span style="color:#1cc88a;font-weight:600;">
                                                    {{ number_format($sale->received_amount, 0) }}
                                                </span>
                                            </td>
                                            <td class="py-3 align-middle text-right">
                                                @if($sale->pending_amount > 0)
                                                    <span class="font-weight-bold" style="color:#e74a3b;">
                                                        {{ number_format($sale->pending_amount, 0) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="py-3 align-middle text-center" style="white-space:nowrap;">
                                                <button class="btn btn-sm viewBtn mr-1"
                                                        data-id="{{ $sale->id }}"
                                                        style="background:#e8f0fe;color:#4e73df;border:1px solid #c3d3f7;border-radius:6px;"
                                                        title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <a href="{{ route('admin.sales.receipt', ['id' => $sale->id]) }}"
                                                   target="_blank"
                                                   class="btn btn-sm"
                                                   style="background:#f3f4f6;color:#374151;border:1px solid #d1d5db;border-radius:6px;"
                                                   title="Print Receipt">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                @if(!empty($sale->bill_image))
                                                    <a href="{{ asset($sale->bill_image) }}" target="_blank"
                                                       class="btn btn-sm ml-1"
                                                       style="background:#d4edda;color:#155724;border:1px solid #c3e6cb;border-radius:6px;"
                                                       title="View Bill Image">
                                                        <i class="fas fa-image"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <i class="fas fa-file-invoice fa-3x mb-3 d-block" style="color:#d1d5db;"></i>
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
                                    <tr style="background:#f8f9fc;border-top:2px solid #e3e6f0;">
                                        <td class="pl-3 py-3 font-weight-bold" colspan="2" style="color:#2d3748;">Totals</td>
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
                            <i class="fas fa-calendar-alt mr-2"></i>Filter by Month
                        </h6>
                    </div>
                    <div class="card-body py-3">
                        <form method="GET">
                            <input type="hidden" name="shop_id" value="{{ $shop->id }}">
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
                            <a href="{{ route('admin.sales.by_shop', ['shop_id' => $shop->id]) }}"
                               class="btn btn-block btn-sm mt-1"
                               style="background:#f3f4f6;color:#6b7280;border-radius:8px;border:1px solid #d1d5db;">
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
                        <h6 class="mb-0 font-weight-bold" style="color:#e74a3b;">
                            <i class="fas fa-chart-pie mr-2"></i>Product Breakdown
                        </h6>
                    </div>
                    <div class="card-body py-3 px-3">
                        @foreach([
                            ['Dalla',    $dallaStats->total   ?? 0, $dallaStats->total_kg   ?? 0, $dallaStats->count   ?? 0, '#4e73df', 'fa-tint'],
                            ['Thaila',   $thailaStats->total  ?? 0, $thailaStats->total_kg  ?? 0, $thailaStats->count  ?? 0, '#1cc88a', 'fa-shopping-bag'],
                            ['Packages', $packageStats->total ?? 0, $packageStats->total_kg ?? 0, $packageStats->count ?? 0, '#f6c23e', 'fa-box'],
                        ] as [$label, $amount, $kg, $cnt, $color, $icon])
                            @php $pct = $grandBreak > 0 ? round(($amount / $grandBreak) * 100) : 0; @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span style="font-size:13px;color:#374151;">
                                        <i class="fas {{ $icon }} mr-1" style="color:{{ $color }};width:14px;text-align:center;"></i>
                                        <strong>{{ $label }}</strong>
                                        <small class="text-muted ml-1">{{ $cnt }} txn</small>
                                    </span>
                                    <span style="font-size:12px;">
                                        <span style="color:#374151;font-weight:600;">{{ number_format($amount, 0) }}</span>
                                        <span class="text-muted ml-1">{{ $pct }}%</span>
                                    </span>
                                </div>
                                <div class="progress" style="height:5px;border-radius:10px;background:#f0f0f0;">
                                    <div class="progress-bar" style="width:{{ $pct }}%;background:{{ $color }};border-radius:10px;transition:width .6s ease;"></div>
                                </div>
                                @if($kg > 0)
                                    <small class="text-muted">{{ number_format($kg, 0) }} KG total</small>
                                @endif
                            </div>
                        @endforeach
                        <div class="d-flex justify-content-between align-items-center pt-2 mt-1"
                             style="border-top:2px solid #f0f0f0;">
                            <span class="font-weight-bold" style="color:#374151;">Grand Total</span>
                            <span class="font-weight-bold" style="color:#e74a3b;font-size:15px;">
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
                        <h6 class="mb-0 font-weight-bold" style="color:#1cc88a;">
                            <i class="fas fa-wallet mr-2"></i>Payment Summary
                        </h6>
                    </div>
                    <div class="card-body py-3 px-3">
                        @php
                            $recPct = $totalRevenue > 0 ? round(($totalReceived / $totalRevenue) * 100) : 0;
                            $penPct = $totalRevenue > 0 ? round(($totalPending  / $totalRevenue) * 100) : 0;
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span style="font-size:13px;color:#374151;"><strong>Received</strong></span>
                                <span style="font-size:12px;color:#1cc88a;font-weight:600;">{{ number_format($totalReceived, 0) }} <small class="text-muted">({{ $recPct }}%)</small></span>
                            </div>
                            <div class="progress" style="height:7px;border-radius:10px;background:#f0f0f0;">
                                <div class="progress-bar" style="width:{{ $recPct }}%;background:#1cc88a;border-radius:10px;"></div>
                            </div>
                        </div>
                        <div class="mb-1">
                            <div class="d-flex justify-content-between mb-1">
                                <span style="font-size:13px;color:#374151;"><strong>Pending</strong></span>
                                <span style="font-size:12px;color:#e74a3b;font-weight:600;">{{ number_format($totalPending, 0) }} <small class="text-muted">({{ $penPct }}%)</small></span>
                            </div>
                            <div class="progress" style="height:7px;border-radius:10px;background:#f0f0f0;">
                                <div class="progress-bar" style="width:{{ $penPct }}%;background:#e74a3b;border-radius:10px;"></div>
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
        <div class="modal-content border-0" style="border-radius:12px;overflow:hidden;">
            <div class="modal-header border-0 text-white px-4 py-3"
                 style="background:linear-gradient(135deg,#4e73df,#224abe);">
                <h5 class="modal-title"><i class="fas fa-file-invoice mr-2"></i>Sale Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" style="opacity:.8;">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body px-4 py-4" id="saleDetailBody">
                <div class="text-center py-5">
                    <span class="spinner-border" style="color:#4e73df;"></span>
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
        $('#saleDetailBody').html('<div class="text-center py-5"><span class="spinner-border" style="color:#4e73df;"></span></div>');
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
