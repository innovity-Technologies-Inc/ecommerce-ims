@extends('admin.structure.master')
@section('app_content')
    <!-- START Wrapper -->
    <div class="wrapper">

        @if(Route::currentRouteNamed('login'))
        @else
        @include('admin.structure.partials.header')
        @include('admin.structure.partials.sidebar')
        @endif
        <!-- ==================================================== -->
        <!-- Start right Content here -->
        <!-- ==================================================== -->
        <div class="page-content">

            @yield('content')

            @include('admin.structure.partials.footer')

        </div>
        <!-- ==================================================== -->
        <!-- End Page Content -->
        <!-- ==================================================== -->

    </div>
    <!-- END Wrapper -->


@endsection
