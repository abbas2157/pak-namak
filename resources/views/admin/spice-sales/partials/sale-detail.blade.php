<div class="row mb-3">
    <div class="col-md-4"><strong>Shop / دکان:</strong> {{ $sale->shop->name ?? '-' }}</div>
    <div class="col-md-4"><strong>Phone / فون:</strong> {{ $sale->shop->phone_number ?? '-' }}</div>
    <div class="col-md-4"><strong>Date / تاریخ:</strong> {{ $sale->sale_date ?? '-' }}</div>
</div>

@foreach($sale->items->groupBy('spice_type_id') as $items)
<h5 class="text-warning">{{ $items->first()->spiceType->title ?? 'Spice' }}</h5>
<table class="table table-sm table-bordered">
    <thead class="thead-light">
        <tr>
            <th>Size (گرام)</th>
            <th>Quantity Sold (پیکٹ)</th>
            <th>کل وزن(KG)</th>
            <th>Rate (فی پیکٹ قیمت)</th>
            <th>Subtotal / ذیلی کل</th>
        </tr>
    </thead>
    @foreach($items as $p)
    <tr>
        <td>{{ $p->packet_gram ?? '' }} گرام</td>
        <td>{{ $p->quantity ?? '' }} پیکٹ</td>
        <td>{{ $p->total_kg ?? '' }} KG</td>
        <td>Rs. {{ $p->price_per_unit ?? '' }}</td>
        <td>Rs. {{ $p->sub_total ?? '' }}</td>
    </tr>
    @endforeach
</table>
@endforeach

<div class="mb-3 d-flex justify-content-between align-items-center gap-2">
    <div><strong>Total Price / کل قیمت:</strong> Rs. {{ number_format($sale->total_amount ?? 0, 2) }}</div>
</div>

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
