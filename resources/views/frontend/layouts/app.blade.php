<!DOCTYPE html>
<html lang="{{ session('locale', 'ar') }}" dir="{{ session('direction', 'rtl') }}">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Ogani Template">
    <meta name="keywords" content="Ogani, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'Ogani | Template' }}</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;900&display=swap" rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="{{ asset('front/assets/css/bootstrap.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('front/assets/css/font-awesome.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('front/assets/css/elegant-icons.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('front/assets/css/nice-select.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('front/assets/css/jquery-ui.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('front/assets/css/owl.carousel.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('front/assets/css/slicknav.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('front/assets/css/style.css') }}" type="text/css">
    
    <!-- RTL Support -->
    @if(session('direction', 'ltr') === 'rtl')
        <link rel="stylesheet" href="{{ asset('front/assets/css/rtl.css') }}" type="text/css">
    @endif
    
    <style>
        /* RTL Basic Styles */
        [dir="rtl"] {
            text-align: right;
        }
        [dir="rtl"] .row {
            direction: rtl;
        }
        [dir="rtl"] .col-lg-3,
        [dir="rtl"] .col-lg-6,
        [dir="rtl"] .col-lg-9,
        [dir="rtl"] .col-md-4,
        [dir="rtl"] .col-md-6,
        [dir="rtl"] .col-sm-6 {
            float: right;
        }
        [dir="rtl"] .ml-auto {
            margin-left: 0 !important;
            margin-right: auto !important;
        }
        [dir="rtl"] .mr-auto {
            margin-right: 0 !important;
            margin-left: auto !important;
        }
        [dir="rtl"] .text-left {
            text-align: right !important;
        }
        [dir="rtl"] .text-right {
            text-align: left !important;
        }
        [dir="rtl"] .float-left {
            float: right !important;
        }
        [dir="rtl"] .float-right {
            float: left !important;
        }
        
        /* User dropdown menu styles */
        .header__top__right__auth {
            position: relative;
        }
        .auth-dropdown {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .auth-dropdown li {
            padding: 8px 15px;
        }
        .auth-dropdown li:hover {
            background-color: #f5f5f5;
        }
        .auth-dropdown a, .auth-dropdown button {
            display: block;
            width: 100%;
            text-align: left;
        }
        [dir="rtl"] .auth-dropdown {
            right: auto;
            left: 0;
        }
        [dir="rtl"] .auth-dropdown a, [dir="rtl"] .auth-dropdown button {
            text-align: right;
        }
    </style>
    
    @stack('head') {{-- مكان لإضافة أكواد CSS أو meta إضافية --}}
</head>

<body>
    @include('frontend.partials.header')

    <main>
        @if(session('success'))
            <div class="container mt-3">
                <div class="alert alert-success alert-dismissible fade show text-center" role="alert" style="margin-top: 20px;">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif
        
        @if(session('error'))
            <div class="container mt-3">
                <div class="alert alert-danger alert-dismissible fade show text-center" role="alert" style="margin-top: 20px;">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif
        
        @yield('content')
    </main>

    @include('frontend.partials.footer')

    <!-- Js Plugins -->
    <script src="{{ asset('front/assets/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('front/assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('front/assets/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('front/assets/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('front/assets/js/jquery.slicknav.js') }}"></script>
    <script src="{{ asset('front/assets/js/mixitup.min.js') }}"></script>
    <script src="{{ asset('front/assets/js/owl.carousel.min.js') }}"></script>
    @if(session('direction', 'ltr') === 'rtl')
        <script src="{{ asset('front/assets/js/main-rtl.js') }}"></script>
    @else
        <script src="{{ asset('front/assets/js/main.js') }}"></script>
    @endif
    @stack('scripts') {{-- مكان لإضافة سكربتات صفحات معينة --}}
    
    <script>
        // Wishlist functionality
        $(document).ready(function() {
            // Handle wishlist toggle on product pages
            $(document).on('click', '.wishlist-toggle-btn', function(e) {
                e.preventDefault();
                
                const productId = $(this).data('product-id');
                const heartIcon = $(this).find('i.fa-heart');
                
                @auth
                    // User is logged in - toggle wishlist
                    $.ajax({
                        url: '{{ url("/wishlist/toggle") }}/' + productId,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                // Toggle active class
                                if (response.is_in_wishlist) {
                                    heartIcon.addClass('active');
                                } else {
                                    heartIcon.removeClass('active');
                                    
                                    // If on wishlist page and item removed, remove the product card
                                    const removeOnToggle = $(e.target).closest('.wishlist-toggle-btn').data('remove-on-toggle');
                                    if (removeOnToggle) {
                                        $(e.target).closest('.col-lg-3, .col-md-4, .col-sm-6').fadeOut(300, function() {
                                            $(this).remove();
                                            
                                            // Check if wishlist is now empty
                                            if ($('.product__item').length === 0) {
                                                location.reload(); // Reload to show empty message
                                            }
                                        });
                                    }
                                }
                                
                                // Update wishlist count in header
                                $('#wishlist-count, #mobile-wishlist-count').text(response.wishlist_count);
                                
                                // Show notification (optional)
                                console.log(response.message);
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 401) {
                                alert('Please login to add items to wishlist');
                                // Optionally redirect to login
                                // window.location.href = '{{ route("login") }}';
                            } else {
                                alert('An error occurred. Please try again.');
                            }
                        }
                    });
                @else
                    // User is not logged in
                    alert('Please login to add items to wishlist');
                    // Optionally redirect to login
                    // window.location.href = '{{ route("login") }}';
                @endauth
            });
            
            // Update wishlist count on page load (if user is logged in)
            @auth
                $.ajax({
                    url: '{{ route("frontend.wishlist.count") }}',
                    method: 'GET',
                    success: function(response) {
                        $('#wishlist-count, #mobile-wishlist-count').text(response.count);
                    }
                });
            @endauth
        });
        
        // User dropdown menu toggle (only for logged-in users)
        $(document).ready(function() {
            // Only prevent default for dropdown toggle (when user is logged in)
            $('.header__top__right__auth').on('click', 'a[href="#"]', function(e) {
                e.preventDefault();
                $(this).next('.auth-dropdown').toggle();
            });
            
            // Allow normal link behavior for login link (when user is not logged in)
            // Login link will work normally without preventDefault
            
            // Close dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.header__top__right__auth').length) {
                    $('.auth-dropdown').hide();
                }
            });
        });
    </script>
</body>

</html>
