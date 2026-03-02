<!-- Breadcrumb Area start -->
<section class="breadcrumb-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-content">
                    <h1 class="breadcrumb-hrading">{{$title ?? ''}}</h1>
                    <ul class="breadcrumb-links">
                        <li><a href="{{route('home')}}">Home</a></li>
                        <li>{{$section ?? ''}}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Area End -->
