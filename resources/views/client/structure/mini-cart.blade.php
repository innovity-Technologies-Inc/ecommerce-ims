@php
    $cartItems = \App\HelperClass::getCartItems();
    $total = $cartItems->sum('subtotal');
@endphp
<div class="inner">
    <div class="head">
        <span class="title">Cart</span>
        <button class="offcanvas-close">×</button>
    </div>
    <div class="body customScroll">
        @if($cartItems->count() > 0)
            <ul class="minicart-product-list">
                @foreach($cartItems as $item)
                    <li>
                        <a href="{{ route('client.products.details', $item->product_slug) }}" class="image">
                            <img
                                src="{{ $item->image ? asset('storage/'.$item->image) : asset('client/assets/images/product-image/mini-cart/1.jpg') }}"
                                alt="{{ $item->product_name }}">
                        </a>
                        <div class="content">
                            <a href="{{ route('client.products.details', $item->product_slug) }}"
                               class="title">{{ $item->product_name }}</a>
                            @if($item->variant_name)
                                <span class="variant-info"
                                      style="font-size: 12px; color: #666;">{{ $item->variant_details }}</span>
                            @endif
                            <span class="quantity-price">{{ $item->quantity }} x <span
                                    class="amount">${{ number_format($item->price, 2) }}</span></span>
                            <a href="javascript:void(0)" class="remove remove-from-cart" data-cart-id="{{ $item->id }}">×</a>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="text-center py-5">
                <i class="ion-bag mb-3" style="font-size: 48px; color: #ccc;"></i>
                <p>Your cart is empty.</p>
            </div>
        @endif
    </div>
    <div class="shopping-cart-total">
        <h4>Subtotal : <span>${{ number_format($total, 2) }}</span></h4>
        {{-- You can add shipping/tax logic here if needed --}}
        <h4 class="shop-total">Total : <span>${{ number_format($total, 2) }}</span></h4>
    </div>
    <div class="foot">
        <div class="buttons">
            <a href="{{ route('cart.index') }}" class="btn btn-dark btn-hover-primary mb-30px">view cart</a>
            <a href="checkout.html" class="btn btn-outline-dark current-btn">checkout</a>
        </div>
    </div>
</div>
