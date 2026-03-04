<!-- cart area start -->
@php
    $gs = \App\HelperClass::generalSettings();
@endphp
<style>
    /* Force full-width by overriding template's hardcoded pixel widths */
    .cart-table-content table tbody > tr td.product-name,
    .cart-table-content table tbody > tr td.product-price-cart,
    .cart-table-content table tbody > tr td.product-quantity,
    .cart-table-content table tbody > tr td.product-subtotal {
        width: auto !important;
    }
    .cart-table-content table {
        width: 100% !important;
    }
    .cart-btn-2 {
        background-color: #7AAACE !important;
        color: #fff !important;
    }
    .cart-btn-2:hover {
        background-color: #253237 !important;
        color: #fff !important;
    }
</style>
<div class="cart-main-area">
    <h3 class="cart-page-title">Your {{ $type == 'wishlist' ? 'Wishlist' : 'Cart' }} items</h3>
    <div class="table-content table-responsive cart-table-content">
        <table>
            <thead>
            <tr>
                <th>Image</th>
                <th>Product Name</th>
                <th>Unit Price</th>
                @if($type == 'cart')
                    <th>Qty</th>
                    <th>Subtotal</th>
                @endif
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse($items as $item)
                @php
                    $product = $type == 'wishlist' ? $item->product : $item->model;
                    $minPrice = $product->variants->min(fn($v) => $v->discount_price ?? $v->regular_price);
                @endphp
                <tr>
                    <td class="product-thumbnail">
                        <a href="#">
                            @if($product->primaryImage)
                                <img src="{{ asset('storage/'.$product->primaryImage->image_path) }}" alt="{{ $product->name }}" style="max-width: 100px;">
                            @else
                                <img src="{{ asset('client/assets/images/product-image/mini-cart/1.1.jpg') }}" alt="No Image" style="max-width: 100px;">
                            @endif
                        </a>
                    </td>
                    <td class="product-name"><a href="#">{{ $product->name }}</a></td>
                    <td class="product-price-cart">
                        <span class="amount">{{ $gs->currency ?? '$' }}{{ number_format($minPrice, 2) }}</span>
                    </td>
                    @if($type == 'cart')
                        <td class="product-quantity">
                            <div class="cart-plus-minus">
                                <input class="cart-plus-minus-box" type="text" name="qtybutton" value="{{ $item->qty }}">
                            </div>
                        </td>
                        <td class="product-subtotal">{{ $gs->currency ?? '$' }}{{ number_format($item->subtotal, 2) }}</td>
                    @endif
                    <td class="product-wishlist-cart">
                        <div class="d-flex align-items-center justify-content-center gap-3">
                            @if($type == 'wishlist')
                                <a href="#" class="cart-btn-2">Add to Cart</a>
                                <form action="{{ route('user.wishlist.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="border-0 bg-transparent text-danger confirmDelete" style="font-size: 20px;"><i class="fa fa-times"></i></button>
                                </form>
                            @else
                                <form action="#" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="border-0 bg-transparent text-danger confirmDelete" style="font-size: 20px;"><i class="fa fa-times"></i></button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $type == 'cart' ? 6 : 4 }}" class="text-center py-5">
                        <h4>Your {{ $type }} is empty!</h4>
                        <div class="cart-shiping-update-wrapper justify-content-center">
                            <div class="cart-shiping-update mt-3">
                                <a href="{{ route('client.products.index') }}">Continue Shopping</a>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
