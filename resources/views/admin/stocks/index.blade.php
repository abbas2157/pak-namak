@extends('admin.layout.app')
@section('title', 'Stock')

@php
$typeMeta = [
    'dalla'   => ['label' => 'Dalla — ڈلہ', 'unit' => 'Mann', 'icon' => 'fa-tint', 'cls' => 'pni-blue'],
    'thaila'  => ['label' => 'Thaila — تھیلا', 'unit' => 'Bags', 'icon' => 'fa-shopping-bag', 'cls' => 'pni-teal'],
    'package' => ['label' => 'Package — پیکٹ', 'unit' => 'Bundles', 'icon' => 'fa-box', 'cls' => 'pni-purple'],
];
$sizeLabel = fn ($type, $size, $bundleSize = null) => match ($type) {
    'dalla'   => '—',
    'thaila'  => $size . ' KG bag',
    'package' => $size . 'g pack' . ($bundleSize ? ' (' . $bundleSize . '-pack)' : ''),
};

$dallaLine    = $levels->firstWhere('product_type', 'dalla');
$thailaLines  = $levels->where('product_type', 'thaila')->values();
$packageLines = $levels->where('product_type', 'package')->values();
$packageGrams = $packageLines->pluck('size')->unique()->values();

$totalDalla      = $dallaLine['quantity'] ?? 0;
$totalThailaBags = $thailaLines->sum('quantity');
$totalPackageKg  = $packageLines->sum('quantity_kg');
$negativeCount   = $levels->where('quantity', '<', 0)->count();
@endphp

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Stock <small class="text-muted ch-sub">اسٹاک</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Stock</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex flex-wrap justify-content-end">
                <button class="btn btn-primary btn-pn px-3 mb-1" id="addDallaBtn" style="background:#4e73df;border-color:#4e73df;">
                    <i class="fas fa-tint mr-1"></i> Add Dalla
                </button>
                <button class="btn btn-primary btn-pn px-3 ml-2 mb-1" id="addThailaBtn" style="background:#1a8a8a;border-color:#1a8a8a;">
                    <i class="fas fa-shopping-bag mr-1"></i> Add Thaila
                </button>
                <button class="btn btn-primary btn-pn px-3 ml-2 mb-1" id="addPackageBtn" style="background:#7a4fbf;border-color:#7a4fbf;">
                    <i class="fas fa-box mr-1"></i> Add Package
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

        {{-- Summary stat cards --}}
        <div class="row mb-3">
            <div class="col-6 col-md-3 mb-2">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-blue">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="pn-stat-lbl mb-1">Dalla on hand / ڈلہ</div>
                                <div class="pn-stat-num-md pn-text-heading mb-0">{{ number_format($totalDalla, 0) }}</div>
                                <div class="pn-stat-sub">Mann</div>
                            </div>
                            <div class="pn-icon pn-icon-md pni-blue"><i class="fas fa-tint"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-teal">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="pn-stat-lbl mb-1">Thaila on hand / تھیلا</div>
                                <div class="pn-stat-num-md pn-text-heading mb-0">{{ number_format($totalThailaBags, 0) }}</div>
                                <div class="pn-stat-sub">Bags, all sizes</div>
                            </div>
                            <div class="pn-icon pn-icon-md pni-teal"><i class="fas fa-shopping-bag"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-purple">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="pn-stat-lbl mb-1">Package on hand / پیکٹ</div>
                                <div class="pn-stat-num-md pn-text-heading mb-0">{{ number_format($totalPackageKg, 0) }}</div>
                                <div class="pn-stat-sub">KG, all packs</div>
                            </div>
                            <div class="pn-icon pn-icon-md pni-purple"><i class="fas fa-box"></i></div>
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

        {{-- Current levels — grouped by product --}}
        <div class="row mb-3">
            <div class="col-lg-4 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-c-blue2">
                            <i class="fas fa-tint mr-2 pni-blue"></i>Dalla — ڈلہ
                        </h6>
                    </div>
                    <div class="card-body p-2">
                        <table class="table pn-table pn-table-font mb-0">
                            <tbody>
                                <tr>
                                    <td class="pl-3 align-middle">Bulk — ڈلہ</td>
                                    <td class="align-middle text-right pr-3 {{ $totalDalla < 0 ? 'text-danger font-weight-bold' : '' }}">
                                        {{ number_format($totalDalla, 2) }} Mann
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-c-teal">
                            <i class="fas fa-shopping-bag mr-2 pni-teal"></i>Thaila — تھیلا
                        </h6>
                    </div>
                    <div class="card-body p-2">
                        <table class="table pn-table pn-table-font mb-0">
                            <tbody>
                                @foreach($thailaLines as $line)
                                    <tr>
                                        <td class="pl-3 align-middle">{{ $line['size'] }} KG bag</td>
                                        <td class="align-middle text-right pr-3 {{ $line['quantity'] < 0 ? 'text-danger font-weight-bold' : '' }}">
                                            {{ number_format($line['quantity'], 0) }} Bags
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-3">
                <div class="card card-pn border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-c-warn">
                            <i class="fas fa-box mr-2 pni-purple"></i>Package — پیکٹ
                        </h6>
                    </div>
                    <div class="card-body p-2">
                        <table class="table pn-table pn-table-font mb-0">
                            <thead>
                                <tr>
                                    <th class="pl-3">Size</th>
                                    <th class="text-center">10-Pack</th>
                                    <th class="text-center">20-Pack</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($packageGrams as $gram)
                                    @php
                                        $p10 = $packageLines->first(fn ($l) => $l['size'] == $gram && $l['bundle_size'] == 10);
                                        $p20 = $packageLines->first(fn ($l) => $l['size'] == $gram && $l['bundle_size'] == 20);
                                        $v10 = $p10['quantity'] ?? 0;
                                        $v20 = $p20['quantity'] ?? 0;
                                    @endphp
                                    <tr>
                                        <td class="pl-3 align-middle">{{ $gram }}g</td>
                                        <td class="align-middle text-center {{ $v10 < 0 ? 'text-danger font-weight-bold' : '' }}">{{ number_format($v10, 0) }}</td>
                                        <td class="align-middle text-center {{ $v20 < 0 ? 'text-danger font-weight-bold' : '' }}">{{ number_format($v20, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
                                <th>Product</th>
                                <th>Size</th>
                                <th class="text-center">Qty</th>
                                <th>Reason</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($movements as $m)
                                @php $tm = $typeMeta[$m->product_type]; @endphp
                                <tr>
                                    <td class="pl-3 align-middle">{{ $m->created_at->format('d M Y, h:i A') }}</td>
                                    <td class="align-middle">{{ $tm['label'] }}</td>
                                    <td class="align-middle">{{ $sizeLabel($m->product_type, $m->size, $m->bundle_size) }}</td>
                                    <td class="align-middle text-center {{ $m->quantity < 0 ? 'text-danger' : 'text-success' }} font-weight-bold">
                                        {{ $m->quantity > 0 ? '+' : '' }}{{ number_format($m->quantity, 2) }}
                                    </td>
                                    <td class="align-middle text-capitalize">
                                        {{ $m->reason }}
                                        @if($m->reference_type && str_ends_with($m->reference_type, 'Sale'))
                                            <a href="{{ route('admin.sales.index') }}#row_{{ $m->reference_id }}" class="ml-1">#{{ $m->reference_id }}</a>
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

{{-- ===== ADD DALLA STOCK MODAL ===== --}}
<div class="modal fade modal-pn" id="addDallaModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="stock-add-form" id="addDallaForm">
            @csrf
            <div class="modal-content border-0">
                <div class="modal-header border-0 text-white px-4 py-3" style="background:linear-gradient(135deg,#3a5fc9,#4e73df);">
                    <h5 class="modal-title"><i class="fas fa-tint mr-2"></i>Add Dalla Stock — ڈلہ</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <div class="mb-3">
                        <label class="filter-lbl">Quantity (Mann) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="dalla_qty" class="form-control fc-pn" placeholder="0" required>
                    </div>
                    <div class="mb-0">
                        <label class="filter-lbl">Note / نوٹ</label>
                        <textarea name="note" class="form-control fc-pn" rows="2" placeholder="Optional"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 py-3">
                    <button type="button" class="btn btn-light btn-modal-cancel px-4" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary btn-modal-save px-4" type="submit">
                        <i class="fas fa-save mr-1"></i> Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ===== ADD THAILA STOCK MODAL — all sizes at once ===== --}}
<div class="modal fade modal-pn" id="addThailaModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="stock-add-form" id="addThailaForm">
            @csrf
            <div class="modal-content border-0">
                <div class="modal-header border-0 text-white px-4 py-3" style="background:linear-gradient(135deg,#0d5c5c,#1a8a8a);">
                    <h5 class="modal-title"><i class="fas fa-shopping-bag mr-2"></i>Add Thaila Stock — تھیلا</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <p class="text-muted small mb-3">Fill in a quantity for whichever sizes you're adding — leave the rest blank.</p>
                    <div class="row mb-2">
                        @foreach(config('admin.thaila_sizes') as $size)
                            <div class="col-6 col-md-4 mb-2">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text">{{ $size }}KG</span></div>
                                    <input type="number" step="1" min="0" name="thaila[{{ $size }}]" class="form-control" placeholder="0">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mb-0">
                        <label class="filter-lbl">Note / نوٹ</label>
                        <textarea name="note" class="form-control fc-pn" rows="2" placeholder="Optional"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 py-3">
                    <button type="button" class="btn btn-light btn-modal-cancel px-4" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary btn-modal-save px-4" type="submit">
                        <i class="fas fa-save mr-1"></i> Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ===== ADD PACKAGE STOCK MODAL — all grams / pack sizes at once ===== --}}
