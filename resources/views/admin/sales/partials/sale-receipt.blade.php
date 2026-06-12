@php
    $shop = $sale->shop ?? null;
@endphp

<div class="receipt-wrap" id="saleReceipt">
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

    <div>
        <table class="receipt-table">
            <thead>
                <tr>
                    <th class="col-item">Item / آئٹم</th>
                    <th class="text-right col-qty">Quantity / مقدار</th>
                    <th class="text-right col-amt">Amount / رقم</th>
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
            <div class="text-right">
                <div><strong>Total Price / کل قیمت:</strong></div>
                <div class="receipt-grand">Rs. {{ number_format((float)($sale->total_amount ?? 0), 2) }}</div>
            </div>
        </div>
    </div>
</div>

<script>
    window.printSaleReceipt = function () {
        window.print();
    };
</script>
