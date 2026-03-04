<!-- Breadcrumb Area start -->
@php
    $gs = \App\HelperClass::generalSettings();
@endphp
<style>
    .breadcrumb-links li a {
        color: #eee !important;
    }
    .breadcrumb-links li a:hover {
        color: #7AAACE !important; /* Restoring the original hover color */
    }
</style>
<section class="breadcrumb-area" style="position: relative; {{ $gs->breadcrumb_image ? 'background-image: url('.asset('storage/'.$gs->breadcrumb_image).') !important;' : '' }}">
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1;"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-content">
                    <h1 class="breadcrumb-hrading" style="color: #fff !important;">{{$title ?? ''}}</h1>
                    <ul class="breadcrumb-links">
                        <li><a href="{{route('home')}}">Home</a></li>
                        <li style="color: #ddd !important;">{{$section ?? ''}}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Area End -->
