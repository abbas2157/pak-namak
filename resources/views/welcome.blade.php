<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('admin.shop_name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/pn-public.css') }}">
</head>
<body class="pn-bg-gradient">
    <div class="landing-card">

        <div class="brand-logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Pak Namak Logo">
        </div>

        <div class="brand-name">{{ config('admin.shop_name') }}</div>
        <div class="brand-sub">
            <i class="fas fa-phone me-1"></i>{{ config('admin.pak_namak.phone') }}
        </div>

        <div class="mt-5">

            <a href="{{ route('order.form') }}" class="action-btn-landing btn-order-landing">
                <div class="abl-icon"><i class="fas fa-shopping-cart"></i></div>
                <div>
                    <div class="abl-title">Place an Order</div>
                    <div class="abl-desc">آرڈر کریں — No account needed</div>
                </div>
                <i class="fas fa-chevron-right ms-auto abl-arrow-bright"></i>
            </a>

            <a href="{{ route('spice-order.form') }}" class="action-btn-landing btn-order-landing">
                <div class="abl-icon"><i class="fas fa-pepper-hot"></i></div>
                <div>
                    <div class="abl-title">Place a Spice Order</div>
                    <div class="abl-desc">مصالحہ آرڈر کریں — No account needed</div>
                </div>
                <i class="fas fa-chevron-right ms-auto abl-arrow-bright"></i>
            </a>

            <div class="pn-divider">or</div>

            <a href="{{ route('dashboard') }}" class="action-btn-landing btn-admin-landing">
                <div class="abl-icon"><i class="fas fa-tachometer-alt"></i></div>
                <div>
                    <div class="abl-title">Admin Dashboard</div>
                    <div class="abl-desc">Business management panel</div>
                </div>
                <i class="fas fa-chevron-right ms-auto abl-arrow"></i>
            </a>

        </div>

        <div class="footer-text">
            {{ config('admin.pak_namak.website') }}
        </div>

    </div>
</body>
</html>
