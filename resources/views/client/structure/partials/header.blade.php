<!-- Header Start -->
@php
    $gs = \App\HelperClass::generalSettings();
    $cs = \App\HelperClass::contactSettings();
    $nav_categories = \App\HelperClass::getCategories();
    $nav_brands = \App\HelperClass::getBrands();
@endphp
<header class="main-header">
    <!-- Header Top Start -->
    <div class="header-top-nav">
        <div class="container-fluid">
            <div class="row align-items-center">
                <!--Left Start-->
                <div class="col-lg-4 col-md-12">
                    <div class="text-lg-start text-center">
                        <p class="color-white">Welcome
                            @if(Auth::guard('web')->check())
                                <strong>{{Auth::user()->name}}</strong>
                            @else
                                you
                            @endif to {{ $gs->business_name ?? 'Smart Ecom' }}!</p>
                    </div>
                </div>
                <!--Left End-->
                <!--Right Start-->
                <div class="col-8 d-lg-block d-none">
                    <div class="header-right-nav hover-style-default">
                        <ul>
                            <li class="border-color-white">
                                <a href="{{ route('client.track_order') }}"><i class="ion-ios-location-outline"></i>Track Order</a>
                            </li>
                            <li class="border-color-white">
                                <a href="{{ route('user.wishlist.index') }}"><i class="ion-android-favorite-outline"></i>Wishlist ({{ \App\HelperClass::wishlistCount() }})</a>
                            </li>
                        </ul>
                        <!-- Header Top Language Currency -->
                        <div class="header-top-set-lan-curr d-flex justify-content-end">
                            <div class="header-bottom-set dropdown">
                                <button class="dropdown-toggle header-action-btn hover-style-default color-white"
                                        data-bs-toggle="dropdown"> Settings <i class="ion-ios-arrow-down"></i></button>
                                <ul class="dropdown-menu">
                                    @if(Auth::guard('web')->check())
                                    <li><a class="dropdown-item" href="{{route('user.account')}}">My account</a></li>
                                    <li><a class="dropdown-item" href="{{route('user.orders')}}">My orders</a></li>
                                    @endif
                                        @if(Auth::guard('web')->check())
                                            <form action="{{route('logout')}}" method="post">
                                                @csrf
                                                <input type="hidden" name="type" value="user">
                                                <li><button class="dropdown-item" type="submit">Log Out</button></li>
                                            </form>
                                        @else
                                            <li><a class="dropdown-item" href="{{route('register')}}">Register</a></li>
                                            <li><a class="dropdown-item" href="{{route('login')}}">Login</a></li>
                                        @endif
                                </ul>
                            </div>
                            <!-- Single Wedge Start -->
                            {{--<div class="header-top-curr dropdown">
                                <button class="dropdown-toggle header-action-btn hover-style-default color-white" data-bs-toggle="dropdown"> <img class="me-2" src="{{asset('')}}client/assets/images/icons/1.jpg" alt="">English<i class="ion-ios-arrow-down"></i></button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li><a class="dropdown-item" href="#"><img src="{{asset('client/assets/images/icons/1.jpg')}}" alt="">English</a></li>
                                    <li><a class="dropdown-item" href="#"><img src="{{asset('client/assets/images/icons/2.jpg')}}" alt="">Français</a></li>
                                </ul>
                            </div>--}}
                            <!-- Single Wedge End -->
                            <!-- Single Wedge Start -->
                            {{--<div class="header-top-curr dropdown">
                                <button class="dropdown-toggle header-action-btn hover-style-default color-white pr-0" data-bs-toggle="dropdown">USD $
                                    <i class="ion-ios-arrow-down"></i></button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li><a class="dropdown-item" href="#">USD $</a></li>
                                    <li><a class="dropdown-item" href="#">EUR €</a></li>
                                </ul>
                            </div>--}}
                            <!-- Single Wedge End -->
                        </div>
                        <!-- Header Top Language Currency -->
                    </div>
                </div>
                <!--Right End-->
            </div>
        </div>
    </div>
    <!-- Header Top End -->
    <!-- Header Buttom Start -->
    <div class="header-navigation sticky-nav d-none d-lg-block">
        <div class="container-fluid">
            <div class="row">
                <!-- Logo Start -->
                <div class="col-md-2 col-sm-2">
                    <div class="logo">
                        <a href="{{ route('home') }}"><img src="{{ $gs->light_logo ? asset('storage/'.$gs->light_logo) : asset('client/assets/images/logo/logo.jpg') }}" alt="{{ $gs->business_name ?? '' }}" style="max-height: 50px; width: auto;"></a>
                    </div>
                </div>
                <!-- Logo End -->
                <!-- Navigation Start -->
                <div class="col-md-10 col-sm-10">
                    <!--Main Navigation Start -->
                    <div class="main-navigation">
                        <ul>
                            <li class="menu-dropdown">
                                <a href="{{route('home')}}">Home</a>
                            </li>
                            <li>
                                <a href="{{ route('client.products.index') }}">Products</a>
                            </li>
                            <li class="menu-dropdown">
                                <a href="{{ route('client.products.index') }}">Shop <i class="ion-ios-arrow-down"></i></a>
                                <ul class="mega-menu-wrap">
                                    @foreach($nav_categories as $category)
                                    <li>
                                        <ul>
                                            <li class="mega-menu-title">
                                                <a href="{{ route('client.products.index', ['category_nav' => $category->id]) }}">
                                                    @if($category->icon)
                                                        <img src="{{ asset('storage/'.$category->icon) }}" alt="" style="width: 20px; height: 20px; object-fit: contain; margin-right: 5px;">
                                                    @endif
                                                    {{ $category->name }}
                                                </a>
                                            </li>
                                            @foreach($category->subcategories as $subcategory)
                                               <li>
                                                   <a href="{{ route('client.products.index', ['category_nav' => $subcategory->id]) }}">
                                                       @if($subcategory->icon)
                                                           <img src="{{ asset('storage/'.$subcategory->icon) }}" alt="" style="width: 16px; height: 16px; object-fit: contain; margin-right: 5px;">
                                                       @endif
                                                       {{ $subcategory->name }}
                                                   </a>
                                               </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                    @endforeach
                                    <li class="banner-wrapper">
                                        <a href="{{ route('client.products.index') }}"><img
                                                src="{{asset('client/assets/images/banner-image/banner-menu.jpg')}}"
                                                alt=""></a>
                                    </li>
                                </ul>
                            </li>
                            <li class="menu-dropdown">
                                <a href="{{ Auth::guard('web')->check() ? route('user.account') : route('login') }}">Account <i class="ion-ios-arrow-down"></i></a>
                                <ul class="sub-menu">
                                    @if(Auth::guard('web')->check())
                                        <li><a href="{{ route('user.account') }}">My Account</a></li>
                                        <li><a href="{{ route('user.orders') }}">My Orders</a></li>
                                        <li><a href="{{ route('user.wishlist.index') }}">Wishlist</a></li>
                                    @else
                                        <li><a href="{{ route('login') }}">Login</a></li>
                                        <li><a href="{{ route('register') }}">Register</a></li>
                                        <li><a href="{{ route('client.track_order') }}">Track Order</a></li>
                                    @endif
                                </ul>
                            </li>
                            <li>
                                <a href="{{ route('client.track_order') }}">Track Order</a>
                            </li>
                            <li><a href="{{ route('client.contact') }}">Contact Us</a></li>
                        </ul>
                    </div>
                    <!--Main Navigation End -->
                    <!--Header Bottom Account Start -->
                    <div class="header_account_area">
                        <!--Seach Area Start -->
                        <div class="header_account_list search_list">
                            <a href="javascript:void(0)"><i class="ion-ios-search-strong"></i></a>
                            <div class="dropdown_search">
                                <form action="{{ route('client.products.index') }}" method="GET">
                                    <input name="search" value="{{ request('search') }}" placeholder="Search entire store here ..." type="text">
                                    <button type="submit"><i class="ion-ios-search-strong"></i></button>
                                </form>
                            </div>
                        </div>
                        <!--Seach Area End -->
                        <!--Contact info Start -->
                        @if($cs && $cs->phone_number)
                            <div class="contact-link">
                                <div class="phone">
                                    <p>Call us:</p>
                                    <a href="tel:{{ $cs->phone_number }}">{{ $cs->phone_number }}</a>
                                </div>
                            </div>
                        @endif
                        <!--Contact info End -->
                        <!--Cart info Start -->
                        <div class="cart-info d-flex">
                            <div class="mini-cart-warp">
                                <a href="#offcanvas-cart" class="count-cart color-black offcanvas-toggle">
                                    <span class="amount-tag">${{ number_format(\App\HelperClass::getCartItems()->sum('subtotal'), 2) }}</span>
                                    <span class="item-quantity-tag">{{ sprintf('%02d', \App\HelperClass::cartCount()) }}</span>
                                </a>
                            </div>
                        </div>
                        <!--Cart info End -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Header Bottom Account End -->
    <!-- Header mobile area start -->
    <div class="header-bottom d-lg-none sticky-nav py-3 mobile-navigation">
        <div class="container-fluid">
            <div class="row justify-content-between align-items-center">
                <div class="col-6 col-sm-6">
                    <div class="logo m-0 p-0 text-start">
                        <a href="{{ route('home') }}" class="d-inline-block"><img src="{{ $gs->light_logo ? asset('storage/'.$gs->light_logo) : asset('client/assets/images/logo/logo.jpg') }}" alt="{{ $gs->business_name ?? '' }}" style="max-height: 40px; width: auto; display: block;"></a>
                    </div>
                </div>
                <div class="col-6 col-sm-6">
                    <!--Cart info Start -->
                    <div class="cart-info d-flex m-0 justify-content-end align-items-center">
                        <div class="header-bottom-set dropdown me-3">
                            <button class="dropdown-toggle border-0 bg-transparent p-0 header-action-btn hover-style-default"
                                    data-bs-toggle="dropdown" style="font-size: 20px;"><i class="ion-person"></i></button>
                            <ul class="dropdown-menu">
                                @if(Auth::guard('web')->check())
                                    <li><a class="dropdown-item" href="{{route('user.account')}}">My account</a></li>
                                    <li><a class="dropdown-item" href="{{route('user.orders')}}">My orders</a></li>
                                    <li><a class="dropdown-item" href="{{ route('user.wishlist.index') }}">Wishlist</a></li>
                                    <form action="{{route('logout')}}" method="post">
                                        @csrf
                                        <input type="hidden" name="type" value="user">
                                        <li><button class="dropdown-item" type="submit">Log Out</button></li>
                                    </form>
                                @else
                                    <li><a class="dropdown-item" href="{{ route('client.track_order') }}">Track Order</a></li>
                                    <li><a class="dropdown-item" href="{{ route('login') }}">Sign in</a></li>
                                    <li><a class="dropdown-item" href="{{ route('register') }}">Register</a></li>
                                @endif
                            </ul>
                        </div>
                        <div class="mini-cart-warp me-3">
                            <a href="#offcanvas-cart" class="count-cart color-black offcanvas-toggle">
                                <span class="item-quantity-tag">{{ sprintf('%02d', \App\HelperClass::cartCount()) }}</span>
                            </a>
                        </div>
                        <a href="#offcanvas-mobile-menu" class="offcanvas-toggle mobile-menu">
                            <i class="ion-navicon" style="font-size: 30px;"></i>
                        </a>
                    </div>
                    <!--Cart info End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Header mobile area end -->
