<?php

namespace App\Http\Controllers;

use App\Models\SpiceOrder;
use App\Models\SpiceStock;
use App\Models\SpiceType;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpiceOrderController extends Controller
{
    public function form()
    {
        $shops = Shop::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'city', 'phone_number']);

        $spiceTypes = SpiceType::orderBy('title')->get();

        $stockLevels = SpiceStock::levels()->keyBy(fn ($l) => SpiceStock::key($l['spice_type_id'], $l['size']));

        return view('spice-order.form', compact('shops', 'spiceTypes', 'stockLevels'));
    }

    public function store(Request $request)
    {
        $isUnlisted = $request->boolean('unlisted');

        $rules = [
            'remarks' => 'nullable|string|max:500',
        ];

        if ($isUnlisted) {
            $rules['customer_name'] = 'required|string|max:200';
            $rules['phone']         = 'required|string|max:30';
            $rules['city']          = 'nullable|string|max:100';
        } else {
            $rules['shop_id'] = 'required|exists:shops,id';
        }

        $request->validate($rules, [
            'shop_id.required'       => 'Please select your shop.',
            'customer_name.required' => 'Please enter your shop / name.',
            'phone.required'         => 'Please enter your phone number.',
        ]);

        $spiceTypes = SpiceType::orderBy('title')->get();

        $hasItem = false;
        foreach ($spiceTypes as $spiceType) {
            foreach (config('admin.spice_sizes', []) as $g) {
                if ((float) $request->input("package.{$spiceType->id}.{$g}.qty", 0) > 0) { $hasItem = true; break 2; }
            }
        }

        if (!$hasItem) {
            return back()->withInput()->withErrors(['items' => 'Please enter a quantity for at least one product.']);
        }

        DB::beginTransaction();
        try {
            $order = SpiceOrder::create([
                'reference'     => SpiceOrder::generateReference(),
                'shop_id'       => $isUnlisted ? null : $request->shop_id,
                'customer_name' => $isUnlisted ? $request->customer_name : null,
                'phone'         => $isUnlisted ? $request->phone : null,
                'city'          => $isUnlisted ? $request->city : null,
                'remarks'       => $request->remarks,
                'status'        => 'pending',
                'ip_address'    => $request->ip(),
            ]);

            foreach ($spiceTypes as $spiceType) {
                foreach (config('admin.spice_sizes', []) as $gram) {
                    $qty = (float) $request->input("package.{$spiceType->id}.{$gram}.qty", 0);
                    if ($qty > 0) {
                        $price = (float) $request->input("package.{$spiceType->id}.{$gram}.price", 0) ?: null;
                        $order->items()->create([
                            'spice_type_id' => $spiceType->id,
                            'size'          => $gram,
                            'quantity'      => $qty,
                            'price'         => $price,
                            'sub_total'     => $price ? round($qty * $price, 2) : null,
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

    public function stockData(): \Illuminate\Http\JsonResponse
    {
        return response()->json(['levels' => SpiceStock::levels()->values()]);
    }
}
