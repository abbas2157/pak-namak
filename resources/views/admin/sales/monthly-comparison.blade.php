@extends('admin.layout.app')
@section('title', 'Salt Monthly Comparison')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Salt Monthly Comparison <small class="text-muted">نمک ماہانہ موازنہ</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sales.report') }}">Sales Report</a></li>
                    <li class="breadcrumb-item active">Monthly Comparison</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end gap-2 no-print">
                <a href="{{ route('admin.spice-sales.report.monthly', request()->only('from', 'to')) }}"
                   class="btn btn-outline-secondary btn-modal-cancel">
                    <i class="fas fa-pepper-hot mr-1"></i> Spices
                </a>
                <button class="btn btn-outline-secondary btn-modal-cancel" onclick="window.print()">
                    <i class="fas fa-print mr-1"></i> Print / PDF
                </button>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        @include('admin.reports._monthly_matrix', [
            'routeName'    => 'admin.sales.report.monthly',
            'title'        => 'Dalla · Thaila · Package by Size',
            'urdu'         => 'سائز کے مطابق',
            'groupNoun'    => 'product type',
            'emptyMessage' => 'No salt sales found in the selected months.',
        ])
    </div>
</section>
@endsection

@section('scripts')
    @include('admin.reports._monthly_matrix_scripts', ['routeName' => 'admin.sales.report.monthly'])
@endsection
