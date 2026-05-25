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
        $status = $request->get('status', 'all');

        $query = Order::with(['shop', 'items'])->orderByDesc('id');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders       = $query->paginate(20)->appends($request->only('status'));
        $pendingCount = Order::where('status', 'pending')->count();

        return view('admin.orders.index', compact('orders', 'pendingCount', 'status'));
    }

    public function show(Order $order)
    {
        $order->load(['shop', 'items']);

        $shopSalesTotal   = null;
        $shopSalesPending = null;
        $shopSalesCount   = null;

        if ($order->shop) {
            $shopSalesTotal   = $order->shop->sales()->sum('total_amount');
            $shopSalesPending = $order->shop->sales()->sum('pending_amount');
            $shopSalesCount   = $order->shop->sales()->count();
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
            // Auto-register shop if unlinked
            if (!$order->shop_id && $order->customer_name) {
                $shop = Shop::create([
                    'name'         => $order->customer_name,
                    'phone_number' => $order->phone,
                    'city'         => $order->city,
                    'address'      => $order->city ?? '',
                    'status'       => 'active',
                ]);
                $order->shop_id = $shop->id;
            }

            $order->status = 'confirmed';
            $order->save();

            DB::commit();
            return back()->with('success', 'Order confirmed' . ($order->shop_id && !$order->wasRecentlyCreated ? ' and shop auto-registered' : '') . '.');

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
