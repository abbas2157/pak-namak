@extends('admin.layout.app')
@section('title', 'Order — ' . $order->reference)

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">{{ $order->reference }}</h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
                    <li class="breadcrumb-item active">{{ $order->reference }}</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end gap-2">
                @if($order->status === 'pending')
                    <form action="{{ route('admin.orders.confirm', $order) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success px-4 mr-2 btn-pn">
                            <i class="fas fa-check mr-1"></i> Confirm Order
                        </button>
                    </form>
                    <form action="{{ route('admin.orders.reject', $order) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger px-3 mr-2 btn-pn">
                            <i class="fas fa-times mr-1"></i> Reject
                        </button>
                    </form>
                @endif
                @if(in_array($order->status, ['pending','confirmed']))
                    <a href="{{ route('admin.orders.to_sale', $order) }}"
                       class="btn btn-warning px-4 mr-2 btn-pn">
                        <i class="fas fa-exchange-alt mr-1"></i> Convert to Sale
                    </a>
                @endif
                <a href="{{ route('admin.orders.index') }}" class="btn btn-light px-3 btn-pn btn-modal-cancel">
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

            {{-- Left: Order info + Items --}}
            <div class="col-lg-8 mb-3">

                {{-- Order header card --}}
                <div class="card border-0 shadow-sm mb-3 card-pn">
                    <div class="card-body py-4 px-4">
                        <div class="d-flex justify-content-between align-items-start flex-wrap">
                            <div>
                                <h5 class="font-weight-bold mb-1 pn-text-heading">{{ $order->reference }}</h5>
                                <small class="text-muted">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    {{ $order->created_at->format('d M Y, h:i A') }}
                                    &nbsp;·&nbsp;
                                    <i class="fas fa-network-wired mr-1"></i>{{ $order->ip_address ?? '—' }}
                                </small>
                            </div>
                            <div>
                                @if($order->status === 'pending')
                                    <span class="badge badge-warning status-badge-lg">
                                        <i class="fas fa-clock mr-1"></i> Pending
                                    </span>
                                @elseif($order->status === 'confirmed')
                                    <span class="badge badge-success status-badge-lg">
                                        <i class="fas fa-check-circle mr-1"></i> Confirmed
                                    </span>
                                @else
                                    <span class="badge badge-danger status-badge-lg">
                                        <i class="fas fa-times-circle mr-1"></i> Rejected
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if($order->remarks)
                            <div class="mt-3 info-box-muted">
                                <strong>Remarks:</strong> {{ $order->remarks }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Items --}}
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
                                    <th class="pl-4">Type</th>
                                    <th>Size</th>
                                    <th>Quantity</th>
                                    <th>Rate</th>
                                    <th class="text-right pr-4">Sub Total</th>
                                </tr>
                            </thead>
                            <tbody>
                            @php $orderTotal = 0; @endphp
                            @foreach($order->items as $item)
                                @php
                                    $itemBadgeClass = match($item->type) {
                                        'dalla'   => 'badge-dalla',
                                        'thaila'  => 'badge-thaila',
                                        'package' => 'badge-package',
                                        default   => '',
                                    };
                                    $orderTotal += $item->sub_total ?? 0;
                                @endphp
                                <tr>
                                    <td class="pl-4 align-middle">
                                        <span class="badge pn-bdg {{ $itemBadgeClass }}">
                                            {{ ucfirst($item->type) }}
                                        </span>
                                    </td>
                                    <td class="align-middle pn-text-heading">
                                        @if($item->type === 'dalla') Bulk (Mann)
                                        @elseif($item->type === 'thaila') {{ $item->size }} KG bag
                                        @else {{ $item->size }}g pack
                                        @endif
                                    </td>
                                    <td class="align-middle font-weight-bold pn-text-heading">
                                        {{ $item->quantity }}
                                        <span class="text-muted font-weight-normal pn-stat-sub">
                                            @if($item->type === 'dalla') Mann
                                            @elseif($item->type === 'thaila') bags
                                            @else bundles
                                            @endif
                                        </span>
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

            {{-- Right: Customer / Shop panel --}}
            <div class="col-lg-4 mb-3">

                {{-- Customer info --}}
                <div class="card border-0 shadow-sm mb-3 card-pn">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-c-blue2">
                            <i class="fas fa-store mr-2"></i>
                            @if($order->shop) Registered Shop @else Customer Info @endif
                        </h6>
                    </div>
                    <div class="card-body py-3 px-4">
                        @if($order->shop)
                            <div class="font-weight-bold mb-1 pn-text-heading pn-stat-num-md">
                                {{ $order->shop->name }}
                            </div>
                            @if($order->shop->owner_name)
                                <div class="text-muted pn-table-font">
                                    <i class="fas fa-user mr-1"></i>{{ $order->shop->owner_name }}
                                </div>
                            @endif
                            <div class="text-muted pn-table-font">
                                <i class="fas fa-phone mr-1"></i>{{ $order->shop->phone_number ?? '—' }}
                            </div>
                            @if($order->shop->city)
                            <div class="text-muted pn-table-font">
                                <i class="fas fa-map-marker-alt mr-1"></i>{{ $order->shop->city }}
                            </div>
                            @endif
                            <div class="mt-2">
                                <span class="badge {{ $order->shop->status === 'active' ? 'badge-success' : 'badge-warning' }}">
                                    {{ ucfirst($order->shop->status) }}
                                </span>
                            </div>
                        @else
                            <div class="font-weight-bold mb-1 pn-text-heading pn-stat-num-md">
                                {{ $order->customer_name ?? '—' }}
                                <span class="badge badge-secondary ml-1 badge-new-shop">Not Registered</span>
                            </div>
                            <div class="text-muted pn-table-font">
                                <i class="fas fa-phone mr-1"></i>{{ $order->phone ?? '—' }}
                            </div>
                            @if($order->city)
                            <div class="text-muted pn-table-font">
                                <i class="fas fa-map-marker-alt mr-1"></i>{{ $order->city }}
                            </div>
                            @endif
                            @if($order->status === 'pending')
                            <div class="mt-3 info-warn-sm">
                                <i class="fas fa-info-circle mr-1"></i>
                                Confirming this order will auto-register them as a new shop.
                            </div>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Shop sales history (only if registered) --}}
                @if($order->shop && $shopSalesCount !== null)
                <div class="card border-0 shadow-sm card-pn">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-c-teal">
                            <i class="fas fa-history mr-2"></i>Shop History
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
                        <a href="{{ route('admin.sales.by_shop', ['shop_id' => $order->shop_id]) }}"
                           class="btn btn-block btn-sm btn-pn btn-act-view mt-3">
                            <i class="fas fa-eye mr-1"></i> View All Sales
                        </a>
                    </div>
                </div>
                @endif

            </div>

        </div>
    </div>
</section>
@endsection
