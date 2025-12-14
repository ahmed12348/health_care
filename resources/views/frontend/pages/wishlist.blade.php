@extends('frontend.layouts.app')

@section('content')

<!-- Breadcrumb Section Begin -->
<section class="breadcrumb-section set-bg" data-setbg="{{ asset('front/assets/img/health1.png') }}">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="breadcrumb__text">
                    <h2>{{ __t('wishlist') }}</h2>
                    <div class="breadcrumb__option">
                        <a href="{{ route('frontend.home') }}">{{ __t('home') }}</a>
                        <span>{{ __t('wishlist') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->

<!-- Wishlist Section Begin -->
<section class="product spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    @forelse($wishlists as $wishlist)
                        @php
                            $product = $wishlist->product;
                        @endphp
                        <div class="col-lg-3 col-md-4 col-sm-6">
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
                                               data-remove-on-toggle="true"
                                               title="{{ __t('remove_from_wishlist') }}">
                                                <i class="fa fa-heart active"></i>
                                            </a>
                                        </li>
                                        <li>
                                            @if(($product->stock_quantity ?? 0) > 0)
                                                <a href="{{ route('frontend.checkout.index') }}?product_id={{ $product->id }}&quantity=1"><i class="fa fa-shopping-cart"></i></a>
                                            @else
                                                <a href="#" class="disabled"><i class="fa fa-shopping-cart"></i></a>
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
                                <h4>{{ __t('wishlist_empty') }}</h4>
                                <p>{{ __t('wishlist_empty_message') }}</p>
                                <a href="{{ route('frontend.products.index') }}" class="primary-btn">{{ __t('continue_shopping') }}</a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Wishlist Section End -->

@endsection

