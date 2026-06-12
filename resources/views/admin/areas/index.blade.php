@extends('admin.layout.app')
@section('title', 'Areas')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Areas <small class="text-muted">علاقے</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.cities.index') }}">Cities</a></li>
                    <li class="breadcrumb-item active">Areas</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end">
                <a href="{{ route('admin.cities.index') }}"
                   class="btn btn-light px-3 mr-2 btn-modal-cancel">
                    <i class="fas fa-city mr-1"></i> Cities
                </a>
                <button class="btn btn-primary px-4 btn-modal-save" id="addBtn">
                    <i class="fas fa-plus mr-1"></i> Add Area / علاقہ شامل کریں
                </button>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        {{-- Stats --}}
        <div class="row mb-3">
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100 pn-bl-blue">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Areas / کل علاقے</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ $totalAreas }}</div>
                                <small class="text-muted">{{ $totalShops }} shops</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-blue">
                                <i class="fas fa-map-marker-alt"></i>
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
                                <small class="text-muted">PKR all areas</small>
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
                <div class="card border-0 shadow-sm h-100 pn-bl-cyan">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Shops / کل دکانیں</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ $totalShops }}</div>
                                <small class="text-muted">across all areas</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-cyan">
                                <i class="fas fa-store"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- City filter tabs --}}
        <div class="mb-3 d-flex flex-wrap">
            <a href="{{ route('admin.areas.index') }}"
               class="btn btn-sm rounded-pill mr-1 mb-1 {{ !$selectedCity ? 'btn-secondary' : 'btn-outline-secondary' }}">All Cities</a>
            @foreach($cities as $c)
                <a href="{{ route('admin.areas.index', ['city_id' => $c->id]) }}"
                   class="btn btn-sm rounded-pill mr-1 mb-1 {{ $selectedCity == $c->id ? 'btn-primary' : 'btn-outline-primary' }}">{{ $c->name }}</a>
            @endforeach
        </div>

        {{-- Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 font-weight-bold text-c-blue2">
                    <i class="fas fa-map-marker-alt mr-2"></i>Areas Directory / علاقوں کی فہرست
                </h6>
                <span class="badge bdg-blue bdg-md">
                    {{ $totalAreas }} areas
                </span>
            </div>
            <div class="card-body p-0">
                @if($areas->count() > 0)
                <div class="table-responsive">
                    <table class="table mb-0 pn-table pn-table-font" id="areaTable">
                        <thead>
                            <tr>
                                <th class="pl-3 py-3 text-uppercase">Area / علاقہ</th>
                                <th class="py-3 text-uppercase">City / شہر</th>
                                <th class="py-3 text-center text-uppercase">Shops</th>
                                <th class="py-3 text-center text-uppercase">Sales</th>
                                <th class="py-3 text-right text-uppercase">Revenue (PKR)</th>
                                <th class="py-3 text-right text-uppercase">Pending (PKR)</th>
                                <th class="py-3 text-center text-uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($areas as $area)
                            <tr id="row_{{ $area->id }}" class="pn-table-row">
                                <td class="pl-3 py-3 align-middle">
                                    <span class="font-weight-bold text-c-dark" id="area_name_{{ $area->id }}">{{ $area->name }}</span>
                                </td>
                                <td class="py-3 align-middle">
                                    <span class="badge bdg-blue bdg-sm" id="area_city_{{ $area->id }}">
                                        {{ $area->city->name ?? '—' }}
                                    </span>
                                </td>
                                <td class="py-3 align-middle text-center">
                                    <span class="badge bdg-neutral bdg-md">
                                        {{ $area->shops_count }}
                                    </span>
                                </td>
                                <td class="py-3 align-middle text-center">
                                    <span class="badge bdg-teal bdg-md">
                                        {{ $area->total_sales }}
                                    </span>
                                </td>
                                <td class="py-3 align-middle text-right font-weight-bold text-c-dark">
                                    {{ number_format($area->total_revenue, 0) }}
                                </td>
                                <td class="py-3 align-middle text-right">
                                    @if($area->total_pending > 0)
                                        <span class="font-weight-bold text-c-red">{{ number_format($area->total_pending, 0) }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="py-3 align-middle text-center text-nowrap">
                                    <button class="btn btn-sm btn-pn btn-act-edit edit-btn mr-1"
                                            data-id="{{ $area->id }}"
                                            title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-pn btn-act-delete delete-btn"
                                            data-id="{{ $area->id }}"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="pn-total-row">
                                <td class="pl-3 py-3 font-weight-bold text-c-dark" colspan="4">Totals / کل</td>
                                <td class="py-3 text-right font-weight-bold text-c-dark pn-stat-num-sm">{{ number_format($totalRevenue, 0) }}</td>
                                <td class="py-3 text-right font-weight-bold text-c-red pn-stat-num-sm">{{ number_format($totalPending, 0) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-map-marker-alt fa-3x mb-3 d-block text-muted"></i>
                    <p class="text-muted mb-3">No areas added yet.</p>
                    <button class="btn btn-primary btn-sm" id="addBtnEmpty">
                        <i class="fas fa-plus mr-1"></i> Add First Area
                    </button>
                </div>
                @endif
            </div>
        </div>

    </div>
</section>

{{-- Modal --}}
<div class="modal fade" id="areaModal" tabindex="-1" role="dialog" aria-labelledby="areaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-narrow" role="document">
        <div class="modal-content border-0 modal-pn">
            <div class="modal-header border-0 text-white px-4 py-3">
                <h5 class="modal-title" id="areaModalLabel">
                    <i class="fas fa-map-marker-alt mr-2"></i><span id="modalTitleText">Add Area / علاقہ شامل کریں</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="areaForm" novalidate>
                @csrf
                <input type="hidden" id="areaId">
                <div class="modal-body px-4 py-4">
                    <div class="mb-3">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">
                            City / شہر <span class="text-danger">*</span>
                        </label>
                        <select id="aCityId" class="form-control fc-pn" required>
                            <option value="">— Select City —</option>
                            @foreach($cities as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                        <div id="cityError" class="text-danger mt-1 err-msg"></div>
                    </div>
                    <div>
                        <label class="pn-label text-uppercase font-weight-bold text-muted">
                            Area Name / علاقے کا نام <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="aName" class="form-control fc-pn"
                               placeholder="e.g. Main Bazaar, Near Masjid" required>
                        <div id="nameError" class="text-danger mt-1 err-msg"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 py-3">
                    <button type="button" class="btn btn-light px-4 btn-modal-cancel" data-dismiss="modal" data-bs-dismiss="modal">
                        Cancel / منسوخ
                    </button>
                    <button type="submit" class="btn btn-primary px-4 btn-modal-save" id="submitBtn">
                        <i class="fas fa-save mr-1"></i> <span id="submitText">Save Area / محفوظ کریں</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(function () {

    // ── DataTable ───────────────────────────────────────────────
    @if($areas->count() > 0)
    $('#areaTable').DataTable({
        paging: true,
        pageLength: 20,
        lengthChange: false,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        responsive: true,
        order: [[1, 'asc'], [0, 'asc']],
        columnDefs: [{ orderable: false, targets: [6] }],
        language: {
            search: '',
            searchPlaceholder: 'Search areas...',
            info: 'Showing _START_–_END_ of _TOTAL_',
            paginate: { previous: '‹', next: '›' }
        }
    });
    @endif

    // ── Helpers ─────────────────────────────────────────────────
    function openModal(id, name, cityId) {
        $('#areaId').val(id || '');
        $('#aCityId').val(cityId || '{{ $selectedCity ?? '' }}');
        $('#aName').val(name || '');
        $('#nameError, #cityError').hide().text('');
        $('#aCityId, #aName').removeClass('is-invalid');

        if (id) {
            $('#modalTitleText').text('Edit Area / علاقہ ترمیم');
            $('#submitText').text('Update Area / اپ ڈیٹ کریں');
        } else {
            $('#modalTitleText').text('Add Area / علاقہ شامل کریں');
            $('#submitText').text('Save Area / محفوظ کریں');
        }
        $('#areaModal').modal('show');
    }

    // ── Open add ────────────────────────────────────────────────
    $(document).on('click', '#addBtn, #addBtnEmpty', function () {
        openModal();
    });

    // ── Edit ────────────────────────────────────────────────────
    $(document).on('click', '.edit-btn', function () {
        const id = $(this).data('id');
        $.get(APP_URL + '/areas/' + id + '/edit')
            .done(function (a) { openModal(a.id, a.name, a.city_id); })
            .fail(function ()  { toastr.error('Could not load area.'); });
    });

    // ── Submit ──────────────────────────────────────────────────
    $('#areaForm').on('submit', function (e) {
        e.preventDefault();

        const id      = $('#areaId').val();
        const cityId  = $('#aCityId').val();
        const name    = $.trim($('#aName').val());
        const btn     = $('#submitBtn');
        const origHtml = btn.html();

        // Client-side validation
        let valid = true;
        $('#nameError, #cityError').hide().text('');
        if (!cityId) {
            $('#cityError').text('Please select a city.').show();
            valid = false;
        }
        if (!name) {
            $('#nameError').text('Area name is required.').show();
            valid = false;
        }
        if (!valid) return;

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        const url    = id ? (APP_URL + '/areas/' + id) : APP_URL + '/areas';
        const method = id ? 'PUT' : 'POST';

        $.ajax({
            url,
            type: 'POST',
            data: {
                _token: $('input[name=_token]').val(),
                _method: method,
                city_id: cityId,
                name: name,
            },
            success: function (res) {
                const a = res.area;
                if (id) {
                    $('#area_name_' + id).text(a.name);
                    $('#area_city_'  + id).text(a.city ? a.city.name : '—');
                    toastr.success('Area updated!');
                    $('#areaModal').modal('hide');
                } else {
                    toastr.success('Area added!');
                    setTimeout(() => location.reload(), 600);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errs = xhr.responseJSON.errors || {};
                    if (errs.name)    $('#nameError').text(errs.name[0]).show();
                    if (errs.city_id) $('#cityError').text(errs.city_id[0]).show();
                } else {
                    toastr.error('Something went wrong. Please try again.');
                }
                btn.prop('disabled', false).html(origHtml);
            },
            complete: function (xhr) {
                if (xhr.status !== 422) {
                    btn.prop('disabled', false).html(origHtml);
                }
            }
        });
    });

    // ── Delete ──────────────────────────────────────────────────
    $(document).on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Delete this area?',
            text: 'Shops in this area will have their area removed.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then(r => {
            if (!r.isConfirmed) return;
            $.ajax({
                url: APP_URL + '/areas/' + id,
                type: 'POST',
                data: { _method: 'DELETE', _token: $('input[name=_token]').val() },
                success: function () {
                    $('#row_' + id).fadeOut(300, function () { $(this).remove(); });
                    toastr.success('Area deleted.');
                },
                error: function () { toastr.error('Could not delete area.'); }
            });
        });
    });

    // ── Reset modal on close ────────────────────────────────────
    $('#areaModal').on('hidden.bs.modal', function () {
        $('#areaForm')[0].reset();
        $('#areaId').val('');
        $('#nameError, #cityError').hide().text('');
        $('#submitBtn').prop('disabled', false)
            .html('<i class="fas fa-save mr-1"></i> <span id="submitText">Save Area / محفوظ کریں</span>');
    });

});
</script>
@endsection
