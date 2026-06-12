@extends('admin.layout.app')
@section('title', 'Expenses')

@php
$catClass = [
    'Fuel'                => 'fuel',
    'Vehicle Maintenance' => 'vehicle',
    'Food'                => 'food',
    'Rent'                => 'rent',
    'Utilities'           => 'utilities',
    'Repair'              => 'repair',
    'Others'              => 'others',
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
$payClass = [
    'Cash'      => 'cash',
    'JazzCash'  => 'jazzcash',
    'EasyPaisa' => 'easypaisa',
    'Bank'      => 'bank',
];
$catBarClass = [
    'Fuel'                => 'pbar-orange',
    'Vehicle Maintenance' => 'pbar-blue',
    'Food'                => 'pbar-teal',
    'Rent'                => 'pbar-purple',
    'Utilities'           => 'pbar-cyan',
    'Repair'              => 'pbar-red',
    'Others'              => 'pbar-grey',
];
$catIconClass = [
    'Fuel'                => 'text-c-orange',
    'Vehicle Maintenance' => 'text-c-blue2',
    'Food'                => 'text-c-teal',
    'Rent'                => 'text-c-purple2',
    'Utilities'           => 'text-c-cyan',
    'Repair'              => 'text-c-red',
    'Others'              => 'text-c-grey',
];
@endphp

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Expenses <small class="text-muted pn-stat-sub">اخراجات</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Expenses</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end">
                <button class="btn btn-primary px-4 btn-pn" id="addBtn">
                    <i class="fas fa-plus mr-1"></i> Add Expense / خرچ شامل کریں
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
                <div class="card border-0 shadow-sm h-100 pn-bl-red">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Spent / کل خرچ</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($grandTotal, 0) }}</div>
                                <small class="text-muted">PKR</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-red">
                                <i class="fas fa-receipt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100 pn-bl-cyan">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Transactions / لین دین</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ $expenses->count() }}</div>
                                <small class="text-muted">entries</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-cyan">
                                <i class="fas fa-list-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100 pn-bl-yellow">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Largest Expense / سب سے بڑا خرچ</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($expenses->max('amount') ?? 0, 0) }}</div>
                                <small class="text-muted">PKR</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-yellow">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100 pn-bl-teal">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Top Category / سب سے بڑی زمرہ</div>
                                <div class="h5 mb-0 font-weight-bold text-dark pn-stat-num-sm">
                                    {{ $categoryTotals->keys()->first() ?? '-' }}
                                </div>
                                <small class="text-muted">highest spend</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-teal">
                                <i class="fas fa-tags"></i>
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
                <div class="card border-0 shadow-sm card-pn">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h6 class="mb-0 font-weight-bold text-c-blue2">
                                <i class="fas fa-table mr-2"></i>Expense Records / اخراجات کی فہرست
                            </h6>
                            @if($selectedMonth)
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y') }}
                                </small>
                            @else
                                <small class="text-muted">All time</small>
                            @endif
                        </div>
                        <span class="badge pn-bdg pn-bdg-blue">{{ $expenses->count() }} records</span>
                    </div>
                    <div class="card-body p-2">
                        <div class="table-responsive">
                            <table class="table mb-0 pn-table pn-table-font" id="expenseTable">
                                <thead>
                                    <tr>
                                        <th class="pl-4">Date / تاریخ</th>
                                        <th>Category / زمرہ</th>
                                        <th>Description / تفصیل</th>
                                        <th class="text-right">Amount / رقم</th>
                                        <th>Payment / ادائیگی</th>
                                        <th class="text-center">Actions / اقدامات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($expenses as $expense)
                                        @php
                                            $cc  = $catClass[$expense->category]  ?? 'others';
                                            $ci  = $catIcons[$expense->category]  ?? 'fa-tag';
                                            $pc  = $payClass[$expense->payment_method] ?? 'bank';
                                        @endphp
                                        <tr id="row_{{ $expense->id }}">
                                            <td class="pl-4 align-middle">
                                                <span class="font-weight-bold d-block pn-text-heading">
                                                    {{ \Carbon\Carbon::parse($expense->expense_date)->format('d M') }}
                                                </span>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($expense->expense_date)->format('Y') }}</small>
                                            </td>
                                            <td class="align-middle">
                                                <span class="exp-pill exp-pill-{{ $cc }}">
                                                    <i class="fas {{ $ci }} mr-1"></i>{{ $expense->category }}
                                                </span>
                                            </td>
                                            <td class="align-middle col-desc">
                                                <span class="text-truncate d-block pn-table-font">
                                                    {{ $expense->description ?: ($expense->remarks ?: '—') }}
                                                </span>
                                            </td>
                                            <td class="align-middle text-right">
                                                <span class="font-weight-bold text-c-red pn-stat-num-sm">
                                                    {{ number_format($expense->amount, 0) }}
                                                </span>
                                            </td>
                                            <td class="align-middle">
                                                <span class="exp-pill pay-pill-{{ $pc }}">
                                                    <i class="fas {{ $ci }} mr-1"></i>{{ $expense->payment_method }}
                                                </span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <button class="btn btn-sm btn-pn btn-act-edit editBtn mr-1"
                                                        data-id="{{ $expense->id }}" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-pn btn-act-delete deleteBtn"
                                                        data-id="{{ $expense->id }}" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <i class="fas fa-receipt fa-3x mb-3 d-block icon-fade"></i>
                                                <p class="text-muted mb-0">No expenses found for this period.</p>
                                                <button class="btn btn-sm btn-primary btn-pn mt-3" id="addBtnEmpty">
                                                    <i class="fas fa-plus mr-1"></i> Add First Expense
                                                </button>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($expenses->count() > 0)
                                <tfoot>
                                    <tr class="pn-total-row">
                                        <td class="pl-4 py-3 font-weight-bold pn-text-heading" colspan="3">Grand Total / کل مجموعہ</td>
                                        <td class="py-3 text-right font-weight-bold text-c-red pn-stat-num-sm">
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
                <div class="card border-0 shadow-sm mb-3 card-pn">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-c-blue2">
                            <i class="fas fa-calendar-alt mr-2"></i>Filter by Month / مہینے کے مطابق فلٹر
                        </h6>
                    </div>
                    <div class="card-body py-3">
                        <form method="GET">
                            <select name="month" class="form-control mb-2 fc-pn" onchange="this.form.submit()">
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
                               class="btn btn-block btn-sm btn-pn btn-clear-filter mt-1">
                                <i class="fas fa-times mr-1"></i> Show All Time
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Category Breakdown --}}
                @if($categoryTotals->count())
                <div class="card border-0 shadow-sm card-pn">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-c-red">
                            <i class="fas fa-chart-pie mr-2"></i>Breakdown / زمرہ وار تفصیل
                        </h6>
                    </div>
                    <div class="card-body py-3 px-3">
                        @foreach($categoryTotals as $cat => $total)
                            @php
                                $pct   = $grandTotal > 0 ? round(($total / $grandTotal) * 100) : 0;
                                $cc    = $catClass[$cat]    ?? 'others';
                                $ci    = $catIcons[$cat]    ?? 'fa-tag';
                                $bc    = $catBarClass[$cat] ?? 'pbar-grey';
                                $iclr  = $catIconClass[$cat] ?? 'text-c-grey';
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="breakdown-item">
                                        <i class="fas {{ $ci }} mr-1 icon-fw14 {{ $iclr }}"></i>
                                        <strong>{{ $cat }}</strong>
                                    </span>
                                    <span class="breakdown-amount">
                                        <span class="breakdown-val">{{ number_format($total, 0) }}</span>
                                        <span class="text-muted ml-1">{{ $pct }}%</span>
                                    </span>
                                </div>
                                <div class="progress breakdown-bar">
                                    <div class="progress-bar pbar {{ $bc }}" style="--w:{{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                        <div class="d-flex justify-content-between align-items-center pt-2 mt-1 breakdown-grand-total-bar">
                            <span class="font-weight-bold pn-text-heading">Grand Total / کل مجموعہ</span>
                            <span class="font-weight-bold text-c-red pn-stat-num-sm">
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
<div class="modal fade modal-pn" id="ExpenseModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="ExpenseForm">
            @csrf
            <input type="hidden" id="expense_id" name="id">
            <div class="modal-content">

                {{-- Header --}}
                <div class="modal-header px-4 py-3">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-receipt mr-2"></i>Add Expense / خرچ شامل کریں
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                {{-- Body --}}
                <div class="modal-body px-4 py-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Date / تاریخ <span class="text-danger">*</span>
                            </label>
                            <input type="date" id="expense_date" name="expense_date"
                                   class="form-control fc-pn" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Category / زمرہ <span class="text-danger">*</span>
                            </label>
                            <select name="category" id="category" class="form-control fc-pn" required>
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
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Amount (PKR) / رقم <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text input-pre">PKR</span>
                                </div>
                                <input type="number" id="amount" step="0.01" name="amount"
                                       class="form-control fc-lg-pn" min="0" placeholder="0" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Payment Method / ادائیگی کا طریقہ <span class="text-danger">*</span>
                            </label>
                            <select name="payment_method" id="payment_method" class="form-control fc-pn" required>
                                <option value="Cash">💵 Cash</option>
                                <option value="JazzCash">📱 JazzCash</option>
                                <option value="EasyPaisa">📲 EasyPaisa</option>
                                <option value="Bank">🏦 Bank Transfer</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Description / تفصیل
                            </label>
                            <input type="text" id="description" name="description" class="form-control fc-pn"
                                   placeholder="e.g. Diesel for delivery truck...">
                        </div>
                        <div class="col-12 mb-1">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Remarks / ملاحظات
                            </label>
                            <textarea name="remarks" id="remarks" class="form-control fc-pn" rows="2"
                                      placeholder="Optional notes..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="modal-footer px-4 py-3">
                    <button type="button" class="btn btn-light px-4 btn-modal-cancel"
                            data-dismiss="modal" data-bs-dismiss="modal">
                        Cancel / منسوخ
                    </button>
                    <button class="btn btn-primary px-4 btn-modal-save" type="submit" id="submitBtn">
                        <i class="fas fa-save mr-1"></i> Save Expense / محفوظ کریں
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
const CAT_ICONS = {
    'Fuel':                'fa-gas-pump',
    'Vehicle Maintenance': 'fa-car',
    'Food':                'fa-utensils',
    'Rent':                'fa-home',
    'Utilities':           'fa-bolt',
    'Repair':              'fa-tools',
    'Others':              'fa-ellipsis-h',
};
const CAT_CLASS = {
    'Fuel': 'fuel', 'Vehicle Maintenance': 'vehicle',
    'Food': 'food', 'Rent': 'rent',
    'Utilities': 'utilities', 'Repair': 'repair', 'Others': 'others',
};
const PAY_ICONS = {
    'Cash': 'fa-money-bill-wave', 'JazzCash': 'fa-mobile-alt',
    'EasyPaisa': 'fa-mobile', 'Bank': 'fa-university', 'Bank Transfer': 'fa-university',
};
const PAY_CLASS = {
    'Cash': 'cash', 'JazzCash': 'jazzcash',
    'EasyPaisa': 'easypaisa', 'Bank': 'bank', 'Bank Transfer': 'bank',
};

