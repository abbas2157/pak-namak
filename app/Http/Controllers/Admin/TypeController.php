<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Title;
use Illuminate\Http\Request;
class TypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $types = Title::paginate('5');
        return view('admin.title.index', compact('types'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.title.create');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required'
        ]);
        $title = new Title();
        $title->title = $request->title;
        $title->save();
        return redirect()->route('admin.types.index')->with('success', 'Title created successfully!');
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
    public function edit(Title $type)
    {

        return view('admin.title.edit', compact('type'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Title $type)
    {
        $request->validate([
            'title' => 'required'
        ]);
        $type->title = $request->title;
        $type->save();
        return redirect()->route('admin.types.index')->with('success', 'Title updated successfully!');

    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Title $type)
    {
        $type->delete();
        return redirect()->route('admin.types.index')->with('success', 'Title deleted successfully!');
    }
}
