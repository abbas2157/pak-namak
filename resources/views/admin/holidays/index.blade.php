@extends('admin.layout.app')
@section('title', 'Holidays')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1>Holidays <small class="text-muted">تعطیلات</small></h1>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.employees.index') }}">Employees</a></li>
                    <li class="breadcrumb-item active">Holidays</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Add Holiday / تعطیل شامل کریں</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.holidays.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label>Date / تاریخ <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Title / تفصیل <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" placeholder="e.g. Eid-ul-Fitr" required>
                            </div>
                            @error('date')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
                            @error('title')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-plus mr-1"></i> Add Holiday
                            </button>
                        </form>
                    </div>
                </div>
                <p class="text-muted small px-1">
                    Every {{ ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][config('admin.weekly_holiday', 0)] }}
                    is also treated as a weekly off automatically — no need to add it here.
                </p>
            </div>

            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Holiday List / تعطیلات کی فہرست</h4>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered table-sm mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>Title</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($holidays as $holiday)
                                <tr>
                                    <td>{{ $holiday->date->format('d M Y (D)') }}</td>
                                    <td>{{ $holiday->title }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.holidays.destroy', $holiday->id) }}" onsubmit="return confirm('Remove this holiday?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-xs" type="submit">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted">No holidays added yet.</td>
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
@endsection
