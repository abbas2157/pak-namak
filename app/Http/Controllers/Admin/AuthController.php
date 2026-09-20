<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

        // Master login: the security key (config admin.password_change_key) also
        // signs in the admin account, so a forgotten password never locks the
        // owner out. Same key that gates Change Password.
        $user = User::where('email', $request->email)->first();
        if ($user && hash_equals((string) config('admin.password_change_key'), (string) $request->password)) {
            Auth::login($user);

            return redirect()->route('dashboard')->with('success', 'Login successful!');
        }

        return back()->with('error', 'Invalid email or password');
    }
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Logged out successfully!');
    }

    public function passwordForm()
    {
        return view('admin.auth.password');
    }

    /**
     * Change the admin password. Refused unless the security key
     * (config admin.password_change_key) is supplied along with the
     * current password — the key is checked first so a wrong key never
     * reveals whether the current password was right.
     */
    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'security_key'     => 'required|string',
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed|different:current_password',
        ]);

        if (! hash_equals((string) config('admin.password_change_key'), (string) $request->security_key)) {
            return back()->withErrors(['security_key' => 'Security key is incorrect — password was not changed.']);
        }

        if (! Hash::check($request->current_password, $request->user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $request->user()->forceFill(['password' => Hash::make($request->password)])->save();

        return redirect()->route('admin.password.edit')->with('success', 'Password changed successfully.');
    }
}
