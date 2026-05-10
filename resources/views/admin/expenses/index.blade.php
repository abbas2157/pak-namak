@extends('admin.layout.app')
@section('title', 'Expenses')

@php
$catColors = [
    'Fuel'                => '#fd7e14',
    'Vehicle Maintenance' => '#4e73df',
    'Food'                => '#1cc88a',
    'Rent'                => '#6f42c1',
    'Utilities'           => '#36b9cc',
    'Repair'              => '#e74a3b',
    'Others'              => '#858796',
];
$catIcons = [
    'Fuel'                => 'fa-gas-pump',
    'Vehicle Maintenance' => 'fa-car',
    'Food'                => 'fa-utensils',
    'Rent'                => 'fa-home',
    'Utilities'           => 'fa-bolt',
    'Repair'              => 'fa-tools',
    'Others'              => 'fa-ellipsis-h',
];
$payMeta = [
    'Cash'      => ['bg' => '#1cc88a', 'icon' => 'fa-money-bill-wave'],
    'JazzCash'  => ['bg' => '#fd7e14', 'icon' => 'fa-mobile-alt'],
    'EasyPaisa' => ['bg' => '#28a745', 'icon' => 'fa-mobile'],
    'Bank'      => ['bg' => '#4e73df', 'icon' => 'fa-university'],
];
@endphp

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Expenses</h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Expenses</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end">
                <button class="btn btn-primary px-4" style="border-radius:8px;" id="addBtn">
                    <i class="fas fa-plus mr-1"></i> Add Expense
                </button>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        {{-- ===== STAT CARDS ===== --}}
        <div class="row mb-3">
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #e74a3b !important;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Spent</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($grandTotal, 0) }}</div>
                                <small class="text-muted">PKR</small>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:46px;height:46px;background:rgba(231,74,59,.12);">
                                <i class="fas fa-receipt" style="color:#e74a3b;font-size:18px;"></i>
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
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Transactions</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ $expenses->count() }}</div>
                                <small class="text-muted">entries</small>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:46px;height:46px;background:rgba(54,185,204,.12);">
                                <i class="fas fa-list-alt" style="color:#36b9cc;font-size:18px;"></i>
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
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Largest Expense</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($expenses->max('amount') ?? 0, 0) }}</div>
                                <small class="text-muted">PKR</small>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:46px;height:46px;background:rgba(246,194,62,.12);">
                                <i class="fas fa-arrow-up" style="color:#f6c23e;font-size:18px;"></i>
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
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Top Category</div>
                                <div class="h5 mb-0 font-weight-bold text-dark" style="font-size:15px !important;">
                                    {{ $categoryTotals->keys()->first() ?? '-' }}
                                </div>
                                <small class="text-muted">highest spend</small>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:46px;height:46px;background:rgba(28,200,138,.12);">
                                <i class="fas fa-tags" style="color:#1cc88a;font-size:18px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== TABLE + SIDEBAR ===== --}}
        <div class="row">

            {{-- TABLE --}}
            <div class="col-lg-8 mb-3">
                <div class="card border-0 shadow-sm ">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h6 class="mb-0 font-weight-bold" style="color:#4e73df;">
                                <i class="fas fa-table mr-2"></i>Expense Records
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
                            {{ $expenses->count() }} records
                        </span>
                    </div>
                    <div class="card-body p-2">
                        <div class="table-responsive">
                            <table class="table mb-0" id="expenseTable" style="font-size:13.5px;">
                                <thead>
                                    <tr style="background:#f8f9fc;border-bottom:2px solid #e3e6f0;">
                                        <th class="pl-4 py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Date</th>
                                        <th class="py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Category</th>
                                        <th class="py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Description</th>
                                        <th class="py-3 text-right text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Amount</th>
                                        <th class="py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Payment</th>
                                        <th class="py-3 text-center text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($expenses as $expense)
                                        @php
                                            $cc = $catColors[$expense->category] ?? '#858796';
                                            $ci = $catIcons[$expense->category]  ?? 'fa-tag';
                                            $pm = $payMeta[$expense->payment_method] ?? ['bg'=>'#858796','icon'=>'fa-credit-card'];
                                        @endphp
                                        <tr id="row_{{ $expense->id }}" style="border-bottom:1px solid #f0f0f0;">
                                            <td class="pl-4 py-3 align-middle">
                                                <span class="font-weight-bold d-block" style="color:#2d3748;">
                                                    {{ \Carbon\Carbon::parse($expense->expense_date)->format('d M') }}
                                                </span>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($expense->expense_date)->format('Y') }}</small>
                                            </td>
                                            <td class="py-3 align-middle">
                                                <span class="d-inline-flex align-items-center px-2 py-1 rounded-pill"
                                                      style="background:{{ $cc }}18;color:{{ $cc }};font-size:12px;font-weight:600;white-space:nowrap;">
                                                    <i class="fas {{ $ci }} mr-1"></i>{{ $expense->category }}
                                                </span>
                                            </td>
                                            <td class="py-3 align-middle" style="max-width:180px;">
                                                <span class="text-truncate d-block" style="color:#444;">
                                                    {{ $expense->description ?: ($expense->remarks ?: '—') }}
                                                </span>
                                            </td>
                                            <td class="py-3 align-middle text-right">
                                                <span class="font-weight-bold" style="color:#e74a3b;font-size:14px;">
                                                    {{ number_format($expense->amount, 0) }}
                                                </span>
                                            </td>
                                            <td class="py-3 align-middle">
                                                <span class="d-inline-flex align-items-center px-2 py-1 rounded-pill"
                                                      style="background:{{ $pm['bg'] }}18;color:{{ $pm['bg'] }};font-size:12px;font-weight:600;white-space:nowrap;">
                                                    <i class="fas {{ $pm['icon'] }} mr-1"></i>{{ $expense->payment_method }}
                                                </span>
                                            </td>
                                            <td class="py-3 align-middle text-center">
                                                <button class="btn btn-sm editBtn mr-1"
                                                        data-id="{{ $expense->id }}"
                                                        style="background:#fff3cd;color:#856404;border:1px solid #ffc107;border-radius:6px;"
                                                        title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm deleteBtn"
                                                        data-id="{{ $expense->id }}"
                                                        style="background:#fce8e6;color:#c62828;border:1px solid #ef9a9a;border-radius:6px;"
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <i class="fas fa-receipt fa-3x mb-3 d-block" style="color:#d1d5db;"></i>
                                                <p class="text-muted mb-0">No expenses found for this period.</p>
                                                <button class="btn btn-sm btn-primary mt-3" id="addBtnEmpty">
                                                    <i class="fas fa-plus mr-1"></i> Add First Expense
                                                </button>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($expenses->count() > 0)
                                <tfoot>
                                    <tr style="background:#f8f9fc;border-top:2px solid #e3e6f0;">
                                        <td class="pl-4 py-3 font-weight-bold" colspan="3" style="color:#2d3748;">Grand Total</td>
                                        <td class="py-3 text-right font-weight-bold" style="color:#e74a3b;font-size:15px;">
                                            {{ number_format($grandTotal, 0) }}
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

            {{-- SIDEBAR --}}
            <div class="col-lg-4">

                {{-- Month Filter --}}
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
                                    <option value="{{ $m['value'] }}" {{ $selectedMonth == $m['value'] ? 'selected' : '' }}>
                                        {{ $m['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                        @if($selectedMonth)
                            <a href="{{ route('admin.expenses.index') }}"
                               class="btn btn-block btn-sm mt-1"
                               style="background:#f3f4f6;color:#6b7280;border-radius:8px;border:1px solid #d1d5db;">
                                <i class="fas fa-times mr-1"></i> Show All Time
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Category Breakdown --}}
                @if($categoryTotals->count())
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold" style="color:#e74a3b;">
                            <i class="fas fa-chart-pie mr-2"></i>Breakdown
                        </h6>
                    </div>
                    <div class="card-body py-3 px-3">
                        @foreach($categoryTotals as $cat => $total)
                            @php
                                $pct = $grandTotal > 0 ? round(($total / $grandTotal) * 100) : 0;
                                $cc  = $catColors[$cat] ?? '#858796';
                                $ci  = $catIcons[$cat]  ?? 'fa-tag';
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span style="font-size:13px;color:#374151;">
                                        <i class="fas {{ $ci }} mr-1" style="color:{{ $cc }};width:14px;text-align:center;"></i>
                                        <strong>{{ $cat }}</strong>
                                    </span>
                                    <span style="font-size:12px;">
                                        <span style="color:#374151;font-weight:600;">{{ number_format($total, 0) }}</span>
                                        <span class="text-muted ml-1">{{ $pct }}%</span>
                                    </span>
                                </div>
                                <div class="progress" style="height:5px;border-radius:10px;background:#f0f0f0;">
                                    <div class="progress-bar" role="progressbar"
                                         style="width:{{ $pct }}%;background:{{ $cc }};border-radius:10px;transition:width .6s ease;">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="d-flex justify-content-between align-items-center pt-2 mt-1"
                             style="border-top:2px solid #f0f0f0;">
                            <span class="font-weight-bold" style="color:#374151;">Grand Total</span>
                            <span class="font-weight-bold" style="color:#e74a3b;font-size:15px;">
                                PKR {{ number_format($grandTotal, 0) }}
                            </span>
                        </div>
                    </div>
                </div>
                @endif

            </div>{{-- /sidebar --}}
        </div>

    </div>
</section>

{{-- ============================================================
     MODAL
     ============================================================ --}}
<div class="modal fade" id="ExpenseModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="ExpenseForm">
            @csrf
            <input type="hidden" id="expense_id" name="id">
            <div class="modal-content border-0" style="border-radius:12px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.15);">

                {{-- Header --}}
                <div class="modal-header border-0 text-white px-4 py-3"
                     style="background:linear-gradient(135deg,#4e73df,#224abe);">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-receipt mr-2"></i>Add Expense
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" style="opacity:.8;">
                        <span>&times;</span>
                    </button>
                </div>

                {{-- Body --}}
                <div class="modal-body px-4 py-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" id="expense_date" name="expense_date" class="form-control"
                                   style="border-radius:8px;border-color:#d1d5db;" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Category <span class="text-danger">*</span>
                            </label>
                            <select name="category" id="category" class="form-control"
                                    style="border-radius:8px;border-color:#d1d5db;" required>
                                <option value="">— Select —</option>
                                <option value="Fuel">⛽ Fuel</option>
                                <option value="Vehicle Maintenance">🚗 Vehicle Maintenance</option>
                                <option value="Food">🍽️ Food</option>
                                <option value="Rent">🏠 Rent</option>
                                <option value="Utilities">⚡ Utilities</option>
                                <option value="Repair">🔧 Repair</option>
                                <option value="Others">📦 Others</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Amount (PKR) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group" style="border-radius:8px;">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" style="background:#f3f4f6;border-color:#d1d5db;border-radius:8px 0 0 8px;font-weight:600;color:#6b7280;">PKR</span>
                                </div>
                                <input type="number" id="amount" step="0.01" name="amount"
                                       class="form-control" style="border-color:#d1d5db;border-radius:0 8px 8px 0;"
                                       min="0" placeholder="0" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Payment Method <span class="text-danger">*</span>
                            </label>
                            <select name="payment_method" id="payment_method" class="form-control"
                                    style="border-radius:8px;border-color:#d1d5db;" required>
                                <option value="Cash">💵 Cash</option>
                                <option value="JazzCash">📱 JazzCash</option>
                                <option value="EasyPaisa">📲 EasyPaisa</option>
                                <option value="Bank">🏦 Bank Transfer</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Description
                            </label>
                            <input type="text" id="description" name="description" class="form-control"
                                   style="border-radius:8px;border-color:#d1d5db;"
                                   placeholder="e.g. Diesel for delivery truck...">
                        </div>
                        <div class="col-12 mb-1">
                            <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                                Remarks
                            </label>
                            <textarea name="remarks" id="remarks" class="form-control" rows="2"
                                      style="border-radius:8px;border-color:#d1d5db;"
                                      placeholder="Optional notes..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="modal-footer border-0 px-4 py-3" style="background:#f8f9fc;">
                    <button type="button" class="btn btn-light px-4"
                            data-dismiss="modal" data-bs-dismiss="modal"
                            style="border-radius:8px;border:1px solid #d1d5db;">
                        Cancel
                    </button>
                    <button class="btn btn-primary px-4" type="submit" id="submitBtn"
                            style="border-radius:8px;background:linear-gradient(135deg,#4e73df,#224abe);border:none;">
                        <i class="fas fa-save mr-1"></i> Save Expense
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
const CAT_COLORS = {
    'Fuel':                '#fd7e14',
    'Vehicle Maintenance': '#4e73df',
    'Food':                '#1cc88a',
    'Rent':                '#6f42c1',
    'Utilities':           '#36b9cc',
    'Repair':              '#e74a3b',
    'Others':              '#858796',
};
const CAT_ICONS = {
    'Fuel':                'fa-gas-pump',
    'Vehicle Maintenance': 'fa-car',
    'Food':                'fa-utensils',
    'Rent':                'fa-home',
    'Utilities':           'fa-bolt',
    'Repair':              'fa-tools',
    'Others':              'fa-ellipsis-h',
};
const PAY_META = {
    'Cash':           { bg: '#1cc88a', icon: 'fa-money-bill-wave' },
    'JazzCash':       { bg: '#fd7e14', icon: 'fa-mobile-alt' },
    'EasyPaisa':      { bg: '#28a745', icon: 'fa-mobile' },
    'Bank':           { bg: '#4e73df', icon: 'fa-university' },
    'Bank Transfer':  { bg: '#4e73df', icon: 'fa-university' },
};

function buildRow(exp) {
    const d    = new Date(exp.expense_date + 'T00:00:00');
    const day  = d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short' });
    const year = d.getFullYear();
    const amt  = Number(exp.amount).toLocaleString('en-PK', { maximumFractionDigits: 0 });
    const cc   = CAT_COLORS[exp.category]        || '#858796';
    const ci   = CAT_ICONS[exp.category]         || 'fa-tag';
    const pm   = PAY_META[exp.payment_method]    || { bg: '#858796', icon: 'fa-credit-card' };
    const desc = exp.description || exp.remarks  || '—';
    return `<tr id="row_${exp.id}" style="border-bottom:1px solid #f0f0f0;">
        <td class="pl-4 py-3 align-middle">
            <span class="font-weight-bold d-block" style="color:#2d3748;">${day}</span>
            <small class="text-muted">${year}</small>
        </td>
        <td class="py-3 align-middle">
            <span class="d-inline-flex align-items-center px-2 py-1 rounded-pill"
                  style="background:${cc}18;color:${cc};font-size:12px;font-weight:600;white-space:nowrap;">
                <i class="fas ${ci} mr-1"></i>${exp.category}
            </span>
        </td>
        <td class="py-3 align-middle" style="max-width:180px;color:#444;">${desc}</td>
        <td class="py-3 align-middle text-right font-weight-bold" style="color:#e74a3b;font-size:14px;">${amt}</td>
        <td class="py-3 align-middle">
            <span class="d-inline-flex align-items-center px-2 py-1 rounded-pill"
                  style="background:${pm.bg}18;color:${pm.bg};font-size:12px;font-weight:600;white-space:nowrap;">
                <i class="fas ${pm.icon} mr-1"></i>${exp.payment_method}
            </span>
        </td>
        <td class="py-3 align-middle text-center">
            <button class="btn btn-sm editBtn mr-1" data-id="${exp.id}"
                    style="background:#fff3cd;color:#856404;border:1px solid #ffc107;border-radius:6px;">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-sm deleteBtn" data-id="${exp.id}"
                    style="background:#fce8e6;color:#c62828;border:1px solid #ef9a9a;border-radius:6px;">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>`;
}

$(function () {

    // Open modal for Add
    $('#addBtn, #addBtnEmpty').on('click', function () {
        $('#ExpenseForm')[0].reset();
        $('#expense_id').val('');
        $('#modalTitle').html('<i class="fas fa-receipt mr-2"></i>Add Expense');
        $('#submitBtn').html('<i class="fas fa-save mr-1"></i> Save Expense');
        $('#expense_date').val(new Date().toISOString().split('T')[0]);
        $('#ExpenseModal').modal('show');
    });

    // Submit (Add + Edit)
    $('#ExpenseForm').on('submit', function (e) {
        e.preventDefault();
        const id  = $('#expense_id').val();
        const url = id ? (APP_URL + '/expenses/' + id) : "{{ route('admin.expenses.store') }}";
        const btn = $('#submitBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        $.ajax({
            url, type: 'POST',
            data: $(this).serialize() + (id ? '&_method=PUT' : ''),
            success: function (exp) {
                const row = buildRow(exp);
                if (id) {
                    $('#row_' + id).replaceWith(row);
                } else {
                    $('#expenseTable tbody tr td[colspan]').closest('tr').remove();
                    $('#expenseTable tbody').prepend(row);
                }
                toastr.success('Expense saved!');
                $('#ExpenseModal').modal('hide');
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    alert(Object.values(xhr.responseJSON.errors).map(e => e[0]).join("\n"));
                } else {
                    toastr.error('Something went wrong.');
                }
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Expense');
            }
        });
    });

    // Edit
    $(document).on('click', '.editBtn', function () {
        const id = $(this).data('id');
        $.get(APP_URL + '/expenses/' + id + '/edit', function (exp) {
            $('#expense_id').val(exp.id);
            $('#expense_date').val(exp.expense_date);
            $('#category').val(exp.category);
            $('#payment_method').val(exp.payment_method);
            $('#amount').val(exp.amount);
            $('#description').val(exp.description);
            $('#remarks').val(exp.remarks);
            $('#modalTitle').html('<i class="fas fa-edit mr-2"></i>Edit Expense');
            $('#submitBtn').html('<i class="fas fa-save mr-1"></i> Update Expense');
            $('#ExpenseModal').modal('show');
        });
    });

    // Delete
    $(document).on('click', '.deleteBtn', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Delete this expense?',
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
                url: APP_URL + '/expenses/' + id,
                type: 'POST',
                data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                success: function () {
                    $('#row_' + id).fadeOut(300, function () { $(this).remove(); });
                    toastr.success('Expense deleted.');
                },
                error: function (xhr) { toastr.error('Error: ' + xhr.status); }
            });
        });
    });

    // DataTable (search + sort)
    $('#expenseTable').DataTable({
        paging: true,
        pageLength: 15,
        lengthChange: false,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        responsive: true,
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: [5] },
            { className: 'text-right', targets: [3] }
        ],
        language: {
            search: '',
            searchPlaceholder: 'Search expenses...',
            info: 'Showing _START_–_END_ of _TOTAL_',
            paginate: { previous: '‹', next: '›' }
        }
    });

});
</script>
@endsection
