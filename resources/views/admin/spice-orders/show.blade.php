@extends('admin.layout.app')
@section('title', 'Spice Order — ' . $spiceOrder->reference)

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">{{ $spiceOrder->reference }}</h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.spice-orders.index') }}">Spice Orders</a></li>
                    <li class="breadcrumb-item active">{{ $spiceOrder->reference }}</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end gap-2">
                @if($spiceOrder->status === 'pending')
                    <form action="{{ route('admin.spice-orders.confirm', $spiceOrder) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success px-4 mr-2 btn-pn">
                            <i class="fas fa-check mr-1"></i> Confirm Order
                        </button>
                    </form>
                    <form action="{{ route('admin.spice-orders.reject', $spiceOrder) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger px-3 mr-2 btn-pn">
                            <i class="fas fa-times mr-1"></i> Reject
                        </button>
                    </form>
                @endif
                @if($spiceOrder->sale)
                    <a href="{{ route('admin.spice-sales.index') }}"
                       class="btn btn-success px-4 mr-2 btn-pn">
                        <i class="fas fa-check-circle mr-1"></i> Converted — Sale #{{ $spiceOrder->sale->id }}
                    </a>
                @elseif(in_array($spiceOrder->status, ['pending','confirmed']))
                    <a href="{{ route('admin.spice-orders.to_sale', $spiceOrder) }}"
                       class="btn btn-warning px-4 mr-2 btn-pn">
                        <i class="fas fa-exchange-alt mr-1"></i> Convert to Sale
                    </a>
                @endif
                <a href="{{ route('admin.spice-orders.index') }}" class="btn btn-light px-3 btn-pn btn-modal-cancel">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show card-pn">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show card-pn">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        <div class="row">

            <div class="col-lg-8 mb-3">

                <div class="card border-0 shadow-sm mb-3 card-pn">
                    <div class="card-body py-4 px-4">
                        <div class="d-flex justify-content-between align-items-start flex-wrap">
                            <div>
                                <h5 class="font-weight-bold mb-1 pn-text-heading">{{ $spiceOrder->reference }}</h5>
                                <small class="text-muted">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    {{ $spiceOrder->created_at->format('d M Y, h:i A') }}
                                    &nbsp;·&nbsp;
                                    <i class="fas fa-network-wired mr-1"></i>{{ $spiceOrder->ip_address ?? '—' }}
                                </small>
                            </div>
                            <div>
                                @if($spiceOrder->status === 'pending')
                                    <span class="badge badge-warning status-badge-lg">
                                        <i class="fas fa-clock mr-1"></i> Pending
                                    </span>
                                @elseif($spiceOrder->status === 'confirmed')
                                    <span class="badge badge-success status-badge-lg">
                                        <i class="fas fa-check-circle mr-1"></i> Confirmed
                                    </span>
                                @else
                                    <span class="badge badge-danger status-badge-lg">
                                        <i class="fas fa-times-circle mr-1"></i> Rejected
                                    </span>
                                @endif
                                @if($spiceOrder->sale)
                                    <a href="{{ route('admin.spice-sales.index') }}"
                                       class="order-sale-badge mt-1">
                                        <i class="fas fa-check-circle mr-1"></i>Converted — Sale #{{ $spiceOrder->sale->id }}
                                        &nbsp;·&nbsp; {{ $spiceOrder->sale->sale_date }}
                                        &nbsp;·&nbsp; PKR {{ number_format($spiceOrder->sale->total_amount, 0) }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        @if($spiceOrder->remarks)
                            <div class="mt-3 info-box-muted">
                                <strong>Remarks:</strong> {{ $spiceOrder->remarks }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card border-0 shadow-sm card-pn">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-c-blue2">
                            <i class="fas fa-boxes mr-2"></i>Items Ordered
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                        <table class="table mb-0 pn-table pn-table-font table-simple">
                            <thead>
                                <tr>
                                    <th class="pl-4">Spice</th>
                                    <th>Size</th>
                                    <th>Quantity</th>
                                    <th>Rate</th>
                                    <th class="text-right pr-4">Sub Total</th>
                                </tr>
                            </thead>
                            <tbody>
                            @php $orderTotal = 0; @endphp
                            @foreach($spiceOrder->items as $item)
                                @php $orderTotal += $item->sub_total ?? 0; @endphp
                                <tr>
                                    <td class="pl-4 align-middle">
                                        <span class="badge pn-bdg badge-package">
                                            {{ $item->spiceType->title ?? 'Spice' }}
                                        </span>
                                    </td>
                                    <td class="align-middle pn-text-heading">{{ $item->size }}g pack</td>
                                    <td class="align-middle font-weight-bold pn-text-heading">
                                        {{ $item->quantity }}
                                        <span class="text-muted font-weight-normal pn-stat-sub">packets</span>
                                    </td>
                                    <td class="align-middle text-muted">
                                        @if($item->price)
                                            PKR {{ number_format($item->price, 0) }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-right pr-4 font-weight-bold text-c-green">
                                        @if($item->sub_total)
                                            PKR {{ number_format($item->sub_total, 0) }}
                                        @else
                                            <span class="text-muted font-weight-normal">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            @if($orderTotal > 0)
                            <tfoot>
                                <tr class="pn-grand-green">
                                    <td colspan="4" class="pl-4 font-weight-bold text-right text-c-green pn-table-font">Grand Total — کل رقم</td>
                                    <td class="text-right pr-4 font-weight-bold text-c-green pn-stat-num-sm">PKR {{ number_format($orderTotal, 0) }}</td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-4 mb-3">

                <div class="card border-0 shadow-sm mb-3 card-pn">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-c-blue2">
                            <i class="fas fa-store mr-2"></i>
                            @if($spiceOrder->shop) Registered Shop @else Customer Info @endif
                        </h6>
                    </div>
                    <div class="card-body py-3 px-4">
                        @if($spiceOrder->shop)
                            <div class="font-weight-bold mb-1 pn-text-heading pn-stat-num-md">
                                {{ $spiceOrder->shop->name }}
                            </div>
                            <div class="text-muted pn-table-font">
                                <i class="fas fa-phone mr-1"></i>{{ $spiceOrder->shop->phone_number ?? '—' }}
                            </div>
                            @if($spiceOrder->shop->city)
                            <div class="text-muted pn-table-font">
                                <i class="fas fa-map-marker-alt mr-1"></i>{{ $spiceOrder->shop->city }}
                            </div>
                            @endif
                            <div class="mt-2">
                                <span class="badge {{ $spiceOrder->shop->status === 'active' ? 'badge-success' : 'badge-warning' }}">
                                    {{ ucfirst($spiceOrder->shop->status) }}
                                </span>
                            </div>
                        @else
                            <div class="font-weight-bold mb-1 pn-text-heading pn-stat-num-md">
                                {{ $spiceOrder->customer_name ?? '—' }}
                                <span class="badge badge-secondary ml-1 badge-new-shop">Not Registered</span>
                            </div>
                            <div class="text-muted pn-table-font">
                                <i class="fas fa-phone mr-1"></i>{{ $spiceOrder->phone ?? '—' }}
                            </div>
                            @if($spiceOrder->city)
                            <div class="text-muted pn-table-font">
                                <i class="fas fa-map-marker-alt mr-1"></i>{{ $spiceOrder->city }}
                            </div>
                            @endif
                            @if($spiceOrder->status === 'pending')
                            <div class="mt-3 info-warn-sm">
                                <i class="fas fa-info-circle mr-1"></i>
                                Confirming this order will auto-register them as a new shop.
                            </div>
                            @endif
                        @endif
                    </div>
                </div>

                @if($spiceOrder->shop && $shopSalesCount !== null)
                <div class="card border-0 shadow-sm card-pn">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-c-teal">
                            <i class="fas fa-history mr-2"></i>Shop History (Spices)
                        </h6>
                    </div>
                    <div class="card-body py-3 px-4">
                        <div class="detail-row">
                            <span class="history-label">Total Sales</span>
                            <strong class="pn-text-heading">{{ $shopSalesCount }}</strong>
                        </div>
                        <div class="detail-row">
                            <span class="history-label">Total Revenue</span>
                            <strong class="pn-text-heading">{{ number_format($shopSalesTotal, 0) }} PKR</strong>
                        </div>
                        <div class="detail-row">
                            <span class="history-label">Outstanding</span>
                            <strong class="{{ $shopSalesPending > 0 ? 'text-c-red' : 'text-c-teal' }}">
                                {{ number_format($shopSalesPending, 0) }} PKR
                            </strong>
                        </div>
                    </div>
                </div>
                @endif

            </div>

        </div>
    </div>
</section>
@endsection
