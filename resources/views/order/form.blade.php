<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Place Order — {{ config('admin.shop_name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('css/pn-public.css') }}">
</head>
<body class="pn-form-body">

{{-- Sticky top bar --}}
<div class="top-bar">
    <div class="top-bar-inner">
        <div class="top-bar-left">
            <div class="logo-circle">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
            </div>
            <div>
                <div class="top-brand">{{ config('admin.shop_name') }}</div>
                <div class="top-sub"><i class="fas fa-file-alt me-1"></i>آرڈر فارم — Order Form</div>
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

    <form action="{{ route('order.store') }}" method="POST" id="orderForm">
        @csrf

        {{-- ══════════════════════════════════════════
             SHOP SELECTION
        ══════════════════════════════════════════ --}}
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

        {{-- ══════════════════════════════════════════
             DALLA
        ══════════════════════════════════════════ --}}
        <div class="section-block">
            <div class="section-title st-dalla" onclick="toggleSection('dallaBody', this)">
                <div class="title-left">
                    <div class="title-icon"><i class="fas fa-tint"></i></div>
                    <div>
                        <div>Dalla — ڈلہ</div>
                        <div class="section-title-sub">Bulk salt · 1 Mann = 40 KG</div>
                    </div>
                </div>
                <i class="fas fa-chevron-down chevron"></i>
            </div>
            <div id="dallaBody" class="section-body">

                <div class="item-card" id="dalla-card">
                    <div class="item-card-header">
                        <span class="size-pill sp-dalla"><i class="fas fa-weight fa-icon-xs me-1"></i> Bulk — ڈلہ</span>
                        <div>
                            <span class="subtotal-display" id="dalla_sub_display"></span>
                            <span class="subtotal-placeholder" id="dalla_sub_placeholder">Enter qty &amp; rate</span>
                        </div>
                    </div>

                    <div class="input-pair">
                        <div>
                            <div class="input-group-label">Qty — من (Mann)</div>
                            <input type="number" name="dalla_qty" id="dalla_qty"
                                   value="{{ old('dalla_qty', 0) }}"
                                   class="form-input dalla-calc" min="0" step="0.5" placeholder="0">
                            <div class="kg-hint" id="dalla_kg_hint"></div>
                        </div>
                        <div>
                            <div class="input-group-label">Rate / Mann — فی من</div>
                            <div class="pkr-wrap">
                                <span class="pkr-pre">PKR</span>
                                <input type="number" name="dalla_price" id="dalla_price"
                                       value="{{ old('dalla_price', 0) }}"
                                       class="form-input dalla-calc" min="0" step="1" placeholder="0">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ══════════════════════════════════════════
             THAILA
        ══════════════════════════════════════════ --}}
        <div class="section-block">
            <div class="section-title st-thaila" onclick="toggleSection('thailaBody', this)">
                <div class="title-left">
                    <div class="title-icon"><i class="fas fa-shopping-bag"></i></div>
                    <div>
                        <div>Thaila — تھیلا</div>
                        <div class="section-title-sub">Bagged salt — enter qty &amp; rate per bag</div>
                    </div>
                </div>
                <i class="fas fa-chevron-down chevron"></i>
            </div>
            <div id="thailaBody" class="section-body">

                @foreach([5,10,30,35,40,50] as $size)
                <div class="item-card" id="thaila-card-{{ $size }}">
                    <div class="item-card-header">
                        <span class="size-pill sp-thaila">{{ $size }} KG bag</span>
                        <div>
                            <span class="subtotal-display" id="thaila_sub_display_{{ $size }}"></span>
                            <span class="subtotal-placeholder" id="thaila_sub_placeholder_{{ $size }}">—</span>
                        </div>
                    </div>
                    <div class="input-pair">
                        <div>
                            <div class="input-group-label">Qty — تھیلے (Bags)</div>
                            <input type="number" name="thaila[{{ $size }}][qty]"
                                   value="{{ old('thaila.'.$size.'.qty', 0) }}"
                                   class="form-input thaila-calc" min="0" step="1" placeholder="0"
                                   data-size="{{ $size }}">
                        </div>
                        <div>
                            <div class="input-group-label">Rate / Bag — فی تھیلا</div>
                            <div class="pkr-wrap">
                                <span class="pkr-pre">PKR</span>
                                <input type="number" name="thaila[{{ $size }}][price]"
                                       value="{{ old('thaila.'.$size.'.price', 0) }}"
                                       class="form-input thaila-calc" min="0" step="1" placeholder="0"
                                       data-size="{{ $size }}">
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>

        {{-- ══════════════════════════════════════════
             PACKAGE
        ══════════════════════════════════════════ --}}
        <div class="section-block">
            <div class="section-title st-package" onclick="toggleSection('packageBody', this)">
                <div class="title-left">
                    <div class="title-icon"><i class="fas fa-box"></i></div>
                    <div>
                        <div>Package — پیکٹ</div>
                        <div class="section-title-sub">Retail packs — enter qty &amp; rate per bundle</div>
                    </div>
                </div>
                <i class="fas fa-chevron-down chevron"></i>
            </div>
            <div id="packageBody" class="section-body">

                @foreach([250,300,400,500,600,700] as $gram)
                <div class="item-card" id="package-card-{{ $gram }}">
                    <div class="item-card-header">
                        <span class="size-pill sp-package">{{ $gram }}g pack</span>
                        <div>
                            <span class="subtotal-display" id="package_sub_display_{{ $gram }}"></span>
                            <span class="subtotal-placeholder" id="package_sub_placeholder_{{ $gram }}">—</span>
                        </div>
                    </div>
                    <div class="input-pair">
                        <div>
                            <div class="input-group-label">Qty — بنڈل (Bundles)</div>
                            <input type="number" name="package[{{ $gram }}][qty]"
                                   value="{{ old('package.'.$gram.'.qty', 0) }}"
                                   class="form-input package-calc" min="0" step="1" placeholder="0"
                                   data-gram="{{ $gram }}">
                        </div>
                        <div>
                            <div class="input-group-label">Rate / Bundle — فی بنڈل</div>
                            <div class="pkr-wrap">
                                <span class="pkr-pre">PKR</span>
                                <input type="number" name="package[{{ $gram }}][price]"
                                       value="{{ old('package.'.$gram.'.price', 0) }}"
                                       class="form-input package-calc" min="0" step="1" placeholder="0"
                                       data-gram="{{ $gram }}">
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>

        {{-- ══════════════════════════════════════════
             GRAND TOTAL
        ══════════════════════════════════════════ --}}
        <div class="grand-bar">
            <div>
                <div class="grand-bar-label">Grand Total — کل رقم</div>
                <div class="grand-bar-value">PKR <span id="grand_total">0</span></div>
                <div class="grand-bar-note">Auto-calculated from qty &amp; rates above</div>
            </div>
            <div class="grand-bar-icon"><i class="fas fa-calculator"></i></div>
        </div>

        {{-- ══════════════════════════════════════════
             REMARKS + SUBMIT
        ══════════════════════════════════════════ --}}
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

// ── Section toggle ─────────────────────────────────────
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

    // ── Select2 ────────────────────────────────────────
    $('#shop_id').select2({ placeholder: '— Search your shop —', allowClear: true, width: '100%' });

    // ── Unlisted toggle ────────────────────────────────
    $('#unlistedCheck').on('change', function () {
        if (this.checked) {
            $('#unlistedFields').slideDown(200);
            $('#shopSelectWrap').slideUp(200);
            $('#shop_id').val('').trigger('change');
        } else {
            $('#unlistedFields').slideUp(200);
            $('#shopSelectWrap').slideDown(200);
        }
    });

    // ── Format number ──────────────────────────────────
    function fmt(n) {
        return n.toLocaleString('en-PK', { maximumFractionDigits: 0 });
    }

    // ── Update item card state ─────────────────────────
    function updateCard(cardId, qty) {
        const card = document.getElementById(cardId);
        if (!card) return;
        card.classList.toggle('has-value', qty > 0);
    }

    // ── Show/hide subtotal display ─────────────────────
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

    // ── Dalla ──────────────────────────────────────────
    function calcDalla() {
        const qty   = parseFloat($('#dalla_qty').val())   || 0;
        const price = parseFloat($('#dalla_price').val()) || 0;
        const kg    = qty * 40;
        const sub   = qty * price;

        const hint = document.getElementById('dalla_kg_hint');
        hint.innerHTML = qty > 0 ? '= <span>' + kg + ' KG</span>' : '';

        setSubtotal('dalla_sub_display', 'dalla_sub_placeholder', sub);
        updateCard('dalla-card', qty);
        updateGrandTotal();
    }
    $('.dalla-calc').on('input', calcDalla);

    // ── Thaila ─────────────────────────────────────────
    function calcThaila(size) {
        const inputs = $('[data-size="' + size + '"]');
        const qty    = parseFloat(inputs.eq(0).val()) || 0;
        const price  = parseFloat(inputs.eq(1).val()) || 0;
        const sub    = qty * price;

        setSubtotal('thaila_sub_display_' + size, 'thaila_sub_placeholder_' + size, sub);
        updateCard('thaila-card-' + size, qty);
        updateGrandTotal();
    }
    $('.thaila-calc').on('input', function () { calcThaila($(this).data('size')); });

    // ── Package ────────────────────────────────────────
    function calcPackage(gram) {
        const inputs = $('[data-gram="' + gram + '"]');
        const qty    = parseFloat(inputs.eq(0).val()) || 0;
        const price  = parseFloat(inputs.eq(1).val()) || 0;
        const sub    = qty * price;

        setSubtotal('package_sub_display_' + gram, 'package_sub_placeholder_' + gram, sub);
        updateCard('package-card-' + gram, qty);
        updateGrandTotal();
    }
    $('.package-calc').on('input', function () { calcPackage($(this).data('gram')); });

    // ── Grand total ────────────────────────────────────
    function updateGrandTotal() {
        let total = 0;

        total += (parseFloat($('#dalla_qty').val()) || 0) * (parseFloat($('#dalla_price').val()) || 0);

        @foreach([5,10,30,35,40,50] as $size)
        (function(){ const i=$('[data-size="{{ $size }}"]'); total += (parseFloat(i.eq(0).val())||0)*(parseFloat(i.eq(1).val())||0); })();
        @endforeach

        @foreach([250,300,400,500,600,700] as $gram)
        (function(){ const i=$('[data-gram="{{ $gram }}"]'); total += (parseFloat(i.eq(0).val())||0)*(parseFloat(i.eq(1).val())||0); })();
        @endforeach

        document.getElementById('grand_total').textContent = total > 0 ? fmt(total) : '0';
    }

    // ── Init ───────────────────────────────────────────
    calcDalla();
    @foreach([5,10,30,35,40,50] as $size)
    calcThaila({{ $size }});
    @endforeach
    @foreach([250,300,400,500,600,700] as $gram)
    calcPackage({{ $gram }});
    @endforeach

});
</script>
</body>
</html>
