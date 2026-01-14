@extends('admin.layout.app')
@section('title', 'Shops')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Shops List</h1>
                </div>
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Shops List</li>
                        </ol>
                    </div>
                    <div class="col-sm-6 d-flex justify-content-end">
                        <button class="btn btn-primary mb-3 float-right mt-3 px-3 mr-3 rounded-pill" id="addBtn">
                            <i class="fas fa-plus"></i> Add Shop
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <table class="table table-bordered table-striped" id="shopTable">
                <thead class="thead-dark">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Create Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($shops as $shop)
                        <tr id="row_{{ $shop->id }}">
                            <td>{{ $shop->name ?? '' }}</td>
                            <td>{{ $shop->email ?? '' }}</td>
                            <td>{{ $shop->phone_number ?? '' }}</td>
                            <td>{{ $shop->address ?? '' }}</td>
                            <td>{{ $shop->created_at ? $shop->created_at->format('d-m-Y h:i A') : '' }}</td>
                            <td>
                                <button class="btn btn-sm btn-info editBtn" data-id="{{ $shop->id }}">Edit</button>
                                <button class="btn btn-sm btn-danger deleteBtn" data-id="{{ $shop->id }}">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    <div class="modal fade" id="shopModal">
        <div class="modal-dialog modal-lg">
            <form id="shopForm">
                @csrf
                <input type="hidden" id="id" name="id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Shop</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body row">
                        <div class="col-md-6 mb-2">
                            <label>Name</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Enter Name" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Email</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="Enter Email" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Phone Number</label>
                            <input type="text" name="phone_number" id="phone_number" class="form-control" placeholder="Enter Phone Number">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Address</label>
                            <input type="text" name="address" id="address" class="form-control" placeholder="Enter Address">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-success" type="submit">Create/Update Shop</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {

            $('#addBtn').click(function() {
                $('#shopForm')[0].reset();
                $('#id').val('');
                $('#shopModal').modal('show');
            });
            $(document).on('submit', '#shopForm', function(e) {
                e.preventDefault();
                let id = $('#id').val();
                let url = id ? APP_URL + "/admin/shops/" + id : "{{ route('admin.shops.store') }}";
                let method = id ? "PUT" : "POST";
                $.ajax({
                    url: url,
                    type: "POST",
                    data: $(this).serialize() + (method === "PUT" ? '&_method=PUT' : ''),
                    success: function(shop) {
                        let row = `<tr id="row_${shop.id}">
                                <td>${shop.name}</td>
                                <td>${shop.email}</td>
                                <td>${shop.phone_number}</td>
                                <td>${shop.address}</td>
                                <td>${shop.created_at}</td>
                                <td>
                                    <button class="btn btn-sm btn-info editBtn" data-id="${shop.id}">Edit</button>
                                    <button class="btn btn-sm btn-danger deleteBtn" data-id="${shop.id}">Delete</button>
                                </td>
                            </tr>`;

                        if (id) {
                            $('#row_' + id).replaceWith(row);
                        } else {
                            $('#shopTable tbody').append(row);
                        }
                        toastr.success('Shop Saved successfully!');
                        $('#shopModal').modal('hide');
                    },
                    error: function(xhr) {
                        toastr.error('Something went wrong');
                        console.log(xhr.responseText);
                    }
                });
            });

            $(document).on('click', '.editBtn', function() {
                let id = $(this).data('id');
                $.ajax({
                    url: APP_URL + "/admin/shops/" + id + "/edit",
                    type: "GET",
                    dataType: 'json',
                    success: function(shop) {
                        $('#id').val(shop.id);
                        $('#name').val(shop.name);
                        $('#email').val(shop.email);
                        $('#phone_number').val(shop.phone_number);
                        $('#address').val(shop.address);
                        $('#shopModal').modal('show');
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });

            $(document).on('click', '.deleteBtn', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: APP_URL + "/admin/shops/" + id,
                            type: "POST",
                            data: {
                                _method: "DELETE",
                                _token: "{{ csrf_token() }}"
                            },
                            success: function() {
                                $('#row_' + id).remove();

                                Swal.fire({
                                    title: "Deleted!",
                                    text: "Your shop has been deleted.",
                                    icon: "success",
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: "Error!",
                                    text: "Something went wrong.",
                                    icon: "error"
                                });
                                console.log(xhr.responseText);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
