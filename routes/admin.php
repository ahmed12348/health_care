<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

// ============================================
// ADMIN AUTHENTICATION ROUTES
// ============================================

// Admin Login (accessible to everyone, even if logged in as user)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'createAdmin'])
        ->name('login');
    
    Route::post('login', [AuthenticatedSessionController::class, 'storeAdmin'])
        ->name('login.store');
});

// Admin Logout (requires authentication)
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroyAdmin'])
        ->name('logout');
});

