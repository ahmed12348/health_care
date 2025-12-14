<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\UserLogin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the user login view.
     */
    public function create(): View
    {
        return view('auth.login', ['isAdmin' => false]);
    }

    /**
     * Display the admin login view.
     */
    public function createAdmin(): View
    {
        // Allow access even if already logged in as user
        // User can switch to admin login
        return view('auth.login', ['isAdmin' => true]);
    }

    /**
     * Handle an incoming user authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // If already logged in, logout first to allow new login
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // Check if user is admin trying to login from user login page
        if ($user->role === 'admin') {
            Auth::logout();
            return redirect()->route('admin.login')
                ->with('error', 'Please use admin login page to access admin panel.');
        }

        // Track user login
        UserLogin::create([
            'user_id' => $user->id,
            'login_type' => 'user',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'logged_in_at' => now(),
            'session_id' => $request->session()->getId(),
        ]);

        // Set Arabic as default language after login
        if (!session()->has('locale')) {
            session(['locale' => 'ar', 'direction' => 'rtl']);
        }
        
        // Get intended URL but make sure it's not an admin route
        $intended = $request->session()->pull('url.intended', route('frontend.home', absolute: false));
        
        // If intended URL is an admin route, redirect to home instead
        if (str_contains($intended, '/admin/')) {
            $intended = route('frontend.home', absolute: false);
        }

        // Redirect to intended URL (like /checkout) or home page for regular users
        return redirect($intended);
    }

    /**
     * Handle an incoming admin authentication request.
     */
    public function storeAdmin(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // If already logged in, logout first to allow new login
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // Use web guard for authentication (same users table)
        if (!Auth::guard('web')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return redirect()->route('admin.login')
                ->withErrors([
                    'email' => trans('auth.failed'),
                ])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Check if user is not admin
        if ($user->role !== 'admin') {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Access denied. Admin access required.');
        }

        // Track admin login
        UserLogin::create([
            'user_id' => $user->id,
            'login_type' => 'admin',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'logged_in_at' => now(),
            'session_id' => $request->session()->getId(),
        ]);
        
        // Set Arabic as default language after login
        if (!session()->has('locale')) {
            session(['locale' => 'ar', 'direction' => 'rtl']);
        }

        // Redirect to admin dashboard
        return redirect()->intended(route('admin.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated user session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $sessionId = $request->session()->getId();
        
        // Track logout
        if (Auth::check()) {
            UserLogin::where('session_id', $sessionId)
                ->whereNull('logged_out_at')
                ->update(['logged_out_at' => now()]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
        
        // Set Arabic as default language after logout
        session(['locale' => 'ar', 'direction' => 'rtl']);

        return redirect('/');
    }

    /**
     * Destroy an authenticated admin session.
     */
    public function destroyAdmin(Request $request): RedirectResponse
    {
        $sessionId = $request->session()->getId();
        
        // Track logout
        if (Auth::check()) {
            UserLogin::where('session_id', $sessionId)
                ->whereNull('logged_out_at')
                ->update(['logged_out_at' => now()]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
        
        // Set Arabic as default language after logout
        session(['locale' => 'ar', 'direction' => 'rtl']);

        return redirect()->route('admin.login');
    }
}
