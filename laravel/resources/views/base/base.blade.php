<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8" />
    <title>{{ config('app.name', 'SIMBIM') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Font Google -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />

    <!-- Vendor Stylesheets -->
    <link href="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />

    <!-- Global Stylesheets Bundle (Wajib Metronic) -->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
</head>

<body id="kt_app_body" 
      data-kt-app-layout="dark-sidebar" 
      data-kt-app-header-fixed="true" 
      data-kt-app-sidebar-enabled="true" 
      data-kt-app-sidebar-fixed="true" 
      data-kt-app-sidebar-hoverable="true" 
      data-kt-app-sidebar-push-header="true" 
      data-kt-app-sidebar-push-toolbar="true" 
      data-kt-app-sidebar-push-footer="true" 
      data-kt-app-toolbar-enabled="true" 
      class="app-default">

    <!-- Content dari Master Layout -->
    @yield('content')

    <!-- Global Javascript Bundle (Wajib Metronic) -->
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}"></script>
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
</body>
</html>