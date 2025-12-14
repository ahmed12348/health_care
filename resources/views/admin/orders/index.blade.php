@extends('admin.layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Orders</h1>
        @auth
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.orders.create') }}" class="btn btn-success btn-sm">
                    + Create Order
                </a>
            @endif
        @endauth
    </div>

    @if($orders->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>User</th>
                                <th>Total</th>
                                <th>Points Earned</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->user->name ?? 'Guest' }}</td>
                                    <td>${{ number_format($order->total_price, 2) }}</td>
                                    <td>{{ $order->total_points_earned }} pts</td>
                                    <td>
                                        @if($order->order_status === 'completed')
                                            <span class="badge bg-success">{{ ucfirst($order->order_status) }}</span>
                                        @elseif($order->order_status === 'cancelled')
                                            <span class="badge bg-danger">{{ ucfirst($order->order_status) }}</span>
                                        @else
                                            <span class="badge bg-warning">{{ ucfirst($order->order_status) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info">
                                            View
                                        </a>
                                        @auth
                                            @if(auth()->user()->role === 'admin')
                                                <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-sm btn-warning">
                                                    Edit
                                                </a>
                                            @endif
                                        @endauth
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <p class="mb-0">No orders found.</p>
        </div>
    @endif

@endsection
