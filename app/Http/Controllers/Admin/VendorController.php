<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vendors = Vendor::Paginate(5);
        return view('admin.vendors.index', compact('vendors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.vendors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vendor $vendor)
    {
        return view('admin.vendors.edit', compact('vendor'));
    }

    /**
     * Update the specified resource in storage.
     */
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Vendor::find($id);
        $data->delete();
        return redirect()->route('admin.vendors.index')->with('success', 'Vendor deleted successfully!');
    }
}
