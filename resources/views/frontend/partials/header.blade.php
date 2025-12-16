<!-- Page Preloder -->
<div id="preloder">
    <div class="loader"></div>
</div>

<!-- Humberger Begin -->
<div class="humberger__menu__overlay"></div>
<div class="humberger__menu__wrapper">
    <div class="humberger__menu__logo">
        <a href="{{ route('frontend.home') }}"><img src="{{ asset('front/assets/img/Health Care Logo.png') }}" alt=""></a>
    </div>
    <div class="humberger__menu__cart">
        <ul>
            <li>
                <a href="{{ auth()->check() ? route('frontend.wishlist.index') : route('login') }}" id="mobile-wishlist-link">
                    <i class="fa fa-heart"></i> 
                    <span id="mobile-wishlist-count">{{ auth()->check() ? auth()->user()->wishlists()->count() : 0 }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('frontend.cart.index') }}">
                    <i class="fa fa-shopping-bag"></i> 
                    <span id="mobile-cart-count">{{ count(session('cart', [])) }}</span>
                </a>
            </li>
        </ul>
        @php
            $cart = session('cart', []);
            $cartTotal = 0;
            foreach ($cart as $item) {
                $product = \App\Models\Product::find($item['product_id'] ?? null);
                if ($product) {
                    $price = $product->price;
                    if (!empty($item['variant_id'])) {
                        $variant = $product->variants->where('id', $item['variant_id'])->first();
                        if ($variant) {
                            $price = $variant->variant_price ?? $product->price;
                        }
                    }
                    $cartTotal += $price * ($item['quantity'] ?? 1);
                }
            }
        @endphp
        <div class="header__cart__price"><span id="cart-total">{{ number_format($cartTotal, 2) }}</span></div>
    </div>
    <div class="humberger__menu__widget">
        <div class="header__top__right__language">
            <img src="{{ asset('front/assets/img/language.png') }}" alt="">
            <div>{{ session('locale', 'en') === 'ar' ? 'العربية' : 'English' }}</div>
            {{-- <span class="arrow_carrot-down"></span> --}}
            {{-- <ul>
                <li>
                    <form action="{{ route('frontend.language.switch') }}" method="POST" style="display: inline;">
                        @csrf
                        <input type="hidden" name="lang" value="ar">
                        <input type="hidden" name="dir" value="rtl">
                        <button type="submit" style="background: none; border: none; color: inherit; cursor: pointer; width: 100%; text-align: left; padding: 5px 15px;">العربية</button>
                    </form>
                </li>
                <li>
                    <form action="{{ route('frontend.language.switch') }}" method="POST" style="display: inline;">
                        @csrf
                        <input type="hidden" name="lang" value="en">
                        <input type="hidden" name="dir" value="ltr">
                        <button type="submit" style="background: none; border: none; color: inherit; cursor: pointer; width: 100%; text-align: left; padding: 5px 15px;">English</button>
                    </form>
                </li>
            </ul> --}}
        </div>
        <div class="header__top__right__auth">
            @auth
                <div style="position: relative; display: inline-block;" class="auth-dropdown-wrapper">
                    <a href="#" style="cursor: pointer;" class="auth-dropdown-toggle"><i class="fa fa-user"></i> {{ auth()->user()->name }}</a>
                    <ul class="auth-dropdown">
                        <li style="border-bottom: 1px solid #eee;">
                            <span style="font-size: 12px; color: #666;">{{ auth()->user()->email }}</span>
                        </li>
                        <li style="border-bottom: 1px solid #eee;">
                            <span style="font-size: 12px; color: #666;">{{ __t('role') }}: {{ auth()->user()->role }}</span>
                        </li>
                        @if(auth()->user()->role === 'admin')
                            <li>
                                <a href="{{ route('admin.dashboard') }}">{{ __t('admin_dashboard') }}</a>
                            </li>
                        @endif
                        <li>
                            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit">{{ __t('logout') }}</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <a href="{{ route('login') }}"><i class="fa fa-user"></i> {{ __t('login') }}</a>
            @endauth
        </div>
    </div>
    <div class="humberger__menu__categories">
        <div class="hero__categories__all">
            <i class="fa fa-bars"></i>
            <span>{{ __t('all_departments') }}</span>
        </div>
        <ul>
            @php
                $categoryRepo = app(\App\Repositories\CategoryRepository::class);
                $allCategories = $categoryRepo->getAll();
            @endphp
            @foreach($allCategories as $cat)
                <li><a href="{{ route('frontend.categories.show', $cat->id) }}">{{ $cat->name }}</a></li>
            @endforeach
        </ul>
    </div>
    <nav class="humberger__menu__nav mobile-menu">
        <ul>
            <li class="active"><a href="{{ route('frontend.home') }}">{{ __t('home') }}</a></li>
            <li><a href="{{ route('frontend.products.index') }}">{{ __t('shop') }}</a></li>
            {{-- <li><a href="#">{{ __t('pages') }}</a>
                <ul class="header__menu__dropdown">
                    <li><a href="{{ route('frontend.products.index') }}">{{ __t('shop_details') }}</a></li>
                    <li><a href="#">{{ __t('shopping_cart') }}</a></li>
                    <li><a href="#">{{ __t('checkout') }}</a></li>
                    <li><a href="#">{{ __t('blog') }} Details</a></li>
                </ul>
            </li> --}}
            <li><a href="#">{{ __t('blog') }}</a></li>
            <li><a href="{{ route('frontend.contact') }}">{{ __t('contact') }}</a></li>
        </ul>
    </nav>
    <div id="mobile-menu-wrap"></div>
    <div class="header__top__right__social">
        <a href="#"><i class="fa fa-facebook"></i></a>
        <a href="#"><i class="fa fa-linkedin"></i></a>
        <a href="#"><i class="fa-brands fa-tiktok"></i></a>
    </div>
    <div class="humberger__menu__contact">
        <ul>
            <li><i class="fa fa-envelope"></i> info@healthcare.com</li>
            <li>{{ __t('free_shipping') }}</li>
        </ul>
    </div>
