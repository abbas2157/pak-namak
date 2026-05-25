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
                        <button type="submit" class="btn btn-success px-4 mr-2" style="border-radius:8px;">
                            <i class="fas fa-check mr-1"></i> Confirm Order
                        </button>
                    </form>
                    <form action="{{ route('admin.orders.reject', $order) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger px-3 mr-2" style="border-radius:8px;">
                            <i class="fas fa-times mr-1"></i> Reject
                        </button>
                    </form>
                @endif
                @if(in_array($order->status, ['pending','confirmed']))
                    <a href="{{ route('admin.orders.to_sale', $order) }}"
                       class="btn btn-warning px-4 mr-2" style="border-radius:8px;">
                        <i class="fas fa-exchange-alt mr-1"></i> Convert to Sale
                    </a>
                @endif
                <a href="{{ route('admin.orders.index') }}" class="btn btn-light px-3" style="border-radius:8px;border:1px solid #d1d5db;">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" style="border-radius:10px;">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        <div class="row">

            {{-- Left: Order info + Items --}}
            <div class="col-lg-8 mb-3">

                {{-- Order header card --}}
                <div class="card border-0 shadow-sm mb-3" style="border-radius:12px;">
                    <div class="card-body py-4 px-4">
                        <div class="d-flex justify-content-between align-items-start flex-wrap">
                            <div>
                                <h5 class="font-weight-bold mb-1" style="color:#2d3748;">{{ $order->reference }}</h5>
                                <small class="text-muted">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    {{ $order->created_at->format('d M Y, h:i A') }}
                                    &nbsp;·&nbsp;
                                    <i class="fas fa-network-wired mr-1"></i>{{ $order->ip_address ?? '—' }}
                                </small>
                            </div>
                            <div>
                                @if($order->status === 'pending')
                                    <span class="badge badge-warning px-3 py-2" style="font-size:13px;border-radius:20px;">
                                        <i class="fas fa-clock mr-1"></i> Pending
                                    </span>
                                @elseif($order->status === 'confirmed')
                                    <span class="badge badge-success px-3 py-2" style="font-size:13px;border-radius:20px;">
                                        <i class="fas fa-check-circle mr-1"></i> Confirmed
                                    </span>
                                @else
                                    <span class="badge badge-danger px-3 py-2" style="font-size:13px;border-radius:20px;">
                                        <i class="fas fa-times-circle mr-1"></i> Rejected
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if($order->remarks)
                            <div class="mt-3 p-3 rounded" style="background:#f8f9fc;font-size:13px;color:#555;">
                                <strong>Remarks:</strong> {{ $order->remarks }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Items --}}
                <div class="card border-0 shadow-sm" style="border-radius:12px;">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold" style="color:#4e73df;">
                            <i class="fas fa-boxes mr-2"></i>Items Ordered
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table mb-0" style="font-size:14px;">
                            <thead>
                                <tr style="background:#f8f9fc;">
                                    <th class="pl-4 py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Type</th>
                                    <th class="py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Size</th>
                                    <th class="py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($order->items as $item)
                                @php
                                    $typeColor = match($item->type) {
                                        'dalla'   => ['bg'=>'#e8f0fe','color'=>'#4e73df','label'=>'Dalla'],
                                        'thaila'  => ['bg'=>'#d4edda','color'=>'#155724','label'=>'Thaila'],
                                        'package' => ['bg'=>'#fff3cd','color'=>'#856404','label'=>'Package'],
                                        default   => ['bg'=>'#f0f0f0','color'=>'#555','label'=>$item->type],
                                    };
                                @endphp
                                <tr style="border-bottom:1px solid #f0f0f0;">
                                    <td class="pl-4 py-3 align-middle">
                                        <span class="badge" style="background:{{ $typeColor['bg'] }};color:{{ $typeColor['color'] }};font-size:12px;padding:4px 10px;border-radius:20px;">
                                            {{ $typeColor['label'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 align-middle font-weight-bold" style="color:#2d3748;">
                                        @if($item->type === 'dalla')
                                            — (Bulk)
                                        @elseif($item->type === 'thaila')
                                            {{ $item->size }} KG bag
                                        @else
                                            {{ $item->size }} gram pack
                                        @endif
                                    </td>
                                    <td class="py-3 align-middle font-weight-bold" style="color:#2d3748;">
                                        {{ $item->quantity }}
                                        @if($item->type === 'dalla') Mann
                                        @elseif($item->type === 'thaila') bags
                                        @else bundles
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            {{-- Right: Customer / Shop panel --}}
            <div class="col-lg-4 mb-3">

                {{-- Customer info --}}
                <div class="card border-0 shadow-sm mb-3" style="border-radius:12px;">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold" style="color:#4e73df;">
                            <i class="fas fa-store mr-2"></i>
                            @if($order->shop) Registered Shop @else Customer Info @endif
                        </h6>
                    </div>
                    <div class="card-body py-3 px-4">
                        @if($order->shop)
                            <div class="font-weight-bold mb-1" style="color:#2d3748;font-size:16px;">
                                {{ $order->shop->name }}
                            </div>
                            @if($order->shop->owner_name)
                                <div class="text-muted" style="font-size:13px;">
                                    <i class="fas fa-user mr-1"></i>{{ $order->shop->owner_name }}
                                </div>
                            @endif
                            <div class="text-muted" style="font-size:13px;">
                                <i class="fas fa-phone mr-1"></i>{{ $order->shop->phone_number ?? '—' }}
                            </div>
                            @if($order->shop->city)
                            <div class="text-muted" style="font-size:13px;">
                                <i class="fas fa-map-marker-alt mr-1"></i>{{ $order->shop->city }}
                            </div>
                            @endif
                            <div class="mt-2">
                                <span class="badge {{ $order->shop->status === 'active' ? 'badge-success' : 'badge-warning' }}" style="font-size:11px;">
                                    {{ ucfirst($order->shop->status) }}
                                </span>
                            </div>
                        @else
                            <div class="font-weight-bold mb-1" style="color:#2d3748;font-size:16px;">
                                {{ $order->customer_name ?? '—' }}
                                <span class="badge badge-secondary ml-1" style="font-size:10px;">Not Registered</span>
                            </div>
                            <div class="text-muted" style="font-size:13px;">
                                <i class="fas fa-phone mr-1"></i>{{ $order->phone ?? '—' }}
                            </div>
                            @if($order->city)
                            <div class="text-muted" style="font-size:13px;">
                                <i class="fas fa-map-marker-alt mr-1"></i>{{ $order->city }}
                            </div>
                            @endif
                            @if($order->status === 'pending')
                            <div class="mt-3 p-2 rounded" style="background:#fff3cd;font-size:12px;color:#856404;">
                                <i class="fas fa-info-circle mr-1"></i>
                                Confirming this order will auto-register them as a new shop.
                            </div>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Shop sales history (only if registered) --}}
                @if($order->shop && $shopSalesCount !== null)
                <div class="card border-0 shadow-sm" style="border-radius:12px;">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold" style="color:#1cc88a;">
                            <i class="fas fa-history mr-2"></i>Shop History
                        </h6>
                    </div>
                    <div class="card-body py-3 px-4">
                        <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid #f0f0f0;">
                            <span style="font-size:13px;color:#555;">Total Sales</span>
                            <strong style="color:#2d3748;">{{ $shopSalesCount }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid #f0f0f0;">
                            <span style="font-size:13px;color:#555;">Total Revenue</span>
                            <strong style="color:#2d3748;">{{ number_format($shopSalesTotal, 0) }} PKR</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span style="font-size:13px;color:#555;">Outstanding</span>
                            <strong style="color:{{ $shopSalesPending > 0 ? '#e74a3b' : '#1cc88a' }};">
                                {{ number_format($shopSalesPending, 0) }} PKR
                            </strong>
                        </div>
                        <a href="{{ route('admin.sales.by_shop', ['shop_id' => $order->shop_id]) }}"
                           class="btn btn-block btn-sm mt-3"
                           style="background:#e8f0fe;color:#4e73df;border-radius:8px;border:1px solid #c3d3f7;font-size:12px;">
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
