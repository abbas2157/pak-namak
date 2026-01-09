<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\SaltType;
use Illuminate\Http\Request;
class TypeController extends Controller
{

    public function index()
    {
        $types = SaltType::paginate('10');
        return view('admin.types.index', compact('types'));

    }

    public function create()
    {
        return view('admin.types.index');
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

    public function update(Request $request, SaltType $type,$id)
    {
            $request->validate([
            'title' => 'required'
        ]);

        $type = SaltType::findOrFail($id);
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
