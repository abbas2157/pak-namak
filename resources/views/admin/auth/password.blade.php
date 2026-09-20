@extends('admin.layout.app')
@section('title', 'Change Password')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Change Password <small class="text-muted ch-sub">پاس ورڈ تبدیل کریں</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Change Password</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">

                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm card-pn">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    </div>
                @endif

                <div class="card card-pn border-0 shadow-sm">
                    <div class="card-header border-bottom py-3 ch-header-blue">
                        <h6 class="mb-0 font-weight-bold text-white">
                            <i class="fas fa-key mr-2"></i>Admin Password / ایڈمن پاس ورڈ
                        </h6>
                    </div>
                    <div class="card-body py-4 px-4">
                        <div class="alert alert-light border mb-4 py-2 px-3 small">
                            <i class="fas fa-shield-halved mr-1 text-c-red"></i>
                            The password will <strong>not</strong> change unless the <strong>security key</strong> is entered correctly.
                            <span class="text-muted d-block">سیکیورٹی کی کے بغیر پاس ورڈ تبدیل نہیں ہوگا۔</span>
                        </div>

                        <form method="POST" action="{{ route('admin.password.update') }}" autocomplete="off">
                            @csrf

                            <div class="mb-3">
                                <label class="filter-lbl">Security Key / سیکیورٹی کی <span class="text-danger">*</span></label>
                                <div class="pw-field">
                                    <input type="password" name="security_key" id="security_key" class="form-control fc-pn @error('security_key') is-invalid @enderror" autocomplete="off" required>
                                    <button type="button" class="pw-toggle" data-target="#security_key" title="Show / hide"><i class="fas fa-eye"></i></button>
                                </div>
                                @error('security_key') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="filter-lbl">Current Password / موجودہ پاس ورڈ <span class="text-danger">*</span></label>
                                <div class="pw-field">
                                    <input type="password" name="current_password" id="current_password" class="form-control fc-pn @error('current_password') is-invalid @enderror" autocomplete="current-password" required>
                                    <button type="button" class="pw-toggle" data-target="#current_password" title="Show / hide"><i class="fas fa-eye"></i></button>
                                </div>
                                @error('current_password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="filter-lbl">New Password / نیا پاس ورڈ <span class="text-danger">*</span></label>
                                    <div class="pw-field">
                                        <input type="password" name="password" id="password" class="form-control fc-pn @error('password') is-invalid @enderror" autocomplete="new-password" minlength="8" required>
                                        <button type="button" class="pw-toggle" data-target="#password" title="Show / hide"><i class="fas fa-eye"></i></button>
                                    </div>
                                    @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    <small class="text-muted">At least 8 characters.</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="filter-lbl">Confirm New Password / تصدیق <span class="text-danger">*</span></label>
                                    <div class="pw-field">
                                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control fc-pn" autocomplete="new-password" minlength="8" required>
                                        <button type="button" class="pw-toggle" data-target="#password_confirmation" title="Show / hide"><i class="fas fa-eye"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-2">
                                <a href="{{ route('dashboard') }}" class="btn btn-light btn-pn px-4 mr-2">Cancel</a>
                                <button type="submit" class="btn btn-primary btn-pn px-4">
                                    <i class="fas fa-save mr-1"></i> Change Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    // Show / hide password fields
    $(document).on('click', '.pw-toggle', function () {
        const input = $($(this).data('target'));
        const show = input.attr('type') === 'password';
        input.attr('type', show ? 'text' : 'password');
        $(this).find('i').toggleClass('fa-eye', !show).toggleClass('fa-eye-slash', show);
    });
</script>
@endsection
