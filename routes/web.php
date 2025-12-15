<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController as FrontendProductController;
use App\Http\Controllers\Frontend\CategoryController as FrontendCategoryController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\LanguageController;
use App\Http\Controllers\Frontend\WishlistController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\CartController;
use Illuminate\Support\Facades\Route;

// ============================================
// PUBLIC FRONTEND ROUTES (Customer-facing)
// ============================================

Route::get('/', [HomeController::class, 'index'])->name('frontend.home');
Route::get('/products', [FrontendProductController::class, 'index'])->name('frontend.products.index');
Route::get('/products/{id}', [FrontendProductController::class, 'show'])->name('frontend.products.show');
Route::get('/categories/{id}', [FrontendCategoryController::class, 'show'])->name('frontend.categories.show');
Route::get('/contact', [ContactController::class, 'index'])->name('frontend.contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
Route::post('/language/switch', [LanguageController::class, 'switch'])->name('frontend.language.switch');

// Cart routes
Route::get('/cart', [CartController::class, 'index'])->name('frontend.cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('frontend.cart.add');
Route::put('/cart/update/{key}', [CartController::class, 'update'])->name('frontend.cart.update');
Route::put('/cart/update-variant/{key}', [CartController::class, 'updateVariant'])->name('frontend.cart.updateVariant');
Route::delete('/cart/remove/{key}', [CartController::class, 'remove'])->name('frontend.cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('frontend.cart.clear');

// Checkout routes (require authentication)
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('frontend.checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('frontend.checkout.store');
});

// Wishlist routes (require authentication)
Route::middleware('auth')->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('frontend.wishlist.index');
    Route::post('/wishlist/toggle/{productId}', [WishlistController::class, 'toggle'])->name('frontend.wishlist.toggle');
    Route::get('/wishlist/count', [WishlistController::class, 'count'])->name('frontend.wishlist.count');
    Route::get('/wishlist/check/{productId}', [WishlistController::class, 'check'])->name('frontend.wishlist.check');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
});

// ============================================
// ADMIN ROUTES (with /admin prefix)
// ============================================

// Note: Only using 'admin' middleware, not 'auth'
// The 'admin' middleware (EnsureUserIsAdmin) already checks authentication
// and redirects to /admin/login if not authenticated
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    
    // Admin Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Admin Products
    Route::resource('/products', ProductController::class);
    
    // Admin Categories
    Route::resource('/categories', CategoryController::class);
    
    // Admin Orders
    Route::resource('/orders', OrderController::class);
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    
    // Additional Product Routes
    Route::get('/products/{productId}/variants', [ProductController::class, 'getVariants'])->name('products.variants');
    Route::get('/get-products/{category_id}', [ProductController::class, 'getProducts'])->name('products.byCategory');
    Route::get('/get-product-price/{product_id}', [ProductController::class, 'getPrice'])->name('products.price');
});

require __DIR__.'/auth.php';
