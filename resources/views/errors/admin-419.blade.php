<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Session Expired - {{ \App\HelperClass::generalSettings()->business_name ?? 'Smart Ecom' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Monaco&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <style>
        :root {
            --emerald-primary: #10b981;
            --emerald-glow: rgba(16, 185, 129, 0.4);
            --bg-dark: #0f172a;
        }

        body {
            margin: 0; padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-dark);
            color: #f8fafc;
            height: 100vh;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden;
        }

        .background-glow {
            position: absolute; width: 600px; height: 600px;
            background: radial-gradient(circle, var(--emerald-glow) 0%, transparent 70%);
            filter: blur(60px); z-index: -1;
            animation: pulse 6s infinite alternate;
        }

        @keyframes pulse {
            0% { transform: scale(1) translate(-10%, -10%); opacity: 0.4; }
            100% { transform: scale(1.2) translate(10%, 10%); opacity: 0.7; }
        }

        .container {
            text-align: center; padding: 2rem;
            max-width: 500px; width: 90%;
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(16, 185, 129, 0.2);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
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

        h1 { font-size: 2rem; font-weight: 800; margin: 1rem 0; }
        p { color: #94a3b8; font-size: 1.1rem; line-height: 1.6; margin-bottom: 2.5rem; }

        .btn-action {
            display: inline-flex; align-items: center; gap: 0.75rem;
            background: var(--emerald-primary); color: #fff;
            padding: 1.1rem 3rem; border-radius: 16px;
            text-decoration: none; font-weight: 700;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.5);
        }

        .btn-action:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 20px 30px -10px rgba(16, 185, 129, 0.6);
            background: #059669;
        }
    </style>
</head>
<body>
    <div class="background-glow"></div>
    <div class="container">
        <div class="icon-box">
            <iconify-icon icon="solar:shield-user-bold-duotone" style="font-size: 50px; color: #fff;"></iconify-icon>
        </div>
        <div class="error-code">ADMIN / 419</div>
        <h1>Staff Session Expired</h1>
        <p>Your administrative security token has expired for your safety. <br><strong>Please login again to continue managing the store.</strong></p>
        
        <a href="{{ url('/admin/login') }}" class="btn-action">
            <iconify-icon icon="solar:login-3-bold-duotone" style="font-size: 24px;"></iconify-icon>
            Re-authenticate Admin
        </a>
    </div>
</body>
</html>
