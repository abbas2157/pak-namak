@extends('admin.layout.app')
@section('title', 'Spice Stock')

@php
$sizeLabel = fn ($gram) => $gram >= 1000 ? (($gram / 1000) . 'kg') : ($gram . 'g');

$totalKg = $levels->sum('quantity_kg');
$negativeCount = $levels->where('quantity', '<', 0)->count();
@endphp

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Spice Stock <small class="text-muted ch-sub">مصالحہ اسٹاک</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Spice Stock</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex flex-wrap justify-content-end">
                <button class="btn btn-primary btn-pn px-3 mb-1" id="addPackageBtn" style="background:#7a4fbf;border-color:#7a4fbf;">
                    <i class="fas fa-box mr-1"></i> Add Stock
                </button>
                <button class="btn btn-outline-secondary btn-pn px-3 ml-2 mb-1" id="adjustStockBtn">
                    <i class="fas fa-sliders-h mr-1"></i> Adjust
                </button>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        <div class="row mb-3">
            <div class="col-6 col-md-3 mb-2">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-purple">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="pn-stat-lbl mb-1">Total on hand / کل اسٹاک</div>
                                <div class="pn-stat-num-md pn-text-heading mb-0">{{ number_format($totalKg, 0) }}</div>
                                <div class="pn-stat-sub">KG, all spices &amp; packs</div>
                            </div>
                            <div class="pn-icon pn-icon-md pni-purple"><i class="fas fa-pepper-hot"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-red">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="pn-stat-lbl mb-1">Negative Stock / منفی اسٹاک</div>
                                <div class="pn-stat-num-md pn-text-heading mb-0">{{ $negativeCount }}</div>
                                <div class="pn-stat-sub">line{{ $negativeCount != 1 ? 's' : '' }} oversold</div>
                            </div>
                            <div class="pn-icon pn-icon-md pni-red"><i class="fas fa-triangle-exclamation"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Current levels — one card per spice type --}}
        <div class="row mb-3">
            @foreach($spiceTypes as $spiceType)
                @php
                    $lines = $levels->where('spice_type_id', $spiceType->id)->values();
                    $grams = $lines->pluck('size')->unique()->sort()->values();
                @endphp
                <div class="col-lg-6 mb-3">
                    <div class="card card-pn border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 font-weight-bold text-c-warn">
                                <i class="fas fa-pepper-hot mr-2 pni-purple"></i>{{ $spiceType->title }}
                            </h6>
                        </div>
                        <div class="card-body p-2">
                            <table class="table pn-table pn-table-font mb-0">
                                <thead>
                                    <tr>
                                        <th class="pl-3">Size</th>
                                        <th class="text-center">In Stock (Packets)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($grams as $gram)
                                        @php
                                            $line = $lines->first(fn ($l) => $l['size'] == $gram);
                                            $qty = $line['quantity'] ?? 0;
                                        @endphp
                                        <tr>
                                            <td class="pl-3 align-middle">{{ $sizeLabel($gram) }}</td>
                                            <td class="align-middle text-center {{ $qty < 0 ? 'text-danger font-weight-bold' : '' }}">{{ number_format($qty, 0) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Movement history --}}
        <div class="card card-pn border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 font-weight-bold text-c-blue2">
                    <i class="fas fa-history mr-2"></i>Movement History / تاریخ
                </h6>
            </div>
            <div class="card-body p-2">
                <div class="table-responsive">
                    <table class="table pn-table pn-table-font mb-0">
                        <thead>
                            <tr>
                                <th class="pl-3">Date</th>
                                <th>Spice</th>
                                <th>Size</th>
                                <th class="text-center">Qty</th>
                                <th>Reason</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($movements as $m)
                                <tr>
                                    <td class="pl-3 align-middle">{{ $m->created_at->format('d M Y, h:i A') }}</td>
                                    <td class="align-middle">{{ $m->spiceType->title ?? '—' }}</td>
                                    <td class="align-middle">{{ $sizeLabel($m->size) }} pack</td>
                                    <td class="align-middle text-center {{ $m->quantity < 0 ? 'text-danger' : 'text-success' }} font-weight-bold">
                                        {{ $m->quantity > 0 ? '+' : '' }}{{ number_format($m->quantity, 2) }}
                                    </td>
                                    <td class="align-middle text-capitalize">
                                        {{ $m->reason }}
                                        @if($m->reference_type && str_ends_with($m->reference_type, 'SpiceSale'))
                                            <a href="{{ route('admin.spice-sales.index') }}#row_{{ $m->reference_id }}" class="ml-1">#{{ $m->reference_id }}</a>
                                        @endif
                                    </td>
                                    <td class="align-middle">{{ $m->note }}</td>
                                </tr>
                            @empty
                                <tr class="empty-row">
                                    <td colspan="6">
                                        <i class="fas fa-history empty-icon"></i>
                                        <p class="empty-msg mb-0">No stock movements yet.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-3 pb-2">{{ $movements->links() }}</div>
            </div>
        </div>

    </div>
</section>

