<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Confirmed — {{ config('admin.shop_name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f4f6fc 0%, #e8f0fe 100%);
            font-family: 'Segoe UI', sans-serif;
            display: flex; align-items: center; justify-content: center;
        }
        .confirm-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(0,0,0,.10);
            padding: 40px 36px;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        .success-icon {
            width: 80px; height: 80px;
            background: linear-gradient(135deg,#1cc88a,#17a673);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            font-size: 36px; color: #fff;
            box-shadow: 0 6px 24px rgba(28,200,138,.35);
        }
        .reference-box {
            background: #f0f4ff;
            border: 2px dashed #c3d3f7;
            border-radius: 12px;
            padding: 16px 24px;
            margin: 20px 0;
        }
        .reference-label { font-size: 11px; text-transform: uppercase; letter-spacing: .6px; color: #6c757d; font-weight: 700; }
        .reference-code  { font-size: 26px; font-weight: 800; color: #4e73df; letter-spacing: 1px; }
        .item-list { text-align: left; }
        .item-row {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 0; border-bottom: 1px solid #f0f0f0; font-size: 14px;
        }
        .item-dot {
            width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
        }
        .btn-new-order {
            background: linear-gradient(135deg,#4e73df,#224abe);
            border: none; border-radius: 10px;
            color: #fff; font-weight: 700; padding: 12px 32px;
            text-decoration: none; display: inline-block;
            box-shadow: 0 4px 16px rgba(78,115,223,.35);
        }
        .btn-new-order:hover { color: #fff; opacity: .9; }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="confirm-card mx-auto">

        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>

        <h4 class="fw-bold mb-1" style="color:#2d3748;">Order Submitted!</h4>
        <p class="text-muted mb-0" style="font-size:14px;">آپ کا آرڈر مل گیا ہے۔ ہم جلد رابطہ کریں گے۔</p>
        <p class="text-muted" style="font-size:13px;">We will contact you shortly to confirm.</p>

        <div class="reference-box">
            <div class="reference-label">Your Order Reference</div>
            <div class="reference-code">{{ $order->reference }}</div>
            <div style="font-size:12px;color:#6c757d;margin-top:4px;">
                {{ $order->created_at->format('d M Y, h:i A') }}
            </div>
        </div>

        {{-- Shop / Customer info --}}
        <div class="text-start mb-3 p-3 rounded-3" style="background:#f8f9fc;font-size:13px;">
            <div class="fw-bold text-muted mb-1" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Order From</div>
            <div class="fw-bold" style="color:#2d3748;">{{ $order->display_name }}</div>
            @if($order->display_phone !== '—')
                <div class="text-muted"><i class="fas fa-phone me-1" style="font-size:11px;"></i>{{ $order->display_phone }}</div>
            @endif
            @if($order->shop?->city ?? $order->city)
                <div class="text-muted"><i class="fas fa-map-marker-alt me-1" style="font-size:11px;"></i>{{ $order->shop?->city ?? $order->city }}</div>
            @endif
        </div>

        {{-- Items --}}
        <div class="item-list mb-4">
            <div class="fw-bold text-muted mb-2" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Items Ordered</div>
            @foreach($order->items as $item)
            @php
                $color = match($item->type) {
                    'dalla'   => '#4e73df',
                    'thaila'  => '#1cc88a',
                    'package' => '#f6c23e',
                    default   => '#6c757d',
                };
            @endphp
            <div class="item-row">
                <div class="item-dot" style="background:{{ $color }};"></div>
                <span>{{ $item->label }}</span>
            </div>
            @endforeach
        </div>

        <a href="{{ route('order.form') }}" class="btn-new-order">
            <i class="fas fa-plus me-2"></i>Place Another Order
        </a>

        <div class="mt-4" style="font-size:12px;color:#adb5bd;">
            <i class="fas fa-phone me-1"></i>{{ config('admin.pak_namak.phone') }}
        </div>

    </div>
</div>
</body>
</html>
