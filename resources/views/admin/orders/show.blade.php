@extends('admin.layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Order Details</h1>
        <div class="d-flex gap-2">
            @auth
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-warning btn-sm">
                        Edit Order
                    </a>
                @endif
            @endauth
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">
                ← Back to Orders
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h4 mb-3">Order #{{ $order->id }}</h2>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <p class="mb-2">
                        <strong>User Account:</strong> {{ $order->user->name ?? 'Guest Order' }} 
                        @if($order->user)
                            <span class="text-muted">({{ $order->user->email }})</span>
                        @endif
                    </p>
                    <p class="mb-2">
                        <strong>Status:</strong> 
                        @if($order->order_status === 'completed')
                            <span class="badge bg-success">{{ ucfirst($order->order_status) }}</span>
                        @elseif($order->order_status === 'cancelled')
                            <span class="badge bg-danger">{{ ucfirst($order->order_status) }}</span>
                        @else
                            <span class="badge bg-warning">{{ ucfirst($order->order_status) }}</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="mb-2">
                        <strong>Date:</strong> {{ $order->created_at->format('F d, Y h:i A') }}
                    </p>
                    @if(auth()->user()->role === 'admin')
                        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <div class="input-group">
                                <select name="status" class="form-select form-select-sm">
                                    <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Customer Information --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Customer Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <strong>Name:</strong> {{ $order->customer_name ?? ($order->user->name ?? 'N/A') }}
                </div>
                <div class="col-md-6 mb-2">
                    <strong>Email:</strong> {{ $order->customer_email ?? ($order->user->email ?? 'N/A') }}
                </div>
                <div class="col-md-6 mb-2">
                    <strong>Phone:</strong> {{ $order->customer_phone ?? ($order->user->phone_number ?? 'N/A') }}
                </div>
                <div class="col-md-6 mb-2">
                    <strong>Address:</strong> {{ $order->customer_address ?? ($order->user->address_line1 ?? 'N/A') }}
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h3 class="h5 mb-3">Order Items</h3>
            
            @if($order->orderItems->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Variant</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderItems as $item)
                                <tr>
                                    <td>{{ $item->product->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($item->variant)
                                            <span class="badge bg-info">{{ $item->variant->variant_type }}: {{ $item->variant->variant_value }}</span>
                                        @else
                                            <span class="text-muted">No variant</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->price, 2) }}</td>
                                    <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted mb-0">No items in this order.</p>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-2">
                        <strong>Total Price:</strong> 
                        <span class="h4 text-success">${{ number_format($order->total_price, 2) }}</span>
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="mb-2">
                        <strong>Points Earned:</strong> 
                        <span class="h5">{{ $order->total_points_earned }} pts</span>
                    </p>
                    @if($order->total_points_spent > 0)
                        <p class="mb-0">
                            <strong>Points Spent:</strong> 
                            <span class="h5">{{ $order->total_points_spent }} pts</span>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

