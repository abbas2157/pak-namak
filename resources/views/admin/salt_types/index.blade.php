@extends('admin.layout.app')
@section('title','Salt Types List')

@section('content')
<div class="container-fluid mt-2">

    <div class="d-flex justify-content-between mb-3">
        <h3 class="mt-3">Salt Types List</h3>
        <button class="btn btn-primary rounded-pill mt-3" id="addBtn">
            <i class="fas fa-plus"></i> Add Salt Type
        </button>
    </div>

    <table class="table table-bordered table-striped" id="typesTable">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Salt Type</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($types as $type)
            <tr id="row_{{ $type->id }}">
                <td>{{ $loop->iteration }}</td>
                <td>{{ $type->title }}</td>
                <td>{{ $type->created_at->format('Y-m-d') }}</td>
                <td>
                    <button class="btn btn-info btn-sm editBtn" data-id="{{ $type->id }}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-sm deleteBtn" data-id="{{ $type->id }}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>

<!-- Modal for Add/Edit -->
<div class="modal fade" id="typeModal">
    <div class="modal-dialog">
        <form id="typeForm">
            @csrf
            <input type="hidden" name="id" id="id">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add/Edit Salt Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" id="title" class="form-control" required>
                        <span class="text-danger" id="titleError"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Save</button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(function () {

    // Show Add Modal
    $('#addBtn').click(function () {
        $('#typeForm')[0].reset();
        $('#id').val('');
        $('#titleError').text('');
        $('#typeModal').modal('show');
    });

    // Submit Form (Add / Edit)
    $('#typeForm').submit(function (e) {
        e.preventDefault();
        let id = $('#id').val();
        let url = id ? `/admin/salt-types/${id}` : "{{ route('admin.salt-types.store') }}";

        $.ajax({
            url: url,
            type: id ? 'PUT' : 'POST',
            data: $(this).serialize(),
            success: function (res) {
                $('#typeModal').modal('hide');

                // Reload table row or add new row dynamically
                let row = `
                    <tr id="row_${res.id}">
                        <td>${res.id}</td>
                        <td>${res.title}</td>
                        <td>${res.created_at}</td>
                        <td>
                            <button class="btn btn-info btn-sm editBtn" data-id="${res.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm deleteBtn" data-id="${res.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                if(id) {
                    $(`#row_${id}`).replaceWith(row);
                    toastr.success('Updated successfully!');
                } else {
                    $('#typesTable tbody').append(row);
                    toastr.success('Added successfully!');
                }
            },
            error: function (xhr) {
                let err = xhr.responseJSON.errors;
                if(err && err.title) {
                    $('#titleError').text(err.title[0]);
                } else {
                    toastr.error('Something went wrong!');
                }
            }
        });
    });

    // Edit Button Click
    $(document).on('click', '.editBtn', function () {
        let id = $(this).data('id');

        $.get(`/admin/salt-types/${id}/edit`, function (res) {
            $('#id').val(res.id);
            $('#title').val(res.title);
            $('#typeModal').modal('show');
        });
    });

    // Delete Button Click
    $(document).on('click', '.deleteBtn', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: "Are you sure?",
            text: "This will delete the salt type!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if(result.isConfirmed) {
                $.ajax({
                    url: `/admin/salt-types/${id}`,
                    type: 'DELETE',
                    data: {_token: '{{ csrf_token() }}'},
                    success: function () {
                        $(`#row_${id}`).remove();
                        toastr.success('Deleted successfully!');
                    },
                    error: function () {
                        toastr.error('Something went wrong!');
                    }
                });
            }
        });
    });

});
</script>
@endsection
