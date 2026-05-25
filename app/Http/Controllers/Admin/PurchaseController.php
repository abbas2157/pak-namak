<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\{Purchase, Vendor};
use App\Http\Controllers\Controller;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with('vendor');

        if ($request->month) {
            [$year, $month] = explode('-', $request->month);
            $query->whereYear('purchase_date', $year)->whereMonth('purchase_date', $month);
        }

        $purchases = $query->orderByDesc('id')->get();
        $vendors   = Vendor::orderBy('name')->get();

        $totalSpent   = $purchases->sum('grand_total');
        $totalQtyKg   = $purchases->sum('salt_quantity_kg');
        $totalQtyTon  = $purchases->sum('salt_quantity');
        $totalEntries = $purchases->count();

        $months = Purchase::selectRaw("DATE_FORMAT(purchase_date,'%Y-%m') as value, DATE_FORMAT(purchase_date,'%M %Y') as label")
            ->whereNotNull('purchase_date')
            ->groupBy('value', 'label')
            ->orderByDesc('value')
            ->get();

        $selectedMonth = $request->month;

        return view('admin.purchases.index', compact(
            'purchases', 'vendors',
            'totalSpent', 'totalQtyKg', 'totalQtyTon', 'totalEntries',
            'months', 'selectedMonth'
        ));
    }

    public function create()
    {
        $vendors = Vendor::orderBy('name')->get();
        return view('admin.purchases.index', compact('vendors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_id'    => 'required|exists:vendors,id',
            'salt_quantity' => 'required|numeric|min:0',
            'rate_per_kg'  => 'required|numeric|min:0',
            'total_cost'   => 'required|numeric|min:0',
            'grand_total'  => 'required|numeric|min:0',
        ]);

        Purchase::create([
            'vendor_id'              => $request->vendor_id,
            'purchase_date'          => $request->purchase_date ?: now()->toDateString(),
            'salt_quantity'          => $request->salt_quantity,
            'salt_quantity_kg'       => $request->salt_quantity_kg,
            'rate_per_kg'            => $request->rate_per_kg,
            'total_cost'             => $request->total_cost,
            'transport_cost'         => $request->transport_cost ?? 0,
            'loading_unloading_cost' => $request->loading_unloading_cost ?? 0,
            'grand_total'            => $request->grand_total,
            'remarks'                => $request->remarks,
        ]);

        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        $purchase = Purchase::findOrFail($id);
        $data = $purchase->toArray();
        $data['purchase_date'] = $purchase->purchase_date
            ? $purchase->purchase_date->format('Y-m-d')
            : null;
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'vendor_id'    => 'required|exists:vendors,id',
            'salt_quantity' => 'required|numeric|min:0',
            'rate_per_kg'  => 'required|numeric|min:0',
            'total_cost'   => 'required|numeric|min:0',
            'grand_total'  => 'required|numeric|min:0',
        ]);

        $purchase = Purchase::findOrFail($id);
        $purchase->update([
            'vendor_id'              => $request->vendor_id,
            'purchase_date'          => $request->purchase_date ?: $purchase->purchase_date,
            'salt_quantity'          => $request->salt_quantity,
            'salt_quantity_kg'       => $request->salt_quantity_kg,
            'rate_per_kg'            => $request->rate_per_kg,
            'total_cost'             => $request->total_cost,
            'transport_cost'         => $request->transport_cost ?? 0,
            'loading_unloading_cost' => $request->loading_unloading_cost ?? 0,
            'grand_total'            => $request->grand_total,
            'remarks'                => $request->remarks,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        Purchase::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
