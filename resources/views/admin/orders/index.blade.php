@extends('admin.layout.app')
@section('title', 'Orders')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Orders</h1>
                <ol class="breadcrumb mt-1">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Orders</li>
                </ol>
            </div>
            <div class="col-sm-6 d-flex justify-content-end">
                @if($pendingCount > 0)
                    <span class="badge badge-warning px-3 py-2" style="font-size:14px;border-radius:20px;">
                        <i class="fas fa-clock mr-1"></i> {{ $pendingCount }} Pending
                    </span>
                @endif
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:10px;">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:10px;">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        {{-- Stats --}}
        <div class="row mb-3">
            @php
                $counts = ['pending'=>0,'confirmed'=>0,'rejected'=>0,'all'=>0];
            @endphp
            @foreach([
                ['pending',   'Pending',   '#f6c23e', 'fa-clock'],
                ['confirmed', 'Confirmed', '#1cc88a', 'fa-check-circle'],
                ['rejected',  'Rejected',  '#e74a3b', 'fa-times-circle'],
            ] as [$s, $label, $color, $icon])
            @php $cnt = \App\Models\Order::where('status',$s)->count(); @endphp
            <div class="col-6 col-md-4 mb-2">
                <a href="?status={{ $s }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 {{ $status === $s ? 'border-left-primary' : '' }}"
                         style="border-left:4px solid {{ $color }} !important;border-radius:10px;{{ $status===$s?'box-shadow:0 0 0 2px '.$color.'44 !important;':'' }}">
                        <div class="card-body py-3 px-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-xs text-uppercase font-weight-bold text-muted mb-1">{{ $label }}</div>
                                    <div class="h5 mb-0 font-weight-bold text-dark">{{ $cnt }}</div>
                                </div>
                                <div class="rounded-circle d-flex align-items-center justify-content-center"
                                     style="width:42px;height:42px;background:{{ $color }}18;">
                                    <i class="fas {{ $icon }}" style="color:{{ $color }};font-size:16px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

        {{-- Filter --}}
        <div class="mb-3">
            <a href="?status=all" class="btn btn-sm {{ $status==='all' ? 'btn-secondary' : 'btn-outline-secondary' }} mr-1">All</a>
            <a href="?status=pending" class="btn btn-sm {{ $status==='pending' ? 'btn-warning' : 'btn-outline-warning' }} mr-1">Pending</a>
            <a href="?status=confirmed" class="btn btn-sm {{ $status==='confirmed' ? 'btn-success' : 'btn-outline-success' }} mr-1">Confirmed</a>
            <a href="?status=rejected" class="btn btn-sm {{ $status==='rejected' ? 'btn-danger' : 'btn-outline-danger' }}">Rejected</a>
        </div>

        {{-- Table --}}
        <div class="card border-0 shadow-sm" style="border-radius:12px;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0" style="font-size:13.5px;">
                        <thead>
                            <tr style="background:#f8f9fc;border-bottom:2px solid #e3e6f0;">
                                <th class="pl-3 py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Reference</th>
                                <th class="py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Shop / Customer</th>
                                <th class="py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Items</th>
                                <th class="py-3 text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Submitted</th>
                                <th class="py-3 text-center text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Status</th>
                                <th class="py-3 text-center text-uppercase" style="font-size:11px;color:#6c757d;font-weight:700;letter-spacing:.5px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($orders as $order)
                            <tr style="border-bottom:1px solid #f0f0f0;">
                                <td class="pl-3 py-3 align-middle">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                       class="font-weight-bold" style="color:#4e73df;text-decoration:none;">
                                        {{ $order->reference }}
                                    </a>
                                </td>
                                <td class="py-3 align-middle">
                                    <span class="font-weight-bold d-block" style="color:#2d3748;">
                                        {{ $order->display_name }}
                                        @if(!$order->shop_id)
                                            <span class="badge badge-secondary ml-1" style="font-size:9px;">New</span>
                                        @endif
                                    </span>
                                    <small class="text-muted">{{ $order->display_phone }}</small>
                                </td>
                                <td class="py-3 align-middle" style="max-width:200px;">
                                    @foreach($order->items->take(2) as $item)
                                        <span class="badge mr-1 mb-1"
                                              style="font-size:10px;padding:3px 7px;border-radius:20px;
                                              background:{{ $item->type==='dalla'?'#e8f0fe':($item->type==='thaila'?'#d4edda':'#fff3cd') }};
                                              color:{{ $item->type==='dalla'?'#4e73df':($item->type==='thaila'?'#155724':'#856404') }};">
                                            {{ $item->quantity }}×
                                            @if($item->type==='dalla') Dalla
                                            @elseif($item->type==='thaila') {{ $item->size }}kg
                                            @else {{ $item->size }}g
                                            @endif
                                        </span>
                                    @endforeach
                                    @if($order->items->count() > 2)
                                        <small class="text-muted">+{{ $order->items->count()-2 }} more</small>
                                    @endif
                                </td>
                                <td class="py-3 align-middle">
                                    <span class="d-block" style="color:#2d3748;">{{ $order->created_at->format('d M Y') }}</span>
                                    <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                </td>
                                <td class="py-3 align-middle text-center">
                                    @if($order->status === 'pending')
                                        <span class="badge badge-warning" style="font-size:11px;padding:4px 10px;border-radius:20px;">Pending</span>
                                    @elseif($order->status === 'confirmed')
                                        <span class="badge badge-success" style="font-size:11px;padding:4px 10px;border-radius:20px;">Confirmed</span>
                                    @else
                                        <span class="badge badge-danger" style="font-size:11px;padding:4px 10px;border-radius:20px;">Rejected</span>
                                    @endif
                                </td>
                                <td class="py-3 align-middle text-center" style="white-space:nowrap;">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                       class="btn btn-sm mr-1"
                                       style="background:#e8f0fe;color:#4e73df;border:1px solid #c3d3f7;border-radius:6px;"
                                       title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($order->status === 'pending')
                                        <form action="{{ route('admin.orders.confirm', $order) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm mr-1"
                                                    style="background:#d4edda;color:#155724;border:1px solid #c3e6cb;border-radius:6px;"
                                                    title="Confirm">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.orders.reject', $order) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm"
                                                    style="background:#fce8e6;color:#c62828;border:1px solid #ef9a9a;border-radius:6px;"
                                                    title="Reject">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if(in_array($order->status, ['pending','confirmed']))
                                        <a href="{{ route('admin.orders.to_sale', $order) }}"
                                           class="btn btn-sm ml-1"
                                           style="background:#fff3cd;color:#856404;border:1px solid #ffc107;border-radius:6px;"
                                           title="Convert to Sale">
                                            <i class="fas fa-exchange-alt"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block" style="color:#d1d5db;"></i>
                                    <p class="text-muted mb-0">No orders found.</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        @if($orders->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-3 px-1">
            <small class="text-muted">
                Showing {{ $orders->firstItem() }}–{{ $orders->lastItem() }} of {{ $orders->total() }} orders
            </small>
            {{ $orders->links() }}
        </div>
        @endif

    </div>
</section>
@endsection
