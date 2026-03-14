@extends('client.structure.app')
@section('content')
    <!-- login area start -->
    <div class="login-register-area mb-60px mt-53px">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 col-md-12 mx-auto">
                    <div class="login-register-wrapper">
                        <div class="login-register-tab-list nav">
                            <a class="active">
                                <h4>login</h4>
                            </a>
                        </div>
                        <div class="tab-content">
                            <div id="lg1" class="tab-pane active">
                                <div class="login-form-container">
                                    <div class="login-register-form">
                                        <form action="{{route('login')}}" method="post">
                                            @csrf
                                            @if ($errors->any())
                                                <div class="text-danger mb-3">
                                                    <ul>
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif

                                            <input type="text" name="email" placeholder="Email Address">
                                            <input type="password" name="password" placeholder="Password">
                                            <div class="button-box">
                                                <div class="login-toggle-btn">
                                                    <!-- Remember Me -->
                                                    <input type="checkbox"
                                                           name="remember"
                                                           id="remember"
                                                        {{ old('remember') ? 'checked' : '' }}>
                                                    <label for="remember">Remember me</label>
                                                    <a href="{{route('password.request')}}">Forgot Password?</a>
                                                </div>

                                                <button type="submit"><span>Login</span></button>
                                            </div>

                                            @php
                                                $socialSetting = \App\HelperClass::socialLoginSettings();
                                            @endphp
                                            
                                            @if($socialSetting && $socialSetting->google_status)
                                                <div class="login-social mt-4 text-center">
                                                    <p class="mb-2">Or Login with</p>
                                                    <a href="{{ route('auth.google') }}" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2">
                                                        <i class="bx bxl-google fs-20"></i> Google
                                                    </a>
                                                </div>
                                            @endif
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- login area end -->
@endsection
