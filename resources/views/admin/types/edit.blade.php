{{-- @extends('admin.layout.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Salt Types</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Edit Salt Types</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Edit Salt Types</h3>
                        </div>
                        <div class="card-body">
                            @if(Session::has('success'))
                                <div class="alert alert-success">
                                    {{ Session::get('success') }}
                                </div>
                            @endif
                            <form action="{{ route('admin.salt-types.update',$type->id)}}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" name="title" id="title" class="form-control"
                                        placeholder="Enter title" value="{{ $type->title}}">
                                    @error('title')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="{{ route('salt-types.index') }}" class="btn btn-secondary">Back</a>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection --}}
