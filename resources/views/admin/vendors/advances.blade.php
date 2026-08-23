@extends('admin.layout.app')
@section('title', 'Vendor Advances')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Vendor Advances <small class="text-muted pn-stat-sub">وینڈر ایڈوانس</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.vendors.index') }}">Vendors</a></li>
                    <li class="breadcrumb-item active">Advances</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end">
                <a href="{{ route('admin.vendors.advance_form') }}" class="btn btn-primary btn-pn px-4">
                    <i class="fas fa-plus mr-1"></i> Send Advance / ایڈوانس بھیجیں
                </a>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        {{-- ===== STAT CARDS ===== --}}
        <div class="row mb-3">
            <div class="col-6 col-md-4 mb-2">
                <div class="card border-0 shadow-sm h-100 pn-bl-teal">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Total Sent / کل بھیجی گئی</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($totalSent, 0) }}</div>
                                <small class="text-muted">PKR</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-teal">
                                <i class="fas fa-money-bill-transfer"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 mb-2">
                <div class="card border-0 shadow-sm h-100 pn-bl-blue">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Applied to Purchases / استعمال شدہ</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($totalApplied, 0) }}</div>
                                <small class="text-muted">PKR</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-blue">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 mb-2">
                <div class="card border-0 shadow-sm h-100 pn-bl-yellow">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Unused Credit / غیر استعمال شدہ</div>
                                <div class="h5 mb-0 font-weight-bold text-dark">{{ number_format($totalRemaining, 0) }}</div>
                                <small class="text-muted">PKR</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-yellow">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== TABLE ===== --}}
        <div class="card border-0 shadow-sm card-pn">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 font-weight-bold text-c-blue2">
                    <i class="fas fa-table mr-2"></i>Advance Records / ایڈوانس کی فہرست
                </h6>
                <span class="badge pn-bdg pn-bdg-blue">{{ $advances->count() }} advances</span>
            </div>
            <div class="card-body p-2">
                <div class="table-responsive">
                    <table class="table mb-0 pn-table pn-table-font">
                        <thead>
                            <tr>
                                <th class="pl-4">Date / تاریخ</th>
                                <th>Vendor / سپلائر</th>
                                <th class="text-right">Sent / بھیجا گیا</th>
                                <th class="text-right">Applied / استعمال شدہ</th>
                                <th class="text-right">Remaining / باقی</th>
                                <th>Applied To / کہاں استعمال ہوا</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($advances as $advance)
                                <tr>
                                    <td class="pl-4 align-middle">
                                        <span class="font-weight-bold d-block pn-text-heading">
                                            {{ \Carbon\Carbon::parse($advance->advance_date)->format('d M Y') }}
                                        </span>
                                    </td>
                                    <td class="align-middle">{{ $advance->vendor?->name ?? '—' }}</td>
                                    <td class="align-middle text-right">{{ number_format($advance->amount, 0) }}</td>
                                    <td class="align-middle text-right">{{ number_format($advance->applied_amount, 0) }}</td>
                                    <td class="align-middle text-right">
                                        @if($advance->remaining_amount > 0)
                                            <span class="font-weight-bold text-c-orange">{{ number_format($advance->remaining_amount, 0) }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @forelse($advance->applied_to as $use)
                                            <span class="d-block small">
                                                {{ $use->type }} #{{ $use->ref }}
                                                <span class="text-muted">— {{ number_format($use->amount, 0) }}</span>
                                            </span>
                                        @empty
                                            <span class="text-muted small">Not yet applied</span>
                                        @endforelse
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fas fa-money-bill-transfer fa-3x mb-3 d-block icon-fade"></i>
                                        <p class="text-muted mb-0">No vendor advances sent yet.</p>
                                        <a href="{{ route('admin.vendors.advance_form') }}" class="btn btn-sm btn-primary btn-pn mt-3">
                                            <i class="fas fa-plus mr-1"></i> Send First Advance
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
