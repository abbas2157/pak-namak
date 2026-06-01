@extends('admin.layout.app')
@section('title','Employees')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1>Employees <small class="text-muted" style="font-size:14px;">ملازمین</small></h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Employees</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end">
                <button class="btn btn-primary rounded-pill px-4" id="addEmployee">
                    <i class="fas fa-plus mr-1"></i> Add Employee / ملازم شامل کریں
                </button>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        {{-- Stats bar --}}
        <div class="row mb-3">
            <div class="col-6 col-md-3">
                <div class="info-box bg-success mb-2">
                    <span class="info-box-icon"><i class="fas fa-user-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Working / کام کرنے والے</span>
                        <span class="info-box-number">{{ $employees->where('status','working')->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="info-box bg-warning mb-2">
                    <span class="info-box-icon"><i class="fas fa-user-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">On Leave / چھٹی پر</span>
                        <span class="info-box-number">{{ $employees->where('status','on_leave')->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="info-box bg-danger mb-2">
                    <span class="info-box-icon"><i class="fas fa-user-times"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Terminated / برخاست</span>
                        <span class="info-box-number">{{ $employees->where('status','terminated')->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="info-box bg-info mb-2">
                    <span class="info-box-icon"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total / کل</span>
                        <span class="info-box-number">{{ $employees->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter buttons --}}
        <div class="mb-3">
            <button class="btn btn-sm btn-secondary filter-btn active mr-1" data-filter="all">All</button>
            <button class="btn btn-sm btn-success filter-btn mr-1" data-filter="working">Working</button>
            <button class="btn btn-sm btn-warning filter-btn mr-1" data-filter="on_leave">On Leave</button>
            <button class="btn btn-sm btn-danger filter-btn" data-filter="terminated">Terminated</button>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0" id="employeesTable">
                    <thead class="thead-dark">
                        <tr>
                            <th>Name / نام</th>
                            <th>CNIC / شناختی کارڈ</th>
                            <th>Designation / عہدہ</th>
                            <th>Phone / فون</th>
                            <th>Salary/Month / تنخواہ/ماہ</th>
                            <th>Status / حیثیت</th>
                            <th>Joining Date / شمولیت</th>
                            <th>Actions / اقدامات</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($employees as $i => $emp)
                        <tr class="emp-row" data-status="{{ $emp->status }}">
                            <td><strong>{{ $emp->name }}</strong></td>
                            <td>{{ $emp->cnic ?? '-' }}</td>
                            <td>{{ $emp->designation ?? '-' }}</td>
                            <td>{{ $emp->phone ?? '-' }}</td>
                            <td>{{ number_format($emp->salary, 0) }}</td>
                            <td>
                                @if($emp->status === 'working')
                                    <span class="badge badge-success">Working</span>
                                @elseif($emp->status === 'on_leave')
                                    <span class="badge badge-warning">On Leave</span>
                                @else
                                    <span class="badge badge-danger">Terminated</span>
                                @endif
                            </td>
                            <td>{{ $emp->joining_date ? $emp->joining_date->format('d M Y') : '-' }}</td>
                            <td>
                                <a href="{{ route('admin.employees.show', $emp->id) }}" class="btn btn-info btn-sm" title="View Profile">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="btn btn-warning btn-sm edit" data-id="{{ $emp->id }}" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm delete" data-id="{{ $emp->id }}" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-4 text-muted">No employees found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ========== CREATE MODAL ========== --}}
<div class="modal fade" id="createEmployeeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="createEmployeeForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-user-plus mr-2"></i>Add New Employee / نیا ملازم شامل کریں</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-6 mb-3">
                        <label>Full Name / پورا نام <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Email / ای میل <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Phone / فون <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="phone" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>CNIC / شناختی کارڈ</label>
                        <input type="text" class="form-control" name="cnic" placeholder="42101-1234567-1">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Designation / عہدہ</label>
                        <input type="text" class="form-control" name="designation" placeholder="e.g. Manager, Worker, Driver">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Monthly Salary (PKR) / ماہانہ تنخواہ <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="salary" min="0" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Joining Date / شمولیت کی تاریخ</label>
                        <input type="date" class="form-control" name="joining_date">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Status / حیثیت <span class="text-danger">*</span></label>
                        <select class="form-control" name="status" required>
                            <option value="working">Working</option>
                            <option value="on_leave">On Leave</option>
                            <option value="terminated">Terminated</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Leave / End Date / چھٹی کی تاریخ</label>
                        <input type="date" class="form-control" name="leave_date">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Address / پتہ</label>
                        <input type="text" class="form-control" name="address">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel / منسوخ</button>
                    <button class="btn btn-primary" type="submit">Save Employee / ملازم محفوظ کریں</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ========== EDIT MODAL ========== --}}
<div class="modal fade" id="editEmployeeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editEmployeeForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="employee_id" id="editId">
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
                        <label>Leave / End Date / چھٹی کی تاریخ</label>
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

    // --- Status filter ---
    $('.filter-btn').on('click', function () {
        $('.filter-btn').removeClass('active btn-secondary btn-success btn-warning btn-danger')
            .addClass('btn-outline-secondary btn-outline-success btn-outline-warning btn-outline-danger');
        $(this).removeClass('btn-outline-secondary btn-outline-success btn-outline-warning btn-outline-danger')
            .addClass('active');

        const filter = $(this).data('filter');
        if (filter === 'all') {
            $('.emp-row').show();
        } else {
            $('.emp-row').hide();
            $('.emp-row[data-status="' + filter + '"]').show();
        }
    });

    // --- Create ---
    $('#addEmployee').on('click', function () {
        $('#createEmployeeForm')[0].reset();
        $('#createEmployeeModal').modal('show');
    });

    $('#createEmployeeForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('admin.employees.store') }}",
            type: 'POST',
            data: $(this).serialize(),
            success: function (res) {
                if (res.success) {
                    $('#createEmployeeModal').modal('hide');
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

    // --- Edit ---
    $(document).on('click', '.edit', function () {
        const id = $(this).data('id');
        $.get(APP_URL + '/employees/' + id + '/edit', function (emp) {
            $('#editId').val(emp.id);
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
        const id = $('#editId').val();
        $.ajax({
            url: APP_URL + '/employees/' + id,
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

    // --- Delete ---
    $(document).on('click', '.delete', function () {
        if (!confirm('Delete this employee? All salary records will also be removed.')) return;
        const id = $(this).data('id');
        $.ajax({
            url: APP_URL + '/employees/' + id,
            type: 'POST',
            data: { _method: 'DELETE', _token: "{{ csrf_token() }}" },
            success: function (res) {
                if (res.success) location.reload();
            },
            error: function (xhr) { alert('Error: ' + xhr.status); }
        });
    });

});
</script>
@endsection
