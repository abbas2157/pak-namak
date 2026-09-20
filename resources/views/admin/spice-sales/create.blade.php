@extends('admin.layout.app')
@section('title', 'New Spice Sale')

@php
$sizeLabel = fn ($gram) => $gram >= 1000 ? (($gram / 1000) . 'kg') : ($gram . 'g');
@endphp

@section('content')
<style>
    .stock-hint { font-size: .68rem; line-height: 1.3; margin-top: 4px; white-space: nowrap; }
</style>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">New Spice Sale <small class="text-muted pn-stat-sub">نئی مصالحہ فروخت</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.spice-sales.index') }}">Spice Sales</a></li>
                    <li class="breadcrumb-item active">New Sale</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end">
                <a href="{{ route('admin.spice-sales.index') }}"
                   class="btn btn-light px-4 btn-modal-cancel">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Spice Sales
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
        <form action="{{ route('admin.spice-sales.store') }}" method="POST" id="saleForm" enctype="multipart/form-data">
            @csrf
            @if($prefill)
                <input type="hidden" name="spice_order_id" value="{{ $prefill->id }}">
            @endif

        @foreach($spiceTypes as $spiceType)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header border-bottom py-3 ch-yellow">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold text-c-warn">
                        <i class="fas fa-pepper-hot mr-2"></i>{{ $spiceType->title }}
                    </h6>
                    <button type="button" class="btn btn-sm btn-pn btn-act-sale section-toggle"
                            data-target="#spiceBody{{ $spiceType->id }}">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" id="spiceBody{{ $spiceType->id }}">

                <div class="row mb-1 d-none d-md-flex">
                    <div class="col-md-2"><small class="pn-form-col-lbl">Size</small></div>
                    <div class="col-md-3"><small class="pn-form-col-lbl">Qty (پیکٹ)</small></div>
                    <div class="col-md-2"><small class="pn-form-col-lbl">Total KG</small></div>
                    <div class="col-md-2"><small class="pn-form-col-lbl">Rate/KG</small></div>
                    <div class="col-md-3"><small class="pn-form-col-lbl">Sub Total</small></div>
                </div>

                @foreach(config('admin.spice_sizes') as $gram)
                @php $prefillItem = $prefill?->items->where('spice_type_id', $spiceType->id)->firstWhere('size', $gram); @endphp
                <div class="row align-items-center mb-2 py-2 sale-form-row">
                    <div class="col-md-2 mb-2 mb-md-0 text-center">
                        <input type="text" value="{{ $sizeLabel($gram) }}" readonly
                               class="form-control text-center font-weight-bold fc-tag-package">
                        <small class="text-muted d-block stock-hint">{{ number_format($stockLevels[$spiceType->id.':'.$gram]['quantity'] ?? 0, 0) }} in stock</small>
                    </div>
                    <div class="col-md-3 mb-2 mb-md-0">
                        <input type="number" name="package[{{ $spiceType->id }}][{{ $gram }}][qty]"
                               id="qty_{{ $spiceType->id }}_{{ $gram }}"
                               class="form-control fc-pn spice-qty" placeholder="0"
                               data-spice="{{ $spiceType->id }}" data-gram="{{ $gram }}"
                               value="{{ $prefillItem?->quantity ?? '' }}">
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <input type="text" id="totalkg_{{ $spiceType->id }}_{{ $gram }}"
                               readonly class="form-control fc-ro-pn">
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        @php
                            // Orders quote a per-packet price; convert to the
                            // per-KG rate this form now expects.
                            $prefillRate = $prefillItem?->price ? round($prefillItem->price / ($gram / 1000), 2) : '';
                        @endphp
                        <input type="number" name="package[{{ $spiceType->id }}][{{ $gram }}][rate_per_kg]"
                               id="rate_{{ $spiceType->id }}_{{ $gram }}"
                               class="form-control fc-pn spice-qty" placeholder="0"
                               data-spice="{{ $spiceType->id }}" data-gram="{{ $gram }}"
                               value="{{ $prefillRate }}">
                    </div>
                    <div class="col-md-3 mb-2 mb-md-0">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-pre">PKR</span>
                            </div>
                            <input type="text" id="subtotal_{{ $spiceType->id }}_{{ $gram }}"
                                   readonly class="form-control font-weight-bold sub-total-package">
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
        @endforeach

        {{-- ======================================================
             SHOP INFO PANEL (shown on shop select via AJAX) — same
             account summary the salt sale form shows; the shop's
             udhaar is one account, so totals are salt + spice with
             the split shown under Pending.
        ====================================================== --}}
        <div id="shop-info-panel" class="card border-0 shadow-sm mb-3 d-none">
            <div class="card-header border-bottom py-2 ch-header-blue d-flex justify-content-between align-items-center">
                <h6 class="mb-0 font-weight-bold text-white">
                    <i class="fas fa-store mr-2"></i>
                    <span id="sip-shop-name">—</span>
                    <small class="ml-2 font-weight-normal" style="opacity:.8;" id="sip-shop-location"></small>
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
                            <div class="small text-muted" id="sip-pending-split"></div>
                        </div>
                    </div>
                </div>
                <div id="sip-orders-section" class="d-none mt-3">
                    <h6 class="font-weight-bold text-muted border-top pt-3 mb-2">
                        <i class="fas fa-clipboard-list mr-1"></i>
                        زیر التواء آرڈر · Unconverted Spice Orders
                    </h6>
                    <div id="sip-orders-list"></div>
                </div>
            </div>
        </div>

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
                                    {{ $shop->name }}{{ $shop->location ? ' — ' . $shop->location : '' }}
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
                            <input type="number" value="0" readonly
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
                    <div class="col-md-2 mb-3">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">
                            Received Into / کہاں موصول ہوا
                        </label>
                        <select name="account_id" class="form-control fc-pn">
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ $account->type === 'cash' ? 'selected' : '' }}>{{ $account->label() }}</option>
                            @endforeach
                            <option value="">Other / Not from Cash &amp; Bank (کیش/بینک سے نہیں)</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">
                            Remarks / ملاحظات
                        </label>
                        <textarea name="remarks" id="remarks" class="form-control fc-pn" rows="2"
                                  placeholder="Optional notes..."></textarea>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">
                            Bill Image / بل تصویر
                        </label>
                        <input type="file" name="bill_image" class="form-control-file fc-pn"
                               accept="image/jpeg,image/png,image/webp">
                        <small class="text-muted">Optional — JPG/PNG/WebP, max 4MB.</small>
                    </div>
                </div>

                <div class="d-flex justify-content-end pt-2">
                    <a href="{{ route('admin.spice-sales.index') }}"
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

    $('.section-toggle').on('click', function () {
        const target = $($(this).data('target'));
        target.slideToggle(200);
        $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');
    });

    function num(v) { return parseFloat(v) || 0; }
    function round(v) { return Math.round(v * 100) / 100; }

    const SPICE_GRAMS = @json(config('admin.spice_sizes'));
    const SPICE_IDS = @json($spiceTypes->pluck('id'));

    function calcLine(spiceId, gram) {
        let qty  = num($('#qty_' + spiceId + '_' + gram).val());
        let rate = num($('#rate_' + spiceId + '_' + gram).val());
        let kg   = qty * (gram / 1000);
        let sub  = kg * rate;
        $('#totalkg_' + spiceId + '_' + gram).val(round(kg));
        $('#subtotal_' + spiceId + '_' + gram).val(round(sub));
        calcGrandTotal();
    }

    $('.spice-qty').on('input change', function () {
        calcLine($(this).data('spice'), $(this).data('gram'));
    });

    function calcGrandTotal() {
        let total = 0;
        $('input[id^="subtotal_"]').each(function () { total += num($(this).val()); });
        $('#total_sales_amount').val(round(total));
        let received = num($('#received_amount').val());
        if (received > total) { $('#received_amount').val(round(total)); }
    }

    $('#received_amount').on('input', function () {
        let total = num($('#total_sales_amount').val());
        if (num($(this).val()) > total) { $(this).val(round(total)); }
    });

    $('.select2').select2({ placeholder: 'Select a shop', allowClear: true });
    $('.select2').next('.select2-container').find('.select2-selection').css({ height: '39px' });

    /* ── SHOP INFO PANEL (account summary + pending, like the salt form) ── */
    var sipInfoUrl  = '{{ route("admin.shops.info", "__ID__") }}';
    var sipOrderUrl = '{{ route("admin.spice-orders.show", "__ID__") }}';

    function sipFmt(n) {
        return 'PKR ' + Number(n).toLocaleString('en-PK', { maximumFractionDigits: 0 });
    }

    function waBrandingFooter() {
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var d = new Date();
        var dateStr = String(d.getDate()).padStart(2, '0') + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        return '\n———————————\n'
             + '{{ config('admin.shop_name') }}\n'
             + '{{ config('admin.shop_name_urdu') }}\n'
             + '📞 {{ config('admin.pak_namak.phone') }}\n'
             + '📅 ' + dateStr;
    }

    function loadShopInfo(shopId) {
        if (!shopId) {
            $('#shop-info-panel').addClass('d-none');
            return;
        }
        $.getJSON(sipInfoUrl.replace('__ID__', shopId), function (data) {
            $('#sip-shop-name').text(data.shop.name);
            $('#sip-shop-location').html(data.shop.location ? '<i class="fas fa-map-marker-alt mr-1"></i>' + data.shop.location : '');
            $('#sip-total').text(sipFmt(data.financials.total_amount));
            $('#sip-received').text(sipFmt(data.financials.received_amount));
            $('#sip-pending').text(sipFmt(data.financials.pending_amount));
            $('#sip-pending-split').text(
                'Salt ' + sipFmt(data.financials.salt_pending) + ' · Spice ' + sipFmt(data.financials.spice_pending)
            );

            var msg = 'دکان: ' + data.shop.name + '\n'
                    + 'کل فروخت: ' + sipFmt(data.financials.total_amount) + '\n'
                    + 'وصول شدہ: ' + sipFmt(data.financials.received_amount) + '\n'
                    + 'باقی رقم: ' + sipFmt(data.financials.pending_amount) + '\n'
                    + '(نمک: ' + sipFmt(data.financials.salt_pending) + ' · مصالحہ: ' + sipFmt(data.financials.spice_pending) + ')'
                    + waBrandingFooter();
            var waPhone = '';
            if (data.shop.phone_number) {
                var digits = data.shop.phone_number.replace(/\D/g, '');
                if (digits.charAt(0) === '0') digits = '92' + digits.slice(1);
                else if (digits.indexOf('92') !== 0) digits = '92' + digits;
                waPhone = digits;
            }
            $('#sip-whatsapp-btn').attr('href',
                'https://wa.me/' + waPhone + '?text=' + encodeURIComponent(msg));

            var orders = data.spice_orders || [];
            if (orders.length > 0) {
                var html = '';
                orders.forEach(function (o) {
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
    if ($('#shop_id').val()) { loadShopInfo($('#shop_id').val()); }

    SPICE_IDS.forEach(function (spiceId) {
        SPICE_GRAMS.forEach(function (gram) { calcLine(spiceId, gram); });
    });

    const STOCK_LEVELS = @json($stockLevels);
    function stockQty(spiceId, gram) {
        const key = spiceId + ':' + gram;
        return STOCK_LEVELS[key] ? parseFloat(STOCK_LEVELS[key].quantity) : 0;
    }

    $('#saleForm').on('submit', function (e) {
        const shortages = [];

        SPICE_IDS.forEach(function (spiceId) {
            SPICE_GRAMS.forEach(function (gram) {
                const qty = parseFloat($('#qty_' + spiceId + '_' + gram).val()) || 0;
                if (qty > 0) {
                    const avail = stockQty(spiceId, gram);
                    if (qty > avail) shortages.push(gram + 'g pack — need ' + qty + ', only ' + avail + ' in stock');
                }
            });
        });

        if (shortages.length > 0) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'Not enough stock',
                html: shortages.map(function (s) { return '• ' + s; }).join('<br>') + '<br><br>Save this sale anyway?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Save anyway',
                cancelButtonText: 'Let me fix it',
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#6c757d',
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    });
});
</script>
@endsection
