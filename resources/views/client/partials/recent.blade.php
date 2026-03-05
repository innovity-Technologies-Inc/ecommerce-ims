@if($newArrivalProducts->isNotEmpty())
<section class="recent-add-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <!-- Section Title -->
                <div class="section-title">
                    <h2>New Arrivals</h2>
                    <p>Add new products to weekly line up</p>
                </div>
                <!-- Section Title -->
            </div>
        </div>
        <!-- Recent Product slider Start -->
        <div class="recent-product-slider owl-carousel owl-nav-style">
            @foreach($newArrivalProducts as $product)
                @include('client.partials.product_card', ['product' => $product])
            @endforeach
        </div>
        <!-- Recent product slider end -->
    </div>
</section>
@endif
