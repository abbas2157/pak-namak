@extends('admin.layout.app')
@section('title', 'Send Advance')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Send Advance <small class="text-muted ch-sub">ایڈوانس بھیجیں</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.vendors.index') }}">Vendors</a></li>
                    <li class="breadcrumb-item active">Send Advance</li>
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
                            <i class="fas fa-money-bill-transfer mr-2"></i>Send a Vendor an Advance
                        </h6>
                    </div>
                    <div class="card-body py-4 px-4">

                        <p class="text-muted small">For money sent to a vendor before an order is dispatched or delivered — e.g. to fund their purchase or shipment. This becomes credit that can be applied once a real purchase from them is recorded.</p>

                        <div id="adv_alert" class="alert alert-success border-0 shadow-sm d-none" role="alert"></div>

                        <div class="mb-4">
                            <label class="filter-lbl">Vendor / فروش کار <span class="text-danger">*</span></label>
                            <select id="adv_vendor_id" class="form-control fc-pn select2" style="width:100%;">
                                <option value="">— Search for a vendor —</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">
                                        {{ $vendor->name }}{{ $vendor->shop ? ' — '.$vendor->shop : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <form id="advanceForm">
                            @csrf
                            <fieldset id="adv_fieldset" disabled>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="filter-lbl">Amount <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control fc-pn" placeholder="0" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="filter-lbl">Date <span class="text-danger">*</span></label>
                                        <input type="date" name="advance_date" id="adv_date" class="form-control fc-pn" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="filter-lbl">Paid From</label>
                                        <select name="account_id" class="form-control fc-pn">
                                            @foreach($accounts as $account)
                                                <option value="{{ $account->id }}" {{ $account->type === 'cash' ? 'selected' : '' }}>{{ $account->label() }}</option>
                                            @endforeach
                                            <option value="">Other / Not from Cash &amp; Bank (کیش/بینک سے نہیں)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="filter-lbl">Note / نوٹ</label>
                                        <input type="text" name="note" class="form-control fc-pn" placeholder="Optional — e.g. reason for advance">
                                    </div>
                                </div>
                                <p class="text-muted small mb-3">Applied later from that vendor's purchase "Record Payment" screen.</p>
                                <button class="btn btn-primary btn-pn px-4" type="submit" id="advSubmitBtn">
                                    <i class="fas fa-save mr-1"></i> Send Advance
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
    $('.select2').select2({ placeholder: '— Search for a vendor —', allowClear: true, width: '100%' });
    $('#adv_date').val(new Date().toISOString().split('T')[0]);

    $('#adv_vendor_id').on('change', function () {
        $('#adv_fieldset').prop('disabled', !$(this).val());
    });

    $('#advanceForm').on('submit', function (e) {
        e.preventDefault();
        const vendorId = $('#adv_vendor_id').val();
        const btn = $('#advSubmitBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        $.post(APP_URL + '/vendors/' + vendorId + '/advances', $(this).serialize())
            .done(function () {
                $('#adv_alert').removeClass('d-none').text('Advance sent successfully.');
                toastr.success('Advance sent!');
                setTimeout(() => location.reload(), 1200);
            })
            .fail(function (xhr) {
                if (xhr.status === 422) {
                    alert((xhr.responseJSON.message) || Object.values(xhr.responseJSON.errors || {}).map(e => e[0]).join('\n'));
                } else {
                    toastr.error('Could not send advance.');
                }
            })
            .always(() => btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Send Advance'));
    });
});
</script>
@endsection
