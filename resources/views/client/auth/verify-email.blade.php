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
        min-height: 500px;
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
    @media (max-width: 991px) {
        .auth-side-banner { display: none; }
        .auth-form-content { width: 100%; padding: 40px 30px; }
        .auth-card { max-width: 500px; min-height: auto; }
    }
</style>

<div class="auth-page-wrapper">
    <div class="auth-card">
        <div class="auth-side-banner">
            <h2 class="fw-bold mb-4">Verify Email</h2>
            <p class="fs-16 opacity-75">Thanks for signing up! Please verify your email address to unlock all features of your account.</p>
            <div class="mt-auto text-center">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link text-white text-decoration-underline p-0 small">Logout</button>
                </form>
            </div>
        </div>
        <div class="auth-form-content text-center">
            <div class="auth-header mb-4">
                <div class="mb-3">
                    <iconify-icon icon="solar:letter-unread-bold-duotone" class="text-primary display-4"></iconify-icon>
                </div>
                <h3>Check your inbox</h3>
                <p class="text-muted">We've sent a verification link to your email address. Please click it to continue.</p>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="alert alert-success border-0 bg-soft-success text-success small p-3 rounded-3 mb-4">
                    A new verification link has been sent to your email address.
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn-auth-submit">
                    Resend Verification Email
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
