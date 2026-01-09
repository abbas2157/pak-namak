<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\{Purchase, Vendor};
use App\Http\Controllers\Controller;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('vendor')->latest()->get();
        $vendors = Vendor::get();
        return view('admin.purchases.index', compact('purchases', 'vendors'));
    }
    public function create()
    {
        $vendors = Vendor::get();
        return view('admin.purchases.index', compact('vendors'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required',
            'salt_quantity' => 'required|numeric',
            'rate_per_kg' => 'required|numeric',
            'total_cost' => 'required|numeric',
            'grand_total' => 'required|numeric',
        ]);

        $purchase = new Purchase();
        $purchase->vendor_id = $request->vendor_id;
        $purchase->salt_quantity = $request->salt_quantity;
        $purchase->salt_quantity_kg = $request->salt_quantity_kg;
        $purchase->rate_per_kg = $request->rate_per_kg;
        $purchase->total_cost = $request->total_cost;
        $purchase->transport_cost = $request->transport_cost;
        $purchase->loading_unloading_cost = $request->loading_unloading_cost;
        $purchase->grand_total = $request->grand_total;
        $purchase->remarks = $request->remarks;
        $purchase->save();
        return response()->json(['success' => true]);
    }
    public function edit($id)
    {
        $purchase = Purchase::findOrFail($id);
        return response()->json($purchase);
    }
    public function update(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);
        $request->validate([
            'vendor_id' => 'required',
            'salt_quantity' => 'required|numeric',
            'rate_per_kg' => 'required|numeric',
            'total_cost' => 'required|numeric',
            'grand_total' => 'required|numeric',
        ]);
        $purchase->vendor_id = $request->vendor_id;
        $purchase->salt_quantity = $request->salt_quantity;
        $purchase->salt_quantity_kg = $request->salt_quantity_kg;
        $purchase->rate_per_kg = $request->rate_per_kg;
        $purchase->total_cost = $request->total_cost;
        $purchase->transport_cost = $request->transport_cost;
        $purchase->loading_unloading_cost = $request->loading_unloading_cost;
        $purchase->grand_total = $request->grand_total;
        $purchase->remarks = $request->remarks;
        $purchase->save();
        return response()->json(['success' => true]);
    }
    public function destroy($id)
    {
        $purchase = Purchase::findOrFail($id);
        $purchase->delete();
        return response()->json(['success' => true]);
    }
}
