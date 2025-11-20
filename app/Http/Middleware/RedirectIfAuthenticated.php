<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, $role)
    {
        if ($role === 'admin' && session()->has('admin_id')) {
            return redirect()->route('admin.dashboard');
        }

        if ($role === 'company' && session()->has('company_id')) {
            return redirect()->route('company.dashboard'); 
        }

        if ($role === 'driver' && session()->has('driver_id')) {
            return redirect()->route('driver.dashboard');
        }

        return $next($request);
    }
}
