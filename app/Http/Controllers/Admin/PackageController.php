<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{

    public function index()
    {
        $packages = Package::Paginate(5);
        return view('admin.packages.index', compact('packages'));
    }


    public function create()
    {
        return view('admin.packages.create');
    }

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
        return redirect()->route('package.index')->with('success', 'Package created successfully!');
     }

    public function edit(Package $package)
    {
        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, package $package)
    {
        $request->validate([
            'title' => 'required',
            'weight' => 'required'
        ]);
        $package->title = $request->title;
        $package->weight = $request->weight;
        $package->save();
        return redirect()->route('package.index')->with('success', 'Package updated successfully!');

    }

    public function destroy(package $package)
    {
        $package->delete();
        return redirect()->route('package.index')->with('success', 'Package deleted successfully!');
    }
}
