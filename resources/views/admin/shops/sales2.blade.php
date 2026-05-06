@extends('admin.layout.app')
@section('title', 'Shop Sales')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Shop Sales</h1>
                </div>
                <div class="col-sm-6 d-flex justify-content-end gap-2">
                    @if(isset($shop) && $shop)
                        <div class="btn btn-sm btn-primary" style="pointer-events:none; cursor:default; border-radius:999px; padding:8px 14px;">
                            <i class="fas fa-store"></i> {{ $shop->name }}
                        </div>
                    @endif

                    <button class="btn btn-primary no-print" onclick="window.print()">
                        <i class="fas fa-print"></i> Print / PDF
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            @if(isset($shop) && $shop)
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Shop / دکان:</strong> {{ $shop->name ?? '-' }}</div>
                    <div class="col-md-4"><strong>Phone / فون:</strong> {{ $shop->phone_number ?? '-' }}</div>
                    <div class="col-md-4"><strong>Address / پتہ:</strong> {{ $shop->address ?? '-' }}</div>
                </div>

                {{-- Namak totals for this shop --}}
                <div class="row mb-3">
                    <div class="col-md-4 col-sm-6 col-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-primary"><i class="fas fa-pepper-hot"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Dalla</span>
                                <span class="info-box-number">Count: {{ $dallaStats->count ?? 0 }} | Total: PKR. {{ number_format((float)($dallaStats->total ?? 0), 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-pepper-hot"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Thailas</span>
                                <span class="info-box-number">Count: {{ $thailaStats->count ?? 0 }} | Total: PKR. {{ number_format((float)($thailaStats->total ?? 0), 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-box"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Packages</span>
                                <span class="info-box-number">Count: {{ $packageStats->count ?? 0 }} | Total: PKR. {{ number_format((float)($packageStats->total ?? 0), 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <table class="table table-bordered table-striped" id="shopSalesTable">

                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Date / تاریخ</th>
                        <th>Total / کل رقم</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($sales as $sale)
                    <tr id="row_{{ $sale->id }}">
                        <td>{{ $sale->id }}</td>
                        <td>{{ $sale->sale_date }}</td>
                        <td>Rs. {{ number_format($sale->total_amount, 2) }}</td>
                        <td>
                            <a href="{{ route('admin.sales.receipt', ['id' => $sale->id]) }}" class="btn btn-sm btn-primary" target="_blank">Print</a>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No sales found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

@section('scripts')
<script>
    $(function () {
        $('#shopSalesTable').DataTable({
            paging: true,
            lengthChange: false,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true,
        });
    });
</script>
@endsection

