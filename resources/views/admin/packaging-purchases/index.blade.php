@extends('admin.layout.app')
@section('title', 'Packaging Purchases')

@php
    $packagingSizes = config('admin.packaging_sizes');
    $kindLabel = fn ($kind) => $kind === 'thaila' ? 'Thaila' : 'Packet';
@endphp

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Packaging Purchases <small class="text-muted pn-stat-sub">پیکنگ خریداری</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Packaging Purchases</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end">
                <button class="btn btn-primary px-4 btn-pn" id="addBtn">
                    <i class="fas fa-plus mr-1"></i> Add Packaging Purchase / پیکنگ خریداری شامل کریں
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
                <div class="card border-0 shadow-sm h-100 pn-bl-red">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Spent / کل خرچ</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($totalSpent, 0) }}</div>
                                <small class="text-muted">PKR</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-red">
                                <i class="fas fa-box-open"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100 pn-bl-blue">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Qty / کل تعداد</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($totalQty, 0) }}</div>
                                <small class="text-muted">bags / packets</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-blue">
                                <i class="fas fa-layer-group"></i>
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
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Transactions / لین دین</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ $totalEntries }}</div>
                                <small class="text-muted">purchase records</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-teal">
                                <i class="fas fa-file-invoice"></i>
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
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Pending / باقی</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($totalPending, 0) }}</div>
                                <small class="text-muted">PKR</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-yellow">
                                <i class="fas fa-hourglass-half"></i>
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
                <div class="card border-0 shadow-sm card-pn">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h6 class="mb-0 font-weight-bold text-c-blue2">
                                <i class="fas fa-table mr-2"></i>Packaging Purchase Records
                            </h6>
                            @if($selectedMonth)
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y') }}
                                </small>
                            @else
                                <small class="text-muted">All time</small>
                            @endif
                        </div>
                        <span class="badge pn-bdg pn-bdg-blue">{{ $totalEntries }} records</span>
                    </div>
                    <div class="card-body p-2">
                        <div class="table-responsive">
                            <table class="table mb-0 pn-table pn-table-font" id="packagingTable">
                                <thead>
                                    <tr>
                                        <th class="pl-3">Date / تاریخ</th>
                                        <th>Supplier / سپلائر</th>
                                        <th>Item / آئٹم</th>
                                        <th>Qty / تعداد</th>
                                        <th>Rate / نرخ</th>
                                        <th class="text-right">Grand Total / کل مجموعہ</th>
                                        <th class="text-right">Paid / ادا شدہ</th>
                                        <th class="text-right">Pending / باقی</th>
                                        <th class="text-center">Actions / اقدامات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchases as $row)
                                        <tr id="row_{{ $row->id }}">
                                            <td class="pl-3 align-middle">
                                                @if($row->purchase_date)
                                                    <span class="font-weight-bold d-block pn-text-heading">
                                                        {{ $row->purchase_date->format('d M') }}
                                                    </span>
                                                    <small class="text-muted">{{ $row->purchase_date->format('Y') }}</small>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                <span class="font-weight-bold d-block pn-text-heading">{{ $row->vendor->name ?? '—' }}</span>
                                                <small class="text-muted">{{ $row->vendor->phone ?? '' }}</small>
                                                @if($row->is_investment)
                                                    <span class="badge badge-warning d-block mt-1"><i class="fas fa-piggy-bank mr-1"></i>Investment</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                <span class="badge pn-bdg {{ $row->kind === 'thaila' ? 'pn-bdg-blue' : 'pn-bdg-teal' }}">
                                                    {{ $row->sizeLabel() }}
                                                </span>
                                            </td>
                                            <td class="align-middle">{{ number_format($row->quantity, 0) }}</td>
                                            <td class="align-middle">{{ number_format($row->rate_per_unit, 2) }}</td>
                                            <td class="align-middle text-right">
                                                <span class="font-weight-bold text-c-red pn-stat-num-sm">
                                                    {{ number_format($row->grand_total, 0) }}
                                                </span>
                                            </td>
                                            <td class="align-middle text-right">{{ number_format($row->paid_amount, 0) }}</td>
                                            <td class="align-middle text-right">
                                                @if($row->pending_amount > 0)
                                                    <span class="font-weight-bold text-danger">{{ number_format($row->pending_amount, 0) }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center text-nowrap">
                                                @if($row->pending_amount > 0)
                                                <button class="btn btn-sm btn-pn btn-act-confirm recordPaymentBtn mr-1"
                                                        data-id="{{ $row->id }}"
                                                        data-vendor="{{ $row->vendor->name ?? '—' }}"
                                                        data-pending="{{ $row->pending_amount }}"
                                                        title="Record Payment">
                                                    <i class="fas fa-hand-holding-dollar"></i>
                                                </button>
                                                @endif
                                                <button class="btn btn-sm btn-pn btn-act-view historyBtn mr-1"
                                                        data-id="{{ $row->id }}" title="Payment History">
                                                    <i class="fas fa-clock-rotate-left"></i>
                                                </button>
                                                <button class="btn btn-sm btn-pn btn-act-edit editBtn mr-1"
                                                        data-id="{{ $row->id }}" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-pn btn-act-delete deleteBtn"
                                                        data-id="{{ $row->id }}" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5">
                                                <i class="fas fa-box-open fa-3x mb-3 d-block icon-fade"></i>
                                                <p class="text-muted mb-0">No packaging purchases found for this period.</p>
                                                <button class="btn btn-sm btn-primary btn-pn mt-3" id="addBtnEmpty">
                                                    <i class="fas fa-plus mr-1"></i> Add First Packaging Purchase
                                                </button>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($purchases->count() > 0)
                                <tfoot>
                                    <tr class="pn-total-row">
                                        <td class="pl-3 py-3 font-weight-bold pn-text-heading" colspan="5">Grand Total / کل مجموعہ</td>
                                        <td class="py-3 text-right font-weight-bold text-c-red pn-stat-num-sm">
                                            {{ number_format($totalSpent, 0) }}
                                        </td>
                                        <td class="py-3 text-right font-weight-bold pn-text-heading">{{ number_format($totalPaid, 0) }}</td>
                                        <td class="py-3 text-right font-weight-bold text-danger">{{ number_format($totalPending, 0) }}</td>
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
                <div class="card border-0 shadow-sm mb-3 card-pn">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-c-blue2">
                            <i class="fas fa-filter mr-2"></i>Filters
                        </h6>
                    </div>
                    <div class="card-body py-3">
                        <form method="GET">
                            <select name="month" class="form-control mb-2 fc-pn" onchange="this.form.submit()">
                                <option value="">All Time</option>
                                @foreach($months as $m)
                                    <option value="{{ $m->value }}" {{ $selectedMonth == $m->value ? 'selected' : '' }}>
                                        {{ $m->label }}
                                    </option>
                                @endforeach
                            </select>
                            <select name="kind" class="form-control fc-pn" onchange="this.form.submit()">
                                <option value="">All Items</option>
                                @foreach(array_keys($packagingSizes) as $kind)
                                    <option value="{{ $kind }}" {{ $selectedKind === $kind ? 'selected' : '' }}>
                                        {{ $kindLabel($kind) }}s
                                    </option>
                                @endforeach
                            </select>
                        </form>
                        @if($selectedMonth || $selectedKind)
                            <a href="{{ route('admin.packaging-purchases.index') }}"
                               class="btn btn-block btn-sm btn-pn btn-clear-filter mt-2">
                                <i class="fas fa-times mr-1"></i> Clear Filters
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Breakdown by item --}}
                @if($purchases->count() > 0)
                <div class="card border-0 shadow-sm card-pn">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-c-red">
                            <i class="fas fa-chart-pie mr-2"></i>Spend by Item
                        </h6>
                    </div>
                    <div class="card-body py-3 px-3">
                        @php
                            $byItem = $purchases->groupBy(fn ($p) => $p->sizeLabel())
                                ->map(fn ($g) => $g->sum('grand_total'))
                                ->sortDesc();
                            $grandSum = $purchases->sum('grand_total');
                        @endphp
                        @foreach($byItem as $label => $amount)
                        @php $pct = $grandSum > 0 ? round(($amount / $grandSum) * 100) : 0; @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="breakdown-item">
                                    <i class="fas fa-box mr-1 icon-fw14 text-c-blue2"></i>
                                    <strong>{{ $label }}</strong>
                                </span>
                                <span class="breakdown-amount">
                                    <span class="breakdown-val">{{ number_format($amount, 0) }}</span>
                                    <span class="text-muted ml-1">{{ $pct }}%</span>
                                </span>
                            </div>
                            <div class="progress breakdown-bar">
                                <div class="progress-bar pbar pbar-blue" style="--w:{{ $pct }}%"></div>
                            </div>
                        </div>
                        @endforeach
                        <div class="d-flex justify-content-between align-items-center pt-2 mt-1 breakdown-grand-total-bar">
                            <span class="font-weight-bold pn-text-heading">Grand Total</span>
                            <span class="font-weight-bold text-c-red pn-stat-num-sm">
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
<div class="modal fade modal-pn" id="packagingModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="packagingForm">
            @csrf
            <input type="hidden" id="packaging_id" name="_packaging_id">
            <div class="modal-content">

                <div class="modal-header px-4 py-3">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-box-open mr-2"></i>Add Packaging Purchase
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body px-4 py-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Supplier / سپلائر <span class="text-danger">*</span>
                            </label>
                            <select name="vendor_id" id="vendor_id" class="form-control fc-pn" required>
                                <option value="">— Select Supplier —</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}{{ $vendor->shop ? ' ('.$vendor->shop.')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Purchase Date / خریداری کی تاریخ
                            </label>
                            <input type="date" name="purchase_date" id="purchase_date" class="form-control fc-pn">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Item Type / آئٹم کی قسم <span class="text-danger">*</span>
                            </label>
                            <select name="kind" id="kind" class="form-control fc-pn" required>
                                @foreach(array_keys($packagingSizes) as $kind)
                                    <option value="{{ $kind }}">{{ $kindLabel($kind) }} / {{ $kind === 'thaila' ? 'تھیلا' : 'پیکٹ' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Size / سائز <span class="text-danger">*</span>
                            </label>
                            <select name="size" id="size" class="form-control fc-pn" required></select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Quantity / تعداد <span class="text-danger">*</span>
                            </label>
                            <input type="number" step="1" name="quantity" id="quantity"
                                   class="form-control fc-pn" min="0" placeholder="0" required>
                            <small class="text-muted">Number of bags / packets purchased.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Rate per Unit (PKR) / نرخ فی عدد <span class="text-danger">*</span>
                            </label>
                            <input type="number" step="0.01" name="rate_per_unit" id="rate_per_unit"
                                   class="form-control fc-pn" min="0" placeholder="0.00" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Transport Cost (PKR) / نقل و حمل
                            </label>
                            <input type="number" step="0.01" name="transport_cost" id="transport_cost"
                                   class="form-control fc-pn" min="0" placeholder="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Grand Total (PKR) / کل مجموعہ
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text input-pre">PKR</span>
                                </div>
                                <input type="text" id="grand_total_display" class="form-control fc-lg-pn" readonly>
                            </div>
                            <small class="text-muted">= Quantity × Rate + Transport</small>
                        </div>
                        <div class="col-md-6 mb-3" id="amountPaidWrap">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Amount Paid Now (PKR) / ابھی ادا کی گئی رقم
                            </label>
                            <input type="number" step="0.01" name="amount_paid" id="amount_paid"
                                   class="form-control fc-pn" min="0" placeholder="0 (leave blank if unpaid)">
                            <small class="text-muted">Optional — leave blank to record the full amount as pending.</small>
                        </div>
                        <div class="col-md-6 mb-3" id="paidAccountWrap">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">
                                Paid From / کہاں سے ادا کیا
                            </label>
                            <select name="account_id" id="paid_account_id" class="form-control fc-pn">
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ $account->type === 'cash' ? 'selected' : '' }}>{{ $account->label() }}</option>
                                @endforeach
                                <option value="">Other / Not from Cash &amp; Bank (کیش/بینک سے نہیں)</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3 d-none" id="paidSummaryWrap">
                            <div class="p-3 rounded-3" style="background:#f8f9fc;border:1px solid #e9ecef;">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Paid so far</span>
                                    <strong id="paidSummaryPaid">—</strong>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span class="text-muted">Pending</span>
                                    <strong class="text-danger" id="paidSummaryPending">—</strong>
                                </div>
                                <small class="text-muted d-block mt-2">Use "Record Payment" on the list to add more payments.</small>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="is_investment" id="p_is_investment" value="1">
                                <label class="custom-control-label" for="p_is_investment">
                                    Mark as Investment / سرمایہ کاری کے طور پر نشان زد کریں
                                </label>
                            </div>
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

                <div class="modal-footer px-4 py-3">
                    <button type="button" class="btn btn-light px-4 btn-modal-cancel"
                            data-dismiss="modal" data-bs-dismiss="modal">
                        Cancel / منسوخ
                    </button>
                    <button class="btn btn-primary px-4 btn-modal-save" type="submit" id="submitBtn">
                        <i class="fas fa-save mr-1"></i> Save Purchase / محفوظ کریں
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

{{-- ===== RECORD PAYMENT MODAL ===== --}}
<div class="modal fade modal-pn" id="recordPaymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="recordPaymentForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header px-4 py-3">
                    <h5 class="modal-title"><i class="fas fa-hand-holding-dollar mr-2"></i>Record Payment</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <p class="mb-3">
                        <strong id="rp_vendor_name"></strong>
                        <span class="text-muted"> — Pending: </span>
                        <span class="font-weight-bold text-danger" id="rp_pending_display"></span>
                    </p>
                    <div class="mb-3">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control fc-pn" required>
                    </div>
                    <div class="mb-3">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" id="rp_payment_date" class="form-control fc-pn" required>
                    </div>
                    <div class="mb-3">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">Paid From</label>
                        <select name="account_id" id="rp_account_id" class="form-control fc-pn">
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ $account->type === 'cash' ? 'selected' : '' }}>{{ $account->label() }}</option>
                            @endforeach
                            <option value="">Other / Not from Cash &amp; Bank (کیش/بینک سے نہیں)</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">Note</label>
                        <textarea name="note" class="form-control fc-pn" rows="2" placeholder="Optional"></textarea>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3">
                    <button type="button" class="btn btn-light px-4 btn-modal-cancel" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary px-4 btn-modal-save" type="submit" id="rpSubmitBtn">
                        <i class="fas fa-save mr-1"></i> Save Payment
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ===== PAYMENT HISTORY MODAL ===== --}}
<div class="modal fade modal-pn" id="historyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header px-4 py-3">
                <h5 class="modal-title"><i class="fas fa-clock-rotate-left mr-2"></i>Payment History</h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body px-4 py-4">
                <table class="table table-sm mb-0" id="historyTable">
                    <thead>
                        <tr><th>Date</th><th class="text-right">Amount</th><th>Note</th><th></th></tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const PACKAGING_SIZES = @json($packagingSizes);

