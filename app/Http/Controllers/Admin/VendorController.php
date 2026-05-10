<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Vendor, Purchase};
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $vendors      = Vendor::withCount('purchases')->orderBy('name')->get();
        $totalVendors = $vendors->count();
        $totalSpent   = Purchase::sum('grand_total');
        $topVendor    = $vendors->sortByDesc('purchases_count')->first();

        return view('admin.vendors.index', compact('vendors', 'totalVendors', 'totalSpent', 'topVendor'));
    }

    public function create()
    {
        return redirect()->route('admin.vendors.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'shop'    => 'nullable|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $vendor = Vendor::create($request->only(['name', 'shop', 'phone', 'address']));
        return response()->json(['success' => true, 'vendor' => $vendor]);
    }

    public function edit($id)
    {
        return response()->json(Vendor::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'shop'    => 'nullable|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $vendor = Vendor::findOrFail($id);
        $vendor->update($request->only(['name', 'shop', 'phone', 'address']));
        return response()->json(['success' => true, 'vendor' => $vendor]);
    }

    public function destroy($id)
    {
        Vendor::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
