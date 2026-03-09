@extends('client.structure.app', ['title' => 'Order Success', 'section' => 'Success'])

@section('content')
<div class="checkout-area mtb-60px">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="order-success-content">
                    <div class="success-icon mb-4">
                        <i class="ion-android-checkmark-circle" style="font-size: 80px; color: #28a745;"></i>
                    </div>
                    <h2>Thank You for Your Order!</h2>
                    <p class="lead mb-4">Your order has been placed successfully. Your Order ID is: <strong>{{ $order_id }}</strong></p>
                    <p>An email confirmation has been sent to your email address.</p>
                    <div class="mt-5">
                        <a href="{{ route('client.products.index') }}" class="btn btn-primary px-5 py-3" style="background-color: #333; border: none;">Continue Shopping</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
