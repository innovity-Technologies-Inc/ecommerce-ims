<!-- Header Start -->
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
                            @endif to Smart Ecom!</p>
                    </div>
                </div>
                <!--Left End-->
                <!--Right Start-->
                <div class="col-8 d-lg-block d-none">
                    <div class="header-right-nav hover-style-default">
                        <ul>
                            {{--<li>
                                <a href="compare.html"><i class="ion-ios-shuffle-strong"></i>Compare (0)</a>
                            </li>--}}
                            <li class="border-color-white">
                                <a href="wishlist.html"><i class="ion-android-favorite-outline"></i>Wishlist (0)</a>
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
                        <a href="index.html"><img src="{{asset('client/assets/images/logo/logo.jpg')}}" alt=""></a>
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
                            <li class="menu-dropdown">
                                <a href="#">Shop <i class="ion-ios-arrow-down"></i></a>
                                <ul class="mega-menu-wrap">
                                    <li>
                                        <ul>
                                            <li class="mega-menu-title"><a href="#">Shop Grid</a></li>
                                            <li><a href="shop-3-column.html">Shop Grid 3 Column</a></li>
                                            <li><a href="shop-4-column.html">Shop Grid 4 Column</a></li>
                                            <li><a href="shop-left-sidebar.html">Shop Grid Left Sidebar</a></li>
                                            <li><a href="shop-right-sidebar.html">Shop Grid Right Sidebar</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <ul>
                                            <li class="mega-menu-title"><a href="#">Shop List</a></li>
                                            <li><a href="shop-list.html">Shop List</a></li>
                                            <li><a href="shop-list-left-sidebar.html">Shop List Left Sidebar</a></li>
                                            <li><a href="shop-list-right-sidebar.html">Shop List Right Sidebar</a></li>
                                            <li><a href="shop-filter.html">Shop Filter Page</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <ul>
                                            <li class="mega-menu-title"><a href="#">Shop Single</a></li>
                                            <li><a href="single-product.html">Shop Single</a></li>
                                            <li><a href="single-product-variable.html">Shop Variable</a></li>
                                            <li><a href="single-product-affiliate.html">Shop Affiliate</a></li>
                                            <li><a href="single-product-group.html">Shop Group</a></li>
                                            <li><a href="single-product-tabstyle-2.html">Shop Tab 2</a></li>
                                            <li><a href="single-product-tabstyle-3.html">Shop Tab 3</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <ul>
                                            <li class="mega-menu-title"><a href="#">Shop Single</a></li>
                                            <li><a href="single-product-slider.html">Shop Slider</a></li>
                                            <li><a href="single-product-gallery-left.html">Shop Gallery Left</a></li>
                                            <li><a href="single-product-gallery-right.html">Shop Gallery Right</a></li>
                                            <li><a href="single-product-sticky-left.html">Shop Sticky Left</a></li>
                                            <li><a href="single-product-sticky-right.html">Shop Sticky Right</a></li>
                                        </ul>
                                    </li>
                                    <li class="banner-wrapper">
                                        <a href="single-product.html"><img
                                                src="{{asset('client/assets/images/banner-image/banner-menu.jpg')}}"
                                                alt=""></a>
                                    </li>
                                </ul>
                            </li>
                            <li class="menu-dropdown">
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
                                <form action="#">
                                    <input placeholder="Search entire store here ..." type="text">
                                    <div class="search-category">
                                        <select class="bootstrap-select" name="poscats">
                                            <option value="0">All categories</option>
                                            <option value="104">
                                                Fresh Food
                                            </option>
                                            <option value="183">
                                                - - Fresh Fruit
                                            </option>
                                            <option value="187">
                                                - - - - Bananas
                                            </option>
                                            <option value="188">
                                                - - - - Apples &amp; Pears
                                            </option>
                                            <option value="189">
                                                - - - - Berries &amp; Cherries
                                            </option>
                                            <option value="190">
                                                - - - - Oranges &amp; Citrus Fruit
                                            </option>
                                            <option value="191">
                                                - - - - Grapes
                                            </option>
                                            <option value="184">
                                                - - Fresh Vegetables
                                            </option>
                                            <option value="192">
                                                - - - - Potatoes &amp; Sweet Potatoes
                                            </option>
                                            <option value="193">
                                                - - - - Onions &amp; Leeks
                                            </option>
                                            <option value="194">
                                                - - - - Carrots &amp; Root Vegetables
                                            </option>
                                            <option value="195">
                                                - - - - Broccoli &amp; Cauliflower
                                            </option>
                                            <option value="196">
                                                - - - - Cabbages &amp; Greens
                                            </option>
                                            <option value="185">
                                                - - Fresh Salad &amp; Dips
                                            </option>
                                            <option value="197">
                                                - - - - Lettuce &amp; Salad bags
                                            </option>
                                            <option value="198">
                                                - - - - Tomatoes
                                            </option>
                                            <option value="199">
                                                - - - - Cucumber
                                            </option>
                                            <option value="200">
                                                - - - - Celery
                                            </option>
                                            <option value="201">
                                                - - - - Peppers
                                            </option>
                                            <option value="186">
                                                - - Milk, Butter &amp; Eggs
                                            </option>
                                            <option value="202">
                                                - - - - Milk
                                            </option>
                                            <option value="203">
                                                - - - - Spreads &amp; Margarine
                                            </option>
                                            <option value="204">
                                                - - - - Fresh Cream &amp; Custard
                                            </option>
                                            <option value="205">
                                                - - - - Eggs
                                            </option>
                                            <option value="206">
                                                - - - - Baking &amp; Cooking
                                            </option>
                                        </select>
                                    </div>
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
                                    <span class="amount-tag">$20.00</span>
                                    <span class="item-quantity-tag">02</span>
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
                <div class="col-md-3 col-sm-3">
                    <a href="#offcanvas-mobile-menu" class="offcanvas-toggle mobile-menu">
                        <i class="ion-navicon"></i>
                    </a>
                </div>
                <div class="col-md-6 col-sm-4 d-flex justify-content-center">
                    <div class="logo m-0">
                        <a href="index.html"><img src="{{asset('client/assets/images/logo/logo.jpg')}}" alt=""></a>
                    </div>
                </div>
                <div class="col-md-3 col-sm-5">
                    <!--Cart info Start -->
                    <div class="cart-info d-flex m-0 justify-content-end">
                        <div class="header-bottom-set dropdown">
                            <button class="dropdown-toggle border-0 header-action-btn hover-style-default"
                                    data-bs-toggle="dropdown"><i class="ion-person"></i></button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{route('user.account')}}">My account</a></li>
                                <li><a class="dropdown-item" href="checkout.html">Checkout</a></li>
                                <li><a class="dropdown-item" href="login.html">Sign in</a></li>
                            </ul>
                        </div>
                        <div class="mini-cart-warp">
                            <a href="#offcanvas-cart" class="count-cart color-black offcanvas-toggle">
                                <span class="amount-tag">$20.00</span>
                                <span class="item-quantity-tag">02</span>
                            </a>
                        </div>
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
                <form action="#">
                    <input placeholder="Search entire store here ..." type="text">
                    <div class="search-category">
                        <select class="bootstrap-select" name="poscats">
                            <option value="0">All categories</option>
                            <option value="104">
                                Fresh Food
                            </option>
                            <option value="183">
                                - - Fresh Fruit
                            </option>
                            <option value="187">
                                - - - - Bananas
                            </option>
                            <option value="188">
                                - - - - Apples &amp; Pears
                            </option>
                            <option value="189">
                                - - - - Berries &amp; Cherries
                            </option>
                            <option value="190">
                                - - - - Oranges &amp; Citrus Fruit
                            </option>
                            <option value="191">
                                - - - - Grapes
                            </option>
                            <option value="184">
                                - - Fresh Vegetables
                            </option>
                            <option value="192">
                                - - - - Potatoes &amp; Sweet Potatoes
                            </option>
                            <option value="193">
                                - - - - Onions &amp; Leeks
                            </option>
                            <option value="194">
                                - - - - Carrots &amp; Root Vegetables
                            </option>
                            <option value="195">
                                - - - - Broccoli &amp; Cauliflower
                            </option>
                            <option value="196">
                                - - - - Cabbages &amp; Greens
                            </option>
                            <option value="185">
                                - - Fresh Salad &amp; Dips
                            </option>
                            <option value="197">
                                - - - - Lettuce &amp; Salad bags
                            </option>
                            <option value="198">
                                - - - - Tomatoes
                            </option>
                            <option value="199">
                                - - - - Cucumber
                            </option>
                            <option value="200">
                                - - - - Celery
                            </option>
                            <option value="201">
                                - - - - Peppers
                            </option>
                            <option value="186">
                                - - Milk, Butter &amp; Eggs
                            </option>
                            <option value="202">
                                - - - - Milk
                            </option>
                            <option value="203">
                                - - - - Spreads &amp; Margarine
                            </option>
                            <option value="204">
                                - - - - Fresh Cream &amp; Custard
                            </option>
                            <option value="205">
                                - - - - Eggs
                            </option>
                            <option value="206">
                                - - - - Baking &amp; Cooking
                            </option>
                        </select>
                    </div>
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
    <div class="inner">
        <div class="head">
            <span class="title">Cart</span>
            <button class="offcanvas-close">×</button>
        </div>
        <div class="body customScroll">
            <ul class="minicart-product-list">
                <li>
                    <a href="single-product.html" class="image"><img
                            src="{{asset('client/assets/images/product-image/mini-cart/1.jpg')}}"
                            alt="Cart product Image"></a>
                    <div class="content">
                        <a href="single-product.html" class="title">Juicy Couture...</a>
                        <span class="quantity-price">1 x <span class="amount">$18.86</span></span>
                        <a href="#" class="remove">×</a>
                    </div>
                </li>
                <li>
                    <a href="single-product.html" class="image"><img
                            src="{{asset('client/assets/images/product-image/mini-cart/2.jpg')}}"
                            alt="Cart product Image"></a>
                    <div class="content">
                        <a href="single-product.html" class="title">Water and Wind...</a>
                        <span class="quantity-price">1 x <span class="amount">$43.28</span></span>
                        <a href="#" class="remove">×</a>
                    </div>
                </li>
                <li>
                    <a href="single-product.html" class="image"><img
                            src="{{asset('client/assets/images/product-image/mini-cart/3.jpg')}}"
                            alt="Cart product Image"></a>
                    <div class="content">
                        <a href="single-product.html" class="title">Fila Locker Room...</a>
                        <span class="quantity-price">1 x <span class="amount">$37.34</span></span>
                        <a href="#" class="remove">×</a>
                    </div>
                </li>
            </ul>
        </div>
        <div class="shopping-cart-total">
            <h4>Subtotal : <span>$20.00</span></h4>
            <h4>Shipping : <span>$7.00</span></h4>
            <h4>Taxes : <span>$0.00</span></h4>
            <h4 class="shop-total">Total : <span>$27.00</span></h4>
        </div>
        <div class="foot">
            <div class="buttons">
                <a href="cart.html" class="btn btn-dark btn-hover-primary mb-30px">view cart</a>
                <a href="checkout.html" class="btn btn-outline-dark current-btn">checkout</a>
            </div>
        </div>
    </div>
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
    <!-- offcanvas compare & wishlist -->
    <div class="user-panel">
        <ul class="d-flex justify-content-between">
            <li class="m-0">
                <a href="compare.html"><i class="ion-ios-shuffle-strong"></i>Compare (0)</a>
            </li>
            <li>
                <a href="wishlist.html"><i class="ion-android-favorite-outline"></i>Wishlist (0)</a>
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
                <li><a href="#"><span class="menu-text">Home</span></a>
                    <ul class="sub-menu">
                        <li><a href="#"><span class="menu-text">Home Organic</span></a>
                            <ul class="sub-menu">
                                <li><a href="index.html">Organic 1</a></li>
                                <li><a href="index-2.html">Organic 2</a></li>
                                <li><a href="index-3.html">Organic 3</a></li>
                                <li><a href="index-4.html">Organic 4</a></li>
                            </ul>
                        </li>
                        <li><a href="#"><span class="menu-text">Home Cosmetic</span></a>
                            <ul class="sub-menu">
                                <li><a href="index-5.html">Cosmetic 1</a></li>
                                <li><a href="index-6.html">Cosmetic 2</a></li>
                                <li><a href="index-7.html">Cosmetic 3</a></li>
                                <li><a href="index-8.html">Cosmetic 4</a></li>
                            </ul>
                        </li>
                        <li><a href="#"><span class="menu-text">Home Digital</span></a>
                            <ul class="sub-menu">
                                <li><a href="index-9.html">Digital 1</a></li>
                                <li><a href="index-10.html">Digital 2</a></li>
                                <li><a href="index-11.html">Digital 3</a></li>
                                <li><a href="index-12.html">Digital 4</a></li>
                            </ul>
                        </li>
                        <li><a href="#"><span class="menu-text">Home Furniture</span></a>
                            <ul class="sub-menu">
                                <li><a href="index-13.html">Furniture 1</a></li>
                                <li><a href="index-14.html">Furniture 2</a></li>
                                <li><a href="index-15.html">Furniture 3</a></li>
                                <li><a href="index-16.html">Furniture 4</a></li>
                            </ul>
                        </li>
                        <li><a href="#"><span class="menu-text">Home Medical</span></a>
                            <ul class="sub-menu">
                                <li><a href="index-17.html">Medical 1</a></li>
                                <li><a href="index-18.html">Medical 2</a></li>
                                <li><a href="index-19.html">Medical 3</a></li>
                                <li><a href="index-20.html">Medical 4</a></li>
                            </ul>
                        </li>
                        <li class="menu-dropdown position-relative">
                            <a href="index-21.html">Single Product</a>
                            <span class="stekar">new</span>
                        </li>
                    </ul>
                </li>
                <li class="menu-dropdown">
                    <a href="#">Pages</a>
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
                <li><a href="#"><span class="menu-text">Shop</span></a>
                    <ul class="sub-menu">
                        <li>
                            <a href="#"><span class="menu-text">Shop Page</span></a>
                            <ul class="sub-menu">
                                <li><a href="shop-3-column.html">Shop Grid 3 Column</a></li>
                                <li><a href="shop-4-column.html">Shop Grid 4 Column</a></li>
                                <li><a href="shop-left-sidebar.html">Shop Grid Left Sidebar</a>
                                </li>
                                <li><a href="shop-right-sidebar.html">Shop Grid Right
                                        Sidebar</a></li>
                                <li><a href="shop-list.html">Shop List</a></li>
                                <li><a href="shop-list-left-sidebar.html">Shop List Left
                                        Sidebar</a></li>
                                <li><a href="shop-list-right-sidebar.html">Shop List Right
                                        Sidebar</a></li>
                                <li><a href="shop-filter.html">Shop Filter Page</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#"><span class="menu-text">product Details Page</span></a>
                            <ul class="sub-menu">
                                <li><a href="single-product.html">Shop Single</a></li>
                                <li><a href="single-product-variable.html">Shop Variable</a>
                                </li>
                                <li><a href="single-product-affiliate.html">Shop Affiliate</a>
                                </li>
                                <li><a href="single-product-group.html">Shop Group</a></li>
                                <li><a href="single-product-tabstyle-2.html">Shop Tab 2</a></li>
                                <li><a href="single-product-tabstyle-3.html">Shop Tab 3</a></li>
                                <li><a href="single-product-slider.html">Shop Slider</a></li>
                                <li><a href="single-product-gallery-left.html">Shop Gallery
                                        Left</a></li>
                                <li><a href="single-product-gallery-right.html">Shop Gallery
                                        Right</a></li>
                                <li><a href="single-product-sticky-left.html">Shop Sticky
                                        Left</a></li>
                                <li><a href="single-product-sticky-right.html">Shop Sticky
                                        Right</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li><a href="#"><span class="menu-text">Blog</span></a>
                    <ul class="sub-menu">
                        <li><a href="blog-grid-left-sidebar.html">Grid Left Sidebar</a></li>
                        <li><a href="blog-grid-right-sidebar.html">Grid Right Sidebar</a></li>
                        <li><a href="blog-list-left-sidebar.html">List Left Sidebar</a></li>
                        <li><a href="blog-list-right-sidebar.html">List Right Sidebar</a></li>
                        <li><a href="blog-single-left-sidebar.html">Single Left Sidebar</a></li>
                        <li><a href="blog-single-right-sidebar.html">Single Right Sidbar</a>
                        </li>
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
