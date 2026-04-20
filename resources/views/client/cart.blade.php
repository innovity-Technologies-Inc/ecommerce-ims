@extends('client.structure.app')

@section('content')
    <style>
        @media (max-width: 767px) {
            .cart-table-content table thead {
                display: none;
            }
            .cart-table-content table tbody tr {
                display: grid !important;
                grid-template-columns: 40% 60%;
                grid-template-areas: 
                    "remove remove"
                    "thumb name"
                    "thumb price"
                    "thumb qty"
                    "thumb total";
                padding: 10px 0 20px 0;
                border-bottom: 1px solid #ebebeb;
                position: relative;
            }
            .cart-table-content table tbody tr td {
                border: none;
                padding: 4px 10px;
                width: 100% !important;
            }
            .cart-table-content table tbody tr td.product-remove {
                grid-area: remove;
                text-align: right;
                padding-bottom: 10px;
                display: block;
            }
            .cart-table-content table tbody tr td.product-remove a {
                font-size: 20px;
                color: #ff4545;
            }
            .cart-table-content table tbody tr td.product-thumbnail {
                grid-area: thumb;
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100%;
            }
            .cart-table-content table tbody tr td.product-thumbnail img {
                margin: 0 !important;
                max-width: 90px !important;
                height: auto;
            }
            .cart-table-content table tbody tr td.product-name {
                grid-area: name;
                text-align: left;
                font-weight: bold;
                font-size: 15px;
                padding-top: 0;
            }
            .cart-table-content table tbody tr td.product-price-decimal {
                grid-area: price;
                text-align: left;
                display: flex;
                font-size: 14px;
            }
            .cart-table-content table tbody tr td.product-price-decimal::before {
                content: "Price: ";
                margin-right: 5px;
                color: #777;
            }
            .cart-table-content table tbody tr td.product-quantity {
                grid-area: qty;
                text-align: left;
                display: flex;
                align-items: center;
                font-size: 14px;
            }
            .cart-table-content table tbody tr td.product-quantity::before {
                content: "Qty: ";
                margin-right: 5px;
                color: #777;
            }
            .cart-table-content table tbody tr td.product-subtotal {
                grid-area: total;
                text-align: left;
                display: flex;
                font-weight: bold;
                font-size: 14px;
            }
            .cart-table-content table tbody tr td.product-subtotal::before {
                content: "Total: ";
                margin-right: 5px;
                color: #777;
                font-weight: normal;
            }
            .cart-plus-minus {
                width: 100px !important;
                height: 32px !important;
                margin: 0 !important;
                padding: 0 !important;
                display: flex !important;
                align-items: center;
                justify-content: center;
                border: 1px solid #ebebeb;
                background: #fff;
            }
            .cart-plus-minus .qtybutton {
                width: 30px !important;
                height: 100% !important;
                display: flex !important;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                background: #f5f5f5;
                margin: 0 !important;
                padding: 0 !important;
                line-height: 1 !important;
            }
            .cart-plus-minus input.cart-plus-minus-box {
                width: 40px !important;
                height: 100% !important;
                border: none !important;
                text-align: center !important;
                padding: 0 !important;
                margin: 0 !important;
                font-size: 14px !important;
                background: #fff;
                display: block !important;
            }
            .cart-shiping-update a {
                white-space: nowrap !important;
                padding: 10px 15px !important;
            }
        }
    </style>
    <!-- cart area start -->
    <div class="cart-main-area mtb-60px">
        <div class="container">
            <h3 class="cart-page-title">Your cart items</h3>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                    <form action="#">
                        <div class="table-content table-responsive cart-table-content">
                            <table class="w-100">
                                <thead>
                                <tr>
                                    <th style="width: 10%;">Image</th>
                                    <th style="width: 40%;">Product Name</th>
                                    <th style="width: 15%;">Until Price</th>
                                    <th style="width: 15%;">Qty</th>
                                    <th style="width: 15%;">Subtotal</th>
                                    <th style="width: 5%;">Action</th>
                                </tr>
                                </thead>
                                <tbody id="cart-table-body">
                                    @if($cartItems->count() > 0)
                                        @foreach($cartItems as $item)
                                            <tr id="cart-row-{{ $item->id }}">
                                                <td class="product-thumbnail">
                                                    <a href="{{ route('client.products.details', $item->product_slug) }}">
                                                        <img class="img-responsive ml-15px" src="{{ $item->image ? asset('storage/'.$item->image) : asset('client/assets/images/product-image/mini-cart/1.jpg') }}" alt="{{ $item->product_name }}" style="max-width: 80px;" />
                                                    </a>
                                                </td>
                                                <td class="product-name">
                                                    <a href="{{ route('client.products.details', $item->product_slug) }}">{{ $item->product_name }}</a>
                                                    @if($item->variant_name)
                                                        <br><small class="text-muted">{{ $item->variant_details }}</small>
                                                    @endif
                                                </td>
                                                <td class="product-price-decimal">
                                                    @if($item->product_discount > 0)
                                                        <span class="amount">${{ number_format($item->price, 2) }}</span>
                                                        <span class="old-price text-decoration-line-through ms-2" style="color: #999; font-size: 0.9em;">${{ number_format($item->regular_price, 2) }}</span>
                                                    @else
                                                        <span class="amount">${{ number_format($item->price, 2) }}</span>
                                                    @endif
                                                </td>
                                                <td class="product-quantity">
                                                    <div class="cart-plus-minus">
                                                        <input class="cart-plus-minus-box qty-input" type="text" name="qtybutton" value="{{ $item->quantity }}" data-cart-id="{{ $item->id }}" />
                                                    </div>
                                                </td>
                                                <td class="product-subtotal">
                                                    $<span id="subtotal-{{ $item->id }}">{{ number_format($item->subtotal, 2) }}</span>
                                                </td>
                                                <td class="product-remove">
                                                    <a href="javascript:void(0)" class="remove-from-cart" data-cart-id="{{ $item->id }}"><i class="ion-android-close"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <h4>Your cart is empty.</h4>
                                                <a href="{{ route('client.products.index') }}" class="btn btn-primary mt-3" style="white-space: nowrap !important;">Go to Shop</a>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="cart-shiping-update-wrapper">
                                    <div class="cart-shiping-update">
                                        <a href="{{ route('client.products.index') }}">Continue Shopping</a>
                                    </div>
                                    <div class="cart-clear">
                                        <button type="button" id="clear-cart">Clear Shopping Cart</button>
                                        <a href="{{ route('checkout.index') }}" class="proceed-checkout-btn">Proceed to Checkout</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="row d-flex align-items-stretch">
                        <div class="col-lg-8 col-md-12 mb-res-sm-30px d-flex">
                            <div class="cart-banner w-100">
                                <a href="{{ route('client.products.index') }}" class="d-block h-100">
                                    <img src="{{ asset('client/assets/images/banner-image/5.jpg') }}" alt="Cart Banner" class="img-fluid w-100 h-100" style="object-fit: cover;">
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-12 d-flex">
                            <div class="grand-totall w-100">
                                <div class="title-wrap">
                                    <h4 class="cart-bottom-title section-bg-gary-cart">Cart Total</h4>
                                </div>
                                <h5>Subtotal <span class="cart-total-display">${{ number_format($cartItems->sum('subtotal'), 2) }}</span></h5>
                                <div class="total-shipping">
                                    <h5>Shipping Method</h5>
                                    <ul id="shipping-methods-list" class="list-unstyled">
                                        @foreach($shippingMethods as $method)
                                            <li class="mb-3 border-bottom pb-3">
                                                <div class="d-flex align-items-start">
                                                    <div class="shipping-radio-wrapper me-3 mt-1">
                                                        <input type="radio" name="shipping_method" class="shipping-method-radio" 
                                                               id="method-{{ $method->id }}"
                                                               value="{{ $method->id }}" 
                                                               {{ (session('shipping_method_id') == $method->id) ? 'checked' : '' }} 
                                                               style="width: 18px; height: 18px; cursor: pointer; vertical-align: middle;" />
                                                    </div>
                                                    <label class="flex-grow-1 mb-0" for="method-{{ $method->id }}" style="cursor: pointer;">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="fw-bold text-dark fs-15">{{ $method->name }}</span>
                                                            <span class="fw-bold text-dark fs-15">${{ number_format($method->price, 2) }}</span>
                                                        </div>
                                                        @if($method->short_description)
                                                            <div class="mt-1">
                                                                <small class="text-muted d-block" style="font-size: 13px; line-height: 1.4;">
                                                                    {{ $method->short_description }}
                                                                </small>
                                                            </div>
                                                        @endif
                                                    </label>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <h4 class="grand-totall-title">Shipping <span id="shipping-total-display">${{ number_format($selectedShippingMethod ? $selectedShippingMethod->price : 0, 2) }}</span></h4>
                                <h4 class="grand-totall-title">Grand Total <span class="grand-total-display">${{ number_format($cartItems->sum('subtotal') + ($selectedShippingMethod ? $selectedShippingMethod->price : 0), 2) }}</span></h4>
                                <a href="{{ route('checkout.index') }}" class="proceed-checkout-btn">Proceed to Checkout</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- cart area end -->
@endsection

@push('scripts')
    <script>
        $(document).on('change', '.shipping-method-radio', function() {
            let methodId = $(this).val();
            
            $.ajax({
                url: "{{ route('cart.update_shipping') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    shipping_method_id: methodId
                },
                success: function(response) {
                    if (response.status === 'success') {
                        $('#shipping-total-display').text('$' + response.shipping_price);
                        $('.grand-total-display').text('$' + response.grand_total);
                        toastr.success('Shipping method updated!');
                    }
                },
                error: function(xhr) {
                    toastr.error('Failed to update shipping method.');
                }
            });
        });

        $('.proceed-checkout-btn').on('click', function(e) {
            if (!$('input[name="shipping_method"]:checked').val()) {
                e.preventDefault();
                toastr.error('Please select a shipping method before checkout.');
            }
        });
    </script>
@endpush
