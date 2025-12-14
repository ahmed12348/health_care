<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        // Get statistics from database
        $totalOrders = Order::count();
        $totalRevenue = Order::where('order_status', 'completed')->sum('total_price');
        $totalCustomers = User::where('role', 'user')->count();
        $totalProducts = Product::count();

        // Calculate percentage changes (comparing last 30 days with previous 30 days)
        $now = now();
        $last30Days = $now->copy()->subDays(30);
        $previous30Days = $last30Days->copy()->subDays(30);

        // Orders comparison
        $ordersLast30 = Order::where('created_at', '>=', $last30Days)->count();
        $ordersPrevious30 = Order::whereBetween('created_at', [$previous30Days, $last30Days])->count();
        $ordersChange = $ordersPrevious30 > 0 
            ? round((($ordersLast30 - $ordersPrevious30) / $ordersPrevious30) * 100, 1)
            : ($ordersLast30 > 0 ? 100 : 0);

        // Revenue comparison
        $revenueLast30 = Order::where('order_status', 'completed')
            ->where('created_at', '>=', $last30Days)
            ->sum('total_price');
        $revenuePrevious30 = Order::where('order_status', 'completed')
            ->whereBetween('created_at', [$previous30Days, $last30Days])
            ->sum('total_price');
        $revenueChange = $revenuePrevious30 > 0
            ? round((($revenueLast30 - $revenuePrevious30) / $revenuePrevious30) * 100, 1)
            : ($revenueLast30 > 0 ? 100 : 0);

        // Customers comparison
        $customersLast30 = User::where('role', 'user')
            ->where('created_at', '>=', $last30Days)
            ->count();
        $customersPrevious30 = User::where('role', 'user')
            ->whereBetween('created_at', [$previous30Days, $last30Days])
            ->count();
        $customersChange = $customersPrevious30 > 0
            ? round((($customersLast30 - $customersPrevious30) / $customersPrevious30) * 100, 1)
            : ($customersLast30 > 0 ? 100 : 0);

        // Recent orders
        $recentOrders = Order::with(['user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalRevenue',
            'totalCustomers',
            'totalProducts',
            'ordersChange',
            'revenueChange',
            'customersChange',
            'recentOrders'
        ));
    }
}

