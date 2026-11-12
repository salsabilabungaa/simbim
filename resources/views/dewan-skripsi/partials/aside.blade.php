<div id="kt_aside" class="aside aside-dark aside-hoverable" data-kt-drawer="true" data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_mobile_toggle">
    <div class="aside-logo flex-column-auto" id="kt_aside_logo">
        <a href="{{ route('dewan-skripsi.dashboard') }}" class="d-flex align-items-center">
            <img alt="SIMBIM" src="{{ asset('assets/media/logos/logo-1-dark.svg') }}" class="h-25px logo" />
            <span class="badge badge-light-warning fw-bolder fs-9 ms-2 px-2 py-1">DEWAN SKRIPSI</span>
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
                        <span class="menu-section text-muted text-uppercase fs-8 ls-1">Menu Utama Dewan Skripsi</span>
                    </div>
                </div>

                <!-- 1. Dasbor Progres Skripsi -->
                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('dewan-skripsi.dashboard*') ? 'active' : '' }}" href="{{ route('dewan-skripsi.dashboard') }}">
                        <span class="menu-icon">
                            <i class="fas fa-chart-line fs-3 text-primary"></i>
                        </span>
                        <span class="menu-title">Dasbor Progres Skripsi</span>
                    </a>
                </div>

                <!-- 2. Plotting Dosen & Jadwal -->
                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('dewan-skripsi.plotting*') ? 'active' : '' }}" href="{{ route('dewan-skripsi.plotting') }}">
                        <span class="menu-icon">
                            <i class="fas fa-user-friends fs-3 text-info"></i>
                        </span>
                        <span class="menu-title">Plotting Dosen & Jadwal</span>
                    </a>
                </div>

                <!-- 3. Validasi Pendaftaran Sidang -->
                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('dewan-skripsi.validasi-sidang*') ? 'active' : '' }}" href="{{ route('dewan-skripsi.validasi-sidang') }}">
                        <span class="menu-icon">
                            <i class="fas fa-clipboard-check fs-3 text-warning"></i>
                        </span>
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
                        <span class="menu-icon">
                            <i class="fas fa-file-signature fs-3 text-success"></i>
                        </span>
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

                <!-- Navigasi Beralih Peran -->
                <div class="menu-item">
                    <div class="menu-content pt-2 pb-2">
                        <span class="menu-section text-muted text-uppercase fs-8 ls-1">Beralih Tampilan</span>
                    </div>
                </div>

                <div class="menu-item">
                    <a class="menu-link" href="{{ route('mahasiswa.beranda') }}">
                        <span class="menu-icon">
                            <i class="fas fa-user-graduate fs-4 text-info"></i>
                        </span>
                        <span class="menu-title text-info fw-bold">Panel Mahasiswa</span>
                        <span class="badge badge-light-info fs-8 fw-bolder px-2 py-1">Lihat</span>
                    </a>
                </div>

                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                        <span class="menu-icon">
                            <i class="fas fa-user-edit fs-4 text-warning"></i>
                        </span>
                        <span class="menu-title">Edit Profil Saya</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

