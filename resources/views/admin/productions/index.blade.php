@extends('admin.layout.app')
@section('title', 'Productions')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Productions</h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Productions</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end">
                <button class="btn btn-primary px-4" style="border-radius:8px;" id="addBtn">
                    <i class="fas fa-plus mr-1"></i> Add Production
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
                <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #4e73df!important;border-radius:10px;">
                    <div class="card-body py-3 px-4">
                        <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.8px;color:#b0b7c3;">Total Batches</div>
                        <div style="font-size:28px;font-weight:800;color:#4e73df;line-height:1.1;">{{ $productions->count() }}</div>
                        <div style="font-size:11px;color:#b0b7c3;">production runs</div>
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #858796!important;border-radius:10px;">
                    <div class="card-body py-3 px-4">
                        <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.8px;color:#b0b7c3;">Raw Salt Used</div>
                        <div style="font-size:28px;font-weight:800;color:#858796;line-height:1.1;">{{ number_format($totalRaw, 0) }}</div>
                        <div style="font-size:11px;color:#b0b7c3;">KG processed</div>
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #1cc88a!important;border-radius:10px;">
                    <div class="card-body py-3 px-4">
                        <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.8px;color:#b0b7c3;">Finished Salt</div>
                        <div style="font-size:28px;font-weight:800;color:#1cc88a;line-height:1.1;">{{ number_format($totalFinished, 0) }}</div>
                        <div style="font-size:11px;color:#b0b7c3;">KG produced</div>
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #e74a3b!important;border-radius:10px;">
                    <div class="card-body py-3 px-4">
                        <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.8px;color:#b0b7c3;">Total Wastage</div>
                        <div style="font-size:28px;font-weight:800;color:#e74a3b;line-height:1.1;">{{ number_format($totalWastage, 0) }}</div>
                        <div style="font-size:11px;color:#b0b7c3;">KG lost</div>
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #1cc88a!important;border-radius:10px;">
                    <div class="card-body py-3 px-4">
                        <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.8px;color:#b0b7c3;">Efficiency</div>
                        <div style="font-size:28px;font-weight:800;color:#1cc88a;line-height:1.1;">{{ $efficiency }}%</div>
                        <div class="progress mt-1" style="height:4px;border-radius:4px;">
                            <div class="progress-bar bg-success" style="width:{{ $efficiency }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #f6c23e!important;border-radius:10px;">
                    <div class="card-body py-3 px-4">
                        <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.8px;color:#b0b7c3;">Total Cost</div>
                        <div style="font-size:24px;font-weight:800;color:#e0a800;line-height:1.1;">{{ number_format($totalCost, 0) }}</div>
                        <div style="font-size:11px;color:#b0b7c3;">PKR fuel/electricity</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── TABLE ──────────────────────────────────── --}}
        <div class="card border-0 shadow-sm" style="border-radius:10px;">
            <div class="card-body p-0">
                <table class="table table-sm mb-0" id="productionsTable">
                    <thead>
                        <tr style="background:#f8f9fc;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:#b0b7c3;">
                            <th class="pl-3">Date</th>
                            <th class="text-right">Raw Salt (KG)</th>
                            <th class="text-right">Finished (KG)</th>
                            <th class="text-right">Wastage (KG)</th>
                            <th class="text-center">Efficiency</th>
                            <th>Machine</th>
                            <th class="text-right">Cost (PKR)</th>
                            <th>Remarks</th>
                            <th class="text-center pr-3">Actions</th>
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
                                <span class="font-weight-bold" style="font-size:13px;">
                                    {{ $p->production_date ? \Carbon\Carbon::parse($p->production_date)->format('d M Y') : '—' }}
                                </span>
                            </td>
                            <td class="text-right" style="font-size:13px;">{{ number_format($p->raw_salt_used, 0) }}</td>
                            <td class="text-right font-weight-bold" style="font-size:13px;color:#1cc88a;">
                                {{ number_format($p->finished_salt, 0) }}
                            </td>
                            <td class="text-right" style="font-size:13px;color:#e74a3b;">
                                {{ number_format($p->wastage ?? 0, 0) }}
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $eff >= 90 ? 'badge-success' : ($eff >= 75 ? 'badge-warning' : 'badge-danger') }}"
                                      style="border-radius:20px;font-size:11px;padding:4px 8px;">
                                    {{ $eff }}%
                                </span>
                            </td>
                            <td style="font-size:12px;color:#6c757d;">{{ $p->machine_used ?? '—' }}</td>
                            <td class="text-right" style="font-size:13px;">
                                {{ $p->electricity_fuel_cost ? number_format($p->electricity_fuel_cost, 0) : '—' }}
                            </td>
                            <td style="font-size:12px;color:#6c757d;max-width:150px;">
                                <span title="{{ $p->remarks }}">
                                    {{ $p->remarks ? \Str::limit($p->remarks, 30) : '—' }}
                                </span>
                            </td>
                            <td class="text-center pr-3">
                                <button class="btn btn-sm btn-warning editBtn" data-id="{{ $p->id }}"
                                        style="border-radius:6px;" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger deleteBtn" data-id="{{ $p->id }}"
                                        style="border-radius:6px;" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-industry fa-2x mb-2 d-block" style="opacity:.3;"></i>
                                No production records yet.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                    @if($productions->count() > 0)
                    <tfoot>
                        <tr style="background:#f8f9fc;font-weight:700;font-size:13px;">
                            <td class="pl-3">Total</td>
                            <td class="text-right">{{ number_format($totalRaw, 0) }}</td>
                            <td class="text-right" style="color:#1cc88a;">{{ number_format($totalFinished, 0) }}</td>
                            <td class="text-right" style="color:#e74a3b;">{{ number_format($totalWastage, 0) }}</td>
                            <td class="text-center">
                                <span class="badge badge-info" style="border-radius:20px;font-size:11px;">{{ $efficiency }}% avg</span>
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
</section>

{{-- ── MODAL (Add / Edit) ──────────────────────────────── --}}
<div class="modal fade" id="productionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="productionForm">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" id="production_id">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-industry mr-2"></i>Add Production
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-6 mb-3">
                        <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;">Production Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="production_date" id="production_date"
                               value="{{ date('Y-m-d') }}" required style="border-radius:8px;">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;">Machine Used</label>
                        <input type="text" class="form-control" name="machine_used" id="machine_used"
                               placeholder="e.g. Machine #1" style="border-radius:8px;">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;">Raw Salt (KG) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="raw_salt_used" id="raw_salt_used"
                               min="0" step="0.01" placeholder="0" required style="border-radius:8px;">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;">Finished Salt (KG) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="finished_salt" id="finished_salt"
                               min="0" step="0.01" placeholder="0" required style="border-radius:8px;">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;">Wastage (KG)</label>
                        <input type="number" class="form-control" name="wastage" id="wastage"
                               min="0" step="0.01" placeholder="0" style="border-radius:8px;">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;">Electricity / Fuel Cost (PKR)</label>
                        <input type="number" class="form-control" name="electricity_fuel_cost" id="electricity_fuel_cost"
                               min="0" step="0.01" placeholder="0" style="border-radius:8px;">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;">Efficiency</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="efficiency_display" readonly
                                   placeholder="—" style="border-radius:8px 0 0 8px;background:#f8f9fc;">
                            <div class="input-group-append">
                                <span class="input-group-text" style="border-radius:0 8px 8px 0;background:#f8f9fc;">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;">Remarks</label>
                        <textarea class="form-control" name="remarks" id="remarks" rows="2"
                                  placeholder="Optional notes..." style="border-radius:8px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit" id="submitBtn">
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
$(document).ready(function () {

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
