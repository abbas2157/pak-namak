@extends('admin.layout.app')
@section('title', 'Vendors / Suppliers')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Vendors / Suppliers <small class="text-muted ch-sub">فروش کار / سپلائر</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Vendors</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end">
                <button class="btn btn-primary btn-pn px-4" id="addBtn">
                    <i class="fas fa-plus mr-1"></i> Add Vendor / فروش کار شامل کریں
                </button>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        {{-- Stats --}}
        <div class="row mb-3">
            <div class="col-6 col-md-4 mb-2">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-blue">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="pn-stat-lbl mb-1">Total Vendors / کل فروش کار</div>
                                <div class="pn-stat-num-md pn-text-heading mb-0">{{ $totalVendors }}</div>
                                <div class="pn-stat-sub">registered suppliers</div>
                            </div>
                            <div class="pn-icon pn-icon-md pni-blue">
                                <i class="fas fa-truck"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 mb-2">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-teal">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="pn-stat-lbl mb-1">Total Purchased / کل خریداری</div>
                                <div class="pn-stat-num-md pn-text-heading mb-0">{{ number_format($totalSpent, 0) }}</div>
                                <div class="pn-stat-sub">PKR all time</div>
                            </div>
                            <div class="pn-icon pn-icon-md pni-teal">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 mb-2">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-yellow">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="pn-stat-lbl mb-1">Top Supplier / سب سے بڑا سپلائر</div>
                                <div class="pn-stat-num-sm pn-text-heading mb-0">
                                    {{ $topVendor?->name ?? '—' }}
                                </div>
                                <div class="pn-stat-sub">{{ $topVendor?->purchases_count ?? 0 }} purchases</div>
                            </div>
                            <div class="pn-icon pn-icon-md pni-yellow">
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card card-pn border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 font-weight-bold text-c-blue2">
                    <i class="fas fa-list mr-2"></i>Suppliers List / سپلائروں کی فہرست
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-striped pn-table pn-table-font mb-0" id="vendorsTable">
                    <thead class="thead-dark">
                        <tr>
                            <th>Name / نام</th>
                            <th>Shop / Business / دکان</th>
                            <th>Phone / فون</th>
                            <th>Address / پتہ</th>
                            <th class="text-center">Purchases / خریداری</th>
                            <th class="text-right">Pending / باقی</th>
                            <th class="text-center">Actions / اقدامات</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($vendors as $i => $vendor)
                        <tr id="row_{{ $vendor->id }}">
                            <td><strong>{{ $vendor->name }}</strong></td>
                            <td>{{ $vendor->shop ?? '—' }}</td>
                            <td>{{ $vendor->phone ?? '—' }}</td>
                            <td>{{ $vendor->address ?? '—' }}</td>
                            <td class="text-center">
                                <span class="badge badge-info">{{ $vendor->purchases_count }}</span>
                            </td>
                            <td class="text-right">
                                @if(($vendor->purchases_sum_pending_amount ?? 0) > 0)
                                    <span class="font-weight-bold text-danger">{{ number_format($vendor->purchases_sum_pending_amount, 0) }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                @if(($vendor->purchases_sum_pending_amount ?? 0) > 0)
                                <button class="btn btn-sm btn-pn btn-act-confirm vendorRecordPaymentBtn mr-1"
                                        data-id="{{ $vendor->id }}"
                                        data-name="{{ $vendor->name }}"
                                        data-pending="{{ $vendor->purchases_sum_pending_amount }}"
                                        title="Record Payment">
                                    <i class="fas fa-hand-holding-dollar"></i>
                                </button>
                                @endif
                                <button class="btn btn-sm btn-pn btn-act-edit editBtn mr-1"
                                        data-id="{{ $vendor->id }}"
                                        title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-pn btn-act-delete deleteBtn"
                                        data-id="{{ $vendor->id }}"
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyRow">
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-truck fa-3x mb-3 d-block icon-fade"></i>
                                <p class="text-muted mb-0">No vendors added yet.</p>
                                <button class="btn btn-sm btn-primary mt-3" id="addBtnEmpty">
                                    <i class="fas fa-plus mr-1"></i> Add First Vendor
                                </button>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>

{{-- ===== MODAL ===== --}}
<div class="modal fade modal-pn" id="vendorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="vendorForm">
            @csrf
            <input type="hidden" id="vendor_id" name="_vendor_id">
            <div class="modal-content border-0">

                <div class="modal-header border-0 text-white px-4 py-3">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-truck mr-2"></i>Add Vendor / فروش کار شامل کریں
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body px-4 py-4">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="filter-lbl">
                                Name / نام <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="vName" name="name" class="form-control fc-pn"
                                   placeholder="Supplier full name" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="filter-lbl">
                                Shop / Business / دکان
                            </label>
                            <input type="text" id="vShop" name="shop" class="form-control fc-pn"
                                   placeholder="Business or shop name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="filter-lbl">
                                Phone / فون
                            </label>
                            <input type="text" id="vPhone" name="phone" class="form-control fc-pn"
                                   placeholder="03xx-xxxxxxx">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="filter-lbl">
                                Address / پتہ
                            </label>
                            <input type="text" id="vAddress" name="address" class="form-control fc-pn"
                                   placeholder="City / area">
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 px-4 py-3">
                    <button type="button" class="btn btn-light btn-modal-cancel px-4"
                            data-dismiss="modal" data-bs-dismiss="modal">
                        Cancel / منسوخ
                    </button>
                    <button class="btn btn-primary btn-modal-save px-4" type="submit" id="submitBtn">
                        <i class="fas fa-save mr-1"></i> Save Vendor / محفوظ کریں
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

{{-- ===== RECORD PAYMENT MODAL (vendor-level, FIFO across pending purchases) ===== --}}
<div class="modal fade modal-pn" id="vendorRecordPaymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="vendorRecordPaymentForm">
            @csrf
            <div class="modal-content border-0">
                <div class="modal-header border-0 text-white px-4 py-3">
                    <h5 class="modal-title"><i class="fas fa-hand-holding-dollar mr-2"></i>Record Payment / ادائیگی درج کریں</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <p class="mb-3">
                        <strong id="vrp_vendor_name"></strong>
                        <span class="text-muted"> — Total Pending: </span>
                        <span class="font-weight-bold text-c-red" id="vrp_pending_display"></span>
                    </p>
                    <p class="text-muted small mb-3">Applied to this vendor's oldest pending purchases first.</p>
                    <div class="mb-3">
                        <label class="filter-lbl">Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control fc-pn" placeholder="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="filter-lbl">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" id="vrp_payment_date" class="form-control fc-pn" required>
                    </div>
                    <div class="mb-3">
                        <label class="filter-lbl">Paid From <span class="text-danger">*</span></label>
                        <select name="account_id" class="form-control fc-pn" required>
                            @foreach($accounts as $account)
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
                    <button class="btn btn-primary btn-modal-save px-4" type="submit" id="vrpSubmitBtn">
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
let rowCount = {{ $vendors->count() }};

function buildRow(v, idx) {
    return `<tr id="row_${v.id}">
        <td><strong>${v.name}</strong></td>
        <td>${v.shop || '—'}</td>
        <td>${v.phone || '—'}</td>
        <td>${v.address || '—'}</td>
        <td class="text-center"><span class="badge badge-info">0</span></td>
        <td class="text-right"><span class="text-muted">—</span></td>
        <td class="text-center">
            <button class="btn btn-sm btn-pn btn-act-edit editBtn mr-1" data-id="${v.id}" title="Edit">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-sm btn-pn btn-act-delete deleteBtn" data-id="${v.id}" title="Delete">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>`;
}

$(function () {

    $('#vendorsTable').DataTable({
        paging: true,
        pageLength: 15,
        lengthChange: false,
        searching: true,
        ordering: false,
        info: true,
        autoWidth: false,
        responsive: true,
        columnDefs: [{ orderable: false, targets: [4, 6] }, { responsivePriority: 1, targets: -1 }],
        language: {
            search: '',
            searchPlaceholder: 'Search vendors...',
            info: 'Showing _START_–_END_ of _TOTAL_',
            paginate: { previous: '‹', next: '›' }
        }
    });

    // Open add modal
    $('#addBtn, #addBtnEmpty').on('click', function () {
        $('#vendorForm')[0].reset();
        $('#vendor_id').val('');
        $('#modalTitle').html('<i class="fas fa-truck mr-2"></i>Add Vendor');
        $('#submitBtn').html('<i class="fas fa-save mr-1"></i> Save Vendor');
        $('#vendorModal').modal('show');
    });

    // Submit (add + edit)
    $('#vendorForm').on('submit', function (e) {
        e.preventDefault();
        const id  = $('#vendor_id').val();
        const url = id ? (APP_URL + '/vendors/' + id) : "{{ route('admin.vendors.store') }}";
        const btn = $('#submitBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        $.ajax({
            url, type: 'POST',
            data: $(this).serialize() + (id ? '&_method=PUT' : ''),
            success: function (res) {
                if (id) {
                    const v = res.vendor;
                    $('#row_' + id + ' td:nth-child(2)').html(`<strong>${v.name}</strong>`);
                    $('#row_' + id + ' td:nth-child(3)').text(v.shop || '—');
                    $('#row_' + id + ' td:nth-child(4)').text(v.phone || '—');
                    $('#row_' + id + ' td:nth-child(5)').text(v.address || '—');
                    toastr.success('Vendor updated!');
                } else {
                    $('#emptyRow').remove();
                    rowCount++;
                    $('#vendorsTable tbody').append(buildRow(res.vendor, rowCount));
                    toastr.success('Vendor added!');
                }
                $('#vendorModal').modal('hide');
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    alert(Object.values(xhr.responseJSON.errors).map(e => e[0]).join("\n"));
                } else {
                    toastr.error('Something went wrong.');
                }
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Vendor');
            }
        });
    });

    // Edit
    $(document).on('click', '.editBtn', function () {
        const id = $(this).data('id');
        $.get(APP_URL + '/vendors/' + id + '/edit', function (v) {
            $('#vendor_id').val(v.id);
            $('#vName').val(v.name);
            $('#vShop').val(v.shop);
            $('#vPhone').val(v.phone);
            $('#vAddress').val(v.address);
            $('#modalTitle').html('<i class="fas fa-edit mr-2"></i>Edit Vendor');
            $('#submitBtn').html('<i class="fas fa-save mr-1"></i> Update Vendor');
            $('#vendorModal').modal('show');
        });
    });

    // Delete
    $(document).on('click', '.deleteBtn', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Delete this vendor?',
            text: 'All purchase records linked to this vendor will lose their vendor reference.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then(result => {
            if (!result.isConfirmed) return;
            $.ajax({
                url: APP_URL + '/vendors/' + id,
                type: 'POST',
                data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                success: function () {
                    $('#row_' + id).fadeOut(300, function () { $(this).remove(); });
                    toastr.success('Vendor deleted.');
                },
                error: function (xhr) { toastr.error('Error: ' + xhr.status); }
            });
        });
    });

    // Record Payment (vendor-level, FIFO across pending purchases)
    $(document).on('click', '.vendorRecordPaymentBtn', function () {
        const btn = $(this);
        $('#vendorRecordPaymentForm')[0].reset();
        $('#vendorRecordPaymentForm').data('vendor-id', btn.data('id'));
        $('#vrp_vendor_name').text(btn.data('name') || '');
        $('#vrp_pending_display').text(Number(btn.data('pending')).toLocaleString());
        $('#vrp_payment_date').val(new Date().toISOString().split('T')[0]);
        $('#vendorRecordPaymentModal').modal('show');
    });

    $('#vendorRecordPaymentForm').on('submit', function (e) {
        e.preventDefault();
        const vendorId = $(this).data('vendor-id');
        const btn = $('#vrpSubmitBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        $.post(APP_URL + '/vendors/' + vendorId + '/payments', $(this).serialize())
            .done(function (res) {
                toastr.success('Payment recorded across ' + (res.purchases_paid || 0) + ' purchase(s)!');
                $('#vendorRecordPaymentModal').modal('hide');
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

});
</script>
@endsection
