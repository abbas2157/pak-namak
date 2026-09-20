<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\SpiceOrder;
use App\Models\SpiceStock;
use App\Models\SpiceType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpiceOrderController extends Controller
{
    public function form()
    {
        $shops = Shop::with('area:id,name')
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'city', 'area_id', 'phone_number']);

        $spiceTypes = SpiceType::orderBy('title')->get();

        $stockLevels = SpiceStock::levels()->keyBy(fn ($l) => SpiceStock::key($l['spice_type_id'], $l['size']));

        return view('spice-order.form', compact('shops', 'spiceTypes', 'stockLevels'));
    }

    public function store(Request $request)
    {
        $isUnlisted = $request->boolean('unlisted');

        // Quantities and prices come straight off a public, unauthenticated form
        // and are carried into the sale when the order is converted, so they're
        // bounded here rather than trusted.
        $rules = [
            'remarks' => 'nullable|string|max:500',
            'package' => 'nullable|array',
            'package.*.*.qty' => 'nullable|numeric|min:0|max:100000',
            'package.*.*.price' => 'nullable|numeric|min:0|max:10000000',
        ];

        if ($isUnlisted) {
            $rules['customer_name'] = 'required|string|max:200';
            $rules['phone'] = 'required|string|max:30';
            $rules['city'] = 'nullable|string|max:100';
        } else {
            $rules['shop_id'] = 'required|exists:shops,id';
        }

        $request->validate($rules, [
            'shop_id.required' => 'Please select your shop.',
            'customer_name.required' => 'Please enter your shop / name.',
            'phone.required' => 'Please enter your phone number.',
            'package.*.*.qty.*' => 'Please enter a valid quantity.',
            'package.*.*.price.*' => 'Please enter a valid rate.',
        ]);

        $spiceTypes = SpiceType::orderBy('title')->get();

        $hasItem = false;
        foreach ($spiceTypes as $spiceType) {
            foreach (config('admin.spice_sizes', []) as $g) {
                if ((float) $request->input("package.{$spiceType->id}.{$g}.qty", 0) > 0) {
                    $hasItem = true;
                    break 2;
                }
            }
        }

        if (! $hasItem) {
            return back()->withInput()->withErrors(['items' => 'Please enter a quantity for at least one product.']);
        }

        DB::beginTransaction();
        try {
            $order = SpiceOrder::create([
                'reference' => SpiceOrder::generateReference(),
                'shop_id' => $isUnlisted ? null : $request->shop_id,
                'customer_name' => $isUnlisted ? $request->customer_name : null,
                'phone' => $isUnlisted ? $request->phone : null,
                'city' => $isUnlisted ? $request->city : null,
                'remarks' => $request->remarks,
                'status' => 'pending',
                'ip_address' => $request->ip(),
            ]);

            foreach ($spiceTypes as $spiceType) {
                foreach (config('admin.spice_sizes', []) as $gram) {
                    $qty = (float) $request->input("package.{$spiceType->id}.{$gram}.qty", 0);
                    if ($qty > 0) {
                        $price = (float) $request->input("package.{$spiceType->id}.{$gram}.price", 0) ?: null;
                        $order->items()->create([
                            'spice_type_id' => $spiceType->id,
                            'size' => $gram,
                            'quantity' => $qty,
                            'price' => $price,
                            'sub_total' => $price ? round($qty * $price, 2) : null,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('spice-order.confirm', $order->reference);

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->withErrors(['error' => 'Could not submit order. Please try again.']);
        }
    }

    public function confirm(string $reference)
    {
        $order = SpiceOrder::with(['shop', 'items.spiceType'])
            ->where('reference', $reference)
            ->firstOrFail();

        return view('spice-order.confirm', compact('order'));
    }

    public function stockView()
    {
        $levels = SpiceStock::levels();
        $spiceTypes = SpiceType::orderBy('title')->get();

        return view('spice-order.stock', compact('levels', 'spiceTypes'));
    }

    public function stockData(): JsonResponse
    {
        return response()->json(['levels' => SpiceStock::levels()->values()]);
    }

    /**
     * Account summary shown on the public form once a shop is picked —
     * spice sales only, mirroring OrderController::shopInfo() for salt.
     */
    public function shopInfo(Shop $shop): JsonResponse
    {
        $stats = $shop->spiceSales()
            ->selectRaw('COALESCE(SUM(total_amount),0) as total_amount, COALESCE(SUM(received_amount),0) as received_amount, COALESCE(SUM(pending_amount),0) as pending_amount')
            ->first();

        $orders = SpiceOrder::where('shop_id', $shop->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereDoesntHave('sale')
            ->withCount('items')
            ->orderByDesc('id')
            ->get(['id', 'reference', 'status', 'created_at']);

        return response()->json([
            'shop' => [
                'id' => $shop->id,
                'name' => $shop->name,
                'phone_number' => $shop->phone_number,
                'location' => $shop->location,
            ],
            'financials' => [
                'total_amount' => (float) $stats->total_amount,
                'received_amount' => (float) $stats->received_amount,
                'pending_amount' => (float) $stats->pending_amount,
            ],
            'orders' => $orders->map(fn ($o) => [
                'reference' => $o->reference,
                'status' => $o->status,
                'created_at' => $o->created_at->format('d M Y'),
                'items_count' => $o->items_count,
            ])->values(),
        ]);
    }
}
