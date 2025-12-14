<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            // Store intended URL and redirect to admin login
            $request->session()->put('url.intended', $request->url());
            return redirect()->route('admin.login')
                ->with('error', 'Please login to access admin panel.');
        }

        // Check if user has admin role
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            // If user is logged in but not admin, redirect to admin login page
            // This allows them to logout and login as admin
            return redirect()->route('admin.login')
                ->with('error', 'Access denied. Please login as admin to access this page.');
        }

        return $next($request);
    }
}

