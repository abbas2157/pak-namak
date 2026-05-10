@extends('admin.layout.app')
@section('title', 'Sales')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Sales</h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Sales</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end">
                <a href="{{ route('admin.sales.create') }}"
                   class="btn btn-primary px-4" style="border-radius:8px;">
                    <i class="fas fa-plus mr-1"></i> New Sale
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
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Sales</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ $totalCount }}</div>
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
            <div class="col-lg-9 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h6 class="mb-0 font-weight-bold" style="color:#4e73df;">
                                <i class="fas fa-table mr-2"></i>Sale Records
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
                            {{ $totalCount }} records
                        </span>
                    </div>
                    <div class="card-body p-2">
                        <div class="table-responsive">
                            <table class="table mb-0" id="salesTable" style="font-size:13.5px;">
                                <thead>
                                    <tr style="background:#f8f9fc;border-bottom:2px solid #e3e6f0;">
                                        <th class="pl-3 py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Date</th>
                                        <th class="py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Shop</th>
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
                                            <td class="py-3 align-middle">
                                                <span class="font-weight-bold d-block" style="color:#2d3748;">
                                                    {{ $sale->shop->name ?? '—' }}
                                                </span>
                                                @if($sale->shop?->phone_number)
                                                    <small class="text-muted">{{ $sale->shop->phone_number }}</small>
                                                @endif
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
                                                   class="btn btn-sm mr-1"
                                                   style="background:#f3f4f6;color:#374151;border:1px solid #d1d5db;border-radius:6px;"
                                                   title="Print Receipt">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                @if(!empty($sale->bill_image))
                                                    <a href="{{ asset($sale->bill_image) }}" target="_blank"
                                                       class="btn btn-sm mr-1"
                                                       style="background:#d4edda;color:#155724;border:1px solid #c3e6cb;border-radius:6px;"
                                                       title="View Bill">
                                                        <i class="fas fa-image"></i>
                                                    </a>
                                                @endif
                                                <button class="btn btn-sm deleteBtn"
                                                        data-id="{{ $sale->id }}"
                                                        style="background:#fce8e6;color:#c62828;border:1px solid #ef9a9a;border-radius:6px;"
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
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
                                        <td class="pl-3 py-3 font-weight-bold" colspan="3" style="color:#2d3748;">Totals</td>
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
            <div class="col-lg-3">

                {{-- Month Filter --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold" style="color:#4e73df;">
                            <i class="fas fa-calendar-alt mr-2"></i>Filter by Month
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
                            <a href="{{ route('admin.sales.index') }}"
                               class="btn btn-block btn-sm mt-1"
                               style="background:#f3f4f6;color:#6b7280;border-radius:8px;border:1px solid #d1d5db;">
                                <i class="fas fa-times mr-1"></i> Show All Time
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Payment summary --}}
                @if($totalRevenue > 0)
                <div class="card border-0 shadow-sm mb-3">
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
                                <span style="font-size:12px;color:#1cc88a;font-weight:600;">
                                    {{ number_format($totalReceived, 0) }}
                                    <small class="text-muted">({{ $recPct }}%)</small>
                                </span>
                            </div>
                            <div class="progress" style="height:7px;border-radius:10px;background:#f0f0f0;">
                                <div class="progress-bar" style="width:{{ $recPct }}%;background:#1cc88a;border-radius:10px;"></div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between mb-1">
                                <span style="font-size:13px;color:#374151;"><strong>Pending</strong></span>
                                <span style="font-size:12px;color:#e74a3b;font-weight:600;">
                                    {{ number_format($totalPending, 0) }}
                                    <small class="text-muted">({{ $penPct }}%)</small>
                                </span>
                            </div>
                            <div class="progress" style="height:7px;border-radius:10px;background:#f0f0f0;">
                                <div class="progress-bar" style="width:{{ $penPct }}%;background:#e74a3b;border-radius:10px;"></div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-3 mt-2"
                             style="border-top:2px solid #f0f0f0;">
                            <span class="font-weight-bold" style="color:#374151;">Total</span>
                            <span class="font-weight-bold" style="color:#2d3748;font-size:15px;">
                                PKR {{ number_format($totalRevenue, 0) }}
                            </span>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Product type breakdown --}}
                @if($sales->count() > 0)
                @php
                    $dallaTotal   = $sales->sum(fn($s) => $s->dalla?->sub_total ?? 0);
                    $thailaTotal  = $sales->flatMap(fn($s) => $s->thailas)->sum('sub_total');
                    $packageTotal = $sales->flatMap(fn($s) => $s->packages)->sum('sub_total');
                    $typeGrand    = $dallaTotal + $thailaTotal + $packageTotal;
                @endphp
                @if($typeGrand > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold" style="color:#e74a3b;">
                            <i class="fas fa-chart-pie mr-2"></i>Product Breakdown
                        </h6>
                    </div>
                    <div class="card-body py-3 px-3">
                        @foreach([
                            ['Dalla',    $dallaTotal,   '#4e73df', 'fa-tint'],
                            ['Thaila',   $thailaTotal,  '#1cc88a', 'fa-shopping-bag'],
                            ['Packages', $packageTotal, '#f6c23e', 'fa-box'],
                        ] as [$label, $amount, $color, $icon])
                            @php $pct = $typeGrand > 0 ? round(($amount / $typeGrand) * 100) : 0; @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span style="font-size:13px;color:#374151;">
                                        <i class="fas {{ $icon }} mr-1" style="color:{{ $color }};"></i>
                                        <strong>{{ $label }}</strong>
                                    </span>
                                    <span style="font-size:12px;">
                                        <span style="color:#374151;font-weight:600;">{{ number_format($amount, 0) }}</span>
                                        <span class="text-muted ml-1">{{ $pct }}%</span>
                                    </span>
                                </div>
                                <div class="progress" style="height:5px;border-radius:10px;background:#f0f0f0;">
                                    <div class="progress-bar" style="width:{{ $pct }}%;background:{{ $color }};border-radius:10px;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
                @endif

            </div>{{-- /sidebar --}}
        </div>

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

    $('#salesTable').DataTable({
        paging: true,
        pageLength: 20,
        lengthChange: false,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        responsive: true,
        order: [[0, 'desc']],
        columnDefs: [{ orderable: false, targets: [2, 6] }],
        language: {
            search: '',
            searchPlaceholder: 'Search sales...',
            info: 'Showing _START_–_END_ of _TOTAL_',
            paginate: { previous: '‹', next: '›' }
        }
    });

    // View detail
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

    // Delete
    $(document).on('click', '.deleteBtn', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Delete this sale?',
            text: 'This cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then(result => {
            if (!result.isConfirmed) return;
            $.post(APP_URL + '/sales/' + id, {
                _method: 'DELETE',
                _token: '{{ csrf_token() }}'
            }, function () {
                $('#row_' + id).fadeOut(300, function () { $(this).remove(); });
                toastr.success('Sale deleted.');
            }).fail(function () {
                toastr.error('Could not delete sale.');
            });
        });
    });

});
</script>
@endsection
