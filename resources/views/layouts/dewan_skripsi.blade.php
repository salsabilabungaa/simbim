<!DOCTYPE html>
<html lang="id">
<head>
    <base href="{{ url('/') }}/">
    <title>@yield('title', 'Portal Dewan Skripsi') — SIMBIM</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    @stack('styles')
</head>
<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled aside-fixed" style="--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px">
    <div class="d-flex flex-column flex-root">
        <!-- Global Admin Access Banner -->
        @if (auth()->check() && auth()->user()->isAdmin())
            <div class="bg-dark text-white px-6 py-2 d-flex flex-wrap align-items-center justify-content-between shadow-sm">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge badge-danger fw-bolder fs-9 px-2 py-1">HAK AKSES GLOBAL ADMIN</span>
                    <span class="fs-8">Anda sedang meninjau <strong>Portal Dewan Skripsi</strong> dalam mode Administrator.</span>
                </div>
                <a href="{{ route('admin.beranda') }}" class="btn btn-xs btn-primary py-1 px-3 fs-8 fw-bold">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Admin Panel
                </a>
            </div>
        @endif

        <div class="page d-flex flex-row flex-column-fluid">
            <!-- Simple Aside for Dewan Skripsi -->
            <div id="kt_aside" class="aside aside-dark aside-hoverable" data-kt-drawer="true" data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_mobile_toggle">
                <div class="aside-logo flex-column-auto" id="kt_aside_logo">
                    <a href="{{ route('dewan-skripsi.beranda') }}" class="d-flex align-items-center">
                        <img alt="SIMBIM" src="{{ asset('assets/media/logos/logo-1-dark.svg') }}" class="h-25px logo me-2" />
                        <span class="badge badge-light-warning fw-bolder fs-8">DEWAN</span>
                    </a>
                </div>
                <div class="aside-menu flex-column-fluid">
                    <div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true" data-kt-scroll-height="auto">
                        <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500" id="kt_aside_menu" data-kt-menu="true">
                            <div class="menu-item">
                                <a class="menu-link active" href="{{ route('dewan-skripsi.beranda') }}">
                                    <span class="menu-icon"><i class="fas fa-balance-scale fs-3"></i></span>
                                    <span class="menu-title">Beranda Dewan Skripsi</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                <!-- Header -->
                <div id="kt_header" class="header align-items-stretch">
                    <div class="container-fluid d-flex align-items-stretch justify-content-between">
                        <div class="d-flex align-items-center">
                            <span class="badge badge-light-warning fw-bolder fs-7 px-3 py-2 me-3">
                                <i class="fas fa-balance-scale text-warning me-1"></i> DEWAN SKRIPSI
                            </span>
                            <span class="text-gray-700 fw-bold fs-7 d-none d-md-inline">Portal Komisi & Dewan Penguji Skripsi {{ config('simbim.prodi_singkat') }} &mdash; {{ config('simbim.universitas') }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <form method="POST" action="{{ route('logout') }}" id="dewan_logout_form">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-light-danger fw-bold">
                                    <i class="fas fa-sign-out-alt me-1"></i> Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post d-flex flex-column-fluid pt-5" id="kt_post">
                        <div id="kt_content_container" class="container-xxl">
                            @yield('content')
                        </div>
                    </div>
                </div>

                <div class="footer py-4 d-flex flex-lg-column" id="kt_footer">
                    <div class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between">
                        <div class="text-dark order-2 order-md-1">
                            <span class="text-muted fw-bold me-1">{{ date('Y') }}&copy;</span>
                            <span class="text-gray-800 fw-bold">SIMBIM</span>
                            <span class="text-muted fs-8 ms-2">— Portal Dewan Skripsi {{ config('simbim.prodi_singkat') }} &bull; {{ config('simbim.universitas_singkat') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>var hostUrl = "{{ asset('assets') }}/";</script>
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/simbim-ui.js') }}"></script>
    @stack('scripts')
</body>
</html>

