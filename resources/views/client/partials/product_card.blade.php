@if(isset($product))
@php
    $priceData = \App\HelperClass::getProductPriceRange($product);
    $gs = \App\HelperClass::generalSettings();
@endphp
<article class="list-product">
    <div class="img-block">
        <a href="{{ route('client.products.details', $product->slug) }}" class="thumbnail">
            @if($product->primaryImage)
                <img class="first-img" src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name }}">
                @php
                    $secondImage = $product->images->where('is_primary', 0)->first();
                @endphp
                @if($secondImage)
                    <img class="second-img" src="{{ asset('storage/' . $secondImage->image_path) }}" alt="{{ $product->name }}">
                @else
                    <img class="second-img" src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name }}">
                @endif
            @else
                <img class="first-img" src="{{ asset('admin/assets/images/no-image.png') }}" alt="{{ $product->name }}">
            @endif
        </a>
        <div class="quick-view">
            <a class="quick_view" href="#" data-link-action="quickview" title="Quick view" data-bs-toggle="modal" data-bs-target="#exampleModal">
                <i class="ion-ios-search-strong"></i>
            </a>
        </div>
    </div>

    <ul class="product-flag">
        @if($priceData['has_discount'])
            <li class="new bg-danger" style="background-color: #ff4545 !important;">-{{ $priceData['max_discount_percentage'] }}%</li>
        @elseif($product->is_new_arrival)
            <li class="new">New</li>
        @endif
    </ul>

    <div class="product-decs">
        <a class="inner-link" href="#"><span>{{ $product->brand->name ?? 'BRAND' }}</span></a>
        <h2><a href="{{ route('client.products.details', $product->slug) }}" class="product-link text-truncate d-block">{{ $product->name }}</a></h2>
        <div class="rating-product">
            <i class="ion-android-star"></i>
            <i class="ion-android-star"></i>
            <i class="ion-android-star"></i>
            <i class="ion-android-star"></i>
            <i class="ion-android-star"></i>
        </div>
        <div class="pricing-meta">
            <ul>
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
        </div>
    </div>
    <div class="add-to-link">
        <ul>
            @if($product->variants->count() > 0)
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
</article>
@endif
