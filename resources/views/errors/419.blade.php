<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired - {{ \App\HelperClass::generalSettings()->business_name ?? 'Smart Ecom' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Monaco&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <style>
        :root {
            --emerald-primary: #10b981;
            --emerald-glow: rgba(16, 185, 129, 0.4);
            --bg-dark: #0f172a;
            --card-dark: #1e293b;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-dark);
            color: #f8fafc;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Animated Glow Background */
        .background-glow {
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, var(--emerald-glow) 0%, transparent 70%);
            filter: blur(60px);
            z-index: -1;
            animation: pulse 8s infinite alternate;
        }

        @keyframes pulse {
            0% { transform: scale(1) translate(-20%, -20%); opacity: 0.5; }
            100% { transform: scale(1.3) translate(20%, 20%); opacity: 0.8; }
        }

        .container {
            text-align: center;
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(16, 185, 129, 0.2);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 20px rgba(16, 185, 129, 0.1);
        }

        .icon-box {
            width: 100px;
            height: 100px;
            background: var(--emerald-glow);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            border: 2px solid var(--emerald-primary);
            box-shadow: 0 0 30px var(--emerald-glow);
        }

        .error-code {
            font-family: 'Monaco', monospace;
            font-size: 5rem;
            font-weight: 800;
            margin: 0;
            background: linear-gradient(to bottom, #fff, var(--emerald-primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
        }

        h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 1rem 0 0.5rem;
        }

        p {
            color: #94a3b8;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2.5rem;
        }

        .btn-login {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            background: var(--emerald-primary);
            color: #fff;
            padding: 1rem 2.5rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3), 0 0 20px var(--emerald-glow);
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.4), 0 0 30px var(--emerald-primary);
            background: #059669;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .footer-text {
            margin-top: 3rem;
            font-size: 0.85rem;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
    </style>
</head>
<body>
    <div class="background-glow"></div>
    
    <div class="container">
        <div class="icon-box">
            <iconify-icon icon="solar:clock-circle-bold-duotone" style="font-size: 60px; color: #fff;"></iconify-icon>
        </div>
        
        <div class="error-code">419</div>
        <h1>Session Expired</h1>
        <p>Security token mismatch or inactivity timeout. <br><strong>Please login again to continue your task.</strong></p>
        
        @php
            $isAdmin = request()->is('admin/*') || str_contains(url()->previous(), '/admin');
            $redirectUrl = $isAdmin ? url('/admin/login') : url('/');
            $btnText = $isAdmin ? 'Login Again' : 'Go to Homepage';
            $btnIcon = $isAdmin ? 'solar:login-bold-duotone' : 'solar:home-smile-bold-duotone';
        @endphp

        <a href="{{ $redirectUrl }}" class="btn-login">
            <iconify-icon icon="{{ $btnIcon }}"></iconify-icon>
            {{ $btnText }}
        </a>

        <div class="footer-text">
            &copy; {{ date('Y') }} {{ \App\HelperClass::generalSettings()->business_name ?? 'Smart Ecom' }}
        </div>
    </div>
</body>
</html>
