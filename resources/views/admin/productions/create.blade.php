@extends('admin.layout.app')
@section('title','Add Production')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header">
                    <h1 class="mb-0">Add Production</h1>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.productions.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="production_date">Production Date</label>
                            <input type="date" id="production_date" name="production_date" class="form-control">
                        </div>

                        <div class="form-group mb-3">
                            <label for="raw_salt_used">Raw Salt Used</label>
                            <input type="number" id="raw_salt_used" name="raw_salt_used" class="form-control" placeholder="Raw Salt Used">
                        </div>

                        <div class="form-group mb-3">
                            <label for="finished_salt">Finished Salt</label>
                            <input type="number" id="finished_salt" name="finished_salt" class="form-control" placeholder="Finished Salt">
                        </div>

                        <div class="form-group mb-3">
                            <label for="wastage">Wastage</label>
                            <input type="number" id="wastage" name="wastage" class="form-control" placeholder="Wastage">
                        </div>

                        <div class="form-group mb-3">
                            <label for="machine_used">Machine Used</label>
                            <input type="text" id="machine_used" name="machine_used" class="form-control" placeholder="Machine Used">
                        </div>

                        <div class="form-group mb-3">
                            <label for="electricity_fuel_cost">Electricity / Fuel Cost</label>
                            <input type="number" id="electricity_fuel_cost" name="electricity_fuel_cost" class="form-control" placeholder="Electricity/Fuel Cost">
                        </div>

                        <div class="form-group mb-3">
                            <label for="remarks">Remarks</label>
                            <textarea id="remarks" name="remarks" class="form-control" placeholder="Remarks" rows="3"></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">Save</button>
                            <a href="{{ route('admin.productions.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
