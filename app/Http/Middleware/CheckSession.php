<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSession
{
    // protected $timeout;

    // public function __construct()
    // {
    //     // Read timeout (in seconds) from .env, default = 1800 (30 mins)
    //     $this->timeout = env('SESSION_TIMEOUT', 20);
    // }

    public function handle(Request $request, Closure $next, $role)
    {
    //     // --- Session Timeout Check ---
    //     if (session()->has('last_activity')) {
    //         if (time() - session('last_activity') > $this->timeout) {
    //             session()->flush(); // clear session
    //             if ($role === 'admin') {
    //                 return redirect()->route('admin.login')->with('error', 'Session expired, please login again.');
    //             }
    //             if ($role === 'company') {
    //                 return redirect()->route('company.login')->with('error', 'Session expired, please login again.');
    //             }
    //             if ($role === 'driver') {
    //                 return redirect()->route('driver.login')->with('error', 'Session expired, please login again.');
    //             }
    //         }
    //     }
    //     session(['last_activity' => time()]);
        // --- End Timeout Check ---

        // Prevent going back to login if already logged in
        if ($role === 'admin' && session()->has('admin_id') && $request->is('admin/login')) {
            return redirect()->route('admin.dashboard');
        }

        if ($role === 'company' && session()->has('company_id') && $request->is('company/login')) {
            return redirect()->route('company.dashboard');
        }

        if ($role === 'driver' && session()->has('driver_id') && $request->is('driver/login')) {
            return redirect()->route('driver.dashboard');
        }

        // Redirect if not logged in
        if ($role === 'admin' && !session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Session expired. Please login again.');
        }

        if ($role === 'company' && !session()->has('company_id')) {
            return redirect()->route('company.login')->with('invalid', 'Session expired. Please login again.');
        }

        if ($role === 'driver' && !session()->has('driver_id')) {
            return redirect()->route('driver.login')->with('invalid', 'Session expired. Please login again.');
        }

        return $next($request);
    }
}
