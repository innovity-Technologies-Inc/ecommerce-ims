@if($topPicksSection && $topPicksSection->is_visible && $topPicksProducts->isNotEmpty())
<section class="feature-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <!-- Section Title -->
                <div class="section-title">
                    <h2>{{ $topPicksSection->section_title }}</h2>
                    <p>{{ $topPicksSection->section_subtitle }}</p>
                </div>
                <!-- Section Title -->
            </div>
        </div>
        <!-- Feature Slider Start -->
        <div class="feature-slider owl-carousel owl-nav-style">
            @foreach($topPicksProducts->chunk(2) as $chunk)
            <!-- Single Item -->
            <div class="feature-slider-item">
                @foreach($chunk as $product)
                    @include('client.partials.product_card', ['product' => $product])
                @endforeach
            </div>
            @endforeach
        </div>
        <!-- Feature Slider End -->
    </div>
</section>
@endif
