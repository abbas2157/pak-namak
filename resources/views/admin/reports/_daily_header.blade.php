{{-- Shared page header for the daily reports: title, month picker, sibling link, print.
     Expects: $title, $urdu, $month, $months, $siblingRoute, $siblingLabel, $siblingIcon --}}
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">{{ $title }} <small class="text-muted ch-sub">{{ $urdu }}</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">{{ $title }} — {{ $month->format('F Y') }}</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end align-items-center gap-2 no-print">
                <form method="GET" class="form-inline mb-0">
                    <select name="month" class="form-control fc-pn mr-2" onchange="this.form.submit()">
                        @foreach($months as $ym => $label)
                            <option value="{{ $ym }}" {{ $ym === $month->format('Y-m') ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route($siblingRoute, request()->only('month')) }}" class="btn btn-outline-secondary btn-modal-cancel">
                    <i class="fas {{ $siblingIcon }} mr-1"></i> {{ $siblingLabel }}
                </a>
                <button class="btn btn-outline-secondary btn-modal-cancel" onclick="window.print()">
                    <i class="fas fa-print mr-1"></i> Print / PDF
                </button>
            </div>
        </div>
    </div>
</section>
