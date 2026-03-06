@if($featuredSection && $featuredSection->is_visible && $featuredProducts->isNotEmpty())
<section class="feature-area-2">
    <div class="container">
        <div class="row">
            <!-- left side -->
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <div class="feature-left">
                    <img src="{{ $featuredSection->background_image ? asset('storage/'.$featuredSection->background_image) : asset('client/assets/images/feature-bg/2.png') }}" alt="Featured Background" class="img-responsive">
                </div>
            </div>
            <!-- left side -->
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <!-- Section Title -->
                <div class="section-title">
                    <h2>{{ $featuredSection->section_title }}</h2>
                    <p>{{ $featuredSection->section_subtitle }}</p>
                </div>
                <!-- Section Title -->
                <!-- Feature slide 2 start -->
                <div class="feature-slider-2 owl-carousel owl-nav-style">
                    @foreach($featuredProducts->chunk(2) as $chunk)
                    <!-- single item -->
                    <div class="feature-slider-item">
                        @foreach($chunk as $product)
                            @include('client.partials.product_card', ['product' => $product])
                        @endforeach
                    </div>
                    @endforeach
                </div>
                <!-- Feature slide 2 End -->
            </div>
        </div>
    </div>
</section>
@endif
