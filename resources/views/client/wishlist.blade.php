@extends('client.structure.app')
@section('content')

    <div class="wishlist-container py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    @include('client.partials.cart_view', ['items' => $wishlistItems, 'type' => 'wishlist'])
                </div>
            </div>
        </div>
    </div>

@endsection
