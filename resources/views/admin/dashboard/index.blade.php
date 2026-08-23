@extends('admin.layout.app')
@section('title', 'Dashboard')
@section('content')

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Dashboard <small class="text-muted ch-sub">ڈیش بورڈ</small></h1>
                <p class="text-muted mb-0 ch-desc">PAK NAMAK &amp; MASALA JAAT PRIVATE LIMITED</p>
            </div>
            <div class="col-sm-6">
                <form method="GET" id="monthFilterForm" class="d-flex justify-content-end align-items-center">
                    <label class="mb-0 mr-2 text-muted ch-desc text-nowrap">
                        <i class="fas fa-calendar-alt mr-1"></i> Month / مہینہ
                    </label>
                    <select name="month" class="form-control fc-pn month-sel"
                            onchange="document.getElementById('monthFilterForm').submit()">
                        @foreach($months as $month)
                            <option value="{{ $month['value'] }}" {{ $selectedMonth == $month['value'] ? 'selected' : '' }}>
                                {{ $month['label'] }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        {{-- ═══════════════════════════════════════════════════════════
             COMMON — shared across Salt and Spices, shown before the tabs
        ═══════════════════════════════════════════════════════════ --}}

        {{-- ── QUICK ACTIONS ────────────────────────────── --}}
        <div class="row mb-3">
            <div class="col-12 d-flex flex-wrap">
                <a href="{{ route('admin.sales.create') }}" class="btn btn-pn px-4 mr-2 mb-2 text-white" style="background:linear-gradient(135deg,#1a5c35,#2d7a4f);">
                    <i class="fas fa-plus mr-1"></i> Add Sale / فروخت
                </a>
                <a href="{{ route('admin.spice-sales.create') }}" class="btn btn-pn px-4 mr-2 mb-2 text-white" style="background:linear-gradient(135deg,#4a2f7a,#7a4fbf);">
                    <i class="fas fa-pepper-hot mr-1"></i> Add Spice Sale / مصالحہ فروخت
                </a>
                <a href="{{ route('admin.shops.index') }}?open=create" class="btn btn-pn px-4 mr-2 mb-2 text-white" style="background:linear-gradient(135deg,#3a5fc9,#4e73df);">
                    <i class="fas fa-store mr-1"></i> Add Shop / دکان
                </a>
                <a href="{{ route('admin.stocks.index') }}" class="btn btn-pn px-4 mr-2 mb-2 text-white" style="background:linear-gradient(135deg,#0d5c5c,#1a8a8a);">
                    <i class="fas fa-warehouse mr-1"></i> Add Stock / اسٹاک
                </a>
                <a href="{{ route('order.form') }}" target="_blank" rel="noopener" class="btn btn-pn px-4 mr-2 mb-2 text-white" style="background:linear-gradient(135deg,#b8391f,#e74a3b);">
                    <i class="fas fa-cart-plus mr-1"></i> Add Order / آرڈر
                </a>
                <a href="{{ route('admin.shops.payment_form') }}" class="btn btn-pn px-4 mr-2 mb-2 text-white" style="background:linear-gradient(135deg,#0d5c5c,#1a8a8a);">
                    <i class="fas fa-hand-holding-dollar mr-1"></i> Record Payment / ادائیگی
                </a>
                <a href="{{ route('admin.employees.advance_form') }}" class="btn btn-pn px-4 mb-2 text-white" style="background:linear-gradient(135deg,#8a6d0d,#c9a227);">
                    <i class="fas fa-hand-holding-dollar mr-1"></i> Advance / ایڈوانس
                </a>
            </div>
        </div>

        {{-- ── CASH & BANK BALANCE + INVESTMENT ────────────────────────────── --}}
        <div class="row mb-4">
            <div class="col-lg-6 mb-3 mb-lg-0">
                <a href="{{ route('admin.cash_ledger.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100" style="background:linear-gradient(135deg,#1a5c35,#2d7a4f);">
                        <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between text-white">
                            <div>
                                <div class="text-uppercase font-weight-bold mb-1" style="opacity:.85;font-size:.75rem;">
                                    <i class="fas fa-wallet mr-1"></i> Cash &amp; Bank Balance / نقد اور بینک بیلنس
                                </div>
                                <div class="font-weight-bold" style="font-size:1.7rem;">PKR {{ number_format($cashBalance, 0) }}</div>
                            </div>
                            <i class="fas fa-chevron-right" style="opacity:.7;"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 mb-3 mb-lg-0">
                <a href="{{ route('admin.investments.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100" style="background:linear-gradient(135deg,#8a6d0d,#c9a227);">
                        <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between text-white">
                            <div>
                                <div class="text-uppercase font-weight-bold mb-1" style="opacity:.85;font-size:.75rem;">
                                    <i class="fas fa-piggy-bank mr-1"></i> Total Investment / کل سرمایہ کاری
                                </div>
                                <div class="font-weight-bold" style="font-size:1.5rem;">PKR {{ number_format($totalInvestment, 0) }}</div>
                            </div>
                            <i class="fas fa-chevron-right" style="opacity:.7;"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3">
                <a href="{{ route('admin.investments.index', ['month' => $selectedMonth]) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100" style="background:linear-gradient(135deg,#b8860b,#e0ac2b);">
                        <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between text-white">
                            <div>
                                <div class="text-uppercase font-weight-bold mb-1" style="opacity:.85;font-size:.75rem;">
                                    <i class="fas fa-calendar-check mr-1"></i> Monthly Investment / ماہانہ سرمایہ کاری
                                </div>
                                <div class="font-weight-bold" style="font-size:1.5rem;">PKR {{ number_format($monthInvestmentTotal, 0) }}</div>
                            </div>
                            <i class="fas fa-chevron-right" style="opacity:.7;"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- ── ENTITY QUICK COUNTS ────────────────────────────── --}}
        <div class="row mb-4">
            <div class="col-xl-4 col-md-4 mb-3">
                <a href="{{ route('admin.shops.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 pn-bl-green">
                        <div class="card-body d-flex align-items-center justify-content-between py-3 px-4">
                            <div>
                                <div class="pn-stat-lbl">Active Shops / فعال دکانیں</div>
                                <div class="pn-stat-num-lg text-c-green">{{ $activeShopsCount }}</div>
                                <div class="pn-stat-sub">of {{ $totalShops }} total</div>
                            </div>
                            <i class="fas fa-store card-bg-icon text-c-green"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-4 col-md-4 mb-3">
                <a href="{{ route('admin.vendors.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 pn-bl-purple">
                        <div class="card-body d-flex align-items-center justify-content-between py-3 px-4">
                            <div>
                                <div class="pn-stat-lbl">Vendors / Suppliers / فروش کار</div>
                                <div class="pn-stat-num-lg text-c-purple">{{ $totalVendors }}</div>
                                <div class="pn-stat-sub">registered suppliers</div>
                            </div>
                            <i class="fas fa-truck card-bg-icon text-c-purple"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-4 col-md-4 mb-3">
                <a href="{{ route('admin.employees.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 pn-bl-teal">
                        <div class="card-body d-flex align-items-center justify-content-between py-3 px-4">
                            <div>
                                <div class="pn-stat-lbl">Working Employees / کام کرنے والے ملازمین</div>
                                <div class="pn-stat-num-lg text-c-teal">{{ $workingEmployees }}</div>
                                <div class="pn-stat-sub">currently working</div>
                            </div>
                            <i class="fas fa-users card-bg-icon text-c-teal"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- ── ALL-TIME SNAPSHOT (salt + spices combined) ─────── --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <div class="pn-section-lbl">Overall / مجموعی</div>
                <h5 class="mb-0 font-weight-bold pn-section-title">Total Snapshot <small class="text-muted ch-desc">کل جائزہ — نمک اور مصالحہ جات ملا کر</small></h5>
            </div>
        </div>

        <div class="row mb-4">
            {{-- Total Sales --}}
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100 card-pn">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="pn-icon pn-icon-sm pnis-green mr-2 flex-shrink-0">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div>
                                <div class="pn-tbl-lbl">Total Sales / کل فروخت</div>
                                <div class="pn-stat-num-sm text-c-dgreen">{{ number_format($totalSales, 0) }}</div>
                                <div class="pn-stat-sub">{{ $totalSalesCount + $totalSpiceSalesCount }} invoices</div>
                            </div>
                        </div>
                    </div>
                    <div class="snap-bt snap-bt-green"></div>
                </div>
            </div>

            {{-- Total Purchases --}}
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100 card-pn">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="pn-icon pn-icon-sm pnis-yellow mr-2 flex-shrink-0">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div>
                                <div class="pn-tbl-lbl">Total Purchases / کل خریداری</div>
                                <div class="pn-stat-num-sm text-c-orange2">{{ number_format($PurchasesTotal, 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="snap-bt snap-bt-yellow"></div>
                </div>
            </div>

            {{-- Total Expenses --}}
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100 card-pn">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="pn-icon pn-icon-sm pnis-red mr-2 flex-shrink-0">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <div>
                                <div class="pn-tbl-lbl">Total Expenses / کل اخراجات</div>
                                <div class="pn-stat-num-sm text-c-red">{{ number_format($totalExpenses, 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="snap-bt snap-bt-red"></div>
                </div>
            </div>

            {{-- Total Salaries --}}
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100 card-pn">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="pn-icon pn-icon-sm pnis-blue mr-2 flex-shrink-0">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div>
                                <div class="pn-tbl-lbl">Total Salaries / کل تنخواہیں</div>
                                <div class="pn-stat-num-sm text-c-blue">{{ number_format($totalSalaryPaid, 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="snap-bt snap-bt-blue"></div>
                </div>
            </div>

            {{-- Total Pending --}}
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100 card-pn">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="pn-icon pn-icon-sm pnis-orange mr-2 flex-shrink-0">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <div class="pn-tbl-lbl">Total Pending اُدھار</div>
                                <div class="pn-stat-num-sm text-c-orange">{{ number_format($totalPending, 0) }}</div>
                                @php $pendingPct = $totalSales > 0 ? round(($totalPending/$totalSales)*100,1) : 0; @endphp
                                <div class="pn-stat-sub">{{ $pendingPct }}% of sales</div>
                            </div>
                        </div>
                    </div>
                    <div class="snap-bt snap-bt-orange"></div>
                </div>
            </div>

            {{-- Total Profit / Loss --}}
            @php
                $tplCardClass = $totalProfitLoss >= 0 ? 'snap-profit' : 'snap-loss';
                $tplIcon      = $totalProfitLoss >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                $tplLabel     = $totalProfitLoss >= 0 ? 'Total Profit' : 'Total Loss';
            @endphp
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100 card-pn {{ $tplCardClass }}">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="pn-icon pn-icon-sm snap-icon mr-2 flex-shrink-0">
                                <i class="fas {{ $tplIcon }}"></i>
                            </div>
                            <div>
                                <div class="pn-tbl-lbl snap-lbl">{{ $tplLabel }}</div>
                                <div class="pn-stat-num-sm snap-val">{{ number_format(abs($totalProfitLoss), 0) }}</div>
                                <div class="pn-stat-sub snap-lbl">all time</div>
                            </div>
                        </div>
                    </div>
                    <div class="snap-bt"></div>
                </div>
            </div>
        </div>

        {{-- ── MONTHLY FINANCIALS (salt + spices combined) ────── --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <div class="pn-section-lbl">Monthly Snapshot / ماہانہ جائزہ</div>
                <h5 class="mb-0 font-weight-bold pn-section-title">
                    {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y') }}
                    <small class="text-muted ch-desc">— نمک اور مصالحہ جات ملا کر</small>
                </h5>
            </div>
            <a href="{{ route('admin.sales.index', ['month' => $selectedMonth]) }}"
               class="btn btn-sm btn-outline-primary btn-rounded pn-label">
                <i class="fas fa-external-link-alt mr-1"></i> View Sales / فروخت دیکھیں
            </a>
        </div>

        <div class="row mb-4">
            {{-- Sales --}}
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100 card-pn">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="pn-icon pn-icon-sm pnis-green mr-2 flex-shrink-0">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div>
                                <div class="pn-tbl-lbl">Sales / فروخت</div>
                                <div class="pn-stat-num-sm text-c-dgreen">{{ number_format($monthSalesTotal, 0) }}</div>
                                <div class="pn-stat-sub">{{ $monthSalesCount + $monthSpiceSalesCount }} invoices</div>
                            </div>
                        </div>
                    </div>
                    <div class="snap-bt snap-bt-green"></div>
                </div>
            </div>

            {{-- Purchases --}}
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100 card-pn">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="pn-icon pn-icon-sm pnis-yellow mr-2 flex-shrink-0">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div>
                                <div class="pn-tbl-lbl">Purchases / خریداری</div>
                                <div class="pn-stat-num-sm text-c-orange2">{{ number_format($monthPurchasesTotal, 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="snap-bt snap-bt-yellow"></div>
                </div>
            </div>

            {{-- Expenses --}}
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100 card-pn">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="pn-icon pn-icon-sm pnis-red mr-2 flex-shrink-0">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <div>
                                <div class="pn-tbl-lbl">Expenses / اخراجات</div>
                                <div class="pn-stat-num-sm text-c-red">{{ number_format($monthExpensesTotal, 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="snap-bt snap-bt-red"></div>
                </div>
            </div>

            {{-- Salaries --}}
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100 card-pn">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="pn-icon pn-icon-sm pnis-blue mr-2 flex-shrink-0">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div>
                                <div class="pn-tbl-lbl">Salaries / تنخواہیں</div>
                                <div class="pn-stat-num-sm text-c-blue">{{ number_format($monthSalaryTotal, 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="snap-bt snap-bt-blue"></div>
                </div>
            </div>

            {{-- Pending / Udhaar --}}
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100 card-pn">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="pn-icon pn-icon-sm pnis-orange mr-2 flex-shrink-0">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <div class="pn-tbl-lbl">Pending اُدھار</div>
                                <div class="pn-stat-num-sm text-c-orange">{{ number_format($monthPending, 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="snap-bt snap-bt-orange"></div>
                </div>
            </div>

            {{-- Profit / Loss --}}
            @php
                $plCardClass = $profitLoss >= 0 ? 'snap-profit' : 'snap-loss';
                $plIcon      = $profitLoss >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                $plLabel     = $profitLoss >= 0 ? 'Profit' : 'Loss';
            @endphp
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100 card-pn {{ $plCardClass }}">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="pn-icon pn-icon-sm snap-icon mr-2 flex-shrink-0">
                                <i class="fas {{ $plIcon }}"></i>
                            </div>
                            <div>
                                <div class="pn-tbl-lbl snap-lbl">{{ $plLabel }}</div>
                                <div class="pn-stat-num-sm snap-val">{{ number_format(abs($profitLoss), 0) }}</div>
                                <div class="pn-stat-sub snap-lbl">this month</div>
                            </div>
                        </div>
                    </div>
                    <div class="snap-bt"></div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             TABS — Salt vs Chilli & Spices, each with its own detail
        ═══════════════════════════════════════════════════════════ --}}

        <ul class="nav nav-tabs pn-dash-tabs mb-3" id="dashProductTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="salt-tab" data-toggle="tab" data-bs-toggle="tab"
                   href="#salt-panel" role="tab" aria-controls="salt-panel" aria-selected="true">
                    <i class="fas fa-cube mr-1"></i> Salt <small class="d-none d-sm-inline">/ نمک</small>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="spice-tab" data-toggle="tab" data-bs-toggle="tab"
                   href="#spice-panel" role="tab" aria-controls="spice-panel" aria-selected="false">
                    <i class="fas fa-pepper-hot mr-1"></i> Chilli &amp; Spices <small class="d-none d-sm-inline">/ مصالحہ جات</small>
                </a>
            </li>
        </ul>

        <div class="tab-content" id="dashProductTabsContent">

        {{-- ═══════════════════════════ SALT TAB ═══════════════════════════ --}}
        <div class="tab-pane fade show active" id="salt-panel" role="tabpanel" aria-labelledby="salt-tab">

        {{-- ── SALT SALES + ORDERS SUMMARY ─────────────────────── --}}
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <a href="{{ route('admin.sales.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 pn-bl-yellow">
                        <div class="card-body d-flex align-items-center justify-content-between py-3 px-4">
                            <div>
                                <div class="pn-stat-lbl">Total Sale Invoices / کل فروخت بل</div>
                                <div class="pn-stat-num-lg text-c-warn">{{ $totalSalesCount }}</div>
                                <div class="pn-stat-sub">all-time — {{ number_format($totalSaltSales, 0) }} PKR</div>
                            </div>
                            <i class="fas fa-file-invoice card-bg-icon text-c-warn"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 pn-bl-yellow">
                        <div class="card-body d-flex align-items-center justify-content-between py-3 px-4">
                            <div>
                                <div class="pn-stat-lbl">Pending Orders / زیر التواء آرڈرز</div>
                                <div class="pn-stat-num-lg text-c-warn">{{ $pendingOrdersCount }}</div>
                                <div class="pn-stat-sub">awaiting action</div>
                            </div>
                            <i class="fas fa-clock card-bg-icon text-c-warn"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <a href="{{ route('admin.orders.index', ['status' => 'confirmed']) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 pn-bl-teal">
                        <div class="card-body d-flex align-items-center justify-content-between py-3 px-4">
                            <div>
                                <div class="pn-stat-lbl">Confirmed Orders / تصدیق شدہ آرڈرز</div>
                                <div class="pn-stat-num-lg text-c-teal">{{ $confirmedOrdersCount }}</div>
                                <div class="pn-stat-sub">confirmed</div>
                            </div>
                            <i class="fas fa-check-circle card-bg-icon text-c-teal"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <a href="{{ route('admin.orders.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 pn-bl-green">
                        <div class="card-body d-flex align-items-center justify-content-between py-3 px-4">
                            <div>
                                <div class="pn-stat-lbl">Total Orders / کل آرڈرز</div>
                                <div class="pn-stat-num-lg text-c-green">{{ $totalOrdersCount }}</div>
                                <div class="pn-stat-sub">all time</div>
                            </div>
                            <i class="fas fa-inbox card-bg-icon text-c-green"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- ── PRODUCT TYPE BREAKDOWN (MONTH) ────────────────── --}}
        @php
            $prodTotal = $monthDallaTotal + $monthThailaTotal + $monthPackageTotal ?: 1;
            $dallaPct  = round(($monthDallaTotal / $prodTotal) * 100, 1);
            $thailaPct = round(($monthThailaTotal / $prodTotal) * 100, 1);
            $pkgPct    = round(($monthPackageTotal / $prodTotal) * 100, 1);
        @endphp
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm card-pn">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <div class="pn-tbl-lbl">Dalla — ڈلہ</div>
                                <div class="pn-stat-num-md text-c-green">PKR {{ number_format($monthDallaTotal, 0) }}</div>
                            </div>
                            <span class="badge badge-primary pn-bdg">{{ $dallaPct }}%</span>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-primary pbar" style="--w:{{ $dallaPct }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm card-pn">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <div class="pn-tbl-lbl">Thaila — تھیلا</div>
                                <div class="pn-stat-num-md text-c-teal">PKR {{ number_format($monthThailaTotal, 0) }}</div>
                            </div>
                            <span class="badge badge-success pn-bdg">{{ $thailaPct }}%</span>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-success pbar" style="--w:{{ $thailaPct }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm card-pn">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <div class="pn-tbl-lbl">Package — پیکٹ</div>
                                <div class="pn-stat-num-md text-c-warn">PKR {{ number_format($monthPackageTotal, 0) }}</div>
                            </div>
                            <span class="badge badge-warning pn-bdg">{{ $pkgPct }}%</span>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-warning pbar" style="--w:{{ $pkgPct }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── RECENT PENDING ORDERS ──────────────────────────── --}}
        @if($recentPendingOrders->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm card-pn">
                    <div class="card-header border-0 d-flex justify-content-between align-items-center py-3 ch-yellow">
                        <div>
                            <div class="pn-stat-lbl">Action Required / کارروائی درکار</div>
                            <h6 class="mb-0 font-weight-bold text-c-warn">
                                <i class="fas fa-clock mr-1"></i> Pending Orders / زیر التواء آرڈرز
                                <span class="badge badge-warning ml-1 pn-bdg">{{ $pendingOrdersCount }}</span>
                            </h6>
                        </div>
                        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}"
                           class="btn btn-sm btn-outline-warning btn-rounded pn-label">
                            View All / سب دیکھیں
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0 pn-table pn-table-font">
                                <thead>
                                    <tr>
                                        <th class="pl-3">Reference / حوالہ</th>
                                        <th>Shop / Customer / دکان</th>
                                        <th>Items / اشیاء</th>
                                        <th>Submitted / جمع تاریخ</th>
                                        <th class="text-center">Actions / اقدامات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($recentPendingOrders as $order)
                                    <tr>
                                        <td class="pl-3 align-middle">
                                            <a href="{{ route('admin.orders.show', $order) }}"
                                               class="font-weight-bold text-c-green text-decoration-none">
                                                {{ $order->reference }}
                                            </a>
                                        </td>
                                        <td class="align-middle">
                                            <span class="font-weight-bold d-block pn-text-heading">
                                                {{ $order->display_name }}
                                                @if(!$order->shop_id)
                                                    <span class="badge badge-secondary ml-1 badge-new-shop">New</span>
                                                @endif
                                            </span>
                                            <small class="text-muted">{{ $order->display_phone }}</small>
                                        </td>
                                        <td class="align-middle">
                                            @foreach($order->items->take(2) as $item)
                                                <span class="badge mr-1 badge-item-{{ $item->type }}">
                                                    {{ $item->quantity }}×
                                                    @if($item->type==='dalla') Dalla
                                                    @elseif($item->type==='thaila') {{ $item->size }}kg
                                                    @else {{ $item->size }}g
                                                    @endif
                                                </span>
                                            @endforeach
                                            @if($order->items->count() > 2)
                                                <small class="text-muted">+{{ $order->items->count()-2 }} more</small>
                                            @endif
                                        </td>
                                        <td class="align-middle pn-text-heading">{{ $order->created_at->format('d M, h:i A') }}</td>
                                        <td class="align-middle text-center text-nowrap">
                                            <a href="{{ route('admin.orders.show', $order) }}"
                                               class="btn btn-sm btn-pn btn-act-gview mr-1" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.orders.confirm', $order) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-pn btn-act-confirm mr-1" title="Confirm">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.orders.reject', $order) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-pn btn-act-delete" title="Reject">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- ── TOP SHOPS + TOP MONTHS ──────────────────────────── --}}
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100 card-pn">
                    <div class="card-header border-0 d-flex justify-content-between align-items-center py-3 ch-green">
                        <div>
                            <div class="pn-stat-lbl">Top 5</div>
                            <h6 class="mb-0 font-weight-bold text-c-green">Shops by Revenue / آمدن کے مطابق دکانیں</h6>
                        </div>
                        <a href="{{ route('admin.shops.index') }}"
                           class="btn btn-sm btn-outline-primary btn-rounded pn-label">
                            All Shops / تمام دکانیں
                        </a>
                    </div>
                    <div class="card-body p-0">
                        @php $maxShop = $topShops->first()->total ?? 1; @endphp
                        @forelse($topShops as $i => $row)
                            @php $shopPct = $maxShop > 0 ? round(($row->total / $maxShop) * 100) : 0; @endphp
                            <div class="px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="d-flex align-items-center">
                                        <span class="badge mr-2 badge-rank-green">{{ $i + 1 }}</span>
                                        <div>
                                            <div class="font-weight-bold pn-table-font">{{ $row->shop_name }}</div>
                                            @if($row->shop_phone_number)
                                                <div class="text-muted pn-stat-sub">
                                                    <i class="fas fa-phone mr-1 icon-9"></i>{{ $row->shop_phone_number }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="font-weight-bold pn-table-font text-c-green">
                                        PKR {{ number_format($row->total, 0) }}
                                    </span>
                                </div>
                                <div class="progress progress-xs">
                                    <div class="progress-bar pbar bg-primary" style="--w:{{ $shopPct }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-store fa-2x mb-2 d-block icon-fade-soft"></i>
                                No sales data yet
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100 card-pn">
                    <div class="card-header border-0 d-flex justify-content-between align-items-center py-3 ch-teal">
                        <div>
                            <div class="pn-stat-lbl">Top 5</div>
                            <h6 class="mb-0 font-weight-bold text-c-teal">Best Sales Months / بہترین فروخت کے مہینے</h6>
                        </div>
                        <a href="{{ route('admin.sales.report') }}"
                           class="btn btn-sm btn-outline-success btn-rounded pn-label">
                            Full Report / مکمل رپورٹ
                        </a>
                    </div>
                    <div class="card-body p-0">
                        @php $maxMonth = $topDays->first()->total ?? 1; @endphp
                        @forelse($topDays as $i => $row)
                            @php $moPct = $maxMonth > 0 ? round(($row->total / $maxMonth) * 100) : 0; @endphp
                            <div class="px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-success mr-2 badge-circle">{{ $i + 1 }}</span>
                                        <div class="font-weight-bold pn-table-font">
                                            {{ \Carbon\Carbon::createFromFormat('Y-m', $row->day)->format('F Y') }}
                                        </div>
                                    </div>
                                    <span class="font-weight-bold pn-table-font text-c-teal">
                                        PKR {{ number_format($row->total, 0) }}
                                    </span>
                                </div>
                                <div class="progress progress-xs">
                                    <div class="progress-bar pbar bg-success" style="--w:{{ $moPct }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-calendar fa-2x mb-2 d-block icon-fade-soft"></i>
                                No sales data yet
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ── CITY & AREA SALES BREAKDOWN ────────────────────── --}}
        @if($citySales->count() > 0 || $areaSales->count() > 0)
        <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
            <div>
                <div class="pn-section-lbl">Location Breakdown / علاقائی جائزہ</div>
                <h5 class="mb-0 font-weight-bold pn-section-title">
                    Sales by City &amp; Area
                    <small class="text-muted ch-desc">— {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y') }}</small>
                </h5>
            </div>
            <a href="{{ route('admin.cities.index') }}"
               class="btn btn-sm btn-outline-primary btn-rounded pn-label">
                <i class="fas fa-city mr-1"></i> Manage Cities
            </a>
        </div>

        <div class="row mb-4">

            {{-- CITY BREAKDOWN --}}
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100 card-pn">
                    <div class="card-header border-0 d-flex justify-content-between align-items-center py-3 ch-green">
                        <div>
                            <div class="pn-stat-lbl">By City / شہر کے مطابق</div>
                            <h6 class="mb-0 font-weight-bold text-c-green">
                                <i class="fas fa-city mr-1"></i> City-wise Sales / شہر وار فروخت
                            </h6>
                        </div>
                        <span class="pn-bdg pn-bdg-teal">
                            {{ $citySales->count() }} {{ Str::plural('city', $citySales->count()) }}
                        </span>
                    </div>
                    @if($citySales->count() > 0)
                    @php $maxCity = $citySales->first()->total ?: 1; @endphp
                    <div class="card-body p-0">
                        @foreach($citySales as $i => $row)
                        @php $cityPct = $maxCity > 0 ? round(($row->total / $maxCity) * 100) : 0; @endphp
                        <div class="px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div class="d-flex align-items-center">
                                    <span class="badge mr-2 badge-rank-green">{{ $i + 1 }}</span>
                                    <div>
                                        <a href="{{ route('admin.cities.sales', $row->city_id) }}"
                                           class="font-weight-bold d-block pn-table-font text-c-green text-decoration-none">
                                            {{ $row->city_name }}
                                            <i class="fas fa-external-link-alt ml-1 icon-fade"></i>
                                        </a>
                                        <div class="pn-stat-sub">
                                            {{ $row->count }} {{ Str::plural('invoice', $row->count) }}
                                            &nbsp;·&nbsp;
                                            <span class="text-c-orange">PKR {{ number_format($row->pending, 0) }} pending</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-weight-bold pn-table-font text-c-green">
                                        PKR {{ number_format($row->total, 0) }}
                                    </div>
                                    <div class="pn-stat-sub">
                                        {{ $maxCity > 0 ? round(($row->total / $citySales->sum('total')) * 100, 1) : 0 }}% of total
                                    </div>
                                </div>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar pbar pbar-green" style="--w:{{ $cityPct }}%"></div>
                            </div>
                        </div>
                        @endforeach

                        <div class="breakdown-total-row">
                            <span class="breakdown-total-lbl text-muted">TOTAL / کل</span>
                            <span class="breakdown-total-val text-c-green">PKR {{ number_format($citySales->sum('total'), 0) }}</span>
                        </div>
                    </div>
                    @else
                    <div class="card-body text-center py-5 text-muted">
                        <i class="fas fa-city fa-2x mb-2 d-block icon-fade-soft"></i>
                        No city-linked sales this month
                    </div>
                    @endif
                </div>
            </div>

            {{-- AREA BREAKDOWN --}}
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100 card-pn">
                    <div class="card-header border-0 d-flex justify-content-between align-items-center py-3 ch-blue">
                        <div>
                            <div class="pn-stat-lbl">By Area / علاقے کے مطابق</div>
                            <h6 class="mb-0 font-weight-bold text-c-blue2">
                                <i class="fas fa-map-marker-alt mr-1"></i> Top Areas / بہترین علاقے
                            </h6>
                        </div>
                        <span class="pn-bdg pn-bdg-blue">Top 5</span>
                    </div>
                    @if($areaSales->count() > 0)
                    @php $maxArea = $areaSales->first()->total ?: 1; @endphp
                    <div class="card-body p-0">
                        @foreach($areaSales as $i => $row)
                        @php $areaPct = $maxArea > 0 ? round(($row->total / $maxArea) * 100) : 0; @endphp
                        <div class="px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div class="d-flex align-items-center">
                                    <span class="badge mr-2 badge-rank-blue">{{ $i + 1 }}</span>
                                    <div>
                                        <div class="font-weight-bold pn-table-font pn-text-heading">{{ $row->area_name }}</div>
                                        <div class="pn-stat-sub">
                                            <i class="fas fa-city mr-1 icon-9"></i>{{ $row->city_name }}
                                            &nbsp;·&nbsp;{{ $row->count }} {{ Str::plural('invoice', $row->count) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-weight-bold pn-table-font text-c-blue2">
                                        PKR {{ number_format($row->total, 0) }}
                                    </div>
                                    @if($row->pending > 0)
                                    <div class="pn-stat-sub text-c-orange">{{ number_format($row->pending, 0) }} pending</div>
                                    @endif
                                </div>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar pbar pbar-blue" style="--w:{{ $areaPct }}%"></div>
                            </div>
                        </div>
                        @endforeach

                        <div class="breakdown-total-row">
                            <span class="breakdown-total-lbl text-muted">TOTAL / کل</span>
                            <span class="breakdown-total-val text-c-blue2">PKR {{ number_format($areaSales->sum('total'), 0) }}</span>
                        </div>
                    </div>
                    @else
                    <div class="card-body text-center py-5 text-muted">
                        <i class="fas fa-map-marker-alt fa-2x mb-2 d-block icon-fade-soft"></i>
                        No area-linked sales this month
                    </div>
                    @endif
                </div>
            </div>

        </div>
        @endif

        {{-- ── INACTIVE SHOPS (no salt sale in last 30 days) ──── --}}
        @if($inactiveShops->count() > 0)
        <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
            <div>
                <div class="pn-section-lbl">Attention Required / توجہ درکار</div>
                <h5 class="mb-0 font-weight-bold pn-section-title">
                    Inactive Shops
                    <small class="text-muted ch-desc">— no salt sale in last 30 days</small>
                </h5>
            </div>
            <a href="{{ route('admin.shops.index') }}"
               class="btn btn-sm btn-outline-warning btn-rounded pn-label">
                <i class="fas fa-store mr-1"></i> All Shops
            </a>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm card-pn pn-bl-yellow">
                    <div class="card-header border-0 d-flex justify-content-between align-items-center py-3 ch-yellow">
                        <h6 class="mb-0 font-weight-bold text-c-warn">
                            <i class="fas fa-store-slash mr-2"></i>Active shops with no recent sale / فعال دکانیں جن میں حالیہ فروخت نہیں
                        </h6>
                        <span class="badge badge-warning pn-bdg">{{ $inactiveShops->count() }} shops</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0 pn-table pn-table-font">
                                <thead>
                                    <tr>
                                        <th class="pl-3">#</th>
                                        <th>Shop / دکان</th>
                                        <th class="pn-hide-xs">Contact / رابطہ</th>
                                        <th class="pn-hide-xs">City / Area</th>
                                        <th class="text-center">Last Sale / آخری فروخت</th>
                                        <th class="text-center">Days Silent</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inactiveShops as $i => $shop)
                                    @php
                                        $lastSale      = $shop->sales_max_sale_date;
                                        $daysSilent    = $lastSale ? (int) \Carbon\Carbon::parse($lastSale)->diffInDays(now()) : null;
                                        $urgencyClass  = ($daysSilent === null || $daysSilent >= 60)
                                                            ? 'urgency-high'
                                                            : ($daysSilent >= 30 ? 'urgency-medium' : 'urgency-low');
                                    @endphp
                                    <tr>
                                        <td class="pl-3 align-middle text-muted pn-stat-sub">{{ $i + 1 }}</td>
                                        <td class="align-middle">
                                            <span class="font-weight-bold d-block pn-text-heading">{{ $shop->name }}</span>
                                            @if($shop->owner_name)
                                                <small class="text-muted"><i class="fas fa-user mr-1 icon-9"></i>{{ $shop->owner_name }}</small>
                                            @endif
                                        </td>
                                        <td class="align-middle pn-hide-xs">
                                            <i class="fas fa-phone mr-1 text-muted icon-10"></i>{{ $shop->phone_number ?? '—' }}
                                        </td>
                                        <td class="align-middle pn-hide-xs">
                                            @if($shop->cityRecord)
                                                <span class="d-block pn-table-font pn-text-heading">
                                                    <i class="fas fa-city mr-1 text-muted icon-10"></i>{{ $shop->cityRecord->name }}
                                                </span>
                                            @endif
                                            @if($shop->area)
                                                <small class="text-muted"><i class="fas fa-map-marker-alt mr-1 icon-9"></i>{{ $shop->area->name }}</small>
                                            @endif
                                            @if(!$shop->cityRecord && !$shop->area)
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            @if($lastSale)
                                                {{ \Carbon\Carbon::parse($lastSale)->format('d M Y') }}
                                            @else
                                                <span class="badge-never">Never</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="{{ $urgencyClass }}">
                                                {{ $daysSilent !== null ? $daysSilent.'d' : '—' }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <a href="{{ route('admin.sales.by_shop', ['shop_id' => $shop->id]) }}"
                                               class="btn btn-sm btn-pn btn-act-view" title="View Sales">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        </div>{{-- /salt-panel --}}

        {{-- ═══════════════════════════ SPICE TAB ══════════════════════════ --}}
        <div class="tab-pane fade" id="spice-panel" role="tabpanel" aria-labelledby="spice-tab">

        {{-- ── SPICE SALES + ORDERS SUMMARY ────────────────────── --}}
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <a href="{{ route('admin.spice-sales.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 pn-bl-purple">
                        <div class="card-body d-flex align-items-center justify-content-between py-3 px-4">
                            <div>
                                <div class="pn-stat-lbl">Total Spice Sale Invoices / کل مصالحہ فروخت بل</div>
                                <div class="pn-stat-num-lg text-c-purple">{{ $totalSpiceSalesCount }}</div>
                                <div class="pn-stat-sub">all-time — {{ number_format($totalSpiceSales, 0) }} PKR</div>
                            </div>
                            <i class="fas fa-file-invoice card-bg-icon text-c-purple"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <a href="{{ route('admin.spice-orders.index', ['status' => 'pending']) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 pn-bl-yellow">
                        <div class="card-body d-flex align-items-center justify-content-between py-3 px-4">
                            <div>
                                <div class="pn-stat-lbl">Pending Spice Orders / زیر التواء مصالحہ آرڈرز</div>
                                <div class="pn-stat-num-lg text-c-warn">{{ $pendingSpiceOrdersCount }}</div>
                                <div class="pn-stat-sub">awaiting action</div>
                            </div>
                            <i class="fas fa-pepper-hot card-bg-icon text-c-warn"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <a href="{{ route('admin.spice-orders.index', ['status' => 'confirmed']) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 pn-bl-teal">
                        <div class="card-body d-flex align-items-center justify-content-between py-3 px-4">
                            <div>
                                <div class="pn-stat-lbl">Confirmed Spice Orders / تصدیق شدہ مصالحہ آرڈرز</div>
                                <div class="pn-stat-num-lg text-c-teal">{{ $confirmedSpiceOrdersCount }}</div>
                                <div class="pn-stat-sub">confirmed</div>
                            </div>
                            <i class="fas fa-check-circle card-bg-icon text-c-teal"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <a href="{{ route('admin.spice-orders.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 pn-bl-green">
                        <div class="card-body d-flex align-items-center justify-content-between py-3 px-4">
                            <div>
                                <div class="pn-stat-lbl">Total Spice Orders / کل مصالحہ آرڈرز</div>
                                <div class="pn-stat-num-lg text-c-green">{{ $totalSpiceOrdersCount }}</div>
                                <div class="pn-stat-sub">all time</div>
                            </div>
                            <i class="fas fa-inbox card-bg-icon text-c-green"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- ── SPICE FINANCIAL SNAPSHOT ────────────────────────── --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <div class="pn-section-lbl">Spices Only / صرف مصالحہ جات</div>
                <h5 class="mb-0 font-weight-bold pn-section-title">Financial Snapshot <small class="text-muted ch-desc">مالی جائزہ</small></h5>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100 card-pn">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="pn-icon pn-icon-sm pnis-green mr-2 flex-shrink-0">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div>
                                <div class="pn-tbl-lbl">Total Spice Sales / کل مصالحہ فروخت</div>
                                <div class="pn-stat-num-sm text-c-dgreen">{{ number_format($totalSpiceSales, 0) }}</div>
                                <div class="pn-stat-sub">this month: {{ number_format($monthSpiceSalesTotal, 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="snap-bt snap-bt-green"></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100 card-pn">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="pn-icon pn-icon-sm pnis-yellow mr-2 flex-shrink-0">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div>
                                <div class="pn-tbl-lbl">Total Spice Purchases / کل مصالحہ خریداری</div>
                                <div class="pn-stat-num-sm text-c-orange2">{{ number_format($spicePurchasesTotal, 0) }}</div>
                                <div class="pn-stat-sub">this month: {{ number_format($monthSpicePurchasesTotal, 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="snap-bt snap-bt-yellow"></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100 card-pn">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="pn-icon pn-icon-sm pnis-orange mr-2 flex-shrink-0">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <div class="pn-tbl-lbl">Pending اُدھار</div>
                                <div class="pn-stat-num-sm text-c-orange">{{ number_format($totalSpicePending, 0) }}</div>
                                <div class="pn-stat-sub">this month: {{ number_format($monthSpicePending, 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="snap-bt snap-bt-orange"></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <a href="{{ route('admin.spice-stock.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 card-pn">
                        <div class="card-body py-3 px-3">
                            <div class="d-flex align-items-start">
                                <div class="pn-icon pn-icon-sm pnis-blue mr-2 flex-shrink-0">
                                    <i class="fas fa-warehouse"></i>
                                </div>
                                <div>
                                    <div class="pn-tbl-lbl">Spice Stock / مصالحہ اسٹاک</div>
                                    <div class="pn-stat-num-sm text-c-blue">View Levels</div>
                                    <div class="pn-stat-sub">click to open</div>
                                </div>
                            </div>
                        </div>
                        <div class="snap-bt snap-bt-blue"></div>
                    </div>
                </a>
            </div>
        </div>

        {{-- ── RECENT PENDING SPICE ORDERS ─────────────────────── --}}
        @if($recentPendingSpiceOrders->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm card-pn">
                    <div class="card-header border-0 d-flex justify-content-between align-items-center py-3 ch-yellow">
                        <div>
                            <div class="pn-stat-lbl">Action Required / کارروائی درکار</div>
                            <h6 class="mb-0 font-weight-bold text-c-warn">
                                <i class="fas fa-pepper-hot mr-1"></i> Pending Spice Orders / زیر التواء مصالحہ آرڈرز
                                <span class="badge badge-warning ml-1 pn-bdg">{{ $pendingSpiceOrdersCount }}</span>
                            </h6>
                        </div>
                        <a href="{{ route('admin.spice-orders.index', ['status' => 'pending']) }}"
                           class="btn btn-sm btn-outline-warning btn-rounded pn-label">
                            View All / سب دیکھیں
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0 pn-table pn-table-font">
                                <thead>
                                    <tr>
                                        <th class="pl-3">Reference / حوالہ</th>
                                        <th>Shop / Customer / دکان</th>
                                        <th>Items / اشیاء</th>
                                        <th>Submitted / جمع تاریخ</th>
                                        <th class="text-center">Actions / اقدامات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($recentPendingSpiceOrders as $order)
                                    <tr>
                                        <td class="pl-3 align-middle">
                                            <a href="{{ route('admin.spice-orders.show', $order) }}"
                                               class="font-weight-bold text-c-green text-decoration-none">
                                                {{ $order->reference }}
                                            </a>
                                        </td>
                                        <td class="align-middle">
                                            <span class="font-weight-bold d-block pn-text-heading">
                                                {{ $order->display_name }}
                                                @if(!$order->shop_id)
                                                    <span class="badge badge-secondary ml-1 badge-new-shop">New</span>
                                                @endif
                                            </span>
                                            <small class="text-muted">{{ $order->display_phone }}</small>
                                        </td>
                                        <td class="align-middle">
                                            @foreach($order->items->take(2) as $item)
                                                <span class="badge mr-1 badge-item-package">
                                                    {{ $item->quantity }}× {{ $item->spiceType->title ?? 'Spice' }} {{ $item->size }}g
                                                </span>
                                            @endforeach
                                            @if($order->items->count() > 2)
                                                <small class="text-muted">+{{ $order->items->count()-2 }} more</small>
                                            @endif
                                        </td>
                                        <td class="align-middle pn-text-heading">{{ $order->created_at->format('d M, h:i A') }}</td>
                                        <td class="align-middle text-center text-nowrap">
                                            <a href="{{ route('admin.spice-orders.show', $order) }}"
                                               class="btn btn-sm btn-pn btn-act-gview mr-1" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.spice-orders.confirm', $order) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-pn btn-act-confirm mr-1" title="Confirm">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.spice-orders.reject', $order) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-pn btn-act-delete" title="Reject">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm card-pn">
                    <div class="card-body text-center py-5 text-muted">
                        <i class="fas fa-pepper-hot fa-2x mb-2 d-block icon-fade-soft"></i>
                        No pending spice orders right now
                    </div>
                </div>
            </div>
        </div>
        @endif

        </div>{{-- /spice-panel --}}

        </div>{{-- /tab-content --}}

    </div>
</section>
@endsection

@section('scripts')
<script>
$(function () {
    // Bootstrap 4/5-agnostic tab activation (this app loads both bundles).
    $('#dashProductTabs a').on('click', function (e) {
        e.preventDefault();
        var target = $(this).attr('href');

        $('#dashProductTabs a').removeClass('active').attr('aria-selected', 'false');
        $(this).addClass('active').attr('aria-selected', 'true');

        $('.tab-pane').removeClass('show active');
        $(target).addClass('show active');
    });
});
</script>
@endsection
