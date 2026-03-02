@extends('client.structure.app')

@section('content')
    <!-- reset password area start -->
    <div class="login-register-area mb-60px mt-53px">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 col-md-12 mx-auto">
                    <div class="login-register-wrapper">
                        <div class="login-register-tab-list nav">
                            <a class="active">
                                <h4>Reset Password</h4>
                            </a>
                        </div>

                        <div class="tab-content">
                            <div class="tab-pane active">
                                <div class="login-form-container">
                                    <div class="login-register-form">

                                        <form method="POST" action="{{ route('password.store') }}">
                                            @csrf

                                            <!-- Password Reset Token -->
                                            <input type="hidden"
                                                   name="token"
                                                   value="{{ $request->route('token') }}">

                                            <!-- Email -->
                                            <div class="mb-3">
                                                <input type="email"
                                                       name="email"
                                                       placeholder="Email Address"
                                                       value="{{ old('email', $request->email) }}"
                                                       required autofocus>

                                                @error('email')
                                                <small class="text-danger d-block mt-1">
                                                    {{ $message }}
                                                </small>
                                                @enderror
                                            </div>

                                            <!-- New Password -->
                                            <div class="mb-3">
                                                <input type="password"
                                                       name="password"
                                                       placeholder="New Password"
                                                       required>

                                                @error('password')
                                                <small class="text-danger d-block mt-1">
                                                    {{ $message }}
                                                </small>
                                                @enderror
                                            </div>

                                            <!-- Confirm Password -->
                                            <div class="mb-3">
                                                <input type="password"
                                                       name="password_confirmation"
                                                       placeholder="Confirm Password"
                                                       required>

                                                @error('password_confirmation')
                                                <small class="text-danger d-block mt-1">
                                                    {{ $message }}
                                                </small>
                                                @enderror
                                            </div>

                                            <div class="button-box">
                                                <div class="login-toggle-btn">
                                                    <a href="{{ route('login') }}">
                                                        Back to Login
                                                    </a>
                                                </div>

                                                <button type="submit">
                                                    <span>Reset Password</span>
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
    <!-- reset password area end -->
@endsection
