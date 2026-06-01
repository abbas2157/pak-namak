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

    <style>
        @page { margin: 12mm; }
        body { font-family: Arial, Helvetica, sans-serif; color:#111; }
        .no-print { display:none; }

        .receipt-wrap {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 16px;
        }
        .receipt-header {
            display:flex;
            gap:14px;
            align-items:flex-start;
            margin-bottom: 12px;
        }
        .receipt-logo {
            width: 64px;
            height: 64px;
            object-fit: contain;
            background: #fff;
        }
        .receipt-title { font-size: 18px; font-weight: 800; margin:0; }
        .receipt-meta { font-size: 13px; line-height: 1.6; }

        table { width:100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border:1px solid #e9ecef; padding: 8px; font-size: 13px; }
        th { background:#f7f8fa; }
        .text-right { text-align:right; }

        .receipt-total {
            margin-top: 14px;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 16px;
            display:flex;
            justify-content: space-between;
            align-items:center;
            gap: 12px;
        }
        .receipt-total strong { display:block; }
        .grand {
            font-size: 18px;
            font-weight: 900;
        }

        @media print {
            .no-print { display:none !important; }
        }
    </style>
</head>
<body>

<div class="no-print" style="text-align:right; margin-bottom:10px;">
    <button onclick="window.print()" style="padding:8px 12px; cursor:pointer;">Print / پرنٹ</button>
</div>

<div class="receipt-wrap">
    <div class="receipt-header">
        <div>
            <img class="receipt-logo" src="{{ asset('assets/images/logo.png') }}" alt="Logo" />
        </div>
        <div style="flex:1;">
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

    <table>
        <thead>
            <tr>
                <th style="width:55%;">آئٹم / Item</th>
                <th class="text-right" style="width:25%;">مقدار / Quantity</th>
                <th class="text-right" style="width:20%;">رقم / Amount</th>
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
            <div style="text-align:right;">
                <strong>Total Price / کل قیمت:</strong>
                <div class="grand">Rs. {{ number_format((float)($sale->total_amount ?? 0), 2) }}</div>
            </div>
        </div>

        @if(!empty($sale->bill_image))
            <div style="margin-top:12px;">
                <div style="font-weight:700; font-size:13px; margin-bottom:6px;">Bill Image / بل تصویر</div>
                <img src="{{ asset($sale->bill_image) }}" alt="Bill" style="max-width:100%; border:1px solid #e9ecef; border-radius:8px;" />
            </div>
        @endif

    </div>

    {{-- Footer / Pak Namak Details --}}
    @php
        $appName = config('app.name', 'Pak Namak');
        $pakPhone = config('admin.pak_namak.phone', '');
        $pakWebsite = config('admin.pak_namak.website', '');
    @endphp
    <div style="margin-top:18px; border-top:1px dashed #d9d9d9; padding-top:12px; font-size:12.5px; line-height:1.6;">
        <div style="display:flex; justify-content:space-between; gap:10px; flex-wrap:wrap;">
            <div>
                <strong>{{ $appName }}</strong>
                <div>Phone / فون: {{ $pakPhone ?: '-' }}</div>
                <div>Website / ویب سائٹ: {{ $pakWebsite ?: '-' }}</div>
            </div>
        </div>
    </div>
</div>


<script>
    // auto-open print dialog
    window.onload = function(){
        setTimeout(function(){ window.print(); }, 300);
    };
</script>

</body>
</html>

