@if($hotDealsSection && $hotDealsSection->is_visible && $hotDealProducts->isNotEmpty())
<section class="hot-deal-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <!-- Section Title -->
                <div class="section-title">
                    <h2>{{ $hotDealsSection->section_title }}</h2>
                    <p>{{ $hotDealsSection->section_subtitle }}</p>
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
