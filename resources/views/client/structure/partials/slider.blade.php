<!-- Slider Arae Start -->
@if($sliders->isNotEmpty())
<div class="slider-area">
    <div class="slider-active-3 owl-carousel slider-hm8 owl-dot-style">
        @foreach($sliders as $slider)
        <!-- Slider Single Item Start -->
        <div class="slider-height-6 d-flex align-items-start justify-content-start bg-img" style="background-image: url('{{ \App\HelperClass::file_url($slider->image) }}');">
            <div class="container">
                <div class="slider-content-1 slider-animated-1 text-left">
                    @if($slider->subtext)
                        <span class="animated text-uppercase">{{ $slider->subtext }}</span>
                    @endif
                    <h1 class="animated">
                        {!! nl2br(e($slider->title)) !!}
                    </h1>
                    @if($slider->subtitle)
                        <p class="animated">{{ $slider->subtitle }}</p>
                    @endif
                    @if($slider->button_name && $slider->button_url)
                        <a href="{{ $slider->button_url }}" class="shop-btn animated">{{ $slider->button_name }}</a>
                    @endif
                </div>
            </div>
        </div>
        <!-- Slider Single Item End -->
        @endforeach
    </div>
</div>
@endif
<!-- Slider Arae End -->
