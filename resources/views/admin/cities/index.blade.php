@extends('admin.layout.app')
@section('title', 'Cities & Areas')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Cities & Areas <small class="text-muted">شہر اور علاقے</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Cities</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end">
                <a href="{{ route('admin.shops.index') }}"
                   class="btn btn-light px-3 mr-2 btn-modal-cancel">
                    <i class="fas fa-store mr-1"></i> Shops
                </a>
                <button class="btn btn-primary px-4 btn-modal-save" id="addCityBtn">
                    <i class="fas fa-plus mr-1"></i> Add City / شہر شامل کریں
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
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Cities / کل شہر</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ $totalCities }}</div>
                                <small class="text-muted">{{ $totalShops }} shops</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-blue">
                                <i class="fas fa-city"></i>
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
                                <small class="text-muted">PKR all cities</small>
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
                                <small class="text-muted">across all cities</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-cyan">
                                <i class="fas fa-store"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- City cards --}}
        @forelse($cities as $city)
        <div class="card border-0 shadow-sm mb-3" id="city_card_{{ $city->id }}">

            {{-- City header --}}
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <button class="toggle-btn btn btn-sm btn-city-toggle mr-3"
                            data-city="{{ $city->id }}">
                        <i class="fas fa-chevron-right" id="toggle_icon_{{ $city->id }}"></i>
                    </button>
                    <div>
                        <h6 class="mb-0 font-weight-bold pn-text-heading" id="city_name_{{ $city->id }}">
                            {{ $city->name }}
                        </h6>
                        <small class="text-muted" id="city_meta_{{ $city->id }}">
                            {{ $city->shops_count }} shops &nbsp;·&nbsp;
                            <span id="area_count_{{ $city->id }}">{{ $city->areas->count() }}</span> areas &nbsp;·&nbsp;
                            PKR {{ number_format($city->total_revenue, 0) }}
                        </small>
                    </div>
                </div>
                <div class="d-flex">
                    <a href="{{ route('admin.cities.sales', $city->id) }}"
                       class="btn btn-sm btn-pn btn-act-confirm mr-1"
                       title="Sales Report">
                        <i class="fas fa-chart-bar mr-1"></i> Sales
                    </a>
                    <button class="btn btn-sm btn-pn btn-act-view add-area-btn mr-1"
                            data-city="{{ $city->id }}"
                            data-city-name="{{ $city->name }}"
                            title="Add Area">
                        <i class="fas fa-plus mr-1"></i> Add Area
                    </button>
                    <button class="btn btn-sm btn-pn btn-act-edit edit-city-btn mr-1"
                            data-id="{{ $city->id }}"
                            title="Edit City">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-pn btn-act-delete delete-city-btn"
                            data-id="{{ $city->id }}"
                            title="Delete City">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            {{-- Areas panel --}}
            <div id="areas_panel_{{ $city->id }}" style="display:none;">
                <table class="table mb-0 pn-table pn-table-font">
                    <thead>
                        <tr>
                            <th class="pl-4 py-2 text-uppercase">Area / علاقہ</th>
                            <th class="py-2 text-center text-uppercase">Shops / دکانیں</th>
                            <th class="py-2 text-center text-uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="areas_tbody_{{ $city->id }}">
                        @forelse($city->areas as $area)
                        <tr id="area_row_{{ $area->id }}" class="pn-table-row">
                            <td class="pl-4 py-2 align-middle">
                                <span id="area_name_{{ $area->id }}" class="text-c-dark">{{ $area->name }}</span>
                            </td>
                            <td class="py-2 align-middle text-center">
                                <span class="badge bdg-blue bdg-sm">
                                    {{ $area->shops->count() }}
                                </span>
                            </td>
                            <td class="py-2 align-middle text-center text-nowrap">
                                <button class="btn btn-sm btn-pn btn-act-edit edit-area-btn mr-1"
                                        data-id="{{ $area->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-pn btn-act-delete delete-area-btn"
                                        data-id="{{ $area->id }}"
                                        data-city="{{ $city->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr id="no_areas_{{ $city->id }}">
                            <td colspan="3" class="pl-4 py-3 text-muted">
                                No areas yet. Click <strong>Add Area</strong> to add one.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @empty
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-city fa-3x mb-3 d-block text-muted"></i>
                <p class="text-muted mb-0">No cities added yet.</p>
                <button class="btn btn-sm btn-primary mt-3" id="addCityBtnEmpty">
                    <i class="fas fa-plus mr-1"></i> Add First City
                </button>
            </div>
        </div>
        @endforelse

    </div>
