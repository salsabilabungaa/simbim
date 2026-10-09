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
            <a href="{{ route('mahasiswa.beranda') }}" class="d-lg-none">
                <img alt="Logo" src="{{ asset('assets/media/logos/logo-2.svg') }}" class="h-30px" />
            </a>
        </div>

        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
            <div class="d-flex align-items-center" id="kt_header_nav">
            </div>

            <div class="d-flex align-items-stretch flex-shrink-0">
                <!-- Tombol Notifikasi Samping Profile -->
                <div class="d-flex align-items-center ms-1 ms-lg-3">
                    <div class="btn btn-icon btn-icon-muted btn-active-light btn-active-color-primary position-relative w-30px h-30px w-md-40px h-md-40px" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end" title="Notifikasi">
                        <i class="fas fa-bell fs-2"></i>
                        <span class="bullet bullet-dot bg-success h-6px w-6px position-absolute translate-middle top-0 start-50 animation-blink"></span>
                    </div>
                    <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px" data-kt-menu="true">
                        <div class="d-flex flex-column bgi-no-repeat rounded-top p-6" style="background: linear-gradient(135deg, #1e1e2d 0%, #2b2b40 100%);">
                            <h3 class="text-white fw-bolder mb-1 fs-5">
                                <i class="fas fa-bell text-warning me-2"></i> Notifikasi
                            </h3>
                            <span class="text-gray-400 fs-7">{{ count($notifikasi) }} pemberitahuan terbaru</span>
                        </div>
                        <div class="scroll-y mh-325px my-3 px-6">
                            @foreach ($notifikasi as $item)
                                <div class="d-flex align-items-center py-3 border-bottom border-gray-100">
                                    <div class="symbol symbol-35px me-3">
                                        <span class="symbol-label bg-light-primary">
                                            <i class="fas fa-info-circle text-primary fs-5"></i>
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column flex-grow-1">
                                        <a href="#" class="fs-7 text-dark text-hover-primary fw-bolder">{{ $item['judul'] }}</a>
                                        <span class="text-muted fs-8">{{ $item['waktu'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                    <div class="cursor-pointer d-flex align-items-center px-2 py-1 text-hover-primary" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <span class="text-gray-700 fw-bold fs-7 text-uppercase ls-1">{{ $mahasiswa['nama'] }}</span>
                    </div>
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px" data-kt-menu="true">
                        <div class="menu-item px-3">
                            <div class="menu-content d-flex flex-column px-3 py-2">
                                <div class="fw-bolder text-dark fs-6 mb-1">{{ $mahasiswa['nama'] }}</div>
                                <div class="text-muted fs-7 fw-bold mb-1">NIM: {{ $mahasiswa['nim'] }}</div>
                                <div class="badge badge-light-primary fw-bolder fs-8 align-self-start mt-1">
                                    <i class="fas fa-graduation-cap me-1 text-primary"></i> Program Studi {{ $mahasiswa['program_studi'] }}
                                </div>
                            </div>
                        </div>
                        <div class="separator my-2"></div>
                        <div class="menu-item px-5">
                            <a href="#" class="menu-link px-5">
                                <i class="fas fa-user-edit me-2 text-gray-600"></i> Edit Profile
                            </a>
                        </div>
                        <div class="menu-item px-5">
                            <a href="#" class="menu-link px-5 text-danger">
                                <i class="fas fa-sign-out-alt me-2 text-danger"></i> Sign Out
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
