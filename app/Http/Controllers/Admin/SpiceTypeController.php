<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpiceType;
use Illuminate\Http\Request;

class SpiceTypeController extends Controller
{
    public function index()
    {
        $types = SpiceType::orderByDesc('id')->get();
        return view('admin.spice-types.index', compact('types'));
    }

    public function create()
    {
        return view('admin.spice-types.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
        ]);

        $type = SpiceType::create(['title' => $request->title]);

        return response()->json(['id' => $type->id, 'title' => $type->title, 'created_at' => $type->created_at->format('Y-m-d')]);
    }

    public function edit(SpiceType $type)
    {
        return response()->json([
            'id' => $type->id,
            'title' => $type->title,
        ]);
    }

    public function update(Request $request, SpiceType $type)
    {
        $request->validate([
            'title' => 'required',
        ]);

        $type->update(['title' => $request->title]);

        return response()->json([
            'id' => $type->id,
            'title' => $type->title,
            'created_at' => $type->created_at->format('Y-m-d'),
        ]);
    }

    public function destroy(SpiceType $type)
    {
        $type->delete();
        return response()->json(['success' => true, 'data' => $type]);
    }
}
