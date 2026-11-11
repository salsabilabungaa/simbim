<!DOCTYPE html>
<html lang="id">
<head>
    <base href="{{ url('/') }}/">
    <title>@yield('title', 'Portal GKM') — SIMBIM</title>
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
        @if (auth()->check() && auth()->user()->isAdmin())
            <div class="bg-dark text-white px-6 py-2 d-flex flex-wrap align-items-center justify-content-between shadow-sm">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge badge-danger fw-bolder fs-9 px-2 py-1">HAK AKSES GLOBAL ADMIN</span>
                    <span class="fs-8">Anda sedang meninjau <strong>Portal GKM (Kendali Mutu)</strong> dalam mode Administrator.</span>
                </div>
                <a href="{{ route('admin.beranda') }}" class="btn btn-xs btn-primary py-1 px-3 fs-8 fw-bold">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Admin Panel
                </a>
            </div>
        @endif

        <div class="page d-flex flex-row flex-column-fluid">
            <!-- Aside GKM -->
            <div id="kt_aside" class="aside aside-dark aside-hoverable" data-kt-drawer="true" data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '255px'}" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_mobile_toggle">
                <div class="aside-logo flex-column-auto" id="kt_aside_logo">
                    <a href="{{ route('gkm.beranda') }}" class="d-flex align-items-center">
                        <img alt="SIMBIM" src="{{ asset('assets/media/logos/logo-1-dark.svg') }}" class="h-25px logo me-2" />
                        <span class="badge badge-light-info fw-bolder fs-8 px-2 py-1">GKM</span>
                    </a>
                    <div id="kt_aside_toggle" class="btn btn-icon w-auto px-0 btn-active-color-primary aside-toggle" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="aside-minimize">
                        <span class="svg-icon svg-icon-1 rotate-180">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.5" d="M14.2657 11.4343L18.45 7.25C18.8642 6.83579 18.8642 6.16421 18.45 5.75C18.0358 5.33579 17.3642 5.33579 16.95 5.75L11.4071 11.2929C11.0166 11.6834 11.0166 12.3166 11.4071 12.7071L16.95 18.25C17.3642 18.6642 18.0358 18.6642 18.45 18.25C18.8642 17.8358 18.8642 17.1642 18.45 16.75L14.2657 12.5657C13.9533 12.2533 13.9533 11.7467 14.2657 11.4343Z" fill="black" />
                                <path d="M8.2657 11.4343L12.45 7.25C12.8642 6.83579 12.8642 6.16421 12.45 5.75C12.0358 5.33579 11.3642 5.33579 10.95 5.75L5.40712 11.2929C5.01659 11.6834 5.01659 12.3166 5.40712 12.7071L10.95 18.25C11.3642 18.6642 12.0358 18.6642 12.45 18.25C12.8642 17.8358 12.8642 17.1642 12.45 16.75L8.2657 12.5657C7.95328 12.2533 7.95328 11.7467 8.2657 11.4343Z" fill="black" />
                            </svg>
                        </span>
                    </div>
                </div>

                <div class="aside-menu flex-column-fluid">
                    <div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu" data-kt-scroll-offset="0">
                        <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500" id="kt_aside_menu" data-kt-menu="true">
                            <div class="menu-item">
                                <div class="menu-content pt-4 pb-2">
                                    <span class="menu-section text-muted text-uppercase fs-8 ls-1">Kendali Mutu &amp; Perwalian</span>
                                </div>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link {{ request()->routeIs('gkm.beranda*') ? 'active' : '' }}" href="{{ route('gkm.beranda') }}">
                                    <span class="menu-icon"><i class="fas fa-chart-pie fs-3 text-info"></i></span>
                                    <span class="menu-title">Dasbor Rekap Perwalian</span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link {{ request()->routeIs('gkm.riwayat-konsultasi*') ? 'active' : '' }}" href="{{ route('gkm.riwayat-konsultasi') }}">
                                    <span class="menu-icon"><i class="fas fa-clipboard-list fs-3 text-primary"></i></span>
                                    <span class="menu-title">Riwayat &amp; Log Konsultasi</span>
                                    <span class="badge badge-light-secondary fs-9 fw-bold ms-1">View</span>
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
                        <div class="d-flex align-items-center d-lg-none ms-n2 me-2" title="Menu">
                            <div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px" id="kt_aside_mobile_toggle">
                                <span class="svg-icon svg-icon-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z" fill="black" />
                                        <path opacity="0.3" d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z" fill="black" />
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
                            <a href="{{ route('gkm.beranda') }}" class="d-lg-none">
                                <img alt="Logo" src="{{ asset('assets/media/logos/logo-2.svg') }}" class="h-30px" />
                            </a>
                        </div>

                        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
                            <div class="d-flex align-items-center">
                                <span class="badge badge-light-info fw-bolder fs-8 px-3 py-2 me-3">
                                    <i class="fas fa-shield-alt text-info me-1"></i> GUGUS KENDALI MUTU
                                </span>
                                <span class="text-gray-700 fw-bold fs-7 d-none d-md-inline">
                                    Prodi PSTI &mdash; {{ config('simbim.universitas') }}
                                </span>
                            </div>

                            <div class="d-flex align-items-stretch flex-shrink-0">
                                <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                                    <div class="cursor-pointer d-flex align-items-center px-2 py-1 text-hover-primary" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                                        <div class="symbol symbol-30px symbol-circle me-2 bg-light-info text-info fw-bolder d-flex align-items-center justify-content-center">
                                            {{ strtoupper(substr(auth()->user()->name ?? 'G', 0, 1)) }}
                                        </div>
                                        <span class="text-gray-700 fw-bold fs-7 text-uppercase ls-1 me-1 d-none d-sm-inline">{{ auth()->user()->name ?? 'Tim GKM' }}</span>
                                        <i class="fas fa-chevron-down fs-9 text-gray-500"></i>
                                    </div>

                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px" data-kt-menu="true">
                                        <div class="menu-item px-3">
                                            <div class="menu-content d-flex align-items-center px-3">
                                                <div class="symbol symbol-50px symbol-circle me-4 bg-light-info text-info fw-bolder fs-2 d-flex align-items-center justify-content-center">
                                                    {{ strtoupper(substr(auth()->user()->name ?? 'G', 0, 1)) }}
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <div class="fw-bolder fs-6 text-dark">
                                                        {{ auth()->user()->name ?? 'Tim GKM' }}
                                                    </div>
                                                    <span class="badge badge-light-info fw-bolder fs-9 px-2 py-1 my-1 align-self-start">GKM (Kendali Mutu)</span>
                                                    <span class="fw-semibold text-muted fs-8 text-truncate" style="max-width: 160px;">{{ auth()->user()->email ?? '' }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="separator my-2"></div>

                                        <div class="menu-item px-5">
                                            <form method="POST" action="{{ route('logout') }}" id="gkm_logout_form">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-light-danger w-100 fw-bolder">
                                                    <i class="fas fa-sign-out-alt me-2"></i> Keluar (Logout)
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                            <span class="text-muted fs-8 ms-2">— Portal GKM Prodi PSTI, {{ config('simbim.universitas') }}</span>
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
