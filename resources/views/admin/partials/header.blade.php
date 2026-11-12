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
            <a href="{{ route('admin.beranda') }}" class="d-lg-none">
                <img alt="Logo" src="{{ asset('assets/media/logos/logo-2.svg') }}" class="h-30px" />
            </a>
        </div>

        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
            <div class="d-flex align-items-center" id="kt_header_nav">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <span class="badge badge-light-danger fs-8 fw-bolder px-3 py-2">
                        <i class="fas fa-user-shield me-1 text-danger"></i> ADMIN UTAMA
                    </span>
                    <span class="d-none d-md-inline-block text-gray-700 fw-bold fs-7">
                        SIMBIM Prodi PSTI &mdash; {{ config('simbim.universitas') }}
                    </span>
                </div>
            </div>

            <div class="d-flex align-items-stretch flex-shrink-0">
                <!-- User Profile Dropdown -->
                <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                    <div class="cursor-pointer d-flex align-items-center px-2 py-1 text-hover-primary" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <div class="symbol symbol-30px symbol-circle me-2 bg-light-danger text-danger fw-bolder d-flex align-items-center justify-content-center">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <span class="text-gray-700 fw-bold fs-7 text-uppercase ls-1 me-1">{{ auth()->user()->name ?? 'Administrator' }}</span>
                        <i class="fas fa-chevron-down fs-9 text-gray-500"></i>
                    </div>

                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px" data-kt-menu="true">
                        <div class="menu-item px-3">
                            <div class="menu-content d-flex align-items-center px-3">
                                <div class="symbol symbol-50px symbol-circle me-5 bg-light-danger text-danger fw-bolder fs-2 d-flex align-items-center justify-content-center">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="fw-bolder d-flex align-items-center fs-6 text-dark">
                                        {{ auth()->user()->name ?? 'Administrator' }}
                                    </div>
                                    <span class="badge badge-light-danger fw-bolder fs-9 px-2 py-1 my-1 align-self-start">Administrator</span>
                                    <span class="fw-semibold text-muted fs-8 text-truncate" style="max-width: 160px;">{{ auth()->user()->email ?? '' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="separator my-2"></div>

                        <div class="menu-item px-5">
                            <a href="{{ route('profile.edit') }}" class="menu-link px-5">
                                <i class="fas fa-user-edit me-2 text-primary fs-7"></i> Edit Profil
                            </a>
                        </div>

                        <div class="separator my-2"></div>

                        <div class="menu-item px-5">
                            <form method="POST" action="{{ route('logout') }}" id="admin_logout_form">
                                @csrf
                                <a href="javascript:void(0)" class="menu-link px-5 text-danger" onclick="document.getElementById('admin_logout_form').submit();">
                                    <i class="fas fa-sign-out-alt me-2 text-danger fs-7"></i> Keluar (Logout)
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

