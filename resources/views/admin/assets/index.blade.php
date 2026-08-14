@extends('admin.layout.app')
@section('title', 'Assets')

@php
$categories = ['Vehicle', 'Machinery', 'Equipment', 'Furniture & Electronics', 'Other'];

$catMeta = [
    'Vehicle'                 => ['color' => '#4e73df', 'icon' => 'fa-truck',  'cls' => 'pni-blue'],
    'Machinery'               => ['color' => '#e74a3b', 'icon' => 'fa-cogs',   'cls' => 'pni-red'],
    'Equipment'               => ['color' => '#1cc88a', 'icon' => 'fa-tools',  'cls' => 'pni-teal'],
    'Furniture & Electronics' => ['color' => '#6f42c1', 'icon' => 'fa-chair',  'cls' => 'pni-purple'],
    'Other'                   => ['color' => '#858796', 'icon' => 'fa-box',    'cls' => ''],
];

$statusMeta = [
    'active'       => ['label' => 'Active',       'bg' => '#1cc88a', 'badge' => 'badge-success'],
    'under_repair' => ['label' => 'Under Repair',  'bg' => '#f6c23e', 'badge' => 'badge-warning'],
    'disposed'     => ['label' => 'Disposed',      'bg' => '#858796', 'badge' => 'badge-secondary'],
];

