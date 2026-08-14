@extends('admin.layout.app')
@section('title', 'Cash & Bank')

@php
$sourceMeta = [
    'sale_payment'     => ['label' => 'Sale Payment',   'short' => 'Sale Payments',    'badge' => 'badge-success',   'icon' => 'fa-cash-register'],
    'expense'          => ['label' => 'Expense',        'short' => 'Expenses',         'badge' => 'badge-danger',    'icon' => 'fa-receipt'],
    'purchase_payment' => ['label' => 'Vendor Payment',  'short' => 'Vendor Payments',  'badge' => 'badge-warning',   'icon' => 'fa-truck'],
    'employee_salary'  => ['label' => 'Salary/Advance',  'short' => 'Salary/Advances',  'badge' => 'badge-info',      'icon' => 'fa-users'],
    'manual'           => ['label' => 'Manual',          'short' => 'Manual Entries',   'badge' => 'badge-secondary', 'icon' => 'fa-pen'],
    'opening_balance'  => ['label' => 'Opening Balance', 'short' => 'Opening Balance',  'badge' => 'badge-dark',      'icon' => 'fa-flag'],
];
$typeMeta = [
    'cash'      => ['icon' => 'fa-money-bill-wave', 'color' => '#1a5c35'],
    'bank'      => ['icon' => 'fa-building-columns', 'color' => '#4e73df'],
    'jazzcash'  => ['icon' => 'fa-mobile-screen',   'color' => '#c9302c'],
    'easypaisa' => ['icon' => 'fa-mobile-screen',   'color' => '#1a8a8a'],
    'other'     => ['icon' => 'fa-wallet',          'color' => '#858796'],
];
$barColors = ['#1a5c35', '#1a8a8a', '#c9a227', '#7a4fbf', '#4e73df', '#858796'];
$netThisMonth = $thisMonthIn - $thisMonthOut;
$activeAccounts = $accounts->where('is_active', true);
$inactiveAccounts = $accounts->where('is_active', false);
@endphp

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Cash &amp; Bank <small class="text-muted pn-stat-sub">نقد اور بینک</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Cash &amp; Bank</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end">
                <button class="btn btn-outline-secondary btn-pn px-3 mr-2" id="addAccountBtn">
                    <i class="fas fa-plus mr-1"></i> Add Account
                </button>
                <button class="btn btn-primary btn-pn px-4" id="addManualBtn">
                    <i class="fas fa-plus mr-1"></i> Add Manual Entry
                </button>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        {{-- Total balance --}}
        <div class="card border-0 shadow-sm mb-3" style="background:linear-gradient(135deg,#0a2e18,#1a5c35,#2d7a4f); position:relative; overflow:hidden;">
            <i class="fas fa-wallet" style="position:absolute; right:24px; top:50%; transform:translateY(-50%); font-size:6rem; color:rgba(255,255,255,.08);"></i>
            <div class="card-body py-4 px-4 text-white" style="position:relative;">
                <div class="text-uppercase font-weight-bold mb-1" style="opacity:.85;font-size:.8rem;letter-spacing:.5px;">
                    Total Balance — All Accounts / کل بیلنس
                </div>
                <div class="font-weight-bold" style="font-size:2.6rem;line-height:1.1;">PKR {{ number_format($currentBalance, 0) }}</div>
                <div class="mt-2" style="opacity:.9;font-size:.85rem;">
                    This month:
                    <span class="font-weight-bold" style="{{ $netThisMonth < 0 ? 'color:#ffb3b3;' : '' }}">
                        {{ $netThisMonth >= 0 ? '+' : '' }}{{ number_format($netThisMonth, 0) }}
                    </span>
                    &nbsp;·&nbsp; {{ $activeAccounts->count() }} active account{{ $activeAccounts->count() != 1 ? 's' : '' }}
                </div>
            </div>
        </div>

        {{-- Per-account cards --}}
        <div class="row mb-3">
            @foreach($activeAccounts as $account)
                @php $tm = $typeMeta[$account->type] ?? $typeMeta['other']; @endphp
                <div class="col-6 col-md-4 col-lg-3 mb-3">
                    <div class="card border-0 shadow-sm h-100" style="border-top:3px solid {{ $tm['color'] }} !important;">
                        <div class="card-body py-3 px-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <i class="fas {{ $tm['icon'] }}" style="color:{{ $tm['color'] }};font-size:1.1rem;"></i>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link text-muted p-0" data-toggle="dropdown"><i class="fas fa-ellipsis-vertical"></i></button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item small setOpeningBtn" href="#"
                                           data-id="{{ $account->id }}" data-name="{{ $account->label() }}"
                                           data-amount="{{ $account->opening->amount ?? '' }}"
                                           data-date="{{ $account->opening?->transaction_date->format('Y-m-d') ?? date('Y-m-d') }}">
                                            <i class="fas fa-flag mr-1"></i> Set Opening Balance
                                        </a>
                                        <a class="dropdown-item small editAccountBtn" href="#"
                                           data-id="{{ $account->id }}" data-name="{{ $account->name }}"
                                           data-bank="{{ $account->bank_name }}" data-number="{{ $account->account_number }}">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </a>
                                        @if(!in_array($account->type, ['cash','jazzcash','easypaisa']) || $accounts->where('type',$account->type)->count() > 1)
                                        <a class="dropdown-item small text-danger deactivateAccountBtn" href="#" data-id="{{ $account->id }}">
                                            <i class="fas fa-ban mr-1"></i> Deactivate
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="font-weight-bold pn-text-heading" style="font-size:1.3rem;">
                                {{ number_format($account->balance, 0) }}
                            </div>
                            <div class="small text-muted text-truncate">{{ $account->label() }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($inactiveAccounts->count())
        <div class="mb-3">
            <small class="text-muted">Deactivated:
            @foreach($inactiveAccounts as $account)
                <span class="badge badge-light border mr-1">{{ $account->label() }}
                    <a href="#" class="reactivateAccountBtn text-primary ml-1" data-id="{{ $account->id }}" title="Reactivate"><i class="fas fa-rotate-left"></i></a>
                </span>
            @endforeach
            </small>
        </div>
        @endif

        {{-- Stat row --}}
        <div class="row mb-3">
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100 pn-bl-teal">
                    <div class="card-body py-3 px-3">
                        <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">All-Time In</div>
                        <div class="h5 mb-0 font-weight-bold text-success">+{{ number_format($allTimeIn, 0) }}</div>
                        <small class="text-muted">PKR received</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100 pn-bl-red">
                    <div class="card-body py-3 px-3">
                        <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">All-Time Out</div>
                        <div class="h5 mb-0 font-weight-bold text-danger">-{{ number_format($allTimeOut, 0) }}</div>
                        <small class="text-muted">PKR paid out</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100 pn-bl-blue">
                    <div class="card-body py-3 px-3">
                        <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">This Month In</div>
                        <div class="h5 mb-0 font-weight-bold text-success">+{{ number_format($thisMonthIn, 0) }}</div>
                        <small class="text-muted">{{ now()->format('F Y') }}</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100 pn-bl-yellow">
                    <div class="card-body py-3 px-3">
                        <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">This Month Out</div>
                        <div class="h5 mb-0 font-weight-bold text-danger">-{{ number_format($thisMonthOut, 0) }}</div>
                        <small class="text-muted">{{ now()->format('F Y') }}</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Where money comes from / goes --}}
        <div class="row mb-3">
            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm card-pn h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-c-blue2">
                            <i class="fas fa-arrow-down mr-2 text-success"></i>Where Money Comes From
                            @if($selectedMonth)<small class="text-muted font-weight-normal">— {{ \Carbon\Carbon::createFromFormat('Y-m',$selectedMonth)->format('F Y') }}</small>@endif
                        </h6>
                    </div>
                    <div class="card-body py-3 px-3">
                        @forelse($inBreakdown as $i => $row)
                            @php
                                $sm = $sourceMeta[$row->source_type] ?? ['short' => $row->source_type, 'icon' => 'fa-circle'];
                                $pct = $inTotal > 0 ? round(($row->total / $inTotal) * 100) : 0;
                                $color = $barColors[$i % count($barColors)];
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="breakdown-item"><i class="fas {{ $sm['icon'] }} mr-1 icon-fw14" style="color:{{ $color }};"></i><strong>{{ $sm['short'] }}</strong></span>
                                    <span class="breakdown-amount">
                                        <span class="breakdown-val">{{ number_format($row->total, 0) }}</span>
                                        <span class="text-muted ml-1">{{ $pct }}%</span>
                                    </span>
                                </div>
                                <div class="progress breakdown-bar"><div class="progress-bar" style="width:{{ $pct }}%;background:{{ $color }};"></div></div>
                            </div>
                        @empty
                            <p class="text-muted text-center py-3 mb-0">No money in recorded yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm card-pn h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-c-red">
                            <i class="fas fa-arrow-up mr-2 text-danger"></i>Where Money Goes
                            @if($selectedMonth)<small class="text-muted font-weight-normal">— {{ \Carbon\Carbon::createFromFormat('Y-m',$selectedMonth)->format('F Y') }}</small>@endif
                        </h6>
                    </div>
                    <div class="card-body py-3 px-3">
                        @forelse($outBreakdown as $i => $row)
                            @php
                                $sm = $sourceMeta[$row->source_type] ?? ['short' => $row->source_type, 'icon' => 'fa-circle'];
                                $pct = $outTotal > 0 ? round(($row->total / $outTotal) * 100) : 0;
                                $color = $barColors[$i % count($barColors)];
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="breakdown-item"><i class="fas {{ $sm['icon'] }} mr-1 icon-fw14" style="color:{{ $color }};"></i><strong>{{ $sm['short'] }}</strong></span>
                                    <span class="breakdown-amount">
                                        <span class="breakdown-val">{{ number_format($row->total, 0) }}</span>
                                        <span class="text-muted ml-1">{{ $pct }}%</span>
                                    </span>
                                </div>
                                <div class="progress breakdown-bar"><div class="progress-bar" style="width:{{ $pct }}%;background:{{ $color }};"></div></div>
                            </div>
                        @empty
                            <p class="text-muted text-center py-3 mb-0">No money out recorded yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Ledger table --}}
            <div class="col-lg-9 mb-3">
                <div class="card border-0 shadow-sm card-pn">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-c-blue2">
                            <i class="fas fa-list mr-2"></i>Transaction History / لین دین کی تفصیل
                        </h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="table-responsive">
                        <table class="table mb-0 pn-table pn-table-font">
                            <thead>
                                <tr>
                                    <th class="pl-3">Date</th>
                                    <th>Type</th>
                                    <th>Account</th>
                                    <th>Source</th>
                                    <th>Description</th>
                                    <th class="text-right">Amount</th>
                                    <th class="text-right">Balance</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($entries as $entry)
                                    @php $sm = $sourceMeta[$entry->source_type] ?? ['label' => $entry->source_type, 'badge' => 'badge-secondary', 'icon' => 'fa-circle']; @endphp
                                    <tr style="border-left:3px solid {{ $entry->type === 'in' ? '#1cc88a' : '#e74a3b' }};">
                                        <td class="pl-3 align-middle text-nowrap">{{ $entry->transaction_date->format('d M Y') }}</td>
                                        <td class="align-middle">
                                            @if($entry->type === 'in')
                                                <span class="badge badge-success"><i class="fas fa-arrow-down icon-9 mr-1"></i>In</span>
                                            @else
                                                <span class="badge badge-danger"><i class="fas fa-arrow-up icon-9 mr-1"></i>Out</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-nowrap">{{ $entry->account?->label() ?? '—' }}</td>
                                        <td class="align-middle text-nowrap">
                                            <span class="badge {{ $sm['badge'] }}"><i class="fas {{ $sm['icon'] }} mr-1"></i>{{ $sm['label'] }}</span>
                                            @if($entry->is_investment)
                                                <span class="badge badge-warning d-block mt-1"><i class="fas fa-piggy-bank mr-1"></i>Investment</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">{{ $entry->description }}{{ $entry->note ? ' — '.$entry->note : '' }}</td>
                                        <td class="align-middle text-right font-weight-bold {{ $entry->type === 'in' ? 'text-success' : 'text-danger' }} text-nowrap">
                                            {{ $entry->type === 'in' ? '+' : '-' }}{{ number_format($entry->amount, 0) }}
                                        </td>
                                        <td class="align-middle text-right font-weight-bold pn-text-heading text-nowrap">
                                            {{ number_format($entry->running_balance ?? 0, 0) }}
                                        </td>
                                        <td class="align-middle text-center">
                                            @if(in_array($entry->source_type, ['manual', 'opening_balance']))
                                                <button class="btn btn-danger btn-xs deleteLedgerBtn" data-id="{{ $entry->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="fas fa-wallet fa-3x mb-3 d-block icon-fade"></i>
                                            <p class="text-muted mb-0">No transactions found{{ $selectedMonth || $selectedSource || $selectedAccount || $selectedInvestment !== null && $selectedInvestment !== '' ? ' for this filter' : ' yet' }}.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        </div>
                        <div class="px-3 pb-2">{{ $entries->links() }}</div>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm card-pn mb-3">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-c-blue2">
                            <i class="fas fa-filter mr-2"></i>Filters
                        </h6>
                    </div>
                    <div class="card-body py-3">
                        <form method="GET" id="filterForm">
                            <label class="pn-label text-uppercase font-weight-bold text-muted small mb-1">Month</label>
                            <select name="month" class="form-control mb-3 fc-pn" onchange="document.getElementById('filterForm').submit()">
                                <option value="">All Time</option>
                                @foreach($months as $m)
                                    <option value="{{ $m->value }}" {{ $selectedMonth == $m->value ? 'selected' : '' }}>
                                        {{ $m->label }}
                                    </option>
                                @endforeach
                            </select>

                            <label class="pn-label text-uppercase font-weight-bold text-muted small mb-1">Account</label>
                            <select name="account" class="form-control mb-3 fc-pn" onchange="document.getElementById('filterForm').submit()">
                                <option value="">All Accounts</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ (string) $selectedAccount === (string) $account->id ? 'selected' : '' }}>{{ $account->label() }}</option>
                                @endforeach
                            </select>

                            <label class="pn-label text-uppercase font-weight-bold text-muted small mb-1">Source</label>
                            <select name="source" class="form-control mb-3 fc-pn" onchange="document.getElementById('filterForm').submit()">
                                <option value="">All Sources</option>
                                @foreach($sourceMeta as $key => $sm)
                                    <option value="{{ $key }}" {{ $selectedSource == $key ? 'selected' : '' }}>{{ $sm['short'] }}</option>
                                @endforeach
                            </select>

                            <label class="pn-label text-uppercase font-weight-bold text-muted small mb-1">Investment</label>
                            <select name="investment" class="form-control mb-2 fc-pn" onchange="document.getElementById('filterForm').submit()">
                                <option value="">All Transactions</option>
                                <option value="1" {{ $selectedInvestment === '1' ? 'selected' : '' }}>Investment Only</option>
                                <option value="0" {{ $selectedInvestment === '0' ? 'selected' : '' }}>Operating Only</option>
                            </select>
                        </form>
                        @if($selectedMonth || $selectedSource || $selectedAccount || $selectedInvestment !== null && $selectedInvestment !== '')
                            <a href="{{ route('admin.cash_ledger.index') }}"
                               class="btn btn-block btn-sm btn-pn btn-clear-filter mt-1">
                                <i class="fas fa-times mr-1"></i> Clear Filters
                            </a>
                        @endif
                    </div>
                </div>

                <div class="card border-0 shadow-sm card-pn">
                    <div class="card-body py-3 px-3">
                        <p class="text-muted small mb-2"><i class="fas fa-circle-info mr-1"></i>How this works</p>
                        <ul class="text-muted small pl-3 mb-0">
                            <li>Every payment picks an account (Cash, Bank, JazzCash, EasyPaisa) — its balance updates automatically.</li>
                            <li>Sale payments received add to that account; expenses, vendor payments, and salaries subtract.</li>
                            <li>Set each account's Opening Balance separately from its card menu.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- ===== ADD/EDIT ACCOUNT MODAL ===== --}}