function buildRow(exp) {
    const d    = new Date(exp.expense_date + 'T00:00:00');
    const day  = d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short' });
    const year = d.getFullYear();
    const amt  = Number(exp.amount).toLocaleString('en-PK', { maximumFractionDigits: 0 });
    const cc   = CAT_CLASS[exp.category]        || 'others';
    const ci   = CAT_ICONS[exp.category]        || 'fa-tag';
    const pc   = PAY_CLASS[exp.payment_method]  || 'bank';
    const pi   = PAY_ICONS[exp.payment_method]  || 'fa-credit-card';
    const desc = exp.description || exp.remarks || '—';
    return `<tr id="row_${exp.id}">
        <td class="pl-4 align-middle">
            <span class="font-weight-bold d-block pn-text-heading">${day}</span>
            <small class="text-muted">${year}</small>
        </td>
        <td class="align-middle">
            <span class="exp-pill exp-pill-${cc}">
                <i class="fas ${ci} mr-1"></i>${exp.category}
            </span>
        </td>
        <td class="align-middle col-desc pn-table-font">${desc}</td>
        <td class="align-middle text-right font-weight-bold text-c-red pn-stat-num-sm">${amt}</td>
        <td class="align-middle">
            <span class="exp-pill pay-pill-${pc}">
                <i class="fas ${pi} mr-1"></i>${exp.payment_method}
            </span>
        </td>
        <td class="align-middle text-center">
            <button class="btn btn-sm btn-pn btn-act-edit editBtn mr-1" data-id="${exp.id}">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-sm btn-pn btn-act-delete deleteBtn" data-id="${exp.id}">
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
        ordering: false,
        info: true,
        autoWidth: false,
        responsive: true,
        columnDefs: [
            { orderable: false, targets: [5] },
            { className: 'text-right', targets: [3] },
            { responsivePriority: 1, targets: -1 }
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
