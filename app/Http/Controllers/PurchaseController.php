<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::latest()->get();
        return view('admin.salt_purchases.index', compact('purchases'));
    }
    public function create()
    {
        return view('admin.salt_purchases.index');
    }
    public function store(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required',
            'salt_quantity' => 'required|numeric',
            'rate_per_kg' => 'required|numeric',
            'total_cost' => 'required|numeric',
            'grand_total' => 'required|numeric',
        ]);

        $purchase = new Purchase();
        $purchase->supplier_name = $request->supplier_name;
        $purchase->salt_quantity = $request->salt_quantity;
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
            'supplier_name' => 'required',
            'salt_quantity' => 'required|numeric',
            'rate_per_kg' => 'required|numeric',
            'total_cost' => 'required|numeric',
            'grand_total' => 'required|numeric',
        ]);
        $purchase->supplier_name = $request->supplier_name;
        $purchase->salt_quantity = $request->salt_quantity;
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
