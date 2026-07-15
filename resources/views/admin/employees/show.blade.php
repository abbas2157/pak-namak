@extends('admin.layout.app')
@section('title', $employee->name)

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1>{{ $employee->name }}</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.employees.index') }}">Employees</a></li>
                    <li class="breadcrumb-item active">{{ $employee->name }}</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end gap-2">
                <button class="btn btn-warning rounded-pill px-3 mr-2" id="editEmpBtn">
                    <i class="fas fa-edit mr-1"></i> Edit / ترمیم
                </button>
                <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary rounded-pill px-3">
                    <i class="fas fa-arrow-left mr-1"></i> Back / واپس
                </a>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">

            {{-- Employee Profile Card --}}
            <div class="col-md-4">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile text-center pb-2">
                        <div class="mb-3">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center emp-avatar">
                                {{ strtoupper(substr($employee->name, 0, 1)) }}
                            </div>
                        </div>
                        <h3 class="profile-username mb-1">{{ $employee->name }}</h3>
                        <p class="text-muted mb-2">{{ $employee->designation ?? 'No Designation' }}</p>
                        @if($employee->status === 'working')
                            <span class="badge badge-success px-3 py-2">Working</span>
                        @elseif($employee->status === 'on_leave')
                            <span class="badge badge-warning px-3 py-2">On Leave</span>
                        @else
                            <span class="badge badge-danger px-3 py-2">Terminated</span>
                        @endif
                    </div>
                    <div class="card-footer p-0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <i class="fas fa-phone mr-2 text-muted"></i>
                                <span>{{ $employee->phone ?? '-' }}</span>
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-envelope mr-2 text-muted"></i>
                                <span>{{ $employee->email ?? '-' }}</span>
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-id-card mr-2 text-muted"></i>
                                <span>{{ $employee->cnic ?? '-' }}</span>
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-map-marker-alt mr-2 text-muted"></i>
                                <span>{{ $employee->address ?? '-' }}</span>
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-calendar-plus mr-2 text-success"></i>
                                Joined / شمولیت: <strong>{{ $employee->joining_date ? $employee->joining_date->format('d M Y') : '-' }}</strong>
                            </li>
                            @if($employee->leave_date)
                            <li class="list-group-item">
                                <i class="fas fa-calendar-minus mr-2 text-danger"></i>
                                Left / رخصت: <strong>{{ $employee->leave_date->format('d M Y') }}</strong>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Salary Section --}}
            <div class="col-md-8">

                {{-- Summary Stats --}}
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Monthly Salary / ماہانہ تنخواہ</span>
                                <span class="info-box-number">{{ number_format($employee->salary, 0) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-coins"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Paid / کل ادا شدہ</span>
                                <span class="info-box-number">{{ number_format($totalPaid, 0) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fas fa-calendar-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Months Paid / ادا شدہ مہینے</span>
                                <span class="info-box-number">{{ $employee->salaries->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Salary History Table --}}
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Salary History / تنخواہ کی تاریخ</h4>
                        <div>
                            <button class="btn btn-outline-primary btn-sm" id="addAdvanceBtn">
                                <i class="fas fa-hand-holding-dollar mr-1"></i> Record Advance
                            </button>
                            <button class="btn btn-primary btn-sm" id="settleMonthBtn">
                                <i class="fas fa-check-circle mr-1"></i> Settle Month
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered table-sm mb-0" id="salaryTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Month / مہینہ</th>
                                    <th>Type</th>
                                    <th>Amount (PKR) / رقم</th>
                                    <th>Breakdown</th>
                                    <th>Paid On / ادائیگی تاریخ</th>
                                    <th>Note / نوٹ</th>
                                    <th>Action / اقدام</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($employee->salaries as $i => $sal)
                                <tr id="salRow{{ $sal->id }}">
                                    <td>{{ $i + 1 }}</td>
                                    <td><strong>{{ $sal->month->format('M Y') }}</strong></td>
                                    <td>
                                        @if($sal->type === 'advance')
                                            <span class="badge badge-info">Advance</span>
                                        @else
                                            <span class="badge badge-success">Salary</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($sal->amount, 0) }}</td>
                                    <td class="small text-muted">
                                        @if($sal->type === 'salary')
                                            Gross {{ number_format($sal->gross_amount, 0) }}
                                            @if($sal->advance_deducted > 0) &minus; Adv {{ number_format($sal->advance_deducted, 0) }} @endif
                                            @if($sal->absent_days > 0) &minus; {{ $sal->absent_days }}d absence ({{ number_format($sal->absence_deducted, 0) }}) @endif
                                        @else
                                            &mdash;
                                        @endif
                                    </td>
                                    <td>{{ $sal->paid_at ? $sal->paid_at->format('d M Y') : '-' }}</td>
                                    <td>{{ $sal->note ?? '-' }}</td>
                                    <td>
                                        <button class="btn btn-danger btn-xs delete-sal" data-id="{{ $sal->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr id="noSalRow">
                                    <td colspan="8" class="text-center py-3 text-muted">No salary payments recorded yet.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Attendance / Absences --}}
                <div class="card mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Attendance / حاضری</h4>
                        <button class="btn btn-outline-danger btn-sm" id="addAbsenceBtn">
                            <i class="fas fa-calendar-xmark mr-1"></i> Mark Absence
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered table-sm mb-0" id="absenceTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Note</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($employee->absences as $i => $abs)
                                <tr id="absRow{{ $abs->id }}">
                                    <td>{{ $i + 1 }}</td>
                                    <td><strong>{{ $abs->date->format('d M Y') }}</strong></td>
                                    <td>
                                        @if($abs->paid)
                                            <span class="badge badge-warning">Paid Leave</span>
                                        @else
                                            <span class="badge badge-danger">Unpaid Absence</span>
                                        @endif
                                    </td>
                                    <td>{{ $abs->note ?? '-' }}</td>
                                    <td>
                                        <button class="btn btn-danger btn-xs delete-abs" data-id="{{ $abs->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr id="noAbsRow">
                                    <td colspan="5" class="text-center py-3 text-muted">No absences recorded.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ========== RECORD ADVANCE MODAL ========== --}}
<div class="modal fade" id="addAdvanceModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="addAdvanceForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-hand-holding-dollar mr-2"></i>Record Advance</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Amount (PKR) / رقم <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="amount" min="0.01" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label>Date / تاریخ <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="payment_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Note / نوٹ</label>
                        <textarea class="form-control" name="note" rows="2" placeholder="Optional"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel / منسوخ</button>
                    <button class="btn btn-info text-white" type="submit">Save Advance</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ========== SETTLE MONTH MODAL ========== --}}
<div class="modal fade" id="settleMonthModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="settleMonthForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-check-circle mr-2"></i>Settle Month / تنخواہ کی ادائیگی</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Month / مہینہ <span class="text-danger">*</span></label>
                        <input type="month" class="form-control" name="month" id="settleMonthInput" required>
                    </div>

                    <div id="settlePreview" class="d-none p-3 mb-3 rounded" style="background:#f4f6f9;">
                        <div class="d-flex justify-content-between"><span>Gross Salary</span><strong id="sp_gross">-</strong></div>
                        <div class="d-flex justify-content-between text-info"><span>Advances Taken</span><strong id="sp_advance">-</strong></div>
                        <div class="d-flex justify-content-between text-danger"><span id="sp_absent_label">Absence Deduction</span><strong id="sp_absence">-</strong></div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between"><span class="font-weight-bold">Net Payable</span><strong id="sp_net" class="text-success">-</strong></div>
                        <div id="sp_settled_warning" class="text-danger small mt-2 d-none">This month is already settled.</div>
                    </div>

                    <div class="mb-3">
                        <label>Paid On / ادائیگی تاریخ</label>
                        <input type="date" class="form-control" name="paid_at" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label>Note / نوٹ</label>
                        <textarea class="form-control" name="note" rows="2" placeholder="Optional"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel / منسوخ</button>
                    <button class="btn btn-primary" type="submit" id="settleSubmitBtn">Confirm &amp; Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ========== MARK ABSENCE MODAL ========== --}}