</div>
<!-- Humberger End -->

<!-- Header Section Begin -->
<header class="header">
    <div class="header__top">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="header__top__left">
                        <ul>
                            <li><i class="fa fa-envelope"></i> info@healthcare.com</li>
                            <li>{{ __t('free_shipping') }}</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="header__top__right">
                        <div class="header__top__right__social">
                            <a href="#"><i class="fa fa-facebook"></i></a>
                            <a href="#"><i class="fa fa-linkedin"></i></a>
                            <a href="#"><i class="fa-brands fa-tiktok"></i></a>
                        </div>
                        <div class="header__top__right__language">
                            <img src="{{ asset('front/assets/img/language.png') }}" alt="">
                            <div>{{ session('locale', 'en') === 'ar' ? 'العربية' : 'English' }}</div>
                            {{-- <span class="arrow_carrot-down"></span> --}}
                            {{-- <ul>
                                <li>
                                    <form action="{{ route('frontend.language.switch') }}" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="lang" value="ar">
                                        <input type="hidden" name="dir" value="rtl">
                                        <button type="submit" style="background: none; border: none; color: inherit; cursor: pointer; width: 100%; text-align: left; padding: 5px 15px;">العربية</button>
                                    </form>
                                </li>
                                <li>
                                    <form action="{{ route('frontend.language.switch') }}" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="lang" value="en">
                                        <input type="hidden" name="dir" value="ltr">
                                        <button type="submit" style="background: none; border: none; color: inherit; cursor: pointer; width: 100%; text-align: left; padding: 5px 15px;">English</button>
                                    </form>
                                </li>
                            </ul> --}}
                        </div>
                        <div class="header__top__right__auth">
                            @auth
                                <div style="position: relative; display: inline-block;" class="auth-dropdown-wrapper">
                                    <a href="#" style="cursor: pointer;" class="auth-dropdown-toggle"><i class="fa fa-user"></i> {{ auth()->user()->name }}</a>
                                    <ul class="auth-dropdown">
                                        <li style="border-bottom: 1px solid #eee;">
                                            <span style="font-size: 12px; color: #666;">{{ auth()->user()->email }}</span>
                                        </li>
                                        <li style="border-bottom: 1px solid #eee;">
                                            <span style="font-size: 12px; color: #666;">{{ __t('role') }}: {{ auth()->user()->role }}</span>
                                        </li>
                                        @if(auth()->user()->role === 'admin')
                                            <li>
                                                <a href="{{ route('admin.dashboard') }}">{{ __t('admin_dashboard') }}</a>
                                            </li>
                                        @endif
                                        <li>
                                            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                                                @csrf
                                                <button type="submit">{{ __t('logout') }}</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @else
                                <a href="{{ route('login') }}"><i class="fa fa-user"></i> {{ __t('login') }}</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="header__logo">
                    <a href="{{ route('frontend.home') }}"><img src="{{ asset('front/assets/img/Health Care Logo.png') }}" alt=""></a>
                </div>
            </div>
            <div class="col-lg-6">
                <nav class="header__menu">
                    <ul>
                        <li class="active"><a href="{{ route('frontend.home') }}">{{ __t('home') }}</a></li>
                        <li><a href="{{ route('frontend.products.index') }}">{{ __t('shop') }}</a></li>
                        {{-- <li><a href="#">{{ __t('pages') }}</a>
                            <ul class="header__menu__dropdown">
                                <li><a href="{{ route('frontend.products.index') }}">{{ __t('shop_details') }}</a></li>
                                <li><a href="#">{{ __t('shopping_cart') }}</a></li>
                                <li><a href="{{ route('frontend.checkout.index') }}">{{ __t('checkout') }}</a></li>
                                <li><a href="#">{{ __t('blog') }} Details</a></li>
                            </ul>
                        </li> --}}
                        <li><a href="#">{{ __t('blog') }}</a></li>
                        <li><a href="{{ route('frontend.contact') }}">{{ __t('contact') }}</a></li>
                    </ul>
                </nav>
            </div>
            <div class="col-lg-3">
                <div class="header__cart">
                    <ul>
                        <li>
                            <a href="{{ auth()->check() ? route('frontend.wishlist.index') : route('login') }}" id="wishlist-link">
                                <i class="fa fa-heart"></i> 
                                <span id="wishlist-count">{{ auth()->check() ? auth()->user()->wishlists()->count() : 0 }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('frontend.cart.index') }}">
                                <i class="fa fa-shopping-bag"></i> 
                                <span id="cart-count">{{ count(session('cart', [])) }}</span>
                            </a>
                        </li>
                    </ul>
                    @php
                        $cart = session('cart', []);
                        $cartTotal = 0;
                        foreach ($cart as $item) {
                            $product = \App\Models\Product::find($item['product_id'] ?? null);
                            if ($product) {
                                $price = $product->price;
                                if (!empty($item['variant_id'])) {
                                    $variant = $product->variants->where('id', $item['variant_id'])->first();
                                    if ($variant) {
                                        $price = $variant->variant_price ?? $product->price;
                                    }
                                }
                                $cartTotal += $price * ($item['quantity'] ?? 1);
                            }
                        }
                    @endphp
                    <div class="header__cart__price"><span id="desktop-cart-total">{{ number_format($cartTotal, 2) }}</span></div>
                </div>
            </div>
        </div>
        <div class="humberger__open">
            <i class="fa fa-bars"></i>
        </div>
    </div>
</header>
<!-- Header Section End -->
