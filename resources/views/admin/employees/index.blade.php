@extends('admin.layout.app')
@section('title','Employees')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Employee List</h1>
                </div>
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Employee List</li>
                        </ol>
                    </div>
                    <div class="col-sm-6 d-flex justify-content-end">
                        <button class="btn btn-primary mb-3 float-right mt-3 px-3 mr-3 rounded-pill" id="addEmployee">
                            <i class="fas fa-plus"></i> Add Employee
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <table class="table table-bordered table-striped" id="employeesTable">
                <thead class="thead-dark">
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Salary</th>
                        <th>Address</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @if($employees->isEmpty())
                    <tr>
                        <td colspan="8" class="text-center">No employees found.</td>
                    </tr>
                @endif
                @foreach($employees as $item)
                    <tr id="row{{$item->id}}">
                        <td>{{$item->name ?? ''}}</td>
                        <td>{{$item->phone ?? ''}}</td>
                        <td>{{$item->salary ?? ''}}</td>
                        <td>{{$item->address ?? ''}}</td>
                        <td>
                            <button class="btn btn-warning btn-sm edit" data-id="{{$item->id ?? ''}}">Edit</button>
                            <button class="btn btn-danger btn-sm delete" data-id="{{$item->id ?? ''}}">Delete</button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>
   <div class="modal fade" id="createEmployeeModal">
        <div class="modal-dialog modal-lg">
            <form id="createEmployeeForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Employee</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body row">
                        <div class="col-md-6 mb-2">
                            <label>Full Name</label>
                            <input type="text" class="form-control mb-2" name="name" placeholder="Name" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Phone</label>
                            <input type="text" class="form-control mb-2" name="phone" placeholder="Phone" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Salary</label>
                            <input type="number" class="form-control mb-2" name="salary" placeholder="Salary" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Address</label>
                            <input type="text" class="form-control mb-2" name="address" placeholder="Address" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">Create Employee</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editEmployeeModal">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Edit Employee</h5>
            <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
            </button>
        </div>

        <form id="editEmployeeForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="employee_id" id="editEmployeeId">
            <div class="modal-body row">
                <div class="col-md-6 mb-2">
                    <label for="name">Name</label>
                    <input type="text" class="form-control mb-2" name="name" id="editName" placeholder="Name" required>
                </div>
                <div class="col-md-6 mb-2">
                    <label for="phone">Phone</label>
                    <input type="text" class="form-control mb-2" name="phone" id="editPhone" placeholder="Phone" required>
                </div>
                <div class="col-md-6 mb-2">
                    <label for="salary">salary</label>
                    <input type="number" class="form-control mb-2" name="salary" id="editSalary" placeholder="Salary" required>
                </div>
                <div class="col-md-6 mb-2">
                    <label for="address">Address</label>
                    <input type="text" class="form-control mb-2" name="address" id="editAddress" placeholder="Address" required>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" type="submit">Update</button>
            </div>
        </form>
        </div>
    </div>
    </div>


@endsection

@section('scripts')
<script>
$(document).ready(function(){

    $('#addEmployee').click(function(){
        $('#createEmployeeForm')[0].reset();
        $('#createEmployeeModal').modal('show');
    });

    $('#createEmployeeForm').on('submit', function(e){
        e.preventDefault();
        $.ajax({
            url: "{{ route('admin.employees.store') }}",
            type: 'POST',
            data: $(this).serialize(),
            success: function(response){
                if(response.success){
                    $('#createEmployeeModal').modal('hide');
                    toastr.success('Employee saved successfully!');
                    location.reload();
                }
            },
            error: function(xhr){
                if(xhr.status === 422){
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = '';
                    $.each(errors, function(key, value){
                        errorMessages += value[0] + "\n";
                    });
                    toastr.error(errorMessages);
                } else {
                    toastr.error('Something went wrong!');
                }
            }
        });
    });

    $(document).on('click', '.edit', function(){
        let id = $(this).data('id');
        $.get(APP_URL + "/admin/employees/" + id + "/edit", function(employee){
            $('#editEmployeeId').val(employee.id);
            $('#editName').val(employee.name);
            $('#editPhone').val(employee.phone);
            $('#editSalary').val(employee.salary);
            $('#editAddress').val(employee.address);
            $('#editEmployeeModal').modal('show');
        });
    });

    $('#editEmployeeForm').on('submit', function(e){
        e.preventDefault();
        let id = $('#editEmployeeId').val();
        $.ajax({
            url: APP_URL + "/admin/employees/" + id,
            type: 'POST',
            data: $(this).serialize(),
            success: function(response){
                if(response.success){
                    $('#editEmployeeModal').modal('hide');
                    toastr.success('Employee updated successfully!');
                    location.reload();
                }
            },
            error: function(xhr){
                if(xhr.status === 422){
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = '';
                    $.each(errors, function(key, value){
                        errorMessages += value[0] + "\n";
                    });
                    toastr.error(errorMessages);
                } else {
                    toastr.error('Error: '+xhr.status);
                }
            }
        });
    });

     $(document).on('click', '.delete', function(){
        if(!confirm('Are you sure you want to delete this employee?')) return;

        let id = $(this).data('id');
        $.ajax({
            url: APP_URL + "/admin/employees/" + id,
            type: 'POST',
            data: {
                _method: 'DELETE',
                _token: "{{ csrf_token() }}"
            },
            success: function(response){
                if(response.success){
                    toastr.success('Employee deleted successfully!');
                    $('#row' + id).remove();
                }
            },
             error: function(xhr){
                Swal.fire(
                    'Error!',
                    'Something went wrong.',
                    'error'
                );
            }
        });
    });

});
$(function () {

    $('#employeesTable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true
    });

});
</script>
@endsection
