<?php

namespace App\Http\Controllers\Admin;

use App\Models\Asset;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::latest('purchase_date')->get();

        $totalValue  = $assets->sum(fn($a) => $a->quantity * $a->purchase_price);
        $totalCount  = $assets->count();
        $activeCount = $assets->where('status', 'active')->count();
        $repairCount = $assets->where('status', 'under_repair')->count();

        $categoryTotals = $assets
            ->groupBy('category')
            ->map(fn($group) => $group->sum(fn($a) => $a->quantity * $a->purchase_price))
            ->sortByDesc(fn($v) => $v);

        return view('admin.assets.index', compact(
            'assets', 'totalValue', 'totalCount', 'activeCount', 'repairCount', 'categoryTotals'
        ));
    }

    public function create()
    {
        return redirect()->route('admin.assets.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_name'     => 'required|string|max:255',
            'category'       => 'required|string',
            'quantity'       => 'required|integer|min:1',
            'purchase_price' => 'required|numeric|min:0',
            'purchase_date'  => 'required|date',
            'status'         => 'required|in:active,under_repair,disposed',
            'condition'      => 'required|in:good,fair,poor',
            'location'       => 'nullable|string|max:255',
            'description'    => 'nullable|string',
        ]);

        $asset = Asset::create($request->only([
            'asset_name', 'category', 'quantity', 'purchase_price',
            'purchase_date', 'description', 'status', 'condition', 'location',
        ]));

        return response()->json(['success' => true, 'asset' => $asset]);
    }

    public function edit(Asset $asset)
    {
        $data = $asset->toArray();
        $data['purchase_date'] = $asset->purchase_date
            ? $asset->purchase_date->format('Y-m-d')
            : null;
        return response()->json($data);
    }

    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'asset_name'     => 'required|string|max:255',
            'category'       => 'required|string',
            'quantity'       => 'required|integer|min:1',
            'purchase_price' => 'required|numeric|min:0',
            'purchase_date'  => 'required|date',
            'status'         => 'required|in:active,under_repair,disposed',
            'condition'      => 'required|in:good,fair,poor',
            'location'       => 'nullable|string|max:255',
            'description'    => 'nullable|string',
        ]);

        $asset->update($request->only([
            'asset_name', 'category', 'quantity', 'purchase_price',
            'purchase_date', 'description', 'status', 'condition', 'location',
        ]));

        return response()->json(['success' => true, 'asset' => $asset]);
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        return response()->json(['success' => true]);
    }
}
