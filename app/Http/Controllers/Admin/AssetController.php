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
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $this->saveImage($request->file('image'));
        }

        $asset = Asset::create(array_merge(
            $request->only([
                'asset_name', 'category', 'quantity', 'purchase_price',
                'purchase_date', 'description', 'status', 'condition', 'location',
            ]),
            ['image' => $imagePath]
        ));

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
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $data = $request->only([
            'asset_name', 'category', 'quantity', 'purchase_price',
            'purchase_date', 'description', 'status', 'condition', 'location',
        ]);

        if ($request->hasFile('image')) {
            if ($asset->image && file_exists(public_path($asset->image))) {
                unlink(public_path($asset->image));
            }
            $data['image'] = $this->saveImage($request->file('image'));
        }

        $asset->update($data);

        return response()->json(['success' => true, 'asset' => $asset]);
    }

    public function destroy(Asset $asset)
    {
        if ($asset->image && file_exists(public_path($asset->image))) {
            unlink(public_path($asset->image));
        }
        $asset->delete();
        return response()->json(['success' => true]);
    }

    private function saveImage($file): string
    {
        $uploadDir = public_path('uploads/assets');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $ext      = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $fileName = 'asset_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $file->move($uploadDir, $fileName);
        return 'uploads/assets/' . $fileName;
    }
}
