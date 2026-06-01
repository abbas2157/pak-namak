<?php

namespace App\Http\Controllers\Admin;

use App\Models\Area;
use App\Models\City;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index(Request $request)
    {
        $cities = City::orderBy('name')->get();

        $areasQuery = Area::with(['city', 'shops' => function ($q) {
            $q->withSum('sales', 'total_amount')
              ->withSum('sales', 'pending_amount')
              ->withCount('sales');
        }])->withCount('shops');

        if ($request->city_id) {
            $areasQuery->where('city_id', $request->city_id);
        }

        $areas = $areasQuery->orderBy('name')->get()->map(function ($area) {
            $area->total_revenue = $area->shops->sum('sales_sum_total_amount');
            $area->total_pending = $area->shops->sum('sales_sum_pending_amount');
            $area->total_sales   = $area->shops->sum('sales_count');
            return $area;
        });

        $totalAreas   = $areas->count();
        $totalShops   = $areas->sum('shops_count');
        $totalRevenue = $areas->sum('total_revenue');
        $totalPending = $areas->sum('total_pending');

        $selectedCity = $request->city_id;

        return view('admin.areas.index', compact(
            'areas', 'cities', 'totalAreas', 'totalShops',
            'totalRevenue', 'totalPending', 'selectedCity'
        ));
    }

    // Return all areas for a city (used by shop form AJAX)
    public function byCity(City $city)
    {
        return response()->json($city->areas);
    }

    public function store(Request $request)
    {
        $request->validate([
            'city_id' => 'required|exists:cities,id',
            'name'    => 'required|string|max:255',
        ]);

        $exists = Area::where('city_id', $request->city_id)
            ->whereRaw('LOWER(name) = ?', [strtolower($request->name)])
            ->exists();
        if ($exists) {
            return response()->json(['errors' => ['name' => ['This area already exists in this city.']]], 422);
        }

        $area = Area::create($request->only('city_id', 'name'));
        $area->load('city');

        return response()->json(['success' => true, 'area' => $area]);
    }

    public function edit($id)
    {
        return response()->json(Area::with('city')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $area = Area::findOrFail($id);

        $request->validate([
            'city_id' => 'required|exists:cities,id',
            'name'    => 'required|string|max:255',
        ]);

        $exists = Area::where('city_id', $request->city_id)
            ->where('id', '!=', $id)
            ->whereRaw('LOWER(name) = ?', [strtolower($request->name)])
            ->exists();
        if ($exists) {
            return response()->json(['errors' => ['name' => ['This area already exists in this city.']]], 422);
        }

        $area->update($request->only('city_id', 'name'));
        $area->load('city');

        return response()->json(['success' => true, 'area' => $area]);
    }

    public function destroy($id)
    {
        Area::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
