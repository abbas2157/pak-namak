<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use App\Models\Sale;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::withCount('shops')
            ->with([
                'areas.shops',
                'shops' => function ($q) {
                    $q->withSum('sales', 'total_amount')
                      ->withSum('sales', 'pending_amount')
                      ->withCount('sales');
                },
            ])
            ->orderBy('name')
            ->get()
            ->map(function ($city) {
                $city->total_revenue = $city->shops->sum('sales_sum_total_amount');
                $city->total_pending = $city->shops->sum('sales_sum_pending_amount');
                $city->total_sales   = $city->shops->sum('sales_count');
                return $city;
            });

        $totalCities  = $cities->count();
        $totalRevenue = $cities->sum('total_revenue');
        $totalPending = $cities->sum('total_pending');
        $totalShops   = $cities->sum('shops_count');

        return view('admin.cities.index', compact(
            'cities', 'totalCities', 'totalRevenue', 'totalPending', 'totalShops'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:cities,name',
        ]);

        $city = City::create($request->only('name'));

        return response()->json(['success' => true, 'city' => $city]);
    }

    public function edit($id)
    {
        return response()->json(City::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $city = City::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:cities,name,' . $id,
        ]);

        $city->update($request->only('name'));

        return response()->json(['success' => true, 'city' => $city]);
    }

    public function destroy($id)
    {
        City::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function sales(City $city, Request $request)
    {
        $selectedMonth = $request->month;

        $salesQuery = Sale::whereHas('shop', function ($q) use ($city) {
            $q->where('city_id', $city->id);
        })->with('shop')->orderByDesc('sale_date');

        if ($selectedMonth) {
            $salesQuery->where('sale_date', 'like', $selectedMonth . '%');
        }

        $sales = $salesQuery->get();

        // Group by shop for shop breakdown
        $shopStats = $sales->groupBy('shop_id')->map(function ($shopSales) {
            return (object) [
                'shop'     => $shopSales->first()->shop,
                'count'    => $shopSales->count(),
                'total'    => $shopSales->sum('total_amount'),
                'received' => $shopSales->sum('received_amount'),
                'pending'  => $shopSales->sum('pending_amount'),
            ];
        })->sortByDesc('total')->values();

        $totalRevenue  = $sales->sum('total_amount');
        $totalReceived = $sales->sum('received_amount');
        $totalPending  = $sales->sum('pending_amount');

        // Build month list (Oct 2025 → now)
        $months = [];
        $cursor = Carbon::create(2025, 10, 1);
        while ($cursor->lte(Carbon::now())) {
            $months[] = (object) [
                'value' => $cursor->format('Y-m'),
                'label' => $cursor->format('F Y'),
            ];
            $cursor->addMonth();
        }
        $months = array_reverse($months);

        return view('admin.cities.sales', compact(
            'city', 'sales', 'shopStats',
            'totalRevenue', 'totalReceived', 'totalPending',
            'selectedMonth', 'months'
        ));
    }
}
