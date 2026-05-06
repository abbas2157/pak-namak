@extends('admin.layout.app')
@section('title', 'Dashboard')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <form method="GET" id="monthFilterForm" class="d-flex justify-content-end">
                        <div class="form-group" style="width:250px;">
                            <label>Select Month</label>
                            <select name="month" class="form-control" onchange="document.getElementById('monthFilterForm').submit()">
                                @foreach($months as $month)
                                    <option value="{{ $month['value'] }}"
                                        {{ $selectedMonth == $month['value'] ? 'selected' : '' }}>
                                        {{ $month['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <h5 class="mb-2">All Sales Data</h5>
            <div class="row">
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-info">
                            <i class="far fa-dollar-sign"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Sales</span>
                            <span class="info-box-number">PKR.  {{ number_format($totalSales, 2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-info">
                            <i class="fas fa-umbrella-beach"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Purchases</span>
                            <span class="info-box-number">PKR. {{ number_format($PurchasesTotal, 2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-info">
                            <i class="nav-icon fas fa-money-bill-wave"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Expenses</span>
                            <span class="info-box-number">PKR. {{ number_format($totalExpenses, 2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-info">
                            <i class="far fa-dollar-sign"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Profit / Loss</span>
                            <span class="info-box-number">PKR. {{ number_format($totalProfitLoss, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-info">
                            <i class="far fa-dollar-sign"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">This Month Sales</span>
                            <span class="info-box-number">PKR.  {{ number_format($monthSalesTotal, 2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-info">
                            <i class="fas fa-umbrella-beach"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">This Month Purchases</span>
                            <span class="info-box-number">PKR. {{ number_format($monthPurchasesTotal, 2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-info">
                            <i class="nav-icon fas fa-money-bill-wave"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">This Month Expenses</span>
                            <span class="info-box-number">PKR. {{ number_format($monthExpensesTotal, 2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-info">
                            <i class="far fa-dollar-sign"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">This Month Profit / Loss</span>
                            <span class="info-box-number">PKR. {{ number_format($profitLoss, 2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning">
                            <i class="fas fa-pepper-hot"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Best Namak Type</span>
                            <span class="info-box-number">
                                {{ $namakBest == 'dallas' ? 'Dalla' : ($namakBest == 'thailas' ? 'Thailas' : ($namakBest == 'packages' ? 'Packages' : $namakBest)) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h5 class="mb-0">Top 5 Shops by Sales</h5>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @forelse($topShops as $row)
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start gap-2">
                                            <div class="pr-2" style="min-width: 0;">
                                                <div class="font-weight-bold">{{ $row->shop_name ?? $row->shop_id ?? '-' }}</div>
                                                <div class="text-muted" style="font-size: 12px;">
                                                    @if(!empty($row->shop_phone_number))
                                                        Phone: {{ $row->shop_phone_number }}
                                                    @endif
                                                    @if(!empty($row->shop_address))
                                                        {{ !empty($row->shop_phone_number) ? ' • ' : '' }}Address: {{ $row->shop_address }}
                                                    @endif
                                                </div>

                                            </div>
                                            <span class="badge badge-primary badge-pill">PKR. {{ number_format($row->total ?? 0, 2) }}</span>
                                        </div>
                                    </li>

                                @empty
                                    <li class="list-group-item text-center text-muted">No sales data</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-12">
                    <div class="card card-outline card-success">
                        <div class="card-header">
                            <h5 class="mb-0">Top 5 Months by Sales</h5>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @forelse($topDays as $row)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>{{ $row->day ?? '-' }}</span>
                                        <span class="badge badge-success badge-pill">PKR. {{ number_format($row->total ?? 0, 2) }}</span>
                                    </li>

                                @empty
                                    <li class="list-group-item text-center text-muted">No sales data</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
@endsection
