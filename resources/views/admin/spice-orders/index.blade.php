@extends('admin.layout.app')
@section('title', 'Spice Orders')

@section('content')

<section class="content-header">
    <div class="container-fluid">
        <ol class="breadcrumb mb-0 mt-1">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Spice Orders</li>
        </ol>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show card-pn">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show card-pn">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        <div class="orders-header">
            <div class="d-flex align-items-start justify-content-between flex-wrap mb-4">
                <div>
                    <h4 class="mb-1 font-weight-bold"><i class="fas fa-pepper-hot mr-2 opacity-75"></i>Spice Orders / مصالحہ آرڈرز</h4>
                    <div class="pn-stat-sub">Manage and process incoming spice orders / آنے والے مصالحہ آرڈرز</div>
                </div>
                <div class="d-flex align-items-center gap-2 mt-2 mt-sm-0">
                    @if($counts['today'] > 0)
                        <span class="today-badge"><i class="fas fa-calendar-day mr-1"></i>{{ $counts['today'] }} today</span>
                    @endif
                    @if($counts['pending'] > 0)
                        <span class="badge-orders-pending">
                            <i class="fas fa-clock mr-1"></i>{{ $counts['pending'] }} pending action
                        </span>
                    @endif
                </div>
            </div>

            <div class="row g-2">
                @foreach([
                    ['all',       'All Orders',  $counts['all'],       'all',       'fa-layer-group'],
                    ['pending',   'Pending',     $counts['pending'],   'pending',   'fa-clock'],
                    ['confirmed', 'Confirmed',   $counts['confirmed'], 'confirmed', 'fa-check-circle'],
                    ['rejected',  'Rejected',    $counts['rejected'],  'rejected',  'fa-times-circle'],
                ] as [$s, $lbl, $cnt, $clr, $icon])
                <div class="col-6 col-md-3 mb-2">
                    <a href="{{ request()->fullUrlWithQuery(['status' => $s, 'page' => 1]) }}"
                       class="stat-pill {{ $status === $s ? 'active-'.$s : '' }}">
                        <div class="stat-num stat-clr-{{ $clr }}">{{ number_format($cnt) }}</div>
                        <div class="stat-lbl">
                            <span class="stat-dot stat-dot-{{ $clr }}"></span>{{ $lbl }}
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        <div class="filter-card">
            <form method="GET" action="{{ route('admin.spice-orders.index') }}" id="filterForm">
                <input type="hidden" name="status" value="{{ $status }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4 col-sm-6">
                        <label class="filter-lbl">Search / تلاش</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-group-pre">
                                    <i class="fas fa-search text-muted icon-11"></i>
                                </span>
                            </div>
                            <input type="text" name="search" value="{{ $search }}"
                                   class="form-control fc-pn"
                                   placeholder="Reference, shop, phone, city…">
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <label class="filter-lbl">From / سے</label>
                        <input type="date" name="date_from" value="{{ $dateFrom }}"
                               class="form-control form-control-sm fc-pn">
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <label class="filter-lbl">To / تک</label>
                        <input type="date" name="date_to" value="{{ $dateTo }}"
                               class="form-control form-control-sm fc-pn">
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <label class="filter-lbl">Per page / فی صفحہ</label>
                        <select name="per_page" class="per-page-select form-control form-control-sm fc-pn" onchange="this.form.submit()">
                            @foreach([20, 50, 100] as $n)
                                <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }} rows</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-primary btn-pn flex-grow-1">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                        @if($search || $dateFrom || $dateTo)
                            <a href="{{ route('admin.spice-orders.index', ['status' => $status]) }}"
                               class="btn btn-sm btn-pn btn-clear-filter" title="Clear filters">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="card border-0 shadow-sm tbl-card">

            <div class="d-flex align-items-center justify-content-between px-4 py-3 tbl-toolbar-top">
                <div class="pn-table-font text-muted">
                    @if($orders->total() > 0)
                        Showing <strong>{{ $orders->firstItem() }}</strong>–<strong>{{ $orders->lastItem() }}</strong>
                        of <strong>{{ number_format($orders->total()) }}</strong> orders
                    @else
                        No orders found
                    @endif
                </div>
            </div>

            <div class="table-responsive">
                <table class="table orders-table mb-0">
                    <thead>
                        <tr>
                            <th>Reference / حوالہ</th>
                            <th>Shop / Customer / دکان</th>
                            <th>Items Ordered / آرڈر کردہ اشیاء</th>
                            <th>Date / تاریخ</th>
                            <th class="text-center">Status / حیثیت</th>
                            <th class="text-center">Actions / اقدامات</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('admin.spice-orders.show', $order) }}" class="ref-link">
                                    {{ $order->reference }}
                                </a>
                                @if($order->remarks)
                                    <i class="fas fa-comment-alt ml-1 text-muted icon-10" title="{{ $order->remarks }}"></i>
                                @endif
                            </td>
                            <td>
                                <div class="shop-name">
                                    {{ $order->display_name }}
                                    @if(!$order->shop_id)
                                        <span class="new-badge ml-1">NEW</span>
                                    @endif
                                </div>
                                @if($order->display_phone !== '—')
                                    <div class="shop-meta"><i class="fas fa-phone mr-1 icon-9"></i>{{ $order->display_phone }}</div>
                                @endif
                                @if($order->shop?->city ?? $order->city)
                                    <div class="shop-meta"><i class="fas fa-map-marker-alt mr-1 icon-9"></i>{{ $order->shop?->city ?? $order->city }}</div>
                                @endif
                            </td>
                            <td>
                                @foreach($order->items as $item)
                                    <span class="type-badge tb-package">
                                        {{ $item->quantity }}× {{ $item->spiceType->title ?? 'Spice' }} {{ $item->size }}g
                                    </span>
                                @endforeach
                            </td>
                            <td class="text-nowrap">
                                <div class="cell-date-main">{{ $order->created_at->format('d M Y') }}</div>
                                <div class="shop-meta">{{ $order->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="text-center">
                                <span class="status-pill sp-{{ $order->status }}">
                                    @if($order->status === 'pending')
                                        <i class="fas fa-clock mr-1 icon-9"></i>Pending
                                    @elseif($order->status === 'confirmed')
                                        <i class="fas fa-check mr-1 icon-9"></i>Confirmed
                                    @else
                                        <i class="fas fa-times mr-1 icon-9"></i>Rejected
                                    @endif
                                </span>
                                @if($order->sale)
                                    <div class="mt-1">
                                        <a href="{{ route('admin.spice-sales.index') }}"
                                           class="order-sale-badge" title="Sale #{{ $order->sale->id }}">
                                            <i class="fas fa-check-circle mr-1"></i>Sale #{{ $order->sale->id }}
                                        </a>
                                    </div>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                <a href="{{ route('admin.spice-orders.show', $order) }}"
                                   class="action-btn ab-view mr-1" title="View details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($order->status === 'pending')
                                    <form action="{{ route('admin.spice-orders.confirm', $order) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="action-btn ab-confirm mr-1" title="Confirm order" type="submit">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.spice-orders.reject', $order) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="action-btn ab-reject mr-1" title="Reject order" type="submit">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @endif
                                @if(!$order->sale && in_array($order->status, ['pending', 'confirmed']))
                                    <a href="{{ route('admin.spice-orders.to_sale', $order) }}"
                                       class="action-btn ab-sale" title="Convert to Sale">
                                        <i class="fas fa-exchange-alt"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-row">
                            <td colspan="6">
                                <i class="fas fa-inbox empty-icon"></i>
                                <p class="font-weight-bold text-muted mb-1">No spice orders found</p>
                                <p class="empty-msg">Orders placed through the public spice order form will appear here.</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($orders->hasPages())
            <div class="d-flex align-items-center justify-content-between px-4 py-3 tbl-toolbar">
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
