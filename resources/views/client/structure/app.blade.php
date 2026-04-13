@extends('client.structure.master')
@section('app_content')
    @include('client.structure.partials.header')
    @if(Route::currentRouteNamed('home'))
        @include('client.structure.partials.slider')
    @elseif(!Route::currentRouteNamed(['login', 'register', 'password.request', 'password.reset', 'verification.notice', 'password.confirm']))
        @include('client.structure.partials.breadcrumb')
    @endif
        @yield('content')
    @include('client.structure.partials.footer')

@endsection
