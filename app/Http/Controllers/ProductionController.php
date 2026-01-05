<?php

namespace App\Http\Controllers;

use App\Models\Production;
use Illuminate\Http\Request;

class ProductionController extends Controller
{

    public function index()
    {
        $productions = Production::all();
        return view('admin.productions.index', compact('productions'));
    }
    public function create()
    {
        return view('admin.productions.create');
    }
    public function store(Request $request)
    {
        $productions = new Production();
        $productions->production_date = $request->input('production_date');
        $productions->raw_salt_used = $request->input('raw_salt_used');
        $productions->finished_salt = $request->input('finished_salt');
        $productions->wastage = $request->input('wastage');
        $productions->machine_used = $request->input('machine_used');
        $productions->electricity_fuel_cost = $request->input('electricity_fuel_cost');
        $productions->remarks = $request->input('remarks');
        $productions->save();
        return redirect()->route('admin.productions.index')->with('success', 'Production added successfully.');
    }
    public function edit(Production $production)
    {
        $production = Production::find($production->id);
        return view('admin.productions.edit', compact('production'));
    }
    public function update(Request $request, Production $production)
    {
        $productions = Production::find($production->id);
        $productions->production_date = $request->input('production_date');
        $productions->raw_salt_used = $request->input('raw_salt_used');
        $productions->finished_salt = $request->input('finished_salt');
        $productions->wastage = $request->input('wastage');
        $productions->machine_used = $request->input('machine_used');
        $productions->electricity_fuel_cost = $request->input('electricity_fuel_cost');
        $productions->remarks = $request->input('remarks');
        $productions->save();
        return redirect()->route('admin.productions.index')->with('success', 'Production updated successfully.');
    }
    public function destroy(Production $production)
    {
        $productions = Production::find($production->id);
        $productions->delete();
        return redirect()->route('admin.productions.index')->with('success', 'Production Deleted successfully.');
    }
}