<div class="modal fade modal-pn" id="accountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="accountForm">
            @csrf
            <input type="hidden" name="account_id" id="acc_id">
            <div class="modal-content">
                <div class="modal-header px-4 py-3">
                    <h5 class="modal-title" id="accountModalTitle"><i class="fas fa-plus mr-2"></i>Add Account</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <div class="mb-3">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">Account Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="acc_name" class="form-control fc-pn" placeholder="e.g. Meezan Bank - Current" required>
                    </div>
                    <div class="mb-3" id="acc_type_wrap">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">Type <span class="text-danger">*</span></label>
                        <select name="type" id="acc_type" class="form-control fc-pn" required>
                            <option value="bank">Bank</option>
                            <option value="cash">Cash</option>
                            <option value="jazzcash">JazzCash</option>
                            <option value="easypaisa">EasyPaisa</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3 d-none" id="acc_bank_wrap">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">Bank <span class="text-danger">*</span></label>
                        <select name="bank_name" id="acc_bank_name" class="form-control fc-pn">
                            @foreach(\App\Models\Account::BANKS as $bank)
                                <option value="{{ $bank }}">{{ $bank }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">Account Number / IBAN</label>
                        <input type="text" name="account_number" id="acc_number" class="form-control fc-pn" placeholder="Optional">
                    </div>
                </div>
                <div class="modal-footer px-4 py-3">
                    <button type="button" class="btn btn-light px-4 btn-modal-cancel" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary px-4 btn-modal-save" type="submit" id="accSubmitBtn">
                        <i class="fas fa-save mr-1"></i> Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ===== OPENING BALANCE MODAL ===== --}}
