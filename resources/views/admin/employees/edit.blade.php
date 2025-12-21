{{-- <!-- EDIT MODAL -->
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
        <input type="hidden" name="employee_id" id="edit_employee_id">

        <div class="modal-body">
            <input class="form-control mb-2" name="name" id="edit_name">
            <input class="form-control mb-2" name="email" id="edit_email">
            <input class="form-control mb-2" name="phone" id="edit_phone">
            <input class="form-control mb-2" name="salary" id="edit_salary">
            <input class="form-control mb-2" name="address" id="edit_address">
        </div>

        <div class="modal-footer">
            <button class="btn btn-primary" type="submit">Update</button>
        </div>
      </form>
    </div>
  </div>
</div> --}}
