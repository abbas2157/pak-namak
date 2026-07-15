<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\Shop;
use App\Models\City;
use App\Models\SalePayment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    public function index()
    {
        $shops = Shop::with('cityRecord', 'area')
            ->withCount('sales')
            ->withSum('sales', 'total_amount')
            ->withSum('sales', 'pending_amount')
            ->orderByDesc('id')
            ->get();

        $totalShops   = $shops->count();
        $activeShops  = $shops->where('status', 'active')->count();
        $totalRevenue = $shops->sum('sales_sum_total_amount');
        $totalPending = $shops->sum('sales_sum_pending_amount');

        $cities = City::with('areas')->orderBy('name')->get();

        return view('admin.shops.index', compact(
            'shops', 'totalShops', 'activeShops', 'totalRevenue', 'totalPending', 'cities'
        ));
    }

    public function create()
    {
        return redirect()->route('admin.shops.index');
    }

    /**
     * Standalone "Record Payment" page — pick any shop and log a payment
     * against its pending balance without going through the Shops list.
     */
    public function paymentForm()
    {
        $shops = Shop::with('area')
            ->withSum('sales', 'pending_amount')
            ->orderByDesc('sales_sum_pending_amount')
            ->get();

        return view('admin.shops.payment-form', compact('shops'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'owner_name'   => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email'        => 'nullable|email|unique:shops,email|max:255',
            'address'      => 'required|string|max:500',
            'city_id'      => 'nullable|exists:cities,id',
            'area_id'      => 'nullable|exists:areas,id',
            'status'       => 'required|in:active,inactive',
        ]);

        $shop = Shop::create($request->only([
            'name', 'owner_name', 'phone_number', 'email', 'address', 'city_id', 'area_id', 'status',
        ]));

        $shop->load('cityRecord', 'area');
        $shop->loadCount('sales');
        $shop->sales_sum_total_amount   = 0;
        $shop->sales_sum_pending_amount = 0;

        return response()->json(['success' => true, 'shop' => $shop]);
    }

    public function show($id)
    {
        $shop = Shop::with([
            'sales.dalla',
            'sales.thailas',
            'sales.packages',
        ])->findOrFail($id);

        return view('admin.shops.sales', compact('shop'));
    }

    public function edit($id)
    {
        return response()->json(Shop::with('cityRecord', 'area')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $shop = Shop::findOrFail($id);

        $request->validate([
            'name'         => 'required|string|max:255',
            'owner_name'   => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email'        => 'nullable|email|unique:shops,email,' . $id . '|max:255',
            'address'      => 'required|string|max:500',
            'city_id'      => 'nullable|exists:cities,id',
            'area_id'      => 'nullable|exists:areas,id',
            'status'       => 'required|in:active,inactive',
        ]);

        $shop->update($request->only([
            'name', 'owner_name', 'phone_number', 'email', 'address', 'city_id', 'area_id', 'status',
        ]));

        $shop->load('cityRecord', 'area');

        return response()->json(['success' => true, 'shop' => $shop]);
    }

    public function destroy($id)
    {
        Shop::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Record a lump-sum payment against a shop's overall pending balance,
     * spreading it across that shop's pending sales oldest-first (FIFO)
     * until the amount is used up or every sale is settled.
     */
    public function recordPayment(Request $request, Shop $shop)
    {
        $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'payment_date'   => 'required|date',
            'payment_method' => 'required|in:Cash,Bank Transfer,EasyPaisa,JazzCash,Other',
            'note'           => 'nullable|string|max:500',
        ]);

        $pendingSales = $shop->sales()
            ->where('pending_amount', '>', 0)
            ->orderBy('sale_date')
            ->orderBy('id')
            ->get();

        $totalPending = $pendingSales->sum('pending_amount');

        if ($totalPending <= 0) {
            return response()->json(['success' => false, 'message' => 'This shop has no pending amount.'], 422);
        }

        $remaining = min((float) $request->amount, (float) $totalPending);
        $salesPaid = 0;

        DB::transaction(function () use ($pendingSales, &$remaining, $request, &$salesPaid) {
            foreach ($pendingSales as $sale) {
                if ($remaining <= 0) {
                    break;
                }

                $allocated = min($remaining, (float) $sale->pending_amount);

                SalePayment::create([
                    'sale_id'        => $sale->id,
                    'amount'         => $allocated,
                    'payment_date'   => $request->payment_date,
                    'payment_method' => $request->payment_method,
                    'note'           => $request->note,
                ]);

                $received = $sale->payments()->sum('amount');
                $sale->update([
                    'received_amount' => $received,
                    'pending_amount'  => $sale->total_amount - $received,
                ]);

                $remaining -= $allocated;
                $salesPaid++;
            }
        });

        return response()->json(['success' => true, 'sales_paid' => $salesPaid]);
    }

    public function info(Shop $shop): \Illuminate\Http\JsonResponse
    {
        $stats = $shop->sales()
            ->selectRaw('COALESCE(SUM(total_amount),0) as total_amount, COALESCE(SUM(received_amount),0) as received_amount, COALESCE(SUM(pending_amount),0) as pending_amount')
            ->first();

        $orders = Order::where('shop_id', $shop->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereDoesntHave('sale')
            ->withCount('items')
            ->orderByDesc('id')
            ->get(['id', 'reference', 'status', 'created_at', 'remarks']);

        return response()->json([
            'shop' => [
                'id'           => $shop->id,
                'name'         => $shop->name,
                'phone_number' => $shop->phone_number,
            ],
            'financials' => [
                'total_amount'    => (float) $stats->total_amount,
                'received_amount' => (float) $stats->received_amount,
                'pending_amount'  => (float) $stats->pending_amount,
            ],
            'orders' => $orders->map(fn($o) => [
                'id'          => $o->id,
                'reference'   => $o->reference,
                'status'      => $o->status,
                'created_at'  => $o->created_at->format('d M Y'),
                'items_count' => $o->items_count,
            ])->values(),
        ]);
    }
}
