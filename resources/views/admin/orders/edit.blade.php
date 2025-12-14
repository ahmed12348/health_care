@extends('admin.layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Edit Order #{{ $order->id }}</h1>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">
            ← Back to Orders
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="user_id" class="form-label">Select Customer</label>
                        <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror">
                            <option value="">-- Select Customer (Optional) --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" 
                                    data-name="{{ $user->name }}"
                                    data-email="{{ $user->email }}"
                                    data-phone="{{ $user->phone_number ?? '' }}"
                                    data-address="{{ $user->address_line1 ?? '' }}"
                                    {{ old('user_id', $order->user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} - {{ $user->email }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Selecting a customer will auto-fill customer info below.</small>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="order_status" class="form-label">Order Status</label>
                        <select name="order_status" id="order_status" class="form-control @error('order_status') is-invalid @enderror" required>
                            <option value="pending" {{ old('order_status', $order->order_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ old('order_status', $order->order_status) == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="completed" {{ old('order_status', $order->order_status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('order_status', $order->order_status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('order_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Customer Information --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="customer_name" class="form-label">Customer Name</label>
                                <input type="text" name="customer_name" id="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name', $order->customer_name) }}" placeholder="Customer full name">
                                @error('customer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="customer_email" class="form-label">Customer Email</label>
                                <input type="email" name="customer_email" id="customer_email" class="form-control @error('customer_email') is-invalid @enderror" value="{{ old('customer_email', $order->customer_email) }}" placeholder="customer@example.com">
                                @error('customer_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="customer_phone" class="form-label">Customer Phone</label>
                                <input type="text" name="customer_phone" id="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror" value="{{ old('customer_phone', $order->customer_phone) }}" placeholder="Phone number">
                                @error('customer_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="customer_address" class="form-label">Customer Address</label>
                                <input type="text" name="customer_address" id="customer_address" class="form-control @error('customer_address') is-invalid @enderror" value="{{ old('customer_address', $order->customer_address) }}" placeholder="Delivery address">
                                @error('customer_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <small class="form-text text-muted">You can update customer information for this order. Selecting a customer will auto-fill from their profile.</small>
                    </div>
                </div>

                <div id="order-items-container">
                    @foreach($order->orderItems as $index => $item)
                        <div class="order-item border rounded p-3 mb-3">
                            {{-- Category --}}
                            <div class="mb-3">
                                <label class="form-label">Select Category</label>
                                <select class="form-control category-select">
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ $item->product->category_id == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Product --}}
                            <div class="mb-3">
                                <label class="form-label">Select Product</label>
                                <select name="products[{{ $index }}][product_id]" class="form-control product-select">
                                    <option value="">-- Select Product --</option>
                                    @if($item->product)
                                        <option value="{{ $item->product->id }}" selected>{{ $item->product->name }}</option>
                                    @endif
                                </select>
                            </div>

                            {{-- Quantity --}}
                            <div class="mb-3">
                                <label class="form-label">Quantity</label>
                                <input type="number" name="products[{{ $index }}][quantity]" class="form-control quantity" value="{{ old('products.'.$index.'.quantity', $item->quantity) }}" min="1">
                            </div>

                            {{-- Price --}}
                            <div class="mb-3">
                                <label class="form-label">Price</label>
                                <input type="number" step="0.01" name="products[{{ $index }}][price]" class="form-control price" value="{{ old('products.'.$index.'.price', $item->price) }}">
                                <small class="form-text text-muted">Price will be auto-filled when product is selected, but you can modify it if needed.</small>
                            </div>

                            <button type="button" class="btn btn-danger remove-item-btn">Remove</button>
                        </div>
                    @endforeach
                </div>

                <button type="button" class="btn btn-primary mb-3" id="add-product-btn">+ Add Product</button>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-success">Update Order</button>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Customer Selected -> Auto-fill Customer Info
    document.getElementById('user_id').addEventListener('change', function() {
        let selectedOption = this.options[this.selectedIndex];
        let customerNameInput = document.getElementById('customer_name');
        let customerEmailInput = document.getElementById('customer_email');
        let customerPhoneInput = document.getElementById('customer_phone');
        let customerAddressInput = document.getElementById('customer_address');

        if (selectedOption.value) {
            // Auto-fill from selected customer
            customerNameInput.value = selectedOption.dataset.name || '';
            customerEmailInput.value = selectedOption.dataset.email || '';
            customerPhoneInput.value = selectedOption.dataset.phone || '';
            customerAddressInput.value = selectedOption.dataset.address || '';
        }
    });

    // AUTO ADD ROW
    document.getElementById('add-product-btn').addEventListener('click', function() {
        let index = document.querySelectorAll('.order-item').length;

        let template = `
        <div class="order-item border rounded p-3 mb-3">
            <div class="mb-3">
                <label class="form-label">Select Category</label>
                <select class="form-control category-select">
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Select Product</label>
                <select name="products[${index}][product_id]" class="form-control product-select">
                    <option value="">-- Select Product --</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" name="products[${index}][quantity]" class="form-control quantity" value="1" min="1">
            </div>

            <div class="mb-3">
                <label class="form-label">Price</label>
                <input type="number" step="0.01" name="products[${index}][price]" class="form-control price" placeholder="Auto-filled from product">
            </div>

            <button type="button" class="btn btn-danger remove-item-btn">Remove</button>
        </div>`;

        document.getElementById('order-items-container').insertAdjacentHTML('beforeend', template);
    });

    // FETCH PRODUCTS & PRICE
    document.addEventListener('change', function (e) {
        // Category Selected -> Load Products
        if (e.target.classList.contains('category-select')) {
            let categoryId = e.target.value;
            let container = e.target.closest('.order-item');
            let productSelect = container.querySelector('.product-select');

            if (categoryId) {
                fetch(`/admin/get-products/${categoryId}`)
                    .then(res => res.json())
                    .then(data => {
                        productSelect.innerHTML = `<option value="">-- Select Product --</option>`;
                        data.forEach(p => {
                            productSelect.innerHTML += `<option value="${p.id}">${p.name}</option>`;
                        });
                    });
            }
        }

        // Product Selected -> Load Price
        if (e.target.classList.contains('product-select')) {
            let productId = e.target.value;
            let container = e.target.closest('.order-item');
            let priceInput = container.querySelector('.price');

            if (productId) {
                fetch(`/admin/get-product-price/${productId}`)
                    .then(res => res.json())
                    .then(data => {
                        priceInput.value = data.price;
                    });
            }
        }
    });

    // REMOVE ROW
    document.addEventListener('click', function(e){
        if(e.target.classList.contains('remove-item-btn')){
            e.target.closest('.order-item').remove();
        }
    });
</script>
@endpush
