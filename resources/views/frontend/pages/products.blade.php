@extends('frontend.layouts.app')

@section('content')

<!-- Breadcrumb Section Begin -->
<section class="breadcrumb-section set-bg" data-setbg="{{ asset('front/assets/img/health1.png') }}">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="breadcrumb__text">
                    <h2>{{ __t('our_products') }}</h2>
                    <div class="breadcrumb__option">
                        <a href="{{ route('frontend.home') }}">{{ __t('home') }}</a>
                        <span>{{ __t('products') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->

<!-- Product Section Begin -->
<section class="product spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-5">
                <div class="sidebar">
                    <div class="sidebar__item">
                        <h4>{{ __t('categories') }}</h4>
                        <ul>
                            <li><a href="{{ route('frontend.products.index') }}">{{ __t('all_products') }}</a></li>
                            @foreach($categories as $category)
                                <li>
                                    <a href="{{ route('frontend.categories.show', $category->id) }}">
                                        {{ $category->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-9 col-md-7">
                <div class="filter__item">
                    <div class="row">
                        <div class="col-lg-4 col-md-5">
                            <div class="filter__sort">
                                <span>{{ __t('sort_by') }}</span>
                                <select>
                                    <option value="">{{ __t('default') }}</option>
                                    <option value="price-low">{{ __t('price_low_high') }}</option>
                                    <option value="price-high">{{ __t('price_high_low') }}</option>
                                    <option value="name">{{ __t('name_a_z') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="filter__found">
                                <h6><span>{{ $products->count() }}</span> {{ __t('products_found') }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    @forelse($products as $product)
                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="product__item">
                                <div class="product__item__pic set-bg" data-setbg="{{ $product->image_url ?? asset('front/assets/img/product/product-1.jpg') }}">
                                    @if($product->is_featured)
                                        <span class="label featured-star" title="{{ __t('featured') }}">
                                            <i class="fa fa-star"></i>
                                        </span>
                                    @endif
                                    @if(($product->stock_quantity ?? 0) <= 0)
                                        <span class="label label-danger">Out of Stock</span>
                                    @endif
                                    <ul class="product__item__pic__hover">
                                        <li>
                                            <a href="#" 
                                               class="wishlist-toggle-btn" 
                                               data-product-id="{{ $product->id }}"
                                               title="{{ auth()->check() ? 'Add to wishlist' : 'Login to add to wishlist' }}">
                                                <i class="fa fa-heart {{ auth()->check() && auth()->user()->wishlists()->where('product_id', $product->id)->exists() ? 'active' : '' }}"></i>
                                            </a>
                                        </li>
                                        <li>
                                            @if(($product->stock_quantity ?? 0) > 0)
                                                <form action="{{ route('frontend.cart.add') }}" method="POST" class="d-inline add-to-cart-form">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                    <input type="hidden" name="quantity" value="1">
                                                    <a href="#" onclick="event.preventDefault(); this.closest('form').submit();" title="{{ __t('add_to_cart') }}">
                                                        <i class="fa fa-shopping-cart"></i>
                                                    </a>
                                                </form>
                                            @else
                                                <a href="#" class="disabled" title="{{ __t('out_of_stock') }}"><i class="fa fa-shopping-cart"></i></a>
                                            @endif
                                        </li>
                                    </ul>
                                </div>
                                <div class="product__item__text">
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
                                    @if($product->category)
                                        <span class="badge bg-secondary">{{ $product->category->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <p>{{ __t('no_products_found') }}</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Product Section End -->

@endsection