$condMeta = [
    'good' => ['label' => 'Good', 'cls' => 'text-c-teal'],
    'fair' => ['label' => 'Fair', 'cls' => 'text-c-warn'],
    'poor' => ['label' => 'Poor', 'cls' => 'text-c-red'],
];
@endphp

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Assets <small class="text-muted ch-sub">اثاثے</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Assets</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end">
                <button class="btn btn-primary btn-pn px-4" id="addBtn">
                    <i class="fas fa-plus mr-1"></i> Add Asset / اثاثہ شامل کریں
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
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-blue">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="pn-stat-lbl mb-1">Total Assets / کل اثاثے</div>
                                <div class="pn-stat-num-md pn-text-heading mb-0">{{ $totalCount }}</div>
                                <div class="pn-stat-sub">items registered</div>
                            </div>
                            <div class="pn-icon pn-icon-md pni-blue">
                                <i class="fas fa-layer-group"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-red">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="pn-stat-lbl mb-1">Total Value / کل مالیت</div>
                                <div class="pn-stat-num-md pn-text-heading mb-0">{{ number_format($totalValue, 0) }}</div>
                                <div class="pn-stat-sub">PKR (purchase cost)</div>
                            </div>
                            <div class="pn-icon pn-icon-md pni-red">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-teal">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="pn-stat-lbl mb-1">Active / فعال</div>
                                <div class="pn-stat-num-md pn-text-heading mb-0">{{ $activeCount }}</div>
                                <div class="pn-stat-sub">in service</div>
                            </div>
                            <div class="pn-icon pn-icon-md pni-teal">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card card-pn border-0 shadow-sm h-100 pn-bl-yellow">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="pn-stat-lbl mb-1">Under Repair / مرمت میں</div>
                                <div class="pn-stat-num-md pn-text-heading mb-0">{{ $repairCount }}</div>
                                <div class="pn-stat-sub">need attention</div>
                            </div>
                            <div class="pn-icon pn-icon-md pni-yellow">
                                <i class="fas fa-wrench"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table + Sidebar --}}
        <div class="row">

            {{-- Table --}}
            <div class="col-lg-8 mb-3">
                <div class="card card-pn border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-c-blue2">
                            <i class="fas fa-table mr-2"></i>Asset Register / اثاثوں کی فہرست
                        </h6>
                    </div>

                    {{-- Category filter tabs --}}
                    <div class="px-3 pt-3 pb-0">
                        <button class="btn btn-sm btn-secondary filter-btn active mr-1 mb-2" data-filter="all">All</button>
                        @foreach($categories as $cat)
                            @php $cm = $catMeta[$cat] ?? ['color'=>'#858796','icon'=>'fa-box','cls'=>'']; @endphp
                            <button class="btn btn-sm filter-btn mr-1 mb-2 btn-pn pn-bdg"
                                    data-filter="{{ $cat }}"
                                    data-color="{{ $cm['color'] }}">
                                <i class="fas {{ $cm['icon'] }} mr-1"></i>{{ $cat }}
                            </button>
                        @endforeach
                    </div>

                    <div class="card-body p-2">
                            <table class="table pn-table pn-table-font mb-0" id="assetsTable">
                                <thead>
                                    <tr>
                                        <th class="pl-3">Asset / اثاثہ</th>
                                        <th>Category / زمرہ</th>
                                        <th class="text-center">Qty / تعداد</th>
                                        <th class="text-right">Value (PKR) / مالیت</th>
                                        <th>Status / حیثیت</th>
                                        <th class="text-center">Actions / اقدامات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($assets as $i => $asset)
                                        @php
                                            $cm  = $catMeta[$asset->category]   ?? ['color'=>'#858796','icon'=>'fa-box','cls'=>''];
                                            $sm  = $statusMeta[$asset->status]  ?? ['label'=>$asset->status,'bg'=>'#858796','badge'=>'badge-secondary'];
                                            $cdm = $condMeta[$asset->condition] ?? ['label'=>$asset->condition,'cls'=>'text-muted'];
                                            $val = $asset->quantity * $asset->purchase_price;
                                        @endphp
                                        <tr class="asset-row" data-cat="{{ $asset->category }}"
                                            id="row_{{ $asset->id }}">
                                            <td class="pl-3 align-middle">
                                                <span class="font-weight-bold d-block pn-text-heading">
                                                    {{ $asset->asset_name }}
                                                    @if($asset->is_investment)
                                                        <span class="badge badge-warning ml-1"><i class="fas fa-piggy-bank mr-1"></i>Investment</span>
                                                    @endif
                                                </span>
                                                @if($asset->location)
                                                    <small class="text-muted"><i class="fas fa-map-marker-alt mr-1"></i>{{ $asset->location }}</small>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                <span class="pn-bdg pn-bdg-blue d-inline-flex align-items-center"
                                                      data-cat-badge="{{ $asset->category }}">
                                                    <i class="fas {{ $cm['icon'] }} mr-1"></i>{{ $asset->category }}
                                                </span>
                                            </td>
                                            <td class="align-middle text-center">{{ $asset->quantity }}</td>
                                            <td class="align-middle text-right">
                                                <span class="font-weight-bold pn-text-heading">{{ number_format($val, 0) }}</span>
                                                <br><small class="text-muted">@ {{ number_format($asset->purchase_price, 0) }} each</small>
                                            </td>
                                            <td class="align-middle">
                                                <span class="badge {{ $sm['badge'] }} d-block mb-1 pn-label">
                                                    {{ $sm['label'] }}
                                                </span>
                                                <small class="{{ $cdm['cls'] }} font-weight-bold">
                                                    <i class="fas fa-circle mr-1 icon-9"></i>{{ $cdm['label'] }}
                                                </small>
                                            </td>
                                            <td class="align-middle text-center">
                                                <button class="btn btn-sm btn-pn btn-act-edit editBtn mr-1"
                                                        data-id="{{ $asset->id }}"
                                                        title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                @if($asset->image)
                                                <a href="{{ asset($asset->image) }}" target="_blank"
                                                   class="btn btn-sm btn-pn btn-act-bill mr-1"
                                                   title="View Image">
                                                    <i class="fas fa-image"></i>
                                                </a>
                                                @endif
                                                <button class="btn btn-sm btn-pn btn-act-delete deleteBtn"
                                                        data-id="{{ $asset->id }}"
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="empty-row">
                                            <td colspan="6">
                                                <i class="fas fa-layer-group empty-icon"></i>
                                                <p class="empty-msg mb-0">No assets registered yet.</p>
                                                <button class="btn btn-sm btn-primary mt-3" id="addBtnEmpty">
                                                    <i class="fas fa-plus mr-1"></i> Add First Asset
                                                </button>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($assets->count() > 0)
                                <tfoot>
                                    <tr class="pn-total-row">
                                        <td class="pl-3 py-3 font-weight-bold pn-text-heading" colspan="3">Total Book Value / کل کتابی مالیت</td>
                                        <td class="py-3 text-right font-weight-bold text-c-red pn-stat-num-sm">
                                            {{ number_format($totalValue, 0) }}
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">

                {{-- Status breakdown --}}
                <div class="card card-pn border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-c-blue2">
                            <i class="fas fa-circle-notch mr-2"></i>Status Overview / حیثیت کا جائزہ
                        </h6>
                    </div>
                    <div class="card-body py-3 px-3">
                        @foreach($statusMeta as $key => $sm)
                            @php $cnt = $assets->where('status', $key)->count(); @endphp
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="pn-table-font">
                                    <span class="badge {{ $sm['badge'] }} mr-2">{{ $sm['label'] }}</span>
                                </span>
                                <span class="font-weight-bold pn-text-heading">{{ $cnt }} item{{ $cnt != 1 ? 's' : '' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Category value breakdown --}}
                @if($categoryTotals->count())
                <div class="card card-pn border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-c-red">
                            <i class="fas fa-chart-pie mr-2"></i>Value by Category / زمرہ وار مالیت
                        </h6>
                    </div>
                    <div class="card-body py-3 px-3">
                        @foreach($categoryTotals as $cat => $val)
                            @php
                                $pct = $totalValue > 0 ? round(($val / $totalValue) * 100) : 0;
                                $cm  = $catMeta[$cat] ?? ['color'=>'#858796','icon'=>'fa-box','cls'=>''];
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="pn-table-font">
                                        <i class="fas {{ $cm['icon'] }} mr-1 {{ $cm['cls'] }}"></i>
                                        <strong>{{ $cat }}</strong>
                                    </span>
                                    <span>
                                        <span class="pn-table-font font-weight-bold">{{ number_format($val, 0) }}</span>
                                        <span class="text-muted ml-1">{{ $pct }}%</span>
                                    </span>
                                </div>
                                <div class="progress progress-sm">
                                    <div class="pbar pbar-dyn" style="--w:{{ $pct }}%;--clr:{{ $cm['color'] }}"></div>
                                </div>
                            </div>
                        @endforeach
                        <div class="d-flex justify-content-between align-items-center pt-2 mt-1 border-top">
                            <span class="font-weight-bold pn-text-heading">Total Book Value / کل کتابی مالیت</span>
                            <span class="font-weight-bold text-c-red pn-stat-num-sm">
                                PKR {{ number_format($totalValue, 0) }}
                            </span>
                        </div>
                    </div>
                </div>
                @endif

            </div>

        </div>
    </div>
