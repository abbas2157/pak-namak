@extends('admin.layout.app')
@section('title', 'Orders')

@section('content')
<style>
    .orders-header { background: linear-gradient(135deg, #0a2e18, #1a5c35); border-radius: 14px; padding: 24px 28px; margin-bottom: 24px; color: #fff; }
    .stat-pill { background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.15); border-radius: 12px; padding: 14px 18px; text-align: center; transition: background .15s; cursor: pointer; text-decoration: none; display: block; }
    .stat-pill:hover { background: rgba(255,255,255,.18); text-decoration: none; }
    .stat-pill.active-pending   { background: rgba(246,194,62,.25);  border-color: rgba(246,194,62,.5); }
    .stat-pill.active-confirmed { background: rgba(28,200,138,.25);  border-color: rgba(28,200,138,.5); }
    .stat-pill.active-rejected  { background: rgba(231,74,59,.25);   border-color: rgba(231,74,59,.5); }
    .stat-pill.active-all       { background: rgba(255,255,255,.2);  border-color: rgba(255,255,255,.4); }
    .stat-num  { font-size: 26px; font-weight: 800; line-height: 1; color: #fff; }
    .stat-lbl  { font-size: 10px; text-transform: uppercase; letter-spacing: .7px; color: rgba(255,255,255,.6); margin-top: 4px; }
    .stat-dot  { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 5px; }

    .filter-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 8px rgba(0,0,0,.07); padding: 16px 20px; margin-bottom: 18px; }

    .orders-table th { background: #f2f5f3; font-size: 11px; text-transform: uppercase; letter-spacing: .5px; color: #6c757d; font-weight: 700; padding: 12px 14px; border-bottom: 2px solid #e0ebe4; white-space: nowrap; }
    .orders-table td { padding: 12px 14px; vertical-align: middle; border-bottom: 1px solid #f0f4f2; }
    .orders-table tbody tr:hover { background: #f8fdf9; }

    .ref-link { font-weight: 700; color: #1a5c35; text-decoration: none; font-size: 13px; }
    .ref-link:hover { color: #2d7a4f; text-decoration: underline; }

    .type-badge { display: inline-block; font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 20px; margin: 1px 2px 1px 0; white-space: nowrap; }
    .tb-dalla   { background: #d0e8d8; color: #1a5c35; }
    .tb-thaila  { background: #d4edda; color: #155724; }
    .tb-package { background: #fff3cd; color: #856404; }

    .status-pill { display: inline-block; font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 20px; white-space: nowrap; }
    .sp-pending   { background: #fff3cd; color: #856404; }
    .sp-confirmed { background: #d4edda; color: #155724; }
    .sp-rejected  { background: #fce8e6; color: #c62828; }

    .action-btn { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 7px; border: none; cursor: pointer; font-size: 12px; transition: opacity .15s; text-decoration: none; }
    .ab-view    { background: #d0e8d8; color: #1a5c35; }
    .ab-confirm { background: #d4edda; color: #155724; }
    .ab-reject  { background: #fce8e6; color: #c62828; }
    .ab-sale    { background: #fff3cd; color: #856404; }
    .action-btn:hover { opacity: .75; }

    .empty-state { text-align: center; padding: 60px 20px; color: #b0b7c3; }
    .empty-state i { font-size: 3rem; margin-bottom: 14px; display: block; opacity: .4; }

    .per-page-select { border: 1px solid #dee2e6; border-radius: 8px; padding: 5px 10px; font-size: 13px; color: #555; background: #fff; }
    .page-link { color: #1a5c35 !important; }
    .page-item.active .page-link { background: #1a5c35 !important; border-color: #1a5c35 !important; color: #fff !important; }

    .shop-name { font-weight: 600; color: #2d3748; font-size: 13px; }
    .shop-meta { font-size: 11px; color: #9ca3af; }
    .new-badge { background: #e9ecef; color: #6c757d; font-size: 9px; font-weight: 700; padding: 1px 6px; border-radius: 10px; vertical-align: middle; }

    .today-badge { background: rgba(246,194,62,.2); color: #856404; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; border: 1px solid rgba(246,194,62,.4); }
</style>

<section class="content-header">
    <div class="container-fluid">
        <ol class="breadcrumb mb-0 mt-1">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Orders</li>
        </ol>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" style="border-radius:10px;">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        {{-- ── Header + Stats ─────────────────────────────────── --}}
        <div class="orders-header">
            <div class="d-flex align-items-start justify-content-between flex-wrap mb-4">
                <div>
                    <h4 class="mb-1 font-weight-bold"><i class="fas fa-inbox mr-2" style="opacity:.8;"></i>Orders</h4>
                    <div style="font-size:12px;opacity:.6;">Manage and process incoming customer orders</div>
                </div>
                <div class="d-flex align-items-center gap-2 mt-2 mt-sm-0">
                    @if($counts['today'] > 0)
                        <span class="today-badge"><i class="fas fa-calendar-day mr-1"></i>{{ $counts['today'] }} today</span>
                    @endif
                    @if($counts['pending'] > 0)
                        <span style="background:rgba(246,194,62,.25);border:1px solid rgba(246,194,62,.5);color:#ffc107;font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px;">
                            <i class="fas fa-clock mr-1"></i>{{ $counts['pending'] }} pending action
                        </span>
                    @endif
                </div>
            </div>

            <div class="row g-2">
                @foreach([
                    ['all',       'All Orders',  $counts['all'],       '#fff',    'fa-layer-group'],
                    ['pending',   'Pending',      $counts['pending'],   '#f6c23e', 'fa-clock'],
                    ['confirmed', 'Confirmed',    $counts['confirmed'], '#1cc88a', 'fa-check-circle'],
                    ['rejected',  'Rejected',     $counts['rejected'],  '#e74a3b', 'fa-times-circle'],
                ] as [$s, $lbl, $cnt, $color, $icon])
                <div class="col-6 col-md-3 mb-2">
                    <a href="{{ request()->fullUrlWithQuery(['status' => $s, 'page' => 1]) }}"
                       class="stat-pill {{ $status === $s ? 'active-'.$s : '' }}">
                        <div class="stat-num" style="color:{{ $color }};">{{ number_format($cnt) }}</div>
                        <div class="stat-lbl">
                            <span class="stat-dot" style="background:{{ $color }};"></span>{{ $lbl }}
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ── Filters ─────────────────────────────────────────── --}}
        <div class="filter-card">
            <form method="GET" action="{{ route('admin.orders.index') }}" id="filterForm">
                <input type="hidden" name="status" value="{{ $status }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4 col-sm-6">
                        <label class="d-block" style="font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px;">Search</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="background:#f2f5f3;border-color:#dee2e6;">
                                    <i class="fas fa-search" style="color:#9ca3af;font-size:11px;"></i>
                                </span>
                            </div>
                            <input type="text" name="search" value="{{ $search }}"
                                   class="form-control" style="border-radius:0 8px 8px 0;font-size:13px;"
                                   placeholder="Reference, shop, phone, city…">
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <label class="d-block" style="font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px;">From</label>
                        <input type="date" name="date_from" value="{{ $dateFrom }}"
                               class="form-control form-control-sm" style="border-radius:8px;font-size:13px;">
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <label class="d-block" style="font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px;">To</label>
                        <input type="date" name="date_to" value="{{ $dateTo }}"
                               class="form-control form-control-sm" style="border-radius:8px;font-size:13px;">
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <label class="d-block" style="font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px;">Per page</label>
                        <select name="per_page" class="per-page-select form-control form-control-sm" onchange="this.form.submit()" style="border-radius:8px;">
                            @foreach([20, 50, 100] as $n)
                                <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }} rows</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-primary flex-grow-1" style="border-radius:8px;">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                        @if($search || $dateFrom || $dateTo)
                            <a href="{{ route('admin.orders.index', ['status' => $status]) }}"
                               class="btn btn-sm btn-outline-secondary" style="border-radius:8px;" title="Clear filters">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- ── Table ───────────────────────────────────────────── --}}
        <div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden;">

            {{-- Table toolbar --}}
            <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-bottom:1px solid #e0ebe4;background:#fafcfa;">
                <div style="font-size:13px;color:#6c757d;">
                    @if($orders->total() > 0)
                        Showing <strong>{{ $orders->firstItem() }}</strong>–<strong>{{ $orders->lastItem() }}</strong>
                        of <strong>{{ number_format($orders->total()) }}</strong> orders
                        @if($search || $dateFrom || $dateTo)
                            <span class="badge badge-secondary ml-1" style="font-size:10px;">filtered</span>
                        @endif
                    @else
                        No orders found
                    @endif
                </div>
                <div class="d-flex gap-1">
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'pending', 'page' => 1]) }}"
                       class="btn btn-sm {{ $status === 'pending' ? 'btn-warning' : 'btn-outline-warning' }}"
                       style="border-radius:8px;font-size:12px;">
                        <i class="fas fa-clock mr-1"></i>Pending ({{ $counts['pending'] }})
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'all', 'page' => 1]) }}"
                       class="btn btn-sm {{ $status === 'all' ? 'btn-secondary' : 'btn-outline-secondary' }}"
                       style="border-radius:8px;font-size:12px;">
                        All
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table orders-table mb-0">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Shop / Customer</th>
                            <th>Items Ordered</th>
                            <th>Date</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($orders as $order)
                        <tr>
                            {{-- Reference --}}
                            <td style="width:130px;">
                                <a href="{{ route('admin.orders.show', $order) }}" class="ref-link">
                                    {{ $order->reference }}
                                </a>
                                @if($order->remarks)
                                    <i class="fas fa-comment-alt ml-1" style="color:#9ca3af;font-size:10px;" title="{{ $order->remarks }}"></i>
                                @endif
                            </td>

                            {{-- Shop / Customer --}}
                            <td>
                                <div class="shop-name">
                                    {{ $order->display_name }}
                                    @if(!$order->shop_id)
                                        <span class="new-badge ml-1">NEW</span>
                                    @endif
                                </div>
                                @if($order->display_phone !== '—')
                                    <div class="shop-meta"><i class="fas fa-phone mr-1" style="font-size:9px;"></i>{{ $order->display_phone }}</div>
                                @endif
                                @if($order->shop?->city ?? $order->city)
                                    <div class="shop-meta"><i class="fas fa-map-marker-alt mr-1" style="font-size:9px;"></i>{{ $order->shop?->city ?? $order->city }}</div>
                                @endif
                            </td>

                            {{-- Items --}}
                            <td style="max-width:220px;">
                                @foreach($order->items as $item)
                                    <span class="type-badge {{ $item->type === 'dalla' ? 'tb-dalla' : ($item->type === 'thaila' ? 'tb-thaila' : 'tb-package') }}">
                                        {{ $item->quantity }}×
                                        @if($item->type === 'dalla') Dalla
                                        @elseif($item->type === 'thaila') {{ $item->size }}kg bag
                                        @else {{ $item->size }}g pack
                                        @endif
                                    </span>
                                @endforeach
                            </td>

                            {{-- Date --}}
                            <td style="width:120px;white-space:nowrap;">
                                <div style="font-size:12px;color:#2d3748;font-weight:600;">{{ $order->created_at->format('d M Y') }}</div>
                                <div class="shop-meta">{{ $order->created_at->format('h:i A') }}</div>
                                @if($order->created_at->isToday())
                                    <span style="font-size:9px;font-weight:700;color:#1a5c35;text-transform:uppercase;letter-spacing:.5px;">Today</span>
                                @elseif($order->created_at->isYesterday())
                                    <span style="font-size:9px;color:#9ca3af;">Yesterday</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="text-center" style="width:110px;">
                                <span class="status-pill sp-{{ $order->status }}">
                                    @if($order->status === 'pending')
                                        <i class="fas fa-clock mr-1" style="font-size:9px;"></i>Pending
                                    @elseif($order->status === 'confirmed')
                                        <i class="fas fa-check mr-1" style="font-size:9px;"></i>Confirmed
                                    @else
                                        <i class="fas fa-times mr-1" style="font-size:9px;"></i>Rejected
                                    @endif
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="text-center" style="width:130px;white-space:nowrap;">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="action-btn ab-view mr-1" title="View details">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if($order->status === 'pending')
                                    <form action="{{ route('admin.orders.confirm', $order) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="action-btn ab-confirm mr-1" title="Confirm order" type="submit">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.orders.reject', $order) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="action-btn ab-reject mr-1" title="Reject order" type="submit">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @endif

                                @if(in_array($order->status, ['pending', 'confirmed']))
                                    <a href="{{ route('admin.orders.to_sale', $order) }}"
                                       class="action-btn ab-sale" title="Convert to Sale">
                                        <i class="fas fa-exchange-alt"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <p class="font-weight-bold mb-1" style="color:#6c757d;">No orders found</p>
                                <p style="font-size:13px;color:#b0b7c3;">
                                    @if($search || $dateFrom || $dateTo)
                                        Try adjusting your search or date filters.
                                    @else
                                        Orders placed through the public form will appear here.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($orders->hasPages())
            <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-top:1px solid #e0ebe4;background:#fafcfa;">
                <small class="text-muted">
                    Page {{ $orders->currentPage() }} of {{ $orders->lastPage() }}
                </small>
                {{ $orders->links() }}
            </div>
            @endif

        </div>

    </div>
</section>
@endsection
