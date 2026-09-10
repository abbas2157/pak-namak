@php
    $shop = $sale->shop ?? null;
    $sizeLabel = fn ($gram) => $gram >= 1000 ? (($gram / 1000) . 'kg') : ($gram . 'g');
@endphp

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Spice Receipt #{{ $sale->id }}</title>
    <link rel="stylesheet" href="{{ asset('css/pn-receipt.css') }}">
</head>
<body>

<div class="no-print-bar">
    <button class="print-btn" onclick="window.print()">Print / پرنٹ</button>
</div>

<div class="receipt-wrap">
    <div class="receipt-header">
        <img class="receipt-logo" src="{{ asset('assets/images/logo.png') }}" alt="Logo" />
        <div class="receipt-hd-body">
            <p class="receipt-title">Spice Sales Receipt / Bill</p>
            <div class="receipt-meta">
                <div><strong>Shop Name / دکان کا نام:</strong> {{ $shop?->name ?? '-' }}</div>
                <div><strong>Shop Phone / فون:</strong> {{ $shop?->phone_number ?? '-' }}</div>
                <div><strong>Date / تاریخ:</strong> {{ $sale->sale_date ?? '-' }}</div>
                <div><strong>Receipt No / رسید نمبر:</strong> #{{ $sale->id }}</div>
            </div>
        </div>
    </div>

    <table class="receipt-table">
        <thead>
            <tr>
                <th class="col-item">آئٹم / Item</th>
                <th class="text-right col-qty">مقدار / Quantity</th>
                <th class="text-right col-amt">رقم / Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sale->items as $item)
                <tr>
                    <td>{{ $item->spiceType->title ?? 'Spice' }} ({{ $sizeLabel($item->packet_gram) }})</td>
                    <td class="text-right">{{ (int) $item->quantity }} Packets</td>
                    <td class="text-right">Rs. {{ number_format((float) $item->sub_total, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-right">No items / کوئی آئٹم نہیں</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="receipt-total">
        <div>
            <strong>Shop Address / پتہ:</strong>
            <div>{{ $shop?->address ?? '-' }}</div>
        </div>
        <div class="text-right">
            <strong>Total Price / کل قیمت:</strong>
            <div class="receipt-grand">Rs. {{ number_format((float) ($sale->total_amount ?? 0), 2) }}</div>
        </div>
    </div>

    @if(!empty($sale->bill_image))
        <div class="receipt-bill-img">
            <div class="receipt-bill-img-lbl">Bill Image / بل تصویر</div>
            <img src="{{ asset($sale->bill_image) }}" alt="Bill" />
        </div>
    @endif

    @if(($sale->pending_amount ?? 0) > 0)
        <div class="receipt-total">
            <div>
                <strong>Received / وصول شدہ:</strong>
                <div>Rs. {{ number_format((float) $sale->received_amount, 2) }}</div>
            </div>
            <div class="text-right">
                <strong>Pending / باقی:</strong>
                <div class="receipt-grand">Rs. {{ number_format((float) $sale->pending_amount, 2) }}</div>
            </div>
        </div>
    @endif

</div>

@php
    $appName = config('app.name', 'Pak Namak');
    $pakPhone = config('admin.pak_namak.phone', '');
    $pakWebsite = config('admin.pak_namak.website', '');
@endphp
<div class="receipt-footer">
    <div class="receipt-footer-inner">
        <div>
            <strong>{{ $appName }}</strong>
            <div>Phone / فون: {{ $pakPhone ?: '-' }}</div>
            <div>Website / ویب سائٹ: {{ $pakWebsite ?: '-' }}</div>
        </div>
    </div>
</div>

<script>
    window.onload = function(){
        setTimeout(function(){ window.print(); }, 300);
    };
</script>

</body>
</html>
