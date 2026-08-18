@extends('admin.layout.app')
@section('title', 'Record Advance')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Record Advance <small class="text-muted ch-sub">ایڈوانس درج کریں</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.employees.index') }}">Employees</a></li>
                    <li class="breadcrumb-item active">Record Advance</li>
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
                            <i class="fas fa-hand-holding-dollar mr-2"></i>Pick an Employee &amp; Log an Advance
                        </h6>
                    </div>
                    <div class="card-body py-4 px-4">

                        <div id="ra_alert" class="alert alert-success border-0 shadow-sm d-none" role="alert"></div>

                        <div class="mb-4">
                            <label class="filter-lbl">Employee <span class="text-danger">*</span></label>
                            <select id="ra_employee_id" class="form-control fc-pn select2" style="width:100%;">
                                <option value="">— Search for an employee —</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" data-salary="{{ $employee->salary }}">
                                        {{ $employee->name }}{{ $employee->designation ? ' — '.$employee->designation : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="ra_salary_box" class="d-none mb-4 p-3 rounded-3" style="background:#f0f9ff;border:1.5px solid #bee3f8;">
                            <span class="text-muted">Monthly Salary for </span>
                            <strong id="ra_employee_name"></strong>
                            <span class="font-weight-bold text-c-blue2 float-right" id="ra_salary_display"></span>
                        </div>

                        <form id="recordAdvanceForm">
                            @csrf
                            <fieldset id="ra_fieldset" disabled>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="filter-lbl">Amount <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control fc-pn" placeholder="0" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="filter-lbl">Date <span class="text-danger">*</span></label>
                                        <input type="date" name="payment_date" id="ra_payment_date" class="form-control fc-pn" required>
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
                                        <input type="text" name="note" class="form-control fc-pn" placeholder="Optional">
                                    </div>
                                </div>
                                <p class="text-muted small mb-3">Deducted automatically when this employee's month is settled.</p>
                                <button class="btn btn-primary btn-pn px-4" type="submit" id="raSubmitBtn">
                                    <i class="fas fa-save mr-1"></i> Record Advance
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
    $('.select2').select2({ placeholder: '— Search for an employee —', allowClear: true, width: '100%' });
    $('#ra_payment_date').val(new Date().toISOString().split('T')[0]);

    $('#ra_employee_id').on('change', function () {
        const opt = $(this).find('option:selected');
        const salary = parseFloat(opt.data('salary')) || 0;

        if (!$(this).val()) {
            $('#ra_salary_box').addClass('d-none');
            $('#ra_fieldset').prop('disabled', true);
            return;
        }

        $('#ra_employee_name').text(opt.text());
        $('#ra_salary_display').text(salary.toLocaleString());
        $('#ra_salary_box').removeClass('d-none');
        $('#ra_fieldset').prop('disabled', false);
    });

    $('#recordAdvanceForm').on('submit', function (e) {
        e.preventDefault();
        const employeeId = $('#ra_employee_id').val();
        const btn = $('#raSubmitBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        $.post(APP_URL + '/employees/' + employeeId + '/advances', $(this).serialize())
            .done(function () {
                $('#ra_alert').removeClass('d-none').text('Advance recorded successfully.');
                toastr.success('Advance recorded!');
                setTimeout(() => location.reload(), 1200);
            })
            .fail(function (xhr) {
                if (xhr.status === 422) {
                    alert((xhr.responseJSON.message) || Object.values(xhr.responseJSON.errors || {}).map(e => e[0]).join('\n'));
                } else {
                    toastr.error('Could not record advance.');
                }
            })
            .always(() => btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Record Advance'));
    });
});
</script>
@endsection