{{-- ===== ADD STOCK MODAL — one spice type, all grams / pack sizes at once ===== --}}
<div class="modal fade modal-pn" id="addPackageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form id="addPackageForm">
            @csrf
            <div class="modal-content border-0">
                <div class="modal-header border-0 text-white px-4 py-3" style="background:linear-gradient(135deg,#4a2f7a,#7a4fbf);">
                    <h5 class="modal-title"><i class="fas fa-box mr-2"></i>Add Spice Stock</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <div class="mb-3">
                        <label class="filter-lbl">Spice / مصالحہ <span class="text-danger">*</span></label>
                        <select name="spice_type_id" class="form-control fc-pn" required>
                            @foreach($spiceTypes as $spiceType)
                                <option value="{{ $spiceType->id }}">{{ $spiceType->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <p class="text-muted small mb-3">Fill in a quantity for whichever sizes you're adding — leave the rest blank.</p>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm pn-table-font mb-0">
                            <thead>
                                <tr><th>Size</th><th>Qty (Packets)</th></tr>
                            </thead>
                            <tbody>
                                @foreach(config('admin.spice_sizes') as $gram)
                                    <tr>
                                        <td class="align-middle">{{ $sizeLabel($gram) }}</td>
                                        <td><input type="number" step="1" min="0" name="package[{{ $gram }}]" class="form-control form-control-sm" placeholder="0"></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mb-0">
                        <label class="filter-lbl">Note / نوٹ</label>
                        <textarea name="note" class="form-control fc-pn" rows="2" placeholder="Optional"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 py-3">
                    <button type="button" class="btn btn-light btn-modal-cancel px-4" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary btn-modal-save px-4" type="submit" id="addSubmitBtn">
                        <i class="fas fa-save mr-1"></i> Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ===== ADJUST STOCK MODAL ===== --}}
<div class="modal fade modal-pn" id="adjustStockModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="adjustStockForm">
            @csrf
            <div class="modal-content border-0">
                <div class="modal-header border-0 text-white px-4 py-3">
                    <h5 class="modal-title"><i class="fas fa-sliders-h mr-2"></i>Adjust Spice Stock</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <div class="mb-3">
                        <label class="filter-lbl">Spice / مصالحہ <span class="text-danger">*</span></label>
                        <select name="spice_type_id" class="form-control fc-pn" required>
                            @foreach($spiceTypes as $spiceType)
                                <option value="{{ $spiceType->id }}">{{ $spiceType->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="filter-lbl">Packet Size <span class="text-danger">*</span></label>
                        <select name="size" class="form-control fc-pn" required>
                            @foreach(config('admin.spice_sizes') as $gram)
                                <option value="{{ $gram }}">{{ $sizeLabel($gram) }} pack</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="filter-lbl">Quantity (+ to add, − to remove) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="quantity" class="form-control fc-pn" placeholder="e.g. -5" required>
                    </div>
                    <div class="mb-0">
                        <label class="filter-lbl">Reason / وجہ <span class="text-danger">*</span></label>
                        <textarea name="note" class="form-control fc-pn" rows="2" placeholder="Required — e.g. damaged, recount" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 py-3">
                    <button type="button" class="btn btn-light btn-modal-cancel px-4" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary btn-modal-save px-4" type="submit" id="ajSubmitBtn">
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
    $('#addPackageBtn').on('click', function () {
        $('#addPackageForm')[0].reset();
        $('#addPackageModal').modal('show');
    });
    $('#adjustStockBtn').on('click', function () {
        $('#adjustStockForm')[0].reset();
        $('#adjustStockModal').modal('show');
    });

    $('#addPackageForm').on('submit', function (e) {
        e.preventDefault();
        const btn = $('#addSubmitBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');
        $.post("{{ route('admin.spice-stock.addition') }}", $(this).serialize())
            .done(function (res) {
                toastr.success('Stock added for ' + (res.lines || 0) + ' item(s)!');
                $('#addPackageModal').modal('hide');
                setTimeout(() => location.reload(), 800);
            })
            .fail(function (xhr) {
                if (xhr.status === 422) {
                    alert((xhr.responseJSON.message) || Object.values(xhr.responseJSON.errors || {}).map(e => e[0]).join("\n"));
                } else {
                    toastr.error('Something went wrong.');
                }
            })
            .always(() => btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save'));
    });

    $('#adjustStockForm').on('submit', function (e) {
        e.preventDefault();
        const btn = $('#ajSubmitBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');
        $.post("{{ route('admin.spice-stock.adjustment') }}", $(this).serialize())
            .done(function () {
                toastr.success('Stock adjusted!');
                $('#adjustStockModal').modal('hide');
                setTimeout(() => location.reload(), 800);
            })
            .fail(function (xhr) {
                if (xhr.status === 422) {
                    alert(Object.values(xhr.responseJSON.errors).map(e => e[0]).join("\n"));
                } else {
                    toastr.error('Something went wrong.');
                }
            })
            .always(() => btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save'));
    });
});
</script>
@endsection
