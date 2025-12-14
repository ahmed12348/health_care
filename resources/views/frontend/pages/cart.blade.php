@extends('frontend.layouts.app')

@section('content')

<!-- Breadcrumb Section Begin -->
<section class="breadcrumb-section set-bg" data-setbg="{{ asset('front/assets/img/health1.png') }}">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="breadcrumb__text">
                    <h2>{{ __t('shopping_cart') }}</h2>
                    <div class="breadcrumb__option">
                        <a href="{{ route('frontend.home') }}">{{ __t('home') }}</a>
                        <span>{{ __t('shopping_cart') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->

<!-- Shoping Cart Section Begin -->
<section class="shoping-cart spad">
    <div class="container">
        @if(count($cartItems) > 0)
            <div class="row">
                <div class="col-lg-12">
                    <div class="shoping__cart__table">
                        <table>
                            <thead>
                                <tr>
                                    <th class="shoping__product">{{ __t('products') }}</th>
                                    <th>{{ __t('price') }}</th>
                                    <th>{{ __t('quantity') }}</th>
                                    <th>{{ __t('total') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cartItems as $item)
                                    <tr>
                                        <td class="shoping__cart__item">
                                            <img src="{{ $item['product']->image_url ?? asset('front/assets/img/product/product-1.jpg') }}" 
                                                 alt="{{ $item['product']->name }}" 
                                                 style="width: 100px; height: 100px; object-fit: cover;">
                                            <h5>
                                                <a href="{{ route('frontend.products.show', $item['product']->id) }}">
                                                    {{ $item['product']->name }}
                                                </a>
                                            </h5>
                                            @if($item['variant'])
                                                <small class="text-muted">
                                                    {{ $item['variant']->variant_type }}: {{ $item['variant']->variant_value }}
                                                </small>
                                            @elseif($item['product']->variants->count() > 0)
                                                {{-- Product has variants but none selected --}}
                                                <div style="margin-top: 10px;">
                                                    <label for="variant_select_{{ $item['key'] }}" style="font-size: 12px; color: #666; display: block; margin-bottom: 5px;">
                                                        {{ __t('select_variant') }} <span style="color: red;">*</span>
                                                    </label>
                                                    <form action="{{ route('frontend.cart.updateVariant', $item['key']) }}" method="POST" class="d-inline" id="variantForm_{{ $item['key'] }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <select name="variant_id" 
                                                                id="variant_select_{{ $item['key'] }}" 
                                                                class="form-control" 
                                                                style="width: 200px; padding: 5px; font-size: 12px;"
                                                                onchange="this.form.submit()"
                                                                required>
                                                            <option value="">{{ __t('select_variant') }}</option>
                                                            @foreach($item['product']->variants as $variant)
                                                                <option value="{{ $variant->id }}" 
                                                                        data-price="{{ $variant->variant_price ?? $item['product']->price }}"
                                                                        data-stock="{{ $variant->variant_stock_quantity ?? 0 }}"
                                                                        {{ ($variant->variant_stock_quantity ?? 0) <= 0 ? 'disabled' : '' }}>
                                                                    {{ $variant->variant_type }}: {{ $variant->variant_value }}
                                                                    - {{ number_format($variant->variant_price ?? $item['product']->price, 2) }}
                                                                    @if(($variant->variant_stock_quantity ?? 0) <= 0)
                                                                        ({{ __t('out_of_stock') }})
                                                                    @endif
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </form>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="shoping__cart__price">
                                            ${{ number_format($item['price'], 2) }}
                                        </td>
                                        <td class="shoping__cart__quantity">
                                            <div class="quantity">
                                                <form action="{{ route('frontend.cart.update', $item['key']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="number" 
                                                           name="quantity" 
                                                           value="{{ $item['quantity'] }}" 
                                                           min="1" 
                                                           max="{{ $item['variant'] ? ($item['variant']->variant_stock_quantity ?? 0) : ($item['product']->stock_quantity ?? 0) }}"
                                                           onchange="this.form.submit()"
                                                           style="width: 80px; padding: 8px; text-align: center; border: 1px solid #ddd; border-radius: 4px;">
                                                </form>
                                            </div>
                                        </td>
                                        <td class="shoping__cart__total">
                                            ${{ number_format($item['subtotal'], 2) }}
                                        </td>
                                        <td class="shoping__cart__item__close">
                                            <form action="{{ route('frontend.cart.remove', $item['key']) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <span onclick="this.parentElement.submit();" style="cursor: pointer;">
                                                    <i class="fa fa-close"></i>
                                                </span>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="shoping__cart__btns">
                        <a href="{{ route('frontend.products.index') }}" class="primary-btn">{{ __t('continue_shopping') }}</a>
                        <form action="{{ route('frontend.cart.clear') }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="site-btn" onclick="return confirm('{{ __t('are_you_sure_clear_cart') }}')">
                                {{ __t('clear_cart') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    {{-- Coupon code section can be added here if needed --}}
                </div>
                <div class="col-lg-6">
                    <div class="shoping__checkout">
                        <h5>{{ __t('cart_total') }}</h5>
                        <ul>
                            <li>{{ __t('subtotal') }} <span style="float: right;">${{ number_format($total, 2) }}</span></li>
                            
                            @auth
                                @php
                                    $useLoyaltyPoints = old('use_loyalty_points', false);
                                    $loyaltyPointsToUse = old('loyalty_points_to_use', 0);
                                    $maxPointsToUse = min($userLoyaltyPoints ?? 0, floor($total * 10)); // 10 points = $1
                                    $loyaltyDiscount = $useLoyaltyPoints && $loyaltyPointsToUse > 0 ? ($loyaltyPointsToUse / 10) : 0;
                                    $finalTotal = max(0, $total - $loyaltyDiscount);
                                @endphp
                                
                                @if(($userLoyaltyPoints ?? 0) > 0)
                                    <li>
                                        <div class="checkout__input__checkbox" style="margin: 10px 0;">
                                            <label for="use_loyalty_points">
                                                {{ __t('use_loyalty_points') }} ({{ number_format($userLoyaltyPoints ?? 0) }} {{ __t('points_available') }})
                                                <input type="checkbox" id="use_loyalty_points" name="use_loyalty_points" value="1" {{ $useLoyaltyPoints ? 'checked' : '' }} onchange="toggleLoyaltyPoints()">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                        <div id="loyalty_points_input" style="display: {{ $useLoyaltyPoints ? 'block' : 'none' }}; margin: 10px 0;">
                                            <label for="loyalty_points_to_use">{{ __t('points_to_use') }} ({{ __t('max') }}: {{ number_format($maxPointsToUse) }})</label>
                                            <input type="number" 
                                                   id="loyalty_points_to_use" 
                                                   name="loyalty_points_to_use" 
                                                   value="{{ $loyaltyPointsToUse }}" 
                                                   min="0" 
                                                   max="{{ $maxPointsToUse }}"
                                                   onchange="calculateTotal()"
                                                   style="width: 100%; padding: 8px; margin-top: 5px;">
                                            <small>{{ __t('points_discount_info') }}: 10 {{ __t('points') }} = $1</small>
                                        </div>
                                    </li>
                                    @if($loyaltyDiscount > 0)
                                        <li>{{ __t('loyalty_discount') }} <span style="float: right;">-${{ number_format($loyaltyDiscount, 2) }}</span></li>
                                    @endif
                                @endif
                            @endauth
                            
                            <li style="font-weight: bold; font-size: 18px; border-top: 2px solid #ddd; padding-top: 10px; margin-top: 10px;">
                                {{ __t('total') }} 
                                <span id="final_total" style="float: right;">${{ number_format(auth()->check() && isset($finalTotal) ? $finalTotal : $total, 2) }}</span>
                            </li>
                        </ul>
                        <form action="{{ route('frontend.checkout.index') }}" method="GET" id="checkoutForm" onsubmit="return validateCartBeforeCheckout(event)">
                            @auth
                                <input type="hidden" name="use_loyalty_points" id="use_loyalty_points_hidden" value="{{ $useLoyaltyPoints ? '1' : '0' }}">
                                <input type="hidden" name="loyalty_points_to_use" id="loyalty_points_to_use_hidden" value="{{ $loyaltyPointsToUse }}">
                            @endauth
                            <button type="submit" class="primary-btn" style="width: 100%; margin-top: 20px;">{{ __t('proceed_to_checkout') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="alert alert-info">
                        <h4>{{ __t('your_cart_is_empty') }}</h4>
                        <p>{{ __t('add_items_to_cart') }}</p>
                        <a href="{{ route('frontend.products.index') }}" class="primary-btn">{{ __t('continue_shopping') }}</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
<!-- Shoping Cart Section End -->

@push('scripts')
<script>
    function toggleLoyaltyPoints() {
        const checkbox = document.getElementById('use_loyalty_points');
        const inputDiv = document.getElementById('loyalty_points_input');
        const hiddenCheckbox = document.getElementById('use_loyalty_points_hidden');
        
        if (checkbox && checkbox.checked) {
            if (inputDiv) inputDiv.style.display = 'block';
            if (hiddenCheckbox) hiddenCheckbox.value = '1';
        } else {
            if (inputDiv) inputDiv.style.display = 'none';
            const pointsInput = document.getElementById('loyalty_points_to_use');
            const pointsHidden = document.getElementById('loyalty_points_to_use_hidden');
            if (pointsInput) pointsInput.value = 0;
            if (pointsHidden) pointsHidden.value = 0;
            if (hiddenCheckbox) hiddenCheckbox.value = '0';
            calculateTotal();
        }
    }
    
    function calculateTotal() {
        const usePointsCheckbox = document.getElementById('use_loyalty_points');
        const pointsInput = document.getElementById('loyalty_points_to_use');
        const finalTotalSpan = document.getElementById('final_total');
        const pointsHidden = document.getElementById('loyalty_points_to_use_hidden');
        
        if (!usePointsCheckbox || !finalTotalSpan) return;
        
        const usePoints = usePointsCheckbox.checked;
        const pointsToUse = parseFloat(pointsInput ? pointsInput.value : 0) || 0;
        const subtotal = {{ $total }};
        const discount = usePoints ? (pointsToUse / 10) : 0;
        const finalTotal = Math.max(0, subtotal - discount);
        
        finalTotalSpan.textContent = '$' + finalTotal.toFixed(2);
        if (pointsHidden) pointsHidden.value = usePoints ? pointsToUse : 0;
        
        // Update discount display
        let discountLi = document.querySelector('.loyalty-discount-li');
        if (discount > 0) {
            if (!discountLi) {
                const subtotalLi = document.querySelector('li:has(span)');
                if (subtotalLi) {
                    discountLi = document.createElement('li');
                    discountLi.className = 'loyalty-discount-li';
                    discountLi.innerHTML = '{{ __t("loyalty_discount") }} <span style="float: right;">-$' + discount.toFixed(2) + '</span>';
                    subtotalLi.parentNode.insertBefore(discountLi, subtotalLi.nextSibling);
                }
            } else {
                discountLi.innerHTML = '{{ __t("loyalty_discount") }} <span style="float: right;">-$' + discount.toFixed(2) + '</span>';
            }
        } else if (discountLi) {
            discountLi.remove();
        }
    }
    
    // Validate cart before checkout
    function validateCartBeforeCheckout(event) {
        // Check if any product has variants but no variant selected
        const variantSelects = document.querySelectorAll('select[name="variant_id"]');
        let hasUnselectedVariant = false;
        
        variantSelects.forEach(function(select) {
            if (!select.value || select.value === '') {
                hasUnselectedVariant = true;
                select.style.border = '2px solid red';
                select.focus();
            } else {
                select.style.border = '';
            }
        });
        
        if (hasUnselectedVariant) {
            event.preventDefault();
            alert('{{ __t("please_select_variant") }}');
            return false;
        }
        
        return true;
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const pointsInput = document.getElementById('loyalty_points_to_use');
        if (pointsInput) {
            pointsInput.addEventListener('input', calculateTotal);
        }
        calculateTotal();
    });
</script>
@endpush

@endsection

