@extends('frontend.layouts.app')

@section('content')

<!-- Breadcrumb Section Begin -->
<section class="breadcrumb-section set-bg" data-setbg="{{ asset('front/assets/img/health1.png') }}">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="breadcrumb__text">
                    <h2>{{ $product->name }}</h2>
                    <div class="breadcrumb__option">
                        <a href="{{ route('frontend.home') }}">{{ __t('home') }}</a>
                        @if($product->category)
                            <a href="{{ route('frontend.categories.show', $product->category->id) }}">{{ $product->category->name }}</a>
                        @endif
                        <span>{{ $product->name }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->

<!-- Product Details Section Begin -->
<section class="product-details spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6">
                <div class="product__details__pic">
                    <div class="product__details__pic__item">
                        <img class="product__details__pic__item--large"
                            src="{{ $product->image_url ?? asset('front/assets/img/product/product-1.jpg') }}"
                            alt="{{ $product->name }}">
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="product__details__text">
                    <h3>{{ $product->name }}</h3>
                    @if($product->category)
                        <div class="product__details__rating">
                            <span class="badge bg-primary">{{ $product->category->name }}</span>
                        </div>
                    @endif
                    <div class="product__details__price">
                        @if($product->variants->count() > 0)
                            {{ number_format($product->variants->min('variant_price') ?? $product->price, 2) }}
                            @if($product->variants->min('variant_price') != $product->variants->max('variant_price'))
                                - {{ number_format($product->variants->max('variant_price'), 2) }}
                            @endif
                        @else
                            {{ number_format($product->price, 2) }}
                        @endif
                    </div>
                    <p>{{ $product->description ?? __t('no_description') }}</p>
                    
                    {{-- @if($product->variants->count() > 0)
                        <div class="product__details__quantity mb-3">
                            <label>{{ __t('select_variant') }}:</label>
                            <select class="form-control variant-select" id="variantSelect" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="">{{ __t('choose_variant') }}</option>
                                @foreach($product->variants as $variant)
                                    <option value="{{ $variant->id }}" 
                                        data-price="{{ $variant->variant_price ?? $product->price }}"
                                        data-stock="{{ $variant->variant_stock_quantity ?? 0 }}">
                                        {{ $variant->variant_type }}: {{ $variant->variant_value }}
                                        - {{ number_format($variant->variant_price ?? $product->price, 2) }}
                                        @if(($variant->variant_stock_quantity ?? 0) <= 0)
                                            ({{ __t('out_of_stock') }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif --}}

                    <div class="product__details__quantity mb-3" style="display: block; margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 10px; font-weight: 600;">{{ __t('quantity') }}:</label>
                        <div class="quantity">
                            <div class="pro-qty" style="display: inline-flex; align-items: center; border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">
                                <span class="dec qtybtn" style="padding: 10px 15px; cursor: pointer; background: #f5f5f5; user-select: none; border-right: 1px solid #ddd;">-</span>
                                <input type="number" value="1" id="quantityInput" min="1" max="{{ $product->stock_quantity ?? 0 }}" style="width: 60px; text-align: center; border: none; padding: 10px; outline: none;">
                                <span class="inc qtybtn" style="padding: 10px 15px; cursor: pointer; background: #f5f5f5; user-select: none; border-left: 1px solid #ddd;">+</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __t('stock_available') }}:</strong> 
                        <span id="stockDisplay">{{ $product->stock_quantity ?? 0 }}</span>
                    </div>

                    <div class="product__details__button">
                        @if(($product->stock_quantity ?? 0) > 0)
                            <form action="{{ route('frontend.cart.add') }}" method="POST" class="d-inline" id="addToCartForm" onsubmit="return handleAddToCart(event)">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" id="cartQuantity" value="1">
                                <input type="hidden" name="variant_id" id="cartVariantId" value="">
                                <button type="submit" class="primary-btn" id="addToCartBtn">
                                    {{ __t('add_to_cart') }}
                                </button>
                            </form>
                        @else
                            <button class="primary-btn" disabled>{{ __t('out_of_stock') }}</button>
                        @endif
                        <a href="#" class="wishlist-toggle-btn heart-icon" data-product-id="{{ $product->id }}">
                            <span class="icon_heart_alt"></span>
                        </a>
                    </div>
                    <ul style="list-style: none; padding: 0; margin-top: 30px;">
                        <li style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #ebebeb;">
                            <b style="display: inline-block; width: 150px; font-weight: 700;">{{ __t('availability') }}</b> 
                            <span id="availabilityDisplay">
                                @if(($product->stock_quantity ?? 0) > 0)
                                    {{ __t('in_stock') }}
                                @else
                                    {{ __t('out_of_stock_label') }}
                                @endif
                            </span>
                        </li>
                        <li style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #ebebeb;">
                            <b style="display: inline-block; width: 150px; font-weight: 700;">{{ __t('shipping') }}</b> 
                            <span>{{ __t('shipping_info') }}</span>
                        </li>
                        @if($product->weight)
                            <li style="margin-bottom: 15px;">
                                <b style="display: inline-block; width: 150px; font-weight: 700;">{{ __t('weight') }}</b> 
                                <span>{{ $product->weight }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="col-lg-12" style="margin-top: 50px;">
                <div class="product__details__tab">
                    <ul class="nav nav-tabs" role="tablist" style="border-bottom: 2px solid #ebebeb; margin-bottom: 30px;">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab"
                                aria-selected="true" style="padding: 15px 20px; font-weight: 600; color: #252525; border-bottom: 2px solid #dd2222; margin-bottom: -2px;">{{ __t('description') }}</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tabs-1" role="tabpanel">
                            <div class="product__details__tab__desc" style="padding: 20px 0;">
                                <h6 style="font-weight: 700; margin-bottom: 20px; color: #252525;">{{ __t('products_information') }}</h6>
                                <p style="line-height: 1.8; color: #6f6f6f;">{{ $product->description ?? __t('no_description') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Product Details Section End -->

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const variantSelect = document.getElementById('variantSelect');
        const stockDisplay = document.getElementById('stockDisplay');
        const availabilityDisplay = document.getElementById('availabilityDisplay');
        const quantityInput = document.getElementById('quantityInput');
        const cartQuantity = document.getElementById('cartQuantity');
        const cartVariantId = document.getElementById('cartVariantId');
        
        // Quantity buttons functionality
        const decBtn = document.querySelector('.dec.qtybtn');
        const incBtn = document.querySelector('.inc.qtybtn');
        
        if (decBtn && quantityInput) {
            decBtn.addEventListener('click', function() {
                let currentValue = parseInt(quantityInput.value) || 1;
                const minValue = parseInt(quantityInput.getAttribute('min')) || 1;
                if (currentValue > minValue) {
                    currentValue--;
                    quantityInput.value = currentValue;
                    if (cartQuantity) {
                        cartQuantity.value = currentValue;
                    }
                }
            });
        }
        
        if (incBtn && quantityInput) {
            incBtn.addEventListener('click', function() {
                let currentValue = parseInt(quantityInput.value) || 1;
                const maxValue = parseInt(quantityInput.getAttribute('max')) || 999;
                if (currentValue < maxValue) {
                    currentValue++;
                    quantityInput.value = currentValue;
                    if (cartQuantity) {
                        cartQuantity.value = currentValue;
                    }
                }
            });
        }
        
        // Sync quantity input with cart form
        if (quantityInput && cartQuantity) {
            quantityInput.addEventListener('change', function() {
                let value = parseInt(this.value) || 1;
                const minValue = parseInt(this.getAttribute('min')) || 1;
                const maxValue = parseInt(this.getAttribute('max')) || 999;
                
                if (value < minValue) value = minValue;
                if (value > maxValue) value = maxValue;
                
                this.value = value;
                cartQuantity.value = value;
            });
        }
        
        if (variantSelect) {
            variantSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const variantId = this.value;
                const stock = selectedOption.getAttribute('data-stock') || 0;
                const price = selectedOption.getAttribute('data-price') || {{ $product->price }};
                
                stockDisplay.textContent = stock;
                if (quantityInput) {
                    quantityInput.setAttribute('max', stock);
                }
                
                if (cartVariantId) {
                    cartVariantId.value = variantId || '';
                }
                
                if (stock > 0) {
                    availabilityDisplay.textContent = '{{ __t('in_stock') }}';
                } else {
                    availabilityDisplay.textContent = '{{ __t('out_of_stock_label') }}';
                }
            });
        }
    });
    
    // Prevent double form submission
    function handleAddToCart(event) {
        const form = event.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        
        if (submitBtn.disabled) {
            event.preventDefault();
            return false;
        }
        
        submitBtn.disabled = true;
        submitBtn.textContent = '{{ __t("adding") }}...';
        
        return true;
    }
</script>
@endpush

@endsection
