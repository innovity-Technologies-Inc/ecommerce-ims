<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Title Meta -->
    <meta charset="utf-8" />
    <title>Smart Ecom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Admin Panel" />
    <meta name="author" content="Daiyan" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('admin/assets/images/favicon.ico')}}">

    <!-- Vendor css (Require in all Page) -->
    <link href="{{asset('admin/assets/css/vendor.min.css')}}" rel="stylesheet" type="text/css" />

    <!-- Icons css (Require in all Page) -->
    <link href="{{asset('admin/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />

    <!-- App css (Require in all Page) -->
    <link href="{{asset('admin/assets/css/app.css')}}" rel="stylesheet" type="text/css" />

    <!-- Theme Config js (Require in all Page) -->
    <script src="{{asset('admin/assets/js/config.js')}}"></script>
</head>

<body>

@yield('app_content')

<!-- Vendor Javascript (Require in all Page) -->
<script src="{{asset('admin/assets/js/vendor.js')}}"></script>

<!-- App Javascript (Require in all Page) -->
<script src="{{asset('admin/assets/js/app.js')}}"></script>

<!-- Vector Map Js -->
<script src="{{asset('admin/assets/vendor/jsvectormap/js/jsvectormap.min.js')}}"></script>
<script src="{{asset('admin/assets/vendor/jsvectormap/maps/world-merc.js')}}"></script>
<script src="{{asset('admin/assets/vendor/jsvectormap/maps/world.js')}}"></script>

<!-- Dashboard Js -->
<script src="{{asset('admin/assets/js/pages/dashboard.js')}}"></script>

</body>

</html>
