@extends('admin.layout.app')
@section('title', 'Assets')

@php
$categories = ['Vehicle', 'Machinery', 'Equipment', 'Furniture & Electronics', 'Other'];

$catMeta = [
    'Vehicle'                 => ['color' => '#4e73df', 'icon' => 'fa-truck'],
    'Machinery'               => ['color' => '#e74a3b', 'icon' => 'fa-cogs'],
    'Equipment'               => ['color' => '#1cc88a', 'icon' => 'fa-tools'],
    'Furniture & Electronics' => ['color' => '#6f42c1', 'icon' => 'fa-chair'],
    'Other'                   => ['color' => '#858796', 'icon' => 'fa-box'],
];

$statusMeta = [
    'active'       => ['label' => 'Active',       'bg' => '#1cc88a', 'badge' => 'badge-success'],
    'under_repair' => ['label' => 'Under Repair',  'bg' => '#f6c23e', 'badge' => 'badge-warning'],
    'disposed'     => ['label' => 'Disposed',      'bg' => '#858796', 'badge' => 'badge-secondary'],
];

$condMeta = [
    'good' => ['label' => 'Good', 'color' => '#1cc88a'],
    'fair' => ['label' => 'Fair', 'color' => '#f6c23e'],
    'poor' => ['label' => 'Poor', 'color' => '#e74a3b'],
];
@endphp

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Assets</h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Assets</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end">
                <button class="btn btn-primary px-4" style="border-radius:8px;" id="addBtn">
                    <i class="fas fa-plus mr-1"></i> Add Asset
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
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Assets</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ $totalCount }}</div>
                                <small class="text-muted">items registered</small>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:46px;height:46px;background:rgba(78,115,223,.12);">
                                <i class="fas fa-layer-group" style="color:#4e73df;font-size:18px;"></i>
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
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Value</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($totalValue, 0) }}</div>
                                <small class="text-muted">PKR (purchase cost)</small>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:46px;height:46px;background:rgba(231,74,59,.12);">
                                <i class="fas fa-money-bill-wave" style="color:#e74a3b;font-size:18px;"></i>
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
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Active</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ $activeCount }}</div>
                                <small class="text-muted">in service</small>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:46px;height:46px;background:rgba(28,200,138,.12);">
                                <i class="fas fa-check-circle" style="color:#1cc88a;font-size:18px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #f6c23e !important;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Under Repair</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ $repairCount }}</div>
                                <small class="text-muted">need attention</small>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:46px;height:46px;background:rgba(246,194,62,.12);">
                                <i class="fas fa-wrench" style="color:#f6c23e;font-size:18px;"></i>
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
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold" style="color:#4e73df;">
                            <i class="fas fa-table mr-2"></i>Asset Register
                        </h6>
                    </div>

                    {{-- Category filter tabs --}}
                    <div class="px-3 pt-3 pb-0">
                        <button class="btn btn-sm btn-secondary filter-btn active mr-1 mb-2" data-filter="all">All</button>
                        @foreach($categories as $cat)
                            @php $cm = $catMeta[$cat] ?? ['color'=>'#858796','icon'=>'fa-box']; @endphp
                            <button class="btn btn-sm filter-btn mr-1 mb-2"
                                    data-filter="{{ $cat }}"
                                    style="background:{{ $cm['color'] }}18;color:{{ $cm['color'] }};border:1px solid {{ $cm['color'] }}44;">
                                <i class="fas {{ $cm['icon'] }} mr-1"></i>{{ $cat }}
                            </button>
                        @endforeach
                    </div>

                    <div class="card-body p-2">
                        <div class="table-responsive">
                            <table class="table mb-0" id="assetsTable" style="font-size:13.5px;">
                                <thead>
                                    <tr style="background:#f8f9fc;border-bottom:2px solid #e3e6f0;">
                                        <th class="pl-3 py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Asset</th>
                                        <th class="py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Category</th>
                                        <th class="py-3 text-center text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Qty</th>
                                        <th class="py-3 text-right text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Value (PKR)</th>
                                        <th class="py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Status</th>
                                        <th class="py-3 text-center text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($assets as $i => $asset)
                                        @php
                                            $cm  = $catMeta[$asset->category]   ?? ['color'=>'#858796','icon'=>'fa-box'];
                                            $sm  = $statusMeta[$asset->status]  ?? ['label'=>$asset->status,'bg'=>'#858796','badge'=>'badge-secondary'];
                                            $cdm = $condMeta[$asset->condition] ?? ['label'=>$asset->condition,'color'=>'#858796'];
                                            $val = $asset->quantity * $asset->purchase_price;
                                        @endphp
                                        <tr class="asset-row" data-cat="{{ $asset->category }}"
                                            id="row_{{ $asset->id }}" style="border-bottom:1px solid #f0f0f0;">
                                            <td class="pl-3 py-3 align-middle">
                                                <span class="font-weight-bold d-block" style="color:#2d3748;">{{ $asset->asset_name }}</span>
                                                @if($asset->location)
                                                    <small class="text-muted"><i class="fas fa-map-marker-alt mr-1"></i>{{ $asset->location }}</small>
                                                @endif
                                            </td>
                                            <td class="py-3 align-middle">
                                                <span class="d-inline-flex align-items-center px-2 py-1 rounded-pill"
                                                      style="background:{{ $cm['color'] }}18;color:{{ $cm['color'] }};font-size:12px;font-weight:600;white-space:nowrap;">
                                                    <i class="fas {{ $cm['icon'] }} mr-1"></i>{{ $asset->category }}
                                                </span>
                                            </td>
                                            <td class="py-3 align-middle text-center">{{ $asset->quantity }}</td>
                                            <td class="py-3 align-middle text-right">
                                                <span class="font-weight-bold" style="color:#2d3748;">{{ number_format($val, 0) }}</span>
                                                <br><small class="text-muted">@ {{ number_format($asset->purchase_price, 0) }} each</small>
                                            </td>
                                            <td class="py-3 align-middle">
                                                <span class="badge {{ $sm['badge'] }} d-block mb-1" style="font-size:11px;">
                                                    {{ $sm['label'] }}
                                                </span>
                                                <small style="color:{{ $cdm['color'] }};font-weight:600;">
                                                    <i class="fas fa-circle mr-1" style="font-size:8px;"></i>{{ $cdm['label'] }}
                                                </small>
                                            </td>
                                            <td class="py-3 align-middle text-center">
                                                <button class="btn btn-sm editBtn mr-1"
                                                        data-id="{{ $asset->id }}"
                                                        style="background:#fff3cd;color:#856404;border:1px solid #ffc107;border-radius:6px;"
                                                        title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                @if($asset->image)
                                                <a href="{{ asset($asset->image) }}" target="_blank"
                                                   class="btn btn-sm mr-1"
                                                   style="background:#d4edda;color:#155724;border:1px solid #c3e6cb;border-radius:6px;"
                                                   title="View Image">
                                                    <i class="fas fa-image"></i>
                                                </a>
                                                @endif
                                                <button class="btn btn-sm deleteBtn"
                                                        data-id="{{ $asset->id }}"
                                                        style="background:#fce8e6;color:#c62828;border:1px solid #ef9a9a;border-radius:6px;"
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <i class="fas fa-layer-group fa-3x mb-3 d-block" style="color:#d1d5db;"></i>
                                                <p class="text-muted mb-0">No assets registered yet.</p>
                                                <button class="btn btn-sm btn-primary mt-3" id="addBtnEmpty">
                                                    <i class="fas fa-plus mr-1"></i> Add First Asset
                                                </button>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($assets->count() > 0)
                                <tfoot>
                                    <tr style="background:#f8f9fc;border-top:2px solid #e3e6f0;">
                                        <td class="pl-3 py-3 font-weight-bold" colspan="3" style="color:#2d3748;">Total Book Value</td>
                                        <td class="py-3 text-right font-weight-bold" style="color:#e74a3b;font-size:15px;">
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
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">

                {{-- Status breakdown --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold" style="color:#4e73df;">
                            <i class="fas fa-circle-notch mr-2"></i>Status Overview
                        </h6>
                    </div>
                    <div class="card-body py-3 px-3">
                        @foreach($statusMeta as $key => $sm)
                            @php $cnt = $assets->where('status', $key)->count(); @endphp
                            <div class="d-flex justify-content-between align-items-center py-2"
                                 style="border-bottom:1px solid #f0f0f0;">
                                <span style="font-size:13px;color:#374151;">
                                    <span class="badge {{ $sm['badge'] }} mr-2">{{ $sm['label'] }}</span>
                                </span>
                                <span class="font-weight-bold" style="color:#2d3748;">{{ $cnt }} item{{ $cnt != 1 ? 's' : '' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Category value breakdown --}}
                @if($categoryTotals->count())
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold" style="color:#e74a3b;">
                            <i class="fas fa-chart-pie mr-2"></i>Value by Category
                        </h6>
                    </div>
                    <div class="card-body py-3 px-3">
                        @foreach($categoryTotals as $cat => $val)
                            @php
                                $pct = $totalValue > 0 ? round(($val / $totalValue) * 100) : 0;
                                $cm  = $catMeta[$cat] ?? ['color'=>'#858796','icon'=>'fa-box'];
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span style="font-size:13px;color:#374151;">
                                        <i class="fas {{ $cm['icon'] }} mr-1" style="color:{{ $cm['color'] }};width:14px;text-align:center;"></i>
                                        <strong>{{ $cat }}</strong>
                                    </span>
                                    <span style="font-size:12px;">
                                        <span style="color:#374151;font-weight:600;">{{ number_format($val, 0) }}</span>
                                        <span class="text-muted ml-1">{{ $pct }}%</span>
                                    </span>
                                </div>
                                <div class="progress" style="height:5px;border-radius:10px;background:#f0f0f0;">
                                    <div class="progress-bar" style="width:{{ $pct }}%;background:{{ $cm['color'] }};border-radius:10px;transition:width .6s ease;"></div>
                                </div>
                            </div>
                        @endforeach
                        <div class="d-flex justify-content-between align-items-center pt-2 mt-1"
                             style="border-top:2px solid #f0f0f0;">
                            <span class="font-weight-bold" style="color:#374151;">Total Book Value</span>
                            <span class="font-weight-bold" style="color:#e74a3b;font-size:15px;">
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
<div class="modal fade" id="assetModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="assetForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="asset_id" name="_asset_id">
            <div class="modal-content border-0" style="border-radius:12px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.15);">

                <div class="modal-header border-0 text-white px-4 py-3"
                     style="background:linear-gradient(135deg,#4e73df,#224abe);">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-layer-group mr-2"></i>Add Asset
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" style="opacity:.8;">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body px-4 py-4">
                    <div class="row">
                        {{-- Row 1: Name + Category --}}
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Asset Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="asset_name" id="aName" class="form-control"
                                   style="border-radius:8px;border-color:#d1d5db;" placeholder="e.g. Truck, Crusher, Scale" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Category <span class="text-danger">*</span>
                            </label>
                            <select name="category" id="aCategory" class="form-control"
                                    style="border-radius:8px;border-color:#d1d5db;" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Row 2: Qty + Price --}}
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Quantity <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="quantity" id="aQty" class="form-control"
                                   style="border-radius:8px;border-color:#d1d5db;" min="1" value="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Purchase Price (PKR each) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" style="background:#f3f4f6;border-color:#d1d5db;border-radius:8px 0 0 8px;font-weight:600;color:#6b7280;">PKR</span>
                                </div>
                                <input type="number" step="0.01" name="purchase_price" id="aPrice" class="form-control"
                                       style="border-color:#d1d5db;border-radius:0 8px 8px 0;" min="0" placeholder="0" required>
                            </div>
                        </div>

                        {{-- Row 3: Date + Location --}}
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Purchase Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="purchase_date" id="aDate" class="form-control"
                                   style="border-radius:8px;border-color:#d1d5db;" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Location
                            </label>
                            <input type="text" name="location" id="aLocation" class="form-control"
                                   style="border-radius:8px;border-color:#d1d5db;" placeholder="e.g. Factory, Office, Warehouse">
                        </div>

                        {{-- Row 4: Status + Condition --}}
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" id="aStatus" class="form-control"
                                    style="border-radius:8px;border-color:#d1d5db;" required>
                                <option value="active">Active</option>
                                <option value="under_repair">Under Repair</option>
                                <option value="disposed">Disposed</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Condition <span class="text-danger">*</span>
                            </label>
                            <select name="condition" id="aCondition" class="form-control"
                                    style="border-radius:8px;border-color:#d1d5db;" required>
                                <option value="good">Good</option>
                                <option value="fair">Fair</option>
                                <option value="poor">Poor</option>
                            </select>
                        </div>

                        {{-- Description --}}
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Description / Notes
                            </label>
                            <textarea name="description" id="aDesc" class="form-control" rows="3"
                                      style="border-radius:8px;border-color:#d1d5db;"
                                      placeholder="Model number, serial number, notes..."></textarea>
                        </div>

                        {{-- Image --}}
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Asset Image
                            </label>
                            {{-- Current image preview (shown only in edit mode) --}}
                            <div id="currentImageWrap" class="mb-2" style="display:none;">
                                <a id="currentImageLink" href="#" target="_blank"
                                   class="btn btn-sm btn-outline-secondary" style="border-radius:6px;">
                                    <i class="fas fa-image mr-1"></i> View Current Image
                                </a>
                            </div>
                            <input type="file" name="image" id="aImage" class="form-control"
                                   accept="image/jpg,image/jpeg,image/png,image/webp"
                                   style="border-radius:8px;border-color:#d1d5db;">
                            <small class="text-muted" id="imageHint">JPG, PNG or WebP, max 4 MB.</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 px-4 py-3" style="background:#f8f9fc;">
                    <button type="button" class="btn btn-light px-4"
                            data-dismiss="modal" data-bs-dismiss="modal"
                            style="border-radius:8px;border:1px solid #d1d5db;">
                        Cancel
                    </button>
                    <button class="btn btn-primary px-4" type="submit" id="submitBtn"
                            style="border-radius:8px;background:linear-gradient(135deg,#4e73df,#224abe);border:none;">
                        <i class="fas fa-save mr-1"></i> Save Asset
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
        $('#currentImageWrap').hide();
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
            $('#aStatus').val(a.status);
            $('#aCondition').val(a.condition);
            $('#aDesc').val(a.description);

            // Show current image link if available
            if (a.image) {
                $('#currentImageLink').attr('href', ASSET_URL + a.image);
                $('#currentImageWrap').show();
                $('#imageHint').text('Upload a new image to replace the current one.');
            } else {
                $('#currentImageWrap').hide();
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
