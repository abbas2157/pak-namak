<?php

namespace App\Http\Controllers\Admin;

use App\Models\Production;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductionController extends Controller
{
    public function index()
    {
        $productions = Production::orderByDesc('id')->get();

        $totalRaw      = $productions->sum('raw_salt_used');
        $totalFinished = $productions->sum('finished_salt');
        $totalWastage  = $productions->sum('wastage');
        $totalCost     = $productions->sum('electricity_fuel_cost');
        $efficiency    = $totalRaw > 0 ? round(($totalFinished / $totalRaw) * 100, 1) : 0;

        return view('admin.productions.index', compact(
            'productions',
            'totalRaw', 'totalFinished', 'totalWastage', 'totalCost', 'efficiency'
        ));
    }

    public function create()
    {
        return redirect()->route('admin.productions.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'production_date'       => 'required|date',
            'raw_salt_used'         => 'required|numeric|min:0',
            'finished_salt'         => 'required|numeric|min:0',
            'wastage'               => 'nullable|numeric|min:0',
            'electricity_fuel_cost' => 'nullable|numeric|min:0',
        ]);

        $production = Production::create($request->only([
            'production_date', 'raw_salt_used', 'finished_salt',
            'wastage', 'machine_used', 'electricity_fuel_cost', 'remarks',
        ]));

        return response()->json(['success' => true, 'production' => $production]);
    }

    public function edit(Production $production)
    {
        return response()->json($production);
    }

    public function update(Request $request, Production $production)
    {
        $request->validate([
            'production_date'       => 'required|date',
            'raw_salt_used'         => 'required|numeric|min:0',
            'finished_salt'         => 'required|numeric|min:0',
            'wastage'               => 'nullable|numeric|min:0',
            'electricity_fuel_cost' => 'nullable|numeric|min:0',
        ]);

        $production->update($request->only([
            'production_date', 'raw_salt_used', 'finished_salt',
            'wastage', 'machine_used', 'electricity_fuel_cost', 'remarks',
        ]));

        return response()->json(['success' => true, 'production' => $production]);
    }

    public function destroy(Production $production)
    {
        $production->delete();
        return response()->json(['success' => true]);
    }
}
