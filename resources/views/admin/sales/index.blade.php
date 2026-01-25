@extends('admin.layout.app')
@section('title', 'Sales')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Sales List</h1>
                </div>
                    <div class="col-sm-2 ms-auto">
                        <div class="form-group mb-0">
                            <label class="small">Select Shop</label>
                            <select class="select2   form-control form-control-sm" id="shopFilter">
                                <option value="">All Shops</option>
                                @foreach($shops as $shop)
                                    <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
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
    </section>
    <section class="content">
        <div class="container-fluid">
            <table class="table table-bordered table-striped" id="salesTable">
                <thead class="thead-dark">
                    <tr>
                        <th>Shop</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="salesBody">
                    @if($sales->isEmpty())
                        <tr>
                            <td colspan="8" class="text-center">No sales found.</td>
                        </tr>
                    @endif
                    @foreach ($sales as $sale)
                    <tr id="row_{{ $sale->id }}">
                        <td>{{ $sale->shop->name ?? '' }}</td>
                        <td>{{ $sale->total_amount ?? '' }}</td>
                        <td>{{ $sale->sale_date ?? '' }}</td>
                        <td>
                            <button class="btn btn-sm btn-primary viewBtn" data-id="{{ $sale->id }}">View</button>
                            <button class="btn btn-sm btn-info editBtn" data-id="{{ $sale->id }}">Edit</button>
                            <button class="btn btn-sm btn-danger deleteBtn" data-id="{{ $sale->id }}">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    <div class="modal fade" id="saleDetailModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Sale Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="saleDetailBody">
                    <div class="text-center p-4">
                        <span class="spinner-border"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    $(document).on('click', '.viewBtn', function () {
        let saleId = $(this).data('id');

        $('#saleDetailModal').modal('show');
        $('#saleDetailBody').html('<div class="text-center p-4"><span class="spinner-border"></span></div>');

        $.get("{{ url('/admin/sales') }}/" + saleId, function (response) {
            $('#saleDetailBody').html(response);
        });
    });
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
<<<<<<< HEAD

    $(document).ready(function () {
        $('#shopFilter').select2({
            placeholder: "Select Shop",
            allowClear: true
        });

        let table = $('#salesTable').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            responsive: true
        });

        $('#shopFilter').on('change', function () {
            let shopName = $(this).val() ? $(this).find('option:selected').text() : '';
            table.column(0).search(shopName).draw();
        });
    });
=======
});
>>>>>>> 01f0bc2180f4f7f3e8747ad56e87a7ac4bba9628
</script>
<script>
  $(function () {
    $('#salesTable').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "buttons": ["csv", "excel", "pdf"]
    }).buttons().container().appendTo('#salesTable_wrapper .col-md-6:eq(0)');
  });
</script>
@endsection
