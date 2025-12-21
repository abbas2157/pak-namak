@extends('admin.layout.app')
@section('title','Employees')

@section('content')
<div class="container-fluid mt-3">
    <div class="container-fluid mt-3 d-flex justify-content-end">
        <button class="btn btn-primary rounded-pill mb-3 mt-3 m-3" id="addEmployee">
            + Add Employee
        </button>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Salary</th>
                <th>Address</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($employees as $emp)
            <tr id="row{{$emp->id}}">
                <td>{{$emp->id}}</td>
                <td>{{$emp->name}}</td>
                <td>{{$emp->email}}</td>
                <td>{{$emp->phone}}</td>
                <td>{{$emp->salary}}</td>
                <td>{{$emp->address}}</td>
                <td>
                    <button class="btn btn-warning btn-sm edit"
                        data-id="{{$emp->id}}">Edit</button>
                    <button class="btn btn-danger btn-sm delete"
                        data-id="{{$emp->id}}">Delete</button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<!-- CREATE MODAL -->
<div class="modal fade" id="createEmployeeModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Employee</h5>
        <button type="button" class="close" data-dismiss="modal">
        <span>&times;</span>
        </button>
      </div>
      <form id="createEmployeeForm">
            @csrf
            <div class="modal-body">
                <input type="text" class="form-control mb-2" name="name" placeholder="Name" required>
                <input type="email" class="form-control mb-2" name="email" placeholder="Email" required>
                <input type="text" class="form-control mb-2" name="phone" placeholder="Phone" required>
                <input type="number" class="form-control mb-2" name="salary" placeholder="Salary" required>
                <input type="text" class="form-control mb-2" name="address" placeholder="Address" required>
                <select name="status" class="form-control mb-2" required>
                    <option value="">Select Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" type="submit">Save</button>
            </div>
        </form>
    </div>
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
        <div class="modal-body">
            <input type="text" class="form-control mb-2" name="name" id="editName" placeholder="Name" required>
            <input type="email" class="form-control mb-2" name="email" id="editEmail" placeholder="Email" required>
            <input type="text" class="form-control mb-2" name="phone" id="editPhone" placeholder="Phone" required>
            <input type="number" class="form-control mb-2" name="salary" id="editSalary" placeholder="Salary" required>
            <input type="text" class="form-control mb-2" name="address" id="editAddress" placeholder="Address" required>
            <select name="status" class="form-control mb-2" id="editStatus" required>
                <option value="">Select Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <div class="modal-footer">
            <button class="btn btn-primary" type="submit">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

@include('admin.employees.create')
@include('admin.employees.edit')
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
                    alert('Employee saved successfully!');
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
                    alert(errorMessages);
                } else {
                    alert('Error: '+xhr.status);
                }
            }
        });
    });

    $(document).on('click', '.edit', function(){
        let id = $(this).data('id');
        $.get("/admin/employees/" + id + "/edit", function(employee){
            $('#editEmployeeId').val(employee.id);
            $('#editName').val(employee.name);
            $('#editEmail').val(employee.email);
            $('#editPhone').val(employee.phone);
            $('#editSalary').val(employee.salary);
            $('#editAddress').val(employee.address);
            $('#editStatus').val(employee.status);
            $('#editEmployeeModal').modal('show');
        });
    });

    $('#editEmployeeForm').on('submit', function(e){
        e.preventDefault();
        let id = $('#editEmployeeId').val();
        $.ajax({
            url: "/admin/employees/" + id,
            type: 'POST',
            data: $(this).serialize(),
            success: function(response){
                if(response.success){
                    $('#editEmployeeModal').modal('hide');
                    alert('Employee updated successfully!');
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
                    alert(errorMessages);
                } else {
                    alert('Error: '+xhr.status);
                }
            }
        });
    });

     $(document).on('click', '.delete', function(){
        if(!confirm('Are you sure you want to delete this employee?')) return;

        let id = $(this).data('id');
        $.ajax({
            url: "/admin/employees/" + id,
            type: 'POST',
            data: {
                _method: 'DELETE',
                _token: "{{ csrf_token() }}"
            },
            success: function(response){
                if(response.success){
                    alert('Employee deleted successfully!');
                    $('#row' + id).remove();
                }
            },
            error: function(xhr){
                console.log(xhr.responseText);
                alert('Error: ' + xhr.status);
            }
        });
    });

});

</script>
@endsection