</section>

{{-- City Modal --}}
<div class="modal fade" id="cityModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;" role="document">
        <div class="modal-content border-0 modal-pn">
            <div class="modal-header border-0 text-white px-4 py-3">
                <h5 class="modal-title">
                    <i class="fas fa-city mr-2"></i><span id="cityModalTitle">Add City / شہر شامل کریں</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <form id="cityForm" novalidate>
                @csrf
                <input type="hidden" id="cityId">
                <div class="modal-body px-4 py-4">
                    <label class="pn-label text-uppercase font-weight-bold text-muted">
                        City Name / شہر کا نام <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="cName" class="form-control fc-pn"
                           placeholder="e.g. Lahore, Khewra" required>
                    <div id="cityNameError" class="text-danger mt-1" style="font-size:12px;display:none;"></div>
                </div>
                <div class="modal-footer border-0 px-4 py-3">
                    <button type="button" class="btn btn-light px-4 btn-modal-cancel" data-dismiss="modal" data-bs-dismiss="modal">Cancel / منسوخ</button>
                    <button type="submit" class="btn btn-primary px-4 btn-modal-save" id="citySubmitBtn">
                        <i class="fas fa-save mr-1"></i> <span id="citySubmitText">Save City / محفوظ کریں</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Area Modal --}}
<div class="modal fade" id="areaModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;" role="document">
        <div class="modal-content border-0 modal-pn">
            <div class="modal-header border-0 text-white px-4 py-3">
                <h5 class="modal-title">
                    <i class="fas fa-map-marker-alt mr-2"></i><span id="areaModalTitle">Add Area / علاقہ شامل کریں</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <form id="areaForm" novalidate>
                @csrf
                <input type="hidden" id="areaId">
                <input type="hidden" id="areaCityId">
                <div class="modal-body px-4 py-4">
                    <div class="mb-3 p-2 rounded btn-area-info">
                        <small class="text-muted d-block">City / شہر:</small>
                        <strong id="areaModalCityName" class="text-c-blue2"></strong>
                    </div>
                    <label class="pn-label text-uppercase font-weight-bold text-muted">
                        Area Name / علاقے کا نام <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="aName" class="form-control fc-pn"
                           placeholder="e.g. Main Bazaar, Near Masjid" required>
                    <div id="areaNameError" class="text-danger mt-1" style="font-size:12px;display:none;"></div>
                </div>
                <div class="modal-footer border-0 px-4 py-3">
                    <button type="button" class="btn btn-light px-4 btn-modal-cancel" data-dismiss="modal" data-bs-dismiss="modal">Cancel / منسوخ</button>
                    <button type="submit" class="btn btn-primary px-4 btn-modal-save" id="areaSubmitBtn">
                        <i class="fas fa-save mr-1"></i> <span id="areaSubmitText">Save Area / محفوظ کریں</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const CSRF = $('input[name=_token]').first().val();