<div class="modal fade modal-pn" id="openingBalanceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="openingBalanceForm">
            @csrf
            <input type="hidden" name="account_id" id="ob_account_id">
            <div class="modal-content">
                <div class="modal-header px-4 py-3">
                    <h5 class="modal-title"><i class="fas fa-flag mr-2"></i>Set Opening Balance</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <p class="mb-3"><strong id="ob_account_name"></strong></p>
                    <p class="text-muted small">Enter what's actually in this account right now. This becomes its starting point — it doesn't replay old transactions.</p>
                    <div class="mb-3">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">Amount (PKR) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="amount" id="ob_amount" class="form-control fc-pn" required>
                    </div>
                    <div class="mb-0">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">As of Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" id="ob_date" class="form-control fc-pn" required>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3">
                    <button type="button" class="btn btn-light px-4 btn-modal-cancel" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary px-4 btn-modal-save" type="submit" id="obSubmitBtn">
                        <i class="fas fa-save mr-1"></i> Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ===== MANUAL ENTRY MODAL ===== --}}
<div class="modal fade modal-pn" id="manualModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="manualForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header px-4 py-3">
                    <h5 class="modal-title"><i class="fas fa-plus mr-2"></i>Add Manual Entry</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <p class="text-muted small">For money in/out that isn't a sale, expense, vendor, or salary payment — e.g. owner capital, bank charges, misc adjustment.</p>
                    <div class="mb-3">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">Account <span class="text-danger">*</span></label>
                        <select name="account_id" class="form-control fc-pn" required>
                            @foreach($activeAccounts as $account)
                                <option value="{{ $account->id }}">{{ $account->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-control fc-pn" required>
                            <option value="in">Money In</option>
                            <option value="out">Money Out</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">Amount (PKR) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control fc-pn" required>
                    </div>
                    <div class="mb-3">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control fc-pn" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">Description <span class="text-danger">*</span></label>
                        <input type="text" name="description" class="form-control fc-pn" placeholder="e.g. Owner capital injection" required>
                    </div>
                    <div class="mb-0">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">Note</label>
                        <textarea name="note" class="form-control fc-pn" rows="2" placeholder="Optional"></textarea>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3">
                    <button type="button" class="btn btn-light px-4 btn-modal-cancel" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary px-4 btn-modal-save" type="submit" id="manualSubmitBtn">
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

    // ── Add/Edit Account ──────────────────────────────
    $('#addAccountBtn').on('click', function () {
        $('#accountForm')[0].reset();
        $('#acc_id').val('');
        $('#acc_type_wrap').removeClass('d-none');
        $('#acc_type').prop('disabled', false).trigger('change');
        $('#accountModalTitle').html('<i class="fas fa-plus mr-2"></i>Add Account');
        $('#accountModal').modal('show');
    });

    $('#acc_type').on('change', function () {
        $('#acc_bank_wrap').toggleClass('d-none', $(this).val() !== 'bank');
    });

    $(document).on('click', '.editAccountBtn', function () {
        $('#accountForm')[0].reset();
        $('#acc_id').val($(this).data('id'));
        $('#acc_name').val($(this).data('name'));
        $('#acc_number').val($(this).data('number'));
        $('#acc_type_wrap').addClass('d-none'); // type can't change after creation
        $('#acc_bank_wrap').addClass('d-none');
        $('#accountModalTitle').html('<i class="fas fa-edit mr-2"></i>Edit Account');
        $('#accountModal').modal('show');
    });

    $('#accountForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#acc_id').val();
        const btn = $('#accSubmitBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        const url = id ? (APP_URL + '/accounts/' + id) : "{{ route('admin.accounts.store') }}";
        const data = $(this).serialize() + (id ? '&_method=PUT' : '');

        $.post(url, data)
            .done(function () {
                toastr.success('Account saved!');
                setTimeout(() => location.reload(), 600);
            })
            .fail(function (xhr) {
                alert(Object.values(xhr.responseJSON?.errors || {}).map(e => e[0]).join('\n') || 'Something went wrong.');
            })
            .always(() => btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save'));
    });

    $(document).on('click', '.deactivateAccountBtn', function (e) {
        e.preventDefault();
        if (!confirm('Deactivate this account? It will stop appearing in new payment forms.')) return;
        const id = $(this).data('id');
        $.ajax({
            url: APP_URL + '/accounts/' + id, type: 'POST',
            data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
            success: function (res) {
                toastr.success(res.deactivated ? res.message : 'Account removed.');
                setTimeout(() => location.reload(), 600);
            },
            error: function () { toastr.error('Could not deactivate account.'); }
        });
    });

    $(document).on('click', '.reactivateAccountBtn', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        $.post(APP_URL + '/accounts/' + id + '/reactivate', { _token: '{{ csrf_token() }}' })
            .done(function () { toastr.success('Account reactivated.'); setTimeout(() => location.reload(), 500); })
            .fail(function () { toastr.error('Could not reactivate account.'); });
    });

    // ── Opening Balance (per account) ─────────────────
    $(document).on('click', '.setOpeningBtn', function (e) {
        e.preventDefault();
        $('#ob_account_id').val($(this).data('id'));
        $('#ob_account_name').text($(this).data('name'));
        $('#ob_amount').val($(this).data('amount'));
        $('#ob_date').val($(this).data('date'));
        $('#openingBalanceModal').modal('show');
    });

    $('#openingBalanceForm').on('submit', function (e) {
        e.preventDefault();
        const btn = $('#obSubmitBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');
        $.post("{{ route('admin.cash_ledger.opening_balance') }}", $(this).serialize())
            .done(function () {
                toastr.success('Opening balance saved!');
                setTimeout(() => location.reload(), 600);
            })
            .fail(function (xhr) {
                alert(Object.values(xhr.responseJSON?.errors || {}).map(e => e[0]).join('\n') || 'Something went wrong.');
            })
            .always(() => btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save'));
    });

    // ── Manual Entry ───────────────────────────────────
    $('#addManualBtn').on('click', function () {
        $('#manualForm')[0].reset();
        $('#manualModal').modal('show');
    });

    $('#manualForm').on('submit', function (e) {
        e.preventDefault();
        const btn = $('#manualSubmitBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');
        $.post("{{ route('admin.cash_ledger.manual.store') }}", $(this).serialize())
            .done(function () {
                toastr.success('Entry added!');
                setTimeout(() => location.reload(), 600);
            })
            .fail(function (xhr) {
                alert(Object.values(xhr.responseJSON?.errors || {}).map(e => e[0]).join('\n') || 'Something went wrong.');
            })
            .always(() => btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save'));
    });

    $(document).on('click', '.deleteLedgerBtn', function () {
        if (!confirm('Remove this entry?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: APP_URL + '/cash-ledger/' + id,
            type: 'POST',
            data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
            success: function () {
                toastr.success('Entry removed.');
                setTimeout(() => location.reload(), 500);
            },
            error: function (xhr) {
                toastr.error(xhr.responseJSON?.message || 'Could not remove entry.');
            }
        });
    });

});
</script>
@endsection
