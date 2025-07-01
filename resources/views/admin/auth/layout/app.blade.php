<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    <!-- 🧩 External CSS (CDNs) -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">


    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- Favicon -->
    <link rel='shortcut icon' type='image/x-icon' href='{{ asset('public/admin/assets/img/favicon2.png') }}' />




    <!-- 🧩 Core & Template CSS -->
    <link rel="stylesheet" href="{{ asset('public/admin/assets/css/app.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/admin/assets/bundles/bootstrap-social/bootstrap-social.css') }}">
    <link rel="stylesheet" href="{{ asset('public/admin/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('public/admin/assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('public/admin/assets/css/custom.css') }}">

    <!-- 📊 DataTables CSS -->
    <link rel="stylesheet" href="{{ asset('public/admin/assets/css/datatables.css') }}">
    <link rel="stylesheet"
        href="{{ asset('public/admin/assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/admin/assets/bundles/datatables/datatables.min.css') }}">

    <!-- 🔔 Toastr CSS -->
    <link rel="stylesheet" href="{{ asset('public/admin/assets/toastr/css/toastr.css') }}">

    <!-- 🔗 Favicon -->
    <link rel='shortcut icon' type='image/x-icon' href='{{ asset('public/admin/assets/img/logo2.png') }}' />

    @yield('style')
</head>

<body>
    <div class="loader"></div>
    <div id="app">
        @yield('content')
    </div>


    <!-- 🧩 External JS (CDNs) -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <!-- 🔧 Core JS -->
    <script src="{{ asset('public/admin/assets/js/app.min.js') }}"></script>

    <!-- 📊 Charts -->
    <script src="{{ asset('public/admin/assets/bundles/apexcharts/apexcharts.min.js') }}"></script>

    <!-- 📊 DataTables -->
    <script src="{{ asset('public/admin/assets/js/datatables.js') }}"></script>
    <script src="{{ asset('public/admin/assets/bundles/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('public/admin/assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}">
    </script>
    <script src="{{ asset('public/admin/assets/js/page/datatables.js') }}"></script>

    <!-- 🧩 Template & Custom JS -->
    <script src="{{ asset('public/admin/assets/js/page/index.js') }}"></script>
    <script src="{{ asset('public/admin/assets/js/scripts.js') }}"></script>
    <script src="{{ asset('public/admin/assets/js/custom.js') }}"></script>

    <!-- 🔔 Toastr JS -->
    <script src="{{ asset('public/admin/assets/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('public/admin/assets/toastr/toastr.js') }}"></script>



    <!-- 🧾 Toastr Notification Handling -->
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

    @yield('js')
</body>

</html>
