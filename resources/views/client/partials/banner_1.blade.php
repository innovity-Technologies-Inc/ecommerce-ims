@php
    $b_left = \App\HelperClass::getBanner('home_1_left');
    $b_middle = \App\HelperClass::getBanner('home_1_middle');
    $b_right = \App\HelperClass::getBanner('home_1_right');
@endphp
<div class="banner-area">
    <div class="container">
        <div class="row mb-n-30px">
            <div class="col-lg-3 col-sm-6 mb-30px xs-6 order-1 order-lg-1">
                <div class="banner-wrapper">
                    <a href="{{ $b_left->link ?? '#' }}"><img src="{{ str_contains($b_left?->image ?? 'default.png', 'client/') ? asset($b_left?->image ?? 'default.png') : \App\HelperClass::file_url(($b_left?->image ?? 'default.png')) }}" alt=""></a>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 mb-30px order-3 order-lg-2">
                <div class="banner-wrapper">
                    <a href="{{ $b_middle->link ?? '#' }}"><img src="{{ str_contains($b_middle?->image ?? 'default.png', 'client/') ? asset($b_middle?->image ?? 'default.png') : \App\HelperClass::file_url(($b_middle?->image ?? 'default.png')) }}" alt=""></a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-30px xs-6 order-2 order-lg-3">
                <div class="banner-wrapper">
                    <a href="{{ $b_right->link ?? '#' }}"><img src="{{ str_contains($b_right?->image ?? 'default.png', 'client/') ? asset($b_right?->image ?? 'default.png') : \App\HelperClass::file_url(($b_right?->image ?? 'default.png')) }}" alt=""></a>
                </div>
            </div>
        </div>
    </div>
</div>