</section>

{{-- ===== MODAL ===== --}}
<div class="modal fade modal-pn" id="assetModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="assetForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="asset_id" name="_asset_id">
            <div class="modal-content border-0">

                <div class="modal-header border-0 text-white px-4 py-3">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-layer-group mr-2"></i>Add Asset / اثاثہ شامل کریں
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body px-4 py-4">
                    <div class="row">
                        {{-- Row 1: Name + Category --}}
                        <div class="col-md-6 mb-3">
                            <label class="filter-lbl">
                                Asset Name / اثاثے کا نام <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="asset_name" id="aName" class="form-control fc-pn"
                                   placeholder="e.g. Truck, Crusher, Scale" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="filter-lbl">
                                Category / زمرہ <span class="text-danger">*</span>
                            </label>
                            <select name="category" id="aCategory" class="form-control fc-pn" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Row 2: Qty + Price --}}
                        <div class="col-md-6 mb-3">
                            <label class="filter-lbl">
                                Quantity / تعداد <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="quantity" id="aQty" class="form-control fc-pn"
                                   min="1" value="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="filter-lbl">
                                Purchase Price (PKR each) / خریداری قیمت <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text input-pre">PKR</span>
                                </div>
                                <input type="number" step="0.01" name="purchase_price" id="aPrice"
                                       class="form-control fc-lg-pn" min="0" placeholder="0" required>
                            </div>
                        </div>

                        {{-- Row 3: Date + Location --}}
                        <div class="col-md-6 mb-3">
                            <label class="filter-lbl">
                                Purchase Date / خریداری تاریخ <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="purchase_date" id="aDate" class="form-control fc-pn" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="filter-lbl">
                                Location / مقام
                            </label>
                            <input type="text" name="location" id="aLocation" class="form-control fc-pn"
                                   placeholder="e.g. Factory, Office, Warehouse">
                        </div>

                        {{-- Row 3b: Paid From + Investment --}}
                        <div class="col-md-6 mb-3">
                            <label class="filter-lbl">
                                Paid From / کہاں سے ادا کیا <span class="text-danger">*</span>
                            </label>
                            <select name="account_id" id="aAccount" class="form-control fc-pn" required>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ $account->type === 'cash' ? 'selected' : '' }}>{{ $account->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="is_investment" id="aInvestment" value="1">
                                <label class="custom-control-label" for="aInvestment">
                                    Mark as Investment / سرمایہ کاری کے طور پر نشان زد کریں
                                </label>
                            </div>
                        </div>

                        {{-- Row 4: Status + Condition --}}
                        <div class="col-md-6 mb-3">
                            <label class="filter-lbl">
                                Status / حیثیت <span class="text-danger">*</span>
                            </label>
                            <select name="status" id="aStatus" class="form-control fc-pn" required>
                                <option value="active">Active</option>
                                <option value="under_repair">Under Repair</option>
                                <option value="disposed">Disposed</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="filter-lbl">
                                Condition / حالت <span class="text-danger">*</span>
                            </label>
                            <select name="condition" id="aCondition" class="form-control fc-pn" required>
                                <option value="good">Good</option>
                                <option value="fair">Fair</option>
                                <option value="poor">Poor</option>
                            </select>
                        </div>

                        {{-- Description --}}
                        <div class="col-md-6 mb-3">
                            <label class="filter-lbl">
                                Description / Notes / تفصیل
                            </label>
                            <textarea name="description" id="aDesc" class="form-control fc-pn" rows="3"
                                      placeholder="Model number, serial number, notes..."></textarea>
                        </div>

                        {{-- Image --}}
                        <div class="col-md-6 mb-3">
                            <label class="filter-lbl">
                                Asset Image / اثاثے کی تصویر
                            </label>
                            {{-- Current image preview (shown only in edit mode) --}}
                            <div id="currentImageWrap" class="mb-2 d-none">
                                <a id="currentImageLink" href="#" target="_blank"
                                   class="btn btn-sm btn-outline-secondary btn-pn">
                                    <i class="fas fa-image mr-1"></i> View Current Image
                                </a>
                            </div>
                            <input type="file" name="image" id="aImage" class="form-control fc-pn"
                                   accept="image/jpg,image/jpeg,image/png,image/webp">
                            <small class="text-muted" id="imageHint">JPG, PNG or WebP, max 4 MB.</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 px-4 py-3">
                    <button type="button" class="btn btn-light btn-modal-cancel px-4"
                            data-dismiss="modal" data-bs-dismiss="modal">
                        Cancel / منسوخ
                    </button>
                    <button class="btn btn-primary btn-modal-save px-4" type="submit" id="submitBtn">
                        <i class="fas fa-save mr-1"></i> Save Asset / محفوظ کریں
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
const CAT_META = {
    'Vehicle':                 { color:'#4e73df', icon:'fa-truck' },
    'Machinery':               { color:'#e74a3b', icon:'fa-cogs' },
    'Equipment':               { color:'#1cc88a', icon:'fa-tools' },
    'Furniture & Electronics': { color:'#6f42c1', icon:'fa-chair' },
    'Other':                   { color:'#858796', icon:'fa-box' },
};
const STATUS_META = {
    'active':       { label:'Active',       badge:'badge-success' },
    'under_repair': { label:'Under Repair',  badge:'badge-warning' },
    'disposed':     { label:'Disposed',      badge:'badge-secondary' },
};
const COND_META = {
    'good': { label:'Good', color:'#1cc88a' },
    'fair': { label:'Fair', color:'#f6c23e' },
    'poor': { label:'Poor', color:'#e74a3b' },
};

// Apply dynamic category badge colours via JS (avoids inline style on filter buttons)
document.querySelectorAll('.filter-btn[data-color]').forEach(function(btn) {
    const c = btn.dataset.color;
    btn.style.background = c + '18';
    btn.style.color = c;
    btn.style.borderColor = c + '44';
});

$(function () {

    try {
        $('#assetsTable').DataTable({
            paging: true,
            pageLength: 15,
            lengthChange: false,
            searching: true,
            ordering: false,
            info: true,
            autoWidth: false,
            responsive: true,
            columnDefs: [{ orderable: false, targets: [5] }, { responsivePriority: 1, targets: -1 }],
            language: {
                search: '',
                searchPlaceholder: 'Search assets...',
                info: 'Showing _START_–_END_ of _TOTAL_',
                paginate: { previous: '‹', next: '›' }
            }
        });
    } catch (e) {
        console.warn('DataTables init error:', e);
    }

    // Category filter
    $('.filter-btn').on('click', function () {
        $('.filter-btn').removeClass('active').css({ 'opacity': '0.65' });
        $(this).addClass('active').css({ 'opacity': '1' });
        const filter = $(this).data('filter');
        if (filter === 'all') {
            $('.asset-row').show();
        } else {
            $('.asset-row').hide();
            $('.asset-row[data-cat="' + filter + '"]').show();
        }
    });

    // Open add modal
    $('#addBtn, #addBtnEmpty').on('click', function () {
        $('#assetForm')[0].reset();
        $('#asset_id').val('');
        $('#aDate').val(new Date().toISOString().split('T')[0]);
        $('#aQty').val(1);
        $('#currentImageWrap').addClass('d-none');
        $('#imageHint').text('JPG, PNG or WebP, max 4 MB.');
        $('#modalTitle').html('<i class="fas fa-layer-group mr-2"></i>Add Asset');
        $('#submitBtn').html('<i class="fas fa-save mr-1"></i> Save Asset');
        $('#assetModal').modal('show');
    });

    // Submit (add + edit) — uses FormData to support file upload
    $('#assetForm').on('submit', function (e) {
        e.preventDefault();
        const id  = $('#asset_id').val();
        const url = id ? (APP_URL + '/assets/' + id) : "{{ route('admin.assets.store') }}";
        const btn = $('#submitBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        const fd = new FormData(this);
        if (id) fd.append('_method', 'PUT');

        $.ajax({
            url, type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function (res) {
                toastr.success('Asset saved!');
                $('#assetModal').modal('hide');
                setTimeout(() => location.reload(), 800);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    alert(Object.values(xhr.responseJSON.errors).map(e => e[0]).join("\n"));
                } else {
                    toastr.error('Something went wrong.');
                }
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Asset');
            }
        });
    });

    // Edit
    $(document).on('click', '.editBtn', function () {
        const id = $(this).data('id');
        $.get(APP_URL + '/assets/' + id + '/edit', function (a) {
            $('#assetForm')[0].reset();
            $('#asset_id').val(a.id);
            $('#aName').val(a.asset_name);
            $('#aCategory').val(a.category);
            $('#aQty').val(a.quantity);
            $('#aPrice').val(a.purchase_price);
            $('#aDate').val(a.purchase_date);
            $('#aLocation').val(a.location);
            $('#aAccount').val(a.account_id);
            $('#aInvestment').prop('checked', !!a.is_investment);
            $('#aStatus').val(a.status);
            $('#aCondition').val(a.condition);
            $('#aDesc').val(a.description);

            // Show current image link if available
            if (a.image) {
                $('#currentImageLink').attr('href', ASSET_URL + a.image);
                $('#currentImageWrap').removeClass('d-none');
                $('#imageHint').text('Upload a new image to replace the current one.');
            } else {
                $('#currentImageWrap').addClass('d-none');
                $('#imageHint').text('JPG, PNG or WebP, max 4 MB.');
            }

            $('#modalTitle').html('<i class="fas fa-edit mr-2"></i>Edit Asset');
            $('#submitBtn').html('<i class="fas fa-save mr-1"></i> Update Asset');
            $('#assetModal').modal('show');
        });
    });

    // Delete
    $(document).on('click', '.deleteBtn', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Delete this asset?',
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
                url: APP_URL + '/assets/' + id,
                type: 'POST',
                data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                success: function () {
                    $('#row_' + id).fadeOut(300, function () { $(this).remove(); });
                    toastr.success('Asset deleted.');
                },
                error: function (xhr) { toastr.error('Error: ' + xhr.status); }
            });
        });
    });

});
</script>
@endsection
