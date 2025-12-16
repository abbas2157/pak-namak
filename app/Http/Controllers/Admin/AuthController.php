<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login()
    {
        return view('admin.auth.login');
    }
    public function auth(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $auth = $request->only('email', 'password');
        if (Auth::attempt(credentials: $auth)) {
            return redirect()->route('admin.dashboard')->with('success', 'Login successful!');
        }
        return back()->with('error', 'Invalid email or password');
    }
}
