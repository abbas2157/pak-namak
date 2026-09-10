<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\SaltType;
use Illuminate\Http\Request;
class TypeController extends Controller
{

    public function index()
    {
        $types = SaltType::orderByDesc('id')->get();
        return view('admin.types.index', compact('types'));

    }

    public function create()
    {
        // The index page adds types through a modal; returning the index view
        // from here rendered it without $types and threw. Redirect instead.
        return redirect()->route('admin.types.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required'
        ]);
        $type = new SaltType();
        $type->title = $request->title;
        $type->save();
         return response()->json(['id' => $type->id,'title' => $type->title,'created_at' => $type->created_at->format('Y-m-d')]);
     }

    public function edit(SaltType $type)
    {
        return response()->json([
            'id' => $type->id,
            'title' => $type->title
        ]);
    }

    public function update(Request $request, SaltType $type)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $type->title = $request->title;
        $type->save();

        return response()->json([
            'id' => $type->id,
            'title' => $type->title,
            'created_at' => $type->created_at->format('Y-m-d')
        ]);
    }

    public function destroy(SaltType $type)
    {
        $type->delete();
        return response()->json(['success' => true,'data' => $type ]);
    }
}
