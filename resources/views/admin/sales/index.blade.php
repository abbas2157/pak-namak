@extends('admin.layout.app')
@section('title', 'Sales')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Sales List</h1>
                </div>
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Sales List</li>
                        </ol>
                    </div>
                    <div class="col-sm-6 d-flex justify-content-end">
                        <a href="{{ route('admin.sales.create') }}" class="btn btn-primary shadow rounded-pill">
                            <i class="fas fa-plus"></i> Add Sales
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <table class="table table-bordered table-striped" id="salesTable">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Shop</th>
                        <th>Salt Type</th>
                        <th>Size</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if($sales->isEmpty())
                        <tr>
                            <td colspan="8" class="text-center">No sales found.</td>
                        </tr>
                    @endif
                    @foreach ($sales as $sale)
                    <tr id="row_{{ $sale->id }}">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $sale->shop->name }}</td>
                        <td>{{ $sale->salt_type->title }}</td>
                        <td>{{ $sale->product_size }}</td>
                        <td>{{ $sale->quantity_sold }}</td>
                        <td>{{ $sale->total_sales_amount }}</td>
                        <td>{{ $sale->date }}</td>
                        <td>
                            <button class="btn btn-sm btn-info editBtn" data-id="{{ $sale->id }}">Edit</button>
                            <button class="btn btn-sm btn-danger deleteBtn" data-id="{{ $sale->id }}">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </section>
@endsection

@section('scripts')
<script>
$(function () {
    $('.deleteBtn').click(function () {
        let id = $(this).data('id');

        Swal.fire({
            title: "Are you sure?",
            icon: "warning",
            showCancelButton: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(APP_URL + `/admin/sales/${id}`, {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                }, function () {
                    $('#row_' + id).remove();
                    Swal.fire("Deleted!", "", "success");
                });
            }
        });
    });

});
</script>
@endsection
