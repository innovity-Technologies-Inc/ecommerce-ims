<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Smart E-commerce</title>
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('client/assets/images/favicon/favicon.png')}}">

    <!-- Google Fonts -->
    <link href="../../css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{asset('client/assets/css/vendor/vendor.min.css')}}">
    <link rel="stylesheet" href="{{asset('client/assets/css/plugins/plugins.min.css')}}">
    <link rel="stylesheet" href="{{asset('client/assets/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('client/assets/css/responsive.css')}}">

</head>

<body>

<div id="main">

    @yield('app_content')

</div>

<script src="{{asset('client/assets/js/vendor/vendor.min.js')}}"></script>
<script src="{{asset('client/assets/js/plugins/plugins.min.js')}}"></script>

<!-- Main Activation JS -->
<script src="{{asset('client/assets/js/main.js')}}"></script>

</body>
</html>
