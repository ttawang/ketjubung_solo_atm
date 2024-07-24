<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="bootstrap material admin template">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Ketjubung Solo</title>

    <link rel="apple-touch-icon" href="{{ asset('img/apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/favicon-white.ico') }}">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-extend.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/site.min.css') }}">

    <!-- Plugins -->
    <link rel="stylesheet" href="{{ asset('css/animsition.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/asScrollable.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/switchery.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/introjs.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/slidePanel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/flag-icon.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/waves.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jquery-confirm.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    @if ($isLogin ?? false)
        <link rel="stylesheet" href="{{ asset('css/login/login.css') }}">
    @endif

    @if ($isDatatable ?? false)
        {{-- <link rel="stylesheet" href="{{ asset('css/multi-select.css') }}"> --}}
        <link rel="stylesheet" href="{{ asset('css/datatables/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/datatables/dataTables.fixedheader.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/datatables/dataTables.fixedcolumns.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/datatables/dataTables.rowgroup.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/datatables/dataTables.scroller.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/datatables/dataTables.responsive.bootstrap4.min.css') }}">
        <style type="text/css">
            .table {
                width: 100% !important;
            }

            .modal-body {
                max-height: 500px;
                overflow-y: auto;
            }
        </style>
    @endif

    <style type="text/css">
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .spin {
            animation-name: spin;
            animation-duration: 4000ms;
            animation-iteration-count: infinite;
            animation-timing-function: linear;
        }

        .text-primary {
            color: #009688 !important;
        }

        a.text-primary:focus,
        a.text-primary:hover {
            color: #005850 !important;
        }

        .confirmation {
            border-color: #009688;
            box-shadow: none;
        }

        .confirmation-not-match {
            border-color: #960000;
            box-shadow: none;
        }
    </style>

    @yield('css')

    <!-- Fonts -->
    <link rel="stylesheet" href="{{ asset('css/material-design.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/brand-icons.min.css') }}">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto:400,500,600,400italic'>
    <script src="{{ asset('js/breakpoints.min.js') }}"></script>
    <script>
        Breakpoints();
    </script>

</head>
