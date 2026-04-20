@extends('admin.structure.master')

@section('app_content')
<style>
    :root {
        --login-bg: #040d0a; /* Very deep dark green/black */
        --glass-bg: rgba(255, 255, 255, 0.03);
        --glass-border: rgba(255, 255, 255, 0.08);
        --accent-glow: rgba(16, 185, 129, 0.3); /* Emerald green glow */
        --primary-glow: #10b981; /* Emerald green */
    }

    /* Isolated scope for login page to prevent tampering with other admin pages */
    html.login-page-html, 
    body.login-page-body {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        height: 100% !important;
        background-color: var(--login-bg) !important;
        overflow: hidden !important;
    }

    .login-wrapper {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        height: 100dvh !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        z-index: 9999 !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
    }

    /* Full screen background fix with greenish gradient */
    .login-background {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        background-color: var(--login-bg) !important;
        background-image: 
            radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.12) 0px, transparent 50%),
            radial-gradient(at 100% 100%, rgba(5, 150, 105, 0.12) 0px, transparent 50%) !important;
        z-index: 0 !important;
    }

    /* Handle mobile/small screens */
    @media (max-height: 750px) {
        html.login-page-html, 
        body.login-page-body {
            height: auto !important;
            min-height: 100% !important;
            overflow-y: auto !important;
        }
        .login-wrapper {
            align-items: flex-start !important;
            padding: 60px 0 !important;
            position: relative !important;
            height: auto !important;
            min-height: 100vh !important;
        }
    }

    .auth-container {
        width: 100%;
        max-width: 450px;
        margin: auto;
        padding: 20px;
        z-index: 2;
        position: relative;
    }

    .login-card {
        background: var(--glass-bg);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--glass-border);
        border-radius: 28px;
        padding: 40px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .login-card:hover {
        border-color: rgba(16, 185, 129, 0.35);
        box-shadow: 0 0 40px rgba(16, 185, 129, 0.12);
        transform: translateY(-2px);
    }

    .auth-logo img {
        filter: drop-shadow(0 0 12px rgba(16, 185, 129, 0.2));
        transition: transform 0.3s ease;
    }

    .form-label {
        color: rgba(255, 255, 255, 0.75);
        font-weight: 500;
        margin-bottom: 8px;
        font-size: 0.85rem;
        letter-spacing: 0.3px;
    }

    .form-control {
        background: rgba(255, 255, 255, 0.04) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        border-radius: 14px !important;
        color: #fff !important;
        padding: 14px 18px !important;
        font-size: 0.95rem !important;
        transition: all 0.3s ease !important;
    }

    .form-control:focus {
        background: rgba(255, 255, 255, 0.07) !important;
        border-color: var(--primary-glow) !important;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.18) !important;
    }

    .btn-login {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        border-radius: 14px;
        padding: 14px;
        color: white;
        font-weight: 600;
        font-size: 1rem;
        letter-spacing: 0.5px;
        margin-top: 10px;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        transition: all 0.3s ease;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px -5px rgba(16, 185, 129, 0.45);
        color: #fff;
        filter: brightness(1.1);
    }

    .form-check-input {
        background-color: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.15);
        cursor: pointer;
    }

    .form-check-input:checked {
        background-color: var(--primary-glow);
        border-color: var(--primary-glow);
    }

    .form-check-label {
        color: rgba(255, 255, 255, 0.55) !important;
        font-size: 0.85rem;
        cursor: pointer;
    }

    .footer-text {
        color: rgba(255, 255, 255, 0.35);
        font-size: 0.75rem;
        text-align: center;
        margin-top: 28px;
        letter-spacing: 0.5px;
    }

    .error-msg {
        background: rgba(239, 68, 68, 0.08);
        border-left: 3px solid #ef4444;
        color: #fca5a5;
        padding: 10px 14px;
        border-radius: 6px;
        font-size: 0.8rem;
        margin-top: 8px;
        display: block;
    }

    .shape {
        position: fixed !important;
        border-radius: 50%;
        filter: blur(100px);
        z-index: 0;
        pointer-events: none;
    }

    .shape-1 {
        width: 350px;
        height: 350px;
        background: rgba(16, 185, 129, 0.1);
        top: -120px;
        right: -120px;
    }

    .shape-2 {
        width: 300px;
        height: 300px;
        background: rgba(5, 150, 105, 0.08);
        bottom: -80px;
        left: -80px;
    }
</style>

<script>
    // Apply specialized classes to html and body only for this page
    document.documentElement.classList.add('login-page-html');
    document.body.classList.add('login-page-body');
</script>

<div class="login-background"></div>
<div class="login-wrapper">
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>

    <div class="auth-container">
        <div class="login-card">
            <div class="text-center mb-4">
                @php($gs = \App\HelperClass::generalSettings())
                <div class="auth-logo mb-4">
                    <a href="{{route('admin.login')}}">
                        <img src="{{ ($gs && $gs->dark_logo) ? asset('storage/'.$gs->dark_logo) : asset('admin_assets/assets/images/logo-dark.png') }}" height="45" alt="logo">
                    </a>
                </div>
                <h3 class="text-white fw-bold mb-1">Welcome Back</h3>
                <p class="text-muted small">Enter your credentials to access the admin portal</p>
            </div>

            <form action="{{route('admin.login')}}" method="post" class="mt-4">
                @csrf
                <div class="mb-4">
                    <label class="form-label" for="email">Email Address</label>
                    <div class="input-group">
                        <input type="email" id="email" name="email" class="form-control" placeholder="admin@example.com" value="{{ old('email') }}" required>
                    </div>
                    @error('email')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                    @error('password')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="checkbox-signin" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="checkbox-signin">Keep me logged in</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input toggle-password-visibility" id="show-password">
                        <label class="form-check-label" for="show-password">Show Password</label>
                    </div>
                </div>

                <div class="d-grid">
                    <button class="btn btn-login" type="submit">
                        Sign In to Dashboard
                    </button>
                </div>
            </form>

            <div class="footer-text">
                &copy; {{ date('Y') }} {{ $gs->business_name ?? 'Smart Ecom' }}. All rights reserved.
            </div>
        </div>
    </div>
</div>
@endsection
