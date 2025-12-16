<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;   
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class DashboardController extends Controller
{
    public function login()
    {
        return view('admin.auth.login');
    }
    public function adminlogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $auth = $request->only('email', 'password');
        if (Auth::attempt($auth)) {
            return redirect()->route('home')->with('success', 'Login successful!');
        }
        return back()->with('error', 'Invalid email or password');
    }

    public function home()
    {
        return view('admin.layout.app');
    }
    public function index()
    {
        $posts = Post::all();
        return view('admin.posts.index', compact('posts'));
    }
    public function create()
    {
        return view('admin.posts.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required'
        ]);
        $data = new Post();
        $data->title = $request->title;
        $data->save();
        return redirect()->route('posts.index')->with('success', 'Post created successfully!');
    }
     public function edit($id){
        $post = Post::find($id);
        return view('admin.posts.edit', compact('post'));
        
    }
    public function update(Request $request, $id){
        $request->validate([
            'title' => 'required'
        ]);
        $data = Post::find($id);
        $data->title = $request->title;
        $data->save();
        return redirect()->route('posts.index')->with('success', 'Post updated successfully!');
    }
    public function delete($id){
        $post = Post::find($id);
        $post->delete();
        return redirect()->route('posts.index')->with('success', 'Post deleted successfully!');
    }


}