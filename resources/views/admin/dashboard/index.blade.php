@extends('admin.layout.app')
@section('title', 'Dashboard')
@section('content')

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Dashboard <small class="text-muted" style="font-size:14px;">ڈیش بورڈ</small></h1>
                <p class="text-muted mb-0" style="font-size:13px;">
                    PAK NAMAK & MASALA JAAT PRIVATE LIMITED
                </p>
            </div>
            <div class="col-sm-6">
                <form method="GET" id="monthFilterForm" class="d-flex justify-content-end align-items-center">
                    <label class="mb-0 mr-2 text-muted" style="font-size:13px;white-space:nowrap;">
                        <i class="fas fa-calendar-alt mr-1"></i> Month / مہینہ
                    </label>
                    <select name="month" class="form-control" style="width:200px;border-radius:8px;"
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

        {{-- ── ENTITY QUICK COUNTS ────────────────────────────── --}}
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <a href="{{ route('admin.shops.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #1a5c35!important;border-radius:10px;">
                        <div class="card-body d-flex align-items-center justify-content-between py-3 px-4">
                            <div>
                                <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.8px;color:#b0b7c3;">Active Shops / فعال دکانیں</div>
                                <div style="font-size:30px;font-weight:800;color:#1a5c35;line-height:1.1;">{{ $activeShopsCount }}</div>
                                <div style="font-size:11px;color:#b0b7c3;">of {{ $totalShops }} total</div>
                            </div>
                            <i class="fas fa-store" style="font-size:2.5rem;color:#1a5c35;opacity:.15;"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <a href="{{ route('admin.vendors.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #6f42c1!important;border-radius:10px;">
                        <div class="card-body d-flex align-items-center justify-content-between py-3 px-4">
                            <div>
                                <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.8px;color:#b0b7c3;">Vendors / Suppliers / فروش کار</div>
                                <div style="font-size:30px;font-weight:800;color:#6f42c1;line-height:1.1;">{{ $totalVendors }}</div>
                                <div style="font-size:11px;color:#b0b7c3;">registered suppliers</div>
                            </div>
                            <i class="fas fa-truck" style="font-size:2.5rem;color:#6f42c1;opacity:.15;"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <a href="{{ route('admin.employees.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #1cc88a!important;border-radius:10px;">
                        <div class="card-body d-flex align-items-center justify-content-between py-3 px-4">
                            <div>
                                <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.8px;color:#b0b7c3;">Working Employees / کام کرنے والے ملازمین</div>
                                <div style="font-size:30px;font-weight:800;color:#1cc88a;line-height:1.1;">{{ $workingEmployees }}</div>
                                <div style="font-size:11px;color:#b0b7c3;">currently working</div>
                            </div>
                            <i class="fas fa-users" style="font-size:2.5rem;color:#1cc88a;opacity:.15;"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <a href="{{ route('admin.sales.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #f6c23e!important;border-radius:10px;">
                        <div class="card-body d-flex align-items-center justify-content-between py-3 px-4">
                            <div>
                                <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.8px;color:#b0b7c3;">Total Sale Invoices / کل فروخت بل</div>
                                <div style="font-size:30px;font-weight:800;color:#e0a800;line-height:1.1;">{{ $totalSalesCount }}</div>
                                <div style="font-size:11px;color:#b0b7c3;">all-time transactions</div>
                            </div>
                            <i class="fas fa-file-invoice" style="font-size:2.5rem;color:#e0a800;opacity:.15;"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- ── ORDERS SUMMARY ─────────────────────────────────── --}}
        <div class="row mb-4">
            <div class="col-xl-4 col-md-4 col-sm-6 mb-3">
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #f6c23e!important;border-radius:10px;">
                        <div class="card-body d-flex align-items-center justify-content-between py-3 px-4">
                            <div>
                                <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.8px;color:#b0b7c3;">Pending Orders / زیر التواء آرڈرز</div>
                                <div style="font-size:30px;font-weight:800;color:#e0a800;line-height:1.1;">{{ $pendingOrdersCount }}</div>
                                <div style="font-size:11px;color:#b0b7c3;">awaiting action</div>
                            </div>
                            <i class="fas fa-clock" style="font-size:2.5rem;color:#e0a800;opacity:.15;"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-4 col-md-4 col-sm-6 mb-3">
                <a href="{{ route('admin.orders.index', ['status' => 'confirmed']) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #1cc88a!important;border-radius:10px;">
                        <div class="card-body d-flex align-items-center justify-content-between py-3 px-4">
                            <div>
                                <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.8px;color:#b0b7c3;">Confirmed Orders / تصدیق شدہ آرڈرز</div>
                                <div style="font-size:30px;font-weight:800;color:#1cc88a;line-height:1.1;">{{ $confirmedOrdersCount }}</div>
                                <div style="font-size:11px;color:#b0b7c3;">confirmed</div>
                            </div>
                            <i class="fas fa-check-circle" style="font-size:2.5rem;color:#1cc88a;opacity:.15;"></i>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-4 col-md-4 col-sm-6 mb-3">
                <a href="{{ route('admin.orders.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #1a5c35!important;border-radius:10px;">
                        <div class="card-body d-flex align-items-center justify-content-between py-3 px-4">
                            <div>
                                <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.8px;color:#b0b7c3;">Total Orders / کل آرڈرز</div>
                                <div style="font-size:30px;font-weight:800;color:#1a5c35;line-height:1.1;">{{ $totalOrdersCount }}</div>
                                <div style="font-size:11px;color:#b0b7c3;">all time</div>
                            </div>
                            <i class="fas fa-inbox" style="font-size:2.5rem;color:#1a5c35;opacity:.15;"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- ── ALL-TIME SNAPSHOT ───────────────────────────────── --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:1px;color:#b0b7c3;">Overall / مجموعی</div>
                <h5 class="mb-0 font-weight-bold" style="color:#2d3748;">Total Snapshot <small class="text-muted" style="font-size:13px;">کل جائزہ</small></h5>
            </div>
        </div>

        <div class="row mb-4">
            {{-- Total Sales --}}
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius:10px;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-2 flex-shrink-0"
                                 style="width:36px;height:36px;background:#e8f5e9;">
                                <i class="fas fa-chart-line" style="color:#2e7d32;font-size:15px;"></i>
                            </div>
                            <div>
                                <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.5px;color:#b0b7c3;">Total Sales / کل فروخت</div>
                                <div style="font-size:15px;font-weight:700;color:#2e7d32;">{{ number_format($totalSales, 0) }}</div>
                                <div style="font-size:10px;color:#aaa;">{{ $totalSalesCount }} invoices</div>
                            </div>
                        </div>
                    </div>
                    <div style="height:3px;background:#2e7d32;border-radius:0 0 10px 10px;"></div>
                </div>
            </div>

            {{-- Total Purchases --}}
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius:10px;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-2 flex-shrink-0"
                                 style="width:36px;height:36px;background:#fff8e1;">
                                <i class="fas fa-shopping-cart" style="color:#f57f17;font-size:15px;"></i>
                            </div>
                            <div>
                                <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.5px;color:#b0b7c3;">Total Purchases / کل خریداری</div>
                                <div style="font-size:15px;font-weight:700;color:#f57f17;">{{ number_format($PurchasesTotal, 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div style="height:3px;background:#f57f17;border-radius:0 0 10px 10px;"></div>
                </div>
            </div>

            {{-- Total Expenses --}}
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius:10px;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-2 flex-shrink-0"
                                 style="width:36px;height:36px;background:#fce4ec;">
                                <i class="fas fa-receipt" style="color:#c62828;font-size:15px;"></i>
                            </div>
                            <div>
                                <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.5px;color:#b0b7c3;">Total Expenses / کل اخراجات</div>
                                <div style="font-size:15px;font-weight:700;color:#c62828;">{{ number_format($totalExpenses, 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div style="height:3px;background:#c62828;border-radius:0 0 10px 10px;"></div>
                </div>
            </div>

            {{-- Total Salaries --}}
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius:10px;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-2 flex-shrink-0"
                                 style="width:36px;height:36px;background:#e3f2fd;">
                                <i class="fas fa-money-bill-wave" style="color:#1565c0;font-size:15px;"></i>
                            </div>
                            <div>
                                <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.5px;color:#b0b7c3;">Total Salaries / کل تنخواہیں</div>
                                <div style="font-size:15px;font-weight:700;color:#1565c0;">{{ number_format($totalSalaryPaid, 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div style="height:3px;background:#1565c0;border-radius:0 0 10px 10px;"></div>
                </div>
            </div>

            {{-- Total Pending --}}
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius:10px;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-2 flex-shrink-0"
                                 style="width:36px;height:36px;background:#fff3e0;">
                                <i class="fas fa-clock" style="color:#e65100;font-size:15px;"></i>
                            </div>
                            <div>
                                <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.5px;color:#b0b7c3;">Total Pending اُدھار</div>
                                <div style="font-size:15px;font-weight:700;color:#e65100;">{{ number_format($totalPending, 0) }}</div>
                                @php $pendingPct = $totalSales > 0 ? round(($totalPending/$totalSales)*100,1) : 0; @endphp
                                <div style="font-size:10px;color:#aaa;">{{ $pendingPct }}% of sales</div>
                            </div>
                        </div>
                    </div>
                    <div style="height:3px;background:#e65100;border-radius:0 0 10px 10px;"></div>
                </div>
            </div>

            {{-- Total Profit / Loss --}}
            @php
                $tplColor  = $totalProfitLoss >= 0 ? '#1b5e20' : '#b71c1c';
                $tplBg     = $totalProfitLoss >= 0 ? '#e8f5e9' : '#ffebee';
                $tplBorder = $totalProfitLoss >= 0 ? '#2e7d32' : '#c62828';
                $tplIcon   = $totalProfitLoss >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                $tplLabel  = $totalProfitLoss >= 0 ? 'Total Profit' : 'Total Loss';
            @endphp
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius:10px;background:{{ $tplBg }};">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-2 flex-shrink-0"
                                 style="width:36px;height:36px;background:{{ $tplColor }}22;">
                                <i class="fas {{ $tplIcon }}" style="color:{{ $tplColor }};font-size:15px;"></i>
                            </div>
                            <div>
                                <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.5px;color:{{ $tplColor }};">{{ $tplLabel }}</div>
                                <div style="font-size:15px;font-weight:700;color:{{ $tplColor }};">{{ number_format(abs($totalProfitLoss), 0) }}</div>
                                <div style="font-size:10px;color:{{ $tplColor }};opacity:.7;">all time</div>
                            </div>
                        </div>
                    </div>
                    <div style="height:3px;background:{{ $tplBorder }};border-radius:0 0 10px 10px;"></div>
                </div>
            </div>
        </div>
        
        {{-- ── MONTHLY FINANCIALS ──────────────────────────────── --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:1px;color:#b0b7c3;">Monthly Snapshot / ماہانہ جائزہ</div>
                <h5 class="mb-0 font-weight-bold" style="color:#2d3748;">
                    {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y') }}
                </h5>
            </div>
            <a href="{{ route('admin.sales.index', ['month' => $selectedMonth]) }}"
               class="btn btn-sm btn-outline-primary" style="border-radius:20px;font-size:12px;">
                <i class="fas fa-external-link-alt mr-1"></i> View Sales / فروخت دیکھیں
            </a>
        </div>

        <div class="row mb-3">
            {{-- Sales --}}
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius:10px;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-2 flex-shrink-0"
                                 style="width:36px;height:36px;background:#e8f5e9;">
                                <i class="fas fa-chart-line" style="color:#2e7d32;font-size:15px;"></i>
                            </div>
                            <div>
                                <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.5px;color:#b0b7c3;">Sales / فروخت</div>
                                <div style="font-size:15px;font-weight:700;color:#2e7d32;">{{ number_format($monthSalesTotal, 0) }}</div>
                                <div style="font-size:10px;color:#aaa;">{{ $monthSalesCount }} invoices</div>
                            </div>
                        </div>
                    </div>
                    <div style="height:3px;background:#2e7d32;border-radius:0 0 10px 10px;"></div>
                </div>
            </div>

            {{-- Purchases --}}
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius:10px;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-2 flex-shrink-0"
                                 style="width:36px;height:36px;background:#fff8e1;">
                                <i class="fas fa-shopping-cart" style="color:#f57f17;font-size:15px;"></i>
                            </div>
                            <div>
                                <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.5px;color:#b0b7c3;">Purchases / خریداری</div>
                                <div style="font-size:15px;font-weight:700;color:#f57f17;">{{ number_format($monthPurchasesTotal, 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div style="height:3px;background:#f57f17;border-radius:0 0 10px 10px;"></div>
                </div>
            </div>

            {{-- Expenses --}}
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius:10px;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-2 flex-shrink-0"
                                 style="width:36px;height:36px;background:#fce4ec;">
                                <i class="fas fa-receipt" style="color:#c62828;font-size:15px;"></i>
                            </div>
                            <div>
                                <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.5px;color:#b0b7c3;">Expenses / اخراجات</div>
                                <div style="font-size:15px;font-weight:700;color:#c62828;">{{ number_format($monthExpensesTotal, 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div style="height:3px;background:#c62828;border-radius:0 0 10px 10px;"></div>
                </div>
            </div>

            {{-- Salaries --}}
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius:10px;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-2 flex-shrink-0"
                                 style="width:36px;height:36px;background:#e3f2fd;">
                                <i class="fas fa-money-bill-wave" style="color:#1565c0;font-size:15px;"></i>
                            </div>
                            <div>
                                <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.5px;color:#b0b7c3;">Salaries / تنخواہیں</div>
                                <div style="font-size:15px;font-weight:700;color:#1565c0;">{{ number_format($monthSalaryTotal, 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div style="height:3px;background:#1565c0;border-radius:0 0 10px 10px;"></div>
                </div>
            </div>

            {{-- Pending / Udhaar --}}
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius:10px;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-2 flex-shrink-0"
                                 style="width:36px;height:36px;background:#fff3e0;">
                                <i class="fas fa-clock" style="color:#e65100;font-size:15px;"></i>
                            </div>
                            <div>
                                <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.5px;color:#b0b7c3;">Pending اُدھار</div>
                                <div style="font-size:15px;font-weight:700;color:#e65100;">{{ number_format($monthPending, 0) }}</div>
                            </div>
                        </div>
                    </div>
                    <div style="height:3px;background:#e65100;border-radius:0 0 10px 10px;"></div>
                </div>
            </div>

            {{-- Profit / Loss --}}
            @php
                $plColor  = $profitLoss >= 0 ? '#1b5e20' : '#b71c1c';
                $plBg     = $profitLoss >= 0 ? '#e8f5e9' : '#ffebee';
                $plBorder = $profitLoss >= 0 ? '#2e7d32' : '#c62828';
                $plIcon   = $profitLoss >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                $plLabel  = $profitLoss >= 0 ? 'Profit' : 'Loss';
            @endphp
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius:10px;background:{{ $plBg }};">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mr-2 flex-shrink-0"
                                 style="width:36px;height:36px;background:{{ $plColor }}22;">
                                <i class="fas {{ $plIcon }}" style="color:{{ $plColor }};font-size:15px;"></i>
                            </div>
                            <div>
                                <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.5px;color:{{ $plColor }};">{{ $plLabel }}</div>
                                <div style="font-size:15px;font-weight:700;color:{{ $plColor }};">{{ number_format(abs($profitLoss), 0) }}</div>
                                <div style="font-size:10px;color:{{ $plColor }};opacity:.7;">this month</div>
                            </div>
                        </div>
                    </div>
                    <div style="height:3px;background:{{ $plBorder }};border-radius:0 0 10px 10px;"></div>
                </div>
            </div>
        </div>

        {{-- ── PRODUCT TYPE BREAKDOWN (MONTH) ────────────────── --}}
        @php
            $prodTotal = $monthDallaTotal + $monthThailaTotal + $monthPackageTotal ?: 1;
        @endphp
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm" style="border-radius:10px;">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.5px;color:#b0b7c3;">Dalla — ڈلہ</div>
                                <div style="font-size:18px;font-weight:700;color:#1a5c35;">PKR {{ number_format($monthDallaTotal, 0) }}</div>
                            </div>
                            <span class="badge badge-primary px-2 py-1" style="font-size:12px;border-radius:20px;">
                                {{ number_format(($monthDallaTotal / $prodTotal) * 100, 1) }}%
                            </span>
                        </div>
                        <div class="progress" style="height:5px;border-radius:5px;">
                            <div class="progress-bar bg-primary"
                                 style="width:{{ ($monthDallaTotal / $prodTotal) * 100 }}%;border-radius:5px;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm" style="border-radius:10px;">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.5px;color:#b0b7c3;">Thaila — تھیلا</div>
                                <div style="font-size:18px;font-weight:700;color:#1cc88a;">PKR {{ number_format($monthThailaTotal, 0) }}</div>
                            </div>
                            <span class="badge badge-success px-2 py-1" style="font-size:12px;border-radius:20px;">
                                {{ number_format(($monthThailaTotal / $prodTotal) * 100, 1) }}%
                            </span>
                        </div>
                        <div class="progress" style="height:5px;border-radius:5px;">
                            <div class="progress-bar bg-success"
                                 style="width:{{ ($monthThailaTotal / $prodTotal) * 100 }}%;border-radius:5px;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm" style="border-radius:10px;">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.5px;color:#b0b7c3;">Package — پیکٹ</div>
                                <div style="font-size:18px;font-weight:700;color:#f6c23e;">PKR {{ number_format($monthPackageTotal, 0) }}</div>
                            </div>
                            <span class="badge badge-warning px-2 py-1" style="font-size:12px;border-radius:20px;">
                                {{ number_format(($monthPackageTotal / $prodTotal) * 100, 1) }}%
                            </span>
                        </div>
                        <div class="progress" style="height:5px;border-radius:5px;">
                            <div class="progress-bar bg-warning"
                                 style="width:{{ ($monthPackageTotal / $prodTotal) * 100 }}%;border-radius:5px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── RECENT PENDING ORDERS ──────────────────────────── --}}
        @if($recentPendingOrders->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius:10px;">
                    <div class="card-header border-0 d-flex justify-content-between align-items-center py-3"
                         style="background:#fffbf0;border-radius:10px 10px 0 0;">
                        <div>
                            <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.8px;color:#b0b7c3;">Action Required / کارروائی درکار</div>
                            <h6 class="mb-0 font-weight-bold" style="color:#e0a800;">
                                <i class="fas fa-clock mr-1"></i> Pending Orders / زیر التواء آرڈرز
                                <span class="badge badge-warning ml-1" style="font-size:11px;border-radius:20px;">{{ $pendingOrdersCount }}</span>
                            </h6>
                        </div>
                        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}"
                           class="btn btn-sm btn-outline-warning" style="border-radius:20px;font-size:12px;">
                            View All / سب دیکھیں
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0" style="font-size:13px;">
                                <thead>
                                    <tr style="background:#f8f9fc;">
                                        <th class="pl-3 py-2 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Reference / حوالہ</th>
                                        <th class="py-2 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Shop / Customer / دکان</th>
                                        <th class="py-2 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Items / اشیاء</th>
                                        <th class="py-2 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Submitted / جمع تاریخ</th>
                                        <th class="py-2 text-center text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Actions / اقدامات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($recentPendingOrders as $order)
                                    <tr style="border-bottom:1px solid #f0f0f0;">
                                        <td class="pl-3 py-2 align-middle">
                                            <a href="{{ route('admin.orders.show', $order) }}"
                                               class="font-weight-bold" style="color:#1a5c35;text-decoration:none;">
                                                {{ $order->reference }}
                                            </a>
                                        </td>
                                        <td class="py-2 align-middle">
                                            <span class="font-weight-bold d-block" style="color:#2d3748;">
                                                {{ $order->display_name }}
                                                @if(!$order->shop_id)
                                                    <span class="badge badge-secondary ml-1" style="font-size:9px;">New</span>
                                                @endif
                                            </span>
                                            <small class="text-muted">{{ $order->display_phone }}</small>
                                        </td>
                                        <td class="py-2 align-middle">
                                            @foreach($order->items->take(2) as $item)
                                                <span class="badge mr-1"
                                                      style="font-size:10px;padding:3px 7px;border-radius:20px;
                                                      background:{{ $item->type==='dalla'?'#d0e8d8':($item->type==='thaila'?'#d4edda':'#fff3cd') }};
                                                      color:{{ $item->type==='dalla'?'#1a5c35':($item->type==='thaila'?'#155724':'#856404') }};">
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
                                        <td class="py-2 align-middle">
                                            <span style="color:#2d3748;">{{ $order->created_at->format('d M, h:i A') }}</span>
                                        </td>
                                        <td class="py-2 align-middle text-center" style="white-space:nowrap;">
                                            <a href="{{ route('admin.orders.show', $order) }}"
                                               class="btn btn-sm mr-1"
                                               style="background:#d0e8d8;color:#1a5c35;border:1px solid #a8d4b8;border-radius:6px;"
                                               title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.orders.confirm', $order) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm mr-1"
                                                        style="background:#d4edda;color:#155724;border:1px solid #c3e6cb;border-radius:6px;"
                                                        title="Confirm">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.orders.reject', $order) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm"
                                                        style="background:#fce8e6;color:#c62828;border:1px solid #ef9a9a;border-radius:6px;"
                                                        title="Reject">
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
                <div class="card border-0 shadow-sm h-100" style="border-radius:10px;">
                    <div class="card-header border-0 d-flex justify-content-between align-items-center py-3"
                         style="background:#eaf3ee;border-radius:10px 10px 0 0;">
                        <div>
                            <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.8px;color:#b0b7c3;">Top 5</div>
                            <h6 class="mb-0 font-weight-bold" style="color:#1a5c35;">Shops by Revenue / آمدن کے مطابق دکانیں</h6>
                        </div>
                        <a href="{{ route('admin.shops.index') }}"
                           class="btn btn-sm btn-outline-primary" style="border-radius:20px;font-size:12px;">
                            All Shops / تمام دکانیں
                        </a>
                    </div>
                    <div class="card-body p-0">
                        @php $maxShop = $topShops->first()->total ?? 1; @endphp
                        @forelse($topShops as $i => $row)
                            <div class="px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-primary mr-2"
                                              style="font-size:11px;min-width:22px;border-radius:50%;padding:4px 7px;">
                                            {{ $i + 1 }}
                                        </span>
                                        <div>
                                            <div class="font-weight-bold" style="font-size:13px;">{{ $row->shop_name }}</div>
                                            @if($row->shop_phone_number)
                                                <div class="text-muted" style="font-size:11px;">
                                                    <i class="fas fa-phone mr-1" style="font-size:9px;"></i>{{ $row->shop_phone_number }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="font-weight-bold" style="font-size:13px;color:#1a5c35;">
                                        PKR {{ number_format($row->total, 0) }}
                                    </span>
                                </div>
                                <div class="progress" style="height:4px;border-radius:4px;">
                                    <div class="progress-bar bg-primary"
                                         style="width:{{ ($row->total / $maxShop) * 100 }}%;border-radius:4px;"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-store fa-2x mb-2 d-block" style="opacity:.3;"></i>
                                No sales data yet
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius:10px;">
                    <div class="card-header border-0 d-flex justify-content-between align-items-center py-3"
                         style="background:#f0fff8;border-radius:10px 10px 0 0;">
                        <div>
                            <div style="font-size:10px;text-transform:uppercase;font-weight:700;letter-spacing:.8px;color:#b0b7c3;">Top 5</div>
                            <h6 class="mb-0 font-weight-bold" style="color:#1cc88a;">Best Sales Months / بہترین فروخت کے مہینے</h6>
                        </div>
                        <a href="{{ route('admin.sales.report') }}"
                           class="btn btn-sm btn-outline-success" style="border-radius:20px;font-size:12px;">
                            Full Report / مکمل رپورٹ
                        </a>
                    </div>
                    <div class="card-body p-0">
                        @php $maxMonth = $topDays->first()->total ?? 1; @endphp
                        @forelse($topDays as $i => $row)
                            <div class="px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-success mr-2"
                                              style="font-size:11px;min-width:22px;border-radius:50%;padding:4px 7px;">
                                            {{ $i + 1 }}
                                        </span>
                                        <div class="font-weight-bold" style="font-size:13px;">
                                                {{ \Carbon\Carbon::createFromFormat('Y-m', $row->day)->format('F Y') }}
                                        </div>
                                    </div>
                                    <span class="font-weight-bold" style="font-size:13px;color:#1cc88a;">
                                        PKR {{ number_format($row->total, 0) }}
                                    </span>
                                </div>
                                <div class="progress" style="height:4px;border-radius:4px;">
                                    <div class="progress-bar bg-success"
                                         style="width:{{ ($row->total / $maxMonth) * 100 }}%;border-radius:4px;"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-calendar fa-2x mb-2 d-block" style="opacity:.3;"></i>
                                No sales data yet
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection

@section('scripts')
@endsection
