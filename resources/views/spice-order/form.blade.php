<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Place Spice Order — {{ config('admin.shop_name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('css/pn-public.css') }}">
</head>
<body class="pn-form-body">

<div class="top-bar">
    <div class="top-bar-inner">
        <div class="top-bar-left">
            <div class="logo-circle">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
            </div>
            <div>
                <div class="top-brand">{{ config('admin.shop_name') }}</div>
                <div class="top-sub"><i class="fas fa-pepper-hot me-1"></i>مصالحہ آرڈر فارم — Spice Order Form</div>
            </div>
        </div>
        <a href="{{ url('/') }}" class="back-link"><i class="fas fa-arrow-left me-1"></i>Back</a>
    </div>
</div>

<div class="form-wrap">

    @if($errors->any())
    <div class="form-alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
    </div>
    @endif

    <form action="{{ route('spice-order.store') }}" method="POST" id="orderForm">
        @csrf

        <div class="section-block">
            <div class="section-title st-shop" onclick="toggleSection('shopBody', this)">
                <div class="title-left">
                    <div class="title-icon"><i class="fas fa-store"></i></div>
                    <div>
                        <div>Your Shop — آپ کی دکان</div>
                        <div class="section-title-sub">Select or add your shop details</div>
                    </div>
                </div>
                <i class="fas fa-chevron-down chevron chevron-white"></i>
            </div>
            <div id="shopBody" class="section-body">

                <div id="shopSelectWrap">
                    <label class="form-label-sm">Select Your Shop <span class="shop-required">*</span></label>
                    <select name="shop_id" id="shop_id">
                        <option value="">— Search your shop —</option>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}" {{ old('shop_id') == $shop->id ? 'selected' : '' }}>
                                {{ $shop->name }}{{ $shop->city ? ' — '.$shop->city : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-3">
                    <label class="check-row">
                        <input type="checkbox" id="unlistedCheck" name="unlisted" value="1"
                               class="check-input"
                               {{ old('unlisted') ? 'checked' : '' }}>
                        <span class="check-label">My shop is not listed</span>
                    </label>
                </div>

                <div class="unlisted-wrap {{ old('unlisted') ? 'd-block' : '' }}" id="unlistedFields">
                    <div class="unlisted-divider"></div>
                    <div class="field-group-of">
                        <label class="form-label-sm">Shop / Name <span class="shop-required">*</span></label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                               class="form-input" placeholder="Your shop or full name">
                    </div>
                    <div class="unlisted-grid">
                        <div class="field-group-of">
                            <label class="form-label-sm">Phone <span class="shop-required">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                   class="form-input" placeholder="03xx-xxxxxxx">
                        </div>
                        <div class="field-group-of">
                            <label class="form-label-sm">City / Area</label>
                            <input type="text" name="city" value="{{ old('city') }}"
                                   class="form-input" placeholder="Lahore, Khewra…">
                        </div>
                    </div>
                </div>

            </div>
        </div>

        @foreach($spiceTypes as $spiceType)
        <div class="section-block">
            <div class="section-title st-package" onclick="toggleSection('spiceBody{{ $spiceType->id }}', this)">
                <div class="title-left">
                    <div class="title-icon"><i class="fas fa-pepper-hot"></i></div>
                    <div>
                        <div>{{ $spiceType->title }}</div>
                        <div class="section-title-sub">Enter qty &amp; rate per pack</div>
                    </div>
                </div>
                <i class="fas fa-chevron-down chevron"></i>
            </div>
            <div id="spiceBody{{ $spiceType->id }}" class="section-body">

                @foreach(config('admin.spice_sizes') as $gram)
                @php $sizeLabel = $gram >= 1000 ? (($gram / 1000) . 'kg') : ($gram . 'g'); @endphp
                <div class="item-card" id="spice-card-{{ $spiceType->id }}-{{ $gram }}">
                    <div class="item-card-header">
                        <span class="size-pill sp-package">{{ $sizeLabel }} pack</span>
                        <small class="text-muted ms-2">In stock: {{ number_format($stockLevels[$spiceType->id.':'.$gram]['quantity'] ?? 0, 0) }} packets</small>
                        <div>
                            <span class="subtotal-display" id="sub_display_{{ $spiceType->id }}_{{ $gram }}"></span>
                            <span class="subtotal-placeholder" id="sub_placeholder_{{ $spiceType->id }}_{{ $gram }}">—</span>
                        </div>
                    </div>
                    <div class="input-pair">
                        <div>
                            <div class="input-group-label">Qty — پیکٹ (Packets)</div>
                            <input type="number" name="package[{{ $spiceType->id }}][{{ $gram }}][qty]"
                                   value="{{ old('package.'.$spiceType->id.'.'.$gram.'.qty', 0) }}"
                                   class="form-input spice-calc" min="0" step="1" placeholder="0"
                                   data-spice="{{ $spiceType->id }}" data-gram="{{ $gram }}">
                        </div>
                        <div>
                            <div class="input-group-label">Rate / Packet — فی پیکٹ</div>
                            <div class="pkr-wrap">
                                <span class="pkr-pre">PKR</span>
                                <input type="number" name="package[{{ $spiceType->id }}][{{ $gram }}][price]"
                                       value="{{ old('package.'.$spiceType->id.'.'.$gram.'.price', 0) }}"
                                       class="form-input spice-calc" min="0" step="1" placeholder="0"
                                       data-spice="{{ $spiceType->id }}" data-gram="{{ $gram }}">
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
        @endforeach

        <div class="grand-bar">
            <div>
                <div class="grand-bar-label">Grand Total — کل رقم</div>
                <div class="grand-bar-value">PKR <span id="grand_total">0</span></div>
                <div class="grand-bar-note">Auto-calculated from qty &amp; rates above</div>
            </div>
            <div class="grand-bar-icon"><i class="fas fa-calculator"></i></div>
        </div>

        <div class="section-block">
            <div class="section-body">
                <label class="form-label-sm">Remarks / Notes (Optional)</label>
                <textarea name="remarks" class="form-textarea" rows="2"
                          placeholder="Any special instructions…">{{ old('remarks') }}</textarea>
            </div>
        </div>

        <button type="submit" class="submit-btn">
            <i class="fas fa-paper-plane me-2"></i>Submit Order — آرڈر بھیجیں
        </button>

    </form>

    <div class="page-footer">
        <i class="fas fa-phone me-1"></i>{{ config('admin.pak_namak.phone') }}
        &nbsp;·&nbsp; {{ config('admin.pak_namak.website') }}
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>

function toggleSection(id, titleEl) {
    const body = document.getElementById(id);
    const chevron = titleEl.querySelector('.chevron');
    if (body.style.display === 'none') {
        body.style.display = '';
        chevron.style.transform = '';
    } else {
        body.style.display = 'none';
        chevron.style.transform = 'rotate(-90deg)';
    }
}

$(function () {

    $('#shop_id').select2({ placeholder: '— Search your shop —', allowClear: true, width: '100%' });

    $('#unlistedCheck').on('change', function () {
        if (this.checked) {
            $('#unlistedFields').slideDown(200);
            $('#shopSelectWrap').slideUp(200);
            $('#shop_id').val('').trigger('change.select2');
        } else {
            $('#unlistedFields').slideUp(200);
            $('#shopSelectWrap').slideDown(200);
        }
    });

    function fmt(n) {
        return n.toLocaleString('en-PK', { maximumFractionDigits: 0 });
    }

    function updateCard(cardId, qty) {
        const card = document.getElementById(cardId);
        if (!card) return;
        card.classList.toggle('has-value', qty > 0);
    }

    function setSubtotal(displayId, placeholderId, sub) {
        const disp = document.getElementById(displayId);
        const ph   = document.getElementById(placeholderId);
        if (sub > 0) {
            disp.textContent = 'PKR ' + fmt(sub);
            disp.style.display = 'inline';
            ph.style.display = 'none';
        } else {
            disp.style.display = 'none';
            ph.style.display = 'inline';
        }
    }

    function calcLine(spiceId, gram) {
        const inputs = $('[data-spice="' + spiceId + '"][data-gram="' + gram + '"]');
        const qty    = parseFloat(inputs.eq(0).val()) || 0;
        const price  = parseFloat(inputs.eq(1).val()) || 0;
        const sub    = qty * price;

        setSubtotal('sub_display_' + spiceId + '_' + gram, 'sub_placeholder_' + spiceId + '_' + gram, sub);
        updateCard('spice-card-' + spiceId + '-' + gram, qty);
        updateGrandTotal();
    }
    $('.spice-calc').on('input', function () { calcLine($(this).data('spice'), $(this).data('gram')); });

    const SPICE_IDS = @json($spiceTypes->pluck('id'));
    const SPICE_GRAMS = @json(config('admin.spice_sizes'));

    function updateGrandTotal() {
        let total = 0;
        SPICE_IDS.forEach(function (spiceId) {
            SPICE_GRAMS.forEach(function (gram) {
                const i = $('[data-spice="' + spiceId + '"][data-gram="' + gram + '"]');
                total += (parseFloat(i.eq(0).val()) || 0) * (parseFloat(i.eq(1).val()) || 0);
            });
        });
        document.getElementById('grand_total').textContent = total > 0 ? fmt(total) : '0';
    }

    SPICE_IDS.forEach(function (spiceId) {
        SPICE_GRAMS.forEach(function (gram) { calcLine(spiceId, gram); });
    });

});
</script>
</body>
</html>
