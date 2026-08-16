<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpiceOrder;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpiceOrderAdminController extends Controller
{
    public function index(Request $request)
    {
        $statusCounts = SpiceOrder::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $counts = [
            'pending'   => $statusCounts['pending']   ?? 0,
            'confirmed' => $statusCounts['confirmed'] ?? 0,
            'rejected'  => $statusCounts['rejected']  ?? 0,
            'all'       => $statusCounts->sum(),
            'today'     => SpiceOrder::whereDate('created_at', today())->count(),
        ];

        $status    = $request->input('status', 'all');
        $search    = $request->input('search');
        $dateFrom  = $request->input('date_from');
        $dateTo    = $request->input('date_to');
        $perPage   = in_array($request->input('per_page'), [20, 50, 100]) ? (int) $request->input('per_page') : 20;

        $query = SpiceOrder::with(['shop:id,name,phone_number,city', 'items.spiceType', 'sale:id,spice_order_id'])
            ->orderByDesc('id');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhereHas('shop', fn($s) => $s->where('name', 'like', "%{$search}%"));
            });
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $orders = $query->paginate($perPage)->appends($request->query());

        return view('admin.spice-orders.index', compact('orders', 'counts', 'status', 'search', 'dateFrom', 'dateTo', 'perPage'));
    }

    public function show(SpiceOrder $spiceOrder)
    {
        $spiceOrder->load(['shop', 'items.spiceType', 'sale:id,spice_order_id,sale_date,total_amount']);

        $shopSalesTotal   = null;
        $shopSalesPending = null;
        $shopSalesCount   = null;

        if ($spiceOrder->shop) {
            $shopStats = $spiceOrder->shop->spiceSales()
                ->selectRaw('count(*) as cnt, sum(total_amount) as revenue, sum(pending_amount) as pending')
                ->first();

            $shopSalesCount   = $shopStats->cnt;
            $shopSalesTotal   = $shopStats->revenue ?? 0;
            $shopSalesPending = $shopStats->pending ?? 0;
        }

        return view('admin.spice-orders.show', compact(
            'spiceOrder', 'shopSalesTotal', 'shopSalesPending', 'shopSalesCount'
        ));
    }

    public function confirm(SpiceOrder $spiceOrder)
    {
        if ($spiceOrder->status !== 'pending') {
            return back()->with('error', 'Order is already ' . $spiceOrder->status . '.');
        }

        DB::beginTransaction();
        try {
            $autoRegistered = false;

            if (!$spiceOrder->shop_id && $spiceOrder->customer_name) {
                $shop = Shop::create([
                    'name'         => $spiceOrder->customer_name,
                    'phone_number' => $spiceOrder->phone,
                    'city'         => $spiceOrder->city,
                    'address'      => $spiceOrder->city ?? '',
                    'status'       => 'active',
                ]);
                $spiceOrder->shop_id = $shop->id;
                $autoRegistered = true;
            }

            $spiceOrder->status = 'confirmed';
            $spiceOrder->save();

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

    public function reject(SpiceOrder $spiceOrder)
    {
        if ($spiceOrder->status !== 'pending') {
            return back()->with('error', 'Order is already ' . $spiceOrder->status . '.');
        }

        $spiceOrder->update(['status' => 'rejected']);
        return back()->with('success', 'Order rejected.');
    }

    public function toSale(SpiceOrder $spiceOrder)
    {
        session(['prefill_spice_order_id' => $spiceOrder->id]);
        return redirect()->route('admin.spice-sales.create');
    }
}
