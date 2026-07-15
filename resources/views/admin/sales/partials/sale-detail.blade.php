<div class="row mb-3">
    <div class="col-md-4"><strong>Shop / دکان:</strong> {{ $sale->shop->name ?? '-' }}</div>
    <div class="col-md-4"><strong>Phone / فون:</strong> {{ $sale->shop->phone_number ?? '-' }}</div>
    <div class="col-md-4"><strong>Date / تاریخ:</strong> {{ $sale->sale_date ?? '-' }}</div>
</div>
@if($sale->dalla)
<h5 class="text-primary">Dalla / ڈلہ</h5>
<table class="table table-sm table-bordered">
    <thead class="thead-light">
        <tr>
            <th>Quantity Sold (وزن: من)</th>
            <th>کل وزن(KG)</th>
            <th>Rate (فی من قیمت)</th>
            <th>Rate (فی کلو قیمت)</th>
            <th>Subtotal / ذیلی کل</th>
        </tr>
    </thead>
    <tr>
        <td>{{ $sale->dalla->quantity_mann ?? '' }} Mann</td>
        <td>{{ $sale->dalla->quantity_kg ?? ''}} KG</td>
        <td>Rs. {{ $sale->dalla->price_per_mann ?? '' }}</td>
        <td>Rs. {{ $sale->dalla->price_per_kg }}</td>
        <td>Rs. {{ $sale->dalla->sub_total }}</td>
    </tr>
</table>
@endif
@if($sale->thailas->count())
<h5 class="text-success">Thaila / تھیلا</h5>
<table class="table table-sm table-bordered">
    <thead class="thead-light">
        <tr>
            <th>Size (Kg) (کلو)</th>
            <th>Quantity Sold (تھیلا)</th>
            <th>کل وزن(KG)</th>
            <th>Rate (فی کلو قیمت)</th>
            <th>Rate (فی تھیلا قیمت)</th>
            <th>Subtotal / ذیلی کل</th>
        </tr>
    </thead>
    @foreach($sale->thailas as $t)
    <tr>
        <td>{{ $t->bag_size_kg ?? '' }} KG</td>
        <td>{{ $t->quantity ?? ''}} (تھیلا)</td>
        <td>{{ $t->total_kg ?? '' }} KG</td>
        <td>Rs. {{ $t->price_per_kg ?? '' }}</td>
        <td>Rs. {{ $t->price_per_bag ?? '' }}</td>
        <td>Rs. {{ $t->sub_total ?? '' }}</td>
    </tr>
    @endforeach
</table>
@endif

{{-- PACKAGES --}}
@if($sale->packages->count())
<h5 class="text-warning">Packages / پیکٹ</h5>
<table class="table table-sm table-bordered">
    <thead class="thead-light">
        <tr>
            <th>Size (گرام)</th>
            <th>Quantity Sold (بنڈل)</th>
            <th>Bundle Type (بنڈل)</th>
            <th>کل وزن(KG)</th>
            <th>Rate (فی تھیلا قیمت)</th>
            <th>Subtotal / ذیلی کل</th>
        </tr>
    </thead>
    @foreach($sale->packages as $p)
    <tr>
        <td>{{ $p->packet_gram ?? '' }} گرام</td>
        <td>{{ $p->bundle_quantity ?? '' }} بنڈل</td>
        <td>{{ $p->bundle_size }} Packets</td>
        <td>{{ $p->total_kg ?? '' }} KG</td>
        <td>Rs. {{ $p->price_per_bundle ?? '' }}</td>
        <td>Rs. {{ $p->sub_total ?? '' }}</td>
    </tr>
    @endforeach
</table>


@endif

<div class="mb-3 d-flex justify-content-between align-items-center gap-2">
    <div><strong>Total Price / کل قیمت:</strong> Rs. {{ number_format($sale->total_amount ?? 0, 2) }}</div>
    <button class="btn btn-sm btn-outline-secondary no-print" onclick="window.open('{{ route('admin.sales.receipt', ['id' => $sale->id]) }}', '_blank')">
        <i class="fas fa-print"></i> Print / پرنٹ
    </button>

</div>

{{-- PAYMENTS --}}
<h5 class="text-c-teal">Payments / ادائیگیاں</h5>
<div class="row mb-2">
    <div class="col-4"><strong>Total:</strong> Rs. {{ number_format($sale->total_amount, 0) }}</div>
    <div class="col-4"><strong>Received:</strong> <span class="text-c-teal">Rs. {{ number_format($sale->received_amount, 0) }}</span></div>
    <div class="col-4"><strong>Pending:</strong> <span class="text-c-red font-weight-bold">Rs. {{ number_format($sale->pending_amount, 0) }}</span></div>
</div>
<button type="button" class="btn btn-sm btn-primary btn-pn mb-3 recordPaymentBtn"
        data-id="{{ $sale->id }}"
        data-shop="{{ $sale->shop->name ?? '' }}"
        data-pending="{{ $sale->pending_amount }}">
    <i class="fas fa-hand-holding-dollar mr-1"></i> Record Payment
</button>

@if($sale->payments->count())
<table class="table table-sm table-bordered">
    <thead class="thead-light">
        <tr>
            <th>Date</th>
            <th>Amount</th>
            <th>Method</th>
            <th>Note</th>
            <th class="no-print"></th>
        </tr>
    </thead>
    @foreach($sale->payments as $p)
    <tr>
        <td>{{ $p->payment_date->format('d M Y') }}</td>
        <td>Rs. {{ number_format($p->amount, 2) }}</td>
        <td>{{ $p->payment_method }}</td>
        <td>{{ $p->note }}</td>
        <td class="no-print">
            <button type="button" class="btn btn-sm btn-outline-danger deletePaymentBtn"
                    data-sale-id="{{ $sale->id }}" data-payment-id="{{ $p->id }}" title="Delete">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
    @endforeach
</table>
@else
<p class="text-muted">No payments recorded yet.</p>
@endif