@extends('client.structure.app')
@section('content')

    <style>
        .shop-top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            background: #fff;
            border: 1px solid #ebebeb;
            margin-bottom: 30px;
        }
        .select-shoing-wrap {
            display: flex;
            align-items: center;
            flex-wrap: nowrap;
        }
        .shot-product p {
            white-space: nowrap;
            margin-bottom: 0;
            margin-right: 10px;
        }
        @media (max-width: 767px) {
            .shop-top-bar {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }
    </style>

    <div class="shop-category-area">
        <div class="container">
            <form id="filter-form" action="{{ route('client.products.index') }}" method="GET">
                {{-- Preserve search and category from navbar if present --}}
                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                @if(request('category') && !is_array(request('category'))) <input type="hidden" name="category_nav" value="{{ request('category') }}"> @endif

                <div class="row">
                    <div class="col-lg-9 order-lg-last col-md-12 order-md-first">
                        <!-- Shop Top Area Start -->
                        <div class="shop-top-bar">
                            <!-- Left Side start -->
                            <div class="shop-tab nav">
                                <a class="active" href="#shop-1" data-bs-toggle="tab">
                                    <i class="fa fa-th show_grid"></i>
                                </a>
                                <a href="#shop-2" data-bs-toggle="tab">
                                    <i class="fa fa-list-ul"></i>
                                </a>
                                <p>Showing <span class="fw-semibold">{{ $products->firstItem() ?? 0 }}</span> to <span class="fw-semibold">{{ $products->lastItem() ?? 0 }}</span> of <span class="fw-semibold">{{ $products->total() }}</span> Results</p>
                            </div>
                            <!-- Left Side End -->
                            <!-- Right Side Start -->
                            <div class="select-shoing-wrap">
                                <div class="shot-product">
                                    <p>Sort By:</p>
                                </div>
                                <div class="shop-select">
                                    <select class="nice-select" name="sort" id="sort-select">
                                        <option value="newness" {{ request('sort') == 'newness' ? 'selected' : '' }}>Sort by newness</option>
                                        <option value="price-low" {{ request('sort') == 'price-low' ? 'selected' : '' }}>Price: Low to High</option>
                                        <option value="price-high" {{ request('sort') == 'price-high' ? 'selected' : '' }}>Price: High to Low</option>
                                        <option value="a-z" {{ request('sort') == 'a-z' ? 'selected' : '' }}>A to Z</option>
                                        <option value="z-a" {{ request('sort') == 'z-a' ? 'selected' : '' }}>Z to A</option>
                                        <option value="in-stock" {{ request('sort') == 'in-stock' ? 'selected' : '' }}>In stock</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Right Side End -->
                        </div>
                        <!-- Shop Top Area End -->

                        <!-- Shop Bottom Area Start -->
                        <div class="shop-bottom-area mt-35">
                            <!-- Shop Tab Content Start -->
                            <div class="tab-content jump">
                                <!-- Tab One Start -->
                                <div id="shop-1" class="tab-pane fade show active">
                                    <div class="row gy-4">
                                        @forelse($products as $product)
                                        <div class="col-xl-3 col-md-6 col-lg-4 col-sm-6 col-xs-12">
                                            @include('client.partials.product_card', ['product' => $product])
                                        </div>
                                        @empty
                                            <div class="col-12 text-center py-5">
                                                <h3>No Products Found</h3>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                                <!-- Tab One End -->
                                <!-- Tab Two Start -->
                                <div id="shop-2" class="tab-pane fade">
                                    @foreach($products as $product)
                                    <div class="shop-list-wrap mb-30px scroll-zoom">
                                        <div class="row list-product m-0px">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                                        <div class="left-img">
                                                            <div class="img-block">
                                                                <a href="{{ route('client.products.details', $product->slug) }}" class="thumbnail">
                                                                    @if($product->primaryImage)
                                                                        <img class="first-img" src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name }}">
                                                                        @php
                                                                            $secondImage = $product->images->where('is_primary', false)->first();
                                                                        @endphp
                                                                        @if($secondImage)
                                                                            <img class="second-img" src="{{ asset('storage/' . $secondImage->image_path) }}" alt="{{ $product->name }}">
                                                                        @else
                                                                            <img class="second-img" src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name }}">
                                                                        @endif
                                                                    @else
                                                                        <img class="first-img" src="{{ asset('client/assets/images/product-image/organic/product-1.jpg') }}" alt="">
                                                                    @endif
                                                                </a>
                                                            </div>
                                                            <ul class="product-flag">
                                                                @php
                                                                    $maxDiscount = $product->variants->max('discount_percentage');
                                                                @endphp
                                                                @if(!$product->status)
                                                                    <li class="new bg-danger" style="background-color: #dc3545 !important;">Discontinued</li>
                                                                @elseif($maxDiscount && $maxDiscount > 0)
                                                                    <li class="new bg-danger" style="background-color: #ff4545 !important;">-{{ $maxDiscount }}%</li>
                                                                @elseif($product->is_new_arrival)
                                                                    <li class="new">New</li>
                                                                @endif
                                                            </ul>
                                                            </div>
                                                            </div>
                                                            <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
                                                            <div class="product-desc-wrap">
                                                            <div class="product-decs">
                                                                <a class="inner-link" href="#"><span>{{ $product->brand->name ?? 'BRAND' }}</span></a>
                                                                <h2><a href="{{ route('client.products.details', $product->slug) }}" class="product-link">{{ $product->name }}</a></h2>
                                                                <div class="rating-product">
                                                                    <i class="ion-android-star"></i>
                                                                    <i class="ion-android-star"></i>
                                                                    <i class="ion-android-star"></i>
                                                                    <i class="ion-android-star"></i>
                                                                    <i class="ion-android-star"></i>
                                                                </div>
                                                                <div class="pricing-meta">
                                                                    <ul>
                                                                        @php
                                                                            $gs = \App\HelperClass::generalSettings();
                                                                            $priceData = \App\HelperClass::getProductPriceRange($product);
                                                                        @endphp
                                                                        <li class="current-price">
                                                                            {{ $gs->currency ?? '$' }}{{ number_format($priceData['min'], 2) }}
                                                                            @if($priceData['has_range'])
                                                                                - {{ $gs->currency ?? '$' }}{{ number_format($priceData['max'], 2) }}
                                                                            @endif
                                                                        </li>
                                                                        @if($priceData['has_discount'] && $priceData['min_regular_price'] > $priceData['min'])
                                                                            <li class="old-price">{{ $gs->currency ?? '$' }}{{ number_format($priceData['min_regular_price'], 2) }}</li>
                                                                        @endif
                                                                    </ul>
                                                                </div>                                                                <div class="product-intro-info">
                                                                    {!! Str::limit(strip_tags($product->description), 200) !!}
                                                                </div>
                                                                <div class="in-stock">Availability: <span>{{ $product->variants->sum('stock') ?? 0 }} In Stock</span></div>
                                                            </div>
                                                            <div class="add-to-link">
                                                                <ul>
                                                                    @if(!$product->status)
                                                                        <li class="cart"><a class="cart-btn" href="{{ route('client.products.details', $product->slug) }}">VIEW DETAILS</a></li>
                                                                    @elseif($product->variants->count() > 0)
                                                                        <li class="cart"><a class="cart-btn" href="{{ route('client.products.details', $product->slug) }}">SELECT OPTIONS</a></li>
                                                                    @else
                                                                        <li class="cart"><a class="cart-btn add-to-cart-btn" href="javascript:void(0)" data-product-id="{{ $product->id }}" data-quantity="1">ADD TO CART</a></li>
                                                                    @endif
                                                                    @if(Auth::guard('web')->check())
                                                                    <li>
                                                                        <a href="javascript:void(0)" onclick="addToWishlist({{ $product->id }})"><i class="ion-android-favorite-outline"></i></a>
                                                                    </li>
                                                                    @endif
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <!-- Tab Two End -->
                            </div>
                            <!-- Shop Tab Content End -->
                            <!--  Pagination Area Start -->
                            <div class="pro-pagination-style text-center">
                                <div class="text-muted mb-2">
                                    Showing <span class="fw-semibold">{{ $products->firstItem() ?? 0 }}</span> to <span class="fw-semibold">{{ $products->lastItem() ?? 0 }}</span> of <span class="fw-semibold">{{ $products->total() }}</span> Results
                                </div>
                                {{ $products->links() }}
                            </div>
                            <!--  Pagination Area End -->
                        </div>
                        <!-- Shop Bottom Area End -->
                    </div>
                    <!-- Sidebar Area Start -->
                    <div class="col-lg-3 order-lg-first col-md-12 order-md-last mb-res-md-60px mb-res-sm-60px">
                        <div class="left-sidebar">
                            <div class="sidebar-heading">
                                <div class="main-heading">
                                    <h2>Filter By</h2>
                                </div>
                                <!-- Sidebar single item -->
                                <div class="sidebar-widget">
                                    <h4 class="pro-sidebar-title">Categories</h4>
                                    <div class="sidebar-widget-list">
                                        <ul>
                                            @foreach(\App\HelperClass::getCategories() as $category)
                                            <li>
                                                <div class="sidebar-widget-list-left">
                                                    <label class="d-flex align-items-center w-100" style="cursor: pointer;">
                                                        <input type="checkbox" name="category[]" value="{{ $category->id }}" {{ is_array(request('category')) && in_array($category->id, request('category')) ? 'checked' : '' }} onchange="document.getElementById('filter-form').submit();">
                                                        <span class="ms-4">{{ $category->name }}</span>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </div>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <!-- Sidebar single item -->
                                @if($flashSales->isNotEmpty())
                                <div class="sidebar-widget mt-30">
                                    <h4 class="pro-sidebar-title">Flash Sales</h4>
                                    <div class="sidebar-widget-list">
                                        <ul>
                                            @foreach($flashSales as $sale)
                                            <li>
                                                <div class="sidebar-widget-list-left">
                                                    <label class="d-flex align-items-center w-100" style="cursor: pointer;">
                                                        <input type="checkbox" name="flash_sale[]" value="{{ $sale->id }}" {{ is_array(request('flash_sale')) && in_array($sale->id, request('flash_sale')) ? 'checked' : '' }} onchange="document.getElementById('filter-form').submit();">
                                                        <span class="ms-4">{{ $sale->name }}</span>
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </div>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                @endif
                                <!-- Sidebar single item -->
                            </div>
                            <!-- Sidebar single item -->
                            <div class="sidebar-widget mt-20">
                                <h4 class="pro-sidebar-title">Price</h4>
                                <div class="price-filter mt-10">
                                    <div class="price-slider-amount">
                                        <input type="text" id="amount-custom" readonly style="border:0; color:#555; font-weight:bold;">
                                    </div>
                                    <div id="slider-range-custom"></div>
                                </div>
                            </div>

                            @php
                                $variantMax = \App\Models\ProductVariant::max('regular_price') ?? 0;
                                $productMax = \App\Models\Product::max('regular_price') ?? 0;
                                $allMinPrice = 0;
                                $allMaxPrice = ceil(max($variantMax, $productMax));
                                if($allMaxPrice <= 0) $allMaxPrice = 1000; // Fallback

                                $currentMin = request('min_price', $allMinPrice);
                                $currentMax = request('max_price', $allMaxPrice);
                            @endphp

                            <input type="hidden" name="min_price" id="min_price" value="{{ $currentMin }}">
                            <input type="hidden" name="max_price" id="max_price" value="{{ $currentMax }}">

                            <!-- Sidebar single item -->
                            <div class="sidebar-widget mt-30">
                                <h4 class="pro-sidebar-title">Brand</h4>
                                <div class="sidebar-widget-list">
                                    <ul>
                                        @foreach(\App\HelperClass::getBrands() as $brand)
                                        <li>
                                            <div class="sidebar-widget-list-left">
                                                <label class="d-flex align-items-center w-100" style="cursor: pointer;">
                                                    <input type="checkbox" name="brand[]" value="{{ $brand->id }}" {{ is_array(request('brand')) && in_array($brand->id, request('brand')) ? 'checked' : '' }} onchange="document.getElementById('filter-form').submit();"> 
                                                    <span class="ms-4">{{ $brand->name }}</span>
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            </div>
                            </div>
                            <!-- Sidebar Area End -->                </div>
            </form>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            // Trigger form submit when NiceSelect value changes
            $('#sort-select').on('change', function() {
                $('#filter-form').submit();
            });

            // We use custom IDs to completely bypass the hardcoded Euro logic in template vendor scripts
            if ($("#slider-range-custom").length) {
                $("#slider-range-custom").slider({
                    range: true,
                    min: {{ $allMinPrice }},
                    max: {{ $allMaxPrice }},
                    values: [{{ $currentMin }}, {{ $currentMax }}],
                    slide: function(event, ui) {
                        $("#amount-custom").val(ui.values[0] + " - " + ui.values[1]);
                        $("#min_price").val(ui.values[0]);
                        $("#max_price").val(ui.values[1]);
                    },
                    stop: function(event, ui) {
                        $('#filter-form').submit();
                    }
                });

                // Set initial label value without currency
                $("#amount-custom").val($("#slider-range-custom").slider("values", 0) + " - " + $("#slider-range-custom").slider("values", 1));
            }
        });
    </script>
    @endsection
