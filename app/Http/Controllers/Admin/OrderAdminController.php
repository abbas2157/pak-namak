<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderAdminController extends Controller
{
    public function index(Request $request)
    {
        // Single query for all status counts (avoids 3 separate COUNT queries)
        $statusCounts = Order::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $counts = [
            'pending'   => $statusCounts['pending']   ?? 0,
            'confirmed' => $statusCounts['confirmed'] ?? 0,
            'rejected'  => $statusCounts['rejected']  ?? 0,
            'all'       => $statusCounts->sum(),
            'today'     => Order::whereDate('created_at', today())->count(),
        ];

        $status    = $request->input('status', 'all');
        $search    = $request->input('search');
        $dateFrom  = $request->input('date_from');
        $dateTo    = $request->input('date_to');
        $perPage   = in_array($request->input('per_page'), [20, 50, 100]) ? (int) $request->input('per_page') : 20;

        $query = Order::with(['shop:id,name,phone_number,city', 'items'])
            ->orderByDesc('id');

        // Status filter
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Search: reference, customer name, phone, or linked shop name
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhereHas('shop', fn($s) => $s->where('name', 'like', "%{$search}%"));
            });
        }

        // Date range
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $orders = $query->paginate($perPage)->appends($request->query());

        return view('admin.orders.index', compact('orders', 'counts', 'status', 'search', 'dateFrom', 'dateTo', 'perPage'));
    }

    public function show(Order $order)
    {
        $order->load(['shop', 'items']);

        $shopSalesTotal   = null;
        $shopSalesPending = null;
        $shopSalesCount   = null;

        if ($order->shop) {
            // Single query instead of three
            $shopStats = $order->shop->sales()
                ->selectRaw('count(*) as cnt, sum(total_amount) as revenue, sum(pending_amount) as pending')
                ->first();

            $shopSalesCount   = $shopStats->cnt;
            $shopSalesTotal   = $shopStats->revenue ?? 0;
            $shopSalesPending = $shopStats->pending ?? 0;
        }

        return view('admin.orders.show', compact(
            'order', 'shopSalesTotal', 'shopSalesPending', 'shopSalesCount'
        ));
    }

    public function confirm(Order $order)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'Order is already ' . $order->status . '.');
        }

        DB::beginTransaction();
        try {
            $autoRegistered = false;

            if (!$order->shop_id && $order->customer_name) {
                $shop = Shop::create([
                    'name'         => $order->customer_name,
                    'phone_number' => $order->phone,
                    'city'         => $order->city,
                    'address'      => $order->city ?? '',
                    'status'       => 'active',
                ]);
                $order->shop_id    = $shop->id;
                $autoRegistered    = true;
            }

            $order->status = 'confirmed';
            $order->save();

            DB::commit();

            $msg = $autoRegistered
                ? 'Order confirmed and shop auto-registered.'
                : 'Order confirmed.';

            return back()->with('success', $msg);

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Could not confirm order.');
        }
    }

    public function reject(Order $order)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'Order is already ' . $order->status . '.');
        }

        $order->update(['status' => 'rejected']);
        return back()->with('success', 'Order rejected.');
    }

    public function toSale(Order $order)
    {
        session(['prefill_order_id' => $order->id]);
        return redirect()->route('admin.sales.create');
    }
}
