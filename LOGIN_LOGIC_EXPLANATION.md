# Login Logic Explanation

## Current System Overview

The application uses a **single authentication guard (`web`)** with **role-based access control** to differentiate between admin and regular users.

### Key Components

1. **Single Guard System**: Both admin and user use the same `web` guard and `users` table
2. **Role-Based**: Users have a `role` field (`admin` or `user`)
3. **Separate Routes**: Different login URLs but same authentication system
4. **Session Management**: Only one user can be logged in at a time (shared session)

---

## Login Flow

### 1. User Login (`/login`)

**Route**: `routes/auth.php` (lines 20-23)
- **Middleware**: `guest` (blocks if already logged in)
- **Controller**: `AuthenticatedSessionController::create()` and `store()`

**Process**:
```
1. User visits /login
2. If already logged in → Can't access (guest middleware blocks)
3. User enters email/password
4. Controller checks:
   - Logs out any existing session first (line 39-43)
   - Authenticates user
   - If user is admin → Redirects to admin login page (line 52-56)
   - If user is regular → Logs in and redirects to home/checkout
5. Tracks login in user_logins table
```

**Issues**:
- ❌ If logged in as admin, can't access `/login` to switch to user account
- ❌ If logged in as user, can't access `/login` to switch to another user

---

### 2. Admin Login (`/admin/login`)

**Route**: `routes/admin.php` (lines 12-16)
- **Middleware**: None (accessible even when logged in)
- **Controller**: `AuthenticatedSessionController::createAdmin()` and `storeAdmin()`

**Process**:
```
1. User visits /admin/login
2. Can access even if already logged in (no guest middleware)
3. User enters email/password
4. Controller checks:
   - Logs out any existing session first (line 91-95)
   - Authenticates user
   - If user is NOT admin → Redirects to user login (line 111-115)
   - If user IS admin → Logs in and redirects to admin dashboard
5. Tracks login in user_logins table
```

**Issues**:
- ✅ Can switch accounts (no guest middleware)
- ⚠️ But this creates inconsistency with user login

---

## Session Management

### Current Behavior

**Single Session**: Only one user can be logged in at a time because:
- Both use the same `web` guard
- Both use the same session storage
- When you login as one user, the previous session is destroyed

**Login Tracking**:
- Each login creates a record in `user_logins` table
- Tracks: `user_id`, `login_type` (user/admin), `ip_address`, `session_id`, `logged_in_at`
- On logout, updates `logged_out_at`

---

## Middleware Protection

### `EnsureUserIsAdmin` Middleware

**Location**: `app/Http/Middleware/EnsureUserIsAdmin.php`

**Behavior**:
```
1. Check if authenticated
   - If NO → Redirect to /admin/login
2. Check if user role is 'admin'
   - If NO → Redirect to /admin/login (allows switching)
   - If YES → Allow access
```

**Issues**:
- ✅ Allows switching accounts (redirects to admin login)
- ⚠️ But user login route blocks switching (guest middleware)

---

## Problems Identified

### Problem 1: Inconsistent Access
- **User login** (`/login`) has `guest` middleware → Blocks if already logged in
- **Admin login** (`/admin/login`) has NO middleware → Allows switching
- **Result**: Can switch TO admin, but can't switch FROM admin to user

### Problem 2: Session Conflicts
- Both use same guard → Only one session at a time
- When switching, old session is destroyed
- This is actually correct behavior, but might confuse users

### Problem 3: Route Protection
- User login route blocks logged-in users
- Admin login route allows logged-in users
- Inconsistent user experience

---

## Recommended Solutions

### Option A: Allow Account Switching (Recommended)

**Remove `guest` middleware from user login route** to allow switching:

```php
// routes/auth.php
// Remove 'guest' middleware from login routes
Route::get('login', [AuthenticatedSessionController::class, 'create'])
    ->name('login');

Route::post('login', [AuthenticatedSessionController::class, 'store']);
```

**Benefits**:
- ✅ Consistent behavior (both routes allow switching)
- ✅ Users can switch between accounts easily
- ✅ Matches current admin login behavior

### Option B: Use Separate Guards (More Complex)

Create separate guards for admin and user:
- `web` guard for users
- `admin` guard for admins
- Separate session storage

**Benefits**:
- ✅ Can be logged in as both simultaneously
- ✅ More secure separation

**Drawbacks**:
- ❌ More complex implementation
- ❌ Requires significant refactoring

---

## Current Login Flow Diagram

```
┌─────────────────────────────────────────────────────────┐
│                    USER VISITS                          │
│              /login  or  /admin/login                   │
└─────────────────────────────────────────────────────────┘
                        │
                        ▼
        ┌───────────────────────────────┐
        │  Already Logged In?           │
        └───────────────────────────────┘
                │              │
         YES    │              │    NO
                ▼              ▼
    ┌──────────────┐   ┌──────────────┐
    │ /login       │   │ Enter        │
    │ BLOCKED      │   │ Credentials  │
    │ (guest)      │   └──────────────┘
    └──────────────┘           │
                                ▼
                    ┌──────────────────────┐
                    │ Logout Existing      │
                    │ Session First       │
                    └──────────────────────┘
                                │
                                ▼
                    ┌──────────────────────┐
                    │ Authenticate User    │
                    └──────────────────────┘
                                │
                ┌───────────────┴───────────────┐
                │                               │
                ▼                               ▼
    ┌──────────────────────┐      ┌──────────────────────┐
    │ Check Role           │      │ Check Role           │
    │                      │      │                      │
    │ Admin? → Redirect    │      │ Admin? → Allow      │
    │ to /admin/login      │      │ User? → Redirect     │
    │                      │      │ to /login            │
    └──────────────────────┘      └──────────────────────┘
```

---

## Testing the Current System

### Test Case 1: Login as User
1. Visit `/login`
2. Enter user credentials
3. ✅ Should login and redirect to home/checkout
4. Try to visit `/login` again
5. ❌ Should be blocked (guest middleware)

### Test Case 2: Login as Admin
1. Visit `/admin/login`
2. Enter admin credentials
3. ✅ Should login and redirect to admin dashboard
4. Try to visit `/login`
5. ❌ Should be blocked (guest middleware) - **THIS IS THE ISSUE**

### Test Case 3: Switch from User to Admin
1. Login as user
2. Visit `/admin/login`
3. ✅ Should be accessible (no guest middleware)
4. Enter admin credentials
5. ✅ Should logout user and login as admin

### Test Case 4: Switch from Admin to User
1. Login as admin
2. Visit `/login`
3. ❌ Should be blocked (guest middleware) - **THIS IS THE ISSUE**
4. Must logout first, then login as user

---

## Quick Fix

To allow account switching for both admin and user, remove the `guest` middleware from the login routes in `routes/auth.php`:

```php
// Change from:
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

// To:
Route::get('login', [AuthenticatedSessionController::class, 'create'])
    ->name('login');
Route::post('login', [AuthenticatedSessionController::class, 'store']);
```

This will make user login behave the same as admin login (allowing account switching).

