<?php

namespace App\Http\Controllers\Admin;

use App\Models\Shop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShopController extends Controller
{
    public function index()
    {
        $shops = Shop::withCount('sales')
            ->withSum('sales', 'total_amount')
            ->withSum('sales', 'pending_amount')
            ->orderByDesc('id')
            ->get();

        $totalShops    = $shops->count();
        $activeShops   = $shops->where('status', 'active')->count();
        $totalRevenue  = $shops->sum('sales_sum_total_amount');
        $totalPending  = $shops->sum('sales_sum_pending_amount');

        return view('admin.shops.index', compact(
            'shops', 'totalShops', 'activeShops', 'totalRevenue', 'totalPending'
        ));
    }

    public function create()
    {
        return redirect()->route('admin.shops.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'owner_name'   => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email'        => 'nullable|email|unique:shops,email|max:255',
            'address'      => 'required|string|max:500',
            'city'         => 'nullable|string|max:100',
            'status'       => 'required|in:active,inactive',
        ]);

        $shop = Shop::create($request->only([
            'name', 'owner_name', 'phone_number', 'email', 'address', 'city', 'status',
        ]));

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
        return response()->json(Shop::findOrFail($id));
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
            'city'         => 'nullable|string|max:100',
            'status'       => 'required|in:active,inactive',
        ]);

        $shop->update($request->only([
            'name', 'owner_name', 'phone_number', 'email', 'address', 'city', 'status',
        ]));

        return response()->json(['success' => true, 'shop' => $shop]);
    }

    public function destroy($id)
    {
        Shop::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