$(function () {

    // ════════════════════════════════════════
    // TOGGLE AREAS PANEL
    // ════════════════════════════════════════
    $(document).on('click', '.toggle-btn', function () {
        const id   = $(this).data('city');
        const icon = $('#toggle_icon_' + id);
        $('#areas_panel_' + id).slideToggle(150, function () {
            icon.toggleClass('fa-chevron-right fa-chevron-down');
        });
    });

    // ════════════════════════════════════════
    // CITY — ADD
    // ════════════════════════════════════════
    $(document).on('click', '#addCityBtn, #addCityBtnEmpty', function () {
        $('#cityId').val('');
        $('#cName').val('');
        $('#cityNameError').hide().text('');
        $('#cityModalTitle').text('Add City / شہر شامل کریں');
        $('#citySubmitText').text('Save City / محفوظ کریں');
        $('#cityModal').modal('show');
    });

    // ════════════════════════════════════════
    // CITY — EDIT
    // ════════════════════════════════════════
    $(document).on('click', '.edit-city-btn', function () {
        const id = $(this).data('id');
        $.get(APP_URL + '/cities/' + id + '/edit')
            .done(function (c) {
                $('#cityId').val(c.id);
                $('#cName').val(c.name);
                $('#cityNameError').hide().text('');
                $('#cityModalTitle').text('Edit City / شہر ترمیم');
                $('#citySubmitText').text('Update City / اپ ڈیٹ کریں');
                $('#cityModal').modal('show');
            })
            .fail(function () { toastr.error('Could not load city.'); });
    });

    // ════════════════════════════════════════
    // CITY — SUBMIT
    // ════════════════════════════════════════
    $('#cityForm').on('submit', function (e) {
        e.preventDefault();

        const id       = $('#cityId').val();
        const name     = $.trim($('#cName').val());
        const btn      = $('#citySubmitBtn');
        const origHtml = btn.html();

        if (!name) {
            $('#cityNameError').text('City name is required.').show();
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');
        $('#cityNameError').hide().text('');

        const url    = id ? (APP_URL + '/cities/' + id) : APP_URL + '/cities';
        const method = id ? 'PUT' : 'POST';

        $.ajax({
            url,
            type: 'POST',
            data: { _token: CSRF, _method: method, name },
            success: function (res) {
                if (id) {
                    $('#city_name_' + id).text(res.city.name);
                    toastr.success('City updated!');
                    $('#cityModal').modal('hide');
                } else {
                    toastr.success('City added!');
                    setTimeout(() => location.reload(), 600);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON.errors?.name) {
                    $('#cityNameError').text(xhr.responseJSON.errors.name[0]).show();
                } else {
                    toastr.error('Something went wrong.');
                }
                btn.prop('disabled', false).html(origHtml);
            },
            complete: function (xhr) {
                if (xhr.status !== 422) btn.prop('disabled', false).html(origHtml);
            }
        });
    });

    // ════════════════════════════════════════
    // CITY — DELETE
    // ════════════════════════════════════════
    $(document).on('click', '.delete-city-btn', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Delete this city?',
            text: 'All areas in this city will also be deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then(r => {
            if (!r.isConfirmed) return;
            $.ajax({
                url: APP_URL + '/cities/' + id,
                type: 'POST',
                data: { _method: 'DELETE', _token: CSRF },
                success: function () {
                    $('#city_card_' + id).fadeOut(300, function () { $(this).remove(); });
                    toastr.success('City deleted.');
                },
                error: function () { toastr.error('Could not delete city.'); }
            });
        });
    });

    // ════════════════════════════════════════
    // AREA — ADD (from city card)
    // ════════════════════════════════════════
    $(document).on('click', '.add-area-btn', function () {
        $('#areaId').val('');
        $('#areaCityId').val($(this).data('city'));
        $('#areaModalCityName').text($(this).data('city-name'));
        $('#aName').val('');
        $('#areaNameError').hide().text('');
        $('#areaModalTitle').text('Add Area / علاقہ شامل کریں');
        $('#areaSubmitText').text('Save Area / محفوظ کریں');
        $('#areaModal').modal('show');
    });

    // ════════════════════════════════════════
    // AREA — EDIT
    // ════════════════════════════════════════
    $(document).on('click', '.edit-area-btn', function () {
        const id = $(this).data('id');
        $.get(APP_URL + '/areas/' + id + '/edit')
            .done(function (a) {
                $('#areaId').val(a.id);
                $('#areaCityId').val(a.city_id);
                $('#areaModalCityName').text($('#city_name_' + a.city_id).text().trim());
                $('#aName').val(a.name);
                $('#areaNameError').hide().text('');
                $('#areaModalTitle').text('Edit Area / علاقہ ترمیم');
                $('#areaSubmitText').text('Update Area / اپ ڈیٹ کریں');
                $('#areaModal').modal('show');
            })
            .fail(function () { toastr.error('Could not load area.'); });
    });

    // ════════════════════════════════════════
    // AREA — SUBMIT
    // ════════════════════════════════════════
    $('#areaForm').on('submit', function (e) {
        e.preventDefault();

        const id       = $('#areaId').val();
        const cityId   = $('#areaCityId').val();
        const name     = $.trim($('#aName').val());
        const btn      = $('#areaSubmitBtn');
        const origHtml = btn.html();

        if (!name) {
            $('#areaNameError').text('Area name is required.').show();
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');
        $('#areaNameError').hide().text('');

        const url    = id ? (APP_URL + '/areas/' + id) : APP_URL + '/areas';
        const method = id ? 'PUT' : 'POST';

        $.ajax({
            url,
            type: 'POST',
            data: { _token: CSRF, _method: method, city_id: cityId, name },
            success: function (res) {
                const a = res.area;
                if (id) {
                    // Update name in row
                    $('#area_name_' + id).text(a.name);
                    toastr.success('Area updated!');
                } else {
                    // Remove empty-state row if present
                    $('#no_areas_' + cityId).remove();

                    // Append new row
                    $('#areas_tbody_' + cityId).append(
                        `<tr id="area_row_${a.id}" class="pn-table-row">
                            <td class="pl-4 py-2 align-middle">
                                <span id="area_name_${a.id}" class="text-c-dark">${a.name}</span>
                            </td>
                            <td class="py-2 align-middle text-center">
                                <span class="badge bdg-blue bdg-sm">0</span>
                            </td>
                            <td class="py-2 align-middle text-center text-nowrap">
                                <button class="btn btn-sm btn-pn btn-act-edit edit-area-btn mr-1" data-id="${a.id}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-pn btn-act-delete delete-area-btn" data-id="${a.id}" data-city="${cityId}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>`
                    );

                    // Auto-expand panel & update area count
                    const panel = $('#areas_panel_' + cityId);
                    if (!panel.is(':visible')) {
                        panel.slideDown(150);
                        $('#toggle_icon_' + cityId).removeClass('fa-chevron-right').addClass('fa-chevron-down');
                    }
                    const countEl = $('#area_count_' + cityId);
                    countEl.text(parseInt(countEl.text() || 0) + 1);

                    toastr.success('Area added!');
                }
                $('#areaModal').modal('hide');
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON.errors?.name) {
                    $('#areaNameError').text(xhr.responseJSON.errors.name[0]).show();
                } else {
                    toastr.error('Something went wrong.');
                }
                btn.prop('disabled', false).html(origHtml);
            },
            complete: function (xhr) {
                if (xhr.status !== 422) btn.prop('disabled', false).html(origHtml);
            }
        });
    });

    // ════════════════════════════════════════
    // AREA — DELETE
    // ════════════════════════════════════════
    $(document).on('click', '.delete-area-btn', function () {
        const id     = $(this).data('id');
        const cityId = $(this).data('city');
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
                data: { _method: 'DELETE', _token: CSRF },
                success: function () {
                    $('#area_row_' + id).fadeOut(200, function () {
                        $(this).remove();
                        // Update area count in city header
                        const countEl = $('#area_count_' + cityId);
                        const current = parseInt(countEl.text() || 0);
                        if (current > 0) countEl.text(current - 1);

                        // Show empty-state if no areas left
                        if ($('#areas_tbody_' + cityId + ' tr').length === 0) {
                            $('#areas_tbody_' + cityId).append(
                                `<tr id="no_areas_${cityId}">
                                    <td colspan="3" class="pl-4 py-3 text-muted">
                                        No areas yet. Click <strong>Add Area</strong> to add one.
                                    </td>
                                </tr>`
                            );
                        }
                    });
                    toastr.success('Area deleted.');
                },
                error: function () { toastr.error('Could not delete area.'); }
            });
        });
    });

    // ════════════════════════════════════════
    // RESET MODALS ON CLOSE
    // ════════════════════════════════════════
    $('#cityModal').on('hidden.bs.modal', function () {
        $('#cityForm')[0].reset();
        $('#cityId').val('');
        $('#cityNameError').hide().text('');
        $('#citySubmitBtn').prop('disabled', false)
            .html('<i class="fas fa-save mr-1"></i> <span id="citySubmitText">Save City / محفوظ کریں</span>');
    });

    $('#areaModal').on('hidden.bs.modal', function () {
        $('#areaForm')[0].reset();
        $('#areaId').val('');
        $('#areaCityId').val('');
        $('#areaNameError').hide().text('');
        $('#areaSubmitBtn').prop('disabled', false)
            .html('<i class="fas fa-save mr-1"></i> <span id="areaSubmitText">Save Area / محفوظ کریں</span>');
    });

});
</script>
@endsection
