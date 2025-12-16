@extends('admin.layout.app')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Update Vendor</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Update Vendor</li>
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
                            <h3 class="card-title">update Vendor</h3>
                            
                        </div>
                        
                        <div class="card-body">
                            <form action="{{ route('admin.vendors.update', $vendor->id) }}" method="POST">
                                  @method('PUT')  
                                @csrf
                              
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ $vendor->name }}">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="shop">Shop</label>
                                    <input type="text" name="shop" class="form-control" value="{{ $vendor->shop }}">
                                    @error('shop')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="number" name="phone" class="form-control" value="{{ $vendor->phone }}">
                                    @error('phone')
                                        <span class="text-danger">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <input type="text" name="address" class="form-control" value="{{ $vendor->address }}">
                                    @error('address')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary">Update</button>
                                  <a href="{{ route('admin.vendors.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-plus"></i> Back
                            </form>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection