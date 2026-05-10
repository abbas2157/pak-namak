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
                    <i class="fas fa-edit mr-1"></i> Edit
                </button>
                <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary rounded-pill px-3">
                    <i class="fas fa-arrow-left mr-1"></i> Back
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
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                                 style="width:80px;height:80px;font-size:2rem;font-weight:700;">
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
                                Joined: <strong>{{ $employee->joining_date ? $employee->joining_date->format('d M Y') : '-' }}</strong>
                            </li>
                            @if($employee->leave_date)
                            <li class="list-group-item">
                                <i class="fas fa-calendar-minus mr-2 text-danger"></i>
                                Left: <strong>{{ $employee->leave_date->format('d M Y') }}</strong>
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
                                <span class="info-box-text">Monthly Salary</span>
                                <span class="info-box-number">{{ number_format($employee->salary, 0) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-coins"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Paid</span>
                                <span class="info-box-number">{{ number_format($totalPaid, 0) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fas fa-calendar-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Months Paid</span>
                                <span class="info-box-number">{{ $employee->salaries->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Salary History Table --}}
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Salary History</h4>
                        <button class="btn btn-primary btn-sm" id="addSalaryBtn">
                            <i class="fas fa-plus mr-1"></i> Record Payment
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered table-sm mb-0" id="salaryTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Month</th>
                                    <th>Amount (PKR)</th>
                                    <th>Paid On</th>
                                    <th>Note</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($employee->salaries as $i => $sal)
                                <tr id="salRow{{ $sal->id }}">
                                    <td>{{ $i + 1 }}</td>
                                    <td><strong>{{ $sal->month->format('M Y') }}</strong></td>
                                    <td>{{ number_format($sal->amount, 0) }}</td>
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
                                    <td colspan="6" class="text-center py-3 text-muted">No salary payments recorded yet.</td>
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

{{-- ========== ADD SALARY MODAL ========== --}}
<div class="modal fade" id="addSalaryModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="addSalaryForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-coins mr-2"></i>Record Salary Payment</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Month <span class="text-danger">*</span></label>
                        <input type="month" class="form-control" name="month" required>
                    </div>
                    <div class="mb-3">
                        <label>Amount (PKR) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="amount" value="{{ $employee->salary }}" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label>Paid On</label>
                        <input type="date" class="form-control" name="paid_at" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label>Note</label>
                        <textarea class="form-control" name="note" rows="2" placeholder="Optional note (e.g. advance, deduction)..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit">Save Payment</button>
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
                    <h5 class="modal-title"><i class="fas fa-user-edit mr-2"></i>Edit Employee</h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-6 mb-3">
                        <label>Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="editName" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" id="editEmail" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Phone <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="phone" id="editPhone" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>CNIC</label>
                        <input type="text" class="form-control" name="cnic" id="editCnic">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Designation</label>
                        <input type="text" class="form-control" name="designation" id="editDesignation">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Monthly Salary (PKR) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="salary" id="editSalary" min="0" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Joining Date</label>
                        <input type="date" class="form-control" name="joining_date" id="editJoiningDate">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Status <span class="text-danger">*</span></label>
                        <select class="form-control" name="status" id="editStatus" required>
                            <option value="working">Working</option>
                            <option value="on_leave">On Leave</option>
                            <option value="terminated">Terminated</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Leave / End Date</label>
                        <input type="date" class="form-control" name="leave_date" id="editLeaveDate">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Address</label>
                        <input type="text" class="form-control" name="address" id="editAddress">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-warning" type="submit">Update Employee</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {

    // --- Salary: open modal ---
    $('#addSalaryBtn').on('click', function () {
        $('#addSalaryForm')[0].reset();
        $('#addSalaryForm [name="amount"]').val('{{ $employee->salary }}');
        $('#addSalaryForm [name="paid_at"]').val(new Date().toISOString().split('T')[0]);
        $('#addSalaryModal').modal('show');
    });

    // --- Salary: submit ---
    $('#addSalaryForm').on('submit', function (e) {
        e.preventDefault();
        const data = $(this).serializeArray();
        // Normalize "YYYY-MM" to "YYYY-MM-01" for server date parsing
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
                    $('#addSalaryModal').modal('hide');
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

    // --- Salary: delete ---
    $(document).on('click', '.delete-sal', function () {
        if (!confirm('Remove this salary record?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: APP_URL + '/employees/{{ $employee->id }}/salaries/' + id,
            type: 'POST',
            data: { _method: 'DELETE', _token: "{{ csrf_token() }}" },
            success: function (res) {
                if (res.success) {
                    $('#salRow' + id).remove();
                    if ($('#salaryTable tbody tr').length === 0) {
                        $('#salaryTable tbody').append('<tr id="noSalRow"><td colspan="6" class="text-center py-3 text-muted">No salary payments recorded yet.</td></tr>');
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
