@extends('admin.layout.app')
@section('title', content: 'Packages')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Packages List</h1>
                </div>
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active">Packages List</li>
                        </ol>
                    </div>
                    <div class="col-sm-6 d-flex justify-content-end">
                        <a href="{{ route('admin.package.create') }}" class="btn btn-primary shadow rounded-pill">
                            <i class="fas fa-plus"></i> Add package
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        @if(Session::has('success'))
                            <div class="alert alert-success">
                                {{ Session::get('success') }}
                            </div>
                        @endif
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Namak Name</th>
                                        <th>Packages</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($packages as $package)
                                        <tr>
                                            <td>{{ $package->id }}</td>
                                            <td>{{ $package->title }}</td>
                                            <td>{{ $package->weight }}</td>
                                            <td>{{ $package->created_at }}</td>
                                            <td>
                                                <a href="{{ route('admin.package.edit', $package->id) }}"
                                                    class="btn btn-primary btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.package.destroy', $package->id) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Are you sure you want to delete ID:?')"
                                                        title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>

                                                </form>
                                            </td>
                                        </tr>

                                    @endforeach

                                </tbody>
                            </table>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                </div>
                                <div>
                                    {!! $packages->links('pagination::bootstrap-5') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
