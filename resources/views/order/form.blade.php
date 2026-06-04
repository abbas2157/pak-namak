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
    <style>
        * { box-sizing: border-box; }
        body { background: #f0f4f2; font-family: 'Segoe UI', system-ui, sans-serif; margin: 0; }

        /* ── Top bar ── */
        .top-bar {
            background: linear-gradient(135deg,#0a2e18,#1a5c35,#2d7a4f);
            color:#fff; padding:14px 16px;
            box-shadow: 0 3px 16px rgba(0,0,0,.28);
            position: sticky; top: 0; z-index: 100;
        }
        .top-bar .logo-circle {
            width:42px;height:42px;background:#fff;border-radius:50%;
            display:inline-flex;align-items:center;justify-content:center;
            flex-shrink:0;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,.2);
        }
        .top-bar .logo-circle img { width:36px;height:36px;object-fit:contain; }
        .top-bar .brand { font-weight:800;font-size:13px;line-height:1.2; }
        .top-bar .sub   { font-size:10px;opacity:.6;margin-top:2px; }
        .back-link { color:rgba(255,255,255,.75);font-size:13px;text-decoration:none;white-space:nowrap; }
        .back-link:hover { color:#fff; }

        /* ── Main container ── */
        .form-wrap { max-width:680px;margin:0 auto;padding:16px 12px 32px; }

        /* ── Section block ── */
        .section-block {
            background:#fff; border-radius:14px;
            box-shadow:0 2px 10px rgba(0,0,0,.07);
            margin-bottom:14px; overflow:hidden;
        }
        .section-title {
            display:flex;align-items:center;justify-content:space-between;
            padding:13px 16px; font-weight:700; font-size:14px; cursor:pointer;
            user-select:none;
        }
        .section-title .title-left { display:flex;align-items:center;gap:10px; }
        .section-title .title-icon {
            width:34px;height:34px;border-radius:10px;
            display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0;
        }
        .section-title .chevron { transition:transform .2s;font-size:12px; }
        .section-title.collapsed .chevron { transform:rotate(-90deg); }

        .st-dalla   { background:#eaf3ee;color:#1a5c35; }
        .st-dalla .title-icon { background:#d0e8d8;color:#1a5c35; }

        .st-thaila  { background:#e8f5e9;color:#2d7a4f; }
        .st-thaila .title-icon { background:#c3e6cb;color:#155724; }

        .st-package { background:#fffbf0;color:#856404; }
        .st-package .title-icon { background:#ffe69c;color:#856404; }

        .st-shop    { background:linear-gradient(135deg,#1a5c35,#0a2e18);color:#fff; }
        .st-shop .title-icon { background:rgba(255,255,255,.2);color:#fff; }

        .section-body { padding:12px 14px 16px; }

        /* ── Item card (one per product size) ── */
        .item-card {
            border:1px solid #e8eee9; border-radius:12px;
            padding:12px 14px; margin-bottom:10px;
            background:#fafcfa; transition:border-color .15s, box-shadow .15s;
        }
        .item-card:last-child { margin-bottom:0; }
        .item-card.has-value {
            border-color:#a8d4b8;
            box-shadow:0 2px 8px rgba(26,92,53,.08);
            background:#fff;
        }
        .item-card-header {
            display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;
        }
        .size-pill {
            display:inline-flex;align-items:center;gap:6px;
            font-weight:800;font-size:13px;
            padding:5px 12px;border-radius:20px;
        }
        .sp-dalla   { background:#d0e8d8;color:#1a5c35; }
        .sp-thaila  { background:#d4edda;color:#155724; }
        .sp-package { background:#fff3cd;color:#856404; }

        .subtotal-display {
            font-weight:800;font-size:14px;color:#1a5c35;
            background:#eaf3ee;padding:4px 10px;border-radius:20px;
            display:none; /* shown via JS when > 0 */
        }
        .subtotal-placeholder {
            font-size:11px;color:#c0ccc4;
        }

        /* ── Input pair ── */
        .input-pair { display:grid;grid-template-columns:1fr 1fr;gap:10px; }
        .input-group-label {
            font-size:10px;text-transform:uppercase;letter-spacing:.5px;
            font-weight:700;color:#9ca3af;margin-bottom:5px;
        }
        .form-input {
            width:100%;padding:10px 12px;
            border:1.5px solid #e0e8e3;border-radius:9px;
            font-size:15px;color:#2d3748;background:#fff;
            outline:none;transition:border-color .15s,box-shadow .15s;
            -moz-appearance:textfield;
        }
        .form-input::-webkit-inner-spin-button,
        .form-input::-webkit-outer-spin-button { -webkit-appearance:none; }
        .form-input:focus { border-color:#2d7a4f;box-shadow:0 0 0 3px rgba(45,122,79,.12); }
        .form-input.has-value { border-color:#a8d4b8;background:#fafffe; }

        /* ── PKR prefix group ── */
        .pkr-wrap { display:flex; }
        .pkr-pre {
            display:flex;align-items:center;
            padding:0 10px;
            background:#f0f4f2;border:1.5px solid #e0e8e3;border-right:0;
            border-radius:9px 0 0 9px;
            font-size:11px;font-weight:800;color:#6c757d;white-space:nowrap;
            flex-shrink:0;
        }
        .pkr-wrap .form-input {
            border-radius:0 9px 9px 0;
        }
        .kg-hint {
            font-size:11px;color:#9ca3af;margin-top:4px;text-align:center;
        }
        .kg-hint span { color:#1a5c35;font-weight:700; }

        /* ── Shop section ── */
        .bs-select {
            width:100%;padding:11px 12px;
            border:1.5px solid #e0e8e3;border-radius:9px;
            font-size:14px;color:#2d3748;background:#fff;outline:none;
        }
        .bs-select:focus { border-color:#2d7a4f;box-shadow:0 0 0 3px rgba(45,122,79,.12); }
        .form-label-sm {
            font-size:11px;text-transform:uppercase;letter-spacing:.5px;
            font-weight:700;color:#6c757d;margin-bottom:6px;display:block;
        }
        .check-label { font-size:13px;color:#6c757d;margin-left:8px; }

        /* ── Select2 override ── */
        .select2-container--default .select2-selection--single {
            height:46px;border-radius:9px;border:1.5px solid #e0e8e3;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height:44px;padding-left:14px;font-size:14px;color:#2d3748;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height:44px; }
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color:#2d7a4f;box-shadow:0 0 0 3px rgba(45,122,79,.12);
        }

        /* ── Grand total bar ── */
        .grand-bar {
            background:linear-gradient(135deg,#1a5c35,#0a2e18);
            border-radius:14px;padding:16px 20px;
            display:flex;align-items:center;justify-content:space-between;
            margin-bottom:14px;
        }
        .grand-bar-label { color:rgba(255,255,255,.7);font-size:12px;margin-bottom:2px; }
        .grand-bar-value { color:#fff;font-size:24px;font-weight:800;letter-spacing:-.5px; }
        .grand-bar-note  { color:rgba(255,255,255,.4);font-size:10px; }
        .grand-bar-icon  {
            width:48px;height:48px;background:rgba(255,255,255,.1);border-radius:12px;
            display:flex;align-items:center;justify-content:center;font-size:20px;color:rgba(255,255,255,.6);
        }

        /* ── Submit ── */
        .submit-btn {
            width:100%;padding:15px;font-size:16px;font-weight:800;
            background:linear-gradient(135deg,#2d7a4f,#1a5c35);
            color:#fff;border:none;border-radius:12px;
            box-shadow:0 4px 18px rgba(26,92,53,.4);
            cursor:pointer;letter-spacing:.2px;
        }
        .submit-btn:active { opacity:.9;transform:scale(.99); }

        /* ── Textarea ── */
        .form-textarea {
            width:100%;padding:11px 12px;
            border:1.5px solid #e0e8e3;border-radius:9px;
            font-size:14px;color:#2d3748;resize:none;outline:none;
        }
        .form-textarea:focus { border-color:#2d7a4f;box-shadow:0 0 0 3px rgba(45,122,79,.12); }

        /* ── Unlisted fields ── */
        .unlisted-wrap { display:none;margin-top:12px; }
        .field-group { margin-bottom:12px; }
        .field-group:last-child { margin-bottom:0; }

        /* ── Alert ── */
        .form-alert {
            background:#fff0f0;border:1px solid #fca5a5;border-radius:10px;
            padding:12px 14px;font-size:13px;color:#b91c1c;margin-bottom:14px;
        }

        /* ── Footer ── */
        .page-footer { text-align:center;padding:8px 0 4px;font-size:11px;color:#9ca3af; }
    </style>
</head>
<body>

{{-- Sticky top bar --}}
<div class="top-bar">
    <div style="max-width:680px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;gap:12px;">
        <div style="display:flex;align-items:center;gap:10px;min-width:0;">
            <div class="logo-circle">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
            </div>
            <div style="min-width:0;">
                <div class="brand" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ config('admin.shop_name') }}</div>
                <div class="sub"><i class="fas fa-file-alt" style="margin-right:3px;"></i>آرڈر فارم — Order Form</div>
            </div>
        </div>
        <a href="{{ url('/') }}" class="back-link"><i class="fas fa-arrow-left" style="margin-right:4px;"></i>Back</a>
    </div>
</div>

<div class="form-wrap">

    @if($errors->any())
    <div class="form-alert">
        <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i>{{ $errors->first() }}
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
                        <div style="font-size:11px;opacity:.65;font-weight:400;margin-top:1px;">Select or add your shop details</div>
                    </div>
                </div>
                <i class="fas fa-chevron-down chevron" style="color:rgba(255,255,255,.6);"></i>
            </div>
            <div id="shopBody" class="section-body">

                <div id="shopSelectWrap">
                    <label class="form-label-sm">Select Your Shop <span style="color:#e74a3b;">*</span></label>
                    <select name="shop_id" id="shop_id">
                        <option value="">— Search your shop —</option>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}" {{ old('shop_id') == $shop->id ? 'selected' : '' }}>
                                {{ $shop->name }}{{ $shop->city ? ' — '.$shop->city : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-top:12px;">
                    <label style="display:flex;align-items:center;cursor:pointer;gap:0;">
                        <input type="checkbox" id="unlistedCheck" name="unlisted" value="1"
                               style="width:16px;height:16px;accent-color:#1a5c35;cursor:pointer;"
                               {{ old('unlisted') ? 'checked' : '' }}>
                        <span class="check-label">My shop is not listed</span>
                    </label>
                </div>

                <div class="unlisted-wrap" id="unlistedFields"
                     style="{{ old('unlisted') ? 'display:block;' : '' }}">
                    <div style="height:1px;background:#f0f0f0;margin:12px 0;"></div>
                    <div class="field-group">
                        <label class="form-label-sm">Shop / Name <span style="color:#e74a3b;">*</span></label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                               class="form-input" placeholder="Your shop or full name">
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <div class="field-group">
                            <label class="form-label-sm">Phone <span style="color:#e74a3b;">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                   class="form-input" placeholder="03xx-xxxxxxx">
                        </div>
                        <div class="field-group">
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
                        <div style="font-size:11px;opacity:.65;font-weight:400;margin-top:1px;">Bulk salt · 1 Mann = 40 KG</div>
                    </div>
                </div>
                <i class="fas fa-chevron-down chevron"></i>
            </div>
            <div id="dallaBody" class="section-body">

                <div class="item-card" id="dalla-card">
                    <div class="item-card-header">
                        <span class="size-pill sp-dalla"><i class="fas fa-weight" style="font-size:11px;"></i> Bulk — ڈلہ</span>
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
                        <div style="font-size:11px;opacity:.65;font-weight:400;margin-top:1px;">Bagged salt — enter qty &amp; rate per bag</div>
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
                        <div style="font-size:11px;opacity:.65;font-weight:400;margin-top:1px;">Retail packs — enter qty &amp; rate per bundle</div>
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
            <i class="fas fa-paper-plane" style="margin-right:8px;"></i>Submit Order — آرڈر بھیجیں
        </button>

    </form>

    <div class="page-footer">
        <i class="fas fa-phone" style="margin-right:4px;"></i>{{ config('admin.pak_namak.phone') }}
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
    function updateCard(cardId, qty, sub) {
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
        updateCard('dalla-card', qty, sub);
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
        updateCard('thaila-card-' + size, qty, sub);
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
        updateCard('package-card-' + gram, qty, sub);
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
