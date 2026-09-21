@extends('admin.layout.app')
@section('title', 'Record Payment')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Record Payment <small class="text-muted ch-sub">ادائیگی درج کریں</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.shops.index') }}">Shops</a></li>
                    <li class="breadcrumb-item active">Record Payment</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card card-pn border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-c-blue2">
                            <i class="fas fa-hand-holding-dollar mr-2"></i>Pick a Shop &amp; Log a Payment
                        </h6>
                    </div>
                    <div class="card-body py-4 px-4">

                        <div id="rp_alert" class="alert alert-success border-0 shadow-sm d-none" role="alert"></div>

                        <div class="mb-4">
                            <label class="filter-lbl">Shop / دکان <span class="text-danger">*</span></label>
                            <select id="rp_shop_id" class="form-control fc-pn select2" style="width:100%;">
                                <option value="">— Search for a shop —</option>
                                @foreach($shops as $shop)
                                    <option value="{{ $shop->id }}"
                                            data-pending="{{ $shop->combined_pending_amount }}"
                                            data-salt-pending="{{ $shop->salt_pending }}"
                                            data-spice-pending="{{ $shop->spice_pending }}">
                                        {{ $shop->name }}{{ $shop->location ? ' — '.$shop->location : '' }}
                                        @if($shop->combined_pending_amount > 0)
                                            (Pending: {{ number_format($shop->combined_pending_amount, 0) }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="rp_pending_box" class="d-none mb-4 p-3 rounded-3" style="background:#fff5f5;border:1.5px solid #f8d7da;">
                            <span class="text-muted">Total Pending for </span>
                            <strong id="rp_shop_name"></strong>
                            <span class="font-weight-bold text-c-red float-right" id="rp_pending_display"></span>
                            <div class="clearfix"></div>
                            <small class="text-muted d-block mt-2" id="rp_pending_breakdown"></small>
                            <small class="text-muted d-block mt-1">
                                Payment is applied to the oldest unpaid sales first, across both salt and spices.
                            </small>
                        </div>

                        <form id="recordPaymentForm">
                            @csrf
                            <fieldset id="rp_fieldset" disabled>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="filter-lbl">Pay Against / ادائیگی کس کی <span class="text-danger">*</span></label>
                                        <select name="product_line" id="rp_product_line" class="form-control fc-pn" required></select>
                                        <small class="text-muted">Which pending balance this money is for.</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="filter-lbl">Amount <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0.01" name="amount" id="rp_amount" class="form-control fc-pn" placeholder="0" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="filter-lbl">Payment Date <span class="text-danger">*</span></label>
                                        <input type="date" name="payment_date" id="rp_payment_date" class="form-control fc-pn" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="filter-lbl">Received Into</label>
                                        <select name="account_id" class="form-control fc-pn">
                                            @foreach($accounts as $account)
                                                <option value="{{ $account->id }}" {{ $account->type === 'cash' ? 'selected' : '' }}>{{ $account->label() }}</option>
                                            @endforeach
                                            <option value="">Other / Not from Cash &amp; Bank (کیش/بینک سے نہیں)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="filter-lbl">Note / نوٹ</label>
                                        <input type="text" name="note" class="form-control fc-pn" placeholder="Optional">
                                    </div>
                                </div>
                                <p class="text-muted small mb-3">Applied to the oldest pending sales of the selected line first.</p>
                                <button class="btn btn-primary btn-pn px-4" type="submit" id="rpSubmitBtn">
                                    <i class="fas fa-save mr-1"></i> Record Payment
                                </button>
                            </fieldset>
                        </form>

                    </div>
                </div>

                {{-- Payment history for the selected shop (salt + spice, newest first) --}}
                <div id="rp_history" class="card card-pn border-0 shadow-sm mt-3 d-none">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 font-weight-bold text-c-teal">
                            <i class="fas fa-history mr-2"></i>Payment History / ادائیگی کی تاریخ
                            <small class="text-muted font-weight-normal ml-1" id="rp_history_shop"></small>
                        </h6>
                        <span class="badge pn-bdg pn-bdg-blue" id="rp_history_count"></span>
                    </div>
                    <div class="px-4 py-2 tbl-toolbar-top small text-muted" id="rp_history_totals"></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm pn-table pn-table-font mb-0">
                                <thead>
                                    <tr>
                                        <th class="pl-3">Date / تاریخ</th>
                                        <th>Line</th>
                                        <th class="text-right">Amount</th>
                                        <th>Received Into</th>
                                        <th>Against Sale</th>
                                        <th class="pr-3">Note</th>
                                    </tr>
                                </thead>
                                <tbody id="rp_history_rows"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function () {
    $('.select2').select2({ placeholder: '— Search for a shop —', allowClear: true, width: '100%' });
    $('#rp_payment_date').val(new Date().toISOString().split('T')[0]);

    // Payment history for the selected shop
    const fmtMoney = n => 'PKR ' + Number(n).toLocaleString('en-PK', { maximumFractionDigits: 0 });
    const esc = s => $('<div>').text(s == null ? '' : String(s)).html();

    function loadHistory(shopId, shopName) {
        if (!shopId) { $('#rp_history').addClass('d-none'); return; }
        $.getJSON(APP_URL + '/shops/' + shopId + '/payments', function (res) {
            $('#rp_history_shop').text('— ' + shopName);
            $('#rp_history_count').text(res.count + ' payment' + (res.count === 1 ? '' : 's'));
            $('#rp_history_totals').html(
                res.count
                    ? 'Received so far: <strong>' + fmtMoney(res.total) + '</strong>'
                      + ' <span class="ml-2">Salt ' + fmtMoney(res.salt_total) + '</span>'
                      + ' <span class="ml-2">Spice ' + fmtMoney(res.spice_total) + '</span>'
                      + (res.count > res.payments.length ? ' <span class="ml-2">(latest ' + res.payments.length + ' shown)</span>' : '')
                    : 'No payments recorded for this shop yet.'
            );
            const rows = res.payments.map(p =>
                '<tr>'
                + '<td class="pl-3 font-weight-bold">' + esc(p.date_label) + '</td>'
                + '<td><span class="badge pn-bdg ' + (p.line === 'Salt' ? 'badge-info' : 'badge-warning') + '">' + esc(p.line) + '</span></td>'
                + '<td class="text-right font-weight-bold text-c-teal">' + Number(p.amount).toLocaleString() + '</td>'
                + '<td>' + esc(p.account) + '</td>'
                + '<td><small class="text-muted">#' + esc(p.sale_id) + ' · ' + esc(p.sale_date) + ' · ' + Number(p.sale_total).toLocaleString() + '</small></td>'
                + '<td class="pr-3"><small class="text-muted">' + esc(p.note || '—') + '</small></td>'
                + '</tr>'
            ).join('');
            $('#rp_history_rows').html(rows || '<tr><td colspan="6" class="text-center text-muted py-3">No payments yet.</td></tr>');
            $('#rp_history').removeClass('d-none');
        });
    }

    $('#rp_shop_id').on('change', function () {
        const opt = $(this).find('option:selected');
        const pending = parseFloat(opt.data('pending')) || 0;

        if (!$(this).val()) {
            $('#rp_pending_box').addClass('d-none');
            $('#rp_fieldset').prop('disabled', true);
            $('#rp_history').addClass('d-none');
            return;
        }

        loadHistory($(this).val(), opt.text().split(' (Pending:')[0].trim());

        const saltPending  = parseFloat(opt.data('salt-pending')) || 0;
        const spicePending = parseFloat(opt.data('spice-pending')) || 0;

        $('#rp_shop_name').text(opt.text().split(' (Pending:')[0]);
        $('#rp_pending_display').text(pending.toLocaleString());

        const parts = [];
        if (saltPending > 0)  parts.push('Salt ' + saltPending.toLocaleString());
        if (spicePending > 0) parts.push('Spices ' + spicePending.toLocaleString());
        $('#rp_pending_breakdown').text(parts.length > 1 ? parts.join('  +  ') : '');

        // Which udhaar is being paid — only lines that actually have pending.
        const fmt  = n => Number(n).toLocaleString();
        const opts = [];
        if (saltPending > 0)  opts.push({ v: 'salt',  t: 'Salt / نمک — ' + fmt(saltPending), max: saltPending });
        if (spicePending > 0) opts.push({ v: 'spice', t: 'Spice / مصالحہ — ' + fmt(spicePending), max: spicePending });
        if (saltPending > 0 && spicePending > 0) opts.push({ v: 'both', t: 'Both (oldest first) — ' + fmt(pending), max: pending });
        $('#rp_product_line').html(opts.map(o => `<option value="${o.v}" data-max="${o.max}">${o.t}</option>`).join(''));
        $('#rp_amount').attr('max', opts[0]?.max);

        $('#rp_pending_box').toggleClass('d-none', pending <= 0);
        $('#rp_fieldset').prop('disabled', pending <= 0);
    });

    // Cap the amount at the selected line's pending
    $('#rp_product_line').on('change', function () {
        $('#rp_amount').attr('max', $(this).find(':selected').data('max'));
    });

    $('#recordPaymentForm').on('submit', function (e) {
        e.preventDefault();
        const shopId = $('#rp_shop_id').val();
        const btn = $('#rpSubmitBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        $.post(APP_URL + '/shops/' + shopId + '/payments', $(this).serialize())
            .done(function (res) {
                $('#rp_alert').removeClass('d-none').text(
                    'Payment recorded across ' + (res.sales_paid || 0) + ' sale(s).'
                );
                toastr.success('Payment recorded!');
                // Reload with the shop kept selected so the refreshed history + pending show
                setTimeout(() => { location.href = location.pathname + '?shop=' + shopId; }, 1200);
            })
            .fail(function (xhr) {
                if (xhr.status === 422) {
                    alert((xhr.responseJSON.message) || Object.values(xhr.responseJSON.errors || {}).map(e => e[0]).join('\n'));
                } else {
                    toastr.error('Could not record payment.');
                }
            })
            .always(() => btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Record Payment'));
    });

    // Re-select the shop after a save (or when linked here with ?shop=ID)
    const preselect = new URLSearchParams(location.search).get('shop');
    if (preselect && $('#rp_shop_id option[value="' + preselect + '"]').length) {
        $('#rp_shop_id').val(preselect).trigger('change');
    }
});
</script>
@endsection
