@extends('admin.layout.app')
@section('title', 'Vendors / Suppliers')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Vendors / Suppliers</h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Vendors</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end">
                <button class="btn btn-primary px-4" style="border-radius:8px;" id="addBtn">
                    <i class="fas fa-plus mr-1"></i> Add Vendor
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
                <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #4e73df !important;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Vendors</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ $totalVendors }}</div>
                                <small class="text-muted">registered suppliers</small>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:46px;height:46px;background:rgba(78,115,223,.12);">
                                <i class="fas fa-truck" style="color:#4e73df;font-size:18px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 mb-2">
                <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #1cc88a !important;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Purchased</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($totalSpent, 0) }}</div>
                                <small class="text-muted">PKR all time</small>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:46px;height:46px;background:rgba(28,200,138,.12);">
                                <i class="fas fa-money-bill-wave" style="color:#1cc88a;font-size:18px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 mb-2">
                <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #f6c23e !important;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Top Supplier</div>
                                <div class="h5 mb-0 font-weight-bold text-dark" style="font-size:15px !important;">
                                    {{ $topVendor?->name ?? '—' }}
                                </div>
                                <small class="text-muted">{{ $topVendor?->purchases_count ?? 0 }} purchases</small>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:46px;height:46px;background:rgba(246,194,62,.12);">
                                <i class="fas fa-star" style="color:#f6c23e;font-size:18px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 font-weight-bold" style="color:#4e73df;">
                    <i class="fas fa-list mr-2"></i>Suppliers List
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-striped mb-0" id="vendorsTable">
                    <thead class="thead-dark">
                        <tr>
                            <th>Name</th>
                            <th>Shop / Business</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th class="text-center">Purchases</th>
                            <th class="text-center">Actions</th>
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
                            <td class="text-center">
                                <button class="btn btn-sm editBtn mr-1"
                                        data-id="{{ $vendor->id }}"
                                        style="background:#fff3cd;color:#856404;border:1px solid #ffc107;border-radius:6px;"
                                        title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm deleteBtn"
                                        data-id="{{ $vendor->id }}"
                                        style="background:#fce8e6;color:#c62828;border:1px solid #ef9a9a;border-radius:6px;"
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyRow">
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-truck fa-3x mb-3 d-block" style="color:#d1d5db;"></i>
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
<div class="modal fade" id="vendorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="vendorForm">
            @csrf
            <input type="hidden" id="vendor_id" name="_vendor_id">
            <div class="modal-content border-0" style="border-radius:12px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.15);">

                <div class="modal-header border-0 text-white px-4 py-3"
                     style="background:linear-gradient(135deg,#4e73df,#224abe);">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-truck mr-2"></i>Add Vendor
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" style="opacity:.8;">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body px-4 py-4">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="vName" name="name" class="form-control"
                                   style="border-radius:8px;border-color:#d1d5db;" placeholder="Supplier full name" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Shop / Business
                            </label>
                            <input type="text" id="vShop" name="shop" class="form-control"
                                   style="border-radius:8px;border-color:#d1d5db;" placeholder="Business or shop name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Phone
                            </label>
                            <input type="text" id="vPhone" name="phone" class="form-control"
                                   style="border-radius:8px;border-color:#d1d5db;" placeholder="03xx-xxxxxxx">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Address
                            </label>
                            <input type="text" id="vAddress" name="address" class="form-control"
                                   style="border-radius:8px;border-color:#d1d5db;" placeholder="City / area">
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 px-4 py-3" style="background:#f8f9fc;">
                    <button type="button" class="btn btn-light px-4"
                            data-dismiss="modal" data-bs-dismiss="modal"
                            style="border-radius:8px;border:1px solid #d1d5db;">
                        Cancel
                    </button>
                    <button class="btn btn-primary px-4" type="submit" id="submitBtn"
                            style="border-radius:8px;background:linear-gradient(135deg,#4e73df,#224abe);border:none;">
                        <i class="fas fa-save mr-1"></i> Save Vendor
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
        <td class="text-center">
            <button class="btn btn-sm editBtn mr-1" data-id="${v.id}"
                    style="background:#fff3cd;color:#856404;border:1px solid #ffc107;border-radius:6px;" title="Edit">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-sm deleteBtn" data-id="${v.id}"
                    style="background:#fce8e6;color:#c62828;border:1px solid #ef9a9a;border-radius:6px;" title="Delete">
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
        columnDefs: [{ orderable: false, targets: [4, 5] }, { responsivePriority: 1, targets: -1 }],
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

});
</script>
@endsection
