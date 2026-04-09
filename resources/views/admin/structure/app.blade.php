@extends('admin.structure.master')
@section('app_content')
    <!-- START Wrapper -->
    <div class="wrapper">

        @if(Route::currentRouteNamed('login') || request()->has('is_print'))
        @else
        @include('admin.structure.partials.header')
        @include('admin.structure.partials.sidebar')
        @endif
        <!-- ==================================================== -->
        <!-- Start right Content here -->
        <!-- ==================================================== -->
        <div class="{{ request()->has('is_print') ? '' : 'page-content' }}">

            @yield('content')

            @if(!request()->has('is_print'))
                @include('admin.structure.partials.footer')
            @endif

        </div>
        <!-- ==================================================== -->
        <!-- End Page Content -->
        <!-- ==================================================== -->

    </div>
    <!-- END Wrapper -->

@endsection

@section('scripts')
    @yield('scripts')
@endsection
