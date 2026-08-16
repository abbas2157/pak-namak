<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Spice Stock Availability — {{ config('admin.shop_name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0; font-family: 'Segoe UI', system-ui, sans-serif;
            background: #eef2f0; color: #1a2e22;
        }
        .stk-top {
            background: linear-gradient(135deg,#4a2f7a,#7a4fbf);
            color: #fff; padding: 14px 40px;
            display: flex; align-items: center; justify-content: space-between; gap: 16px;
            box-shadow: 0 3px 18px rgba(0,0,0,.25);
        }
        .stk-brand { display: flex; align-items: center; gap: 16px; }
        .stk-logo { width: 56px; height: 56px; border-radius: 50%; background: #fff; padding: 4px; }
        .stk-logo img { width: 100%; height: 100%; object-fit: contain; }
        .stk-title { font-size: 1.5rem; font-weight: 800; letter-spacing: .3px; }
        .stk-sub { font-size: .95rem; opacity: .85; }
        .stk-back { color: rgba(255,255,255,.75); text-decoration: none; font-size: .85rem; }
        .stk-back:hover { color: #fff; }

        .stk-wrap { max-width: 1400px; margin: 0 auto; padding: 18px 40px 28px; }

        .stk-groups { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; align-items: stretch; }
        .stk-group { background: #fff; border-radius: 14px; box-shadow: 0 4px 16px rgba(0,0,0,.06); overflow: hidden; display: flex; flex-direction: column; }
        .stk-group-head {
            padding: 12px 22px; color: #fff; font-size: 1.05rem; font-weight: 800;
            background: linear-gradient(135deg,#4a2f7a,#7a4fbf); display: flex; align-items: center; gap: 10px;
        }
        .stk-group-body { padding: 8px 18px 10px; flex: 1; }

        .stk-pkg-table { width: 100%; border-collapse: collapse; }
        .stk-pkg-table th {
            text-align: center; font-size: .72rem; font-weight: 700; color: #8a7ab0;
            text-transform: uppercase; padding: 0 4px 8px;
        }
        .stk-pkg-table th:first-child { text-align: left; }
        .stk-pkg-table td { padding: 9px 4px; border-bottom: 1px solid #eef1ef; }
        .stk-pkg-table tr:last-child td { border-bottom: none; }
        .stk-pkg-table .pkg-size { font-size: 1.02rem; font-weight: 700; color: #2a3b31; }
        .stk-pkg-table .pkg-val { text-align: center; font-size: 1.15rem; font-weight: 800; color: #4a2f7a; }
        .stk-pkg-table .pkg-val.neg { color: #c0392b; }
        .stk-pkg-table .pkg-val.zero { color: #b0a6c8; }

        .stk-footer { text-align: center; padding: 24px 0 8px; color: #7a8a80; font-size: .85rem; }
        .stk-order-btn {
            display: block; text-align: center; margin: 32px auto 0; max-width: 420px;
            background: linear-gradient(135deg,#7a4fbf,#4a2f7a); color: #fff; text-decoration: none;
            padding: 16px; border-radius: 12px; font-weight: 800; font-size: 1.05rem;
            box-shadow: 0 4px 18px rgba(74,47,122,.35);
        }

        @media (max-width: 900px) {
            .stk-top { padding: 16px 18px; flex-wrap: wrap; }
            .stk-wrap { padding: 18px 14px 32px; }
            .stk-groups { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="stk-top">
    <div class="stk-brand">
        <div class="stk-logo"><img src="{{ asset('assets/images/logo.png') }}" alt="Logo"></div>
        <div>
            <div class="stk-title">{{ config('admin.shop_name') }}</div>
            <div class="stk-sub"><i class="fas fa-warehouse me-1"></i> مصالحہ اسٹاک — Spice Stock Availability</div>
        </div>
    </div>
    <a href="{{ url('/login') }}" class="stk-back"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="stk-wrap">

    @php $sizeLabel = fn ($gram) => $gram >= 1000 ? (($gram / 1000) . 'kg') : ($gram . 'g'); @endphp

    <div class="stk-groups">
        @foreach($spiceTypes as $spiceType)
            @php
                $lines = $levels->where('spice_type_id', $spiceType->id)->values();
                $grams = $lines->pluck('size')->unique()->sort()->values();
            @endphp
            <div class="stk-group">
                <div class="stk-group-head"><i class="fas fa-pepper-hot"></i> {{ $spiceType->title }}</div>
                <div class="stk-group-body">
                    <table class="stk-pkg-table">
                        <thead>
                            <tr><th>Size</th><th>In Stock (Packets)</th></tr>
                        </thead>
                        <tbody>
                            @foreach($grams as $gram)
                                @php
                                    $line = $lines->first(fn ($l) => $l['size'] == $gram);
                                    $qty = $line['quantity'] ?? 0;
                                @endphp
                                <tr>
                                    <td class="pkg-size">{{ $sizeLabel($gram) }}</td>
                                    <td class="pkg-val {{ $qty < 0 ? 'neg' : ($qty == 0 ? 'zero' : '') }}" id="stk-pkg-{{ $spiceType->id }}-{{ $gram }}">{{ number_format($qty, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>

    <a href="{{ route('spice-order.form') }}" class="stk-order-btn">
        <i class="fas fa-shopping-cart me-2"></i>Place an Order — آرڈر کریں
    </a>

    <div class="stk-footer">
        <i class="fas fa-phone me-1"></i>{{ config('admin.pak_namak.phone') }}
        &nbsp;·&nbsp; {{ config('admin.pak_namak.website') }}
        &nbsp;·&nbsp; <span class="stk-live-dot"></span>Live
    </div>

</div>

<script>
    const STOCK_DATA_URL = '{{ route('spice-stock.data') }}';

    function refreshStock() {
        fetch(STOCK_DATA_URL).then(r => r.json()).then(data => {
            (data.levels || []).forEach(line => {
                const qty = parseFloat(line.quantity) || 0;
                const el = document.getElementById('stk-pkg-' + line.spice_type_id + '-' + line.size);
                if (el) {
                    el.textContent = Math.round(qty).toLocaleString();
                    el.classList.remove('neg', 'zero');
                    if (qty < 0) el.classList.add('neg');
                    else if (qty === 0) el.classList.add('zero');
                }
            });
        }).catch(() => {});
    }

    refreshStock();
    setInterval(refreshStock, 5000);
</script>
</body>
</html>
