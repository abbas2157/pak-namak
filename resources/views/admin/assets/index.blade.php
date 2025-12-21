@extends('admin.layout.app')
@section('title','Assets')

@section('content')
<div class="container-fluid mt-3">

    <button class="btn btn-primary mb-3 float-right mt-3 px-3 mr-3 rounded-pill" id="addAsset">
        <i class="fas fa-plus"></i>Add Asset
    </button>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Asset Name</th>
                <th>Qty</th>
                <th>Purchase Date</th>
                <th>Price</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="assetTable">
            @foreach($assets as $asset)
            <tr id="row{{ $asset->id }}">
                <td>{{ $asset->id }}</td>
                <td>{{ $asset->asset_name }}</td>
                <td>{{ $asset->quantity }}</td>
                <td>{{ $asset->purchase_date }}</td>
                <td>{{ $asset->purchase_price }}</td>
                <td>{{ $asset->description }}</td>
                <td>
                    <button class="btn btn-sm btn-info edit" data-id="{{ $asset->id }}">Edit</button>
                    <button class="btn btn-sm btn-danger delete" data-id="{{ $asset->id }}">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="modal fade" id="assetModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="assetForm">
                @csrf
                <input type="hidden" id="asset_id">

                <div class="modal-body">
                    <label>Asset Name</label>
                    <input type="text" id="asset_name" name="asset_name" class="form-control mb-2">

                    <label>Quantity</label>
                    <input type="number" id="quantity" name="quantity" class="form-control mb-2">

                    <label>Purchase Date</label>
                    <input type="date" id="purchase_date" name="purchase_date" class="form-control mb-2">

                    <label>Purchase Price</label>
                    <input type="number" id="purchase_price" name="purchase_price" class="form-control mb-2">

                    <label>Description</label>
                    <textarea id="description" name="description" class="form-control"></textarea>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function(){
    let base_url = "{{ url('admin/assets') }}";
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('#addAsset').click(function(){
        $('#assetForm')[0].reset();
        $('#asset_id').val('');
        $('#assetModal').modal('show');
    });
    $('#assetForm').submit(function(e){
        e.preventDefault();
        let id = $('#asset_id').val();
        let url = id ? `${base_url}/${id}` : base_url;

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                _method: id ? 'PUT' : 'POST',
                asset_name: $('#asset_name').val(),
                quantity: $('#quantity').val(),
                purchase_date: $('#purchase_date').val(),
                purchase_price: $('#purchase_price').val(),
                description: $('#description').val()
            },
            success: function(res){
                alert('Asset saved successfully!');
                location.reload();
            },
            error: function(xhr){
                console.log('Status:', xhr.status);
                console.log('Response:', xhr.responseText);
                alert('Error occurred — check console');
            }
        });
    });
    $(document).on('click','.edit',function(){
        let id = $(this).data('id');
        $.get(`${base_url}/${id}/edit`,function(data){
            $('#asset_id').val(data.id);
            $('#asset_name').val(data.asset_name);
            $('#quantity').val(data.quantity);
            $('#purchase_date').val(data.purchase_date);
            $('#purchase_price').val(data.purchase_price);
            $('#description').val(data.description);
            $('#assetModal').modal('show');
        });
    });
    $(document).on('click','.delete',function(){
        if(!confirm('Are you sure you want to delete?')) return;
        let id = $(this).data('id');
        $.ajax({
            url: `${base_url}/${id}`,
            type: 'POST',
            data:{_token:"{{ csrf_token() }}",_method:'DELETE'},
            success:function(){
                $('#row'+id).remove();
            }
        });
    });

});
</script>
@endsection
