<!-- resources/views/admin/dashboard.blade.php -->
@extends('admin.layouts.app')

@section('content')
    <div class="container">
        <h1>Welcome to the Admin Dashboard</h1>

        <div class="row">
            <!-- Total Orders -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5>Total Orders</h5>
                        <h4>{{ number_format($totalOrders) }}</h4>
                        <p class="{{ $ordersChange >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $ordersChange >= 0 ? '+' : '' }}{{ $ordersChange }}% 
                            <i class="bi bi-arrow-{{ $ordersChange >= 0 ? 'up' : 'down' }}"></i>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Revenue -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5>Revenue</h5>
                        <h4>${{ number_format($totalRevenue, 2) }}</h4>
                        <p class="{{ $revenueChange >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $revenueChange >= 0 ? '+' : '' }}{{ $revenueChange }}% 
                            <i class="bi bi-arrow-{{ $revenueChange >= 0 ? 'up' : 'down' }}"></i>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Customers -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5>Customers</h5>
                        <h4>{{ number_format($totalCustomers) }}</h4>
                        <p class="{{ $customersChange >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $customersChange >= 0 ? '+' : '' }}{{ $customersChange }}% 
                            <i class="bi bi-arrow-{{ $customersChange >= 0 ? 'up' : 'down' }}"></i>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Total Products -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5>Total Products</h5>
                        <h4>{{ number_format($totalProducts) }}</h4>
                        <p class="text-muted">Active Products</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders Table -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5>Recent Orders</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Total Price</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($recentOrders->count() > 0)
                                        @foreach($recentOrders as $order)
                                            <tr>
                                                <td>#{{ $order->id }}</td>
                                                <td>{{ $order->user->name ?? 'Guest' }}</td>
                                                <td>${{ number_format($order->total_price, 2) }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $order->order_status === 'completed' ? 'success' : ($order->order_status === 'pending' ? 'warning' : 'secondary') }}">
                                                        {{ ucfirst($order->order_status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">No orders found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
