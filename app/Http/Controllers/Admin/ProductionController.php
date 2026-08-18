<?php

namespace App\Http\Controllers\Admin;

use App\Models\Production;
use App\Models\Account;
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

        $accounts = Account::where('is_active', true)->orderBy('name')->get();

        return view('admin.productions.index', compact(
            'productions',
            'totalRaw', 'totalFinished', 'totalWastage', 'totalCost', 'efficiency', 'accounts'
        ));
    }

    public function create()
    {
        return redirect()->route('admin.productions.index');
    }

    public function store(Request $request)
    {
        $request->merge(['account_id' => $request->account_id ?: null]);

        $request->validate([
            'production_date'       => 'required|date',
            'raw_salt_used'         => 'required|numeric|min:0',
            'finished_salt'         => 'required|numeric|min:0',
            'wastage'               => 'nullable|numeric|min:0',
            'electricity_fuel_cost' => 'nullable|numeric|min:0',
            'account_id'            => 'nullable|exists:accounts,id',
        ]);

        $production = Production::create($request->only([
            'production_date', 'raw_salt_used', 'finished_salt',
            'wastage', 'machine_used', 'electricity_fuel_cost', 'remarks', 'account_id',
        ]));

        return response()->json(['success' => true, 'production' => $production]);
    }

    public function edit(Production $production)
    {
        return response()->json($production);
    }

    public function update(Request $request, Production $production)
    {
        $request->merge(['account_id' => $request->account_id ?: null]);

        $request->validate([
            'production_date'       => 'required|date',
            'raw_salt_used'         => 'required|numeric|min:0',
            'finished_salt'         => 'required|numeric|min:0',
            'wastage'               => 'nullable|numeric|min:0',
            'electricity_fuel_cost' => 'nullable|numeric|min:0',
            'account_id'            => 'nullable|exists:accounts,id',
        ]);

        $production->update($request->only([
            'production_date', 'raw_salt_used', 'finished_salt',
            'wastage', 'machine_used', 'electricity_fuel_cost', 'remarks', 'account_id',
        ]));

        return response()->json(['success' => true, 'production' => $production]);
    }

    public function destroy(Production $production)
    {
        $production->delete();
        return response()->json(['success' => true]);
    }
}
