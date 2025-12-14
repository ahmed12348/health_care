@extends('admin.layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Category Details</h1>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">
            ← Back to Categories
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h4 mb-3">{{ $category->name }}</h2>
            
            @if($category->description)
                <p class="mb-0">{{ $category->description }}</p>
            @endif

            @auth
                @if(auth()->user()->role === 'admin')
                    <div class="mt-3">
                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-warning btn-sm">
                            Edit Category
                        </a>
                    </div>
                @endif
            @endauth
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h3 class="h5 mb-3">Products in this category</h3>
            
            @if($category->products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category->products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>${{ number_format($product->price, 2) }}</td>
                                    <td>{{ $product->stock_quantity }}</td>
                                    <td>
                                        <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted mb-0">No products in this category.</p>
            @endif
        </div>
    </div>

@endsection

