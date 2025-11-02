<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     * This middleware allows unauthenticated (guest) users through,
     * and redirects authenticated users to the admin dashboard.
     */
    public function handle(Request $request, Closure $next, string ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // User is authenticated - redirect to admin
                return redirect('/admin/dashboard');
            }
        }

        // User is not authenticated - allow them through
        return $next($request);
    }
}
