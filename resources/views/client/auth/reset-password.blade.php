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
        min-height: 550px;
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
        margin-bottom: 15px;
    }
    .auth-input-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: #4a5568;
        font-size: 14px;
    }
    .auth-input-group input {
        width: 100%;
        padding: 10px 16px;
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
    .auth-footer {
        margin-top: 20px;
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
            <h2 class="fw-bold mb-4">Secure Reset</h2>
            <p class="fs-16 opacity-75">Update your password to ensure your account remains protected and secure.</p>
            <div class="mt-auto">
                <a href="{{ route('login') }}" class="text-white fw-bold text-decoration-underline"><i class="fa fa-arrow-left me-2"></i>Back to Login</a>
            </div>
        </div>
        <div class="auth-form-content">
            <div class="auth-header mb-4">
                <h3>New Password</h3>
                <p class="text-muted">Enter your new credentials below.</p>
            </div>

            <form action="{{ route('password.store') }}" method="POST">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="auth-input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $request->email) }}" placeholder="name@example.com" required readonly>
                    @error('email')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="auth-input-group">
                    <label>New Password</label>
                    <input type="password" name="password" placeholder="••••••••" required autofocus>
                    @error('password')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="auth-input-group">
                    <label>Confirm Password</label>
                    <input type="password" name="password_confirmation" placeholder="••••••••" required>
                    @error('password_confirmation')
                        <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-auth-submit">
                    Update Password
                </button>
            </form>

            <div class="auth-footer">
                Back to <a href="{{ route('login') }}">Sign In</a>
            </div>
        </div>
    </div>
</div>
@endsection