<div class="modal fade" id="addAbsenceModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="addAbsenceForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-calendar-xmark mr-2"></i>Mark Absence</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Date / تاریخ <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="paid" id="absencePaid" value="1">
                        <label class="form-check-label" for="absencePaid">Paid leave (does not deduct salary)</label>
                    </div>
                    <div class="mb-3">
                        <label>Note / نوٹ</label>
                        <textarea class="form-control" name="note" rows="2" placeholder="Optional (e.g. sick, personal)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel / منسوخ</button>
                    <button class="btn btn-danger" type="submit">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ========== EDIT EMPLOYEE MODAL ========== --}}
<div class="modal fade" id="editEmployeeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editEmployeeForm">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="fas fa-user-edit mr-2"></i>Edit Employee / ملازم ترمیم</h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-6 mb-3">
                        <label>Full Name / پورا نام <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="editName" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Email / ای میل <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" id="editEmail" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Phone / فون <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="phone" id="editPhone" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>CNIC / شناختی کارڈ</label>
                        <input type="text" class="form-control" name="cnic" id="editCnic">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Designation / عہدہ</label>
                        <input type="text" class="form-control" name="designation" id="editDesignation">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Monthly Salary (PKR) / ماہانہ تنخواہ <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="salary" id="editSalary" min="0" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Joining Date / شمولیت کی تاریخ</label>
                        <input type="date" class="form-control" name="joining_date" id="editJoiningDate">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Status / حیثیت <span class="text-danger">*</span></label>
                        <select class="form-control" name="status" id="editStatus" required>
                            <option value="working">Working</option>
                            <option value="on_leave">On Leave</option>
                            <option value="terminated">Terminated</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Leave / End Date / چھٹی تاریخ</label>
                        <input type="date" class="form-control" name="leave_date" id="editLeaveDate">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Address / پتہ</label>
                        <input type="text" class="form-control" name="address" id="editAddress">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel / منسوخ</button>
                    <button class="btn btn-warning" type="submit">Update Employee / ملازم اپ ڈیٹ کریں</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {

    // --- Advance: open modal ---
    $('#addAdvanceBtn').on('click', function () {
        $('#addAdvanceForm')[0].reset();
        $('#addAdvanceForm [name="payment_date"]').val(new Date().toISOString().split('T')[0]);
        $('#addAdvanceModal').modal('show');
    });

    // --- Advance: submit ---
    $('#addAdvanceForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('admin.employees.advances.store', $employee->id) }}",
            type: 'POST',
            data: $(this).serialize(),
            success: function (res) {
                if (res.success) {
                    $('#addAdvanceModal').modal('hide');
                    location.reload();
                }
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message
                    ?? Object.values(xhr.responseJSON?.errors ?? {}).map(e => e[0]).join("\n")
                    ?? 'Error: ' + xhr.status;
                alert(msg);
            }
        });
    });

    // --- Settle Month: open modal ---
    $('#settleMonthBtn').on('click', function () {
        $('#settleMonthForm')[0].reset();
        $('#settleMonthForm [name="paid_at"]').val(new Date().toISOString().split('T')[0]);
        $('#settlePreview').addClass('d-none');
        const now = new Date();
        $('#settleMonthInput').val(now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0'));
        loadSettlePreview();
        $('#settleMonthModal').modal('show');
    });

    $('#settleMonthInput').on('change', loadSettlePreview);

    function loadSettlePreview() {
        const month = $('#settleMonthInput').val();
        if (!month) { $('#settlePreview').addClass('d-none'); return; }
        $.get("{{ route('admin.employees.salaries.preview', $employee->id) }}", { month: month }, function (res) {
            $('#sp_gross').text(Number(res.gross).toLocaleString());
            $('#sp_advance').text('- ' + Number(res.advance_total).toLocaleString());
            $('#sp_absent_label').text('Absence Deduction (' + res.absent_days + ' day' + (res.absent_days == 1 ? '' : 's') + ')');
            $('#sp_absence').text('- ' + Number(res.absence_deducted).toLocaleString());
            $('#sp_net').text(Number(res.net).toLocaleString());
            $('#sp_settled_warning').toggleClass('d-none', !res.already_settled);
            $('#settleSubmitBtn').prop('disabled', res.already_settled);
            $('#settlePreview').removeClass('d-none');
        });
    }

    // --- Settle Month: submit ---
    $('#settleMonthForm').on('submit', function (e) {
        e.preventDefault();
        const data = $(this).serializeArray();
        data.forEach(function (item) {
            if (item.name === 'month' && item.value.length === 7) {
                item.value += '-01';
            }
        });
        $.ajax({
            url: "{{ route('admin.employees.salaries.store', $employee->id) }}",
            type: 'POST',
            data: $.param(data),
            success: function (res) {
                if (res.success) {
                    $('#settleMonthModal').modal('hide');
                    location.reload();
                }
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message
                    ?? Object.values(xhr.responseJSON?.errors ?? {}).map(e => e[0]).join("\n")
                    ?? 'Error: ' + xhr.status;
                alert(msg);
            }
        });
    });

    // --- Salary: delete (works for advance or salary rows) ---
    $(document).on('click', '.delete-sal', function () {
        if (!confirm('Remove this record?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: APP_URL + '/employees/{{ $employee->id }}/salaries/' + id,
            type: 'POST',
            data: { _method: 'DELETE', _token: "{{ csrf_token() }}" },
            success: function (res) {
                if (res.success) {
                    $('#salRow' + id).remove();
                    if ($('#salaryTable tbody tr').length === 0) {
                        $('#salaryTable tbody').append('<tr id="noSalRow"><td colspan="8" class="text-center py-3 text-muted">No salary payments recorded yet.</td></tr>');
                    }
                }
            },
            error: function (xhr) { alert('Error: ' + xhr.status); }
        });
    });

    // --- Absence: open modal ---
    $('#addAbsenceBtn').on('click', function () {
        $('#addAbsenceForm')[0].reset();
        $('#addAbsenceForm [name="date"]').val(new Date().toISOString().split('T')[0]);
        $('#addAbsenceModal').modal('show');
    });

    // --- Absence: submit ---
    $('#addAbsenceForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('admin.employees.absences.store', $employee->id) }}",
            type: 'POST',
            data: $(this).serialize(),
            success: function (res) {
                if (res.success) {
                    $('#addAbsenceModal').modal('hide');
                    location.reload();
                }
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message
                    ?? Object.values(xhr.responseJSON?.errors ?? {}).map(e => e[0]).join("\n")
                    ?? 'Error: ' + xhr.status;
                alert(msg);
            }
        });
    });

    // --- Absence: delete ---
    $(document).on('click', '.delete-abs', function () {
        if (!confirm('Remove this absence record?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: APP_URL + '/employees/{{ $employee->id }}/absences/' + id,
            type: 'POST',
            data: { _method: 'DELETE', _token: "{{ csrf_token() }}" },
            success: function (res) {
                if (res.success) {
                    $('#absRow' + id).remove();
                    if ($('#absenceTable tbody tr').length === 0) {
                        $('#absenceTable tbody').append('<tr id="noAbsRow"><td colspan="5" class="text-center py-3 text-muted">No absences recorded.</td></tr>');
                    }
                }
            },
            error: function (xhr) { alert('Error: ' + xhr.status); }
        });
    });

    // --- Edit employee ---
    $('#editEmpBtn').on('click', function () {
        $.get(APP_URL + '/employees/{{ $employee->id }}/edit', function (emp) {
            $('#editName').val(emp.name);
            $('#editEmail').val(emp.email);
            $('#editPhone').val(emp.phone);
            $('#editCnic').val(emp.cnic);
            $('#editDesignation').val(emp.designation);
            $('#editSalary').val(emp.salary);
            $('#editAddress').val(emp.address);
            $('#editStatus').val(emp.status);
            $('#editJoiningDate').val(emp.joining_date);
            $('#editLeaveDate').val(emp.leave_date);
            $('#editEmployeeModal').modal('show');
        });
    });

    $('#editEmployeeForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: APP_URL + '/employees/{{ $employee->id }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function (res) {
                if (res.success) {
                    $('#editEmployeeModal').modal('hide');
                    location.reload();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let msgs = Object.values(xhr.responseJSON.errors).map(e => e[0]).join("\n");
                    alert(msgs);
                } else {
                    alert('Error: ' + xhr.status);
                }
            }
        });
    });

});
</script>
@endsection
