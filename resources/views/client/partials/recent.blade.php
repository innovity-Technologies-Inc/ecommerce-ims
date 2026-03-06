@if($recentlyAddedSection && $recentlyAddedSection->is_visible && $recentlyAddedProducts->isNotEmpty())
<section class="recent-add-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <!-- Section Title -->
                <div class="section-title">
                    <h2>{{ $recentlyAddedSection->section_title }}</h2>
                    <p>{{ $recentlyAddedSection->section_subtitle }}</p>
                </div>
                <!-- Section Title -->
            </div>
        </div>
        <!-- Recent Product slider Start -->
        <div class="recent-product-slider owl-carousel owl-nav-style">
            @foreach($recentlyAddedProducts as $product)
                @include('client.partials.product_card', ['product' => $product])
            @endforeach
        </div>
        <!-- Recent product slider end -->
    </div>
</section>
@endif