function sizeUnit(kind) {
    return kind === 'thaila' ? 'kg' : 'g';
}

// Repopulate the size dropdown for the chosen kind (thaila sizes are kg,
// packet sizes are grams), keeping `keep` selected when it's still valid.
function refreshSizes(keep) {
    const kind = $('#kind').val();
    const sizes = PACKAGING_SIZES[kind] || [];
    const unit = sizeUnit(kind);
    $('#size').html(sizes.map(s => '<option value="' + s + '">' + s + unit + '</option>').join(''));
    if (keep && sizes.map(String).includes(String(keep))) {
        $('#size').val(keep);
    }
}

function recalcGrandTotal() {
    const qty  = parseFloat($('#quantity').val())       || 0;
    const rate = parseFloat($('#rate_per_unit').val())  || 0;
    const tr   = parseFloat($('#transport_cost').val()) || 0;
    $('#grand_total_display').val((qty * rate + tr).toFixed(2));
}

$(function () {

    // DataTables throws on an empty (colspan "no records") table when
    // columnDefs targets a specific column index — only initialize when
    // there are real rows, otherwise the crash breaks every button below.
    if ($('#packagingTable tbody tr').not(':has(td[colspan])').length > 0) {
        $('#packagingTable').DataTable({
            paging: true,
            pageLength: 15,
            lengthChange: false,
            searching: true,
            ordering: false,
            info: true,
            autoWidth: false,
            responsive: true,
            columnDefs: [{ orderable: false, targets: [8] }, { responsivePriority: 1, targets: -1 }],
            language: {
                search: '',
                searchPlaceholder: 'Search packaging purchases...',
                info: 'Showing _START_–_END_ of _TOTAL_',
                paginate: { previous: '‹', next: '›' }
            }
        });
    }

    $('#kind').on('change', function () { refreshSizes(); });
    $('#quantity, #rate_per_unit, #transport_cost').on('input', recalcGrandTotal);

    // Open add modal
    $('#addBtn, #addBtnEmpty').on('click', function () {
        $('#packagingForm')[0].reset();
        $('#packaging_id').val('');
        $('#purchase_date').val(new Date().toISOString().split('T')[0]);
        refreshSizes();
        recalcGrandTotal();
        $('#amountPaidWrap').removeClass('d-none');
        $('#paidAccountWrap').removeClass('d-none');
        $('#paidSummaryWrap').addClass('d-none');
        $('#modalTitle').html('<i class="fas fa-box-open mr-2"></i>Add Packaging Purchase');
        $('#submitBtn').html('<i class="fas fa-save mr-1"></i> Save Purchase');
        $('#packagingModal').modal('show');
    });

    // Submit (add + edit)
    $('#packagingForm').on('submit', function (e) {
        e.preventDefault();
        const id  = $('#packaging_id').val();
        const url = id ? (APP_URL + '/packaging-purchases/' + id) : "{{ route('admin.packaging-purchases.store') }}";
        const btn = $('#submitBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        $.ajax({
            url, type: 'POST',
            data: $(this).serialize() + (id ? '&_method=PUT' : ''),
            success: function () {
                toastr.success('Packaging purchase saved!');
                $('#packagingModal').modal('hide');
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
        $.get(APP_URL + '/packaging-purchases/' + id + '/edit', function (p) {
            $('#packaging_id').val(p.id);
            $('#vendor_id').val(p.vendor_id);
            $('#purchase_date').val(p.purchase_date);
            $('#kind').val(p.kind);
            refreshSizes(p.size);
            $('#quantity').val(p.quantity);
            $('#rate_per_unit').val(p.rate_per_unit);
            $('#transport_cost').val(p.transport_cost);
            $('#remarks').val(p.remarks);
            $('#p_is_investment').prop('checked', !!p.is_investment);
            recalcGrandTotal();
            $('#amountPaidWrap').addClass('d-none');
            $('#paidAccountWrap').addClass('d-none');
            $('#paidSummaryWrap').removeClass('d-none');
            $('#paidSummaryPaid').text(Number(p.paid_amount).toLocaleString());
            $('#paidSummaryPending').text(Number(p.pending_amount).toLocaleString());
            $('#modalTitle').html('<i class="fas fa-edit mr-2"></i>Edit Packaging Purchase');
            $('#submitBtn').html('<i class="fas fa-save mr-1"></i> Update Purchase');
            $('#packagingModal').modal('show');
        });
    });

    // Delete
    $(document).on('click', '.deleteBtn', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Delete this packaging purchase?',
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
                url: APP_URL + '/packaging-purchases/' + id,
                type: 'POST',
                data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                success: function () {
                    $('#row_' + id).fadeOut(300, function () { $(this).remove(); });
                    toastr.success('Packaging purchase deleted.');
                },
                error: function (xhr) { toastr.error('Error: ' + xhr.status); }
            });
        });
    });

    // Record Payment: open modal
    $(document).on('click', '.recordPaymentBtn', function () {
        const btn = $(this);
        $('#recordPaymentForm')[0].reset();
        $('#recordPaymentForm').data('purchase-id', btn.data('id'));
        $('#rp_vendor_name').text(btn.data('vendor') || '');
        $('#rp_pending_display').text(Number(btn.data('pending')).toLocaleString());
        $('#rp_payment_date').val(new Date().toISOString().split('T')[0]);
        $('#recordPaymentModal').modal('show');
    });

    // Record Payment: submit
    $('#recordPaymentForm').on('submit', function (e) {
        e.preventDefault();
        const purchaseId = $(this).data('purchase-id');
        const btn = $('#rpSubmitBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        $.post(APP_URL + '/packaging-purchases/' + purchaseId + '/payments', $(this).serialize())
            .done(function () {
                toastr.success('Payment recorded!');
                $('#recordPaymentModal').modal('hide');
                setTimeout(() => location.reload(), 800);
            })
            .fail(function (xhr) {
                if (xhr.status === 422) {
                    alert((xhr.responseJSON.message) || Object.values(xhr.responseJSON.errors || {}).map(e => e[0]).join('\n'));
                } else {
                    toastr.error('Could not record payment.');
                }
            })
            .always(() => btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Payment'));
    });

    // Payment History: open + load
    $(document).on('click', '.historyBtn', function () {
        const purchaseId = $(this).data('id');
        $('#historyTable tbody').html('<tr><td colspan="4" class="text-center text-muted py-3">Loading...</td></tr>');
        $('#historyModal').modal('show');

        $.getJSON(APP_URL + '/packaging-purchases/' + purchaseId + '/payments', function (res) {
            const rows = res.payments || [];
            if (!rows.length) {
                $('#historyTable tbody').html('<tr><td colspan="4" class="text-center text-muted py-3">No payments recorded yet.</td></tr>');
                return;
            }
            let html = '';
            rows.forEach(function (p) {
                html += '<tr id="histRow' + p.id + '">'
                      +   '<td>' + (p.payment_date || '-') + '</td>'
                      +   '<td class="text-right">' + Number(p.amount).toLocaleString() + '</td>'
                      +   '<td>' + (p.note || '-') + '</td>'
                      +   '<td class="text-right">'
                      +     '<button class="btn btn-danger btn-xs deleteHistBtn" data-purchase="' + purchaseId + '" data-id="' + p.id + '">'
                      +       '<i class="fas fa-trash"></i>'
                      +     '</button>'
                      +   '</td>'
                      + '</tr>';
            });
            $('#historyTable tbody').html(html);
        });
    });

    // Payment History: delete a payment
    $(document).on('click', '.deleteHistBtn', function () {
        if (!confirm('Remove this payment?')) return;
        const purchaseId = $(this).data('purchase');
        const paymentId = $(this).data('id');
        $.ajax({
            url: APP_URL + '/packaging-purchases/' + purchaseId + '/payments/' + paymentId,
            type: 'POST',
            data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
            success: function () {
                toastr.success('Payment removed.');
                setTimeout(() => location.reload(), 600);
            },
            error: function () { toastr.error('Could not remove payment.'); }
        });
    });

});
</script>
@endsection
