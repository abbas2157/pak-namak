@extends('admin.layout.app')
@section('title', 'Productions')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Productions <small class="text-muted ch-sub">پیداوار</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Productions</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end">
                <button class="btn btn-primary btn-pn px-4" id="addBtn">
                    <i class="fas fa-plus mr-1"></i> Add Production / پیداوار شامل کریں
                </button>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        {{-- ── STATS ──────────────────────────────────── --}}
        <div class="row mb-4">
            <div class="col-xl col-md-4 col-sm-6 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-blue">
                    <div class="card-body py-3 px-4">
                        <div class="pn-stat-lbl">Total Batches / کل بیچز</div>
                        <div class="pn-stat-num-md text-c-blue2">{{ $productions->count() }}</div>
                        <div class="pn-stat-sub">production runs</div>
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100">
                    <div class="card-body py-3 px-4">
                        <div class="pn-stat-lbl">Raw Salt Used / خام نمک استعمال</div>
                        <div class="pn-stat-num-md text-muted">{{ number_format($totalRaw, 0) }}</div>
                        <div class="pn-stat-sub">KG processed</div>
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-teal">
                    <div class="card-body py-3 px-4">
                        <div class="pn-stat-lbl">Finished Salt / تیار نمک</div>
                        <div class="pn-stat-num-md text-c-teal">{{ number_format($totalFinished, 0) }}</div>
                        <div class="pn-stat-sub">KG produced</div>
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-red">
                    <div class="card-body py-3 px-4">
                        <div class="pn-stat-lbl">Total Wastage / کل ضیاع</div>
                        <div class="pn-stat-num-md text-c-red">{{ number_format($totalWastage, 0) }}</div>
                        <div class="pn-stat-sub">KG lost</div>
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-teal">
                    <div class="card-body py-3 px-4">
                        <div class="pn-stat-lbl">Efficiency / کارکردگی</div>
                        <div class="pn-stat-num-md text-c-teal">{{ $efficiency }}%</div>
                        <div class="progress progress-xs mt-1">
                            <div class="pbar pbar-teal" style="--w:{{ $efficiency }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-yellow">
                    <div class="card-body py-3 px-4">
                        <div class="pn-stat-lbl">Total Cost / کل لاگت</div>
                        <div class="pn-stat-num-md text-c-warn">{{ number_format($totalCost, 0) }}</div>
                        <div class="pn-stat-sub">PKR fuel/electricity</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── TABLE ──────────────────────────────────── --}}
        <div class="card card-pn border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                <table class="table table-sm pn-table pn-table-font mb-0" id="productionsTable">
                    <thead>
                        <tr>
                            <th class="pl-3">Date / تاریخ</th>
                            <th class="text-right">Raw Salt (KG) / خام نمک</th>
                            <th class="text-right">Finished (KG) / تیار</th>
                            <th class="text-right">Wastage (KG) / ضیاع</th>
                            <th class="text-center">Efficiency / کارکردگی</th>
                            <th>Machine / مشین</th>
                            <th class="text-right">Cost (PKR) / لاگت</th>
                            <th>Remarks / ملاحظات</th>
                            <th class="text-center pr-3">Actions / اقدامات</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($productions as $i => $p)
                        @php
                            $eff = $p->raw_salt_used > 0
                                ? round(($p->finished_salt / $p->raw_salt_used) * 100, 1)
                                : 0;
                        @endphp
                        <tr id="prodRow{{ $p->id }}">
                            <td class="pl-3">
                                <span class="font-weight-bold pn-table-font">
                                    {{ $p->production_date ? \Carbon\Carbon::parse($p->production_date)->format('d M Y') : '—' }}
                                </span>
                            </td>
                            <td class="text-right pn-table-font">{{ number_format($p->raw_salt_used, 0) }}</td>
                            <td class="text-right font-weight-bold pn-table-font text-c-teal">
                                {{ number_format($p->finished_salt, 0) }}
                            </td>
                            <td class="text-right pn-table-font text-c-red">
                                {{ number_format($p->wastage ?? 0, 0) }}
                            </td>
                            <td class="text-center">
                                <span class="badge pn-bdg {{ $eff >= 90 ? 'badge-success' : ($eff >= 75 ? 'badge-warning' : 'badge-danger') }}">
                                    {{ $eff }}%
                                </span>
                            </td>
                            <td class="text-muted pn-stat-sub">{{ $p->machine_used ?? '—' }}</td>
                            <td class="text-right pn-table-font">
                                {{ $p->electricity_fuel_cost ? number_format($p->electricity_fuel_cost, 0) : '—' }}
                            </td>
                            <td class="text-muted pn-stat-sub col-narrow">
                                <span title="{{ $p->remarks }}">
                                    {{ $p->remarks ? \Str::limit($p->remarks, 30) : '—' }}
                                </span>
                            </td>
                            <td class="text-center pr-3">
                                <button class="btn btn-sm btn-pn btn-act-edit editBtn" data-id="{{ $p->id }}"
                                        title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-pn btn-act-delete deleteBtn" data-id="{{ $p->id }}"
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-row">
                            <td colspan="9">
                                <i class="fas fa-industry empty-icon"></i>
                                <p class="empty-msg mb-0">No production records yet.</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                    @if($productions->count() > 0)
                    <tfoot>
                        <tr class="pn-total-row font-weight-bold pn-table-font">
                            <td class="pl-3">Total / کل</td>
                            <td class="text-right">{{ number_format($totalRaw, 0) }}</td>
                            <td class="text-right text-c-teal">{{ number_format($totalFinished, 0) }}</td>
                            <td class="text-right text-c-red">{{ number_format($totalWastage, 0) }}</td>
                            <td class="text-center">
                                <span class="badge badge-info pn-bdg">{{ $efficiency }}% avg</span>
                            </td>
                            <td colspan="2" class="text-right">{{ number_format($totalCost, 0) }}</td>
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

