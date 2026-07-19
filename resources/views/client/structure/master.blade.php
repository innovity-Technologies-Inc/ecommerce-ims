<!DOCTYPE html>
<html lang="en">
<head>
    @php
        $gs = \App\HelperClass::generalSettings();
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $gs->business_name ?? 'Smart E-commerce' }}</title>
    <meta name="title" content="{{ $gs->meta_title ?? '' }}">
    <meta name="description" content="{{ $gs->meta_description ?? '' }}">
    <meta name="author" content="{{ $gs->business_name ?? '' }}">
    
    {!! NoCaptcha::renderJs() !!}

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ $gs->favicon ? \App\HelperClass::file_url($gs->favicon) : asset('client/assets/images/favicon/favicon.png') }}">

    <!-- Google Fonts -->
    <link href="../../css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{asset('client/assets/css/vendor/vendor.min.css')}}">
    <link rel="stylesheet" href="{{asset('client/assets/css/plugins/plugins.min.css')}}">
    <link rel="stylesheet" href="{{asset('client/assets/css/style.css')}}?v={{ time() }}">
    <link rel="stylesheet" href="{{asset('client/assets/css/responsive.css')}}">

    {{--    Toastr --}}

    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet">
    {{--    <link href="{{ asset('assets/libs/toastr/toastr.css') }}" rel="stylesheet" type="text/css">--}}


    {{--    Filepond Css --}}

    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet" />

    {{--    <link href="{{ asset('assets/libs/filepond/filepond.css') }}" rel="stylesheet" type="text/css">--}}
    {{--    <link href="{{ asset('assets/libs/filepond/filepond-plugin-image-preview.css') }}" rel="stylesheet" type="text/css">--}}


    {{--    Select 2 Css --}}


    {{--    Summernote Css --}}
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

    {{--    <link href="{{asset('assets/libs/summernote/summernote-lite.min.css')}}" rel="stylesheet" type="text/css"> --}}

    <style>
        .filepond--credits {
            display: none !important;
            visibility: hidden;
            opacity: 0;
            height: 0;
            width: 0;
        }
    </style>

</head>

<body>

<div id="main">

    @yield('app_content')

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="{{asset('client/assets/js/vendor/vendor.min.js')}}"></script>
<script src="{{asset('client/assets/js/plugins/plugins.min.js')}}"></script>

<!-- Main Activation JS -->
<script src="{{asset('client/assets/js/main.js')}}"></script>

@stack('scripts')
@include('client.structure.partials.cart-scripts')
@include('client.structure.partials.whatsapp-widget')


{{-- Toastr --}} <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<script>
    @if (Session::has('message'))
    var type = "{{ Session::get('alert-type', 'info') }}"
    switch (type) {
        case 'info':
            toastr.options.timeOut = 3000;
            toastr.info("{{ Session::get('message') }}");
            break;

        case 'success':
            toastr.options.timeOut = 3000;
            toastr.success("{{ Session::get('message') }}");
            break;

        case 'warning':
            toastr.options.timeOut = 3000;
            toastr.warning("{{ Session::get('message') }}");
            break;

        case 'error':
            toastr.options.timeOut = 3000;
            toastr.error("{{ Session::get('message') }}");
            break;
    }
    @endif
</script>


{{-- Sweet Alert --}}
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
    $('.confirmDelete').click(function(event) {
        event.preventDefault();
        const form = $(this).closest("form");


        Swal.fire({
            title: 'Are you sure you want to delete?',
            text: 'You won\'t be able to revert!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirm'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();


            } else if (result.isDismissed) {
                console.log('Deletion canceled');
            }
        }).catch((error) => {
            console.error('Error:', error);
        });
    });
</script>


{{-- Select 2 Js --}}


{{-- Filepond Js --}}
 <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>


 <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>

<!-- Image Resize -->
 <script src="https://unpkg.com/filepond-plugin-image-resize/dist/filepond-plugin-image-resize.min.js"></script>

<!-- Image Transform -->
 <script src="https://unpkg.com/filepond-plugin-image-transform/dist/filepond-plugin-image-transform.min.js"></script>

<script>
    // Register needed FilePond plugins globally
    FilePond.registerPlugin(
        FilePondPluginImagePreview,
        FilePondPluginImageResize,
        FilePondPluginImageTransform,
    );


    // compression version
    document.querySelectorAll('input.filepond').forEach(input => {
        if (!input.filePondInstance) {
            input.filePondInstance = FilePond.create(input, {
                storeAsFile: true,
                instantUpload: false,
                labelIdle: 'Drag & Drop or <span class="filepond--label-action">Browse</span>',

                // ✅ Compression settings
                imageCompress: true,
                imageCompressQuality: 0.8, // 0–1 (1 = no compression)
                imageCompressMaxWidth: 1920,
                imageCompressMaxHeight: 1080,
                imageCompressMode: 'automatic', // can be 'manual' or 'automatic'
                imageResizeMode: 'contain', // keep aspect ratio


                // 👇 Force image format
                imageCompressOutputMimeType: 'image/webp', // can be 'image/png', 'image/jpeg', etc.
                imageCompressOutputQuality: 0.7, // 0–1
                imageCompressConvertSize: 0
            });
        }
    });


    // Init for original version (NO compression)
    document.querySelectorAll('input.filepond_org').forEach(input => {
        if (!input.filePondInstance) {
            input.filePondInstance = FilePond.create(input, {
                storeAsFile: true,
                instantUpload: false,
                labelIdle: 'Drag & Drop or <span class="filepond--label-action">Browse</span>',
                allowImageTransform: false // ❌ disables compression for this instance
            });
        }
    });
</script>

{{-- Summernote JS --}}
 <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<script>
    $(document).ready(function() {
        $('#editor1').summernote({
            toolbar: [
                // ['style', ['style']], // optional
                ['font', ['fontsize', 'bold', 'italic', 'underline', 'clear',
                    'color'
                ]], // remove 'fontname',
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['codeview']]
            ],
            // Optional: Set your desired height
            height: 200,
        });

        $('#editor2').summernote({
            toolbar: [
                // ['style', ['style']], // optional
                ['font', ['fontsize', 'bold', 'italic', 'underline', 'clear',
                    'color'
                ]], // remove 'fontname',
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['codeview']]
            ],
            // Optional: Set your desired height
            height: 200,
        });

        $('#editor3').summernote({
            toolbar: [
                // ['style', ['style']], // optional
                ['font', ['fontsize', 'bold', 'italic', 'underline', 'clear',
                    'color'
                ]], // remove 'fontname',
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['codeview']]
            ],
            // Optional: Set your desired height
            height: 200,
        });
    });
</script>

    <form id="wishlist-form" action="{{ route('user.wishlist.store') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="product_id" id="wishlist-product-id">
    </form>

    <script>
        function addToWishlist(productId) {
            document.getElementById('wishlist-product-id').value = productId;
            document.getElementById('wishlist-form').submit();
        }

        $(document).on('change', '.toggle-password-visibility', function() {
            const isChecked = $(this).is(':checked');
            const targetForm = $(this).closest('form');
            // Select both current password inputs and those we've temporarily changed to type="text"
            const passwordInputs = targetForm.length ? 
                targetForm.find('input[type="password"], input[data-password-original="true"]') : 
                $('input[type="password"], input[data-password-original="true"]');
            
            passwordInputs.each(function() {
                if (isChecked) {
                    $(this).attr('type', 'text').attr('data-password-original', 'true');
                } else {
                    $(this).attr('type', 'password');
                }
            });
        });
    </script>

    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
</body>
</html>