</header>
<!-- Header End -->
<div class="mobile-search-option pb-3 d-lg-none hover-style-default">
    <div class="container-fluid">
        <div class="header-account-list">
            <div class="dropdown-search">
                <form action="{{ route('client.products.index') }}" method="GET">
                    <input name="search" value="{{ request('search') }}" placeholder="Search entire store here ..." type="text">
                    <button type="submit"><i class="ion-ios-search-strong"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>


{{--Off canvas cart--}}
<!-- offcanvas overlay start -->
<div class="offcanvas-overlay"></div>
<!-- offcanvas overlay end -->
<!-- OffCanvas Cart Start -->
<div id="offcanvas-cart" class="offcanvas offcanvas-cart hover-style-default">
    @include('client.structure.mini-cart')
</div>
<!-- OffCanvas Cart End -->
<!-- OffCanvas Menu Start -->
<div id="offcanvas-mobile-menu" class="offcanvas offcanvas-mobile-menu hover-style-default">
    <button class="offcanvas-close"></button>
    <!-- contact Info -->
    @if($cs && $cs->phone_number)
        <div class="contact-info d-flex align-items-center justify-content-center color-black py-3">
            <img class="me-3" src="{{asset('client/assets/images/icons/mobile-contact.png')}}" alt="">
            <p>Call us:</p>
            <a class="color-black" href="tel:{{ $cs->phone_number }}">{{ $cs->phone_number }}</a>
        </div>
    @endif
    <!-- offcanvas wishlist -->
    <div class="user-panel">
        <ul class="d-flex justify-content-center">
            <li>
                <a href="{{ route('user.wishlist.index') }}"><i class="ion-android-favorite-outline"></i>Wishlist ({{ \App\HelperClass::wishlistCount() }})</a>
            </li>
        </ul>
    </div>
    <div class="menu-close">
        menu
    </div>
    <!-- offcanvas menu -->
    <div class="inner customScroll">
        <div class="offcanvas-menu mb-4">
            <ul>
                <li><a href="{{ route('home') }}"><span class="menu-text">Home</span></a></li>
                <li><a href="{{ route('client.track_order') }}"><span class="menu-text">Track Order</span></a></li>
                <li><a href="{{ route('client.products.index') }}"><span class="menu-text">Products</span></a></li>
                <li><a href="{{ route('client.products.index') }}"><span class="menu-text">Shop</span></a>
                    <ul class="sub-menu">
                        @foreach($nav_categories as $category)
                        <li>
                            <a href="{{ route('client.products.index', ['category_nav' => $category->id]) }}">
                                @if($category->icon)
                                    <img src="{{ asset('storage/'.$category->icon) }}" alt="" style="width: 16px; height: 16px; object-fit: contain; margin-right: 5px;">
                                @endif
                                <span class="menu-text">{{ $category->name }}</span>
                            </a>
                            @if($category->subcategories->count() > 0)
                            <ul class="sub-menu">
                                @foreach($category->subcategories as $subcategory)
                                <li>
                                    <a href="{{ route('client.products.index', ['category_nav' => $subcategory->id]) }}">
                                        @if($subcategory->icon)
                                            <img src="{{ asset('storage/'.$subcategory->icon) }}" alt="" style="width: 14px; height: 14px; object-fit: contain; margin-right: 5px;">
                                        @endif
                                        {{ $subcategory->name }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </li>
                <li class="menu-dropdown">
                    <a href="{{ Auth::guard('web')->check() ? route('user.account') : route('login') }}">Account</a>
                    <ul class="sub-menu">
                        @if(Auth::guard('web')->check())
                            <li><a href="{{ route('user.account') }}">My Account</a></li>
                            <li><a href="{{ route('user.orders') }}">My Orders</a></li>
                            <li><a href="{{ route('user.wishlist.index') }}">Wishlist</a></li>
                        @else
                            <li><a href="{{ route('login') }}">Login</a></li>
                            <li><a href="{{ route('register') }}">Register</a></li>
                            <li><a href="{{ route('client.track_order') }}">Track Order</a></li>
                        @endif
                    </ul>
                </li>
                <li><a href="{{ route('client.contact') }}">Contact Us</a></li>
            </ul>
        </div>
        <!-- OffCanvas Menu End -->
        <div class="offcanvas-social mt-5">
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
<!-- OffCanvas Menu End -->
