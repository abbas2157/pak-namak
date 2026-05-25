<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('admin.shop_name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1f5e 0%, #2d3494 50%, #1a1f5e 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .landing-card {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 20px;
            backdrop-filter: blur(12px);
            padding: 48px 40px;
            max-width: 480px;
            width: 100%;
            text-align: center;
        }
        .brand-logo {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg,#4e73df,#224abe);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 30px;
            color: white;
            box-shadow: 0 8px 32px rgba(78,115,223,.4);
        }
        .brand-name {
            font-size: 22px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }
        .brand-sub {
            color: rgba(255,255,255,.55);
            font-size: 13px;
            margin-top: 6px;
        }
        .action-btn {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 18px 24px;
            border-radius: 14px;
            text-decoration: none;
            transition: transform .15s, box-shadow .15s;
            margin-bottom: 14px;
            text-align: left;
        }
        .action-btn:hover { transform: translateY(-2px); }
        .action-btn .btn-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        .btn-order {
            background: linear-gradient(135deg,#1cc88a,#17a673);
            box-shadow: 0 6px 24px rgba(28,200,138,.35);
        }
        .btn-order .btn-icon { background: rgba(255,255,255,.2); color: #fff; }
        .btn-order .btn-title { color: #fff; font-weight: 700; font-size: 16px; }
        .btn-order .btn-desc  { color: rgba(255,255,255,.75); font-size: 12px; }

        .btn-admin {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            box-shadow: 0 2px 12px rgba(0,0,0,.15);
        }
        .btn-admin .btn-icon { background: rgba(255,255,255,.12); color: rgba(255,255,255,.8); }
        .btn-admin .btn-title { color: rgba(255,255,255,.9); font-weight: 600; font-size: 16px; }
        .btn-admin .btn-desc  { color: rgba(255,255,255,.45); font-size: 12px; }
        .btn-admin:hover { background: rgba(255,255,255,0.13); }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 8px 0 14px;
            color: rgba(255,255,255,.3);
            font-size: 12px;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,.15);
        }
        .footer-text {
            color: rgba(255,255,255,.3);
            font-size: 11px;
            margin-top: 24px;
        }
    </style>
</head>
<body>
    <div class="landing-card">

        <div class="brand-logo">
            <i class="fas fa-cubes"></i>
        </div>

        <div class="brand-name">{{ config('admin.shop_name') }}</div>
        <div class="brand-sub">
            <i class="fas fa-phone me-1"></i>{{ config('admin.pak_namak.phone') }}
        </div>

        <div style="margin-top: 36px;">

            <a href="{{ route('order.form') }}" class="action-btn btn-order">
                <div class="btn-icon"><i class="fas fa-shopping-cart"></i></div>
                <div>
                    <div class="btn-title">Place an Order</div>
                    <div class="btn-desc">آرڈر کریں — No account needed</div>
                </div>
                <i class="fas fa-chevron-right ms-auto" style="color:rgba(255,255,255,.5);"></i>
            </a>

            <div class="divider">or</div>

            <a href="{{ route('dashboard') }}" class="action-btn btn-admin">
                <div class="btn-icon"><i class="fas fa-tachometer-alt"></i></div>
                <div>
                    <div class="btn-title">Admin Dashboard</div>
                    <div class="btn-desc">Business management panel</div>
                </div>
                <i class="fas fa-chevron-right ms-auto" style="color:rgba(255,255,255,.25);"></i>
            </a>

        </div>

        <div class="footer-text">
            {{ config('admin.pak_namak.website') }}
        </div>

    </div>
</body>
</html>
