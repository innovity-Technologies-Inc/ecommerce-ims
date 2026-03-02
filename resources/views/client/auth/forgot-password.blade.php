@extends('client.structure.app')

@section('content')
    <!-- forgot password area start -->
    <div class="login-register-area mb-60px mt-53px">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 col-md-12 mx-auto">
                    <div class="login-register-wrapper">
                        <div class="login-register-tab-list nav">
                            <a class="active">
                                <h4>Forgot Password</h4>
                            </a>
                        </div>

                        <div class="tab-content">
                            <div id="fp1" class="tab-pane active">
                                <div class="login-form-container">
                                    <div class="login-register-form">

                                        <!-- Session Status -->
                                        @if (session('status'))
                                            <div class="text-success mb-3">
                                                {{ session('status') }}
                                            </div>
                                        @endif

                                        <form action="{{ route('password.email') }}" method="POST">
                                            @csrf

                                            <!-- Email Address -->
                                            <div class="mb-3">
                                                <input type="email"
                                                       name="email"
                                                       placeholder="Email Address"
                                                       value="{{ old('email') }}"
                                                       required
                                                       autofocus>
                                                @error('email')
                                                <small class="text-danger d-block mt-1">
                                                    {{ $message }}
                                                </small>
                                                @enderror
                                            </div>

                                            <div class="button-box">
                                                <div class="login-toggle-btn">
                                                    <a href="{{ route('login') }}">Back to Login</a>
                                                </div>

                                                <button type="submit">
                                                    <span>Send Password Reset Link</span>
                                                </button>
                                            </div>
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
    <!-- forgot password area end -->
@endsection
