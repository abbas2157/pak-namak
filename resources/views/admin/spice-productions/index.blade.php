@extends('admin.layout.app')
@section('title', 'Spice Production')

@php
$sizeLabel = fn ($gram) => $gram >= 1000 ? (($gram / 1000) . 'kg') : ($gram . 'g');

// "250g × 40, 1kg × 10" breakdown for one batch
$breakdown = fn ($p) => $p->items
    ->map(fn ($i) => $i->label() . ' × ' . number_format($i->quantity, 0))
    ->implode(', ');
@endphp

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Spice Production <small class="text-muted ch-sub">مصالحہ پیداوار</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Spice Production</li>
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

        {{-- ── FILTER ─────────────────────────────────── --}}
        <div class="card card-pn border-0 shadow-sm mb-4">
            <div class="card-body py-3 px-4">
                <form method="GET" class="form-inline">
                    <label class="filter-lbl mr-2">From / سے</label>
                    <input type="date" name="from" class="form-control fc-pn mr-3 mb-2 mb-md-0" value="{{ $from->toDateString() }}">
                    <label class="filter-lbl mr-2">To / تک</label>
                    <input type="date" name="to" class="form-control fc-pn mr-3 mb-2 mb-md-0" value="{{ $to->toDateString() }}">
                    <label class="filter-lbl mr-2">Spice / مصالحہ</label>
                    <select name="spice_type_id" class="form-control fc-pn mr-3 mb-2 mb-md-0">
                        <option value="">All</option>
                        @foreach($spiceTypes as $st)
                            <option value="{{ $st->id }}" {{ (string) request('spice_type_id') === (string) $st->id ? 'selected' : '' }}>{{ $st->title }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-primary btn-pn px-4 mr-2"><i class="fas fa-filter mr-1"></i> Filter</button>
                    <a href="{{ route('admin.spice-productions.index') }}" class="btn btn-light btn-pn px-3">This Month</a>
                </form>
            </div>
        </div>

        {{-- ── STATS ──────────────────────────────────── --}}
        <div class="row mb-4">
            <div class="col-xl col-md-4 col-sm-6 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-blue">
                    <div class="card-body py-3 px-4">
                        <div class="pn-stat-lbl">Batches / بیچز</div>
                        <div class="pn-stat-num-md text-c-blue2">{{ $productions->count() }}</div>
                        <div class="pn-stat-sub">{{ $daily->pluck('date')->unique()->count() }} production days</div>
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100">
                    <div class="card-body py-3 px-4">
                        <div class="pn-stat-lbl">Raw Spice / خام مصالحہ</div>
                        <div class="pn-stat-num-md text-muted">{{ number_format($totalRaw, 0) }}</div>
                        <div class="pn-stat-sub">KG processed</div>
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-teal">
                    <div class="card-body py-3 px-4">
                        <div class="pn-stat-lbl">Finished / تیار</div>
                        <div class="pn-stat-num-md text-c-teal">{{ number_format($totalFinished, 0) }}</div>
                        <div class="pn-stat-sub">KG · {{ $efficiency }}% efficiency</div>
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-blue">
                    <div class="card-body py-3 px-4">
                        <div class="pn-stat-lbl">Packets / پیکٹ</div>
                        <div class="pn-stat-num-md text-c-blue2">{{ number_format($totalPackets, 0) }}</div>
                        <div class="pn-stat-sub">packets packed</div>
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100">
                    <div class="card-body py-3 px-4">
                        <div class="pn-stat-lbl">Packed / پیک شدہ</div>
                        <div class="pn-stat-num-md text-muted">{{ number_format($totalPackedKg, 0) }}</div>
                        <div class="pn-stat-sub">KG in packets</div>
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-yellow">
                    <div class="card-body py-3 px-4">
                        <div class="pn-stat-lbl">Cost / لاگت</div>
                        <div class="pn-stat-num-md text-c-warn">{{ number_format($totalCost, 0) }}</div>
                        <div class="pn-stat-sub">PKR fuel/electricity</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── DAILY SUMMARY ──────────────────────────── --}}
        <div class="card card-pn border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-3 pb-0 px-4">
                <h6 class="mb-0 font-weight-bold"><i class="fas fa-calendar-day mr-2 text-c-teal"></i>Daily Summary / روزانہ خلاصہ</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                <table class="table table-sm pn-table pn-table-font mb-0">
                    <thead>
                        <tr>
                            <th class="pl-3">Date / تاریخ</th>
                            <th>Spice / مصالحہ</th>
                            <th class="text-center">Batches</th>
                            <th class="text-right">Raw (KG)</th>
                            <th class="text-right">Finished (KG)</th>
                            <th class="text-right">Packets / پیکٹ</th>
                            <th class="text-right">Packed (KG)</th>
                            <th class="text-right pr-3">Cost (PKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($daily as $d)
                        <tr>
                            <td class="pl-3 font-weight-bold">{{ \Carbon\Carbon::parse($d['date'])->format('D, d M Y') }}</td>
                            <td>{{ $d['spice'] }}</td>
                            <td class="text-center">{{ $d['batches'] }}</td>
                            <td class="text-right">{{ number_format($d['raw'], 0) }}</td>
                            <td class="text-right text-c-teal">{{ number_format($d['finished'], 0) }}</td>
                            <td class="text-right font-weight-bold text-c-blue2">{{ number_format($d['packets'], 0) }}</td>
                            <td class="text-right">{{ number_format($d['packed_kg'], 1) }}</td>
                            <td class="text-right pr-3">{{ $d['cost'] ? number_format($d['cost'], 0) : '—' }}</td>
                        </tr>
                    @empty
                        <tr class="empty-row"><td colspan="8"><p class="empty-msg mb-0 py-2">No spice production in this period.</p></td></tr>
                    @endforelse
                    </tbody>
                    @if($daily->count() > 0)
                    <tfoot>
                        <tr class="pn-total-row font-weight-bold pn-table-font">
                            <td class="pl-3" colspan="2">Total / کل</td>
                            <td class="text-center">{{ $productions->count() }}</td>
                            <td class="text-right">{{ number_format($totalRaw, 0) }}</td>
                            <td class="text-right text-c-teal">{{ number_format($totalFinished, 0) }}</td>
                            <td class="text-right text-c-blue2">{{ number_format($totalPackets, 0) }}</td>
                            <td class="text-right">{{ number_format($totalPackedKg, 1) }}</td>
                            <td class="text-right pr-3">{{ number_format($totalCost, 0) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
                </div>
            </div>
        </div>

        {{-- ── BATCH TABLE ────────────────────────────── --}}
        <div class="card card-pn border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3 pb-0 px-4">
                <h6 class="mb-0 font-weight-bold"><i class="fas fa-pepper-hot mr-2 text-c-red"></i>Production Batches / پیداواری بیچز</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                <table class="table table-sm pn-table pn-table-font mb-0" id="productionsTable">
                    <thead>
                        <tr>
                            <th class="pl-3">Date / تاریخ</th>
                            <th>Spice / مصالحہ</th>
                            <th class="text-right">Raw (KG) / خام</th>
                            <th class="text-right">Finished (KG) / تیار</th>
                            <th class="text-right">Wastage / ضیاع</th>
                            <th>Packets / پیکٹ</th>
                            <th class="text-right">Packed (KG)</th>
                            <th>Machine / مشین</th>
                            <th class="text-right">Cost / لاگت</th>
                            <th class="text-center pr-3">Actions / اقدامات</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($productions as $p)
                        @php
                            $eff = $p->raw_spice_used > 0 ? round(($p->finished_spice / $p->raw_spice_used) * 100, 1) : 0;
                        @endphp
                        <tr id="prodRow{{ $p->id }}">
                            <td class="pl-3">
                                <span class="font-weight-bold pn-table-font d-block">{{ \Carbon\Carbon::parse($p->production_date)->format('d M Y') }}</span>
                                <span class="badge pn-bdg {{ $eff >= 90 ? 'badge-success' : ($eff >= 75 ? 'badge-warning' : 'badge-danger') }}">{{ $eff }}%</span>
                            </td>
                            <td class="font-weight-bold">{{ $p->spiceType?->title ?? '—' }}</td>
                            <td class="text-right pn-table-font">{{ number_format($p->raw_spice_used, 0) }}</td>
                            <td class="text-right font-weight-bold pn-table-font text-c-teal">{{ number_format($p->finished_spice, 0) }}</td>
                            <td class="text-right pn-table-font text-c-red">{{ number_format($p->wastage ?? 0, 0) }}</td>
                            <td>
                                <span class="font-weight-bold text-c-blue2">{{ number_format($p->packetCount(), 0) }}</span>
                                <small class="d-block text-muted">{{ $breakdown($p) ?: '—' }}</small>
                            </td>
                            <td class="text-right pn-table-font">{{ number_format($p->packedKg(), 1) }}</td>
                            <td class="text-muted pn-stat-sub">
                                {{ $p->machine_used ?: '—' }}
                                @if($p->remarks)
                                    <i class="fas fa-comment-dots ml-1 text-muted" title="{{ $p->remarks }}"></i>
                                @endif
                            </td>
                            <td class="text-right pn-table-font">{{ $p->electricity_fuel_cost ? number_format($p->electricity_fuel_cost, 0) : '—' }}</td>
                            <td class="text-center pr-3">
                                <button class="btn btn-sm btn-pn btn-act-edit editBtn" data-id="{{ $p->id }}" title="Edit"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-pn btn-act-delete deleteBtn" data-id="{{ $p->id }}" title="Delete"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-row">
                            <td colspan="10">
                                <i class="fas fa-pepper-hot empty-icon"></i>
                                <p class="empty-msg mb-0">No spice production records in this period.</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- ── MODAL (Add / Edit) ──────────────────────────────── --}}
<div class="modal fade modal-pn" id="productionModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <form id="productionForm">
            @csrf
            <input type="hidden" id="production_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-pepper-hot mr-2"></i>Add Spice Production / مصالحہ پیداوار
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="filter-lbl">Spice / مصالحہ <span class="text-danger">*</span></label>
                            <select name="spice_type_id" id="spice_type_id" class="form-control fc-pn" required>
                                @foreach($spiceTypes as $st)
                                    <option value="{{ $st->id }}">{{ $st->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="filter-lbl">Production Date / پیداواری تاریخ <span class="text-danger">*</span></label>
                            <input type="date" class="form-control fc-pn" name="production_date" id="production_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="filter-lbl">Machine Used / مشین</label>
                            <input type="text" class="form-control fc-pn" name="machine_used" id="machine_used" placeholder="e.g. Grinder #1">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="filter-lbl">Raw Spice (KG) / خام <span class="text-danger">*</span></label>
                            <input type="number" class="form-control fc-pn" name="raw_spice_used" id="raw_spice_used" min="0" step="0.01" placeholder="0" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="filter-lbl">Finished (KG) / تیار <span class="text-danger">*</span></label>
                            <input type="number" class="form-control fc-pn" name="finished_spice" id="finished_spice" min="0" step="0.01" placeholder="0" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="filter-lbl">Wastage (KG) / ضیاع</label>
                            <input type="number" class="form-control fc-pn" name="wastage" id="wastage" min="0" step="0.01" placeholder="0">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="filter-lbl">Efficiency / کارکردگی</label>
                            <div class="input-group">
                                <input type="text" class="form-control fc-ro-pn" id="efficiency_display" readonly placeholder="—">
                                <div class="input-group-append"><span class="input-group-text fc-ro-pn">%</span></div>
                            </div>
                        </div>
                    </div>

                    {{-- Packets produced per size --}}
                    <div class="border rounded p-3 mb-3">
                        <h6 class="font-weight-bold mb-1"><i class="fas fa-box mr-1 text-c-blue2"></i>Packets Packed / پیکٹ</h6>
                        <p class="text-muted small mb-2">How many packets of each size were filled — leave the rest blank.</p>
                        <div class="row">
                            @foreach(config('admin.spice_sizes') as $gram)
                                <div class="col-6 col-md-3 mb-2">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend"><span class="input-group-text">{{ $sizeLabel($gram) }}</span></div>
                                        <input type="number" step="1" min="0" name="package[{{ $gram }}]" class="form-control item-input" data-kg="{{ $gram / 1000 }}" placeholder="0">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="alert alert-light border mb-0 py-2 px-3 small">
                            <i class="fas fa-calculator mr-1"></i>
                            Packed total: <strong id="packed_kg_display">0</strong> KG
                            <span class="text-muted">(added to Spice Stock on save)</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="filter-lbl">Electricity / Fuel Cost (PKR) / بجلی/ایندھن لاگت</label>
                            <input type="number" class="form-control fc-pn" name="electricity_fuel_cost" id="electricity_fuel_cost" min="0" step="0.01" placeholder="0">
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
                        <div class="col-12 mb-0">
                            <label class="filter-lbl">Remarks / ملاحظات</label>
                            <textarea class="form-control fc-pn" name="remarks" id="remarks" rows="2" placeholder="Optional notes..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-modal-cancel" data-dismiss="modal" data-bs-dismiss="modal">Cancel / منسوخ</button>
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
    const BASE = "{{ route('admin.spice-productions.index') }}";

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
            columnDefs: [{ orderable: false, targets: [9] }, { responsivePriority: 1, targets: -1 }],
        });
    }

    function calcEfficiency() {
        const raw = parseFloat($('#raw_spice_used').val()) || 0;
        const fin = parseFloat($('#finished_spice').val()) || 0;
        $('#efficiency_display').val(raw > 0 ? (fin / raw * 100).toFixed(1) : '');
    }
    $('#raw_spice_used, #finished_spice').on('input', calcEfficiency);

    function calcPacked() {
        let kg = 0;
        $('.item-input').each(function () {
            kg += (parseFloat($(this).val()) || 0) * parseFloat($(this).data('kg'));
        });
        $('#packed_kg_display').text(kg.toLocaleString(undefined, { maximumFractionDigits: 2 }));
    }
    $(document).on('input', '.item-input', calcPacked);

    function resetForm() {
        $('#productionForm')[0].reset();
        $('#production_id').val('');
        $('#production_date').val(new Date().toISOString().split('T')[0]);
        $('#efficiency_display').val('');
        calcPacked();
    }

    $('#addBtn').on('click', function () {
        resetForm();
        $('#modalTitle').html('<i class="fas fa-pepper-hot mr-2"></i>Add Spice Production');
        $('#submitBtn').html('<i class="fas fa-save mr-1"></i> Save');
        $('#productionModal').modal('show');
    });

    $('#productionForm').on('submit', function (e) {
        e.preventDefault();
        const id  = $('#production_id').val();
        const url = id ? (BASE + '/' + id) : BASE;

        const btn = $('#submitBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving…');

        $.ajax({
            url, type: 'POST',
            data: $(this).serialize() + (id ? '&_method=PUT' : ''),
            success: function () {
                toastr.success('Spice production saved!');
                $('#productionModal').modal('hide');
                setTimeout(() => location.reload(), 600);
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    alert(Object.values(xhr.responseJSON.errors).map(e => e[0]).join('\n'));
                } else {
                    toastr.error(xhr.responseJSON?.message || 'Something went wrong.');
                }
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save');
            }
        });
    });

    $(document).on('click', '.editBtn', function () {
        const id = $(this).data('id');
        $.get(BASE + '/' + id + '/edit', function (p) {
            resetForm();
            $('#production_id').val(p.id);
            $('#spice_type_id').val(p.spice_type_id);
            $('#production_date').val(p.production_date);
            $('#raw_spice_used').val(p.raw_spice_used);
            $('#finished_spice').val(p.finished_spice);
            $('#wastage').val(p.wastage);
            $('#machine_used').val(p.machine_used);
            $('#electricity_fuel_cost').val(p.electricity_fuel_cost);
            $('#account_id').val(p.account_id ?? '');
            $('#remarks').val(p.remarks);
            (p.items || []).forEach(function (it) {
                $('[name="package[' + it.size + ']"]').val(parseFloat(it.quantity));
            });
            calcEfficiency();
            calcPacked();
            $('#modalTitle').html('<i class="fas fa-edit mr-2"></i>Edit Spice Production');
            $('#submitBtn').html('<i class="fas fa-save mr-1"></i> Update');
            $('#productionModal').modal('show');
        });
    });

    $(document).on('click', '.deleteBtn', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Delete this spice production record?',
            text: 'Its packets will be removed from Spice Stock too. This cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then(result => {
            if (!result.isConfirmed) return;
            $.ajax({
                url: BASE + '/' + id,
                type: 'POST',
                data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                success: function () {
                    toastr.success('Spice production deleted.');
                    setTimeout(() => location.reload(), 600);
                },
                error: function () { toastr.error('Delete failed.'); }
            });
        });
    });
});
</script>
@endsection
