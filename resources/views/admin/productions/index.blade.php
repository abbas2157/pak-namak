@extends('admin.layout.app')
@section('title','Productions')

@section('content')
<div class="container mt-4">

    <a href="{{ route('admin.productions.create') }}" class="btn btn-primary mb-3">Add Production</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
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
            @foreach($productions as $p)
            <tr>
                <td>{{ $p->id }}</td>
                <td>{{ $p->raw_salt_used }}</td>
                <td>{{ $p->finished_salt }}</td>
                <td>{{ $p->wastage }}</td>
                <td>{{ $p->machine_used }}</td>
                <td>{{ $p->electricity_fuel_cost }}</td>
                <td>{{ $p->production_date }}</td>
                <td>{{ $p->remarks }}</td>
                <td>
                    <a href="{{ route('admin.productions.edit', $p->id) }}" class="btn btn-sm btn-info">Edit</a>
                    <form action="{{ route('admin.productions.destroy', $p->id) }}" method="POST" style="display:inline-block;">
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
@endsection
