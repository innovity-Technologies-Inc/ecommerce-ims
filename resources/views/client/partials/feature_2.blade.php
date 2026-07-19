@if($featuredSection && $featuredSection->is_visible && $featuredProducts->isNotEmpty())
<section class="feature-area-2" style="background-image: url('{{ $featuredSection->background_image ? \App\HelperClass::file_url($featuredSection->background_image) : asset('client/assets/images/feature-bg/feature-bg.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    <div class="container">
        <div class="row">
            <!-- Content side -->
            <div class="col-lg-6 offset-lg-6 col-md-12 col-sm-12 col-xs-12">
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
