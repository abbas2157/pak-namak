@extends('admin.layout.app')
@section('title', 'Sales Report')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1>Sales Report</h1>
            </div>
            <div class="col-sm-6 d-flex justify-content-end gap-2">
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print"></i> Print / PDF
                </button>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        <div class="row mb-3 no-print">
            <div class="col-md-3">
                <label>From (Y-m-d)</label>
                <input type="date" class="form-control" id="from" value="{{ $from }}" onchange="applyFilter()" />
            </div>
            <div class="col-md-3">
                <label>To (Y-m-d)</label>
                <input type="date" class="form-control" id="to" value="{{ $to }}" onchange="applyFilter()" />
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <div class="small text-muted">Leave empty to show all sales.</div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fas fa-pepper-hot"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Dalla</span>
                        <span class="info-box-number">Count: {{ $dallaStats->count ?? 0 }} | Total: PKR. {{ number_format((float)($dallaStats->total ?? 0),2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-pepper-hot"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Thailas</span>
                        <span class="info-box-number">Count: {{ $thailaStats->count ?? 0 }} | Total: PKR. {{ number_format((float)($thailaStats->total ?? 0),2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-box"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Packages</span>
                        <span class="info-box-number">Count: {{ $packageStats->count ?? 0 }} | Total: PKR. {{ number_format((float)($packageStats->total ?? 0),2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Sales by Shop (Count + Total)</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-striped" id="salesByShopTable">
                    <thead class="thead-dark">
                        <tr>
                            <th>Shop ID</th>
                            <th>Count</th>
                            <th>Total Sales (PKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salesByShop as $row)
                            <tr>
                                <td>{{ $row->shop_id }}</td>
                                <td>{{ $row->count }}</td>
                                <td>{{ number_format((float)$row->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted">No sales found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">All Sales</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-striped" id="salesReportTable">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Shop ID</th>
                            <th>Date</th>
                            <th>Total Sales (PKR)</th>
                            <th>Receipt / Print</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td>{{ $sale->id }}</td>
                                <td>{{ $sale->shop_id }}</td>
                                <td>{{ $sale->sale_date }}</td>
                                <td>{{ number_format((float)$sale->total_amount,2) }}</td>
                                <td>
                                    <a href="{{ route('admin.sales.receipt', ['id' => $sale->id]) }}" class="btn btn-sm btn-primary" target="_blank">Print</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">No sales found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>

@endsection

@section('scripts')
<script>
    function applyFilter() {
        const from = document.getElementById('from').value;
        const to = document.getElementById('to').value;
        const params = new URLSearchParams();
        if (from) params.set('from', from);
        if (to) params.set('to', to);
        const query = params.toString();
        window.location.href = '{{ url('admin/sales-report') }}' + (query ? ('?' + query) : '');
    }

    $(function () {
        if ($('#salesReportTable').length) {
            $('#salesReportTable').DataTable({
                paging: true,
                lengthChange: false,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true,
                buttons: ['csv', 'excel', 'pdf']
            });
        }

        if ($('#salesByShopTable').length) {
            $('#salesByShopTable').DataTable({
                paging: true,
                lengthChange: false,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true
            });
        }
    });
</script>
@endsection


