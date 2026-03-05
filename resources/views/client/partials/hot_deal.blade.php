@if($hotDealProducts->isNotEmpty())
<section class="hot-deal-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <!-- Section Title -->
                <div class="section-title">
                    <h2>Hot Deals</h2>
                    <p>Add hot products to weekly line up</p>
                </div>
                <!-- Section Title -->
            </div>
        </div>
        <!-- Hot Deal Slider 2 Start -->
        <div class="hot-deal-2 owl-carousel owl-nav-style">
            @foreach($hotDealProducts as $product)
                @include('client.partials.product_card', ['product' => $product])
            @endforeach
        </div>
        <!-- Hot Deal Slider 2 Start -->
    </div>
</section>
@endif