{{-- ── MODAL (Add / Edit) ──────────────────────────────── --}}
<div class="modal fade modal-pn" id="productionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="productionForm">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" id="production_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-industry mr-2"></i>Add Production / پیداوار شامل کریں
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-6 mb-3">
                        <label class="filter-lbl">Production Date / پیداواری تاریخ <span class="text-danger">*</span></label>
                        <input type="date" class="form-control fc-pn" name="production_date" id="production_date"
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="filter-lbl">Machine Used / مشین</label>
                        <input type="text" class="form-control fc-pn" name="machine_used" id="machine_used"
                               placeholder="e.g. Machine #1">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="filter-lbl">Raw Salt (KG) / خام نمک <span class="text-danger">*</span></label>
                        <input type="number" class="form-control fc-pn" name="raw_salt_used" id="raw_salt_used"
                               min="0" step="0.01" placeholder="0" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="filter-lbl">Finished Salt (KG) / تیار نمک <span class="text-danger">*</span></label>
                        <input type="number" class="form-control fc-pn" name="finished_salt" id="finished_salt"
                               min="0" step="0.01" placeholder="0" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="filter-lbl">Wastage (KG) / ضیاع</label>
                        <input type="number" class="form-control fc-pn" name="wastage" id="wastage"
                               min="0" step="0.01" placeholder="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="filter-lbl">Electricity / Fuel Cost (PKR) / بجلی/ایندھن لاگت</label>
                        <input type="number" class="form-control fc-pn" name="electricity_fuel_cost" id="electricity_fuel_cost"
                               min="0" step="0.01" placeholder="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="filter-lbl">Efficiency / کارکردگی</label>
                        <div class="input-group">
                            <input type="text" class="form-control fc-ro-pn" id="efficiency_display" readonly
                                   placeholder="—">
                            <div class="input-group-append">
                                <span class="input-group-text fc-ro-pn">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="filter-lbl">Paid From / کہاں سے ادا کیا</label>
                        <select name="account_id" id="account_id" class="form-control fc-pn">
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ $account->type === 'cash' ? 'selected' : '' }}>{{ $account->label() }}</option>
                            @endforeach
                            <option value="">Other / Not from Cash &amp; Bank (کیش/بینک سے نہیں)</option>
                        </select>
                        <small class="text-muted">Only used if a cost above is entered.</small>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="filter-lbl">Remarks / ملاحظات</label>
                        <textarea class="form-control fc-pn" name="remarks" id="remarks" rows="2"
                                  placeholder="Optional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-modal-cancel" data-dismiss="modal">Cancel / منسوخ</button>
                    <button class="btn btn-primary btn-modal-save" type="submit" id="submitBtn">
                        <i class="fas fa-save mr-1"></i> Save / محفوظ کریں
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {

    // DataTables throws on an empty (colspan "no records") table when
    // columnDefs targets a specific column index — only initialize when
    // there are real rows to enhance.
    if ($('#productionsTable tbody tr').not(':has(td[colspan])').length > 0) {
        $('#productionsTable').DataTable({
            paging: true,
            pageLength: 25,
            lengthChange: false,
            searching: true,
            ordering: false,
            info: true,
            autoWidth: false,
            responsive: true,
            columnDefs: [{ orderable: false, targets: [8] }, { responsivePriority: 1, targets: -1 }],
        });
    }

    // Live efficiency calculator
    function calcEfficiency() {
        const raw = parseFloat($('#raw_salt_used').val()) || 0;
        const fin = parseFloat($('#finished_salt').val()) || 0;
        $('#efficiency_display').val(raw > 0 ? (fin / raw * 100).toFixed(1) : '');
    }
    $('#raw_salt_used, #finished_salt').on('input', calcEfficiency);

    // Open Add modal
    $('#addBtn').on('click', function () {
        $('#productionForm')[0].reset();
        $('#production_id').val('');
        $('#formMethod').val('POST');
        $('#production_date').val(new Date().toISOString().split('T')[0]);
        $('#efficiency_display').val('');
        $('#modalTitle').html('<i class="fas fa-industry mr-2"></i>Add Production');
        $('#submitBtn').html('<i class="fas fa-save mr-1"></i> Save');
        $('#productionModal').modal('show');
    });

    // Submit (add + edit)
    $('#productionForm').on('submit', function (e) {
        e.preventDefault();
        const id  = $('#production_id').val();
        const url = id
            ? (APP_URL + '/productions/' + id)
            : "{{ route('admin.productions.store') }}";

        const btn = $('#submitBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving…');

        $.ajax({
            url, type: 'POST',
            data: $(this).serialize() + (id ? '&_method=PUT' : ''),
            success: function () {
                toastr.success('Production saved!');
                $('#productionModal').modal('hide');
                setTimeout(() => location.reload(), 600);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    alert(Object.values(xhr.responseJSON.errors).map(e => e[0]).join('\n'));
                } else {
                    toastr.error('Something went wrong.');
                }
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save');
            }
        });
    });

    // Edit
    $(document).on('click', '.editBtn', function () {
        const id = $(this).data('id');
        $.get(APP_URL + '/productions/' + id + '/edit', function (p) {
            $('#production_id').val(p.id);
            $('#formMethod').val('PUT');
            $('#production_date').val(p.production_date);
            $('#raw_salt_used').val(p.raw_salt_used);
            $('#finished_salt').val(p.finished_salt);
            $('#wastage').val(p.wastage);
            $('#machine_used').val(p.machine_used);
            $('#electricity_fuel_cost').val(p.electricity_fuel_cost);
            $('#account_id').val(p.account_id);
            $('#remarks').val(p.remarks);
            calcEfficiency();
            $('#modalTitle').html('<i class="fas fa-edit mr-2"></i>Edit Production');
            $('#submitBtn').html('<i class="fas fa-save mr-1"></i> Update');
            $('#productionModal').modal('show');
        });
    });

    // Delete
    $(document).on('click', '.deleteBtn', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Delete this production record?',
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
                url: APP_URL + '/productions/' + id,
                type: 'POST',
                data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                success: function () {
                    $('#prodRow' + id).fadeOut(300, function () { $(this).remove(); });
                    toastr.success('Production deleted.');
                },
                error: function () { toastr.error('Delete failed.'); }
            });
        });
    });

});
</script>
@endsection