<div class="modal fade modal-pn" id="addPackageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form class="stock-add-form" id="addPackageForm">
            @csrf
            <div class="modal-content border-0">
                <div class="modal-header border-0 text-white px-4 py-3" style="background:linear-gradient(135deg,#4a2f7a,#7a4fbf);">
                    <h5 class="modal-title"><i class="fas fa-box mr-2"></i>Add Package Stock — پیکٹ</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <p class="text-muted small mb-3">Fill in a quantity for whichever items you're adding — leave the rest blank.</p>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm pn-table-font mb-0">
                            <thead>
                                <tr><th>Size</th><th>10-Pack Qty</th><th>20-Pack Qty</th></tr>
                            </thead>
                            <tbody>
                                @foreach(config('admin.package_grams') as $gram)
                                    <tr>
                                        <td class="align-middle">{{ $gram }}g</td>
                                        <td><input type="number" step="1" min="0" name="package[{{ $gram }}][10]" class="form-control form-control-sm" placeholder="0"></td>
                                        <td><input type="number" step="1" min="0" name="package[{{ $gram }}][20]" class="form-control form-control-sm" placeholder="0"></td>
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
                    <button class="btn btn-primary btn-modal-save px-4" type="submit">
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
                    <h5 class="modal-title"><i class="fas fa-sliders-h mr-2"></i>Adjust Stock / اسٹاک درست کریں</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <div class="mb-3">
                        <label class="filter-lbl">Product / پروڈکٹ <span class="text-danger">*</span></label>
                        <select name="product_type" id="ajProductType" class="form-control fc-pn" required>
                            <option value="dalla">Dalla — ڈلہ</option>
                            <option value="thaila">Thaila — تھیلا</option>
                            <option value="package">Package — پیکٹ</option>
                        </select>
                    </div>
                    <div class="mb-3 d-none" id="ajSizeWrap">
                        <label class="filter-lbl" id="ajSizeLabel">Size <span class="text-danger">*</span></label>
                        <select name="size" id="ajSize" class="form-control fc-pn"></select>
                    </div>
                    <div class="mb-3 d-none" id="ajBundleWrap">
                        <label class="filter-lbl">Bundle Size / بنڈل سائز <span class="text-danger">*</span></label>
                        <select name="bundle_size" id="ajBundleSize" class="form-control fc-pn">
                            @foreach(config('admin.bundles') as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="filter-lbl">Quantity (+ to add, − to remove) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="quantity" id="ajQuantity" class="form-control fc-pn" placeholder="e.g. -5" required>
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
const THAILA_SIZES  = @json(config('admin.thaila_sizes'));
const PACKAGE_GRAMS = @json(config('admin.package_grams'));

function refreshSizeOptions(prefix) {
    const type       = $('#' + prefix + 'ProductType').val();
    const sizeWrap   = $('#' + prefix + 'SizeWrap');
    const sizeSel    = $('#' + prefix + 'Size');
    const sizeLabel  = $('#' + prefix + 'SizeLabel');
    const bundleWrap = $('#' + prefix + 'BundleWrap');

    sizeSel.empty();

    if (type === 'dalla') {
        sizeWrap.addClass('d-none');
        bundleWrap.addClass('d-none');
        return;
    }

    if (type === 'thaila') {
        sizeLabel.html('Bag Size (KG) <span class="text-danger">*</span>');
        THAILA_SIZES.forEach(s => sizeSel.append(new Option(s + ' KG bag', s)));
        bundleWrap.addClass('d-none');
    } else {
        sizeLabel.html('Packet Size (Gram) <span class="text-danger">*</span>');
        PACKAGE_GRAMS.forEach(g => sizeSel.append(new Option(g + 'g pack', g)));
        bundleWrap.removeClass('d-none');
    }
    sizeWrap.removeClass('d-none');
}

$(function () {
    $('#ajProductType').on('change', () => refreshSizeOptions('aj'));

    $('#addDallaBtn').on('click', function () {
        $('#addDallaForm')[0].reset();
        $('#addDallaModal').modal('show');
    });
    $('#addThailaBtn').on('click', function () {
        $('#addThailaForm')[0].reset();
        $('#addThailaModal').modal('show');
    });
    $('#addPackageBtn').on('click', function () {
        $('#addPackageForm')[0].reset();
        $('#addPackageModal').modal('show');
    });
    $('#adjustStockBtn').on('click', function () {
        $('#adjustStockForm')[0].reset();
        refreshSizeOptions('aj');
        $('#adjustStockModal').modal('show');
    });

    $('.stock-add-form').on('submit', function (e) {
        e.preventDefault();
        const form  = $(this);
        const modal = form.closest('.modal');
        const btn   = form.find('button[type="submit"]');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');
        $.post("{{ route('admin.stocks.addition') }}", form.serialize())
            .done(function (res) {
                toastr.success('Stock added for ' + (res.lines || 0) + ' item(s)!');
                modal.modal('hide');
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
        $.post("{{ route('admin.stocks.adjustment') }}", $(this).serialize())
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
