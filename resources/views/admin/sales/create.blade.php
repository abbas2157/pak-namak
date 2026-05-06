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
            <form action="{{ route('admin.sales.store') }}" method="POST">
                @csrf
                <div class="card card-primary collapsed-card">
                    <div class="card-header">
                        <h3 class="card-title">Dalla (ڈلہ)</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label for="sold_quantity_mann">Quantity Sold (وزن: من)</label>
                                <input type="number" name="dalla[sold_quantity_mann]" id="sold_quantity_mann" class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="sold_quantity_kilo_dalla">کل وزن(KG)</label>
                                <input type="text" name="dalla[sold_quantity_kilo]" id="sold_quantity_kilo_dalla" readonly class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="pirce_per_mann">Rate (فی من قیمت)</label>
                                <input type="number" name="dalla[pirce_per_mann]" id="pirce_per_mann" class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="pirce_per_kg_dalla">Rate (فی کلو قیمت)</label>
                                <input type="number" name="dalla[pirce_per_kg]" id="pirce_per_kg_dalla" readonly class="form-control">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="sub_total_dalla">Sub Total (سب ٹوٹل)</label>
                                <input type="text" name="dalla[sub_total]" id="sub_total_dalla" readonly class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card card-primary collapsed-card">
                    <div class="card-header">
                        <h3 class="card-title">Thails (تھیلا)</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-1 mb-2">
                                <label for="quantity_5_kilo"> کلو  5</label>
                                <input type="text" name="thaila[5][kilo_5]" id="quantity_5_kilo" value="5" readonly class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="sold_quantity_kilo_5">Quantity Sold (تھیلا)</label>
                                <input type="number" name="thaila[5][sold_quantity_kilo_5]" id="sold_quantity_kilo_5" class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="sold_quantity_5_kilo_thaila">کل وزن(KG)</label>
                                <input type="text" name="thaila[5][sold_quantity_kilo]" id="sold_quantity_5_kilo_thaila" readonly class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="pirce_per_5_killo_thaila">Rate (فی تھیلا قیمت)</label>
                                <input type="number" name="thaila[5][pirce_per_thaila]" id="pirce_per_5_killo_thaila" class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="pirce_per_kg_5_killo_thaila">Rate (فی کلو قیمت)</label>
                                <input type="number" name="thaila[5][pirce_per_kg]" id="pirce_per_kg_5_killo_thaila" readonly class="form-control">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="sub_total_5_killo_thaila">Sub Total (سب ٹوٹل)</label>
                                <input type="text" name="thaila[5][sub_total]" id="sub_total_5_killo_thaila" readonly class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-1 mb-2">
                                <label for="quantity_10_kilo"> کلو  10</label>
                                <input type="text" name="thaila[10][kilo_10]" id="quantity_10_kilo" value="10" readonly class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="sold_quantity_kilo_10">Quantity Sold (تھیلا)</label>
                                <input type="number" name="thaila[10][sold_quantity_kilo_10]" id="sold_quantity_kilo_10" class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="sold_quantity_10_kilo_thaila">کل وزن(KG)</label>
                                <input type="text" name="thaila[10][sold_quantity_kilo]" id="sold_quantity_10_kilo_thaila" readonly class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="pirce_per_10_killo_thaila">Rate (فی تھیلا قیمت)</label>
                                <input type="number" name="thaila[10][pirce_per_thaila]" id="pirce_per_10_killo_thaila" class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="pirce_per_kg_10_killo_thaila">Rate (فی کلو قیمت)</label>
                                <input type="number" name="thaila[10][pirce_per_kg]" id="pirce_per_kg_10_killo_thaila" readonly class="form-control">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="sub_total_10_killo_thaila">Sub Total (سب ٹوٹل)</label>
                                <input type="text" name="thaila[10][sub_total]" id="sub_total_10_killo_thaila" readonly class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-1 mb-2">
                                <label for="quantity_30_kilo"> کلو  30</label>
                                <input type="text" name="thaila[30][kilo_30]" id="quantity_30_kilo" value="30" readonly class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="sold_quantity_kilo_30">Quantity Sold (تھیلا)</label>
                                <input type="number" name="thaila[30][sold_quantity_kilo_30]" id="sold_quantity_kilo_30" class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="sold_quantity_30_kilo_thaila">کل وزن(KG)</label>
                                <input type="text" name="thaila[30][sold_quantity_kilo]" id="sold_quantity_30_kilo_thaila" readonly class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="pirce_per_30_killo_thaila">Rate (فی تھیلا قیمت)</label>
                                <input type="number" name="thaila[30][pirce_per_thaila]" id="pirce_per_30_killo_thaila" class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="pirce_per_kg_30_killo_thaila">Rate (فی کلو قیمت)</label>
                                <input type="number" name="thaila[30][pirce_per_kg]" id="pirce_per_kg_30_killo_thaila" readonly class="form-control">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="sub_total_30_killo_thaila">Sub Total (سب ٹوٹل)</label>
                                <input type="text" name="thaila[30][sub_total]" id="sub_total_30_killo_thaila" readonly class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-1 mb-2">
                                <label for="quantity_50_kilo"> کلو  50</label>
                                <input type="text" name="thaila[50][kilo_50]" id="quantity_50_kilo" value="50" readonly class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="sold_quantity_kilo_50">Quantity Sold (تھیلا)</label>
                                <input type="number" name="thaila[50][sold_quantity_kilo_50]" id="sold_quantity_kilo_50" class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="sold_quantity_50_kilo_thaila">کل وزن(KG)</label>
                                <input type="text" name="thaila[50][sold_quantity_kilo]" id="sold_quantity_50_kilo_thaila" readonly class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="pirce_per_50_killo_thaila">Rate (فی تھیلا قیمت)</label>
                                <input type="number" name="thaila[50][pirce_per_thaila]" id="pirce_per_50_killo_thaila" class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="pirce_per_kg_50_killo_thaila">Rate (فی کلو قیمت)</label>
                                <input type="number" name="thaila[50][pirce_per_kg]" id="pirce_per_kg_50_killo_thaila" readonly class="form-control">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="sub_total_50_killo_thaila">Sub Total (سب ٹوٹل)</label>
                                <input type="text" name="thaila[50][sub_total]" id="sub_total_50_killo_thaila" readonly class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card card-primary collapsed-card">
                    <div class="card-header">
                        <h3 class="card-title">Package (پیکٹ)</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-1 mb-2">
                                <label for="quantity_250_gram">250 گرام</label>
                                <input type="text" name="package[250][gram_250]" id="quantity_250_gram" value="250" readonly class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="sold_bundles_quantity_250_gram">Quantity Sold (بنڈل)</label>
                                <input type="number" name="package[250][sold_bundles_quantity_250_gram]" id="sold_bundles_quantity_250_gram" class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="bundle_type_250_gram">Bundle </label>
                                <select name="package[250][bundle_type_250_gram]" id="bundle_type_250_gram" class="form-control">
                                    <option value="10">10 packages (پیکٹ)</option>
                                    <option value="20">20 packages (پیکٹ)</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="total_kg_250_gram">کل وزن(KG)</label>
                                <input type="text" name="package[250][total_kg_250_gram]" id="total_kg_250_gram" readonly class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="price_per_bundle_250_gram">Rate (فی بنڈل)</label>
                                <input type="number" name="package[250][price_per_bundle]" id="price_per_bundle_250_gram" class="form-control">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="sub_total_250_gram">Sub Total (سب ٹوٹل)</label>
                                <input type="text" name="package[250][sub_total]" id="sub_total_250_gram" readonly class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-1 mb-2">
                                <label for="quantity_300_gram">300 گرام</label>
                                <input type="text" name="package[300][gram_300]" id="quantity_300_gram" value="300" readonly class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="sold_bundles_quantity_300_gram">Quantity Sold (بنڈل)</label>
                                <input type="number" name="package[300][sold_bundles_quantity_300_gram]" id="sold_bundles_quantity_300_gram" class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="bundle_type_300_gram">Bundle </label>
                                <select name="package[300][bundle_type_300_gram]" id="bundle_type_300_gram" class="form-control">
                                    <option value="10">10 packages (پیکٹ)</option>
                                    <option value="20">20 packages (پیکٹ)</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="total_kg_300_gram">کل وزن(KG)</label>
                                <input type="text" name="package[300][total_kg_300_gram]" id="total_kg_300_gram" readonly class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="price_per_bundle_300_gram">Rate (فی بنڈل)</label>
                                <input type="number" name="package[300][price_per_bundle]" id="price_per_bundle_300_gram" class="form-control">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="sub_total_300_gram">Sub Total (سب ٹوٹل)</label>
                                <input type="text" name="package[300][sub_total]" id="sub_total_300_gram" readonly class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-1 mb-2">
                                <label for="quantity_400_gram">400 گرام</label>
                                <input type="text" name="package[400][gram_400]" id="quantity_400_gram" value="400" readonly class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="sold_bundles_quantity_400_gram">Quantity Sold (بنڈل)</label>
                                <input type="number" name="package[400][sold_bundles_quantity_400_gram]" id="sold_bundles_quantity_400_gram" class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="bundle_type_400_gram">Bundle </label>
                                <select name="package[400][bundle_type_400_gram]" id="bundle_type_400_gram" class="form-control">
                                    <option value="10">10 packages (پیکٹ)</option>
                                    <option value="20">20 packages (پیکٹ)</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="total_kg_400_gram">کل وزن(KG)</label>
                                <input type="text" name="package[400][total_kg_400_gram]" id="total_kg_400_gram" readonly class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="price_per_bundle_400_gram">Rate (فی بنڈل)</label>
                                <input type="number" name="package[400][price_per_bundle]" id="price_per_bundle_400_gram" class="form-control">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="sub_total_400_gram">Sub Total (سب ٹوٹل)</label>
                                <input type="text" name="package[400][sub_total]" id="sub_total_400_gram" readonly class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-1 mb-2">
                                <label for="quantity_500_gram">500 گرام</label>
                                <input type="text" name="package[500][gram_500]" id="quantity_500_gram" value="500" readonly class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="sold_bundles_quantity_500_gram">Quantity Sold (بنڈل)</label>
                                <input type="number" name="package[500][sold_bundles_quantity_500_gram]" id="sold_bundles_quantity_500_gram" class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="bundle_type_500_gram">Bundle </label>
                                <select name="package[500][bundle_type_500_gram]" id="bundle_type_500_gram" class="form-control">
                                    <option value="10">10 packages (پیکٹ)</option>
                                    <option value="20">20 packages (پیکٹ)</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="total_kg_500_gram">کل وزن(KG)</label>
                                <input type="text" name="package[500][total_kg_500_gram]" id="total_kg_500_gram" readonly class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="price_per_bundle_500_gram">Rate (فی بنڈل)</label>
                                <input type="number" name="package[500][price_per_bundle]" id="price_per_bundle_500_gram" class="form-control">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="sub_total_500_gram">Sub Total (سب ٹوٹل)</label>
                                <input type="text" name="package[500][sub_total]" id="sub_total_500_gram" readonly class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-1 mb-2">
                                <label for="quantity_600_gram">600 گرام</label>
                                <input type="text" name="package[600][gram_600]" id="quantity_600_gram" value="600" readonly class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="sold_bundles_quantity_600_gram">Quantity Sold (بنڈل)</label>
                                <input type="number" name="package[600][sold_bundles_quantity_600_gram]" id="sold_bundles_quantity_600_gram" class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="bundle_type_600_gram">Bundle </label>
                                <select name="package[600][bundle_type_600_gram]" id="bundle_type_600_gram" class="form-control">
                                    <option value="10">10 packages (پیکٹ)</option>
                                    <option value="20">20 packages (پیکٹ)</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="total_kg_600_gram">کل وزن(KG)</label>
                                <input type="text" name="package[600][total_kg_600_gram]" id="total_kg_600_gram" readonly class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="price_per_bundle_600_gram">Rate (فی بنڈل)</label>
                                <input type="number" name="package[600][price_per_bundle]" id="price_per_bundle_600_gram" class="form-control">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="sub_total_600_gram">Sub Total (سب ٹوٹل)</label>
                                <input type="text" name="package[600][sub_total]" id="sub_total_600_gram" readonly class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-1 mb-2">
                                <label for="quantity_700_gram">700 گرام</label>
                                <input type="text" name="package[700][gram_700]" id="quantity_700_gram" value="700" readonly class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="sold_bundles_quantity_700_gram">Quantity Sold (بنڈل)</label>
                                <input type="number" name="package[700][sold_bundles_quantity_700_gram]" id="sold_bundles_quantity_700_gram" class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="bundle_type_700_gram">Bundle </label>
                                <select name="package[700][bundle_type_700_gram]" id="bundle_type_700_gram" class="form-control">
                                    <option value="10">10 packages (پیکٹ)</option>
                                    <option value="20">20 packages (پیکٹ)</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="total_kg_700_gram">کل وزن(KG)</label>
                                <input type="text" name="package[700][total_kg_700_gram]" id="total_kg_700_gram" readonly class="form-control">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="price_per_bundle_700_gram">Rate (فی بنڈل)</label>
                                <input type="number" name="package[700][price_per_bundle]" id="price_per_bundle_700_gram" class="form-control">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="sub_total_700_gram">Sub Total (سب ٹوٹل)</label>
                                <input type="text" name="package[700][sub_total]" id="sub_total_700_gram" readonly class="form-control">
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
                                <select name="shop_id" id="shop_id" class="form-control select2" required>
                                    <option value="">Select Shop</option>
                                    @foreach($shops as $shop)
                                        <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label>Sale Date</label>
                                <input type="date" name="sale_date" id="date" class="form-control" value="{{ date('Y-m-d') }}" required>
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
            </form>
        </div>
    </section>
