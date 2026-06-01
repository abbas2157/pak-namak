@extends('admin.layout.app')
@section('title', 'Areas')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Areas <small class="text-muted" style="font-size:14px;">علاقے</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.cities.index') }}">Cities</a></li>
                    <li class="breadcrumb-item active">Areas</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end" style="gap:8px;">
                <a href="{{ route('admin.cities.index') }}"
                   class="btn btn-light px-3" style="border-radius:8px;border:1px solid #d1d5db;">
                    <i class="fas fa-city mr-1"></i> Cities
                </a>
                <button class="btn btn-primary px-4" style="border-radius:8px;" id="addBtn">
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
                <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #4e73df !important;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Areas / کل علاقے</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ $totalAreas }}</div>
                                <small class="text-muted">{{ $totalShops }} shops</small>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:46px;height:46px;background:rgba(78,115,223,.12);">
                                <i class="fas fa-map-marker-alt" style="color:#4e73df;font-size:18px;"></i>
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
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Revenue / کل آمدن</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($totalRevenue, 0) }}</div>
                                <small class="text-muted">PKR all areas</small>
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
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Pending / کل باقی</div>
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
                <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #36b9cc !important;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Shops / کل دکانیں</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ $totalShops }}</div>
                                <small class="text-muted">across all areas</small>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:46px;height:46px;background:rgba(54,185,204,.12);">
                                <i class="fas fa-store" style="color:#36b9cc;font-size:18px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- City filter tabs --}}
        <div class="mb-3 d-flex flex-wrap" style="gap:6px;">
            <a href="{{ route('admin.areas.index') }}"
               class="btn btn-sm {{ !$selectedCity ? 'btn-secondary' : 'btn-outline-secondary' }}"
               style="border-radius:20px;">All Cities</a>
            @foreach($cities as $c)
                <a href="{{ route('admin.areas.index', ['city_id' => $c->id]) }}"
                   class="btn btn-sm {{ $selectedCity == $c->id ? 'btn-primary' : 'btn-outline-primary' }}"
                   style="border-radius:20px;">{{ $c->name }}</a>
            @endforeach
        </div>

        {{-- Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 font-weight-bold" style="color:#4e73df;">
                    <i class="fas fa-map-marker-alt mr-2"></i>Areas Directory / علاقوں کی فہرست
                </h6>
                <span class="badge" style="background:#e8f0fe;color:#4e73df;font-size:12px;padding:5px 10px;border-radius:20px;">
                    {{ $totalAreas }} areas
                </span>
            </div>
            <div class="card-body p-0">
                @if($areas->count() > 0)
                <div class="table-responsive">
                    <table class="table mb-0" id="areaTable" style="font-size:13.5px;">
                        <thead>
                            <tr style="background:#f8f9fc;border-bottom:2px solid #e3e6f0;">
                                <th class="pl-3 py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Area / علاقہ</th>
                                <th class="py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">City / شہر</th>
                                <th class="py-3 text-center text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Shops</th>
                                <th class="py-3 text-center text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Sales</th>
                                <th class="py-3 text-right text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Revenue (PKR)</th>
                                <th class="py-3 text-right text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Pending (PKR)</th>
                                <th class="py-3 text-center text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($areas as $area)
                            <tr id="row_{{ $area->id }}" style="border-bottom:1px solid #f0f0f0;">
                                <td class="pl-3 py-3 align-middle">
                                    <span class="font-weight-bold" style="color:#2d3748;" id="area_name_{{ $area->id }}">{{ $area->name }}</span>
                                </td>
                                <td class="py-3 align-middle">
                                    <span class="badge" style="background:#e8f0fe;color:#4e73df;font-size:11px;padding:4px 10px;border-radius:20px;" id="area_city_{{ $area->id }}">
                                        {{ $area->city->name ?? '—' }}
                                    </span>
                                </td>
                                <td class="py-3 align-middle text-center">
                                    <span class="badge" style="background:#f3f4f6;color:#374151;font-size:12px;padding:5px 10px;border-radius:20px;">
                                        {{ $area->shops_count }}
                                    </span>
                                </td>
                                <td class="py-3 align-middle text-center">
                                    <span class="badge" style="background:#d4edda;color:#155724;font-size:12px;padding:5px 10px;border-radius:20px;">
                                        {{ $area->total_sales }}
                                    </span>
                                </td>
                                <td class="py-3 align-middle text-right font-weight-bold" style="color:#2d3748;">
                                    {{ number_format($area->total_revenue, 0) }}
                                </td>
                                <td class="py-3 align-middle text-right">
                                    @if($area->total_pending > 0)
                                        <span class="font-weight-bold" style="color:#e74a3b;">{{ number_format($area->total_pending, 0) }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="py-3 align-middle text-center" style="white-space:nowrap;">
                                    <button class="btn btn-sm edit-btn mr-1"
                                            data-id="{{ $area->id }}"
                                            style="background:#fff3cd;color:#856404;border:1px solid #ffc107;border-radius:6px;"
                                            title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm delete-btn"
                                            data-id="{{ $area->id }}"
                                            style="background:#fce8e6;color:#c62828;border:1px solid #ef9a9a;border-radius:6px;"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background:#f8f9fc;border-top:2px solid #e3e6f0;">
                                <td class="pl-3 py-3 font-weight-bold" colspan="4" style="color:#2d3748;">Totals / کل</td>
                                <td class="py-3 text-right font-weight-bold" style="color:#2d3748;font-size:15px;">{{ number_format($totalRevenue, 0) }}</td>
                                <td class="py-3 text-right font-weight-bold" style="color:#e74a3b;font-size:15px;">{{ number_format($totalPending, 0) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-map-marker-alt fa-3x mb-3 d-block" style="color:#d1d5db;"></i>
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
    <div class="modal-dialog modal-dialog-centered" style="max-width:460px;" role="document">
        <div class="modal-content border-0" style="border-radius:12px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.15);">
            <div class="modal-header border-0 text-white px-4 py-3"
                 style="background:linear-gradient(135deg,#36b9cc,#1a8fa0);">
                <h5 class="modal-title" id="areaModalLabel">
                    <i class="fas fa-map-marker-alt mr-2"></i><span id="modalTitleText">Add Area / علاقہ شامل کریں</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" style="opacity:.8;" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="areaForm" novalidate>
                @csrf
                <input type="hidden" id="areaId">
                <div class="modal-body px-4 py-4">
                    <div class="mb-3">
                        <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                            City / شہر <span class="text-danger">*</span>
                        </label>
                        <select id="aCityId" class="form-control" style="border-radius:8px;border-color:#d1d5db;" required>
                            <option value="">— Select City —</option>
                            @foreach($cities as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                        <div id="cityError" class="text-danger mt-1" style="font-size:12px;display:none;"></div>
                    </div>
                    <div>
                        <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                            Area Name / علاقے کا نام <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="aName" class="form-control"
                               style="border-radius:8px;border-color:#d1d5db;" placeholder="e.g. Main Bazaar, Near Masjid" required>
                        <div id="nameError" class="text-danger mt-1" style="font-size:12px;display:none;"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 py-3" style="background:#f8f9fc;">
                    <button type="button" class="btn btn-light px-4" data-dismiss="modal" data-bs-dismiss="modal"
                            style="border-radius:8px;border:1px solid #d1d5db;">
                        Cancel / منسوخ
                    </button>
                    <button type="submit" class="btn btn-primary px-4" id="submitBtn"
                            style="border-radius:8px;background:linear-gradient(135deg,#36b9cc,#1a8fa0);border:none;">
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
