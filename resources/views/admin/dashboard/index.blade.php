@extends('admin.layout.app')
@section('title', 'Sales')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Welcome Back</h1>
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
                            <i class="far fa-shop"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Shops</span>
                            <span class="info-box-number">{{ $totalShops }}</span>
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
                            <span class="info-box-number">{{ number_format($monthPurchasesTotal, 2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-info">
                            <i class="far fa-dollar-sign"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">This Month Sales</span>
                            <span class="info-box-number"> {{ number_format($monthSalesTotal, 2) }}</span>
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
                            <span class="info-box-number">{{ number_format($profitLoss, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
@endsection
