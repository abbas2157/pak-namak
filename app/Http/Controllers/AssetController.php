<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::latest()->get();
        return view('admin.assets.index', compact('assets'));
    }
    public function create()
    {
        return view('admin.assets.index');
    }
    public function store(Request $request)
    {
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'quantity' => 'required|integer',
        //     'purchase_price' => 'nullable|numeric',
        //     'purchase_date' => 'nullable|date',
        //     'description' => 'nullable|string',
        // ]);
        $asset = new Asset();
        $asset->asset_name = $request->asset_name;
        $asset->quantity = $request->quantity;
        $asset->purchase_price = $request->purchase_price;
        $asset->purchase_date = $request->purchase_date;
        $asset->description = $request->description;
        $asset->save();
        return response()->json(['success' => true]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Asset $asset)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset)
    {
        return response()->json($asset);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'quantity' => 'required|integer',
        //     'purchase_price' => 'nullable|numeric',
        //     'purchase_date' => 'nullable|date',
        //     'description' => 'nullable|string',
        // ]);
        $asset->asset_name = $request->asset_name;
        $asset->quantity = $request->quantity;
        $asset->purchase_price = $request->purchase_price;
        $asset->purchase_date = $request->purchase_date;
        $asset->description = $request->description;
        $asset->save();
        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset)
    {
        $asset->delete();
        return response()->json(['success' => true]);
    }
}
