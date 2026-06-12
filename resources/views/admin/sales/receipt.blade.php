@php
    use Illuminate\Support\Facades\URL;
    $shop = $sale->shop ?? null;
@endphp

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt #{{ $sale->id }}</title>
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
            <p class="receipt-title">Sales Receipt / Bill</p>
            <div class="receipt-meta">
                <div><strong>Shop Name / دکان کا نام:</strong> {{ $shop?->name ?? '-' }}</div>
                <div><strong>Shop Phone / فون:</strong> {{ $shop?->phone_number ?? $shop?->phone ?? '-' }}</div>
                <div><strong>Date / تاریخ:</strong> {{ $sale->sale_date ?? '-' }}</div>
                <div><strong>Receipt No / رسید نمبر:</strong> #{{ $sale->id }}</div>
            </div>
        </div>
    </div>

    @php
        $lines = [];
        if ($sale->dalla) {
            $lines[] = [
                'name' => 'Dalla',
                'qty' => ($sale->dalla->quantity_mann ?? 0) . ' Mann',
                'amount' => $sale->dalla->sub_total ?? 0,
            ];
        }

        if ($sale->thailas && $sale->thailas->count()) {
            foreach ($sale->thailas as $t) {
                $lines[] = [
                    'name' => 'Thaila (' . ($t->bag_size_kg ?? '-') . ' Kg)',
                    'qty' => ($t->quantity ?? 0) . ' Thaila',
                    'amount' => $t->sub_total ?? 0,
                ];
            }
        }

        if ($sale->packages && $sale->packages->count()) {
            foreach ($sale->packages as $p) {
                $lines[] = [
                    'name' => 'Package (' . ($p->packet_gram ?? '-') . ' g)',
                    'qty' => ($p->bundle_quantity ?? 0) . ' Bundles',
                    'amount' => $p->sub_total ?? 0,
                ];
            }
        }
    @endphp

    <table class="receipt-table">
        <thead>
            <tr>
                <th class="col-item">آئٹم / Item</th>
                <th class="text-right col-qty">مقدار / Quantity</th>
                <th class="text-right col-amt">رقم / Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lines as $line)
                <tr>
                    <td>{{ $line['name'] }}</td>
                    <td class="text-right">{{ $line['qty'] }}</td>
                    <td class="text-right">Rs. {{ number_format((float)$line['amount'], 2) }}</td>
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
            <div class="receipt-grand">Rs. {{ number_format((float)($sale->total_amount ?? 0), 2) }}</div>
        </div>
    </div>

    @if(!empty($sale->bill_image))
        <div class="receipt-bill-img">
            <div class="receipt-bill-img-lbl">Bill Image / بل تصویر</div>
            <img src="{{ asset($sale->bill_image) }}" alt="Bill" />
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