@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<script>
$(document).ready(function () {

    const MANN_TO_KG = 40;

    function num(v) {
        return parseFloat(v) || 0;
    }

    function round(v) {
        return Math.round(v * 100) / 100;
    }

    /* =====================
       DALLA
    ===================== */
    function calcDalla() {
        let mann = num($('#sold_quantity_mann').val());
        let rateMann = num($('#pirce_per_mann').val());

        let kg = mann * MANN_TO_KG;
        let rateKg = rateMann / MANN_TO_KG;
        let sub = mann * rateMann;

        $('#sold_quantity_kilo_dalla').val(round(kg));
        $('#pirce_per_kg_dalla').val(round(rateKg));
        $('#sub_total_dalla').val(round(sub));

        calcGrandTotal();
    }

    $('#sold_quantity_mann, #pirce_per_mann').on('input', calcDalla);


    /* =====================
       THAILA
    ===================== */
    function calcThaila(size) {
        let qty = num($('#sold_quantity_kilo_' + size).val());
        let rate = num($('#pirce_per_' + size + '_killo_thaila').val());

        let kg = qty * size;
        let rateKg = rate / size;
        let sub = qty * rate;

        $('#sold_quantity_' + size + '_kilo_thaila').val(round(kg));
        $('#pirce_per_kg_' + size + '_killo_thaila').val(round(rateKg));
        $('#sub_total_' + size + '_killo_thaila').val(round(sub));

        calcGrandTotal();
    }

    [5, 10, 30, 50].forEach(function (size) {
        $('#sold_quantity_kilo_' + size + ', #pirce_per_' + size + '_killo_thaila')
            .on('input', function () {
                calcThaila(size);
            });
    });



    /* =====================
       PACKAGE (FIXED)
    ===================== */
    function calcPackage(gram) {
        let bundles = num($('#sold_bundles_quantity_' + gram + '_gram').val());
        let bundleType = num($('#bundle_type_' + gram + '_gram').val());
        let rate = num($('#price_per_bundle_' + gram + '_gram').val());

        let kg = bundles * bundleType * (gram / 1000);
        let sub = bundles * rate;

        $('#total_kg_' + gram + '_gram').val(round(kg));
        $('#sub_total_' + gram + '_gram').val(round(sub));

        calcGrandTotal();
    }

    [250, 300, 400, 500, 600, 700].forEach(function (gram) {
        $('#sold_bundles_quantity_' + gram + '_gram, ' +
          '#bundle_type_' + gram + '_gram, ' +
          '#price_per_bundle_' + gram + '_gram')
        .on('input change', function () {
            calcPackage(gram);
        });
    });


    /* =====================
       GRAND TOTAL
    ===================== */
    function calcGrandTotal() {
        let total = 0;
        $('input[id^="sub_total"]').each(function () {
            total += num($(this).val());
        });
        $('#total_sales_amount').val(round(total));
    }

});
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Select an option",
        allowClear: true
    });
    $('.select2').next('.select2-container')
    .find('.select2-selection')
    .css({
        'height': '39px'
    });
});
</script>

@endsection
