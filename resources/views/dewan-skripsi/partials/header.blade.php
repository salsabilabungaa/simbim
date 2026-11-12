<div id="kt_header" class="header align-items-stretch">
    <div class="container-fluid d-flex align-items-stretch justify-content-between">
        <!-- Mobile Toggle -->
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
            <a href="{{ route('dewan-skripsi.dashboard') }}" class="d-lg-none">
                <img alt="Logo" src="{{ asset('assets/media/logos/logo-2.svg') }}" class="h-30px" />
            </a>
        </div>

        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
            <div class="d-flex align-items-center" id="kt_header_nav">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bolder fs-4 flex-column justify-content-center my-0">
                        @yield('page_title', 'Dewan Skripsi')
                    </h1>
                    <span class="text-muted fs-8 fw-bold">Koordinator & Dewan Sidang Skripsi Informatika USK</span>
                </div>
            </div>

            <div class="d-flex align-items-stretch flex-shrink-0">
                <!-- Tombol Beralih ke Mahasiswa -->
                <div class="d-flex align-items-center me-2 me-lg-4">
                    <a href="{{ route('mahasiswa.beranda') }}" class="btn btn-sm btn-light-info fw-bolder d-flex align-items-center">
                        <i class="fas fa-graduation-cap me-1.5 text-info"></i>
                        <span>Beralih ke Mahasiswa</span>
                    </a>
                </div>

                <!-- Profile Dewan Skripsi -->
                <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                    <div class="cursor-pointer d-flex align-items-center px-2 py-1 text-hover-primary" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <div class="symbol symbol-30px me-2">
                            <img src="{{ asset('assets/media/avatars/300-1.jpg') }}" alt="Dewan Skripsi" class="rounded-circle" style="width: 30px; height: 30px; object-fit: cover;" />
                        </div>
                        <div class="d-none d-md-flex flex-column text-start me-2">
                            <span class="text-gray-800 fw-bold fs-7">{{ $dewanUser->name ?? 'Dr. Ir. Taufik Hidayat, M.Kom.' }}</span>
                            <span class="badge badge-light-warning fw-bold fs-9 align-self-start">Dewan Skripsi</span>
                        </div>
                        <i class="fas fa-chevron-down fs-9 text-muted ms-1"></i>
                    </div>

                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px" data-kt-menu="true">
                        <div class="menu-item px-3">
                            <div class="menu-content d-flex flex-column px-3 py-2">
                                <div class="fw-bolder text-dark fs-6 mb-1">{{ $dewanUser->name ?? 'Dr. Ir. Taufik Hidayat, M.Kom.' }}</div>
                                <div class="text-muted fs-7 fw-bold mb-1">NIP: {{ $dewanUser->nip ?? '197803152005011002' }}</div>
                                <div class="badge badge-light-warning fw-bolder fs-8 align-self-start mt-1">
                                    <i class="fas fa-user-shield me-1 text-warning"></i> Role: Dewan Skripsi
                                </div>
                            </div>
                        </div>
                        <div class="separator my-2"></div>
                        <div class="menu-item px-5">
                            <a href="{{ route('dewan-skripsi.dashboard') }}" class="menu-link px-5">
                                <i class="fas fa-chart-line me-2 text-primary"></i> Dasbor Progres
                            </a>
                        </div>
                        <div class="menu-item px-5">
                            <a href="{{ route('dewan-skripsi.plotting') }}" class="menu-link px-5">
                                <i class="fas fa-user-friends me-2 text-info"></i> Plotting Dosen & Jadwal
                            </a>
                        </div>
                        <div class="menu-item px-5">
                            <a href="{{ route('dewan-skripsi.validasi-sidang') }}" class="menu-link px-5">
                                <i class="fas fa-clipboard-check me-2 text-warning"></i> Validasi Sidang
                            </a>
                        </div>
                        <div class="menu-item px-5">
                            <a href="{{ route('dewan-skripsi.verifikasi-pasca-sidang') }}" class="menu-link px-5">
                                <i class="fas fa-file-signature me-2 text-success"></i> Verifikasi Pasca-Sidang
                            </a>
                        </div>
                        <div class="separator my-2"></div>
                        <div class="menu-item px-5">
                            <a href="{{ route('profile.edit') }}" class="menu-link px-5">
                                <i class="fas fa-user-edit me-2 text-warning"></i> Edit Profil
                            </a>
                        </div>
                        <div class="menu-item px-5">
                            <a href="{{ route('mahasiswa.beranda') }}" class="menu-link px-5 text-info">
                                <i class="fas fa-user-graduate me-2 text-info"></i> Buka Tampilan Mahasiswa
                            </a>
                        </div>
                        <div class="separator my-2"></div>
                        <div class="menu-item px-5">
                            <form method="POST" action="{{ route('logout') }}" id="logout-form-dewan-header">
                                @csrf
                                <a href="javascript:void(0)" onclick="document.getElementById('logout-form-dewan-header').submit();" class="menu-link px-5 text-danger">
                                    <i class="fas fa-sign-out-alt me-2 text-danger"></i> Keluar
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

