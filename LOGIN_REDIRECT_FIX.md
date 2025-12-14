# Login Redirect Fix

## Problem

When accessing admin routes (e.g., `/admin/products`) without being authenticated, users were being redirected to `/login` instead of `/admin/login`.

## Root Cause

The admin routes were using both `auth` and `admin` middleware:
```php
Route::middleware(['auth', 'admin'])->prefix('admin')...
```

**Issue**: The `auth` middleware runs **before** the `admin` middleware. When a user is not authenticated:
1. `auth` middleware catches it first
2. Redirects to `/login` (Laravel's default)
3. `admin` middleware never runs (can't redirect to `/admin/login`)

## Solution

Removed `auth` middleware from admin routes because:
- The `EnsureUserIsAdmin` middleware already checks authentication (`Auth::check()`)
- It already redirects to `/admin/login` if not authenticated
- Having both is redundant and causes the redirect issue

**Fixed Code**:
```php
// Only using 'admin' middleware
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    // Admin routes...
});
```

## How It Works Now

### Admin Routes Flow:
```
1. User visits /admin/products (not authenticated)
2. 'admin' middleware runs
3. Checks Auth::check() → false
4. Redirects to /admin/login ✅
5. After login, redirects back to /admin/products
```

### User Routes Flow:
```
1. User visits /checkout (not authenticated)
2. 'auth' middleware runs
3. Redirects to /login ✅
4. After login, redirects back to /checkout
```

## Middleware Responsibilities

### `auth` Middleware (Laravel Default)
- Checks if user is authenticated
- Redirects to `/login` if not authenticated
- Used for: User routes (checkout, cart, etc.)

### `admin` Middleware (EnsureUserIsAdmin)
- Checks if user is authenticated
- Checks if user has `admin` role
- Redirects to `/admin/login` if not authenticated or not admin
- Used for: Admin routes (dashboard, products, orders, etc.)

## Testing

### Test 1: Access Admin Route (Not Authenticated)
1. Logout (if logged in)
2. Visit: `http://127.0.0.1:8000/admin/products`
3. ✅ Should redirect to: `/admin/login`

### Test 2: Access Admin Route (Authenticated as User)
1. Login as regular user
2. Visit: `http://127.0.0.1:8000/admin/products`
3. ✅ Should redirect to: `/admin/login` (with error message)

### Test 3: Access Admin Route (Authenticated as Admin)
1. Login as admin
2. Visit: `http://127.0.0.1:8000/admin/products`
3. ✅ Should show products page

### Test 4: Access User Route (Not Authenticated)
1. Logout (if logged in)
2. Visit: `http://127.0.0.1:8000/checkout`
3. ✅ Should redirect to: `/login`

## Files Changed

- `routes/web.php`: Removed `auth` middleware from admin routes group

