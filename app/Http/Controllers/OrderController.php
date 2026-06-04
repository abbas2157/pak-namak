<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function form()
    {
        $shops = Shop::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'city', 'phone_number']);

        return view('order.form', compact('shops'));
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

        // Ensure at least one item has quantity > 0
        $hasItem = false;
        if ((float) $request->input('dalla_qty', 0) > 0) $hasItem = true;
        foreach ([5, 10, 30, 35, 40, 50] as $s) {
            if ((float) $request->input("thaila.$s.qty", 0) > 0) { $hasItem = true; break; }
        }
        foreach (array_keys(config('admin.packages')) as $g) {
            if ((float) $request->input("package.$g.qty", 0) > 0) { $hasItem = true; break; }
        }

        if (!$hasItem) {
            return back()->withInput()->withErrors(['items' => 'Please enter a quantity for at least one product.']);
        }

        DB::beginTransaction();
        try {
            $order = Order::create([
                'reference'     => Order::generateReference(),
                'shop_id'       => $isUnlisted ? null : $request->shop_id,
                'customer_name' => $isUnlisted ? $request->customer_name : null,
                'phone'         => $isUnlisted ? $request->phone : null,
                'city'          => $isUnlisted ? $request->city : null,
                'remarks'       => $request->remarks,
                'status'        => 'pending',
                'ip_address'    => $request->ip(),
            ]);

            // Dalla
            $dallaQty = (float) $request->input('dalla_qty', 0);
            if ($dallaQty > 0) {
                $price    = (float) $request->input('dalla_price', 0) ?: null;
                $order->items()->create([
                    'type'      => 'dalla',
                    'size'      => null,
                    'quantity'  => $dallaQty,
                    'price'     => $price,
                    'sub_total' => $price ? round($dallaQty * $price, 2) : null,
                ]);
            }

            // Thaila
            foreach ([5, 10, 30, 35, 40, 50] as $size) {
                $qty = (float) $request->input("thaila.$size.qty", 0);
                if ($qty > 0) {
                    $price = (float) $request->input("thaila.$size.price", 0) ?: null;
                    $order->items()->create([
                        'type'      => 'thaila',
                        'size'      => $size,
                        'quantity'  => $qty,
                        'price'     => $price,
                        'sub_total' => $price ? round($qty * $price, 2) : null,
                    ]);
                }
            }

            // Package
            foreach (array_keys(config('admin.packages')) as $gram) {
                $qty = (float) $request->input("package.$gram.qty", 0);
                if ($qty > 0) {
                    $price = (float) $request->input("package.$gram.price", 0) ?: null;
                    $order->items()->create([
                        'type'      => 'package',
                        'size'      => $gram,
                        'quantity'  => $qty,
                        'price'     => $price,
                        'sub_total' => $price ? round($qty * $price, 2) : null,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('order.confirm', $order->reference);

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Could not submit order. Please try again.']);
        }
    }

    public function confirm(string $reference)
    {
        $order = Order::with(['shop', 'items'])
            ->where('reference', $reference)
            ->firstOrFail();

        return view('order.confirm', compact('order'));
    }
}
