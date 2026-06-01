<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login — {{ config('admin.shop_name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            background: linear-gradient(150deg, #0a2e18 0%, #1a5c35 50%, #0d3d20 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            padding: 20px;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                radial-gradient(circle at 15% 25%, rgba(255,255,255,.06) 0%, transparent 45%),
                radial-gradient(circle at 85% 75%, rgba(45,122,64,.25) 0%, transparent 50%);
            pointer-events: none;
        }

        .login-wrap {
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
        }

        /* Brand / logo block */
        .brand-block {
            text-align: center;
            margin-bottom: 24px;
        }
        .logo-ring {
            width: 110px;
            height: 110px;
            background: #fff;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 12px 40px rgba(0,0,0,.35);
            margin-bottom: 14px;
            overflow: hidden;
        }
        .logo-ring img {
            width: 96px;
            height: 96px;
            object-fit: contain;
        }
        .brand-name {
            font-size: 15px;
            font-weight: 800;
            color: #fff;
            line-height: 1.35;
            letter-spacing: -.2px;
        }
        .brand-sub {
            font-size: 12px;
            color: rgba(255,255,255,.45);
            margin-top: 5px;
        }

        /* Card */
        .login-card {
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 20px;
            backdrop-filter: blur(16px);
            padding: 36px 32px 28px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 4px;
        }
        .card-hint {
            font-size: 12px;
            color: rgba(255,255,255,.4);
            margin-bottom: 26px;
        }

        /* Alerts */
        .alert {
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .alert-success { background: rgba(74,196,120,.15); color: #4ac478; border: 1px solid rgba(74,196,120,.3); }
        .alert-danger  { background: rgba(220,53,69,.18);  color: #f08080; border: 1px solid rgba(220,53,69,.3); }

        /* Form */
        .field-group { margin-bottom: 18px; }
        .field-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: rgba(255,255,255,.5);
            margin-bottom: 8px;
        }
        .input-wrap { position: relative; }
        .input-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,.3);
            font-size: 14px;
        }
        .input-wrap input {
            width: 100%;
            padding: 12px 14px 12px 40px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 10px;
            color: #fff;
            font-size: 14px;
            outline: none;
            transition: border-color .2s, background .2s;
        }
        .input-wrap input::placeholder { color: rgba(255,255,255,.28); }
        .input-wrap input:focus {
            border-color: rgba(74,196,120,.6);
            background: rgba(255,255,255,.12);
        }

        /* Submit */
        .btn-signin {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #2d7a4f, #1a5c35);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 10px;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(0,0,0,.3);
            transition: opacity .15s, transform .15s;
            margin-top: 8px;
        }
        .btn-signin:hover { opacity: .9; transform: translateY(-1px); }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 22px 0 18px;
            color: rgba(255,255,255,.25);
            font-size: 11px;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,.12);
        }

        /* Order link */
        .order-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 13px 20px;
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 10px;
            text-decoration: none;
            color: rgba(255,255,255,.8);
            font-size: 13px;
            font-weight: 600;
            transition: background .15s;
        }
        .order-link:hover { background: rgba(255,255,255,.13); color: #fff; text-decoration: none; }

        .login-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 11px;
            color: rgba(255,255,255,.25);
        }
    </style>
</head>
<body>
    <div class="login-wrap">

        <div class="brand-block">
            <div class="logo-ring">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Pak Namak Logo">
            </div>
            <div class="brand-name">{{ config('admin.shop_name') }}</div>
            <div class="brand-sub">
                <i class="fas fa-phone" style="font-size:10px;margin-right:4px;"></i>{{ config('admin.pak_namak.phone') }}
            </div>
        </div>

        <div class="login-card">

            <div class="card-title">Welcome back <small style="font-size:13px;font-weight:400;opacity:.7;">/ خوش آمدید</small></div>
            <div class="card-hint">Sign in to access the admin panel / ایڈمن پینل تک رسائی کے لیے لاگ ان کریں</div>

            @if(Session::has('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ Session::get('success') }}
                </div>
            @endif
            @if(Session::has('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> {{ Session::get('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
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
                    <i class="fas fa-sign-in-alt" style="margin-right:8px;"></i> Sign In / لاگ ان
                </button>
            </form>

            <div class="divider">or</div>

            <a href="{{ route('order.form') }}" class="order-link">
                <i class="fas fa-shopping-cart"></i>
                Place an Order — آرڈر کریں
            </a>

        </div>

        <div class="login-footer">{{ config('admin.pak_namak.website') }}</div>
    </div>
</body>
</html>
