@extends('admin.layout.app')
@section('title', 'Shop Sales Details')

@section('content')

<div class="row mb-3">
    <div class="col-md-3"><strong>Shop:</strong> {{ $shop->name }}</div>
    <div class="col-md-3"><strong>Phone:</strong> {{ $shop->phone_number }}</div>
    <div class="col-md-3"><strong>Address:</strong> {{ $shop->address }}</div>
</div>

<hr>

@foreach($shop->sales as $sale)

<div class="card mb-4">
    <div class="card-header bg-light">
        <strong>Date:</strong> {{ $sale->sale_date }}
        |
        <strong>Total:</strong> Rs. {{ number_format($sale->total_amount, 2) }}
    </div>

    <div class="card-body">

        {{-- DALLA --}}
        @if($sale->dalla)
        <h5 class="text-primary">Dalla</h5>
        <table class="table table-sm table-bordered">
            <tr>
                <td>{{ $sale->dalla->quantity_mann }} Mann</td>
                <td>{{ $sale->dalla->quantity_kg }} KG</td>
                <td>Rs. {{ $sale->dalla->sub_total }}</td>
            </tr>
        </table>
        @endif

        {{-- THAILA --}}
        @if($sale->thailas->count())
        <h5 class="text-success">Thaila</h5>
        <table class="table table-sm table-bordered">
            @foreach($sale->thailas as $t)
            <tr>
                <td>{{ $t->bag_size_kg }} KG</td>
                <td>{{ $t->quantity }}</td>
                <td>Rs. {{ $t->sub_total }}</td>
            </tr>
            @endforeach
        </table>
        @endif

        {{-- PACKAGES --}}
        @if($sale->packages->count())
        <h5 class="text-warning">Packages</h5>
        <table class="table table-sm table-bordered">
            @foreach($sale->packages as $p)
            <tr>
                <td>{{ $p->packet_gram }} Gram</td>
                <td>{{ $p->bundle_quantity }}</td>
                <td>Rs. {{ $p->sub_total }}</td>
            </tr>
            @endforeach
        </table>
        @endif

    </div>
</div>

@endforeach

@endsection
