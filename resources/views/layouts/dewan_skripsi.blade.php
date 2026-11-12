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
<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed aside-enabled aside-fixed">
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
            <!-- Aside for Dewan Skripsi -->
            <div id="kt_aside" class="aside aside-dark aside-hoverable" data-kt-drawer="true" data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_mobile_toggle">
                <div class="aside-logo flex-column-auto" id="kt_aside_logo">
                    <a href="{{ route('dewan-skripsi.beranda') }}" class="d-flex align-items-center">
                        <img alt="SIMBIM" src="{{ asset('assets/media/logos/logo-1-dark.svg') }}" class="h-25px logo me-2" />
                        <span class="badge badge-light-warning fw-bolder fs-8">DEWAN</span>
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
                    <div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true" data-kt-scroll-height="auto">
                        <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500" id="kt_aside_menu" data-kt-menu="true">
                            <div class="menu-item">
                                <div class="menu-content pt-4 pb-2">
                                    <span class="menu-section text-muted text-uppercase fs-8 ls-1">Menu Utama Dewan Skripsi</span>
                                </div>
                            </div>

                            <!-- 1. Dasbor Progres Skripsi -->
                            <div class="menu-item">
                                <a class="menu-link {{ request()->routeIs('dewan-skripsi.beranda*') || request()->routeIs('dewan-skripsi.dashboard*') ? 'active' : '' }}" href="{{ route('dewan-skripsi.beranda') }}">
                                    <span class="menu-icon"><i class="fas fa-chart-line fs-3 text-primary"></i></span>
                                    <span class="menu-title">Dasbor Progres Skripsi</span>
                                </a>
                            </div>

                            <!-- 2. Plotting Dosen & Jadwal -->
                            <div class="menu-item">
                                <a class="menu-link {{ request()->routeIs('dewan-skripsi.plotting*') ? 'active' : '' }}" href="{{ route('dewan-skripsi.plotting') }}">
                                    <span class="menu-icon"><i class="fas fa-user-friends fs-3 text-info"></i></span>
                                    <span class="menu-title">Plotting Dosen & Jadwal</span>
                                </a>
                            </div>

                            <!-- 3. Validasi Pendaftaran Sidang (Approval) -->
                            <div class="menu-item">
                                <a class="menu-link {{ request()->routeIs('dewan-skripsi.validasi-sidang*') ? 'active' : '' }}" href="{{ route('dewan-skripsi.validasi-sidang') }}">
                                    <span class="menu-icon"><i class="fas fa-clipboard-check fs-3 text-warning"></i></span>
                                    <span class="menu-title">Validasi Sidang (Approval)</span>
                                    @php
                                        $pendingApprovalCount = \App\Models\Skripsi::where('status_pendaftaran_sidang', 'Menunggu Persetujuan')->count();
                                    @endphp
                                    @if($pendingApprovalCount > 0)
                                        <span class="badge badge-warning badge-circle fw-bolder fs-8 ms-2">{{ $pendingApprovalCount }}</span>
                                    @endif
                                </a>
                            </div>

                            <!-- 4. Verifikasi Berkas Pasca-Sidang -->
                            <div class="menu-item">
                                <a class="menu-link {{ request()->routeIs('dewan-skripsi.verifikasi-pasca-sidang*') ? 'active' : '' }}" href="{{ route('dewan-skripsi.verifikasi-pasca-sidang') }}">
                                    <span class="menu-icon"><i class="fas fa-file-signature fs-3 text-success"></i></span>
                                    <span class="menu-title">Verifikasi Pasca-Sidang</span>
                                    @php
                                        $pendingRevisiCount = \App\Models\Skripsi::where('tahap', 'Revisi Final')->where('status_kelulusan', '!=', 'Lulus/Selesai')->count();
                                    @endphp
                                    @if($pendingRevisiCount > 0)
                                        <span class="badge badge-success badge-circle fw-bolder fs-8 ms-2">{{ $pendingRevisiCount }}</span>
                                    @endif
                                </a>
                            </div>

                            <div class="separator my-4 border-gray-700"></div>

                            <div class="menu-item">
                                <div class="menu-content pt-2 pb-2">
                                    <span class="menu-section text-muted text-uppercase fs-8 ls-1">Navigasi Terkait</span>
                                </div>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link" href="{{ route('mahasiswa.beranda') }}">
                                    <span class="menu-icon"><i class="fas fa-user-graduate fs-4 text-info"></i></span>
                                    <span class="menu-title text-info fw-bold">Panel Mahasiswa</span>
                                    <span class="badge badge-light-info fs-8 fw-bolder px-2 py-1">Lihat</span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <a class="menu-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                                    <span class="menu-icon"><i class="fas fa-user-edit fs-4 text-warning"></i></span>
                                    <span class="menu-title">Edit Profil Saya</span>
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
                        <div class="d-flex align-items-center gap-3">
                            @if(auth()->check())
                                <div class="d-none d-sm-flex flex-column text-end">
                                    <span class="text-gray-900 fw-bolder fs-7">{{ auth()->user()->name }}</span>
                                    <span class="text-muted fs-8">{{ auth()->user()->email }}</span>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-light-warning fw-bold">
                                    <i class="fas fa-user-edit me-1"></i> Edit Profil
                                </a>
                            @endif
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
                            <!-- Flash Alerts -->
                            @if (session('success'))
                                <div class="alert alert-dismissible bg-light-success border border-success d-flex flex-column flex-sm-row p-5 mb-6">
                                    <span class="svg-icon svg-icon-2hx svg-icon-success me-4 mb-5 mb-sm-0">
                                        <i class="fas fa-check-circle fs-2 text-success"></i>
                                    </span>
                                    <div class="d-flex flex-column pe-0 pe-sm-10 justify-content-center">
                                        <h5 class="mb-1 text-success fw-bolder">Sukses!</h5>
                                        <span class="text-gray-700 fs-7">{{ session('success') }}</span>
                                    </div>
                                    <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                                        <i class="fas fa-times fs-6 text-success"></i>
                                    </button>
                                </div>
                            @endif

                            @if (session('warning'))
                                <div class="alert alert-dismissible bg-light-warning border border-warning d-flex flex-column flex-sm-row p-5 mb-6">
                                    <span class="svg-icon svg-icon-2hx svg-icon-warning me-4 mb-5 mb-sm-0">
                                        <i class="fas fa-exclamation-triangle fs-2 text-warning"></i>
                                    </span>
                                    <div class="d-flex flex-column pe-0 pe-sm-10 justify-content-center">
                                        <h5 class="mb-1 text-warning fw-bolder">Perhatian</h5>
                                        <span class="text-gray-700 fs-7">{{ session('warning') }}</span>
                                    </div>
                                    <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                                        <i class="fas fa-times fs-6 text-warning"></i>
                                    </button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-dismissible bg-light-danger border border-danger d-flex flex-column flex-sm-row p-5 mb-6">
                                    <span class="svg-icon svg-icon-2hx svg-icon-danger me-4 mb-5 mb-sm-0">
                                        <i class="fas fa-times-circle fs-2 text-danger"></i>
                                    </span>
                                    <div class="d-flex flex-column pe-0 pe-sm-10 justify-content-center">
                                        <h5 class="mb-1 text-danger fw-bolder">Terdapat Kesalahan Input</h5>
                                        <ul class="mb-0 text-gray-700 fs-7 ps-3">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                                        <i class="fas fa-times fs-6 text-danger"></i>
                                    </button>
                                </div>
                            @endif

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
    <script src="{{ asset('assets/plugins/global/plugins.bundle.css') }}"></script>
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/simbim-ui.js') }}"></script>
    @stack('scripts')
</body>
</html>
