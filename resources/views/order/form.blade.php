<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Place Order — {{ config('admin.shop_name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <style>
        body { background: #f4f6fc; font-family: 'Segoe UI', sans-serif; }
        .top-bar {
            background: linear-gradient(135deg,#1a1f5e,#2d3494);
            color: #fff;
            padding: 14px 0;
        }
        .top-bar .brand { font-weight: 800; font-size: 16px; }
        .top-bar .sub   { font-size: 12px; opacity: .65; }
        .section-card {
            border-radius: 14px;
            border: none;
            box-shadow: 0 2px 12px rgba(0,0,0,.06);
            margin-bottom: 18px;
        }
        .section-header {
            border-radius: 14px 14px 0 0;
            padding: 14px 20px;
            font-weight: 700;
            font-size: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header-dalla   { background:#f0f4ff; color:#4e73df; }
        .header-thaila  { background:#f0fff8; color:#1cc88a; }
        .header-package { background:#fffdf0; color:#856404; }
        .header-details { background:linear-gradient(135deg,#4e73df,#224abe); color:#fff; }
        .size-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            border-radius: 8px;
            padding: 6px 10px;
            min-width: 52px;
        }
        .size-dalla   { background:#e8f0fe; color:#4e73df; }
        .size-thaila  { background:#d4edda; color:#155724; }
        .size-package { background:#fff3cd; color:#856404; }
        .select2-container--default .select2-selection--single {
            height: 42px; border-radius: 8px; border-color:#dee2e6;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px; padding-left: 12px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }
        .submit-btn {
            background: linear-gradient(135deg,#1cc88a,#17a673);
            border: none; border-radius: 12px;
            padding: 14px 40px;
            font-size: 16px; font-weight: 700; color: #fff;
            width: 100%;
            box-shadow: 0 4px 20px rgba(28,200,138,.35);
        }
        .submit-btn:hover { opacity: .92; color: #fff; }
        .back-link { color: rgba(255,255,255,.7); font-size: 13px; text-decoration: none; }
        .back-link:hover { color: #fff; }
    </style>
</head>
<body>

{{-- Top bar --}}
<div class="top-bar">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <div class="brand"><i class="fas fa-cubes me-2"></i>{{ config('admin.shop_name') }}</div>
                <div class="sub">آرڈر فارم — Order Form</div>
            </div>
            <a href="{{ url('/') }}" class="back-link">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>
</div>

<div class="container py-4" style="max-width:720px;">

    @if($errors->any())
    <div class="alert alert-danger rounded-3 mb-3">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ $errors->first() }}
    </div>
    @endif

    <form action="{{ route('order.store') }}" method="POST" id="orderForm">
        @csrf

        {{-- ── SHOP SELECTION ─────────────────────────────────── --}}
        <div class="card section-card">
            <div class="section-header header-details">
                <span><i class="fas fa-store me-2"></i>Your Shop — آپ کی دکان</span>
            </div>
            <div class="card-body p-4">

                <div id="shopSelectWrap">
                    <label class="form-label fw-bold text-muted" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;">
                        Select Your Shop <span class="text-danger">*</span>
                    </label>
                    <select name="shop_id" id="shop_id" class="form-select" style="border-radius:8px;">
                        <option value="">— Search your shop —</option>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}" {{ old('shop_id') == $shop->id ? 'selected' : '' }}>
                                {{ $shop->name }}{{ $shop->city ? ' — ' . $shop->city : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="unlistedCheck" name="unlisted" value="1"
                               {{ old('unlisted') ? 'checked' : '' }}>
                        <label class="form-check-label text-muted" for="unlistedCheck" style="font-size:13px;">
                            My shop is not listed
                        </label>
                    </div>
                </div>

                <div id="unlistedFields" style="display:{{ old('unlisted') ? 'block' : 'none' }};" class="mt-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;">
                                Shop / Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                                   class="form-control" style="border-radius:8px;" placeholder="Your shop or full name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;">
                                Phone <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                   class="form-control" style="border-radius:8px;" placeholder="03xx-xxxxxxx">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold text-muted" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;">
                                City / Area
                            </label>
                            <input type="text" name="city" value="{{ old('city') }}"
                                   class="form-control" style="border-radius:8px;" placeholder="e.g. Lahore, Khewra">
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── DALLA ──────────────────────────────────────────── --}}
        <div class="card section-card">
            <div class="section-header header-dalla">
                <span><i class="fas fa-tint me-2"></i>Dalla — ڈلہ <small class="ms-2 fw-normal" style="font-size:12px;">(Bulk salt in Mann · 1 Mann = 40 KG)</small></span>
                <button type="button" class="btn btn-sm toggle-btn" data-target="#dallaBody"
                        style="background:#e8f0fe;color:#4e73df;border:none;border-radius:6px;">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            <div id="dallaBody" class="card-body p-4">
                <div class="row align-items-center g-3">
                    <div class="col-6 col-md-4">
                        <label class="form-label fw-bold text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Qty (Mann)</label>
                        <div class="input-group">
                            <span class="input-group-text size-badge size-dalla" style="border-radius:8px 0 0 8px;">من</span>
                            <input type="number" name="dalla_qty" id="dalla_qty" value="{{ old('dalla_qty', 0) }}"
                                   class="form-control" style="border-radius:0 8px 8px 0;" min="0" step="0.5" placeholder="0">
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <label class="form-label fw-bold text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;">= KG</label>
                        <input type="text" id="dalla_kg_display" readonly
                               class="form-control" style="border-radius:8px;background:#f8f9fc;color:#4e73df;font-weight:700;" placeholder="0 KG">
                    </div>
                </div>
            </div>
        </div>

        {{-- ── THAILA ─────────────────────────────────────────── --}}
        <div class="card section-card">
            <div class="section-header header-thaila">
                <span><i class="fas fa-shopping-bag me-2"></i>Thaila — تھیلا <small class="ms-2 fw-normal" style="font-size:12px;">(Bagged salt)</small></span>
                <button type="button" class="btn btn-sm toggle-btn" data-target="#thailaBody"
                        style="background:#d4edda;color:#155724;border:none;border-radius:6px;">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            <div id="thailaBody" class="card-body p-4">
                <div class="row g-3">
                    @foreach([5,10,30,35,40,50] as $size)
                    <div class="col-6 col-md-4">
                        <label class="form-label fw-bold text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;">
                            {{ $size }} KG — Qty (bags)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text size-badge size-thaila" style="border-radius:8px 0 0 8px;font-size:12px;">{{ $size }}kg</span>
                            <input type="number" name="thaila[{{ $size }}]" value="{{ old('thaila.'.$size, 0) }}"
                                   class="form-control" style="border-radius:0 8px 8px 0;" min="0" step="1" placeholder="0">
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── PACKAGE ─────────────────────────────────────────── --}}
        <div class="card section-card">
            <div class="section-header header-package">
                <span><i class="fas fa-box me-2"></i>Package — پیکٹ <small class="ms-2 fw-normal" style="font-size:12px;">(Retail packs — bundles)</small></span>
                <button type="button" class="btn btn-sm toggle-btn" data-target="#packageBody"
                        style="background:#fff3cd;color:#856404;border:none;border-radius:6px;">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            <div id="packageBody" class="card-body p-4">
                <div class="row g-3">
                    @foreach([250,300,400,500,600,700] as $gram)
                    <div class="col-6 col-md-4">
                        <label class="form-label fw-bold text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;">
                            {{ $gram }}g — Bundles
                        </label>
                        <div class="input-group">
                            <span class="input-group-text size-badge size-package" style="border-radius:8px 0 0 8px;font-size:11px;">{{ $gram }}g</span>
                            <input type="number" name="package[{{ $gram }}]" value="{{ old('package.'.$gram, 0) }}"
                                   class="form-control" style="border-radius:0 8px 8px 0;" min="0" step="1" placeholder="0">
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── REMARKS & SUBMIT ─────────────────────────────── --}}
        <div class="card section-card">
            <div class="card-body p-4">
                <label class="form-label fw-bold text-muted" style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;">
                    Remarks / Notes (Optional)
                </label>
                <textarea name="remarks" class="form-control mb-4" rows="2"
                          style="border-radius:8px;" placeholder="Any special instructions...">{{ old('remarks') }}</textarea>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-paper-plane me-2"></i> Submit Order — آرڈر بھیجیں
                </button>
            </div>
        </div>

    </form>

    <p class="text-center text-muted mt-2" style="font-size:12px;">
        <i class="fas fa-phone me-1"></i>{{ config('admin.pak_namak.phone') }} &nbsp;·&nbsp; {{ config('admin.pak_namak.website') }}
    </p>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function () {

    // Select2 shop dropdown
    $('#shop_id').select2({ placeholder: '— Search your shop —', allowClear: true, width: '100%' });

    // Unlisted toggle
    $('#unlistedCheck').on('change', function () {
        if ($(this).is(':checked')) {
            $('#unlistedFields').slideDown(200);
            $('#shopSelectWrap').slideUp(200);
            $('#shop_id').val('').trigger('change');
        } else {
            $('#unlistedFields').slideUp(200);
            $('#shopSelectWrap').slideDown(200);
        }
    });

    // Section collapse toggles
    $('.toggle-btn').on('click', function () {
        const target = $($(this).data('target'));
        target.slideToggle(200);
        $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');
    });

    // Dalla KG display
    $('#dalla_qty').on('input', function () {
        const kg = (parseFloat($(this).val()) || 0) * 40;
        $('#dalla_kg_display').val(kg > 0 ? kg + ' KG' : '');
    }).trigger('input');

});
</script>
</body>
</html>
