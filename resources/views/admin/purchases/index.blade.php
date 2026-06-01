@extends('admin.layout.app')
@section('title', 'Salt Purchases')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Purchases <small class="text-muted" style="font-size:14px;">خریداری</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Purchases</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end">
                <button class="btn btn-primary px-4" style="border-radius:8px;" id="addBtn">
                    <i class="fas fa-plus mr-1"></i> Add Purchase / خریداری شامل کریں
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
                <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #e74a3b !important;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Spent / کل خرچ</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($totalSpent, 0) }}</div>
                                <small class="text-muted">PKR</small>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:46px;height:46px;background:rgba(231,74,59,.12);">
                                <i class="fas fa-shopping-cart" style="color:#e74a3b;font-size:18px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #4e73df !important;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Qty / کل مقدار</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($totalQtyTon, 2) }}</div>
                                <small class="text-muted">Tons / ٹن - {{ number_format($totalQtyKg, 0) }} KG / کلو</small>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:46px;height:46px;background:rgba(78,115,223,.12);">
                                <i class="fas fa-weight" style="color:#4e73df;font-size:18px;"></i>
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
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Transactions / لین دین</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ $totalEntries }}</div>
                                <small class="text-muted">purchase records</small>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:46px;height:46px;background:rgba(28,200,138,.12);">
                                <i class="fas fa-file-invoice" style="color:#1cc88a;font-size:18px;"></i>
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
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Avg per Purchase / اوسط فی خریداری</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">
                                    {{ $totalEntries > 0 ? number_format($totalSpent / $totalEntries, 0) : '0' }}
                                </div>
                                <small class="text-muted">PKR</small>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:46px;height:46px;background:rgba(246,194,62,.12);">
                                <i class="fas fa-chart-line" style="color:#f6c23e;font-size:18px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table + Sidebar --}}
        <div class="row">

            {{-- Table --}}
            <div class="col-lg-9 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h6 class="mb-0 font-weight-bold" style="color:#4e73df;">
                                <i class="fas fa-table mr-2"></i>Purchase Records
                            </h6>
                            @if($selectedMonth)
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y') }}
                                </small>
                            @else
                                <small class="text-muted">All time</small>
                            @endif
                        </div>
                        <span class="badge" style="background:#e8f0fe;color:#4e73df;font-size:12px;padding:5px 10px;border-radius:20px;">
                            {{ $totalEntries }} records
                        </span>
                    </div>
                    <div class="card-body p-2">
                        <div class="table-responsive">
                            <table class="table mb-0" id="purchasesTable" style="font-size:13.5px;">
                                <thead>
                                    <tr style="background:#f8f9fc;border-bottom:2px solid #e3e6f0;">
                                        <th class="pl-3 py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Date / تاریخ</th>
                                        <th class="py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Supplier / سپلائر</th>
                                        <th class="py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Qty (Ton) / مقدار (ٹن)</th>
                                        <th class="py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Qty (KG) / مقدار (کلو)</th>
                                        <th class="py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Rate/KG / نرخ/کلو</th>
                                        <th class="py-3 text-right text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Grand Total / کل مجموعہ</th>
                                        <th class="py-3 text-center text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Actions / اقدامات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchases as $row)
                                        <tr id="row_{{ $row->id }}" style="border-bottom:1px solid #f0f0f0;">
                                            <td class="pl-3 py-3 align-middle">
                                                @if($row->purchase_date)
                                                    <span class="font-weight-bold d-block" style="color:#2d3748;">
                                                        {{ $row->purchase_date->format('d M') }}
                                                    </span>
                                                    <small class="text-muted">{{ $row->purchase_date->format('Y') }}</small>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="py-3 align-middle">
                                                <span class="font-weight-bold d-block" style="color:#2d3748;">{{ $row->vendor->name ?? '—' }}</span>
                                                <small class="text-muted">{{ $row->vendor->phone ?? '' }}</small>
                                            </td>
                                            <td class="py-3 align-middle">{{ $row->salt_quantity ?? '—' }}</td>
                                            <td class="py-3 align-middle">{{ $row->salt_quantity_kg ?? '—' }}</td>
                                            <td class="py-3 align-middle">{{ number_format($row->rate_per_kg, 2) }}</td>
                                            <td class="py-3 align-middle text-right">
                                                <span class="font-weight-bold" style="color:#e74a3b;font-size:14px;">
                                                    {{ number_format($row->grand_total, 0) }}
                                                </span>
                                            </td>
                                            <td class="py-3 align-middle text-center">
                                                <button class="btn btn-sm editBtn mr-1"
                                                        data-id="{{ $row->id }}"
                                                        style="background:#fff3cd;color:#856404;border:1px solid #ffc107;border-radius:6px;"
                                                        title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm deleteBtn"
                                                        data-id="{{ $row->id }}"
                                                        style="background:#fce8e6;color:#c62828;border:1px solid #ef9a9a;border-radius:6px;"
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <i class="fas fa-shopping-cart fa-3x mb-3 d-block" style="color:#d1d5db;"></i>
                                                <p class="text-muted mb-0">No purchases found for this period.</p>
                                                <button class="btn btn-sm btn-primary mt-3" id="addBtnEmpty">
                                                    <i class="fas fa-plus mr-1"></i> Add First Purchase
                                                </button>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($purchases->count() > 0)
                                <tfoot>
                                    <tr style="background:#f8f9fc;border-top:2px solid #e3e6f0;">
                                        <td class="pl-3 py-3 font-weight-bold" colspan="5" style="color:#2d3748;">Grand Total / کل مجموعہ</td>
                                        <td class="py-3 text-right font-weight-bold" style="color:#e74a3b;font-size:15px;">
                                            {{ number_format($totalSpent, 0) }}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold" style="color:#4e73df;">
                            <i class="fas fa-calendar-alt mr-2"></i>Filter by Month
                        </h6>
                    </div>
                    <div class="card-body py-3">
                        <form method="GET">
                            <select name="month" class="form-control mb-2" onchange="this.form.submit()"
                                    style="border-radius:8px;border-color:#d1d5db;">
                                <option value="">All Time</option>
                                @foreach($months as $m)
                                    <option value="{{ $m->value }}" {{ $selectedMonth == $m->value ? 'selected' : '' }}>
                                        {{ $m->label }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                        @if($selectedMonth)
                            <a href="{{ route('admin.purchases.index') }}"
                               class="btn btn-block btn-sm mt-1"
                               style="background:#f3f4f6;color:#6b7280;border-radius:8px;border:1px solid #d1d5db;">
                                <i class="fas fa-times mr-1"></i> Show All Time
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Summary breakdown --}}
                @if($purchases->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold" style="color:#e74a3b;">
                            <i class="fas fa-chart-pie mr-2"></i>Cost Breakdown
                        </h6>
                    </div>
                    <div class="card-body py-3 px-3">
                        @php
                            $saltCost    = $purchases->sum('total_cost');
                            $transCost   = $purchases->sum('transport_cost');
                            $loadCost    = $purchases->sum('loading_unloading_cost');
                            $grandSum    = $purchases->sum('grand_total');
                        @endphp
                        @foreach([
                            ['Salt Cost',      $saltCost,  '#4e73df', 'fa-cube'],
                            ['Transport',      $transCost, '#1cc88a', 'fa-truck'],
                            ['Loading',        $loadCost,  '#f6c23e', 'fa-dolly'],
                        ] as [$label, $amount, $color, $icon])
                        @php $pct = $grandSum > 0 ? round(($amount / $grandSum) * 100) : 0; @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span style="font-size:13px;color:#374151;">
                                    <i class="fas {{ $icon }} mr-1" style="color:{{ $color }};width:14px;text-align:center;"></i>
                                    <strong>{{ $label }}</strong>
                                </span>
                                <span style="font-size:12px;">
                                    <span style="color:#374151;font-weight:600;">{{ number_format($amount, 0) }}</span>
                                    <span class="text-muted ml-1">{{ $pct }}%</span>
                                </span>
                            </div>
                            <div class="progress" style="height:5px;border-radius:10px;background:#f0f0f0;">
                                <div class="progress-bar" style="width:{{ $pct }}%;background:{{ $color }};border-radius:10px;transition:width .6s ease;"></div>
                            </div>
                        </div>
                        @endforeach
                        <div class="d-flex justify-content-between align-items-center pt-2 mt-1"
                             style="border-top:2px solid #f0f0f0;">
                            <span class="font-weight-bold" style="color:#374151;">Grand Total</span>
                            <span class="font-weight-bold" style="color:#e74a3b;font-size:15px;">
                                PKR {{ number_format($grandSum, 0) }}
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
<div class="modal fade" id="purchaseModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="purchaseForm">
            @csrf
            <input type="hidden" id="purchase_id" name="_purchase_id">
            <div class="modal-content border-0" style="border-radius:12px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.15);">

                <div class="modal-header border-0 text-white px-4 py-3"
                     style="background:linear-gradient(135deg,#4e73df,#224abe);">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-shopping-cart mr-2"></i>Add Purchase / خریداری شامل کریں
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" style="opacity:.8;">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body px-4 py-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Supplier / سپلائر <span class="text-danger">*</span>
                            </label>
                            <select name="vendor_id" id="vendor_id" class="form-control"
                                    style="border-radius:8px;border-color:#d1d5db;" required>
                                <option value="">— Select Supplier —</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}{{ $vendor->shop ? ' ('.$vendor->shop.')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Purchase Date / خریداری کی تاریخ
                            </label>
                            <input type="date" name="purchase_date" id="purchase_date" class="form-control"
                                   style="border-radius:8px;border-color:#d1d5db;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Salt Quantity (Ton) / نمک کی مقدار (ٹن) <span class="text-danger">*</span>
                            </label>
                            <input type="number" step="0.01" name="salt_quantity" id="salt_quantity" class="form-control calc-input"
                                   style="border-radius:8px;border-color:#d1d5db;" min="0" placeholder="0.00" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Salt Quantity (KG) / نمک کی مقدار (کلو)
                            </label>
                            <input type="number" step="0.01" name="salt_quantity_kg" id="salt_quantity_kg" class="form-control calc-input"
                                   style="border-radius:8px;border-color:#d1d5db;" min="0" placeholder="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Rate per KG (PKR) / نرخ فی کلو <span class="text-danger">*</span>
                            </label>
                            <input type="number" step="0.01" name="rate_per_kg" id="rate_per_kg" class="form-control calc-input"
                                   style="border-radius:8px;border-color:#d1d5db;" min="0" placeholder="0.00" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Total Cost (PKR) / کل لاگت <span class="text-danger">*</span>
                            </label>
                            <input type="number" step="0.01" name="total_cost" id="total_cost" class="form-control calc-input"
                                   style="border-radius:8px;border-color:#d1d5db;" min="0" placeholder="Auto-calculated" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Transport Cost (PKR) / نقل و حمل
                            </label>
                            <input type="number" step="0.01" name="transport_cost" id="transport_cost" class="form-control calc-input"
                                   style="border-radius:8px;border-color:#d1d5db;" min="0" placeholder="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Loading / Unloading (PKR) / لوڈنگ / اَن لوڈنگ
                            </label>
                            <input type="number" step="0.01" name="loading_unloading_cost" id="loading_unloading_cost" class="form-control calc-input"
                                   style="border-radius:8px;border-color:#d1d5db;" min="0" placeholder="0">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Grand Total (PKR) / کل مجموعہ <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" style="background:#f3f4f6;border-color:#d1d5db;border-radius:8px 0 0 8px;font-weight:600;color:#6b7280;">PKR</span>
                                </div>
                                <input type="number" step="0.01" name="grand_total" id="grand_total" class="form-control"
                                       style="border-color:#d1d5db;border-radius:0 8px 8px 0;font-weight:bold;font-size:16px;" min="0" required>
                            </div>
                            <small class="text-muted">= Total Cost + Transport + Loading</small>
                        </div>
                        <div class="col-12 mb-1">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Remarks / ملاحظات
                            </label>
                            <textarea name="remarks" id="remarks" class="form-control" rows="2"
                                      style="border-radius:8px;border-color:#d1d5db;"
                                      placeholder="Optional notes..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 px-4 py-3" style="background:#f8f9fc;">
                    <button type="button" class="btn btn-light px-4"
                            data-dismiss="modal" data-bs-dismiss="modal"
                            style="border-radius:8px;border:1px solid #d1d5db;">
                        Cancel / منسوخ
                    </button>
                    <button class="btn btn-primary px-4" type="submit" id="submitBtn"
                            style="border-radius:8px;background:linear-gradient(135deg,#4e73df,#224abe);border:none;">
                        <i class="fas fa-save mr-1"></i> Save Purchase / محفوظ کریں
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function recalcGrandTotal() {
    const tc  = parseFloat($('#total_cost').val())             || 0;
    const tr  = parseFloat($('#transport_cost').val())         || 0;
    const lu  = parseFloat($('#loading_unloading_cost').val()) || 0;
    $('#grand_total').val((tc + tr + lu).toFixed(2));
}

function recalcTotalCost() {
    const qty = parseFloat($('#salt_quantity_kg').val()) || 0;
    const rate = parseFloat($('#rate_per_kg').val())     || 0;
    if (qty > 0 && rate > 0) {
        $('#total_cost').val((qty * rate).toFixed(2));
        recalcGrandTotal();
    }
}

$(function () {

    $('#purchasesTable').DataTable({
        paging: true,
        pageLength: 15,
        lengthChange: false,
        searching: true,
        ordering: false,
        info: true,
        autoWidth: false,
        responsive: true,
        columnDefs: [{ orderable: false, targets: [6] }, { responsivePriority: 1, targets: -1 }],
        language: {
            search: '',
            searchPlaceholder: 'Search purchases...',
            info: 'Showing _START_–_END_ of _TOTAL_',
            paginate: { previous: '‹', next: '›' }
        }
    });

    // Auto-calc: KG * rate => total cost
    $('#salt_quantity_kg, #rate_per_kg').on('input', recalcTotalCost);

    // Auto-calc: total + transport + loading => grand total
    $('#total_cost, #transport_cost, #loading_unloading_cost').on('input', recalcGrandTotal);

    // Open add modal
    $('#addBtn, #addBtnEmpty').on('click', function () {
        $('#purchaseForm')[0].reset();
        $('#purchase_id').val('');
        $('#purchase_date').val(new Date().toISOString().split('T')[0]);
        $('#modalTitle').html('<i class="fas fa-shopping-cart mr-2"></i>Add Purchase');
        $('#submitBtn').html('<i class="fas fa-save mr-1"></i> Save Purchase');
        $('#purchaseModal').modal('show');
    });

    // Submit (add + edit)
    $('#purchaseForm').on('submit', function (e) {
        e.preventDefault();
        const id  = $('#purchase_id').val();
        const url = id ? (APP_URL + '/purchases/' + id) : "{{ route('admin.purchases.store') }}";
        const btn = $('#submitBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        $.ajax({
            url, type: 'POST',
            data: $(this).serialize() + (id ? '&_method=PUT' : ''),
            success: function () {
                toastr.success('Purchase saved!');
                $('#purchaseModal').modal('hide');
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
                btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Purchase');
            }
        });
    });

    // Edit
    $(document).on('click', '.editBtn', function () {
        const id = $(this).data('id');
        $.get(APP_URL + '/purchases/' + id + '/edit', function (p) {
            $('#purchase_id').val(p.id);
            $('#vendor_id').val(p.vendor_id);
            $('#purchase_date').val(p.purchase_date);
            $('#salt_quantity').val(p.salt_quantity);
            $('#salt_quantity_kg').val(p.salt_quantity_kg);
            $('#rate_per_kg').val(p.rate_per_kg);
            $('#total_cost').val(p.total_cost);
            $('#transport_cost').val(p.transport_cost);
            $('#loading_unloading_cost').val(p.loading_unloading_cost);
            $('#grand_total').val(p.grand_total);
            $('#remarks').val(p.remarks);
            $('#modalTitle').html('<i class="fas fa-edit mr-2"></i>Edit Purchase');
            $('#submitBtn').html('<i class="fas fa-save mr-1"></i> Update Purchase');
            $('#purchaseModal').modal('show');
        });
    });

    // Delete
    $(document).on('click', '.deleteBtn', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Delete this purchase?',
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
                url: APP_URL + '/purchases/' + id,
                type: 'POST',
                data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                success: function () {
                    $('#row_' + id).fadeOut(300, function () { $(this).remove(); });
                    toastr.success('Purchase deleted.');
                },
                error: function (xhr) { toastr.error('Error: ' + xhr.status); }
            });
        });
    });

});
</script>
@endsection
