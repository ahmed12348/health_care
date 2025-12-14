@extends('admin.layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Product Details</h1>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">
            ← Back to Products
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <h2 class="h4 mb-3">{{ $product->name }}</h2>
            
            @if($product->category)
                <p class="mb-2">
                    <strong>Category:</strong> 
                    <a href="{{ route('admin.categories.show', $product->category_id) }}" class="text-primary">
                        {{ $product->category->name }}
                    </a>
                </p>
            @endif

            <p class="h3 text-success mb-3">${{ number_format($product->price, 2) }}</p>
            
            <div class="mb-3">
                <strong>Description:</strong>
                <p class="mt-2">{{ $product->description }}</p>
            </div>

            <div class="mb-3">
                <p>
                    <strong>Stock Quantity:</strong> {{ $product->stock_quantity }}
                    @if($product->is_featured)
                        <span class="badge bg-warning ms-2">Featured</span>
                    @endif
                </p>
            </div>

            @if($product->variants->count() > 0)
                <div class="mb-3">
                    <h5 class="mb-3">Product Variants</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Value</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->variants as $variant)
                                    <tr>
                                        <td>{{ $variant->variant_type }}</td>
                                        <td>{{ $variant->variant_value }}</td>
                                        <td>${{ number_format($variant->variant_price ?? $product->price, 2) }}</td>
                                        <td>{{ $variant->variant_stock_quantity }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @auth
                @if(auth()->user()->role === 'admin')
                    <div class="mt-4">
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning">
                            Edit Product
                        </a>
                    </div>
                @endif
            @endauth
        </div>
    </div>

@endsection

