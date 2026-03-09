@extends('client.structure.app', ['title' => 'Checkout', 'section' => 'Checkout'])

@section('content')
<!-- checkout area start -->
<div class="checkout-area mtb-60px">
    <div class="container">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <form action="{{ route('checkout.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-7">
                    <div class="billing-info-wrap">
                        <h3>Shipping Details</h3>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="billing-info mb-20px">
                                    <label>Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required />
                                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="billing-info mb-20px">
                                    <label>Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required />
                                    @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="billing-info mb-20px">
                                    <label>Phone <span class="text-danger">*</span></label>
                                    <input type="text" name="mobile" value="{{ old('mobile', $user->mobile ?? '') }}" required />
                                    @error('mobile') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="billing-info mb-20px">
                                    <label>Address <span class="text-danger">*</span></label>
                                    <input type="text" name="address" placeholder="House number and street name" value="{{ old('address', $user->address ?? '') }}" required />
                                    @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="billing-info mb-20px">
                                    <label>City <span class="text-danger">*</span></label>
                                    <input type="text" name="city" value="{{ old('city', $user->city ?? '') }}" required />
                                    @error('city') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="billing-info mb-20px">
                                    <label>State</label>
                                    <input type="text" name="state" value="{{ old('state', $user->state ?? '') }}" />
                                    @error('state') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="billing-info mb-20px">
                                    <label>Postcode / ZIP</label>
                                    <input type="text" name="zip" value="{{ old('zip', $user->zip ?? '') }}" />
                                    @error('zip') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="billing-info mb-20px">
                                    <label>Country</label>
                                    <input type="text" name="country" value="{{ old('country', $user->country ?? 'Bangladesh') }}" />
                                    @error('country') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="additional-info-wrap">
                            <h3>Additional information</h3>
                            <div class="additional-info">
                                <label>Order notes</label>
                                <textarea placeholder="Notes about your order, e.g. special notes for delivery. " name="notes">{{ old('notes') }}</textarea>
                                @error('notes') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="your-order-area">
                        <h3>Your order</h3>
                        <div class="your-order-wrap gray-bg-4">
                            <div class="your-order-product-info">
                                <div class="your-order-top">
                                    <ul>
                                        <li>Product</li>
                                        <li>Total</li>
                                    </ul>
                                </div>
                                <div class="your-order-middle">
                                    <ul>
                                        @foreach($cartItems as $item)
                                            <li>
                                                <span class="order-middle-left">{{ $item->product_name }} X {{ $item->quantity }}</span>
                                                <span class="order-price">${{ number_format($item->subtotal, 2) }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="your-order-bottom">
                                    <ul>
                                        <li class="your-order-shipping">Shipping</li>
                                        <li>Free shipping</li>
                                    </ul>
                                </div>
                                <div class="your-order-total">
                                    <ul>
                                        <li class="order-total">Total</li>
                                        <li>${{ number_format($subtotal, 2) }}</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="payment-method">
                                <div class="payment-accordion element-mrg">
                                    <div class="panel-group" id="accordion">
                                        <div class="panel payment-accordion">
                                            <div class="panel-heading" id="method-one">
                                                <h4 class="panel-title">
                                                    <input type="radio" name="payment_method" value="COD" checked id="payment_cod">
                                                    <label for="payment_cod">Cash on Delivery (COD)</label>
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="panel payment-accordion">
                                            <div class="panel-heading" id="method-two">
                                                <h4 class="panel-title">
                                                    <input type="radio" name="payment_method" value="Online" id="payment_online">
                                                    <label for="payment_online">Pay Now (Online Payment)</label>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="Place-order mt-25">
                            <button type="submit" class="btn btn-primary w-100 py-3" style="background-color: #333; border: none;">Place Order</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- checkout area end -->
@endsection
