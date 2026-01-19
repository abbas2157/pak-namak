@extends('admin.layout.app')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Suppliers List</h1>
                </div>
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Vendors/Suppliers List</li>
                        </ol>
                    </div>
                    <div class="col-sm-6 d-flex justify-content-end">
                        <a href="{{ route('admin.vendors.create') }}" class="btn btn-primary shadow rounded-pill">
                            <i class="fas fa-plus"></i> Add Vendor/Supplier
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    @if(Session::has('success'))
                        <div class="alert alert-success">
                            {{ Session::get('success') }}
                        </div>
                    @endif
                    <table id="vendorsTable" class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>Name</th>
                                <th>Shop</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($vendors->isEmpty())
                                <tr>
                                    <td colspan="8" class="text-center">No vendors found.</td>
                                </tr>
                            @endif
                            @foreach ($vendors as $vendor)
                                <tr>
                                    <td>{{ $vendor->name }}</td>
                                    <td>{{ $vendor->shop }}</td>
                                    <td>{{ $vendor->phone }}</td>
                                    <td>{{ $vendor->address }}</td>
                                    <td>
                                        <a href="{{ route('admin.vendors.edit', $vendor->id) }}"
                                            class="btn btn-primary btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.vendors.destroy', $vendor->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure want to delete?')" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
<script>
    $(function () {
        $('#vendorsTable').DataTable({
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

