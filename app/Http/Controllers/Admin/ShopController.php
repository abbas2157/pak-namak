<?php

namespace App\Http\Controllers\Admin;

use App\Models\Shop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShopController extends Controller
{
    public function index()
    {
        $shops = Shop::orderBy('id', 'desc')->get();
        return view('admin.shops.index', compact('shops'));
    }

    public function create()
    {
        return view('admin.shops.index');
    }

    public function store(Request $request)
    {
         $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:500',
        ]);

        $shop = new Shop();
        $shop->name = $request->name;
        $shop->phone_number = $request->phone_number;
        $shop->address = $request->address;
        $shop->save();

        return response()->json($shop);
    }

    public function show($id)
    {
        $shop = Shop::with([
            'sales.dalla',
            'sales.thailas',
            'sales.packages'
        ])->findOrFail($id);

        return view('admin.shops.sales', compact('shop'));
    }
    public function edit($id)
    {
        $shop = Shop::findOrFail($id);
        return response()->json($shop);
    }

    public function update(Request $request, $id)
    {
         $shop = Shop::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:500',
        ]);

        $shop->name = $request->name;
        $shop->phone_number = $request->phone_number;
        $shop->address = $request->address;
        $shop->save();

        return response()->json($shop);
    }

    public function destroy($id)
    {
        $shop = Shop::findOrFail($id);
        $shop->delete();

        return response()->json(['success' => true]);
    }
}
