@extends('admin.layout.app')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Production List</h1>
                </div>
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Production List</li>
                        </ol>
                    </div>
                    <div class="col-sm-6 d-flex justify-content-end">
                        <a href="{{ route('admin.productions.create') }}" class="btn btn-primary shadow rounded-pill">
                            <i class="fas fa-plus"></i> Add Production
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
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <table id="example1" class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Raw Salt</th>
                            <th>Finished Salt</th>
                            <th>Wastage</th>
                            <th>Machine</th>
                            <th>Cost</th>
                            <th>Date</th>
                            <th>Remarks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($productions->isEmpty())
                            <tr>
                                <td colspan="8" class="text-center">No productions found.</td>
                            </tr>
                        @endif
                        @foreach($productions as $item)
                        <tr>
                            <td>{{ $item->raw_salt_used ?? '' }}</td>
                            <td>{{ $item->finished_salt ?? '' }}</td>
                            <td>{{ $item->wastage ?? '' }}</td>
                            <td>{{ $item->machine_used ?? '' }}</td>
                            <td>{{ $item->electricity_fuel_cost ?? '' }}</td>
                            <td>{{ $item->production_date ?? '' }}</td>
                            <td>{{ $item->remarks ?? '' }}</td>
                            <td>
                                <a href="{{ route('admin.productions.edit', $item->id) }}" class="btn btn-sm btn-info">Edit</a>
                                <form action="{{ route('admin.productions.destroy', $item->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
