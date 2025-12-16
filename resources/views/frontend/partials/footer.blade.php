<footer class="footer spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="footer__about">
                    <div class="footer__about__logo">
                        <a href="{{ route('frontend.home') }}"><img src="{{ asset('front/assets/img/Health Care Logo.png') }}" alt=""></a>
                    </div>
                    <ul>
                        <li>{{ __t('address') }}: Sea St, next to Alex Bank</li>
                        <li>{{ __t('phone') }}: 01550431131</li>
                        <li>{{ __t('email') }}: info@healthcare.com</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 offset-lg-1">
                <div class="footer__widget">
                    <h6>{{ __t('useful_links') }}</h6>
                    <ul>
                        <li><a href="#">{{ __t('about_us') }}</a></li>
                        <li><a href="#">{{ __t('about_our_shop') }}</a></li>
                        <li><a href="#">{{ __t('secure_shopping') }}</a></li>
                        <li><a href="#">{{ __t('delivery_information') }}</a></li>
                        <li><a href="#">{{ __t('privacy_policy') }}</a></li>
                       
                    </ul>
                    <ul>
                        <li><a href="#">{{ __t('who_we_are') }}</a></li>
                        <li><a href="#">{{ __t('our_services') }}</a></li>
                        <li><a href="#">{{ __t('projects') }}</a></li>
                        <li><a href="#">{{ __t('contact') }}</a></li>
                       
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="footer__widget">
                    <h6>{{ __t('join_newsletter') }}</h6>
                    <p>{{ __t('newsletter_description') }}</p>
                    <form action="#">
                        <input type="text" placeholder="{{ __t('enter_your_mail') }}">
                        <button type="submit" class="site-btn">{{ __t('subscribe') }}</button>
                    </form>
                
                </div>
            </div>
        </div>
       
    </div>
</footer>
