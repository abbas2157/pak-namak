@extends('admin.layout.app')
@section('title', 'New Sale')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">New Sale</h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sales.index') }}">Sales</a></li>
                    <li class="breadcrumb-item active">New Sale</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end">
                <a href="{{ route('admin.sales.index') }}"
                   class="btn btn-light px-4" style="border-radius:8px;border:1px solid #d1d5db;">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Sales
                </a>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        @if($prefill)
        <div class="alert border-0 shadow-sm mb-3 d-flex align-items-center"
             style="background:#fff3cd;color:#856404;border-radius:10px;">
            <i class="fas fa-exchange-alt fa-lg mr-3"></i>
            <div>
                <strong>Pre-filled from Order {{ $prefill->reference }}</strong> —
                {{ $prefill->display_name }}.
                Set pricing and save to create the sale.
            </div>
        </div>
        @endif
        <form action="{{ route('admin.sales.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

        {{-- ======================================================
             DALLA
        ====================================================== --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header border-bottom py-3" style="background:#f0f4ff;">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold" style="color:#4e73df;">
                        <i class="fas fa-tint mr-2"></i>Dalla — ڈلہ
                    </h6>
                    <button type="button" class="btn btn-sm section-toggle"
                            data-target="#dallaBody"
                            style="background:#e8f0fe;color:#4e73df;border:1px solid #c3d3f7;border-radius:6px;">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" id="dallaBody">
                <div class="row align-items-end">
                    <div class="col-md-3 mb-3">
                        <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                            Quantity (Mann) — وزن: من
                        </label>
                        @php $prefillDalla = $prefill?->items->firstWhere('type','dalla'); @endphp
                        <input type="number" name="dalla[sold_quantity_mann]" id="sold_quantity_mann"
                               class="form-control" style="border-radius:8px;" placeholder="0"
                               value="{{ $prefillDalla?->quantity ?? '' }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                            Total KG — کل وزن
                        </label>
                        <input type="text" name="dalla[sold_quantity_kilo]" id="sold_quantity_kilo_dalla"
                               readonly class="form-control" style="border-radius:8px;background:#f8f9fc;">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                            Rate / Mann — فی من
                        </label>
                        <input type="number" name="dalla[pirce_per_mann]" id="pirce_per_mann"
                               class="form-control" style="border-radius:8px;" placeholder="0">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                            Rate / KG — فی کلو
                        </label>
                        <input type="number" name="dalla[pirce_per_kg]" id="pirce_per_kg_dalla"
                               readonly class="form-control" style="border-radius:8px;background:#f8f9fc;">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                            Sub Total — سب ٹوٹل
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="background:#f8f9fc;border-color:#d1d5db;border-radius:8px 0 0 8px;font-size:12px;color:#6b7280;">PKR</span>
                            </div>
                            <input type="text" name="dalla[sub_total]" id="sub_total_dalla"
                                   readonly class="form-control font-weight-bold"
                                   style="border-color:#d1d5db;border-radius:0 8px 8px 0;background:#f8f9fc;color:#4e73df;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ======================================================
             THAILA
        ====================================================== --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header border-bottom py-3" style="background:#f0fff8;">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold" style="color:#1cc88a;">
                        <i class="fas fa-shopping-bag mr-2"></i>Thaila — تھیلا
                    </h6>
                    <button type="button" class="btn btn-sm section-toggle"
                            data-target="#thailaBody"
                            style="background:#d4edda;color:#155724;border:1px solid #c3e6cb;border-radius:6px;">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" id="thailaBody">

                {{-- Header row --}}
                <div class="row mb-1 d-none d-md-flex">
                    <div class="col-md-1"><small class="text-uppercase font-weight-bold text-muted" style="font-size:10px;">Size</small></div>
                    <div class="col-md-2"><small class="text-uppercase font-weight-bold text-muted" style="font-size:10px;">Qty (تھیلا)</small></div>
                    <div class="col-md-2"><small class="text-uppercase font-weight-bold text-muted" style="font-size:10px;">Total KG</small></div>
                    <div class="col-md-2"><small class="text-uppercase font-weight-bold text-muted" style="font-size:10px;">Rate/Thaila</small></div>
                    <div class="col-md-2"><small class="text-uppercase font-weight-bold text-muted" style="font-size:10px;">Rate/KG</small></div>
                    <div class="col-md-3"><small class="text-uppercase font-weight-bold text-muted" style="font-size:10px;">Sub Total</small></div>
                </div>

                @foreach([5,10,30,35,40,50] as $size)
                @php $prefillThaila = $prefill?->items->where('type','thaila')->firstWhere('size',$size); @endphp
                <div class="row align-items-center mb-2 py-2" style="border-bottom:1px solid #f0f0f0;">
                    <div class="col-md-1 mb-2 mb-md-0">
                        <input type="text" name="thaila[{{ $size }}][kilo_{{ $size }}]"
                               id="quantity_{{ $size }}_kilo" value="{{ $size }}" readonly
                               class="form-control text-center font-weight-bold"
                               style="border-radius:8px;background:#f0fff8;color:#1cc88a;border-color:#c3e6cb;">
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <input type="number" name="thaila[{{ $size }}][sold_quantity_kilo_{{ $size }}]"
                               id="sold_quantity_kilo_{{ $size }}"
                               class="form-control" style="border-radius:8px;" placeholder="0"
                               value="{{ $prefillThaila?->quantity ?? '' }}">
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <input type="text" name="thaila[{{ $size }}][sold_quantity_kilo]"
                               id="sold_quantity_{{ $size }}_kilo_thaila"
                               readonly class="form-control" style="border-radius:8px;background:#f8f9fc;">
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <input type="number" name="thaila[{{ $size }}][pirce_per_thaila]"
                               id="pirce_per_{{ $size }}_killo_thaila"
                               class="form-control" style="border-radius:8px;" placeholder="0">
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <input type="number" name="thaila[{{ $size }}][pirce_per_kg]"
                               id="pirce_per_kg_{{ $size }}_killo_thaila"
                               readonly class="form-control" style="border-radius:8px;background:#f8f9fc;">
                    </div>
                    <div class="col-md-3 mb-2 mb-md-0">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="background:#f8f9fc;border-color:#d1d5db;border-radius:8px 0 0 8px;font-size:12px;color:#6b7280;">PKR</span>
                            </div>
                            <input type="text" name="thaila[{{ $size }}][sub_total]"
                                   id="sub_total_{{ $size }}_killo_thaila"
                                   readonly class="form-control font-weight-bold"
                                   style="border-color:#d1d5db;border-radius:0 8px 8px 0;background:#f8f9fc;color:#1cc88a;">
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>

        {{-- ======================================================
             PACKAGE
        ====================================================== --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header border-bottom py-3" style="background:#fffdf0;">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold" style="color:#f6c23e;">
                        <i class="fas fa-box mr-2"></i>Package — پیکٹ
                    </h6>
                    <button type="button" class="btn btn-sm section-toggle"
                            data-target="#packageBody"
                            style="background:#fff3cd;color:#856404;border:1px solid #ffc107;border-radius:6px;">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" id="packageBody">

                {{-- Header row --}}
                <div class="row mb-1 d-none d-md-flex">
                    <div class="col-md-1"><small class="text-uppercase font-weight-bold text-muted" style="font-size:10px;">Size</small></div>
                    <div class="col-md-2"><small class="text-uppercase font-weight-bold text-muted" style="font-size:10px;">Qty (بنڈل)</small></div>
                    <div class="col-md-2"><small class="text-uppercase font-weight-bold text-muted" style="font-size:10px;">Bundle Type</small></div>
                    <div class="col-md-2"><small class="text-uppercase font-weight-bold text-muted" style="font-size:10px;">Total KG</small></div>
                    <div class="col-md-2"><small class="text-uppercase font-weight-bold text-muted" style="font-size:10px;">Rate/Bundle</small></div>
                    <div class="col-md-3"><small class="text-uppercase font-weight-bold text-muted" style="font-size:10px;">Sub Total</small></div>
                </div>

                @foreach([250,300,400,500,600,700] as $gram)
                <div class="row align-items-center mb-2 py-2" style="border-bottom:1px solid #f0f0f0;">
                    <div class="col-md-1 mb-2 mb-md-0">
                        <input type="text" name="package[{{ $gram }}][gram_{{ $gram }}]"
                               id="quantity_{{ $gram }}_gram" value="{{ $gram }}" readonly
                               class="form-control text-center font-weight-bold"
                               style="border-radius:8px;background:#fffdf0;color:#856404;border-color:#ffc107;font-size:12px;">
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <input type="number" name="package[{{ $gram }}][sold_bundles_quantity_{{ $gram }}_gram]"
                               id="sold_bundles_quantity_{{ $gram }}_gram"
                               class="form-control" style="border-radius:8px;" placeholder="0">
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <select name="package[{{ $gram }}][bundle_type_{{ $gram }}_gram]"
                                id="bundle_type_{{ $gram }}_gram"
                                class="form-control" style="border-radius:8px;">
                            <option value="10">10 پیکٹ</option>
                            <option value="20">20 پیکٹ</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <input type="text" name="package[{{ $gram }}][total_kg_{{ $gram }}_gram]"
                               id="total_kg_{{ $gram }}_gram"
                               readonly class="form-control" style="border-radius:8px;background:#f8f9fc;">
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <input type="number" name="package[{{ $gram }}][price_per_bundle]"
                               id="price_per_bundle_{{ $gram }}_gram"
                               class="form-control" style="border-radius:8px;" placeholder="0">
                    </div>
                    <div class="col-md-3 mb-2 mb-md-0">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="background:#f8f9fc;border-color:#d1d5db;border-radius:8px 0 0 8px;font-size:12px;color:#6b7280;">PKR</span>
                            </div>
                            <input type="text" name="package[{{ $gram }}][sub_total]"
                                   id="sub_total_{{ $gram }}_gram"
                                   readonly class="form-control font-weight-bold"
                                   style="border-color:#d1d5db;border-radius:0 8px 8px 0;background:#f8f9fc;color:#856404;">
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>

        {{-- ======================================================
             SALE DETAILS (Shop / Date / Total / Remarks / Bill)
        ====================================================== --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header border-bottom py-3"
                 style="background:linear-gradient(135deg,#4e73df,#224abe);">
                <h6 class="mb-0 font-weight-bold text-white">
                    <i class="fas fa-store mr-2"></i>Sale Details
                </h6>
            </div>
            <div class="card-body py-4">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                            Shop <span class="text-danger">*</span>
                        </label>
                        <select name="shop_id" id="shop_id" class="form-control select2" required
                                style="border-radius:8px;">
                            <option value="">Select Shop</option>
                            @foreach($shops as $shop)
                                <option value="{{ $shop->id }}"
                                    {{ ($prefill && $prefill->shop_id == $shop->id) ? 'selected' : '' }}>
                                    {{ $shop->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                            Sale Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="sale_date" id="date" class="form-control"
                               value="{{ date('Y-m-d') }}" required style="border-radius:8px;">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                            Grand Total (PKR)
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text font-weight-bold"
                                      style="background:#e8f0fe;color:#4e73df;border-color:#c3d3f7;border-radius:8px 0 0 8px;">PKR</span>
                            </div>
                            <input type="number" name="total_sales_amount" value="0" readonly
                                   id="total_sales_amount" class="form-control font-weight-bold"
                                   style="border-color:#c3d3f7;border-radius:0 8px 8px 0;font-size:18px;color:#4e73df;background:#f0f4ff;">
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                            Received (PKR)
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text font-weight-bold"
                                      style="background:#d4edda;color:#155724;border-color:#c3e6cb;border-radius:8px 0 0 8px;">PKR</span>
                            </div>
                            <input type="number" name="received_amount" id="received_amount" value="0" min="0"
                                   class="form-control font-weight-bold"
                                   style="border-color:#c3e6cb;border-radius:0 8px 8px 0;font-size:18px;color:#155724;background:#f0fff8;">
                        </div>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                            Remarks
                        </label>
                        <textarea name="remarks" id="remarks" class="form-control" rows="2"
                                  style="border-radius:8px;" placeholder="Optional notes..."></textarea>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-uppercase font-weight-bold text-muted" style="font-size:11px;letter-spacing:.5px;">
                            Upload Bill Image
                        </label>
                        <input type="file" name="bill_image" id="bill_image"
                               class="form-control" accept="image/*"
                               style="border-radius:8px;">
                    </div>
                </div>

                <div class="d-flex justify-content-end pt-2">
                    <a href="{{ route('admin.sales.index') }}"
                       class="btn btn-light px-4 mr-2" style="border-radius:8px;border:1px solid #d1d5db;">
                        Cancel
                    </a>
                    <button class="btn btn-primary px-5" type="submit"
                            style="border-radius:8px;background:linear-gradient(135deg,#4e73df,#224abe);border:none;font-weight:600;">
                        <i class="fas fa-save mr-2"></i> Create Sale
                    </button>
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

    // ── Section toggle ──────────────────────────────────────────
    $('.section-toggle').on('click', function () {
        const target = $($(this).data('target'));
        target.slideToggle(200);
        $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');
    });

    const MANN_TO_KG = 40;
    function num(v) { return parseFloat(v) || 0; }
    function round(v) { return Math.round(v * 100) / 100; }

    /* ── DALLA ── */
    function calcDalla() {
        let mann    = num($('#sold_quantity_mann').val());
        let rateMann = num($('#pirce_per_mann').val());
        let kg      = mann * MANN_TO_KG;
        let rateKg  = rateMann / MANN_TO_KG;
        let sub     = mann * rateMann;
        $('#sold_quantity_kilo_dalla').val(round(kg));
        $('#pirce_per_kg_dalla').val(round(rateKg));
        $('#sub_total_dalla').val(round(sub));
        calcGrandTotal();
    }
    $('#sold_quantity_mann, #pirce_per_mann').on('input', calcDalla);

    /* ── THAILA ── */
    function calcThaila(size) {
        let qty    = num($('#sold_quantity_kilo_' + size).val());
        let rate   = num($('#pirce_per_' + size + '_killo_thaila').val());
        let kg     = qty * size;
        let rateKg = rate / size;
        let sub    = qty * rate;
        $('#sold_quantity_' + size + '_kilo_thaila').val(round(kg));
        $('#pirce_per_kg_' + size + '_killo_thaila').val(round(rateKg));
        $('#sub_total_' + size + '_killo_thaila').val(round(sub));
        calcGrandTotal();
    }
    [5, 10, 30, 35, 40, 50].forEach(function (size) {
        $('#sold_quantity_kilo_' + size + ', #pirce_per_' + size + '_killo_thaila')
            .on('input', function () { calcThaila(size); });
    });

    /* ── PACKAGE ── */
    function calcPackage(gram) {
        let bundles    = num($('#sold_bundles_quantity_' + gram + '_gram').val());
        let bundleType = num($('#bundle_type_' + gram + '_gram').val());
        let rate       = num($('#price_per_bundle_' + gram + '_gram').val());
        let kg         = bundles * bundleType * (gram / 1000);
        let sub        = bundles * rate;
        $('#total_kg_' + gram + '_gram').val(round(kg));
        $('#sub_total_' + gram + '_gram').val(round(sub));
        calcGrandTotal();
    }
    [250, 300, 400, 500, 600, 700].forEach(function (gram) {
        $('#sold_bundles_quantity_' + gram + '_gram, ' +
          '#bundle_type_' + gram + '_gram, ' +
          '#price_per_bundle_' + gram + '_gram')
        .on('input change', function () { calcPackage(gram); });
    });

    /* ── GRAND TOTAL ── */
    function calcGrandTotal() {
        let total = 0;
        $('input[id^="sub_total"]').each(function () { total += num($(this).val()); });
        $('#total_sales_amount').val(round(total));
        // clamp received to not exceed grand total
        let received = num($('#received_amount').val());
        if (received > total) { $('#received_amount').val(round(total)); }
    }

    $('#received_amount').on('input', function () {
        let total = num($('#total_sales_amount').val());
        if (num($(this).val()) > total) { $(this).val(round(total)); }
    });

    /* ── SELECT2 ── */
    $('.select2').select2({ placeholder: 'Select a shop', allowClear: true });
    $('.select2').next('.select2-container').find('.select2-selection').css({ height: '39px' });
});
</script>
@endsection
