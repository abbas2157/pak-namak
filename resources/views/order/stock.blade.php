<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stock Availability — {{ config('admin.shop_name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0; font-family: 'Segoe UI', system-ui, sans-serif;
            background: #eef2f0; color: #1a2e22;
        }
        .stk-top {
            background: linear-gradient(135deg,#0a2e18,#1a5c35,#2d7a4f);
            color: #fff; padding: 14px 40px;
            display: flex; align-items: center; justify-content: space-between; gap: 16px;
            box-shadow: 0 3px 18px rgba(0,0,0,.25);
        }
        .stk-brand { display: flex; align-items: center; gap: 16px; }
        .stk-logo { width: 56px; height: 56px; border-radius: 50%; background: #fff; padding: 4px; }
        .stk-logo img { width: 100%; height: 100%; object-fit: contain; }
        .stk-title { font-size: 1.5rem; font-weight: 800; letter-spacing: .3px; }
        .stk-sub { font-size: .95rem; opacity: .85; }
        .stk-clock { text-align: right; font-size: 1rem; opacity: .9; }
        .stk-clock .stk-time { font-size: 1.4rem; font-weight: 700; }
        .stk-back { color: rgba(255,255,255,.75); text-decoration: none; font-size: .85rem; }
        .stk-back:hover { color: #fff; }

        .stk-wrap { max-width: 1700px; margin: 0 auto; padding: 18px 40px 28px; }

        .stk-hero { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px; }
        .stk-hero-tile {
            background: #fff; border-radius: 14px; padding: 16px 24px;
            box-shadow: 0 4px 16px rgba(0,0,0,.06); border-left: 6px solid #2d7a4f;
            display: flex; align-items: center; justify-content: space-between;
        }
        .stk-hero-tile.thaila-tile  { border-left-color: #1a8a8a; }
        .stk-hero-tile.package-tile { border-left-color: #7a4fbf; }
        .stk-hero-lbl { font-size: 1rem; font-weight: 700; color: #4a5a52; margin-bottom: 4px; }
        .stk-hero-val { font-size: 2.1rem; font-weight: 800; color: #16301f; line-height: 1; }
        .stk-hero-icon { font-size: 1.9rem; color: #2d7a4f; opacity: .35; }
        .thaila-tile .stk-hero-icon  { color: #1a8a8a; }
        .package-tile .stk-hero-icon { color: #7a4fbf; }

        .stk-groups { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; align-items: stretch; }
        .stk-group { background: #fff; border-radius: 14px; box-shadow: 0 4px 16px rgba(0,0,0,.06); overflow: hidden; display: flex; flex-direction: column; }
        .stk-group-head {
            padding: 12px 22px; color: #fff; font-size: 1.05rem; font-weight: 800;
            background: linear-gradient(135deg,#1a5c35,#2d7a4f); display: flex; align-items: center; gap: 10px;
        }
        .stk-group.thaila-group  .stk-group-head { background: linear-gradient(135deg,#0d5c5c,#1a8a8a); }
        .stk-group.package-group .stk-group-head { background: linear-gradient(135deg,#4a2f7a,#7a4fbf); }
        .stk-group-body { padding: 8px 18px 10px; flex: 1; display: flex; flex-direction: column; justify-content: center; }

        .stk-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 9px 6px; border-bottom: 1px solid #eef1ef;
        }
        .stk-row:last-child { border-bottom: none; }
        .stk-row-label { font-size: 1rem; font-weight: 700; color: #2a3b31; }
        .stk-row-value { font-size: 1.15rem; font-weight: 800; }
        .stk-row-value.neg { color: #c0392b; }
        .stk-row-value.pos { color: #1a5c35; }
        .stk-row-value.zero { color: #8a978f; }

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
        .stk-live-dot {
            display: inline-block; width: 8px; height: 8px; border-radius: 50%;
            background: #2d7a4f; margin-right: 5px; animation: stkPulse 1.5s ease-in-out infinite;
        }
        @keyframes stkPulse { 0%,100% { opacity: 1; } 50% { opacity: .3; } }
        .stk-order-btn {
            display: block; text-align: center; margin: 32px auto 0; max-width: 420px;
            background: linear-gradient(135deg,#2d7a4f,#1a5c35); color: #fff; text-decoration: none;
            padding: 16px; border-radius: 12px; font-weight: 800; font-size: 1.05rem;
            box-shadow: 0 4px 18px rgba(26,92,53,.35);
        }

        /* Mobile fallback — collapse the desktop/LCD dashboard to one column */
        @media (max-width: 900px) {
            .stk-top { padding: 16px 18px; flex-wrap: wrap; }
            .stk-clock { display: none; }
            .stk-wrap { padding: 18px 14px 32px; }
            .stk-hero, .stk-groups { grid-template-columns: 1fr; }
            .stk-hero-val { font-size: 2rem; }
        }
    </style>
</head>
<body>

<div class="stk-top">
    <div class="stk-brand">
        <div class="stk-logo"><img src="{{ asset('assets/images/logo.png') }}" alt="Logo"></div>
        <div>
            <div class="stk-title">{{ config('admin.shop_name') }}</div>
            <div class="stk-sub"><i class="fas fa-warehouse me-1"></i> اسٹاک — Stock Availability</div>
        </div>
    </div>
    <div class="stk-clock">
        <div class="stk-time" id="stkTime"></div>
        <div id="stkDate"></div>
    </div>
    <a href="{{ url('/login') }}" class="stk-back"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<div class="stk-wrap">

    @php
    $dalla    = $levels->firstWhere('product_type', 'dalla');
    $thailas  = $levels->where('product_type', 'thaila')->values();
    $packages = $levels->where('product_type', 'package')->values();
    $packageGrams = collect($packages)->pluck('size')->unique()->values();

    $totalThailaBags = $thailas->sum('quantity');
    $totalPackageKg  = $packages->sum('quantity_kg');
    @endphp

    {{-- Hero summary --}}
    <div class="stk-hero">
        <div class="stk-hero-tile">
            <div>
                <div class="stk-hero-lbl">Dalla — کل ڈلہ</div>
                <div class="stk-hero-val"><span id="stk-hero-dalla">{{ number_format($dalla['quantity'] ?? 0, 0) }}</span> <small style="font-size:1.1rem;">Mann</small></div>
            </div>
            <i class="fas fa-tint stk-hero-icon"></i>
        </div>
        <div class="stk-hero-tile thaila-tile">
            <div>
                <div class="stk-hero-lbl">Thaila (all sizes) — کل تھیلا</div>
                <div class="stk-hero-val"><span id="stk-hero-thaila">{{ number_format($totalThailaBags, 0) }}</span> <small style="font-size:1.1rem;">Bags</small></div>
            </div>
            <i class="fas fa-shopping-bag stk-hero-icon"></i>
        </div>
        <div class="stk-hero-tile package-tile">
            <div>
                <div class="stk-hero-lbl">Package (all packs) — کل پیکٹ</div>
                <div class="stk-hero-val"><span id="stk-hero-package">{{ number_format($totalPackageKg, 0) }}</span> <small style="font-size:1.1rem;">KG</small></div>
            </div>
            <i class="fas fa-box stk-hero-icon"></i>
        </div>
    </div>

    {{-- Detail columns --}}
    <div class="stk-groups">

        {{-- Dalla --}}
        <div class="stk-group">
            <div class="stk-group-head"><i class="fas fa-tint"></i> Dalla — ڈلہ</div>
            <div class="stk-group-body">
                @php $q = $dalla['quantity'] ?? 0; @endphp
                <div class="stk-row">
                    <span class="stk-row-label">Bulk — ڈلہ</span>
                    <span class="stk-row-value {{ $q < 0 ? 'neg' : ($q > 0 ? 'pos' : 'zero') }}" id="stk-dalla-val">
                        {{ number_format($q, 2) }} Mann
                    </span>
                </div>
            </div>
        </div>

        {{-- Thaila --}}
        <div class="stk-group thaila-group">
            <div class="stk-group-head"><i class="fas fa-shopping-bag"></i> Thaila — تھیلا</div>
            <div class="stk-group-body">
                @foreach($thailas as $line)
                    @php $q = $line['quantity']; @endphp
                    <div class="stk-row">
                        <span class="stk-row-label">{{ $line['size'] }} KG bag</span>
                        <span class="stk-row-value {{ $q < 0 ? 'neg' : ($q > 0 ? 'pos' : 'zero') }}" id="stk-thaila-{{ $line['size'] }}">
                            {{ number_format($q, 0) }} Bags
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Package --}}
        <div class="stk-group package-group">
            <div class="stk-group-head"><i class="fas fa-box"></i> Package — پیکٹ</div>
            <div class="stk-group-body">
                <table class="stk-pkg-table">
                    <thead>
                        <tr><th>Size</th><th>10-Pack</th><th>20-Pack</th></tr>
                    </thead>
                    <tbody>
                        @foreach($packageGrams as $gram)
                            @php
                                $p10 = $packages->firstWhere(fn ($l) => $l['size'] == $gram && $l['bundle_size'] == 10);
                                $p20 = $packages->firstWhere(fn ($l) => $l['size'] == $gram && $l['bundle_size'] == 20);
                                $v10 = $p10['quantity'] ?? 0;
                                $v20 = $p20['quantity'] ?? 0;
                            @endphp
                            <tr>
                                <td class="pkg-size">{{ $gram }}g</td>
                                <td class="pkg-val {{ $v10 < 0 ? 'neg' : ($v10 == 0 ? 'zero' : '') }}" id="stk-pkg-{{ $gram }}-10">{{ number_format($v10, 0) }}</td>
                                <td class="pkg-val {{ $v20 < 0 ? 'neg' : ($v20 == 0 ? 'zero' : '') }}" id="stk-pkg-{{ $gram }}-20">{{ number_format($v20, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <a href="{{ route('order.form') }}" class="stk-order-btn">
        <i class="fas fa-shopping-cart me-2"></i>Place an Order — آرڈر کریں
    </a>

    <div class="stk-footer">
        <i class="fas fa-phone me-1"></i>{{ config('admin.pak_namak.phone') }}
        &nbsp;·&nbsp; {{ config('admin.pak_namak.website') }}
        &nbsp;·&nbsp; <span class="stk-live-dot"></span>Live
    </div>

</div>

<script>
    // ── Live clock (ticks every second) ─────────────────────
    function stkTick() {
        const now = new Date();
        document.getElementById('stkTime').textContent = now.toLocaleTimeString('en-PK', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        document.getElementById('stkDate').textContent = now.toLocaleDateString('en-PK', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
    }
    stkTick();
    setInterval(stkTick, 1000);

    // ── Live stock totals (polls the JSON feed, updates numbers in place) ──
    const STOCK_DATA_URL = '{{ route('stock.data') }}';

    function stkApplyClass(el, qty) {
        el.classList.remove('neg', 'pos', 'zero');
        el.classList.add(qty < 0 ? 'neg' : (qty > 0 ? 'pos' : 'zero'));
    }

    function refreshStock() {
        fetch(STOCK_DATA_URL).then(r => r.json()).then(data => {
            let totalThailaBags = 0, totalPackageKg = 0, dallaQty = 0;

            (data.levels || []).forEach(line => {
                const qty   = parseFloat(line.quantity) || 0;
                const qtyKg = parseFloat(line.quantity_kg) || 0;

                if (line.product_type === 'dalla') {
                    dallaQty = qty;
                    const el = document.getElementById('stk-dalla-val');
                    if (el) { el.textContent = qty.toFixed(2) + ' Mann'; stkApplyClass(el, qty); }
                } else if (line.product_type === 'thaila') {
                    totalThailaBags += qty;
                    const el = document.getElementById('stk-thaila-' + line.size);
                    if (el) { el.textContent = Math.round(qty).toLocaleString() + ' Bags'; stkApplyClass(el, qty); }
                } else if (line.product_type === 'package') {
                    totalPackageKg += qtyKg;
                    const el = document.getElementById('stk-pkg-' + line.size + '-' + line.bundle_size);
                    if (el) {
                        el.textContent = Math.round(qty).toLocaleString();
                        el.classList.remove('neg', 'zero');
                        if (qty < 0) el.classList.add('neg');
                        else if (qty === 0) el.classList.add('zero');
                    }
                }
            });

            document.getElementById('stk-hero-dalla').textContent   = Math.round(dallaQty).toLocaleString();
            document.getElementById('stk-hero-thaila').textContent  = Math.round(totalThailaBags).toLocaleString();
            document.getElementById('stk-hero-package').textContent = Math.round(totalPackageKg).toLocaleString();
        }).catch(() => {});
    }

    refreshStock();
    setInterval(refreshStock, 5000);
</script>
</body>
</html>
