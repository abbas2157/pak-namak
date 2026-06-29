@extends('admin.layout.app')
@section('title', 'New Sale')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">New Sale <small class="text-muted pn-stat-sub">نئی فروخت</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sales.index') }}">Sales</a></li>
                    <li class="breadcrumb-item active">New Sale</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end">
                <a href="{{ route('admin.sales.index') }}"
                   class="btn btn-light px-4 btn-modal-cancel">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Sales / فروخت پر واپس
                </a>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        @if($prefill)
        <div class="alert alert-warning border-0 shadow-sm mb-3 card-pn d-flex align-items-center">
            <i class="fas fa-exchange-alt fa-lg mr-3"></i>
            <div>
                <strong>Pre-filled from Order {{ $prefill->reference }}</strong> —
                {{ $prefill->display_name }}.
                Quantities and prices are pre-filled — review and save.
            </div>
        </div>
        @endif
        <form action="{{ route('admin.sales.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($prefill)
                <input type="hidden" name="order_id" value="{{ $prefill->id }}">
            @endif

        {{-- ======================================================
             DALLA
        ====================================================== --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header border-bottom py-3 ch-blue">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold text-c-blue2">
                        <i class="fas fa-tint mr-2"></i>Dalla — ڈلہ
                    </h6>
                    <button type="button" class="btn btn-sm btn-pn btn-act-view section-toggle"
                            data-target="#dallaBody">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" id="dallaBody">
                <div class="row align-items-end">
                    <div class="col-md-3 mb-3">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">
                            Quantity (Mann) — وزن: من
                        </label>
                        @php $prefillDalla = $prefill?->items->firstWhere('type','dalla'); @endphp
                        <input type="number" name="dalla[sold_quantity_mann]" id="sold_quantity_mann"
                               class="form-control fc-pn" placeholder="0"
                               value="{{ $prefillDalla?->quantity ?? '' }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">
                            Total KG — کل وزن
                        </label>
                        <input type="text" name="dalla[sold_quantity_kilo]" id="sold_quantity_kilo_dalla"
                               readonly class="form-control fc-ro-pn">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">
                            Rate / Mann — فی من
                        </label>
                        <input type="number" name="dalla[pirce_per_mann]" id="pirce_per_mann"
                               class="form-control fc-pn" placeholder="0"
                               value="{{ $prefillDalla?->price ?? '' }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">
                            Rate / KG — فی کلو
                        </label>
                        <input type="number" name="dalla[pirce_per_kg]" id="pirce_per_kg_dalla"
                               readonly class="form-control fc-ro-pn">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">
                            Sub Total — سب ٹوٹل
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-pre">PKR</span>
                            </div>
                            <input type="text" name="dalla[sub_total]" id="sub_total_dalla"
                                   readonly class="form-control font-weight-bold sub-total-dalla">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ======================================================
             THAILA
        ====================================================== --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header border-bottom py-3 ch-teal">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold text-c-teal">
                        <i class="fas fa-shopping-bag mr-2"></i>Thaila — تھیلا
                    </h6>
                    <button type="button" class="btn btn-sm btn-pn btn-act-confirm section-toggle"
                            data-target="#thailaBody">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" id="thailaBody">

                {{-- Header row --}}
                <div class="row mb-1 d-none d-md-flex">
                    <div class="col-md-1"><small class="pn-form-col-lbl">Size</small></div>
                    <div class="col-md-2"><small class="pn-form-col-lbl">Qty (تھیلا)</small></div>
                    <div class="col-md-2"><small class="pn-form-col-lbl">Total KG</small></div>
                    <div class="col-md-2"><small class="pn-form-col-lbl">Rate/Thaila</small></div>
                    <div class="col-md-2"><small class="pn-form-col-lbl">Rate/KG</small></div>
                    <div class="col-md-3"><small class="pn-form-col-lbl">Sub Total</small></div>
                </div>

                @foreach([5,10,30,35,40,50] as $size)
                @php $prefillThaila = $prefill?->items->where('type','thaila')->firstWhere('size',$size); @endphp
                <div class="row align-items-center mb-2 py-2 sale-form-row">
                    <div class="col-md-1 mb-2 mb-md-0">
                        <input type="text" name="thaila[{{ $size }}][kilo_{{ $size }}]"
                               id="quantity_{{ $size }}_kilo" value="{{ $size }}" readonly
                               class="form-control text-center font-weight-bold fc-tag-thaila">
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <input type="number" name="thaila[{{ $size }}][sold_quantity_kilo_{{ $size }}]"
                               id="sold_quantity_kilo_{{ $size }}"
                               class="form-control fc-pn" placeholder="0"
                               value="{{ $prefillThaila?->quantity ?? '' }}">
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <input type="text" name="thaila[{{ $size }}][sold_quantity_kilo]"
                               id="sold_quantity_{{ $size }}_kilo_thaila"
                               readonly class="form-control fc-ro-pn">
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <input type="number" name="thaila[{{ $size }}][pirce_per_thaila]"
                               id="pirce_per_{{ $size }}_killo_thaila"
                               class="form-control fc-pn" placeholder="0"
                               value="{{ $prefillThaila?->price ?? '' }}">
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <input type="number" name="thaila[{{ $size }}][pirce_per_kg]"
                               id="pirce_per_kg_{{ $size }}_killo_thaila"
                               readonly class="form-control fc-ro-pn">
                    </div>
                    <div class="col-md-3 mb-2 mb-md-0">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-pre">PKR</span>
                            </div>
                            <input type="text" name="thaila[{{ $size }}][sub_total]"
                                   id="sub_total_{{ $size }}_killo_thaila"
                                   readonly class="form-control font-weight-bold sub-total-thaila">
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
            <div class="card-header border-bottom py-3 ch-yellow">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold text-c-warn">
                        <i class="fas fa-box mr-2"></i>Package — پیکٹ
                    </h6>
                    <button type="button" class="btn btn-sm btn-pn btn-act-sale section-toggle"
                            data-target="#packageBody">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" id="packageBody">

                {{-- Header row --}}
                <div class="row mb-1 d-none d-md-flex">
                    <div class="col-md-1"><small class="pn-form-col-lbl">Size</small></div>
                    <div class="col-md-2"><small class="pn-form-col-lbl">Qty (بنڈل)</small></div>
                    <div class="col-md-2"><small class="pn-form-col-lbl">Bundle Type</small></div>
                    <div class="col-md-2"><small class="pn-form-col-lbl">Total KG</small></div>
                    <div class="col-md-2"><small class="pn-form-col-lbl">Rate/Bundle</small></div>
                    <div class="col-md-3"><small class="pn-form-col-lbl">Sub Total</small></div>
                </div>

                @foreach([250,300,400,500,600,700] as $gram)
                @php $prefillPackage = $prefill?->items->where('type','package')->firstWhere('size',$gram); @endphp
                <div class="row align-items-center mb-2 py-2 sale-form-row">
                    <div class="col-md-1 mb-2 mb-md-0">
                        <input type="text" name="package[{{ $gram }}][gram_{{ $gram }}]"
                               id="quantity_{{ $gram }}_gram" value="{{ $gram }}" readonly
                               class="form-control text-center font-weight-bold fc-tag-package">
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <input type="number" name="package[{{ $gram }}][sold_bundles_quantity_{{ $gram }}_gram]"
                               id="sold_bundles_quantity_{{ $gram }}_gram"
                               class="form-control fc-pn" placeholder="0"
                               value="{{ $prefillPackage?->quantity ?? '' }}">
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <select name="package[{{ $gram }}][bundle_type_{{ $gram }}_gram]"
                                id="bundle_type_{{ $gram }}_gram"
                                class="form-control fc-pn">
                            <option value="10">10 پیکٹ</option>
                            <option value="20">20 پیکٹ</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <input type="text" name="package[{{ $gram }}][total_kg_{{ $gram }}_gram]"
                               id="total_kg_{{ $gram }}_gram"
                               readonly class="form-control fc-ro-pn">
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <input type="number" name="package[{{ $gram }}][price_per_bundle]"
                               id="price_per_bundle_{{ $gram }}_gram"
                               class="form-control fc-pn" placeholder="0"
                               value="{{ $prefillPackage?->price ?? '' }}">
                    </div>
                    <div class="col-md-3 mb-2 mb-md-0">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-pre">PKR</span>
                            </div>
                            <input type="text" name="package[{{ $gram }}][sub_total]"
                                   id="sub_total_{{ $gram }}_gram"
                                   readonly class="form-control font-weight-bold sub-total-package">
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>

        {{-- ======================================================
             SHOP INFO PANEL (shown on shop select via AJAX)
        ====================================================== --}}
        <div id="shop-info-panel" class="card border-0 shadow-sm mb-3 d-none">
            <div class="card-header border-bottom py-2 ch-header-blue d-flex justify-content-between align-items-center">
                <h6 class="mb-0 font-weight-bold text-white">
                    <i class="fas fa-store mr-2"></i>
                    <span id="sip-shop-name">—</span>
                    <small class="ml-2 font-weight-normal" style="opacity:.8;">کھاتہ خلاصہ · Account Summary</small>
                </h6>
                <a id="sip-whatsapp-btn" href="#" target="_blank" rel="noopener"
                   class="btn btn-sm font-weight-bold" style="background:#25D366;color:#fff;min-width:110px;">
                    <i class="fab fa-whatsapp mr-1"></i> WhatsApp
                </a>
            </div>
            <div class="card-body py-3">
                <div class="row text-center mb-2">
                    <div class="col-4">
                        <div class="border rounded py-2 px-1">
                            <div class="small text-muted mb-1">کل فروخت<br><span class="text-uppercase" style="font-size:10px;">Total Sales</span></div>
                            <div class="font-weight-bold" id="sip-total">—</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded py-2 px-1" style="border-color:#28a745!important;">
                            <div class="small text-muted mb-1">وصول شدہ<br><span class="text-uppercase" style="font-size:10px;">Received</span></div>
                            <div class="font-weight-bold text-success" id="sip-received">—</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded py-2 px-1" style="border-color:#dc3545!important;">
                            <div class="small text-muted mb-1">باقی رقم<br><span class="text-uppercase" style="font-size:10px;">Pending</span></div>
                            <div class="font-weight-bold text-danger" id="sip-pending">—</div>
                        </div>
                    </div>
                </div>
                <div id="sip-orders-section" class="d-none mt-3">
                    <h6 class="font-weight-bold text-muted border-top pt-3 mb-2">
                        <i class="fas fa-clipboard-list mr-1"></i>
                        زیر التواء آرڈر · Unconverted Orders
                    </h6>
                    <div id="sip-orders-list"></div>
                </div>
            </div>
        </div>

        {{-- ======================================================
             SALE DETAILS (Shop / Date / Total / Remarks / Bill)
        ====================================================== --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header border-bottom py-3 ch-header-blue">
                <h6 class="mb-0 font-weight-bold text-white">
                    <i class="fas fa-store mr-2"></i>Sale Details / فروخت کی تفصیل
                </h6>
            </div>
            <div class="card-body py-4">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">
                            Shop / دکان <span class="text-danger">*</span>
                        </label>
                        <select name="shop_id" id="shop_id" class="form-control fc-pn select2" required>
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
                        <label class="pn-label text-uppercase font-weight-bold text-muted">
                            Sale Date / فروخت کی تاریخ <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="sale_date" id="date" class="form-control fc-pn"
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">
                            Grand Total (PKR) / کل رقم
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text font-weight-bold input-pre-total">PKR</span>
                            </div>
                            <input type="number" name="total_sales_amount" value="0" readonly
                                   id="total_sales_amount" class="form-control font-weight-bold input-grand-total">
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">
                            Received (PKR) / وصول شدہ
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text font-weight-bold input-pre-received">PKR</span>
                            </div>
                            <input type="number" name="received_amount" id="received_amount" value="0" min="0"
                                   class="form-control font-weight-bold input-received">
                        </div>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">
                            Remarks / ملاحظات
                        </label>
                        <textarea name="remarks" id="remarks" class="form-control fc-pn" rows="2"
                                  placeholder="Optional notes..."></textarea>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">
                            Upload Bill Image / بل تصویر اپلوڈ کریں
                        </label>
                        <input type="file" name="bill_image" id="bill_image"
                               class="form-control fc-pn" accept="image/*">
                    </div>
                </div>

                <div class="d-flex justify-content-end pt-2">
                    <a href="{{ route('admin.sales.index') }}"
                       class="btn btn-light px-4 mr-2 btn-modal-cancel">
                        Cancel / منسوخ
                    </a>
                    <button class="btn btn-primary px-5 btn-modal-save font-weight-bold" type="submit">
                        <i class="fas fa-save mr-2"></i> Create Sale / فروخت بنائیں
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

    /* ── SHOP INFO PANEL ── */
    var sipInfoUrl  = '{{ route("admin.shops.info", "__ID__") }}';
    var sipOrderUrl = '{{ route("admin.orders.show", "__ID__") }}';

    function sipFmt(n) {
        return 'PKR ' + Number(n).toLocaleString('en-PK', { maximumFractionDigits: 0 });
    }

    function loadShopInfo(shopId) {
        if (!shopId) {
            $('#shop-info-panel').addClass('d-none');
            return;
        }
        $.getJSON(sipInfoUrl.replace('__ID__', shopId), function (data) {
            $('#sip-shop-name').text(data.shop.name);
            $('#sip-total').text(sipFmt(data.financials.total_amount));
            $('#sip-received').text(sipFmt(data.financials.received_amount));
            $('#sip-pending').text(sipFmt(data.financials.pending_amount));

            // Build WhatsApp message (Urdu)
            var msg = 'دکان: ' + data.shop.name + '\n'
                    + 'کل فروخت: ' + sipFmt(data.financials.total_amount) + '\n'
                    + 'وصول شدہ: ' + sipFmt(data.financials.received_amount) + '\n'
                    + 'باقی رقم: ' + sipFmt(data.financials.pending_amount);
            var waPhone = '';
            if (data.shop.phone_number) {
                var digits = data.shop.phone_number.replace(/\D/g, '');
                if (digits.charAt(0) === '0') digits = '92' + digits.slice(1);
                else if (digits.indexOf('92') !== 0) digits = '92' + digits;
                waPhone = digits;
            }
            $('#sip-whatsapp-btn').attr('href',
                'https://wa.me/' + waPhone + '?text=' + encodeURIComponent(msg));

            // Orders list
            if (data.orders.length > 0) {
                var html = '';
                data.orders.forEach(function (o) {
                    var badge = o.status === 'pending' ? 'warning' : 'success';
                    var oUrl  = sipOrderUrl.replace('__ID__', o.id);
                    html += '<div class="d-flex justify-content-between align-items-center py-2 border-bottom">'
                          +   '<span>'
                          +     '<a href="' + oUrl + '" target="_blank" class="font-weight-bold">' + o.reference + '</a> '
                          +     '<span class="badge badge-' + badge + ' ml-1">' + o.status + '</span> '
                          +     '<small class="text-muted ml-2">' + o.created_at + '</small>'
                          +   '</span>'
                          +   '<small class="text-muted">' + o.items_count + ' item(s)</small>'
                          + '</div>';
                });
                $('#sip-orders-list').html(html);
                $('#sip-orders-section').removeClass('d-none');
            } else {
                $('#sip-orders-section').addClass('d-none');
            }

            $('#shop-info-panel').removeClass('d-none');
        });
    }

    $('#shop_id').on('change', function () { loadShopInfo($(this).val()); });

    // Pre-load if shop is already selected (prefill from order)
    if ($('#shop_id').val()) { loadShopInfo($('#shop_id').val()); }

    /* ── Run calculations on load (for prefilled values) ── */
    calcDalla();
    [5, 10, 30, 35, 40, 50].forEach(function (s) { calcThaila(s); });
    [250, 300, 400, 500, 600, 700].forEach(function (g) { calcPackage(g); });
});
</script>
@endsection
