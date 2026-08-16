@extends('admin.layout.app')
@section('title','Spice Types')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Spice Types <small class="text-muted">مصالحہ کی اقسام</small></h1>
                </div>
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Spice Types</li>
                        </ol>
                    </div>
                    <div class="col-sm-6 d-flex justify-content-end">
                        <button class="btn btn-primary mb-3 float-right mt-3 px-3 mr-3 rounded-pill" id="addBtn">
                            <i class="fas fa-plus"></i> Add Spice Type / قسم شامل کریں
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <table class="table table-bordered table-striped" id="typesTable">
                <thead class="thead-dark">
                    <tr>
                        <th>Spice / مصالحہ</th>
                        <th>Created At / تاریخ</th>
                        <th>Action / اقدام</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($types as $type)
                    <tr id="row_{{ $type->id }}">
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
    </section>
<!-- Modal for Add/Edit -->
<div class="modal fade" id="typeModal">
    <div class="modal-dialog">
        <form id="typeForm">
            @csrf
            <input type="hidden" name="id" id="id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add/Edit Spice Type / قسم شامل/ترمیم</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Title / عنوان</label>
                        <input type="text" name="title" id="title" class="form-control" required placeholder="e.g. Chilli Powder">
                        <span class="text-danger" id="titleError"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Save / محفوظ کریں</button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(function () {

    $('#addBtn').click(function () {
        $('#typeForm')[0].reset();
        $('#id').val('');
        $('#titleError').text('');
        $('#typeModal').modal('show');
    });

    $('#typeForm').submit(function (e) {
        e.preventDefault();
        let id = $('#id').val();
        let url = id ? APP_URL + `/admin/spice-types/${id}` : "{{ route('admin.spice-types.store') }}";

        $.ajax({
            url: url,
            type: id ? 'PUT' : 'POST',
            data: $(this).serialize(),
            success: function (res) {
                $('#typeModal').modal('hide');
                let row = `
                    <tr id="row_${res.id}">
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

    $(document).on('click', '.editBtn', function () {
        let id = $(this).data('id');

        $.get( APP_URL + `/admin/spice-types/${id}/edit`, function (res) {
            $('#id').val(res.id);
            $('#title').val(res.title);
            $('#typeModal').modal('show');
        });
    });

    $(document).on('click', '.deleteBtn', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: "Are you sure?",
            text: "This will delete the spice type!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if(result.isConfirmed) {
                $.ajax({
                    url: APP_URL + `/admin/spice-types/${id}`,
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
