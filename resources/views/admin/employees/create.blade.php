{{--
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
            <input class="form-control mb-2" name="name" placeholder="Name">
            <input class="form-control mb-2" name="email" placeholder="Email">
            <input class="form-control mb-2" name="phone" placeholder="Phone">
            <input class="form-control mb-2" name="salary" placeholder="Salary">
            <input class="form-control mb-2" name="address" placeholder="Address">
        </div>

        <div class="modal-footer">
            <button class="btn btn-primary" type="submit">Save</button>
        </div>
      </form>
    </div>
  </div>
</div> --}}
