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

        <div class="item-list mb-4">
            <div class="fw-bold text-muted mb-2 order-from-lbl">Items Ordered</div>
            @php $grandTotal = 0; @endphp
            @foreach($order->items as $item)
            @php $grandTotal += $item->sub_total ?? 0; @endphp
            <div class="item-row">
                <div class="item-left">
                    <div class="item-dot item-dot-package"></div>
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

        @php
            $waFooter = "\n———————————\n"
                      . config('admin.shop_name') . "\n"
                      . config('admin.shop_name_urdu') . "\n"
                      . '📞 ' . config('admin.pak_namak.phone') . "\n"
                      . '📅 ' . now()->format('d M Y');

            $waMsg = 'آرڈر: ' . $order->reference . "\n"
                   . 'دکان: ' . $order->display_name . "\n"
                   . ($grandTotal > 0 ? "کل رقم: PKR " . number_format($grandTotal, 0) . "\n" : '')
                   . 'تاریخ: ' . $order->created_at->format('d M Y')
                   . $waFooter;
            $rawPhone = $order->shop?->phone_number ?? $order->phone ?? '';
            $waPhone  = '';
            if ($rawPhone) {
                $digits = preg_replace('/\D/', '', $rawPhone);
                if (str_starts_with($digits, '0')) $digits = '92' . substr($digits, 1);
                elseif (!str_starts_with($digits, '92')) $digits = '92' . $digits;
                $waPhone = $digits;
            }
        @endphp
        <a href="https://wa.me/{{ $waPhone }}?text={{ rawurlencode($waMsg) }}"
           target="_blank" rel="noopener" class="btn-new-order"
           style="background:#25D366;border:none;display:block;text-decoration:none;text-align:center;">
            <i class="fab fa-whatsapp me-2"></i>واٹس ایپ پر شیئر کریں · Share on WhatsApp
        </a>

        <div style="height:12px;"></div>

        <a href="{{ route('spice-order.form') }}" class="btn-new-order">
            <i class="fas fa-plus me-2"></i>Place Another Order
        </a>

        <div class="mt-4 confirm-footer">
            <i class="fas fa-phone me-1"></i>{{ config('admin.pak_namak.phone') }}
        </div>

    </div>
</div>
</body>
</html>
