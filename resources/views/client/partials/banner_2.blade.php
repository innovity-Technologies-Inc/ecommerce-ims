@php
    $banner = \App\HelperClass::getBanner('home_2_full');
@endphp
<div class="banner-area-2">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="banner-inner">
                    <a href="{{ $banner->link ?? '#' }}"><img src="{{ str_contains($banner->image, 'client/') ? asset($banner->image) : asset('storage/'.$banner->image) }}" alt=""></a>
                </div>
            </div>
        </div>
    </div>
</div>
