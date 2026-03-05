@if($bestsellerSection && $bestsellerSection->is_visible && $bestsellingProducts->isNotEmpty())
<section class="best-sells-area mt-5">
    <div class="container">
        <!-- Section Title Start -->
        <div class="row">
            <div class="col-md-12">
                <div class="section-title">
                    <h2>{{ $bestsellerSection->section_title }}</h2>
                    <p>{{ $bestsellerSection->section_subtitle }}</p>
                </div>
            </div>
        </div>
        <!-- Section Title End -->
        <!-- Best Sell Slider Carousel Start -->
        <div class="best-sell-slider owl-carousel owl-nav-style">
            @foreach($bestsellingProducts as $product)
                @include('client.partials.product_card', ['product' => $product])
            @endforeach
        </div>
        <!-- Best Sells Carousel End -->
    </div>
</section>
@endif
