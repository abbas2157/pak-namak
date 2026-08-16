@extends('admin.layout.app')
@section('title', 'Spice Sales')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Spice Sales <small class="text-muted pn-stat-sub">مصالحہ فروخت</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Spice Sales</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end">
                <a href="{{ route('admin.spice-sales.create') }}"
                   class="btn btn-primary px-4 btn-pn">
                    <i class="fas fa-plus mr-1"></i> New Sale / نئی فروخت
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
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ $totalCount }}</div>
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

        <div class="row">
            <div class="col-lg-9 mb-3">
                <div class="card border-0 shadow-sm card-pn">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h6 class="mb-0 font-weight-bold text-c-blue2">
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
                        <span class="badge pn-bdg pn-bdg-blue">{{ $totalCount }} records</span>
                    </div>
                    <div class="card-body p-2">
                            <table class="table mb-0 pn-table pn-table-font" id="salesTable">
                                <thead>
                                    <tr>
                                        <th class="pl-3">Date / تاریخ</th>
                                        <th>Shop / دکان</th>
                                        <th>Spices / مصالحہ جات</th>
                                        <th class="text-right">Total / کل</th>
                                        <th class="text-right">Received / وصول</th>
                                        <th class="text-right">Pending / باقی</th>
                                        <th class="text-center">Actions / اقدامات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sales as $sale)
                                        <tr id="row_{{ $sale->id }}">
                                            <td class="pl-3 align-middle">
                                                <span class="font-weight-bold d-block pn-text-heading">
                                                    {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M') }}
                                                </span>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($sale->sale_date)->format('Y') }}</small>
                                            </td>
                                            <td class="align-middle">
                                                <span class="font-weight-bold d-block pn-text-heading">
                                                    {{ $sale->shop->name ?? '—' }}
                                                </span>
                                                @if($sale->shop?->phone_number)
                                                    <small class="text-muted">{{ $sale->shop->phone_number }}</small>
                                                @endif
                                            </td>
                                            <td class="align-middle text-nowrap">
                                                @foreach($sale->items->pluck('spiceType.title')->unique() as $title)
                                                    <span class="badge badge-item-package mr-1">{{ $title }}</span>
                                                @endforeach
                                            </td>
                                            <td class="align-middle text-right font-weight-bold pn-text-heading">
                                                {{ number_format($sale->total_amount, 0) }}
                                            </td>
                                            <td class="align-middle text-right font-weight-600 text-c-teal">
                                                {{ number_format($sale->received_amount, 0) }}
                                            </td>
                                            <td class="align-middle text-right">
                                                @if($sale->pending_amount > 0)
                                                    <span class="font-weight-bold text-c-red">
                                                        {{ number_format($sale->pending_amount, 0) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center text-nowrap">
                                                <button class="btn btn-sm btn-pn btn-act-view viewBtn mr-1"
                                                        data-id="{{ $sale->id }}" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-pn btn-act-edit quickEditBtn mr-1"
                                                        data-id="{{ $sale->id }}"
                                                        data-shop-id="{{ $sale->shop_id }}"
                                                        data-sale-date="{{ $sale->sale_date }}"
                                                        data-received="{{ $sale->received_amount }}"
                                                        data-total="{{ $sale->total_amount }}"
                                                        data-pending="{{ $sale->pending_amount }}"
                                                        data-remarks="{{ $sale->remarks ?? '' }}"
                                                        title="Quick Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-pn btn-act-confirm recordPaymentBtn mr-1"
                                                        data-id="{{ $sale->id }}"
                                                        data-shop="{{ $sale->shop->name ?? '' }}"
                                                        data-pending="{{ $sale->pending_amount }}"
                                                        title="Record Payment">
                                                    <i class="fas fa-hand-holding-dollar"></i>
                                                </button>
                                                <a href="{{ route('admin.spice-sales.edit', $sale->id) }}"
                                                   class="btn btn-sm btn-pn btn-act-view mr-1"
                                                   title="Full Edit">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <button class="btn btn-sm btn-pn btn-act-delete deleteBtn"
                                                        data-id="{{ $sale->id }}" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <i class="fas fa-file-invoice fa-3x mb-3 d-block icon-fade"></i>
                                                <p class="text-muted mb-0">No spice sales found for this period.</p>
                                                <a href="{{ route('admin.spice-sales.create') }}" class="btn btn-sm btn-primary btn-pn mt-3">
                                                    <i class="fas fa-plus mr-1"></i> Create Sale
                                                </a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($sales->count() > 0)
                                <tfoot>
                                    <tr class="pn-total-row">
                                        <td class="pl-3 py-3 font-weight-bold pn-text-heading" colspan="3">Totals / کل</td>
                                        <td class="py-3 text-right font-weight-bold pn-text-heading pn-stat-num-sm">
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

            <div class="col-lg-3">
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
                                    <option value="{{ $m->value }}" {{ $selectedMonth == $m->value ? 'selected' : '' }}>
                                        {{ $m->label }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                        @if($selectedMonth)
                            <a href="{{ route('admin.spice-sales.index') }}"
                               class="btn btn-block btn-sm btn-pn btn-clear-filter mt-1">
                                <i class="fas fa-times mr-1"></i> Show All Time
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- ===== SALE DETAIL MODAL ===== --}}
<div class="modal fade modal-pn" id="saleDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header px-4 py-3">
                <h5 class="modal-title"><i class="fas fa-file-invoice mr-2"></i>Sale Details</h5>
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

{{-- ===== QUICK EDIT MODAL ===== --}}
<div class="modal fade modal-pn" id="editSaleModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="editSaleForm">
            @csrf
            <input type="hidden" id="edit_sale_id">
            <input type="hidden" id="edit_total_amount">
            <div class="modal-content">
                <div class="modal-header px-4 py-3">
                    <h5 class="modal-title">
                        <i class="fas fa-edit mr-2"></i>Edit Sale / فروخت ترمیم
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body px-4 py-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Shop / دکان <span class="text-danger">*</span>
                            </label>
                            <select name="shop_id" id="edit_shop_id" class="form-control fc-pn" required>
                                @foreach($shops as $shop)
                                    <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Sale Date / فروخت کی تاریخ <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="sale_date" id="edit_sale_date"
                                   class="form-control fc-pn" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Total Amount / کل رقم
                            </label>
                            <input type="text" id="edit_total_display" class="form-control fc-ro-pn" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Received Amount / وصول شدہ رقم
                            </label>
                            <input type="text" id="edit_received_display" class="form-control fc-ro-pn" readonly>
                            <small class="text-muted">Use "Record Payment" to change this.</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Pending Amount / باقی رقم
                            </label>
                            <input type="text" id="edit_pending_display" class="form-control fc-ro-pn" readonly>
                        </div>
                        <div class="col-12 mb-1">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Remarks / ملاحظات
                            </label>
                            <textarea name="remarks" id="edit_remarks" class="form-control fc-pn" rows="2"
                                      placeholder="Optional notes..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3">
                    <button type="button" class="btn btn-light px-4 btn-modal-cancel"
                            data-dismiss="modal" data-bs-dismiss="modal">
                        Cancel / منسوخ
                    </button>
                    <button class="btn btn-primary px-4 btn-modal-save" type="submit" id="editSubmitBtn">
                        <i class="fas fa-save mr-1"></i> Update Sale / فروخت اپ ڈیٹ
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ===== RECORD PAYMENT MODAL ===== --}}
<div class="modal fade modal-pn" id="recordPaymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="recordPaymentForm">
            @csrf
            <input type="hidden" name="sale_id" id="rp_sale_id">
            <div class="modal-content border-0">
                <div class="modal-header border-0 text-white px-4 py-3">
                    <h5 class="modal-title"><i class="fas fa-hand-holding-dollar mr-2"></i>Record Payment / ادائیگی درج کریں</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <p class="mb-3">
                        <strong id="rp_shop_name"></strong>
                        <span class="text-muted"> — Pending: </span>
                        <span class="font-weight-bold text-c-red" id="rp_pending_display"></span>
                    </p>
                    <div class="mb-3">
                        <label class="filter-lbl">Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control fc-pn" placeholder="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="filter-lbl">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" id="rp_payment_date" class="form-control fc-pn" required>
                    </div>
                    <div class="mb-3">
                        <label class="filter-lbl">Account <span class="text-danger">*</span></label>
                        <select name="account_id" class="form-control fc-pn" required>
                            @foreach(\App\Models\Account::where('is_active', true)->orderBy('name')->get() as $account)
                                <option value="{{ $account->id }}" {{ $account->type === 'cash' ? 'selected' : '' }}>{{ $account->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="filter-lbl">Note / نوٹ</label>
                        <textarea name="note" class="form-control fc-pn" rows="2" placeholder="Optional"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 py-3">
                    <button type="button" class="btn btn-light btn-modal-cancel px-4" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary btn-modal-save px-4" type="submit" id="rpSubmitBtn">
                        <i class="fas fa-save mr-1"></i> Save
                    </button>
                </div>
            </div>
        </form>
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
        ordering: false,
        info: true,
        autoWidth: false,
        responsive: true,
        columnDefs: [{ orderable: false, targets: [2, 6] }, { responsivePriority: 1, targets: -1 }, { responsivePriority: 2, targets: 1 }],
        language: {
            search: '',
            searchPlaceholder: 'Search sales...',
            info: 'Showing _START_–_END_ of _TOTAL_',
            paginate: { previous: '‹', next: '›' }
        }
    });

    $(document).on('click', '.viewBtn', function () {
        const id = $(this).data('id');
        $('#saleDetailBody').html('<div class="text-center py-5"><span class="spinner-border text-c-blue2"></span></div>');
        $('#saleDetailModal').modal('show');
        $.get(APP_URL + '/admin/spice-sales/' + id, function (html) {
            $('#saleDetailBody').html(html);
        }).fail(function () {
            $('#saleDetailBody').html('<p class="text-danger text-center py-4">Could not load sale details.</p>');
        });
    });

    $(document).on('click', '.quickEditBtn', function () {
        const btn = $(this);
        $('#edit_sale_id').val(btn.data('id'));
        $('#edit_total_amount').val(btn.data('total'));
        $('#edit_shop_id').val(btn.data('shop-id'));
        $('#edit_sale_date').val(btn.data('sale-date'));
        $('#edit_total_display').val(Number(btn.data('total')).toLocaleString());
        $('#edit_received_display').val(Number(btn.data('received')).toLocaleString());
        $('#edit_pending_display').val(Number(btn.data('pending')).toLocaleString());
        $('#edit_remarks').val(btn.data('remarks') ?? '');
        $('#editSaleModal').modal('show');
    });

    $(document).on('click', '.recordPaymentBtn', function () {
        const btn = $(this);
        $('#rp_sale_id').val(btn.data('id'));
        $('#rp_shop_name').text(btn.data('shop') || '');
        $('#rp_pending_display').text(Number(btn.data('pending')).toLocaleString());
        $('#recordPaymentForm')[0].reset();
        $('#rp_payment_date').val(new Date().toISOString().split('T')[0]);
        $('#recordPaymentModal').modal('show');
    });

    $('#recordPaymentForm').on('submit', function (e) {
        e.preventDefault();
        const id  = $('#rp_sale_id').val();
        const btn = $('#rpSubmitBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        $.post(APP_URL + '/admin/spice-sales/' + id + '/payments', $(this).serialize())
            .done(function () {
                toastr.success('Payment recorded!');
                $('#recordPaymentModal').modal('hide');
                setTimeout(() => location.reload(), 800);
            })
            .fail(function (xhr) {
                if (xhr.status === 422) {
                    alert((xhr.responseJSON.message) || Object.values(xhr.responseJSON.errors || {}).map(e => e[0]).join('\n'));
                } else {
                    toastr.error('Could not record payment.');
                }
            })
            .always(() => btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save'));
    });

    $(document).on('click', '.deletePaymentBtn', function () {
        const saleId = $(this).data('sale-id');
        const payId  = $(this).data('payment-id');
        Swal.fire({
            title: 'Delete this payment?',
            text: 'This cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then(result => {
            if (!result.isConfirmed) return;
            $.ajax({
                url: APP_URL + '/admin/spice-sales/' + saleId + '/payments/' + payId,
                type: 'POST',
                data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                success: function () {
                    toastr.success('Payment deleted.');
                    setTimeout(() => location.reload(), 800);
                },
                error: function () { toastr.error('Could not delete payment.'); }
            });
        });
    });

    $('#editSaleForm').on('submit', function (e) {
        e.preventDefault();
        const id  = $('#edit_sale_id').val();
        const btn = $('#editSubmitBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        $.ajax({
            url: APP_URL + '/admin/spice-sales/' + id + '/quick-update',
            type: 'POST',
            data: $(this).serialize(),
            success: function () {
                toastr.success('Sale updated successfully!');
                $('#editSaleModal').modal('hide');
                setTimeout(() => location.reload(), 800);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON?.errors ?? {};
                    alert(Object.values(errors).map(e => e[0]).join('\n'));
                } else {
                    toastr.error('Could not update sale.');
                }
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Update Sale');
            }
        });
    });

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
            $.post(APP_URL + '/admin/spice-sales/' + id, {
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
