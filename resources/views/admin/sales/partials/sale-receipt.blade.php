@php
    $shop = $sale->shop ?? null;
@endphp

<style>
    @media print {
        .no-print { display: none !important; }
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }

    .receipt-wrap {
        font-family: Arial, Helvetica, sans-serif;
        color: #111;
    }

    .receipt-header {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 14px;
    }

    .receipt-logo {
        width: 64px;
        height: 64px;
        object-fit: contain;
        background: #fff;
    }

    .receipt-title {
        font-size: 18px;
        font-weight: 700;
        margin: 0;
    }

    .receipt-meta {
        font-size: 13px;
        line-height: 1.5;
    }

    .receipt-table {
        width: 100%;
        border-collapse: collapse;
    }

    .receipt-table th, .receipt-table td {
        border: 1px solid #e9ecef;
        padding: 8px;
        font-size: 13px;
    }

    .receipt-total {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 12px 16px;
        margin-top: 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 14px;
    }

    .text-right { text-align: right; }
</style>

<div class="receipt-wrap" id="saleReceipt">
    <div class="receipt-header">
        <div style="display:flex; gap:14px; align-items:flex-start;">
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
    </div>

    <div>
        <table class="receipt-table">
            <thead>
                <tr>
                    <th style="width: 55%;">Item / آئٹم</th>
                    <th class="text-right" style="width: 25%;">Quantity / مقدار</th>
                    <th class="text-right" style="width: 20%;">Amount / رقم</th>
                </tr>
            </thead>
            <tbody>
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

                @forelse($lines as $line)
                    <tr>
                        <td>{{ $line['name'] }}</td>
                        <td class="text-right">{{ $line['qty'] }}</td>
                        <td class="text-right">Rs. {{ number_format((float)$line['amount'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-right">No items / کوئی آئٹم نہیں</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="receipt-total">
            <div>
                <div><strong>Shop Address / پتہ:</strong> {{ $shop?->address ?? '-' }}</div>
            </div>
            <div style="text-align:right;">
                <div><strong>Total Price / کل قیمت:</strong></div>
                <div style="font-size:18px; font-weight:800;">Rs. {{ number_format((float)($sale->total_amount ?? 0), 2) }}</div>
            </div>
        </div>
    </div>
</div>

<script>
    // kept for backwards compatibility
    window.printSaleReceipt = function () {
        window.print();
    };
</script>

{{-- Separate “PDF/Receipt page” (printed) --}}
<div class="receipt-page" style="display:none;">
    {{-- This block exists so you can easily switch to a dedicated print layout later --}}
</div>


