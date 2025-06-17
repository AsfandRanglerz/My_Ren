<!DOCTYPE html>
<html lang="en">
<!-- index.html  21 Nov 2019 03:44:50 GMT -->

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>@yield('title')</title>
    <!-- Developed By Ranglerz -->
    <link rel="stylesheet"
        href="https://www.ranglerz.com/cost-to-make-a-web-ios-or-android-app-and-how-long-does-it-take.php">
    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('public/admin/assets/css/app.min.css') }}">
    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('public/admin/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('public/admin/assets/css/components.css') }}">
    <!-- Custom style CSS -->
    <link rel="stylesheet" href="{{ asset('public/admin/assets/toastr/css/toastr.css') }}">
    <link rel="stylesheet" href="{{ asset('public/admin/assets/css/custom.css') }}">
    <link rel='shortcut icon' type='image/x-icon' href='{{ asset('public/admin/assets/img/logo.png') }}' />
    <link rel="stylesheet" href="{{ asset('public/admin/assets/css/datatables.css') }}">

    {{-- toasr css --}}

    <link rel="stylesheet" href="{{ asset('public/admin/assets/toastr/toastr.css') }}">

    <link rel="stylesheet"
        href="{{ asset('public/admin/assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/admin/assets/bundles/datatables/datatables.min.css') }}">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
    <div class="loader"></div>

    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            @include('admin.common.header')
            @include('admin.common.side_menu')
            @yield('content')
            @include('admin.common.footer')
        </div>
    </div>
    <!-- General JS Scripts -->
    <script src="{{ asset('public/admin/assets/js/app.min.js') }}"></script>
    <!-- JS Libraies -->
    <script src="{{ asset('public/admin/assets/bundles/apexcharts/apexcharts.min.js') }}"></script>
    <!-- Page Specific JS File -->
    <script src="{{ asset('public/admin/assets/js/page/index.js') }}"></script>
    <!-- Template JS File -->
    <script src="{{ asset('public/admin/assets/js/scripts.js') }}"></script>
    <!-- Custom JS File -->
    <script src="{{ asset('public/admin/assets/js/custom.js') }}"></script>
    <script src="{{ asset('public/admin/assets/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('public/admin/assets/js/datatables.js') }}"></script>

    <script src="{{ asset('public/admin/assets/bundles/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('public/admin/assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}">
    </script>
    <script src="{{ asset('public/admin/assets/js/page/datatables.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="{{ asset('public/admin/assets/toastr/toastr.js') }}"></script>
    <script>
        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "3000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        @if (Session::has('success'))
            toastr.success("{{ Session::get('success') }}");
        @endif

        @if (Session::has('error'))
            toastr.error("{{ Session::get('error') }}");
        @endif

        @if (Session::has('info'))
            toastr.info("{{ Session::get('info') }}");
        @endif

        @if (Session::has('warning'))
            toastr.warning("{{ Session::get('warning') }}");
        @endif
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>
    @yield('js')
</body>


<!-- index.html  21 Nov 2019 03:47:04 GMT -->

</html>
