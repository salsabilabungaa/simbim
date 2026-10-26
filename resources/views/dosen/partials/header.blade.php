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
            <a href="{{ route('dosen.beranda') }}" class="d-lg-none">
                <img alt="Logo" src="{{ asset('assets/media/logos/logo-2.svg') }}" class="h-30px" />
            </a>
        </div>

        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
            <div class="d-flex align-items-center" id="kt_header_nav">
                <span class="d-none d-md-inline-block text-gray-700 fw-bolder fs-6">
                    <i class="fas fa-university text-primary me-2"></i> Portal Akademik & Pembimbing Dosen
                </span>
            </div>

            <div class="d-flex align-items-stretch flex-shrink-0">
                <!-- Notifikasi Dropdown -->
                <div class="d-flex align-items-center ms-1 ms-lg-3">
                    <div class="btn btn-icon btn-icon-muted btn-active-light btn-active-color-primary position-relative w-30px h-30px w-md-40px h-md-40px" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end" title="Notifikasi Dosen">
                        <i class="fas fa-bell fs-2"></i>
                        <span class="bullet bullet-dot bg-danger h-6px w-6px position-absolute translate-middle top-0 start-50 animation-blink"></span>
                    </div>
                    <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px" data-kt-menu="true">
                        <div class="d-flex flex-column bgi-no-repeat rounded-top p-6" style="background: linear-gradient(135deg, #1e1e2d 0%, #2b2b40 100%);">
                            <h3 class="text-white fw-bolder mb-1 fs-5">
                                <i class="fas fa-bell text-warning me-2"></i> Notifikasi Mahasiswa
                            </h3>
                            <span class="text-gray-400 fs-7">{{ count($notifikasi ?? []) }} pemberitahuan terbaru</span>
                        </div>
                        <div class="scroll-y mh-325px my-3 px-6">
                            @forelse ($notifikasi ?? [] as $item)
                                <div class="d-flex align-items-center py-3 border-bottom border-gray-100">
                                    <div class="symbol symbol-35px me-3">
                                        <span class="symbol-label bg-light-{{ $item['badge'] ?? 'primary' }}">
                                            <i class="{{ $item['icon'] ?? 'fas fa-info-circle' }} text-{{ $item['badge'] ?? 'primary' }} fs-5"></i>
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column flex-grow-1">
                                        <a href="javascript:void(0)" class="fs-7 text-dark text-hover-primary fw-bolder">{{ $item['judul'] }}</a>
                                        <span class="text-muted fs-8">{{ $item['deskripsi'] ?? '' }} &bull; {{ $item['waktu'] }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6 text-muted fs-7">
                                    Tidak ada notifikasi baru saat ini.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- User Profile Dropdown (Popout sesuai desain) -->
                <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                    <div class="cursor-pointer d-flex align-items-center px-2 py-1 text-hover-primary" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <div class="symbol symbol-30px symbol-circle me-2 bg-light-success text-success fw-bolder d-flex align-items-center justify-content-center">
                            {{ strtoupper(substr(auth()->user()->name ?? ($dosen['nama'] ?? 'D'), 0, 1)) }}
                        </div>
                        <span class="text-gray-700 fw-bold fs-7 text-uppercase ls-1">{{ auth()->user()->name ?? ($dosen['nama'] ?? 'Dosen') }}</span>
                    </div>

                    <!-- Popout Card Dropdown (Persis Desain Gambar) -->
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px w-md-300px" data-kt-menu="true">
                        <!-- 1. Header Informasi Dosen (Center-aligned) -->
                        <div class="menu-item px-3">
                            <div class="menu-content d-flex flex-column text-center px-3 py-2">
                                <div class="fw-bolder text-gray-800 fs-6 text-uppercase mb-1" style="letter-spacing: 0.5px;">
                                    {{ auth()->user()->name ?? ($dosen['nama'] ?? 'NAJWA RIZKI AMANULLAH') }}
                                </div>
                                <div class="text-muted fs-7 mb-1">
                                    {{ auth()->user()->nomor_induk ?? ($dosen['nip'] ?? '2401839') }}
                                </div>
                                <div class="text-muted fs-7" style="line-height: 1.3;">
                                    {{ auth()->user()->program_studi ? (str_contains(auth()->user()->program_studi, '(S1)') ? auth()->user()->program_studi : auth()->user()->program_studi . ' (S1)') : ($dosen['program_studi'] ?? 'Pendidikan Sistem dan Teknologi Informasi (S1)') }}
                                </div>
                            </div>
                        </div>

                        <!-- 2. Garis Pemisah (Divider) -->
                        <div class="separator my-2"></div>

                        <!-- 3. Edit Profil -->
                        <div class="menu-item px-5">
                            <a href="javascript:void(0)" class="menu-link px-3 py-2 d-flex align-items-center" onclick="bukaModalEditProfilDosen()">
                                <i class="bi bi-person-fill fs-3 me-3" style="color: #009688;"></i>
                                <span class="text-gray-700 fs-6 fw-semibold">Edit Profil</span>
                            </a>
                        </div>

                        <!-- 4. Sign Out -->
                        <div class="menu-item px-5">
                            <form method="POST" action="{{ route('logout') }}" id="dosen_header_logout_form" class="m-0 p-0">
                                @csrf
                                <button type="submit" class="menu-link px-3 py-2 d-flex align-items-center border-0 bg-transparent w-100 text-start cursor-pointer">
                                    <i class="bi bi-power fs-3 me-3" style="color: #009688;"></i>
                                    <span class="text-gray-700 fs-6 fw-semibold">Sign Out</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Profil Dosen -->
<div class="modal fade" id="modal_edit_profil_dosen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-600px">
        <div class="modal-content rounded">
            <div class="modal-header border-0 pb-0 justify-content-between">
                <h3 class="fw-bolder text-dark mb-0">Edit Profil Dosen</h3>
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="fas fa-times fs-4"></i>
                </div>
            </div>
            <div class="modal-body px-8 py-6">
                <form id="form_edit_profil_dosen" onsubmit="simpanProfilDosen(event)">
                    <div class="text-center mb-6">
                        <div class="symbol symbol-70px symbol-circle bg-light-primary text-primary fw-bolder fs-1 mb-2 mx-auto d-flex align-items-center justify-content-center">
                            {{ strtoupper(substr(auth()->user()->name ?? ($dosen['nama'] ?? 'D'), 0, 1)) }}
                        </div>
                        <div class="fw-bolder fs-6 text-gray-800">{{ auth()->user()->name ?? ($dosen['nama'] ?? 'Dosen Pembimbing') }}</div>
                        <div class="text-muted fs-7">NIP: {{ auth()->user()->nomor_induk ?? ($dosen['nip'] ?? '-') }}</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bolder text-gray-700 fs-7 required">Nama Lengkap & Gelar</label>
                        <input type="text" class="form-control form-control-solid" id="edit_nama_dosen" value="{{ auth()->user()->name ?? ($dosen['nama'] ?? 'Dr. Siti Rahmawati, M.Kom.') }}" required />
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label class="form-label fw-bolder text-gray-700 fs-7 required">NIP / Nomor Induk</label>
                            <input type="text" class="form-control form-control-solid" id="edit_nip_dosen" value="{{ auth()->user()->nomor_induk ?? ($dosen['nip'] ?? '198503152010122001') }}" required />
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-bolder text-gray-700 fs-7 required">Program Studi</label>
                            <input type="text" class="form-control form-control-solid" id="edit_prodi_dosen" value="{{ auth()->user()->program_studi ?? ($dosen['program_studi'] ?? 'Pendidikan Sistem dan Teknologi Informasi (S1)') }}" required />
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label class="form-label fw-bolder text-gray-700 fs-7">Email</label>
                            <input type="email" class="form-control form-control-solid" id="edit_email_dosen" value="{{ auth()->user()->email ?? ($dosen['email'] ?? 'dosen@usk.ac.id') }}" />
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-bolder text-gray-700 fs-7">Nomor Telepon / WhatsApp</label>
                            <input type="text" class="form-control form-control-solid" id="edit_telepon_dosen" value="{{ $dosen['telepon'] ?? '+62 812-6900-8821' }}" />
                        </div>
                    </div>

                    <div class="row g-3 mb-5">
                        <div class="col-sm-6">
                            <label class="form-label fw-bolder text-gray-700 fs-7">Ruangan Dosen</label>
                            <input type="text" class="form-control form-control-solid" id="edit_ruangan_dosen" value="{{ $dosen['ruangan'] ?? 'Gedung Fasilkom Lt. 2 R.204' }}" />
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-bolder text-gray-700 fs-7">Status Kehadiran</label>
                            <select class="form-select form-select-solid" id="edit_status_hadir">
                                <option value="Tersedia di Kampus" selected>Tersedia di Kampus</option>
                                <option value="Bimbingan Daring">Bimbingan Daring</option>
                                <option value="Sedang Mengajar / Rapat">Sedang Mengajar / Rapat</option>
                                <option value="Tugas Luar Kampus">Tugas Luar Kampus</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary fw-bolder">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function bukaModalEditProfilDosen() {
        const modalEl = document.getElementById('modal_edit_profil_dosen');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }

    function simpanProfilDosen(e) {
        e.preventDefault();
        const modalEl = document.getElementById('modal_edit_profil_dosen');
        if (modalEl) {
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) {
                modal.hide();
            }
        }

        Swal.fire({
            icon: 'success',
            title: 'Profil Berhasil Disimpan',
            text: 'Informasi data diri dan ketersediaan bimbingan dosen telah diperbarui.',
            confirmButtonText: 'Selesai',
            customClass: { confirmButton: 'btn btn-primary' }
        });
    }
</script>
