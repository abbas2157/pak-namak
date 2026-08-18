@extends('admin.layout.app')
@section('title', 'Edit Spice Sale')

@php
$sizeLabel = fn ($gram) => $gram >= 1000 ? (($gram / 1000) . 'kg') : ($gram . 'g');
$itemsByKey = $spiceSale->items->keyBy(fn ($i) => $i->spice_type_id . ':' . $i->packet_gram);
@endphp

@section('content')
<style>
    .stock-hint { font-size: .68rem; line-height: 1.3; margin-top: 4px; white-space: nowrap; }
</style>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Spice Sale <small class="text-muted pn-stat-sub">مصالحہ فروخت ترمیم</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.spice-sales.index') }}">Spice Sales</a></li>
                    <li class="breadcrumb-item active">Edit Sale #{{ $spiceSale->id }}</li>
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

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible card-pn">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('admin.spice-sales.update', $spiceSale->id) }}" method="POST" id="saleForm">
            @csrf
            @method('PUT')

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
                @php $existing = $itemsByKey->get($spiceType->id . ':' . $gram); @endphp
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
                               value="{{ $existing?->quantity ?? '' }}">
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <input type="text" id="totalkg_{{ $spiceType->id }}_{{ $gram }}"
                               readonly class="form-control fc-ro-pn">
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <input type="number" name="package[{{ $spiceType->id }}][{{ $gram }}][rate_per_kg]"
                               id="rate_{{ $spiceType->id }}_{{ $gram }}"
                               class="form-control fc-pn spice-qty" placeholder="0"
                               data-spice="{{ $spiceType->id }}" data-gram="{{ $gram }}"
                               value="{{ $existing?->price_per_kg ?? '' }}">
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
                                @php $shopArea = $shop->area?->name ?? $shop->city; @endphp
                                <option value="{{ $shop->id }}" {{ $spiceSale->shop_id == $shop->id ? 'selected' : '' }}>
                                    {{ $shop->name }}{{ $shopArea ? ' — ' . $shopArea : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">
                            Sale Date / فروخت کی تاریخ <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="sale_date" id="date" class="form-control fc-pn"
                               value="{{ \Carbon\Carbon::parse($spiceSale->sale_date)->format('Y-m-d') }}" required>
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
                            <input type="text" value="{{ number_format($spiceSale->received_amount, 0) }}" readonly
                                   class="form-control font-weight-bold input-received">
                        </div>
                        <small class="text-muted">Use "Record Payment" on the list to change this.</small>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="pn-label text-uppercase font-weight-bold text-muted">
                            Remarks / ملاحظات
                        </label>
                        <textarea name="remarks" id="remarks" class="form-control fc-pn" rows="2"
                                  placeholder="Optional notes...">{{ $spiceSale->remarks }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end pt-2">
                    <a href="{{ route('admin.spice-sales.index') }}"
                       class="btn btn-light px-4 mr-2 btn-modal-cancel">
                        Cancel / منسوخ
                    </a>
                    <button class="btn btn-primary px-5 btn-modal-save font-weight-bold" type="submit">
                        <i class="fas fa-save mr-2"></i> Update Sale / فروخت اپ ڈیٹ
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
    }

    $('.select2').select2({ placeholder: 'Select a shop', allowClear: true });
    $('.select2').next('.select2-container').find('.select2-selection').css({ height: '39px' });

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
