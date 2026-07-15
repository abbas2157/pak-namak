@extends('admin.layout.app')
@section('title', 'Shops')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Shops <small class="text-muted pn-stat-sub">دکانیں</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Shops</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end">
                <button class="btn btn-primary px-4 btn-pn" id="addBtn">
                    <i class="fas fa-plus mr-1"></i> Add Shop / دکان شامل کریں
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
                <div class="card border-0 shadow-sm h-100 pn-bl-blue">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Shops / کل دکانیں</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ $totalShops }}</div>
                                <small class="text-muted">{{ $activeShops }} active</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-blue">
                                <i class="fas fa-store"></i>
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
                                <small class="text-muted">PKR all sales</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-teal">
                                <i class="fas fa-chart-line"></i>
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
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Pending / کل باقی</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($totalPending, 0) }}</div>
                                <small class="text-muted">PKR udhaar</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-red">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100 pn-bl-yellow">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Inactive Shops / غیر فعال دکانیں</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ $totalShops - $activeShops }}</div>
                                <small class="text-muted">not buying</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-yellow">
                                <i class="fas fa-store-slash"></i>
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
        <div class="card border-0 shadow-sm card-pn">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 font-weight-bold text-c-blue2">
                    <i class="fas fa-store mr-2"></i>Shop Directory / دکانوں کی فہرست
                </h6>
                <span class="badge pn-bdg pn-bdg-blue">{{ $totalShops }} shops</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 pn-table pn-table-font" id="shopTable">
                        <thead>
                            <tr>
                                <th class="pl-3">Shop / دکان</th>
                                <th>Contact / رابطہ</th>
                                <th>City / Area / شہر</th>
                                <th class="text-center">Sales / فروخت</th>
                                <th class="text-right">Total (PKR) / کل</th>
                                <th class="text-right">Pending (PKR) / باقی</th>
                                <th class="text-center">Status / حیثیت</th>
                                <th class="text-center">Actions / اقدامات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shops as $i => $shop)
                                <tr class="shop-row" data-status="{{ $shop->status }}" id="row_{{ $shop->id }}">
                                    <td class="pl-3 align-middle">
                                        <span class="shop-cell-name">{{ $shop->name }}</span>
                                        @if($shop->owner_name)
                                            <small class="text-muted"><i class="fas fa-user mr-1"></i>{{ $shop->owner_name }}</small>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <span class="shop-cell-contact">
                                            <i class="fas fa-phone mr-1 text-muted icon-11"></i>{{ $shop->phone_number ?? '—' }}
                                        </span>
                                        @if($shop->email)
                                            <small class="text-muted">
                                                <i class="fas fa-envelope mr-1 icon-10"></i>{{ $shop->email }}
                                            </small>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if($shop->cityRecord)
                                            <span class="shop-cell-contact">
                                                <i class="fas fa-city mr-1 text-muted icon-11"></i>{{ $shop->cityRecord->name }}
                                            </span>
                                        @elseif($shop->city)
                                            <span class="shop-cell-contact">
                                                <i class="fas fa-city mr-1 text-muted icon-11"></i>{{ $shop->city }}
                                            </span>
                                        @endif
                                        @if($shop->area)
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt mr-1 icon-10"></i>{{ $shop->area->name }}
                                            </small>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        <a href="{{ route('admin.sales.by_shop', ['shop_id' => $shop->id]) }}"
                                           class="badge pn-bdg pn-bdg-blue">
                                            {{ $shop->sales_count }} sales
                                        </a>
                                    </td>
                                    <td class="align-middle text-right font-weight-bold pn-text-heading">
                                        {{ number_format($shop->sales_sum_total_amount ?? 0, 0) }}
                                    </td>
                                    <td class="align-middle text-right">
                                        @if(($shop->sales_sum_pending_amount ?? 0) > 0)
                                            <span class="font-weight-bold text-c-red">
                                                {{ number_format($shop->sales_sum_pending_amount, 0) }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        @if($shop->status === 'active')
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-warning">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center text-nowrap">
                                        <a href="{{ route('admin.sales.by_shop', ['shop_id' => $shop->id]) }}"
                                           class="btn btn-sm btn-pn btn-act-view mr-1"
                                           title="View Sales">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-pn btn-act-edit editBtn mr-1"
                                                data-id="{{ $shop->id }}"
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @if(($shop->sales_sum_pending_amount ?? 0) > 0)
                                        <button class="btn btn-sm btn-pn btn-act-confirm shopRecordPaymentBtn mr-1"
                                                data-id="{{ $shop->id }}"
                                                data-name="{{ $shop->name }}"
                                                data-pending="{{ $shop->sales_sum_pending_amount }}"
                                                title="Record Payment">
                                            <i class="fas fa-hand-holding-dollar"></i>
                                        </button>
                                        @endif
                                        <button class="btn btn-sm btn-pn btn-act-delete deleteBtn"
                                                data-id="{{ $shop->id }}"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="fas fa-store fa-3x mb-3 d-block icon-fade"></i>
                                        <p class="text-muted mb-0">No shops added yet.</p>
                                        <button class="btn btn-sm btn-primary btn-pn mt-3" id="addBtnEmpty">
                                            <i class="fas fa-plus mr-1"></i> Add First Shop
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($shops->count() > 0)
                        <tfoot>
                            <tr class="pn-total-row">
                                <td class="pl-3 py-3 font-weight-bold pn-text-heading" colspan="4">Totals / کل</td>
                                <td class="py-3 text-right font-weight-bold pn-text-heading pn-stat-num-sm">
                                    {{ number_format($totalRevenue, 0) }}
                                </td>
                                <td class="py-3 text-right font-weight-bold text-c-red pn-stat-num-sm">
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
<div class="modal fade modal-pn" id="shopModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="shopForm">
            @csrf
            <input type="hidden" id="shop_id" name="_shop_id">
            <div class="modal-content">

                <div class="modal-header px-4 py-3">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-store mr-2"></i>Add Shop / دکان شامل کریں
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body px-4 py-4">
                    <div class="row">
                        {{-- Shop Name + Owner --}}
                        <div class="col-md-6 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Shop Name / دکان کا نام <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="sName" class="form-control fc-pn"
                                   placeholder="Shop / business name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Owner Name / مالک کا نام
                            </label>
                            <input type="text" name="owner_name" id="sOwner" class="form-control fc-pn"
                                   placeholder="Owner / contact person">
                        </div>

                        {{-- Phone + Email --}}
                        <div class="col-md-6 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Phone / فون <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="phone_number" id="sPhone" class="form-control fc-pn"
                                   placeholder="03xx-xxxxxxx" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Email / ای میل
                            </label>
                            <input type="email" name="email" id="sEmail" class="form-control fc-pn"
                                   placeholder="shop@example.com">
                        </div>

                        {{-- City + Area --}}
                        <div class="col-md-6 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                City / شہر
                            </label>
                            <div class="input-group">
                                <select name="city_id" id="sCity" class="form-control fc-pn">
                                    <option value="">— Select City —</option>
                                    @foreach($cities as $c)
                                        <option value="{{ $c->id }}" data-areas="{{ $c->areas->toJson() }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                    <a href="{{ route('admin.cities.index') }}" target="_blank"
                                       class="btn btn-light btn-pn"
                                       title="Manage Cities">
                                        <i class="fas fa-city"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Area / علاقہ
                            </label>
                            <select name="area_id" id="sArea" class="form-control fc-pn">
                                <option value="">— Select Area —</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Status / حیثیت <span class="text-danger">*</span>
                            </label>
                            <select name="status" id="sStatus" class="form-control fc-pn" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        {{-- Address --}}
                        <div class="col-12 mb-1">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Address / پتہ <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="address" id="sAddress" class="form-control fc-pn"
                                   placeholder="Full street address" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer px-4 py-3">
                    <button type="button" class="btn btn-light px-4 btn-modal-cancel"
                            data-dismiss="modal" data-bs-dismiss="modal">
                        Cancel / منسوخ
                    </button>
                    <button class="btn btn-primary px-4 btn-modal-save" type="submit" id="submitBtn">
                        <i class="fas fa-save mr-1"></i> Save Shop / محفوظ کریں
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

{{-- ===== RECORD PAYMENT MODAL (shop-level, FIFO across pending sales) ===== --}}
<div class="modal fade modal-pn" id="shopRecordPaymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="shopRecordPaymentForm">
            @csrf
            <div class="modal-content border-0">
                <div class="modal-header border-0 text-white px-4 py-3">
                    <h5 class="modal-title"><i class="fas fa-hand-holding-dollar mr-2"></i>Record Payment / ادائیگی درج کریں</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <p class="mb-3">
                        <strong id="srp_shop_name"></strong>
                        <span class="text-muted"> — Total Pending: </span>
                        <span class="font-weight-bold text-c-red" id="srp_pending_display"></span>
                    </p>
                    <p class="text-muted small mb-3">Applied to this shop's oldest pending sales first.</p>
                    <div class="mb-3">
                        <label class="filter-lbl">Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control fc-pn" placeholder="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="filter-lbl">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" id="srp_payment_date" class="form-control fc-pn" required>
                    </div>
                    <div class="mb-3">
                        <label class="filter-lbl">Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-control fc-pn" required>
                            <option value="Cash" selected>Cash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="EasyPaisa">EasyPaisa</option>
                            <option value="JazzCash">JazzCash</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="filter-lbl">Note / نوٹ</label>
                        <textarea name="note" class="form-control fc-pn" rows="2" placeholder="Optional"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 py-3">
                    <button type="button" class="btn btn-light btn-modal-cancel px-4" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary btn-modal-save px-4" type="submit" id="srpSubmitBtn">
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

    // DataTable
    $('#shopTable').DataTable({
        paging: true,
        pageLength: 15,
        lengthChange: false,
        searching: true,
        ordering: false,
        info: true,
        autoWidth: false,
        responsive: true,
        columnDefs: [{ orderable: false, targets: [3, 7] }, { responsivePriority: 1, targets: -1 }],
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

    // Populate area dropdown when city changes
    function populateAreas(areas, selectedAreaId) {
        const sel = $('#sArea');
        sel.html('<option value="">— Select Area —</option>');
        if (areas && areas.length) {
            areas.forEach(function (a) {
                const opt = $('<option>').val(a.id).text(a.name);
                if (selectedAreaId && a.id == selectedAreaId) opt.prop('selected', true);
                sel.append(opt);
            });
            sel.closest('.col-md-6').show();
        } else {
            sel.closest('.col-md-6').hide();
        }
    }

    $('#sCity').on('change', function () {
        const selected = $(this).find(':selected');
        const areas = selected.data('areas') || [];
        populateAreas(areas, null);
    });

    // Open add modal
    $('#addBtn, #addBtnEmpty').on('click', function () {
        $('#shopForm')[0].reset();
        $('#shop_id').val('');
        $('#sStatus').val('active');
        $('#sArea').html('<option value="">— Select Area —</option>');
        $('#sArea').closest('.col-md-6').hide();
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
                    const nameCell = `<span class="shop-cell-name">${s.name}</span>`
                        + (s.owner_name ? `<small class="text-muted"><i class="fas fa-user mr-1"></i>${s.owner_name}</small>` : '');
                    $('#row_' + id + ' td:nth-child(2)').html(nameCell);

                    // Update contact cell
                    const contactCell = `<span class="shop-cell-contact"><i class="fas fa-phone mr-1 text-muted icon-11"></i>${s.phone_number || '—'}</span>`
                        + (s.email ? `<small class="text-muted"><i class="fas fa-envelope mr-1 icon-10"></i>${s.email}</small>` : '');
                    $('#row_' + id + ' td:nth-child(3)').html(contactCell);

                    // Update city/area cell
                    const cityName = s.city_record ? s.city_record.name : (s.city || '');
                    const areaName = s.area ? s.area.name : null;
                    const cityCell = (cityName ? `<span class="shop-cell-contact"><i class="fas fa-city mr-1 text-muted icon-11"></i>${cityName}</span>` : '')
                        + (areaName ? `<small class="text-muted"><i class="fas fa-map-marker-alt mr-1 icon-10"></i>${areaName}</small>` : '');
                    $('#row_' + id + ' td:nth-child(4)').html(cityCell);

                    // Status badge
                    const statusBadge = s.status === 'active'
                        ? '<span class="badge badge-success">Active</span>'
                        : '<span class="badge badge-warning">Inactive</span>';
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
            $('#sCity').val(s.city_id || '');
            // populate areas for selected city then set area
            const cityOption = $('#sCity').find(':selected');
            const areas = cityOption.data('areas') || [];
            populateAreas(areas, s.area_id);
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

    // Deep link from other pages (e.g. dashboard "Add Shop" button) straight into the Add Shop modal
    if (new URLSearchParams(window.location.search).get('open') === 'create') {
        $('#addBtn').trigger('click');
    }

    // Record Payment (shop-level, FIFO across pending sales)
    $(document).on('click', '.shopRecordPaymentBtn', function () {
        const btn = $(this);
        $('#shopRecordPaymentForm')[0].reset();
        $('#shopRecordPaymentForm').data('shop-id', btn.data('id'));
        $('#srp_shop_name').text(btn.data('name') || '');
        $('#srp_pending_display').text(Number(btn.data('pending')).toLocaleString());
        $('#srp_payment_date').val(new Date().toISOString().split('T')[0]);
        $('#shopRecordPaymentModal').modal('show');
    });

    $('#shopRecordPaymentForm').on('submit', function (e) {
        e.preventDefault();
        const shopId = $(this).data('shop-id');
        const btn = $('#srpSubmitBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        $.post(APP_URL + '/shops/' + shopId + '/payments', $(this).serialize())
            .done(function (res) {
                toastr.success('Payment recorded across ' + (res.sales_paid || 0) + ' sale(s)!');
                $('#shopRecordPaymentModal').modal('hide');
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
