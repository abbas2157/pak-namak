@extends('admin.layout.app')
@section('title', 'Sales')

@section('content')
<div class="container-fluid">

    <div class="text-right">
        <button class="btn btn-primary mb-3 float-right mt-3 px-3 mr-3 rounded-pill" id="addBtn">
            <i class="fas fa-plus"></i> Add Sale
        </button>
    </div>

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
            @foreach ($sales as $sale)
            <tr id="row_{{ $sale->id }}">
                <td>{{ $loop->iteration }}</td>
                <td>{{ $sale->shop->name }}</td>
                <td>{{ $sale->saltType->title }}</td>
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

<!-- Modal -->
<div class="modal fade" id="saleModal">
    <div class="modal-dialog modal-lg">
        <form id="saleForm">
            @csrf
            <input type="hidden" id="id" name="id">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sale</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row">

                    <!-- Shop -->
                    <div class="col-md-6 mb-2">
                        <label>Shop</label>
                        <select name="shop_id" id="shop_id" class="form-control" required>
                            <option value="">Select Shop</option>
                            @foreach($shops as $shop)
                                <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Salt Type -->
                    <div class="col-md-6 mb-2">
                        <label>Salt Type</label>
                        <select name="salt_type_id" id="salt_type_id" class="form-control" required>
                            <option value="">Select Salt</option>
                            @foreach($types as $salt)
                                <option value="{{ $salt->id }}">{{ $salt->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-2">
                        <label>Product Size</label>
                        <input type="text" name="product_size" id="product_size" class="form-control">
                    </div>

                    <div class="col-md-6 mb-2">
                        <label>Quantity Sold</label>
                        <input type="number" name="quantity_sold" id="quantity_sold" class="form-control">
                    </div>

                    <div class="col-md-6 mb-2">
                        <label>Rate Per Pack</label>
                        <input type="number" step="0.01" name="rate_per_pack" id="rate_per_pack" class="form-control">
                    </div>

                    <div class="col-md-6 mb-2">
                        <label>Total Amount</label>
                        <input type="number" step="0.01" name="total_sales_amount" id="total_sales_amount" class="form-control">
                    </div>

                    <div class="col-md-6 mb-2">
                        <label>Date</label>
                        <input type="date" name="date" id="date" class="form-control">
                    </div>

                    <div class="col-md-12 mb-2">
                        <label>Remarks</label>
                        <textarea name="remarks" id="remarks" class="form-control"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Save</button>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(function () {

    $('#addBtn').click(function () {
        $('#saleForm')[0].reset();
        $('#id').val('');
        $('#saleModal').modal('show');
    });

    $('#saleForm').submit(function (e) {
        e.preventDefault();

        let id = $('#id').val();
        let url = id ? `/admin/sales/${id}` : `{{ route('admin.sales.store') }}`;

        $.ajax({
            url: url,
            type: "POST",
            data: $(this).serialize() + (id ? '&_method=PUT' : ''),
            success: function () {
                location.reload();
            },
            error: function () {
                toastr.error('Something went wrong');
            }
        });
    });

    $('.editBtn').click(function () {
        let id = $(this).data('id');

        $.get(`/admin/sales/${id}/edit`, function (sale) {
            $('#id').val(sale.id);
            $('#shop_id').val(sale.shop_id);
            $('#salt_type_id').val(sale.salt_type_id);
            $('#product_size').val(sale.product_size);
            $('#quantity_sold').val(sale.quantity_sold);
            $('#rate_per_pack').val(sale.rate_per_pack);
            $('#total_sales_amount').val(sale.total_sales_amount);
            $('#date').val(sale.date);
            $('#remarks').val(sale.remarks);

            $('#saleModal').modal('show');
        });
    });

    $('.deleteBtn').click(function () {
        let id = $(this).data('id');

        Swal.fire({
            title: "Are you sure?",
            icon: "warning",
            showCancelButton: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(`/admin/sales/${id}`, {
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
