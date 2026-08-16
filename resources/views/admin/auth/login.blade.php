<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login — {{ config('admin.shop_name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/pn-public.css') }}">
</head>
<body class="pn-bg-gradient">
    <div class="login-wrap">

        <div class="brand-block">
            <div class="logo-ring">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Pak Namak Logo">
            </div>
            <div class="brand-name-login">{{ config('admin.shop_name') }}</div>
            <div class="brand-sub-login">
                <i class="fas fa-phone fa-icon-xs"></i>{{ config('admin.pak_namak.phone') }}
            </div>
        </div>

        <div class="login-card">

            <div class="card-title">Welcome back <small class="ch-sub ch-desc opacity-muted">/ خوش آمدید</small></div>
            <div class="card-hint">Sign in to access the admin panel / ایڈمن پینل تک رسائی کے لیے لاگ ان کریں</div>

            @if(Session::has('success'))
                <div class="login-alert login-alert-success">
                    <i class="fas fa-check-circle"></i> {{ Session::get('success') }}
                </div>
            @endif
            @if(Session::has('error'))
                <div class="login-alert login-alert-danger">
                    <i class="fas fa-exclamation-circle"></i> {{ Session::get('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="login-alert login-alert-danger">
                    <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <div class="field-group">
                    <label class="field-label">Email Address / ای میل پتہ</label>
                    <div class="input-wrap">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" value="{{ old('email') }}"
                               placeholder="admin@example.com" required autofocus>
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">Password / پاس ورڈ</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn-signin">
                    <i class="fas fa-sign-in-alt fa-icon-sm"></i> Sign In / لاگ ان
                </button>
            </form>

            <div class="login-divider">or</div>

            <a href="{{ route('order.form') }}" class="order-link">
                <i class="fas fa-shopping-cart"></i>
                Place an Order — آرڈر کریں
            </a>

            <a href="{{ route('stock.public') }}" class="order-link" style="margin-top: 10px;">
                <i class="fas fa-warehouse"></i>
                View Stock — اسٹاک دیکھیں
            </a>

            <a href="{{ route('spice-order.form') }}" class="order-link" style="margin-top: 10px;">
                <i class="fas fa-pepper-hot"></i>
                Place a Spice Order — مصالحہ آرڈر کریں
            </a>

            <a href="{{ route('spice-stock.public') }}" class="order-link" style="margin-top: 10px;">
                <i class="fas fa-warehouse"></i>
                View Spice Stock — مصالحہ اسٹاک دیکھیں
            </a>

        </div>

        <div class="login-footer">{{ config('admin.pak_namak.website') }}</div>
    </div>
</body>
</html>
