@extends('admin.layout.app')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Title List</h1>
                </div>
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active">Title List</li>
                        </ol>
                    </div>
                    <div class="col-sm-6 d-flex justify-content-end">
                        <a href="{{ route('admin.types.create') }}" class="btn btn-primary shadow rounded-pill">
                            <i class="fas fa-plus"></i> Add Title
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
                                        <th>Title</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($types as $type)
                                        <tr>
                                            <td>{{ $type->id }}</td>
                                            <td>{{ $type->title }}</td>
                                            <td>{{ $type->created_at }}</td>
                                            <td>
                                                <a href="{{ route('admin.types.edit', $type->id) }}"
                                                    class="btn btn-primary btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.types.destroy', $type->id) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Are you sure you want to delete ID: {{ $type->id }} ?')"
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
                                    {!! $types->links('pagination::bootstrap-5') !!}
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection