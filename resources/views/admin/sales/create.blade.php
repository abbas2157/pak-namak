@extends('admin.layout.app')
@section('title', 'Sales')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Sales List</h1>
                </div>
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Sales List</li>
                        </ol>
                    </div>
                    <div class="col-sm-6 d-flex justify-content-end">
                        <a href="{{ route('admin.sales.index') }}" class="btn btn-primary shadow rounded-pill">
                            <i class="fas fa-plus"></i> All Sales
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Sale Details</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label>Salt Type</label>
                            <select name="salt_type_id" id="salt_type_id" class="form-control" required>
                                <option value="">Select Salt</option>
                                @foreach($types as $salt)
                                    <option value="{{ $salt->id }}">{{ $salt->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <div id="TypeDallaSection">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label>Quantity (Ton)</label>
                                        <input type="number" name="quantity_ton" id="quantity_ton" class="form-control" placeholder="10 Ton">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label>Quantity (KG)</label>
                                        <input type="number" name="quantity_kg" id="quantity_kg" value="0" readonly class="form-control" placeholder="10000 KG">
                                    </div>
                                </div>
                            </div>
                            <div id="TypeThailaSection"></div>
                            <div id="TypePacketSection"></div>
                        </div>
                    </div>
                </div>
            </div>
             <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Other Sales Detail</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label>Shop</label>
                            <select name="shop_id" id="shop_id" class="form-control" required>
                                <option value="">Select Shop</option>
                                @foreach($shops as $shop)
                                    <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label>Sale Date</label>
                            <input type="date" name="date" id="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label>Total Amount</label>
                            <input type="number" name="total_sales_amount" value="0" readonly id="total_sales_amount" class="form-control">
                        </div>
                        <div class="col-md-12 mb-2">
                            <label>Remarks</label>
                            <textarea name="remarks" id="remarks" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success" type="submit">Create Sale</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
@endsection
