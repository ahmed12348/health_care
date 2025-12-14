@extends('admin.layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Create Order</h1>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">
            ← Back to Orders
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.orders.store') }}" method="POST">
                @csrf

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
                                    {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} - {{ $user->email }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Leave empty for guest order. Customer orders earn loyalty points.</small>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="order_status" class="form-label">Order Status</label>
                        <select name="order_status" id="order_status" class="form-control @error('order_status') is-invalid @enderror" required>
                            <option value="pending" {{ old('order_status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ old('order_status') == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="completed" {{ old('order_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('order_status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
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
                                <input type="text" name="customer_name" id="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name') }}" placeholder="Customer full name">
                                @error('customer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="customer_email" class="form-label">Customer Email</label>
                                <input type="email" name="customer_email" id="customer_email" class="form-control @error('customer_email') is-invalid @enderror" value="{{ old('customer_email') }}" placeholder="customer@example.com">
                                @error('customer_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="customer_phone" class="form-label">Customer Phone</label>
                                <input type="text" name="customer_phone" id="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror" value="{{ old('customer_phone') }}" placeholder="Phone number">
                                @error('customer_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="customer_address" class="form-label">Customer Address</label>
                                <input type="text" name="customer_address" id="customer_address" class="form-control @error('customer_address') is-invalid @enderror" value="{{ old('customer_address') }}" placeholder="Delivery address">
                                @error('customer_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <small class="form-text text-muted">Customer information will be auto-filled when you select a customer, but you can edit it for this order.</small>
                    </div>
                </div>
        <div id="order-items-container">
            <div class="order-item border rounded p-3 mb-3">

                {{-- Category --}}
                <div class="mb-3">
                    <label class="form-label">Select Category</label>
                    <select class="form-control category-select">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Product --}}
                <div class="mb-3">
                    <label class="form-label">Select Product</label>
                    <select name="products[0][product_id]" class="form-control product-select">
                        <option value="">-- Select Product --</option>
                    </select>
                </div>

                {{-- Variant --}}
                <div class="mb-3">
                    <label class="form-label">Select Variant (Optional)</label>
                    <select name="products[0][variant_id]" class="form-control variant-select">
                        <option value="">-- No Variant --</option>
                    </select>
                    <small class="form-text text-muted">Select a variant if product has variants (Size, Color, etc.)</small>
                </div>

                {{-- Quantity --}}
                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="products[0][quantity]" class="form-control quantity" value="1">
                </div>

                {{-- Price --}}
                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" step="0.01" name="products[0][price]" class="form-control price" placeholder="Auto-filled from product or variant">
                    <small class="form-text text-muted">Price will be auto-filled when product/variant is selected, but you can modify it if needed.</small>
                </div>

                <button type="button" class="btn btn-danger remove-item-btn">Remove</button>

            </div>
        </div>

                <button type="button" class="btn btn-primary mb-3" id="add-product-btn">+ Add Product</button>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-success">Create Order</button>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>

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
                <label class="form-label">Select Variant (Optional)</label>
                <select name="products[${index}][variant_id]" class="form-control variant-select">
                    <option value="">-- No Variant --</option>
                </select>
                <small class="form-text text-muted">Select a variant if product has variants</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" name="products[${index}][quantity]" class="form-control quantity" value="1" min="1">
            </div>

            <div class="mb-3">
                <label class="form-label">Price</label>
                <input type="number" step="0.01" name="products[${index}][price]" class="form-control price" placeholder="Auto-filled from product or variant">
            </div>

            <button type="button" class="btn btn-danger remove-item-btn">Remove</button>
        </div>`;

        document.getElementById('order-items-container').insertAdjacentHTML('beforeend', template);
    });

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
        } else {
            // Clear fields if no customer selected
            customerNameInput.value = '';
            customerEmailInput.value = '';
            customerPhoneInput.value = '';
            customerAddressInput.value = '';
        }
    });

    // FETCH PRODUCTS, VARIANTS & PRICE
    document.addEventListener('change', function (e) {

        // Category Selected -> Load Products
        if (e.target.classList.contains('category-select')) {

            let categoryId = e.target.value;
            let container = e.target.closest('.order-item');
            let productSelect = container.querySelector('.product-select');
            let variantSelect = container.querySelector('.variant-select');
            let priceInput = container.querySelector('.price');

            // Reset product, variant and price
            productSelect.innerHTML = `<option value="">-- Select Product --</option>`;
            variantSelect.innerHTML = `<option value="">-- No Variant --</option>`;
            priceInput.value = '';

            if (categoryId) {
                fetch(`/admin/get-products/${categoryId}`)
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(p => {
                            productSelect.innerHTML += `<option value="${p.id}">${p.name}</option>`;
                        });
                    })
                    .catch(err => console.error('Error loading products:', err));
            }
        }

        // Product Selected -> Load Variants & Price
        if (e.target.classList.contains('product-select')) {

            let productId = e.target.value;
            let container = e.target.closest('.order-item');
            let variantSelect = container.querySelector('.variant-select');
            let priceInput = container.querySelector('.price');

            // Reset variant and price
            variantSelect.innerHTML = `<option value="">-- No Variant --</option>`;
            priceInput.value = '';

            if (productId) {
                // Load product price
                fetch(`/admin/get-product-price/${productId}`)
                    .then(res => res.json())
                    .then(data => {
                        let unitPrice = parseFloat(data.price) || 0;
                        let quantity = parseFloat(container.querySelector('.quantity').value) || 1;
                        
                        // Store unit price in data attribute
                        priceInput.dataset.unitPrice = unitPrice;
                        
                        // Set price = unit price * quantity
                        priceInput.value = (unitPrice * quantity).toFixed(2);
                    })
                    .catch(err => console.error('Error loading price:', err));

                // Load variants
                fetch(`/admin/products/${productId}/variants`)
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            data.forEach(variant => {
                                let displayText = `${variant.variant_type}: ${variant.variant_value}`;
                                if (variant.variant_price) {
                                    displayText += ` ($${parseFloat(variant.variant_price).toFixed(2)})`;
                                }
                                variantSelect.innerHTML += `<option value="${variant.id}" data-price="${variant.variant_price || ''}">${displayText}</option>`;
                            });
                        }
                    })
                    .catch(err => console.error('Error loading variants:', err));
            }
        }

        // Variant Selected -> Update Price
        if (e.target.classList.contains('variant-select')) {
            let container = e.target.closest('.order-item');
            let priceInput = container.querySelector('.price');
            let selectedOption = e.target.options[e.target.selectedIndex];

            if (selectedOption.value && selectedOption.dataset.price) {
                // Use variant price if available
                let unitPrice = parseFloat(selectedOption.dataset.price) || 0;
                let quantity = parseFloat(container.querySelector('.quantity').value) || 1;
                
                // Store unit price in data attribute
                priceInput.dataset.unitPrice = unitPrice;
                
                // Set price = unit price * quantity
                priceInput.value = (unitPrice * quantity).toFixed(2);
            } else if (!selectedOption.value) {
                // No variant selected, use product price
                let productSelect = container.querySelector('.product-select');
                if (productSelect.value) {
                    fetch(`/admin/get-product-price/${productSelect.value}`)
                        .then(res => res.json())
                        .then(data => {
                            let unitPrice = parseFloat(data.price) || 0;
                            let quantity = parseFloat(container.querySelector('.quantity').value) || 1;
                            
                            // Store unit price in data attribute
                            priceInput.dataset.unitPrice = unitPrice;
                            
                            // Set price = unit price * quantity
                            priceInput.value = (unitPrice * quantity).toFixed(2);
                        })
                        .catch(err => console.error('Error loading price:', err));
                }
            }
        }

        // Quantity Change -> Auto-update price (quantity * unit price)
        if (e.target.classList.contains('quantity')) {
            let container = e.target.closest('.order-item');
            let quantityInput = e.target;
            let priceInput = container.querySelector('.price');
            let unitPrice = parseFloat(priceInput.dataset.unitPrice || priceInput.value) || 0;
            let quantity = parseFloat(quantityInput.value) || 1;
            
            // Update price = unit price * quantity
            if (unitPrice > 0) {
                priceInput.value = (unitPrice * quantity).toFixed(2);
            }
            
            calculateTotals();
        }

    });

    // REMOVE ROW
    document.addEventListener('click', function(e){
        if(e.target.classList.contains('remove-item-btn')){
            e.target.closest('.order-item').remove();
            calculateTotals();
        }
    });

    // Calculate Total (for display purposes only)
    function calculateTotals(){
        // This is just for UI feedback, actual calculation is done server-side
    }

</script>
@endpush
