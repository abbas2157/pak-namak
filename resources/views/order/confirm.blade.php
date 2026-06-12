<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Confirmed — {{ config('admin.shop_name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/pn-public.css') }}">
</head>
<body class="pn-green-bg">
<div class="container py-4">
    <div class="confirm-card mx-auto">

        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>

        <h4 class="fw-bold mb-1 pn-text-heading">Order Submitted!</h4>
        <p class="text-muted mb-0">آپ کا آرڈر مل گیا ہے۔ ہم جلد رابطہ کریں گے۔</p>
        <p class="text-muted">We will contact you shortly to confirm.</p>

        <div class="reference-box">
            <div class="reference-label">Your Order Reference</div>
            <div class="reference-code">{{ $order->reference }}</div>
            <div class="ref-date">
                {{ $order->created_at->format('d M Y, h:i A') }}
            </div>
        </div>

        {{-- Shop / Customer info --}}
        <div class="text-start mb-3 p-3 rounded-3 order-from-box">
            <div class="fw-bold text-muted mb-1 order-from-lbl">Order From</div>
            <div class="fw-bold pn-text-heading">{{ $order->display_name }}</div>
            @if($order->display_phone !== '—')
                <div class="text-muted"><i class="fas fa-phone me-1 fa-icon-xs"></i>{{ $order->display_phone }}</div>
            @endif
            @if($order->shop?->city ?? $order->city)
                <div class="text-muted"><i class="fas fa-map-marker-alt me-1 fa-icon-xs"></i>{{ $order->shop?->city ?? $order->city }}</div>
            @endif
        </div>

        {{-- Items --}}
        <div class="item-list mb-4">
            <div class="fw-bold text-muted mb-2 order-from-lbl">Items Ordered</div>
            @php $grandTotal = 0; @endphp
            @foreach($order->items as $item)
            @php
                $dotClass = match($item->type) {
                    'dalla'   => 'item-dot-dalla',
                    'thaila'  => 'item-dot-thaila',
                    'package' => 'item-dot-package',
                    default   => 'item-dot-other',
                };
                $grandTotal += $item->sub_total ?? 0;
            @endphp
            <div class="item-row">
                <div class="item-left">
                    <div class="item-dot {{ $dotClass }}"></div>
                    <span>{{ $item->label }}</span>
                </div>
                @if($item->sub_total)
                    <span class="item-subtotal">PKR {{ number_format($item->sub_total, 0) }}</span>
                @endif
            </div>
            @endforeach
            @if($grandTotal > 0)
            <div class="grand-total-row">
                <span class="gt-label">Grand Total — کل رقم</span>
                <span class="gt-value">PKR {{ number_format($grandTotal, 0) }}</span>
            </div>
            @endif
        </div>

        <a href="{{ route('order.form') }}" class="btn-new-order">
            <i class="fas fa-plus me-2"></i>Place Another Order
        </a>

        <div class="mt-4 confirm-footer">
            <i class="fas fa-phone me-1"></i>{{ config('admin.pak_namak.phone') }}
        </div>

    </div>
</div>
</body>
</html>
