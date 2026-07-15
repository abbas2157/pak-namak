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
                                    @php $shopArea = $shop->area?->name ?? $shop->city; @endphp
                                    <option value="{{ $shop->id }}" data-pending="{{ $shop->sales_sum_pending_amount ?? 0 }}">
                                        {{ $shop->name }}{{ $shopArea ? ' — '.$shopArea : '' }}
                                        @if(($shop->sales_sum_pending_amount ?? 0) > 0)
                                            (Pending: {{ number_format($shop->sales_sum_pending_amount, 0) }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="rp_pending_box" class="d-none mb-4 p-3 rounded-3" style="background:#fff5f5;border:1.5px solid #f8d7da;">
                            <span class="text-muted">Total Pending for </span>
                            <strong id="rp_shop_name"></strong>
                            <span class="font-weight-bold text-c-red float-right" id="rp_pending_display"></span>
                        </div>

                        <form id="recordPaymentForm">
                            @csrf
                            <fieldset id="rp_fieldset" disabled>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="filter-lbl">Amount <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control fc-pn" placeholder="0" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="filter-lbl">Payment Date <span class="text-danger">*</span></label>
                                        <input type="date" name="payment_date" id="rp_payment_date" class="form-control fc-pn" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="filter-lbl">Method <span class="text-danger">*</span></label>
                                        <select name="payment_method" class="form-control fc-pn" required>
                                            <option value="Cash" selected>Cash</option>
                                            <option value="Bank Transfer">Bank Transfer</option>
                                            <option value="EasyPaisa">EasyPaisa</option>
                                            <option value="JazzCash">JazzCash</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="filter-lbl">Note / نوٹ</label>
                                        <input type="text" name="note" class="form-control fc-pn" placeholder="Optional">
                                    </div>
                                </div>
                                <p class="text-muted small mb-3">Applied to this shop's oldest pending sales first.</p>
                                <button class="btn btn-primary btn-pn px-4" type="submit" id="rpSubmitBtn">
                                    <i class="fas fa-save mr-1"></i> Record Payment
                                </button>
                            </fieldset>
                        </form>

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

    $('#rp_shop_id').on('change', function () {
        const opt = $(this).find('option:selected');
        const pending = parseFloat(opt.data('pending')) || 0;

        if (!$(this).val()) {
            $('#rp_pending_box').addClass('d-none');
            $('#rp_fieldset').prop('disabled', true);
            return;
        }

        $('#rp_shop_name').text(opt.text().split(' (Pending:')[0]);
        $('#rp_pending_display').text(pending.toLocaleString());
        $('#rp_pending_box').toggleClass('d-none', pending <= 0);
        $('#rp_fieldset').prop('disabled', pending <= 0);
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
                setTimeout(() => location.reload(), 1200);
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
});
</script>
@endsection
