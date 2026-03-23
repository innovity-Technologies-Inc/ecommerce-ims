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
                                            
                                            <div class="mb-3">
                                                {!! NoCaptcha::display() !!}
                                                @error('g-recaptcha-response')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>

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

                                            @if(config('services.google.client_id'))
                                                <div class="login-social mt-4 text-center">
                                                    <p class="mb-2 text-muted">Or Login with</p>
                                                    <div class="d-flex justify-content-center">
                                                        <a href="{{ route('auth.google') }}" class="btn p-0 border-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; transition: transform 0.2s; background: none;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'" title="Login with Google">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 48 48">
                                                                <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                                                                <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
                                                                <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
                                                                <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571l6.19,5.238C43.196,34.212,44,29.351,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
                                                            </svg>
                                                        </a>
                                                    </div>
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
