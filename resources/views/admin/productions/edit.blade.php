@extends('admin.layout.app')
@section('title','Edit Production')

@section('content')
<div class="container mt-4">
    <h3>Edit Production</h3>
    <form action="{{ route('admin.productions.update', $production->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-2">
            <input type="date" name="production_date" class="form-control" value="{{ $production->production_date }}">
        </div>
        <div class="mb-2">
            <input type="number" name="raw_salt_used" class="form-control" value="{{ $production->raw_salt_used }}">
        </div>
        <div class="mb-2">
            <input type="number" name="finished_salt" class="form-control" value="{{ $production->finished_salt }}">
        </div>
        <div class="mb-2">
            <input type="number" name="wastage" class="form-control" value="{{ $production->wastage }}">
        </div>
        <div class="mb-2">
            <input type="text" name="machine_used" class="form-control" value="{{ $production->machine_used }}">
        </div>
        <div class="mb-2">
            <input type="number" name="electricity_fuel_cost" class="form-control" value="{{ $production->electricity_fuel_cost }}">
        </div>
        <div class="mb-2">
            <textarea name="remarks" class="form-control">{{ $production->remarks }}</textarea>
        </div>
        <button class="btn btn-success">Update</button>
        <a href="{{ route('admin.productions.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
