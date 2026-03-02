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

    {{--    Toastr --}}

    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet">
    {{--    <link href="{{ asset('assets/libs/toastr/toastr.css') }}" rel="stylesheet" type="text/css">--}}


    {{--    Filepond Css --}}

    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet" />

    {{--    <link href="{{ asset('assets/libs/filepond/filepond.css') }}" rel="stylesheet" type="text/css">--}}
    {{--    <link href="{{ asset('assets/libs/filepond/filepond-plugin-image-preview.css') }}" rel="stylesheet" type="text/css">--}}


    {{--    Select 2 Css --}}

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    {{--    <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css">--}}


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

        .note-editor.note-airframe .note-editing-area .note-editable,
        .note-editor.note-frame .note-editing-area .note-editable {
            background: var(--bs-secondary-bg);
            color: var(--bs-body-color);
        }

        .note-toolbar {
            background: var(--bs-tertiary-bg);
            border-color: var(--bs-border-color);
        }

        .note-editor.note-frame {
            border-color: var(--bs-border-color);
        }

        .note-btn {
            background-color: var(--bs-secondary-bg);
            color: var(--bs-body-color);
            border-color: var(--bs-border-color);
        }

        .note-dropdown-menu {
            background-color: var(--bs-secondary-bg);
            border-color: var(--bs-border-color);
        }

        .note-dropdown-item {
            color: var(--bs-body-color);
        }

        .note-dropdown-item:hover {
            background-color: var(--bs-tertiary-bg);
        }

        .select2-container--bootstrap-5 .select2-selection--single {
            height: calc(1.5em + 0.75rem + 2px);
            /* Match Bootstrap form-control height */
            padding: 0.375rem 0.75rem;
            border: 1px solid var(--bs-border-color);
            /* Match Bootstrap border */
            border-radius: 0.375rem;
            /* Match Bootstrap border-radius */
            font-size: 1rem;
            line-height: 1.5;
            background: var(--bs-secondary-bg);
            color: var(--bs-body-color);
        }


        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
            height: calc(1.5em + 0.75rem + 2px);
            /* Align the arrow with the selection box */
            top: 50%;
            transform: translateY(-50%);
            right: 0.75rem;
        }


        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            line-height: 1.5;
            /* Center the selected text vertically */
            color: var(--bs-body-color);
        }


        .select2-container--bootstrap-5 .select2-dropdown {
            border-radius: 0.375rem;
            /* Match Bootstrap dropdown border-radius */
            border: 1px solid var(--bs-border-color);
            /* Match Bootstrap border */
            background-color: var(--bs-secondary-bg);
            color: var(--bs-body-color);
        }

        .select2-container--bootstrap-5 .select2-results__option {
            color: var(--bs-body-color);
        }

        .select2-container--bootstrap-5 .select2-results__option--highlighted {
            background-color: #108dff;
            /* Bootstrap primary color for highlighting */
            color: white;
        }

        .select2-container--bootstrap-5 .select2-search__field {
            background-color: var(--bs-secondary-bg);
            color: var(--bs-body-color);
            border-color: var(--bs-border-color);
        }

        .select2-container--bootstrap-5 .select2-selection--multiple {
            padding: 0.375rem 0.75rem;
            border: 1px solid var(--bs-border-color);
            border-radius: 0.375rem;
            font-size: 1rem;
            line-height: 1.5;
            background-color: var(--bs-secondary-bg);
            color: var(--bs-body-color);
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            background-color: var(--bs-tertiary-bg);
            border-color: var(--bs-border-color);
            color: var(--bs-body-color);
        }

        /* Content polish */
        .content-page {
            background: radial-gradient(1200px 400px at 20% -5%, rgba(16, 141, 255, 0.06), transparent 40%),
            radial-gradient(900px 300px at 110% 10%, rgba(99, 102, 241, 0.05), transparent 35%),
            var(--bs-body-bg);
        }

        .content-page .content {
            padding-top: 8px;
        }

        .content-page .content .container-fluid {
            padding-top: 0;
            margin-top: 0;
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


{{-- Toastr --}}
 <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

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
 <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script>
    $(document).ready(function() {
        // basic select2
        $('.select2_list').select2({
            width: '100%',
            theme: 'bootstrap-5',
        });

        // can add tags, select the typed word and press enter to add it to the list
        $('.list').select2({
            width: '100%',
            tags: true, // Allow new entries as tags
            tokenSeparators: [','],
            placeholder: "Choose One",
            theme: 'bootstrap-5',
        });
    });
</script>

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

</body>
</html>
