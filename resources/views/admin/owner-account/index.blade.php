@extends('admin.layout.app')
@section('title', 'Owner Account')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-5">
                <h1 class="m-0">Owner Account <small class="text-muted pn-stat-sub">مالک کا کھاتہ</small></h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Owner Account</li>
                </ol>
            </div>
            <div class="col-sm-7 d-flex justify-content-end flex-wrap">
                <button class="btn btn-danger px-3 btn-pn addBtn mr-2 mb-1" data-type="taken">
                    <i class="fas fa-house mr-1"></i> Taken for Home / گھر کے لیے لیا
                </button>
                <button class="btn btn-success px-3 btn-pn addBtn mb-1" data-type="put_in">
                    <i class="fas fa-hand-holding-usd mr-1"></i> Own Money Put In / اپنی رقم لگائی
                </button>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        <div class="alert alert-light border small mb-3">
            <i class="fas fa-info-circle mr-1 text-c-blue2"></i>
            This is a personal record only. It does not affect sales, profit, investments or Cash &amp; Bank balances.
            <span class="d-block">یہ صرف ذاتی ریکارڈ ہے — اس کا کاروبار کے حساب پر کوئی اثر نہیں۔</span>
        </div>

        {{-- ===== STAT CARDS ===== --}}
        @php $periodLabel = $selectedMonth ? \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y') : 'All time'; @endphp
        <div class="row mb-3">
            <div class="col-md-4 mb-2">
                <div class="card border-0 shadow-sm h-100 pn-bl-red">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Taken for Home / گھر کے لیے لیا</div>
                                <div class="h5 mb-0 font-weight-bold text-c-red">{{ number_format($periodTaken, 0) }}</div>
                                <small class="text-muted">PKR · {{ $periodLabel }}</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-red"><i class="fas fa-house"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="card border-0 shadow-sm h-100 pn-bl-green">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Own Money Put In / اپنی رقم لگائی</div>
                                <div class="h5 mb-0 font-weight-bold text-c-green">{{ number_format($periodPutIn, 0) }}</div>
                                <small class="text-muted">PKR · {{ $periodLabel }}</small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-teal"><i class="fas fa-hand-holding-usd"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="card border-0 shadow-sm h-100 {{ $netBalance >= 0 ? 'pn-bl-green' : 'pn-bl-red' }}">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">Net Balance (all time) / خالص بیلنس</div>
                                <div class="h5 mb-0 font-weight-bold {{ $netBalance >= 0 ? 'text-c-green' : 'text-c-red' }}">
                                    {{ number_format(abs($netBalance), 0) }}
                                </div>
                                <small class="text-muted">
                                    @if($netBalance > 0)
                                        Pak Namak owes you / پاک نمک نے آپ کو دینے ہیں
                                    @elseif($netBalance < 0)
                                        You owe Pak Namak / آپ نے پاک نمک کو دینے ہیں
                                    @else
                                        Settled / برابر
                                    @endif
                                </small>
                            </div>
                            <div class="pn-icon pn-icon-md pni-blue"><i class="fas fa-scale-balanced"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- ===== TABLE ===== --}}
            <div class="col-lg-9 mb-3">
                <div class="card border-0 shadow-sm card-pn">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h6 class="mb-0 font-weight-bold text-c-blue2">
                                <i class="fas fa-table mr-2"></i>Transactions / لین دین
                            </h6>
                            <small class="text-muted">{{ $periodLabel }}</small>
                        </div>
                        <span class="badge pn-bdg pn-bdg-blue">{{ $transactions->count() }} records</span>
                    </div>
                    <div class="card-body p-2">
                        <div class="table-responsive">
                            <table class="table mb-0 pn-table pn-table-font" id="ownerTable">
                                <thead>
                                    <tr>
                                        <th class="pl-4">Date / تاریخ</th>
                                        <th>Type / قسم</th>
                                        <th>Purpose / مقصد</th>
                                        <th>Via / ذریعہ</th>
                                        <th class="text-right">Amount / رقم</th>
                                        <th class="text-right">Balance / بیلنس</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $t)
                                        @php $isPutIn = $t->type === \App\Models\OwnerTransaction::TYPE_PUT_IN; @endphp
                                        <tr>
                                            <td class="pl-4 align-middle">
                                                <span class="font-weight-bold d-block pn-text-heading">{{ $t->transaction_date->format('d M') }}</span>
                                                <small class="text-muted">{{ $t->transaction_date->format('Y') }}</small>
                                            </td>
                                            <td class="align-middle">
                                                <span class="badge {{ $isPutIn ? 'badge-success' : 'badge-danger' }}">
                                                    <i class="fas {{ $isPutIn ? 'fa-hand-holding-usd' : 'fa-house' }} mr-1"></i>{{ $t->typeLabel() }}
                                                </span>
                                            </td>
                                            <td class="align-middle col-desc">
                                                <span class="font-weight-bold d-block">{{ $t->purpose }}</span>
                                                @if($t->note)
                                                    <small class="text-muted d-block">{{ $t->note }}</small>
                                                @endif
                                            </td>
                                            <td class="align-middle">{{ $t->paid_via ?: '—' }}</td>
                                            <td class="align-middle text-right font-weight-bold pn-stat-num-sm {{ $isPutIn ? 'text-c-green' : 'text-c-red' }}">
                                                {{ $isPutIn ? '+' : '−' }}{{ number_format($t->amount, 0) }}
                                            </td>
                                            <td class="align-middle text-right font-weight-bold {{ $t->running_balance >= 0 ? 'text-c-green' : 'text-c-red' }}">
                                                {{ number_format($t->running_balance, 0) }}
                                            </td>
                                            <td class="align-middle text-center text-nowrap">
                                                <button class="btn btn-sm btn-pn btn-act-edit editBtn mr-1" data-id="{{ $t->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-pn btn-act-delete deleteBtn" data-id="{{ $t->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <i class="fas fa-user-tie fa-3x mb-3 d-block icon-fade"></i>
                                                <p class="text-muted mb-0">No owner transactions for this period.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <small class="text-muted d-block px-3 pt-2">
                            Balance column: + means Pak Namak owes you, − means you owe Pak Namak (as of that entry).
                        </small>
                    </div>
                </div>
            </div>

            {{-- ===== FILTERS ===== --}}
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm mb-3 card-pn">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 font-weight-bold text-c-blue2">
                            <i class="fas fa-filter mr-2"></i>Filter / فلٹر
                        </h6>
                    </div>
                    <div class="card-body py-3">
                        <form method="GET">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">Month / مہینہ</label>
                            <select name="month" class="form-control mb-2 fc-pn" onchange="this.form.submit()">
                                <option value="">All Time</option>
                                @foreach($months as $m)
                                    <option value="{{ $m['value'] }}" {{ $selectedMonth == $m['value'] ? 'selected' : '' }}>{{ $m['label'] }}</option>
                                @endforeach
                            </select>
                            <label class="pn-label text-uppercase font-weight-bold text-muted">Type / قسم</label>
                            <select name="type" class="form-control mb-2 fc-pn" onchange="this.form.submit()">
                                <option value="">All</option>
                                <option value="taken" {{ $selectedType === 'taken' ? 'selected' : '' }}>Taken for Home</option>
                                <option value="put_in" {{ $selectedType === 'put_in' ? 'selected' : '' }}>Own Money Put In</option>
                            </select>
                        </form>
                        @if($selectedMonth || $selectedType)
                            <a href="{{ route('admin.owner_account.index') }}" class="btn btn-block btn-sm btn-pn btn-clear-filter mt-1">
                                <i class="fas fa-times mr-1"></i> Clear Filters
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- ===== MODAL ===== --}}
<div class="modal fade modal-pn" id="OwnerModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="OwnerForm">
            @csrf
            <input type="hidden" id="owner_id">
            <div class="modal-content">
                <div class="modal-header px-4 py-3">
                    <h5 class="modal-title" id="modalTitle"></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body px-4 py-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">Type / قسم <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-control fc-pn" required>
                                <option value="taken">Taken for Home — Pak Namak money used for house</option>
                                <option value="put_in">Own Money Put In — my money used for Pak Namak</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">Date / تاریخ <span class="text-danger">*</span></label>
                            <input type="date" id="transaction_date" name="transaction_date" class="form-control fc-pn" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">Amount (PKR) / رقم <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text input-pre">PKR</span></div>
                                <input type="number" id="amount" step="0.01" min="0.01" name="amount" class="form-control fc-lg-pn" placeholder="0" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">Via / ذریعہ</label>
                            <select name="paid_via" id="paid_via" class="form-control fc-pn">
                                <option value="">—</option>
                                @foreach(\App\Models\OwnerTransaction::PAID_VIA as $via)
                                    <option value="{{ $via }}">{{ $via }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">Purpose / مقصد <span class="text-danger">*</span></label>
                            <input type="text" id="purpose" name="purpose" class="form-control fc-pn" maxlength="255" required
                                   placeholder="e.g. House rent, School fees, Paid labour from my pocket...">
                        </div>
                        <div class="col-12 mb-1">
                            <label class="pn-label text-uppercase font-weight-bold text-muted">Note / نوٹ</label>
                            <textarea name="note" id="note" class="form-control fc-pn" rows="2" placeholder="Optional..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3">
                    <button type="button" class="btn btn-light px-4 btn-modal-cancel" data-dismiss="modal" data-bs-dismiss="modal">Cancel / منسوخ</button>
                    <button class="btn btn-primary px-4 btn-modal-save" type="submit" id="submitBtn">
                        <i class="fas fa-save mr-1"></i> Save / محفوظ کریں
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(function () {
    const BASE = APP_URL + '/owner-account';
    const TITLES = {
        taken:  '<i class="fas fa-house mr-2"></i>Taken for Home / گھر کے لیے لیا',
        put_in: '<i class="fas fa-hand-holding-usd mr-2"></i>Own Money Put In / اپنی رقم لگائی',
    };

    $('#type').on('change', function () {
        if (!$('#owner_id').val()) $('#modalTitle').html(TITLES[this.value]);
    });

    $('.addBtn').on('click', function () {
        $('#OwnerForm')[0].reset();
        $('#owner_id').val('');
        $('#type').val($(this).data('type'));
        $('#modalTitle').html(TITLES[$(this).data('type')]);
        $('#transaction_date').val(new Date().toISOString().split('T')[0]);
        $('#OwnerModal').modal('show');
    });

    $(document).on('click', '.editBtn', function () {
        $.get(BASE + '/' + $(this).data('id') + '/edit', function (t) {
            $('#owner_id').val(t.id);
            $('#type').val(t.type);
            $('#transaction_date').val(t.transaction_date);
            $('#amount').val(t.amount);
            $('#paid_via').val(t.paid_via || '');
            $('#purpose').val(t.purpose);
            $('#note').val(t.note);
            $('#modalTitle').html('<i class="fas fa-edit mr-2"></i>Edit Entry / ترمیم');
            $('#OwnerModal').modal('show');
        });
    });

    // Totals and running balances depend on every row, so reload after any change.
    $('#OwnerForm').on('submit', function (e) {
        e.preventDefault();
        const id  = $('#owner_id').val();
        const btn = $('#submitBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        $.ajax({
            url: id ? BASE + '/' + id : BASE,
            type: 'POST',
            data: $(this).serialize() + (id ? '&_method=PUT' : ''),
            success: function () {
                toastr.success('Saved!');
                location.reload();
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    toastr.error(Object.values(xhr.responseJSON.errors).map(e => e[0]).join('<br>'));
                } else {
                    toastr.error('Something went wrong.');
                }
                btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save / محفوظ کریں');
            }
        });
    });

    $(document).on('click', '.deleteBtn', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Delete this entry?',
            text: 'This cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete',
        }).then(result => {
            if (!result.isConfirmed) return;
            $.ajax({
                url: BASE + '/' + id,
                type: 'POST',
                data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                success: function () { location.reload(); },
                error: function (xhr) { toastr.error('Error: ' + xhr.status); }
            });
        });
    });
});
</script>
@endsection
