@extends('admin.layout.app')
@section('content')
<section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Update Production</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Update Production</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Update Production</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.productions.update', $production->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body row">
                                    <div class="form-group col-md-6 mb-2">
                                        <label for="production_date">Production Date</label>
                                        <input type="date" id="production_date" name="production_date" class="form-control" value="{{ $production->production_date }}">
                                    </div>

                                    <div class="form-group col-md-6 mb-2">
                                        <label for="raw_salt_used">Raw Salt Used</label>
                                        <input type="number" id="raw_salt_used" name="raw_salt_used" class="form-control" placeholder="Raw Salt Used" value="{{ $production->raw_salt_used }}">
                                    </div>

                                    <div class="form-group col-md-6 mb-2">
                                        <label for="finished_salt">Finished Salt</label>
                                        <input type="number" id="finished_salt" name="finished_salt" class="form-control" placeholder="Finished Salt" value="{{ $production->finished_salt }}">
                                    </div>

                                    <div class="form-group col-md-6 mb-2">
                                        <label for="wastage">Wastage</label>
                                        <input type="number" id="wastage" name="wastage" class="form-control" placeholder="Wastage" value="{{ $production->wastage }}">
                                    </div>

                                    <div class="form-group col-md-6 mb-2">
                                        <label for="machine_used">Machine Used</label>
                                        <input type="text" id="machine_used" name="machine_used" class="form-control" placeholder="Machine Used" value="{{ $production->machine_used }}">
                                    </div>

                                    <div class="form-group col-md-6 mb-2">
                                        <label for="electricity_fuel_cost">Electricity / Fuel Cost</label>
                                        <input type="number" id="electricity_fuel_cost" name="electricity_fuel_cost" class="form-control" placeholder="Electricity/Fuel Cost" value="{{ $production->electricity_fuel_cost }}">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="remarks">Remarks</label>
                                        <textarea id="remarks" name="remarks" class="form-control" placeholder="Remarks" rows="3">{{ $production->remarks }}</textarea>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('admin.productions.index') }}" class="btn btn-secondary">Back</a>
                                        <button type="submit" class="btn btn-success">Update Production</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section
@endsection
