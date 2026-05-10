@extends('admin.layout.app')
@section('title', 'Shops')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Shops</h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Shops</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end">
                <button class="btn btn-primary px-4" style="border-radius:8px;" id="addBtn">
                    <i class="fas fa-plus mr-1"></i> Add Shop
                </button>
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
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Shops</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ $totalShops }}</div>
                                <small class="text-muted">{{ $activeShops }} active</small>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:46px;height:46px;background:rgba(78,115,223,.12);">
                                <i class="fas fa-store" style="color:#4e73df;font-size:18px;"></i>
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
                                <small class="text-muted">PKR all sales</small>
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
                <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #e74a3b !important;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Pending</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($totalPending, 0) }}</div>
                                <small class="text-muted">PKR udhaar</small>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:46px;height:46px;background:rgba(231,74,59,.12);">
                                <i class="fas fa-clock" style="color:#e74a3b;font-size:18px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #f6c23e !important;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Inactive Shops</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ $totalShops - $activeShops }}</div>
                                <small class="text-muted">not buying</small>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:46px;height:46px;background:rgba(246,194,62,.12);">
                                <i class="fas fa-store-slash" style="color:#f6c23e;font-size:18px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== FILTER TABS ===== --}}
        <div class="mb-3">
            <button class="btn btn-sm btn-secondary filter-btn active mr-1" data-filter="all">All</button>
            <button class="btn btn-sm btn-success filter-btn mr-1" data-filter="active">Active</button>
            <button class="btn btn-sm btn-warning filter-btn" data-filter="inactive">Inactive</button>
        </div>

        {{-- ===== TABLE ===== --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 font-weight-bold" style="color:#4e73df;">
                    <i class="fas fa-store mr-2"></i>Shop Directory
                </h6>
                <span class="badge" style="background:#e8f0fe;color:#4e73df;font-size:12px;padding:5px 10px;border-radius:20px;">
                    {{ $totalShops }} shops
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0" id="shopTable" style="font-size:13.5px;">
                        <thead>
                            <tr style="background:#f8f9fc;border-bottom:2px solid #e3e6f0;">
                                <th class="pl-3 py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">#</th>
                                <th class="py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Shop</th>
                                <th class="py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Contact</th>
                                <th class="py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">City / Area</th>
                                <th class="py-3 text-center text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Sales</th>
                                <th class="py-3 text-right text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Total (PKR)</th>
                                <th class="py-3 text-right text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Pending (PKR)</th>
                                <th class="py-3 text-center text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Status</th>
                                <th class="py-3 text-center text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shops as $i => $shop)
                                <tr class="shop-row" data-status="{{ $shop->status }}"
                                    id="row_{{ $shop->id }}" style="border-bottom:1px solid #f0f0f0;">
                                    <td class="pl-3 py-3 align-middle text-muted">{{ $i + 1 }}</td>
                                    <td class="py-3 align-middle">
                                        <span class="font-weight-bold d-block" style="color:#2d3748;">{{ $shop->name }}</span>
                                        @if($shop->owner_name)
                                            <small class="text-muted"><i class="fas fa-user mr-1"></i>{{ $shop->owner_name }}</small>
                                        @endif
                                    </td>
                                    <td class="py-3 align-middle">
                                        <span class="d-block" style="color:#374151;">
                                            <i class="fas fa-phone mr-1 text-muted" style="font-size:11px;"></i>{{ $shop->phone_number ?? '—' }}
                                        </span>
                                        @if($shop->email)
                                            <small class="text-muted">
                                                <i class="fas fa-envelope mr-1" style="font-size:10px;"></i>{{ $shop->email }}
                                            </small>
                                        @endif
                                    </td>
                                    <td class="py-3 align-middle">
                                        @if($shop->city)
                                            <span class="d-block" style="color:#374151;">
                                                <i class="fas fa-map-marker-alt mr-1 text-muted" style="font-size:11px;"></i>{{ $shop->city }}
                                            </span>
                                        @endif
                                        @if($shop->address)
                                            <small class="text-muted">{{ Str::limit($shop->address, 30) }}</small>
                                        @endif
                                    </td>
                                    <td class="py-3 align-middle text-center">
                                        <a href="{{ route('admin.sales.by_shop', ['shop_id' => $shop->id]) }}"
                                           class="badge"
                                           style="background:#e8f0fe;color:#4e73df;font-size:12px;padding:5px 10px;border-radius:20px;text-decoration:none;">
                                            {{ $shop->sales_count }} sales
                                        </a>
                                    </td>
                                    <td class="py-3 align-middle text-right">
                                        <span class="font-weight-bold" style="color:#2d3748;">
                                            {{ number_format($shop->sales_sum_total_amount ?? 0, 0) }}
                                        </span>
                                    </td>
                                    <td class="py-3 align-middle text-right">
                                        @if(($shop->sales_sum_pending_amount ?? 0) > 0)
                                            <span class="font-weight-bold" style="color:#e74a3b;">
                                                {{ number_format($shop->sales_sum_pending_amount, 0) }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3 align-middle text-center">
                                        @if($shop->status === 'active')
                                            <span class="badge badge-success" style="font-size:11px;">Active</span>
                                        @else
                                            <span class="badge badge-warning" style="font-size:11px;">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="py-3 align-middle text-center" style="white-space:nowrap;">
                                        <a href="{{ route('admin.sales.by_shop', ['shop_id' => $shop->id]) }}"
                                           class="btn btn-sm mr-1"
                                           style="background:#e8f0fe;color:#4e73df;border:1px solid #c3d3f7;border-radius:6px;"
                                           title="View Sales">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-sm editBtn mr-1"
                                                data-id="{{ $shop->id }}"
                                                style="background:#fff3cd;color:#856404;border:1px solid #ffc107;border-radius:6px;"
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm deleteBtn"
                                                data-id="{{ $shop->id }}"
                                                style="background:#fce8e6;color:#c62828;border:1px solid #ef9a9a;border-radius:6px;"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="fas fa-store fa-3x mb-3 d-block" style="color:#d1d5db;"></i>
                                        <p class="text-muted mb-0">No shops added yet.</p>
                                        <button class="btn btn-sm btn-primary mt-3" id="addBtnEmpty">
                                            <i class="fas fa-plus mr-1"></i> Add First Shop
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($shops->count() > 0)
                        <tfoot>
                            <tr style="background:#f8f9fc;border-top:2px solid #e3e6f0;">
                                <td class="pl-3 py-3 font-weight-bold" colspan="5" style="color:#2d3748;">Totals</td>
                                <td class="py-3 text-right font-weight-bold" style="color:#2d3748;font-size:15px;">
                                    {{ number_format($totalRevenue, 0) }}
                                </td>
                                <td class="py-3 text-right font-weight-bold" style="color:#e74a3b;font-size:15px;">
                                    {{ number_format($totalPending, 0) }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- ===== MODAL ===== --}}
<div class="modal fade" id="shopModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="shopForm">
            @csrf
            <input type="hidden" id="shop_id" name="_shop_id">
            <div class="modal-content border-0" style="border-radius:12px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.15);">

                <div class="modal-header border-0 text-white px-4 py-3"
                     style="background:linear-gradient(135deg,#4e73df,#224abe);">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-store mr-2"></i>Add Shop
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" style="opacity:.8;">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body px-4 py-4">
                    <div class="row">
                        {{-- Shop Name + Owner --}}
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Shop Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="sName" class="form-control"
                                   style="border-radius:8px;border-color:#d1d5db;" placeholder="Shop / business name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Owner Name
                            </label>
                            <input type="text" name="owner_name" id="sOwner" class="form-control"
                                   style="border-radius:8px;border-color:#d1d5db;" placeholder="Owner / contact person">
                        </div>

                        {{-- Phone + Email --}}
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Phone <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="phone_number" id="sPhone" class="form-control"
                                   style="border-radius:8px;border-color:#d1d5db;" placeholder="03xx-xxxxxxx" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Email
                            </label>
                            <input type="email" name="email" id="sEmail" class="form-control"
                                   style="border-radius:8px;border-color:#d1d5db;" placeholder="shop@example.com">
                        </div>

                        {{-- City + Status --}}
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                City / Area
                            </label>
                            <input type="text" name="city" id="sCity" class="form-control"
                                   style="border-radius:8px;border-color:#d1d5db;" placeholder="e.g. Lahore, Khewra">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" id="sStatus" class="form-control"
                                    style="border-radius:8px;border-color:#d1d5db;" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        {{-- Address --}}
                        <div class="col-12 mb-1">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Address <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="address" id="sAddress" class="form-control"
                                   style="border-radius:8px;border-color:#d1d5db;" placeholder="Full street address" required>
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
                        <i class="fas fa-save mr-1"></i> Save Shop
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

    // DataTable
    $('#shopTable').DataTable({
        paging: true,
        pageLength: 15,
        lengthChange: false,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        responsive: true,
        columnDefs: [{ orderable: false, targets: [4, 8] }],
        language: {
            search: '',
            searchPlaceholder: 'Search shops...',
            info: 'Showing _START_–_END_ of _TOTAL_',
            paginate: { previous: '‹', next: '›' }
        }
    });

    // Status filter
    $('.filter-btn').on('click', function () {
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        const f = $(this).data('filter');
        if (f === 'all') {
            $('.shop-row').show();
        } else {
            $('.shop-row').hide();
            $('.shop-row[data-status="' + f + '"]').show();
        }
    });

    // Open add modal
    $('#addBtn, #addBtnEmpty').on('click', function () {
        $('#shopForm')[0].reset();
        $('#shop_id').val('');
        $('#sStatus').val('active');
        $('#modalTitle').html('<i class="fas fa-store mr-2"></i>Add Shop');
        $('#submitBtn').html('<i class="fas fa-save mr-1"></i> Save Shop');
        $('#shopModal').modal('show');
    });

    // Submit (add + edit)
    $('#shopForm').on('submit', function (e) {
        e.preventDefault();
        const id  = $('#shop_id').val();
        const url = id ? (APP_URL + '/shops/' + id) : "{{ route('admin.shops.store') }}";
        const btn = $('#submitBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        $.ajax({
            url, type: 'POST',
            data: $(this).serialize() + (id ? '&_method=PUT' : ''),
            success: function (res) {
                const s = res.shop;
                if (id) {
                    // Update name/owner cell
                    const nameCell = `<span class="font-weight-bold d-block" style="color:#2d3748;">${s.name}</span>`
                        + (s.owner_name ? `<small class="text-muted"><i class="fas fa-user mr-1"></i>${s.owner_name}</small>` : '');
                    $('#row_' + id + ' td:nth-child(2)').html(nameCell);

                    // Update contact cell
                    const contactCell = `<span class="d-block" style="color:#374151;"><i class="fas fa-phone mr-1 text-muted" style="font-size:11px;"></i>${s.phone_number || '—'}</span>`
                        + (s.email ? `<small class="text-muted"><i class="fas fa-envelope mr-1" style="font-size:10px;"></i>${s.email}</small>` : '');
                    $('#row_' + id + ' td:nth-child(3)').html(contactCell);

                    // Update city cell
                    const cityCell = (s.city ? `<span class="d-block" style="color:#374151;"><i class="fas fa-map-marker-alt mr-1 text-muted" style="font-size:11px;"></i>${s.city}</span>` : '')
                        + (s.address ? `<small class="text-muted">${s.address.substring(0, 30)}</small>` : '');
                    $('#row_' + id + ' td:nth-child(4)').html(cityCell);

                    // Status badge
                    const statusBadge = s.status === 'active'
                        ? '<span class="badge badge-success" style="font-size:11px;">Active</span>'
                        : '<span class="badge badge-warning" style="font-size:11px;">Inactive</span>';
                    $('#row_' + id + ' td:nth-child(8)').html(statusBadge);
                    $('#row_' + id).attr('data-status', s.status);

                    toastr.success('Shop updated!');
                } else {
                    toastr.success('Shop added!');
                    setTimeout(() => location.reload(), 800);
                }
                $('#shopModal').modal('hide');
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    alert(Object.values(xhr.responseJSON.errors).map(e => e[0]).join("\n"));
                } else {
                    toastr.error('Something went wrong.');
                }
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Shop');
            }
        });
    });

    // Edit
    $(document).on('click', '.editBtn', function () {
        const id = $(this).data('id');
        $.get(APP_URL + '/shops/' + id + '/edit', function (s) {
            $('#shop_id').val(s.id);
            $('#sName').val(s.name);
            $('#sOwner').val(s.owner_name);
            $('#sPhone').val(s.phone_number);
            $('#sEmail').val(s.email);
            $('#sCity').val(s.city);
            $('#sAddress').val(s.address);
            $('#sStatus').val(s.status || 'active');
            $('#modalTitle').html('<i class="fas fa-edit mr-2"></i>Edit Shop');
            $('#submitBtn').html('<i class="fas fa-save mr-1"></i> Update Shop');
            $('#shopModal').modal('show');
        });
    });

    // Delete
    $(document).on('click', '.deleteBtn', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Delete this shop?',
            text: 'All sales linked to this shop will lose their shop reference.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then(result => {
            if (!result.isConfirmed) return;
            $.ajax({
                url: APP_URL + '/shops/' + id,
                type: 'POST',
                data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                success: function () {
                    $('#row_' + id).fadeOut(300, function () { $(this).remove(); });
                    toastr.success('Shop deleted.');
                },
                error: function (xhr) { toastr.error('Error: ' + xhr.status); }
            });
        });
    });

});
</script>
@endsection
