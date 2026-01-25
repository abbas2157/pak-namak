<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::orderBy('id', 'desc')->get();
        return view('admin.vendors.index', compact('vendors'));
    }

    public function create()
    {
        return view('admin.vendors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'shop' => 'required',
            'phone' => 'required',
            'address' => 'required'
        ]);
        $data = new Vendor();
        $data->name = $request->name;
        $data->shop = $request->shop;
        $data->phone = $request->phone;
        $data->address = $request->address;
        $data->save();
        return redirect()->route('admin.vendors.index')->with('success', 'Vendor created successfully!');
    }

    public function edit(Vendor $vendor)
    {
        return view('admin.vendors.edit', compact('vendor'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'shop' => 'required',
            'phone' => 'required',
            'address' => 'required'

        ]);
        $data = Vendor::find($id);
        $data->name = $request->name;
        $data->shop = $request->shop;
        $data->phone = $request->phone;
        $data->address = $request->address;
        $data->save();
        return redirect()->route('admin.vendors.index')->with('success', 'Vendor updated successfully!');
    }

    public function destroy(string $id)
    {
        $data = Vendor::find($id);
        $data->delete();
        return redirect()->route('admin.vendors.index')->with('success', 'Vendor deleted successfully!');
    }
}
