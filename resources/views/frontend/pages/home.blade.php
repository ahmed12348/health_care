@extends('frontend.layouts.app')

@section('content')

    <!-- Hero Section Begin -->
    <section class="hero">
        <div class="container">
            <div class="row">
                {{-- Sidebar column (categories) --}}
                @include('frontend.partials.sidebar')

                {{-- Right column: search + hero item --}}
                <div class="col-lg-9">
                    <div class="hero__search">
                        <div class="hero__search__form">
                            <form action="#">
                                <div class="hero__search__categories">
                                    {{ __t('all_categories') }}
                                    <span class="arrow_carrot-down"></span>
                                </div>
                                <input type="text" placeholder="{{ __t('what_do_you_need') }}">
                                <button type="submit" class="site-btn">{{ __t('search') }}</button>
                            </form>
                        </div>
                        <div class="hero__search__phone">
                            <div class="hero__search__phone__icon">
                                <i class="fa fa-phone"></i>
                            </div>
                            <div class="hero__search__phone__text">
                                <h5>01550431131</h5>
                                <span>{{ __t('support_24_7') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="hero__item set-bg" data-setbg="{{ asset('front/assets/img/hero/bannerr.jpg') }}">
                        <div class="hero__text">
                            
                            {{-- <a href="{{ route('frontend.products.index') }}" class="primary-btn">{{ __t('shop_now') }}</a> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Hero Section End -->

    <!-- Categories Section Begin -->
    <section class="categories">
        <div class="container">
            <div class="row">
                <div class="categories__slider owl-carousel">

                    @foreach($sliderCategories as $cat)
                        <div class="col-lg-3">
                            <div class="categories__item set-bg"
                                data-setbg="{{ $cat->image_url ?? asset('front/assets/img/categories/cat-1.jpg') }}">
                                <h5><a href="{{ route('frontend.categories.show', $cat->id) }}">{{ $cat->name }}</a></h5>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </section>
    <!-- Categories Section End -->

 <!-- Featured Section Begin -->
    <section class="featured spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <h2>{{ __t('featured_product') }}</h2>
                    </div>
                    <div class="featured__controls">
                        <ul>
                            <li class="active" data-filter="*">{{ __t('all') }}</li>
                            @foreach($categories as $category)
                                <li data-filter=".category-{{ $category->id }}">{{ $category->name }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row featured__filter">
                @foreach($featuredProducts as $product)
                    <div class="col-lg-3 col-md-4 col-sm-6 mix category-{{ $product->category_id ?? 0 }}">
                        <div class="featured__item">
                            <div class="featured__item__pic set-bg" data-setbg="{{ $product->image_url }}">
                                <ul class="featured__item__pic__hover">
                                    <li>
                                        <a href="#" 
                                           class="wishlist-toggle-btn" 
                                           data-product-id="{{ $product->id }}"
                                           title="{{ auth()->check() ? 'Add to wishlist' : 'Login to add to wishlist' }}">
                                            <i class="fa fa-heart {{ auth()->check() && auth()->user()->wishlists()->where('product_id', $product->id)->exists() ? 'active' : '' }}"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('frontend.cart.add') }}" method="POST" class="d-inline add-to-cart-form">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <a href="#" onclick="event.preventDefault(); this.closest('form').submit();" title="{{ __t('add_to_cart') }}">
                                                <i class="fa fa-shopping-cart"></i>
                                            </a>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                            <div class="featured__item__text">
                                <h6><a href="{{ route('frontend.products.show', $product->id) }}">{{ $product->name }}</a></h6>
                                <h5>
                                    @if($product->variants->count() > 0)
                                        ${{ number_format($product->variants->min('variant_price') ?? $product->price, 2) }}
                                        @if($product->variants->min('variant_price') != $product->variants->max('variant_price'))
                                            - ${{ number_format($product->variants->max('variant_price'), 2) }}
                                        @endif
                                    @else
                                        ${{ number_format($product->price, 2) }}
                                    @endif
                                </h5>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- Featured Section End -->

    <!-- All Products Section Begin -->
    <section class="featured spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <h2>{{ __t('all_products') }}</h2>
                    </div>
                    <div class="featured__controls">
                        <ul>
                            <li class="active" data-filter="*">{{ __t('all') }}</li>
                            @foreach($categories as $category)
                                <li data-filter=".all-category-{{ $category->id }}">{{ $category->name }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row featured__filter" id="all-products-filter">
                @foreach($allProducts as $product)
                    <div class="col-lg-3 col-md-4 col-sm-6 mix all-category-{{ $product->category_id ?? 0 }}">
                        <div class="featured__item">
                            <div class="featured__item__pic set-bg" data-setbg="{{ $product->image_url }}">
                                <ul class="featured__item__pic__hover">
                                    <li>
                                        <a href="#" 
                                           class="wishlist-toggle-btn" 
                                           data-product-id="{{ $product->id }}"
                                           title="{{ auth()->check() ? 'Add to wishlist' : 'Login to add to wishlist' }}">
                                            <i class="fa fa-heart {{ auth()->check() && auth()->user()->wishlists()->where('product_id', $product->id)->exists() ? 'active' : '' }}"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('frontend.cart.add') }}" method="POST" class="d-inline add-to-cart-form">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <a href="#" onclick="event.preventDefault(); this.closest('form').submit();" title="{{ __t('add_to_cart') }}">
                                                <i class="fa fa-shopping-cart"></i>
                                            </a>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                            <div class="featured__item__text">
                                <h6><a href="{{ route('frontend.products.show', $product->id) }}">{{ $product->name }}</a></h6>
                                <h5>
                                    @if($product->variants->count() > 0)
                                        ${{ number_format($product->variants->min('variant_price') ?? $product->price, 2) }}
                                        @if($product->variants->min('variant_price') != $product->variants->max('variant_price'))
                                            - ${{ number_format($product->variants->max('variant_price'), 2) }}
                                        @endif
                                    @else
                                        ${{ number_format($product->price, 2) }}
                                    @endif
                                </h5>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- All Products Section End -->

    <!-- Banner Begin -->
    {{-- <div class="banner">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="banner__pic">
                        <img src="{{ asset('front/assets/img/banner/banner-1.jpg') }}" alt="">
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="banner__pic">
                        <img src="{{ asset('front/assets/img/banner/banner-2.jpg') }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <!-- Banner End -->

    <!-- Latest Product Section Begin (copy) -->
    <!-- <section class="latest-product spad">
        <div class="container">
            <div class="row">
                {{-- left slider --}}
                <div class="col-lg-4 col-md-6">
                    <div class="latest-product__text">
                        <h4>{{ __t('latest_products') }}</h4>
                        <div class="latest-product__slider owl-carousel">
                            <div class="latest-prdouct__slider__item">
                                <a href="#" class="latest-product__item">
                                    <div class="latest-product__item__pic">
                                        <img src="{{ asset('front/assets/img/latest-product/lp-1.jpg') }}" alt="">
                                    </div>
                                    <div class="latest-product__item__text">
                                        <h6>Crab Pool Security</h6>
                                        <span>$30.00</span>
                                    </div>
                                </a>
                                {{-- more items... --}}
                            </div>
                            <div class="latest-prdouct__slider__item">
                                {{-- more items... --}}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- center --}}
                <div class="col-lg-4 col-md-6">
                    <div class="latest-product__text">
                        <h4>{{ __t('top_rated_products') }}</h4>
                        <div class="latest-product__slider owl-carousel">
                            {{-- slider items --}}
                        </div>
                    </div>
                </div>

                {{-- right --}}
                <div class="col-lg-4 col-md-6">
                    <div class="latest-product__text">
                        <h4>{{ __t('review_products') }}</h4>
                        <div class="latest-product__slider owl-carousel">
                            {{-- slider items --}}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section> -->
    <!-- Latest Product Section End -->

    <!-- Blog Section Begin -->
    <section class="from-blog spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title from-blog__title">
                        <h2>{{ __t('from_the_blog') }}</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="blog__item">
                        <div class="blog__item__pic">
                            <img src="{{ asset('front/assets/img/blog/1_.jpg') }}" alt="{{ __t('blog_post_1_title') }}">
                        </div>
                        <div class="blog__item__text">
                            <h5><a href="#">{{ __t('blog_post_1_title') }}</a></h5>
                            <p>{{ __t('blog_post_1_description') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="blog__item">
                        <div class="blog__item__pic">
                            <img src="{{ asset('front/assets/img/blog/1_.jpg') }}" alt="{{ __t('blog_post_2_title') }}">
                        </div>
                        <div class="blog__item__text">
                            <h5><a href="#">{{ __t('blog_post_2_title') }}</a></h5>
                            <p>{{ __t('blog_post_2_description') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="blog__item">
                        <div class="blog__item__pic">
                            <img src="{{ asset('front/assets/img/blog/1_.jpg') }}" alt="{{ __t('blog_post_3_title') }}">
                        </div>
                        <div class="blog__item__text">
                            <h5><a href="#">{{ __t('blog_post_3_title') }}</a></h5>
                            <p>{{ __t('blog_post_3_description') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Blog Section End -->

@endsection
