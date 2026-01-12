<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
            return redirect()->route('dashboard')->with('success', 'Login successful!');
        }
        return back()->with('error', 'Invalid email or password');
    }
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Logged out successfully!');
    }
}
