<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('admin.shop_name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(150deg, #0a2e18 0%, #1a5c35 50%, #0d3d20 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                radial-gradient(circle at 15% 25%, rgba(255,255,255,.05) 0%, transparent 45%),
                radial-gradient(circle at 85% 75%, rgba(45,122,64,.2) 0%, transparent 50%);
            pointer-events: none;
        }
        .landing-card {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.13);
            border-radius: 22px;
            backdrop-filter: blur(14px);
            padding: 44px 38px;
            max-width: 460px;
            width: 100%;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        .brand-logo {
            width: 100px;
            height: 100px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            overflow: hidden;
            box-shadow: 0 10px 36px rgba(0,0,0,.35);
        }
        .brand-logo img { width: 88px; height: 88px; object-fit: contain; }
        .brand-name {
            font-size: 18px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.3px;
            line-height: 1.3;
        }
        .brand-sub {
            color: rgba(255,255,255,.5);
            font-size: 12px;
            margin-top: 5px;
        }
        .action-btn {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 18px 22px;
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
            background: linear-gradient(135deg,#2d7a4f,#1a5c35);
            box-shadow: 0 6px 24px rgba(26,92,53,.45);
            border: 1px solid rgba(255,255,255,.12);
        }
        .btn-order .btn-icon { background: rgba(255,255,255,.18); color: #fff; }
        .btn-order .btn-title { color: #fff; font-weight: 700; font-size: 16px; }
        .btn-order .btn-desc  { color: rgba(255,255,255,.7); font-size: 12px; }

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
            color: rgba(255,255,255,.28);
            font-size: 11px;
            margin-top: 22px;
        }
    </style>
</head>
<body>
    <div class="landing-card">

        <div class="brand-logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Pak Namak Logo">
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
