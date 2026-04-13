@extends('client.structure.app')
@section('content')
@php $gs = \App\HelperClass::generalSettings(); @endphp
<style>
    .auth-page-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
        padding: 40px 20px;
    }
    .auth-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.05);
        overflow: hidden;
        display: flex;
        max-width: 1000px;
        width: 100%;
        min-height: 600px;
        transition: all 0.4s ease;
        border: 1px solid transparent;
    }
    .auth-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 50px rgba(122, 170, 206, 0.3), 0 0 15px rgba(122, 170, 206, 0.2);
        border-color: #7AAACE;
    }
    .auth-side-banner {
        background: {{ isset($gs->login_banner) ? 'url('.asset('storage/'.$gs->login_banner).')' : 'linear-gradient(135deg, #7AAACE, #9CC2E2)' }};
        background-size: cover;
        background-position: center;
        width: 45%;
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        text-align: left;
        color: #fff;
        position: relative;
    }
    .auth-side-banner::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: {{ isset($gs->login_banner) ? 'rgba(0,0,0,0.3)' : "url('https://www.transparenttextures.com/patterns/cubes.png')" }};
        opacity: {{ isset($gs->login_banner) ? '1' : '0.1' }};
    }
    .auth-side-banner > * {
        position: relative;
        z-index: 1;
    }
    .auth-form-content {
        width: 55%;
        padding: 50px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .auth-header h3 {
        font-weight: 700;
        color: #253237;
        margin-bottom: 10px;
    }
    .auth-input-group {
        margin-bottom: 20px;
    }
    .auth-input-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #4a5568;
        font-size: 14px;
    }
    .auth-input-group input {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #edf2f7;
        border-radius: 10px;
        transition: all 0.3s ease;
        outline: none;
    }
    .auth-input-group input:focus {
        border-color: #7AAACE;
        box-shadow: 0 0 0 4px rgba(122, 170, 206, 0.1);
    }
    .btn-auth-submit {
        background: #7AAACE;
        color: #fff;
        border: none;
        padding: 14px;
        border-radius: 10px;
        font-weight: 700;
        width: 100%;
        transition: all 0.3s ease;
        margin-top: 10px;
    }
    .btn-auth-submit:hover {
        background: #6b99ba;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(122, 170, 206, 0.3);
    }
    .social-auth-divider {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 25px 0;
        color: #a0aec0;
    }
    .social-auth-divider::before, .social-auth-divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #e2e8f0;
    }
    .social-auth-divider span {
        padding: 0 10px;
        font-size: 13px;
    }
    .btn-google-auth {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        padding: 12px;
        border: 2px solid #edf2f7;
        border-radius: 10px;
        background: #fff;
        color: #4a5568;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-google-auth:hover {
        background: #f7fafc;
        border-color: #e2e8f0;
    }
    .auth-footer {
        margin-top: 30px;
        text-align: center;
        font-size: 14px;
        color: #718096;
    }
    .auth-footer a {
        color: #7AAACE;
        font-weight: 700;
        text-decoration: none;
    }
    @media (max-width: 991px) {
        .auth-side-banner { display: none; }
        .auth-form-content { width: 100%; padding: 40px 30px; }
        .auth-card { max-width: 500px; min-height: auto; }
    }
</style>

<div class="auth-page-wrapper">
    <div class="auth-card">
        <div class="auth-side-banner">
            <h2 class="fw-bold mb-4">Welcome Back!</h2>
            <p class="fs-16 opacity-75">Sign in to your account to continue your shopping journey with {{ config('app.name') }}.</p>
            <div class="mt-auto">
                <p class="mb-0 small">Don't have an account?</p>
                <a href="{{ route('register') }}" class="text-white fw-bold text-decoration-underline">Register now for free!</a>
            </div>
        </div>
        <div class="auth-form-content">
            <div class="auth-header mb-4">
                <h3>Login</h3>
                <p class="text-muted">Enter your credentials to access your account.</p>
            </div>

            <form action="{{ route('login') }}" method="POST">
                @csrf
                
                @if ($errors->any())
                    <div class="alert alert-danger border-0 bg-soft-danger text-danger small p-3 rounded-3 mb-4">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="auth-input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" required autofocus>
                </div>

                <div class="auth-input-group">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="mb-0">Password</label>
                        <a href="{{ route('password.request') }}" class="small text-decoration-none" style="color: #7AAACE;">Forgot?</a>
                    </div>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>

                <div class="d-flex align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label small text-muted" for="remember">
                            Stay logged in
                        </label>
                    </div>
                </div>

                <div class="mb-4">
                    {!! NoCaptcha::display() !!}
                    @error('g-recaptcha-response')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-auth-submit">
                    Sign In
                </button>

                @if(config('services.google.client_id'))
                    <div class="social-auth-divider">
                        <span>OR</span>
                    </div>

                    <a href="{{ route('auth.google') }}" class="btn-google-auth">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48">
                            <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                            <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
                            <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
                            <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571l6.19,5.238C43.196,34.212,44,29.351,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
                        </svg>
                        Continue with Google
                    </a>
                @endif
            </form>

            <div class="auth-footer">
                New here? <a href="{{ route('register') }}">Create an account</a>
            </div>
        </div>
    </div>
</div>
@endsection
