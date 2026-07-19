<style>
    .brand-slider-item img {
        transition: transform 0.4s ease-in-out;
        filter: grayscale(100%);
        opacity: 0.6;
    }
    .brand-slider-item:hover img {
        transform: scale(1.15);
        filter: grayscale(0%);
        opacity: 1;
    }
    .brand-slider-item {
        overflow: hidden;
        padding: 10px;
    }
</style>

<div class="brand-area">
    <div class="container">
        <div class="brand-slider owl-carousel owl-nav-style owl-nav-style-2">
            @php
                $nav_brands = \App\HelperClass::getBrands();
            @endphp
            @foreach($nav_brands as $brand)
                <div class="brand-slider-item">
                    <a href="#"><img src="{{ \App\HelperClass::file_url($brand->icon) }}" alt="{{ $brand->name }}"></a>
                </div>
            @endforeach
        </div>
    </div>
</div>
