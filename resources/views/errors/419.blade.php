@php
    $isAdmin = request()->is('admin/*') || str_contains(url()->previous(), '/admin');
@endphp

@if($isAdmin)
    @include('errors.admin-419')
@else
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired - {{ \App\HelperClass::generalSettings()->business_name ?? 'Smart Ecom' }}</title>
    
    <!-- Existing Client Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    
    <!-- Existing Client CSS -->
    <link rel="stylesheet" href="{{asset('client/assets/css/vendor/vendor.min.css')}}">
    <link rel="stylesheet" href="{{asset('client/assets/css/plugins/plugins.min.css')}}">
    <link rel="stylesheet" href="{{asset('client/assets/css/style.css')}}">
    
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .expired-card {
            text-align: center;
            padding: 50px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            max-width: 500px;
            width: 90%;
            border: 1px solid #ebebeb;
        }

        .icon-circle {
            width: 120px;
            height: 120px;
            background: #f0fdf4;
            color: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 60px;
        }

        .error-title {
            font-family: 'DM Serif Display', serif;
            font-size: 32px;
            color: #222;
            margin-bottom: 15px;
        }

        .error-text {
            font-family: 'Open Sans', sans-serif;
            color: #666;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 35px;
        }

        .btn-shop {
            background-color: #222;
            color: #fff;
            padding: 15px 40px;
            border-radius: 5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .btn-shop:hover {
            background-color: #10b981;
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
        }

        .code-hint {
            margin-top: 40px;
            font-size: 12px;
            color: #ccc;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
    </style>
</head>
<body>
    <div class="expired-card">
        <div class="icon-circle">
            <iconify-icon icon="solar:bag-heart-bold-duotone"></iconify-icon>
        </div>
        
        <h1 class="error-title">Session Expired</h1>
        <p class="error-text">
            For your security, your shopping session has timed out due to inactivity. 
            <br><strong>Please return to the store to continue your shopping.</strong>
        </p>
        
        <a href="{{ url('/') }}" class="btn-shop">
            <iconify-icon icon="solar:home-smile-bold-duotone" style="font-size: 20px;"></iconify-icon>
            Back to Home Store
        </a>

        <div class="code-hint">Error Code: 419</div>
    </div>
</body>
</html>
@endif
