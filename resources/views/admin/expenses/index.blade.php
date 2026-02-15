@extends('admin.layout.app')
@section('title', 'Expenses')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Expenses List</h1>
                </div>
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Expenses List</li>
                        </ol>
                    </div>
                    <div class="col-sm-6 d-flex justify-content-end">
                        <button class="btn btn-primary mb-3 float-right mt-3 px-3 mr-3 rounded-pill" id="addBtn">
                            <i class="fas fa-plus"></i> Add Expense
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <table class="table table-bordered table-striped" id="expenseTable">
                <thead class="thead-dark">
                    <tr>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expenses as $expense)
                        <tr id="row_{{ $expense->id }}">
                            <td>{{ $expense->category ?? '' }}</td>
                            <td>{{ number_format($expense->amount, 2) }}</td>
                            <td>{{ $expense->payment_method ?? '' }}</td>
                            <td>{{ $expense->expense_date ?? '' }}</td>
                            <td>
                                <button class="btn btn-sm btn-info editBtn" data-id="{{ $expense->id }}">Edit</button>
                                <button class="btn btn-sm btn-danger deleteBtn" data-id="{{ $expense->id }}">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    <div class="modal fade" id="ExpenseModal">
        <div class="modal-dialog modal-lg">
            <form id="ExpenseForm">
                @csrf
                <input type="hidden" id="id" name="id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Expense</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label>Date</label>
                                <input type="date" id="expense_date" name="expense_date" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Category</label>
                                <select name="category" id="category" class="form-control" required>
                                    <option value="">-- Select Category --</option>
                                    <option value="Fuel">Fuel</option>
                                    <option value="Vehicle Maintenance">Vehicle Maintenance</option>
                                    <option value="Food">Food</option>
                                    <option value="Salaries">Salaries</option>
                                    <option value="Others">Others</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Payment Method</label>
                                <select name="payment_method" id="payment_method" class="form-control" required>
                                    <option value="Cash">Cash</option>
                                    <option value="JazzCash">JazzCash</option>
                                    <option value="EasyPaisa">EasyPaisa</option>
                                    <option value="Bank">Bank</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Amount</label>
                                <input type="number" id="amount" step="0.01" name="amount" class="form-control" required>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label>Remarks</label>
                            <textarea name="remarks" id="remarks" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-success" type="submit">Add/Update Expense</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {
            $('#ExpenseModal').on('hidden.bs.modal', function () {
                $(this).find(':focus').blur();
            });
            $('#addBtn').click(function() {
                $('#ExpenseForm')[0].reset();
                $('#id').val('');
                $('#ExpenseModal').modal('show');
            });
            $(document).on('submit', '#ExpenseForm', function(e) {
                e.preventDefault();
                let id = $('#id').val();
                let url = id ? APP_URL + "/admin/expenses/" + id : "{{ route('admin.expenses.store') }}";
                let method = id ? "PUT" : "POST";
                $.ajax({
                    url: url,
                    type: "POST",
                    data: $(this).serialize() + (method === "PUT" ? '&_method=PUT' : ''),
                    success: function(expense) {
                        let row = `<tr id="row_${expense.id}">
                                <td>${expense.category}</td>
                                <td>${expense.amount}</td>
                                <td>${expense.payment_method}</td>
                                <td>${expense.expense_date}</td>
                                <td>
                                    <button class="btn btn-sm btn-info editBtn" data-id="${expense.id}">Edit</button>
                                    <button class="btn btn-sm btn-danger deleteBtn" data-id="${expense.id}">Delete</button>
                                </td>
                            </tr>`;

                        if (id) {
                            $('#row_' + id).replaceWith(row);
                        } else {
                            $('#expenseTable tbody').append(row);
                        }
                        toastr.success('Expense Saved successfully!');
                        $('#ExpenseModal').modal('hide');
                    },
                    error: function(xhr) {
                        toastr.error('Something went wrong');
                        console.log(xhr.responseText);
                    }
                });
            });

            $(document).on('click', '.editBtn', function() {
                let id = $(this).data('id');
                $.ajax({
                    url: APP_URL + "/admin/expenses/" + id + "/edit",
                    type: "GET",
                    dataType: 'json',
                    success: function(expense) {
                        $('#id').val(expense.id);
                        $('#expense_date').val(expense.expense_date);
                        $('#category').val(expense.category);
                        $('#payment_method').val(expense.payment_method);
                        $('#amount').val(expense.amount);
                        $('#remarks').val(expense.remarks);
                        $('#ExpenseModal').modal('show');
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });

            $(document).on('click', '.deleteBtn', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: APP_URL + "/admin/expenses/" + id,
                            type: "POST",
                            data: {
                                _method: "DELETE",
                                _token: "{{ csrf_token() }}"
                            },
                            success: function() {
                                $('#row_' + id).remove();

                                Swal.fire({
                                    title: "Deleted!",
                                    text: "Your expense has been deleted.",
                                    icon: "success",
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: "Error!",
                                    text: "Something went wrong.",
                                    icon: "error"
                                });
                                console.log(xhr.responseText);
                            }
                        });
                    }
                });
            });
        });
    </script>
    <script>
        $(function () {
            $('#expenseTable').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "buttons": ["csv", "excel", "pdf"]
            }).buttons().container().appendTo('#shopTable_wrapper .col-md-6:eq(0)');
        });
    </script>
@endsection
