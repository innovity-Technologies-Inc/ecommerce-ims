<!-- Footer Area start -->
@php
    $gs = \App\HelperClass::generalSettings();
    $cs = \App\HelperClass::contactSettings();
@endphp
<footer class="footer-area">
    <div class="footer-top">
        <div class="container">
            <div class="row">
                <!-- footer single wedget -->
                <div class="col-md-6 col-lg-4">
                    <!-- footer logo -->
                    <div class="footer-logo">
                        <a href="{{ route('home') }}"><img src="{{ $gs->light_logo ? \App\HelperClass::file_url($gs->light_logo) : asset('client/assets/images/logo/footer-logo.png') }}" alt="{{ $gs->business_name ?? '' }}" style="max-height: 40px; width: auto;"></a>
                    </div>
                    <!-- footer logo -->
                    <div class="about-footer">
                        <p class="text-info">{{ $gs->meta_description ?? 'High quality e-commerce products at your doorstep.' }}</p>
                        @if($cs && $cs->phone_number)
                            <div class="need-help">
                                <p class="phone-info">
                                    NEED HELP?
                                    <span>
                                        {{ $cs->phone_number }}
                                    </span>
                                </p>
                            </div>
                        @endif
                        <div class="social-info">
                            <ul>
                                @if($cs && $cs->facebook_status && $cs->facebook_url)
                                    <li>
                                        <a href="{{ $cs->facebook_url }}" target="_blank"><i class="ion-social-facebook"></i></a>
                                    </li>
                                @endif
                                @if($cs && $cs->x_status && $cs->x_url)
                                    <li>
                                        <a href="{{ $cs->x_url }}" target="_blank"><iconify-icon icon="ri:twitter-x-fill"></iconify-icon></a>
                                    </li>
                                @endif
                                @if($cs && $cs->instagram_status && $cs->instagram_url)
                                    <li>
                                        <a href="{{ $cs->instagram_url }}" target="_blank"><i class="ion-social-instagram"></i></a>
                                    </li>
                                @endif
                                @if($cs && $cs->youtube_status && $cs->youtube_url)
                                    <li>
                                        <a href="{{ $cs->youtube_url }}" target="_blank"><i class="ion-social-youtube"></i></a>
                                    </li>
                                @endif
                                @if($cs && $cs->whatsapp_status && $cs->whatsapp_url)
                                    <li>
                                        <a href="{{ $cs->whatsapp_url }}" target="_blank"><i class="ion-social-whatsapp"></i></a>
                                    </li>
                                @endif
                                @if($cs && $cs->tiktok_status && $cs->tiktok_url)
                                    <li>
                                        <a href="{{ $cs->tiktok_url }}" target="_blank"><iconify-icon icon="ri:tiktok-fill"></iconify-icon></a>
                                    </li>
                                @endif
                                @if($cs && $cs->linkedin_status && $cs->linkedin_url)
                                    <li>
                                        <a href="{{ $cs->linkedin_url }}" target="_blank"><i class="ion-social-linkedin"></i></a>
                                    </li>
                                @endif
                                @if($cs && $cs->thread_status && $cs->thread_url)
                                    <li>
                                        <a href="{{ $cs->thread_url }}" target="_blank"><iconify-icon icon="ri:threads-fill"></iconify-icon></a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- footer single wedget -->
                <div class="col-md-6 col-lg-2 mt-res-sx-30px mt-res-md-30px">
                    <div class="single-wedge">
                        <h4 class="footer-herading">Information</h4>
                        <div class="footer-links">
                            <ul>
                                <li><a href="{{ route('client.privacy_policy') }}">Privacy Policy</a></li>
                                <li><a href="{{ route('client.return_policy') }}">Return Policy</a></li>
                                <li><a href="{{ route('client.faq') }}">FAQ</a></li>
                                <li><a href="{{ route('client.returns.index') }}">Returns</a></li>
                                <li><a href="{{ route('client.contact') }}">Contact Us</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- footer single wedget -->
                <div class="col-md-6 col-lg-2 mt-res-md-50px mt-res-sx-30px mt-res-md-30px">
                    <div class="single-wedge">
                        <h4 class="footer-herading">Custom Links</h4>
                        <div class="footer-links">
                            <ul>
                                <li><a href="#">Legal Notice</a></li>
                                <li><a href="#">Prices Drop</a></li>
                                <li><a href="#">New Products</a></li>
                                <li><a href="#">Best Sales</a></li>
                                <li><a href="{{ route('client.returns.index') }}">Returns</a></li>
                                <li><a href="{{ route('client.track_order') }}">Track Order</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- footer single wedget -->
                <div class="col-md-6 col-lg-4 mt-res-md-50px mt-res-sx-30px mt-res-md-30px">
                    <div class="single-wedge">
                        <h4 class="footer-herading">Newsletter</h4>
                        <div class="subscrib-text">
                            <p>You may unsubscribe at any moment. For that purpose, please find our contact info in the legal notice.</p>
                        </div>
                        <div id="mc_embed_signup" class="subscribe-form">
                            <form id="mc-embedded-subscribe-form" class="validate" novalidate="" target="_blank" name="mc-embedded-subscribe-form" method="post" action="http://devitems.us11.list-manage.com/subscribe/post?u=6bbb9b6f5827bd842d9640c82&amp;id=05d85f18ef">
                                <div id="mc_embed_signup_scroll" class="mc-form">
                                    <input class="email" type="email" required="" placeholder="Enter your email here.." name="EMAIL" value="">
                                    <div class="mc-news" aria-hidden="true" style="position: absolute; left: -5000px;">
                                        <input type="text" value="" tabindex="-1" name="b_6bbb9b6f5827bd842d9640c82_05d85f18ef">
                                    </div>
                                    <div class="clear">
                                        <input id="mc-embedded-subscribe" class="button" type="submit" name="subscribe" value="Sign Up">
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="img_app">
                            <a href="#"><img src="{{asset('client/assets/images/icons/app_store.png')}}" alt=""></a>
                            <a href="#"><img src="{{asset('client/assets/images/icons/google_play.png')}}" alt=""></a>
                        </div>
                    </div>
                </div>
                <!-- footer single wedget -->
            </div>
        </div>
    </div>
    <!--  Footer Bottom Area start -->
    <div class="footer-bottom">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-lg-5 text-center text-md-start order-2 order-md-1 mt-4 mt-md-0">
                    <p class="copy-text">
                        Copyright © <a href="{{ route('home') }}"> {{ $gs->business_name ?? 'Smart Ecom' }}</a>. All Rights Reserved
                        | Developed by <a href="https://gen-itech.com/" target="_blank">Gen-Itech</a>
                    </p>
                </div>
                <div class="col-md-6 col-lg-7 text-center text-md-end order-1 order-md-2">
                    <img class="payment-img" src="{{asset('client/assets/images/icons/payment.png')}}" alt="">
                </div>
            </div>
        </div>
    </div>
    <!--  Footer Bottom Area End-->
</footer>
<!--  Footer Area End -->
