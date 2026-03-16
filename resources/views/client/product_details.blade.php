@extends('client.structure.app')
@section('content')

    <style>
        @media (max-width: 767px) {
            .product-details-content h2 {
                line-height: 1.3 !important;
                font-size: 22px !important;
                margin-bottom: 15px !important;
            }
        }
    </style>

    <!-- Shop details Area start -->
    <section class="product-details-area mtb-60px">
        <div class="container">
            <div class="row">
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <div class="product-details-img product-details-tab">
                        <div class="zoompro-wrap zoompro-2">
                            <div class="zoompro-border zoompro-span">
                                @if($product->primaryImage)
                                    <img id="main-product-image" src="{{ asset('storage/'.$product->primaryImage->image_path) }}" alt="{{ $product->name }}" style="width: 100%;">
                                @else
                                    <img id="main-product-image" src="{{ asset('client/assets/images/product-image/organic/product-1.jpg') }}" alt="No Image" style="width: 100%;">
                                @endif
                            </div>
                        </div>
                        <div id="gallery" class="product-dec-slider-2">
                            @foreach($product->images as $image)
                                <a class="{{ $image->is_primary ? 'active' : '' }}" 
                                   href="javascript:void(0)"
                                   data-image="{{ asset('storage/'.$image->image_path) }}">
                                    <img src="{{ asset('storage/'.$image->image_path) }}" alt="{{ $product->name }}">
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <div class="product-details-content">
                        <h2>
                            {{ $product->name }}
                            @if(!$product->status)
                                <span class="badge bg-danger" style="font-size: 14px; vertical-align: middle; margin-left: 10px;">Discontinued</span>
                            @endif
                        </h2>
                        <p class="reference">Brand:<span> {{ $product->brand->name ?? '-' }}</span></p>
                        <div class="pro-details-rating-wrap">
                            <div class="rating-product">
                                <i class="ion-android-star"></i>
                                <i class="ion-android-star"></i>
                                <i class="ion-android-star"></i>
                                <i class="ion-android-star"></i>
                                <i class="ion-android-star"></i>
                            </div>
                        </div>
                        <div class="pricing-meta">
                            <ul>
                                @php
                                    $gs = \App\HelperClass::generalSettings();
                                    $priceData = \App\HelperClass::getProductPriceRange($product);
                                @endphp
                                <li class="old-price not-cut" id="current-selling-price">
                                    {{ $gs->currency ?? '$' }}{{ number_format($priceData['min'], 2) }}
                                    @if($priceData['has_range'])
                                        - {{ $gs->currency ?? '$' }}{{ number_format($priceData['max'], 2) }}
                                    @endif
                                </li>
                                <li class="old-price text-decoration-line-through" id="old-regular-price" style="margin-left: 10px; color: #999; {{ ($priceData['min_regular_price'] > $priceData['min']) ? '' : 'display:none;' }}">
                                    {{ $gs->currency ?? '$' }}{{ number_format($priceData['min_regular_price'] ?? 0, 2) }}
                                </li>
                            </ul>
                        </div>
                        <div class="pro-details-list">
                            <p>{{ $product->short_description }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold text-dark me-2">Availability:</label>
                            <span id="stock-display" class="badge {{ ($product->variants->count() > 0 ? ($product->variants->first()->stock > 0) : ($product->stock > 0)) ? 'bg-success' : 'bg-danger' }}">
                                @if($product->variants->count() > 0)
                                    {{ $product->variants->first()->stock > 0 ? $product->variants->first()->stock . ' In Stock' : 'Out of Stock' }}
                                @else
                                    {{ $product->stock > 0 ? $product->stock . ' In Stock' : 'Out of Stock' }}
                                @endif
                            </span>
                        </div>

                        @if($product->variants->count() > 0)
                        <div class="mb-4 pt-2">
                            <label class="d-block mb-2 fw-bold" style="color: #253237; font-size: 14px;">Available Variants</label>
                            <div class="custom-select-wrapper" style="position: relative; max-width: 450px;">
                                <select id="variant-selector" 
                                        style="height: 45px; 
                                               border: 1px solid #ebebeb; 
                                               border-radius: 0; 
                                               cursor: pointer; 
                                               width: 100%; 
                                               display: block;
                                               padding: 0 40px 0 15px;
                                               background: #fff;
                                               font-size: 14px;
                                               color: #555;
                                               outline: none;
                                               appearance: none;
                                               -webkit-appearance: none;
                                               -moz-appearance: none;
                                               background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23333%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-7.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E');
                                               background-repeat: no-repeat;
                                               background-position: right 15px center;
                                               background-size: 12px auto;">
                                    @foreach($product->variants as $variant)
                                        <option value="{{ $variant->id }}"
                                                data-regular-price="{{ number_format($variant->regular_price ?? $product->regular_price, 2) }}"
                                                data-discount-price="{{ ($variant->discount_price > 0) ? number_format($variant->discount_price, 2) : ( ($product->discount_price > 0) ? number_format($product->discount_price, 2) : '' ) }}"
                                                data-discount-percentage="{{ $variant->discount_percentage ?? $product->discount_percentage ?? '' }}"                                                data-stock="{{ $variant->stock ?? 0 }}">
                                            {{ $variant->variant_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif

                        @if($product->status)
                        <div class="pro-details-quality">
                            <div class="cart-plus-minus">
                                <input class="cart-plus-minus-box" type="text" name="qtybutton" value="1" id="product-quantity">
                            </div>
                            <div class="pro-details-cart btn-hover">
                                <a href="javascript:void(0)" class="add-to-cart-btn" data-product-id="{{ $product->id }}"> + Add To Cart</a>
                            </div>
                        </div>
                        @else
                        <div class="pro-details-quality">
                            <div class="pro-details-cart">
                                <span class="btn btn-danger disabled" style="cursor: not-allowed; opacity: 0.7; padding: 15px 35px; text-transform: uppercase; font-weight: 600; border-radius: 0;">Product Unavailable</span>
                            </div>
                        </div>
                        @endif
                        <div class="pro-details-wish-com">
                            <div class="pro-details-wishlist">
                                @if(Auth::guard('web')->check())
                                    <a href="javascript:void(0)" onclick="addToWishlist({{ $product->id }})"><i class="ion-android-favorite-outline"></i>Add to wishlist</a>
                                @endif
                            </div>
                        </div>
                        <div class="pro-details-social-info">
                            <span>Share</span>
                            <div class="social-info">
                                <ul>
                                    <li><a href="#"><i class="ion-social-facebook"></i></a></li>
                                    <li><a href="#"><i class="ion-social-twitter"></i></a></li>
                                    <li><a href="#"><i class="ion-social-google"></i></a></li>
                                    <li><a href="#"><i class="ion-social-instagram"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Shop details Area End -->

    <!-- product details description area start -->
    <div class="description-review-area mb-60px">
        <div class="container">
            <div class="description-review-wrapper">
                <div class="description-review-topbar nav">
                    <a class="active" data-bs-toggle="tab" href="#des-details1">Description</a>
                    <a data-bs-toggle="tab" href="#des-details2">Product Details</a>
                </div>
                <div class="tab-content description-review-bottom">
                    <div id="des-details1" class="tab-pane active">
                        <div class="product-description-wrapper">
                            {!! $product->description !!}
                        </div>
                    </div>
                    <div id="des-details2" class="tab-pane">
                        <div class="product-anotherinfo-wrapper">
                            <ul>
                                <li><span>Category</span> {{ $product->category->name ?? '-' }}</li>
                                <li><span>Sub Category</span> {{ $product->subCategory->name ?? '-' }}</li>
                                <li><span>Brand</span> {{ $product->brand->name ?? '-' }}</li>
                                <li><span>Stock Status</span> {{ $product->variants->sum('stock') > 0 ? 'In Stock' : 'Contact for Availability' }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- product details description area end -->

    <!-- Related Product Area Start -->
    <section class="recent-add-area mb-60px">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-title">
                        <h2>Related Products</h2>
                        <p>Customers who bought this also bought</p>
                    </div>
                </div>
            </div>
            <div class="recent-product-slider owl-carousel owl-nav-style">
                @foreach($relatedProducts as $item)
                <article class="list-product">
                    <div class="img-block">
                        <a href="{{ route('client.products.details', $item->slug) }}" class="thumbnail">
                            @if($item->primaryImage)
                                <img class="first-img" src="{{ asset('storage/'.$item->primaryImage->image_path) }}" alt="{{ $item->name }}">
                            @else
                                <img class="first-img" src="{{ asset('client/assets/images/product-image/organic/product-1.jpg') }}" alt="">
                            @endif
                        </a>
                    </div>
                    <div class="product-decs">
                        <a class="inner-link" href="#"><span>{{ $item->brand->name ?? 'BRAND' }}</span></a>
                        <h2><a href="{{ route('client.products.details', $item->slug) }}" class="product-link text-truncate d-block">{{ $item->name }}</a></h2>
                        <div class="pricing-meta">
                            <ul>
                                @php
                                    $priceRange = \App\HelperClass::getProductPriceRange($item);
                                @endphp
                                <li class="current-price">{{ $gs->currency ?? '$' }}{{ number_format($priceRange['min'], 2) }}</li>                            </ul>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </section>

    <script>
        window.addEventListener('load', function() {
            const currency = "{{ $gs->currency ?? '$' }}";
            const mainImg = $('#main-product-image');

            // Manual Thumbnail Switch Logic
            $(document).on('click', '#gallery a', function(e) {
                e.preventDefault();
                
                const newImage = $(this).attr('data-image');
                
                // Swap image source
                mainImg.attr('src', newImage);
                
                // Active class management
                $('#gallery a').removeClass('active');
                $(this).addClass('active');
            });

            $('#variant-selector').on('change', function() {
                const selected = $(this).find(':selected');
                const regPrice = selected.data('regular-price');
                const discPrice = selected.data('discount-price');
                const stock = parseInt(selected.data('stock'));
                const stockDisplay = $('#stock-display');
                
                // Update Price
                if (discPrice) {
                    $('#current-selling-price').text(currency + discPrice);
                    $('#old-regular-price').text(currency + regPrice).show();
                } else {
                    $('#current-selling-price').text(currency + regPrice);
                    $('#old-regular-price').hide();
                }

                // Update Stock
                if (!isNaN(stock)) {
                    if (stock > 0) {
                        stockDisplay.text(stock + ' In Stock').removeClass('bg-danger').addClass('bg-success');
                        $('.add-to-cart-btn').parent().show();
                    } else {
                        stockDisplay.text('Out of Stock').removeClass('bg-success').addClass('bg-danger');
                        $('.add-to-cart-btn').parent().hide();
                    }
                }
            });
        });
    </script>
@endsection
