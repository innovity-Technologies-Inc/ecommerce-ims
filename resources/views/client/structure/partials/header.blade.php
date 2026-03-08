<!-- Header Start -->
@php
    $gs = \App\HelperClass::generalSettings();
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
                            <li class="menu-dropdown">                                <a href="#">Shop <i class="ion-ios-arrow-down"></i></a>
                                <ul class="mega-menu-wrap">
                                    @foreach($nav_categories as $category)
                                    <li>
                                        <ul>
                                            <li class="mega-menu-title">
                                                <a href="{{ route('client.products.index', ['category' => $category->id]) }}">
                                                    @if($category->icon)
                                                        <img src="{{ asset('storage/'.$category->icon) }}" alt="" style="width: 20px; height: 20px; object-fit: contain; margin-right: 5px;">
                                                    @endif
                                                    {{ $category->name }}
                                                </a>
                                            </li>
                                            @foreach($category->subcategories as $subcategory)
                                               <li>
                                                   <a href="{{ route('client.products.index', ['category' => $subcategory->id]) }}">
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
                                    <li class="banner-wrapper">                                        <a href="#"><img
                                                src="{{asset('client/assets/images/banner-image/banner-menu.jpg')}}"
                                                alt=""></a>
                                    </li>
                                </ul>
                            </li>                            <li class="menu-dropdown">
                                <a href="#">Pages <i class="ion-ios-arrow-down"></i></a>
                                <ul class="sub-menu">
                                    <li><a href="about.html">About Page</a></li>
                                    <li><a href="cart.html">Cart Page</a></li>
                                    <li><a href="checkout.html">Checkout Page</a></li>
                                    <li><a href="compare.html">Compare Page</a></li>
                                    <li><a href="login.html">Login & Regiter Page</a></li>
                                    <li><a href="{{route('user.account')}}">Account Page</a></li>
                                    <li><a href="wishlist.html">Wishlist Page</a></li>
                                </ul>
                            </li>
                            <li class="menu-dropdown">
                                <a href="#">Blog <i class="ion-ios-arrow-down"></i></a>
                                <ul class="sub-menu">
                                    <li class="menu-dropdown position-static">
                                        <a href="#">Blog Grid <i class="ion-ios-arrow-down"></i></a>
                                        <ul class="sub-menu sub-menu-2">
                                            <li><a href="blog-grid-left-sidebar.html">Blog Grid Left Sidebar</a></li>
                                            <li><a href="blog-grid-right-sidebar.html">Blog Grid Right Sidebar</a></li>
                                        </ul>
                                    </li>
                                    <li class="menu-dropdown position-static">
                                        <a href="#">Blog List <i class="ion-ios-arrow-down"></i></a>
                                        <ul class="sub-menu sub-menu-2">
                                            <li><a href="blog-list-left-sidebar.html">Blog List Left Sidebar</a></li>
                                            <li><a href="blog-list-right-sidebar.html">Blog List Right Sidebar</a></li>
                                        </ul>
                                    </li>
                                    <li class="menu-dropdown position-static">
                                        <a href="#">Blog Single <i class="ion-ios-arrow-down"></i></a>
                                        <ul class="sub-menu sub-menu-2">
                                            <li><a href="blog-single-left-sidebar.html">Blog Single Left Sidebar</a>
                                            </li>
                                            <li><a href="blog-single-right-sidebar.html">Blog Single Right Sidebar</a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li><a href="contact.html">Contact Us</a></li>
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
                        <div class="contact-link">
                            <div class="phone">
                                <p>Call us:</p>
                                <a href="tel:(+800)345678">(+800)345678</a>
                            </div>
                        </div>
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
                                    <li><a class="dropdown-item" href="{{ route('user.wishlist.index') }}">Wishlist</a></li>
                                    <form action="{{route('logout')}}" method="post">
                                        @csrf
                                        <input type="hidden" name="type" value="user">
                                        <li><button class="dropdown-item" type="submit">Log Out</button></li>
                                    </form>
                                @else
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
    <div class="contact-info d-flex align-items-center justify-content-center color-black py-3">
        <img class="me-3" src="{{asset('client/assets/images/icons/mobile-contact.png')}}" alt="">
        <p>Call us:</p>
        <a class="color-black" href="tel:(+800)345678">(+800)345678</a>
    </div>
    <!-- offcanvas wishlist -->
    <div class="user-panel">
        <ul class="d-flex justify-content-center">
            <li>
                <a href="{{ route('user.wishlist.index') }}"><i class="ion-android-favorite-outline"></i>Wishlist ({{ \App\HelperClass::wishlistCount() }})</a>
            </li>
        </ul>
    </div>
    <!-- offcanvas currency -->
    <div class="offcanvas-userpanel">
        <ul>
            <li class="offcanvas-userpanel__role">
                <a href="#">USD $ <i class="ion-ios-arrow-down"></i></a>
                <ul class="user-sub-menu">
                    <li><a class="current" href="#">USD $</a></li>
                    <li><a href="#">EUR €</a></li>
                </ul>
            </li>
        </ul>
    </div>
    <!-- offcanvas language -->
    <div class="offcanvas-userpanel">
        <ul>
            <li class="offcanvas-userpanel__role">
                <a href="#"><img src="{{asset('client/assets/images/icons/1.jpg')}}" alt="">English <i
                        class="ion-ios-arrow-down"></i></a>
                <ul class="user-sub-menu">
                    <li><a class="current" href="#"><img src="{{asset('client/assets/images/icons/1.jpg')}}" alt="">English</a>
                    </li>
                    <li><a href="#"><img src="{{asset('client/assets/images/icons/2.jpg')}}" alt=""> Français</a></li>
                </ul>
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
                <li><a href="{{ route('client.products.index') }}"><span class="menu-text">Products</span></a></li>
                <li><a href="{{ route('client.products.index') }}"><span class="menu-text">Shop</span></a>
                    <ul class="sub-menu">
                        @foreach($nav_categories as $category)
                        <li>
                            <a href="{{ route('client.products.index', ['category' => $category->id]) }}">
                                @if($category->icon)
                                    <img src="{{ asset('storage/'.$category->icon) }}" alt="" style="width: 16px; height: 16px; object-fit: contain; margin-right: 5px;">
                                @endif
                                <span class="menu-text">{{ $category->name }}</span>
                            </a>
                            @if($category->subcategories->count() > 0)
                            <ul class="sub-menu">
                                @foreach($category->subcategories as $subcategory)
                                <li>
                                    <a href="{{ route('client.products.index', ['category' => $subcategory->id]) }}">
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
                    <a href="#">Pages</a>
                    <ul class="sub-menu">
                        <li><a href="about.html">About Page</a></li>
                        <li><a href="cart.html">Cart Page</a></li>
                        <li><a href="checkout.html">Checkout Page</a></li>
                        @if(Auth::guard('web')->check())
                            <li><a href="{{ route('user.wishlist.index') }}">Wishlist Page</a></li>
                            <li><a href="{{route('user.account')}}">Account Page</a></li>
                        @else
                            <li><a href="{{ route('login') }}">Login Page</a></li>
                            <li><a href="{{ route('register') }}">Register Page</a></li>
                        @endif
                    </ul>
                </li>
                <li><a href="contact.html">Contact Us</a></li>
            </ul>
        </div>
        <!-- OffCanvas Menu End -->
        <div class="offcanvas-social mt-5">
            <ul>
                <li>
                    <a href="#"><i class="ion-social-facebook"></i></a>
                </li>
                <li>
                    <a href="#"><i class="ion-social-twitter"></i></a>
                </li>
                <li>
                    <a href="#"><i class="ion-social-google"></i></a>
                </li>
                <li>
                    <a href="#"><i class="ion-social-youtube"></i></a>
                </li>
                <li>
                    <a href="#"><i class="ion-social-instagram"></i></a>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- OffCanvas Menu End -->
