<!-- resources/views/admin/partials/sidebar.blade.php -->
<aside class="sidebar-wrapper">
    <div class="sidebar-header">
        <div>
            <img src="{{ asset('front/assets/img/Health Care Logo.png') }}" class="logo-icon" alt="logo icon">
        </div>
        <div>
            <h4 class="logo-text"></h4>
        </div>
        <div class="toggle-icon ms-auto">
            <i class="bi bi-list"></i>
        </div>
    </div>

    <ul class="metismenu" id="menu">
        <li>
            <a href="{{ route('admin.dashboard') }}">
                <div class="parent-icon"><i class="bi bi-house-fill"></i></div>
                <div class="menu-title">Dashboard</div>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.products.index') }}">
                <div class="parent-icon"><i class="bi bi-basket-fill"></i></div>
                <div class="menu-title">Products</div>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.categories.index') }}">
                <div class="parent-icon"><i class="bi bi-basket-fill"></i></div>
                <div class="menu-title">Category</div>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.orders.index') }}">
                <div class="parent-icon"><i class="bi bi-basket-fill"></i></div>
                <div class="menu-title">Orders</div>
            </a>
        </li>
    </ul>
</aside>
