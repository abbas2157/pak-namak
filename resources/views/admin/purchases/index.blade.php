@extends('admin.layout.app')
@section('title','Salt Purchases')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Purchases List</h1>
                </div>
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Purchases List</li>
                        </ol>
                    </div>
                    <div class="col-sm-6 d-flex justify-content-end">
                        <button class="btn btn-primary mb-3 float-right mt-3 px-3 mr-3 rounded-pill" id="addBtn">
                            <i class="fas fa-plus"></i> Add Purchase
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <table class="table table-bordered table-striped" id="purchasesTable">
                <thead class="thead-dark">
                    <tr>
                        <th>Supplier Name</th>
                        <th>Supplier Phone</th>
                        <th>Qty</th>
                        <th>Rate/KG</th>
                        <th>Total</th>
                        <th>Grand Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if($purchases->isEmpty())
                        <tr>
                            <td colspan="10" class="text-center">No Purchases found.</td>
                        </tr>
                    @endif
                    @foreach($purchases as $row)
                    <tr>
                        <td>{{ $row->vendor->name ?? '' }}</td>
                        <td>{{ $row->vendor->phone ?? '' }}</td>
                        <td>{{ $row->salt_quantity ?? '' }}</td>
                        <td>{{ $row->rate_per_kg ?? '' }}</td>
                        <td>{{ $row->total_cost ?? ''}}</td>
                        <td>{{ $row->grand_total ?? '' }}</td>
                        <td>
                            <button class="btn btn-sm btn-info editBtn" data-id="{{ $row->id }}">Edit</button>
                            <button class="btn btn-sm btn-danger deleteBtn" data-id="{{ $row->id }}">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
<div class="modal fade" id="purchaseModal">
    <div class="modal-dialog modal-lg">
        <form id="purchaseForm">
            @csrf
            <input type="hidden" id="id" name="id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Purchase</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-6 mb-2">
                        <label>Select Supplier</label>
                        <select name="vendor_id" id="vendor_id" class="form-control" required>
                            <option disabled selected>Select Supplier</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id ?? '' }}">{{ $vendor->name ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label>Grand Total</label>
                        <input type="number" name="grand_total" id="grand_total" class="form-control">
                    </div>
                     <div class="col-md-6 mb-2">
                        <label>Salt Quantity (Ton)</label>
                        <input type="number" name="salt_quantity" id="salt_quantity" class="form-control">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label>Salt Quantity (KG)</label>
                        <input type="number" name="salt_quantity_kg" id="salt_quantity_kg" class="form-control">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label>Total Cost</label>
                        <input type="number" name="total_cost" id="total_cost" class="form-control">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label>Rate per KG</label>
                        <input type="number" name="rate_per_kg" id="rate_per_kg" class="form-control">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label>Transport Cost</label>
                        <input type="number" name="transport_cost" id="transport_cost" class="form-control">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label>Loading/Unloading</label>
                        <input type="number" name="loading_unloading_cost" id="loading_unloading_cost" class="form-control">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label>Remarks</label>
                        <input type="text" name="remarks" id="remarks" class="form-control">
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
        $('#purchaseForm')[0].reset();
        $('#id').val('');
        $('#purchaseModal').modal('show');
    });
    $(document).on('submit', '#purchaseForm', function (e) {
        e.preventDefault();
        let id = $('#id').val();
        let url = id ? APP_URL + "/admin/purchases/" + id : "{{ route('admin.purchases.store') }}";
        let method = id ? "PUT" : "POST";
        $.ajax({
            url: url,
            type: "POST",
            data: $(this).serialize() + (method === "PUT" ? '&_method=PUT' : ''),
            success: function () {
                window.location.href = window.location.href;
            },
            error: function(xhr){
                console.log(xhr.responseText);
            }
        });
    });

    $('.editBtn').click(function () {
        let id = $(this).data('id');
        $.ajax({
            url: APP_URL + "/admin/purchases/" + id + "/edit",
            type: "GET",
            dataType: 'json',
            success: function (response) {
                $('#id').val(response.id);
                $('#vendor_id').val(response.vendor_id);
                $('#salt_quantity').val(response.salt_quantity);
                $('#salt_quantity_kg').val(response.salt_quantity_kg);
                $('#rate_per_kg').val(response.rate_per_kg);
                $('#total_cost').val(response.total_cost);
                $('#transport_cost').val(response.transport_cost);
                $('#loading_unloading_cost').val(response.loading_unloading_cost);
                $('#grand_total').val(response.grand_total);
                $('#remarks').val(response.remarks);
                $('#purchaseModal').modal('show');
            },
            error: function(xhr){
                console.log(xhr.responseText);
            }
        });
    });

    $('.deleteBtn').click(function () {
        if (!confirm('Delete this record?')) return;
        let id = $(this).data('id');
        let url = APP_URL + "/admin/purchases/" + id;
        $.ajax({
            url: url,
            type: "POST",
            data: {
                _method: "DELETE",
                _token: "{{ csrf_token() }}"
            },
            success: function (response) {
                console.log(response);
                window.location.href = window.location.href;
            },
            error: function(xhr){
                console.log(xhr.responseText);
            }
        });
    });
});

$(function () {
    $('#purchasesTable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true
    });

});
</script>
@endsection
