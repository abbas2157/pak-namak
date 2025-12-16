<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $packages = Package::Paginate(5);
        return view('admin.packages.index', compact('packages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.packages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'weight' => 'required'
        ]);
        $packages = new Package();
        $packages->title = $request->title;
        $packages->weight = $request->weight;
        $packages->save();
        return redirect()->route('admin.package.index')->with('success', 'Package created successfully!'); 
     }

    /**
     * Display the specified resource.
     */
    public function show( string $id)
    {
        return view('admin.packages.edit', compact('package'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Package $package)
    {
        return view('admin.packages.edit', compact('package'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, package $package)
    {
        $request->validate([
            'title' => 'required',
            'weight' => 'required'
        ]);
        $package->title = $request->title;
        $package->weight = $request->weight;
        $package->save();
        return redirect()->route('admin.package.index')->with('success', 'Package updated successfully!');
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(package $package)
    {
        $package->delete();
        return redirect()->route('admin.package.index')->with('success', 'Package deleted successfully!');
    }
}
