<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\Admin;

class AuthController extends Controller
{
    // Show login form
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    // Handle login submission
    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'username' => 'required|string|min:3|max:50',
            'password' => 'required|string|min:6|max:50',
        ]);

        $throttleKey = Str::lower($request->input('username')) . '|' . $request->ip();
        $maxAttempts = 3;
        $decaySeconds = 60;

        // Check if user exceeded max attempts
        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return redirect()->back()
                ->withErrors([
                    'username' => "Too many login attempts. Please try again in $seconds seconds.",
                    'password' => "Too many login attempts. Please try again in $seconds seconds."
                ])
                ->withInput($request->only('username', 'password'));
        }

        // Step 1: Check if username exists
        $admin = Admin::where('username', $request->username)->first();

        if (!$admin) {
            // Username not found
            RateLimiter::hit($throttleKey, $decaySeconds);
            return redirect()->back()
                ->withErrors([
                    'username' => 'Incorrect username',
                ])
                ->withInput($request->only('username', 'password'));
        }

        // Step 2: Check password manually if username exists
        if (!Auth::guard('admin')->attempt(
            $request->only('username', 'password'),
            $request->filled('remember')
        )) {
            // Wrong password
            RateLimiter::hit($throttleKey, $decaySeconds);
            return redirect()->back()
                ->withErrors([
                    'password' => 'Incorrect password',
                ])
                ->withInput($request->only('username', 'password'));
        }

        // Step 3: Successful login
        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    // Admin dashboard
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    // Logout admin
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
