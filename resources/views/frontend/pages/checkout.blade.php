@extends('frontend.layouts.app')

@section('content')

<!-- Breadcrumb Section Begin -->
<section class="breadcrumb-section set-bg" data-setbg="{{ asset('front/assets/img/health1.png') }}">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="breadcrumb__text">
                    <h2>{{ __t('checkout') }}</h2>
                    <div class="breadcrumb__option">
                        <a href="{{ route('frontend.home') }}">{{ __t('home') }}</a>
                        <span>{{ __t('checkout') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->

<!-- Checkout Section Begin -->
<section class="checkout spad">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-12">
                {{-- <h6>
                    <span class="icon_tag_alt"></span> 
                    {{ __t('have_a_coupon') }}? 
                    <a href="#">{{ __t('click_here') }}</a> 
                    {{ __t('to_enter_your_code') }}
                </h6> --}}
            </div>
        </div>
        <div class="checkout__form">
            <h4>{{ __t('billing_details') }}</h4>
            <form action="{{ route('frontend.checkout.store') }}" method="POST">
                @csrf
                <input type="hidden" name="order_status" value="pending">
                <div class="row">
                    <div class="col-lg-8 col-md-6">
                        @auth
                            {{-- User Info (Read-only) --}}
                            <div class="checkout__input">
                                <p>{{ __t('name') }}</p>
                                <input type="text" value="{{ auth()->user()->name }}" disabled style="background-color: #f5f5f5; cursor: not-allowed;">
                                <input type="hidden" name="customer_name" value="{{ auth()->user()->name }}">
                            </div>
                            <div class="checkout__input">
                                <p>{{ __t('email') }}</p>
                                <input type="email" value="{{ auth()->user()->email }}" disabled style="background-color: #f5f5f5; cursor: not-allowed;">
                                <input type="hidden" name="customer_email" value="{{ auth()->user()->email }}">
                            </div>
                            <div class="checkout__input">
                                <p>{{ __t('phone') }}</p>
                                <input type="text" value="{{ auth()->user()->phone_number ?? '-' }}" disabled style="background-color: #f5f5f5; cursor: not-allowed;">
                                <input type="hidden" name="customer_phone" value="{{ auth()->user()->phone_number ?? '' }}">
                            </div>
                            
                            {{-- Address (Editable) --}}
                            <div class="checkout__input">
                                <p>{{ __t('address') }}<span>*</span></p>
                                <input type="text" name="customer_address" placeholder="{{ __t('street_address') }}" value="{{ old('customer_address', auth()->user()->address_line1 ?? '') }}" required>
                            </div>
                            
                            {{-- Order Notes (Editable) --}}
                            <div class="checkout__input">
                                <p>{{ __t('order_notes') }}</p>
                                <textarea name="order_notes" placeholder="{{ __t('order_notes_placeholder') }}" rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">{{ old('order_notes') }}</textarea>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                {{ __t('please_login_to_checkout') }}
                            </div>
                        @endauth
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="checkout__order">
                            <h4>{{ __t('your_order') }}</h4>
                            <div class="checkout__order__products">{{ __t('products') }} <span>{{ __t('total') }}</span></div>
                            <ul>
                                @foreach($products as $index => $item)
                                    @php
                                        $itemTotal = $item['price'] * $item['quantity'];
                                    @endphp
                                    <li>
                                        {{ $item['product']->name }}
                                        @if($item['variant'])
                                            ({{ $item['variant']->variant_type }}: {{ $item['variant']->variant_value }})
                                        @endif
                                        x{{ $item['quantity'] }}
                                        <span style="float: right;">${{ number_format($itemTotal, 2) }}</span>
                                    </li>
                                    {{-- Hidden inputs for form submission --}}
                                    <input type="hidden" name="products[{{ $index }}][product_id]" value="{{ $item['product']->id }}">
                                    <input type="hidden" name="products[{{ $index }}][variant_id]" value="{{ $item['variant_id'] ?? '' }}">
                                    <input type="hidden" name="products[{{ $index }}][quantity]" value="{{ $item['quantity'] }}">
                                    <input type="hidden" name="products[{{ $index }}][price]" value="{{ $item['price'] }}">
                                @endforeach
                            </ul>
                            @php
                                $calculatedSubtotal = $subtotal ?? 0;
                                $calculatedLoyaltyDiscount = $loyaltyDiscount ?? 0;
                                $calculatedTotal = $total ?? $calculatedSubtotal;
                                $calculatedUseLoyaltyPoints = $useLoyaltyPoints ?? false;
                                $calculatedLoyaltyPointsToUse = $loyaltyPointsToUse ?? 0;
                            @endphp
                            
                            <div class="checkout__order__subtotal">{{ __t('subtotal') }} <span style="float: right;">${{ number_format($calculatedSubtotal, 2) }}</span></div>
                            
                            @auth
                                @php
                                    $maxPointsToUse = min($userLoyaltyPoints ?? 0, floor($calculatedSubtotal * 10)); // 10 points = $1
                                @endphp
                                
                                @if(($userLoyaltyPoints ?? 0) > 0)
                                    <li style="list-style: none; padding: 10px 0; border-top: 1px solid #eee;">
                                        <div class="checkout__input__checkbox" style="margin: 10px 0;">
                                            <label for="use_loyalty_points_checkout">
                                                {{ __t('use_loyalty_points') }} ({{ number_format($userLoyaltyPoints ?? 0) }} {{ __t('points_available') }})
                                                <input type="checkbox" id="use_loyalty_points_checkout" name="use_loyalty_points" value="1" {{ $calculatedUseLoyaltyPoints ? 'checked' : '' }} onchange="toggleLoyaltyPointsCheckout()">
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>
                                        <div id="loyalty_points_input_checkout" style="display: {{ $calculatedUseLoyaltyPoints ? 'block' : 'none' }}; margin: 10px 0;">
                                            <label for="loyalty_points_to_use_checkout">{{ __t('points_to_use') }} ({{ __t('max') }}: {{ number_format($maxPointsToUse) }})</label>
                                            <input type="number" 
                                                   id="loyalty_points_to_use_checkout" 
                                                   name="loyalty_points_to_use" 
                                                   value="{{ $calculatedLoyaltyPointsToUse }}" 
                                                   min="0" 
                                                   max="{{ $maxPointsToUse }}"
                                                   onchange="calculateTotalCheckout()"
                                                   style="width: 100%; padding: 8px; margin-top: 5px;">
                                            <small>{{ __t('points_discount_info') }}: 10 {{ __t('points') }} = $1</small>
                                        </div>
                                    </li>
                                @endif
                            @endauth
                            
                            @if($calculatedLoyaltyDiscount > 0)
                                <div class="checkout__order__discount">{{ __t('loyalty_discount') }} <span style="float: right;">-${{ number_format($calculatedLoyaltyDiscount, 2) }}</span></div>
                            @endif
                            
                            <div class="checkout__order__total" style="font-weight: bold; font-size: 18px; border-top: 2px solid #ddd; padding-top: 10px; margin-top: 10px;">
                                {{ __t('total') }} <span id="checkout_total" style="float: right;">${{ number_format($calculatedTotal, 2) }}</span>
                            </div>
                            <div class="checkout__input__checkbox">
                                <label for="cash_payment">
                                    {{ __t('cash_payment') }}
                                    <input type="radio" id="cash_payment" name="payment_method" value="cash" checked>
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                            <button type="submit" class="site-btn">{{ __t('place_order') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<!-- Checkout Section End -->

@push('scripts')
<script>
    function toggleLoyaltyPointsCheckout() {
        const checkbox = document.getElementById('use_loyalty_points_checkout');
        const inputDiv = document.getElementById('loyalty_points_input_checkout');
        
        if (checkbox && checkbox.checked) {
            if (inputDiv) inputDiv.style.display = 'block';
        } else {
            if (inputDiv) inputDiv.style.display = 'none';
            const pointsInput = document.getElementById('loyalty_points_to_use_checkout');
            if (pointsInput) pointsInput.value = 0;
            calculateTotalCheckout();
        }
    }
    
    function calculateTotalCheckout() {
        const usePointsCheckbox = document.getElementById('use_loyalty_points_checkout');
        const pointsInput = document.getElementById('loyalty_points_to_use_checkout');
        const totalSpan = document.getElementById('checkout_total') || document.querySelector('.checkout__order__total span');
        const discountDiv = document.querySelector('.checkout__order__discount');
        
        if (!usePointsCheckbox || !totalSpan) return;
        
        const usePoints = usePointsCheckbox.checked;
        const pointsToUse = parseFloat(pointsInput ? pointsInput.value : 0) || 0;
        const subtotal = {{ $calculatedSubtotal ?? 0 }};
        const discount = usePoints ? (pointsToUse / 10) : 0;
        const finalTotal = Math.max(0, subtotal - discount);
        
        totalSpan.textContent = '$' + finalTotal.toFixed(2);
        
        // Update discount display
        if (discount > 0) {
            if (!discountDiv) {
                const subtotalDiv = document.querySelector('.checkout__order__subtotal');
                if (subtotalDiv) {
                    const newDiscountDiv = document.createElement('div');
                    newDiscountDiv.className = 'checkout__order__discount';
                    newDiscountDiv.innerHTML = '{{ __t("loyalty_discount") }} <span style="float: right;">-$' + discount.toFixed(2) + '</span>';
                    subtotalDiv.parentNode.insertBefore(newDiscountDiv, subtotalDiv.nextSibling);
                }
            } else {
                discountDiv.innerHTML = '{{ __t("loyalty_discount") }} <span style="float: right;">-$' + discount.toFixed(2) + '</span>';
            }
        } else {
            if (discountDiv) discountDiv.remove();
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize loyalty points calculation
        const pointsInput = document.getElementById('loyalty_points_to_use_checkout');
        if (pointsInput) {
            pointsInput.addEventListener('input', calculateTotalCheckout);
        }
        calculateTotalCheckout();
    });
</script>
@endpush

@endsection

