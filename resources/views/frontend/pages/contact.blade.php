@extends('frontend.layouts.app')

@section('content')

<!-- Breadcrumb Section Begin -->
<section class="breadcrumb-section set-bg" data-setbg="{{ asset('front/assets/img/health1.png') }}">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="breadcrumb__text">
                    <h2>{{ __t('contact_us') }}</h2>
                    <div class="breadcrumb__option">
                        <a href="{{ route('frontend.home') }}">{{ __t('home') }}</a>
                        <span>{{ __t('contact_us') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->

<!-- Contact Section Begin -->
<section class="contact spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="contact__form">
                    <h4>{{ __t('contact_us') }}</h4>
                    <form action="{{ route('contact.submit') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <div class="checkout__input">
                                    <p>{{ __t('your_name') }}<span>*</span></p>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="checkout__input">
                                    <p>{{ __t('your_email') }}<span>*</span></p>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>{{ __t('your_message') }}<span>*</span></p>
                                    <textarea name="message" id="message" rows="6" style="width: 100%; padding: 12px; border: 1px solid #e1e1e1; border-radius: 4px; resize: vertical; font-family: inherit;" required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <button type="submit" class="site-btn">{{ __t('send_message') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Contact Section End -->

<style>
.contact {
    padding: 50px 0;
}

.contact__form {
    background: #f5f5f5;
    padding: 40px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.contact__form h4 {
    color: #1c1c1c;
    font-weight: 700;
    margin-bottom: 30px;
    font-size: 24px;
    text-align: center;
}

.contact__form .checkout__input {
    margin-bottom: 20px;
}

.contact__form .checkout__input p {
    color: #1c1c1c;
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 14px;
}

.contact__form .checkout__input p span {
    color: #e74c3c;
}

.contact__form .checkout__input input,
.contact__form .checkout__input textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #e1e1e1;
    border-radius: 4px;
    font-size: 14px;
    color: #1c1c1c;
    transition: all 0.3s;
}

.contact__form .checkout__input input:focus,
.contact__form .checkout__input textarea:focus {
    border-color: #7fad39;
    outline: none;
    box-shadow: 0 0 0 2px rgba(127, 173, 57, 0.1);
}

.contact__form .site-btn {
    width: 100%;
    padding: 14px 30px;
    font-size: 16px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-top: 10px;
}

.contact__form .site-btn:hover {
    background: #6a9a2e;
}

/* RTL Support */
[dir="rtl"] .contact__form {
    text-align: right;
}

[dir="rtl"] .contact__form h4 {
    text-align: center;
}

[dir="rtl"] .contact__form .checkout__input p {
    text-align: right;
}

[dir="rtl"] .contact__form .checkout__input input,
[dir="rtl"] .contact__form .checkout__input textarea {
    text-align: right;
}

[dir="rtl"] .contact__form .site-btn {
    text-align: center;
}

@media (max-width: 768px) {
    .contact__form {
        padding: 25px 20px;
    }
    
    .contact__form h4 {
        font-size: 20px;
    }
}
</style>

@endsection
