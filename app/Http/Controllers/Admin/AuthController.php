<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AuthController extends Controller
{
        public function showLoginForm()
    {
        return view('admin.auth.login');
    }

        public function login(Request $request)
    {
        // Use IP-based throttle key to count all failed attempts from this IP
        $throttleKey = 'admin|' . $request->ip();
        $maxAttempts   = 3;
        $decaySeconds  = 60;

        // Clear attempts if timer expired naturally
        if ($request->query('clear_attempts') == '1') {
            RateLimiter::clear($throttleKey);
            return redirect()->route('admin.login');
        }

        // Check if rate limited
        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            
            // If lockout has expired on server, clear it
            if ($seconds <= 0) {
                RateLimiter::clear($throttleKey);
            } else {
                $lockoutTime = now()->addSeconds($seconds)->timestamp;
            
                return back()
                    ->withErrors(['lockout' => "Too many login attempts. Please try again in $seconds seconds."])
                    ->withInput($request->only('username'))
                    ->with('lockout_until', $lockoutTime);
            }
        }

        $admin = Admin::whereRaw('BINARY username = ?', [$request->username])->first();

        if (!$admin) {
            // Count this as a failed attempt
            RateLimiter::hit($throttleKey, $decaySeconds);
            $currentAttempts = RateLimiter::attempts($throttleKey);
            $attemptsLeft = $maxAttempts - $currentAttempts;
            
            // Check if this was the last attempt
            if ($attemptsLeft <= 0) {
                $lockoutTime = now()->addSeconds($decaySeconds)->timestamp;
                return back()
                    ->withErrors(['lockout' => "Too many login attempts. Please try again in $decaySeconds seconds."])
                    ->withInput($request->only('username'))
                    ->with('lockout_until', $lockoutTime);
            }

            return redirect()->back()->with('error', 'Invalid Credentials');
        }

        // Password check
        if (!\Illuminate\Support\Facades\Hash::check($request->password, $admin->password)) {
            // Count this as a failed attempt
            RateLimiter::hit($throttleKey, $decaySeconds);
            $currentAttempts = RateLimiter::attempts($throttleKey);
            $attemptsLeft = $maxAttempts - $currentAttempts;
            
            // Check if this was the last attempt
            if ($attemptsLeft <= 0) {
                $lockoutTime = now()->addSeconds($decaySeconds)->timestamp;
                return back()
                    ->withErrors(['lockout' => "Too many login attempts. Please try again in $decaySeconds seconds."])
                    ->withInput($request->only('username'))
                    ->with('lockout_until', $lockoutTime);
            }
            
            return redirect()->back()->with('error', 'Invalid Credentials');
        }

        // Login success - clear all attempts
        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();
        $request->session()->put('admin_id', $admin->id);

        return redirect()->intended(route('admin.dashboard'));
    }

        public function dashboard()
    {
        return view('admin.dashboard');
    }

 public function logout(Request $request)
{
    Auth::guard('admin')->logout();

    // Remove only admin session data
    $request->session()->forget(['admin_id', 'admin_name', 'admin_email']);
    
    // Keep session valid for others
    $request->session()->regenerateToken();

    return redirect()->route('admin.login');
}

}
