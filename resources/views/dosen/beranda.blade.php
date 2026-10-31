@extends('layouts.dosen')

@section('title', 'Dashboard Dosen')
@section('page-title', 'Dashboard')

@section('content')
<!-- 1. Hero Greeting Banner Dosen -->
<div class="card mb-7 shadow-sm border-0 overflow-hidden" style="background: linear-gradient(135deg, #1e1e2d 0%, #151521 50%, #222736 100%);">
    <div class="card-body p-7 p-lg-9 position-relative">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between position-relative z-index-1">
            <div class="mb-4 mb-lg-0">
                <div class="d-flex align-items-center mb-2 flex-wrap gap-2">
                    <span class="badge badge-light-primary fw-bolder px-3 py-2 fs-8">
                        <i class="fas fa-chalkboard-teacher me-1 text-primary"></i> {{ $dosen['jabatan'] }}
                    </span>
                    <span class="badge badge-light-primary fw-bolder px-3 py-2 fs-8">
                        <i class="fas fa-graduation-cap me-1 text-primary"></i> Program Studi {{ $dosen['program_studi'] }}
                    </span>
                    <span class="badge badge-light-primary fw-bolder px-3 py-2 fs-8">
                        <i class="fas fa-map-marker-alt me-1 text-primary"></i> {{ $dosen['ruangan'] }}
                    </span>
                    <span class="badge badge-light-primary fw-bolder px-3 py-2 fs-8">
                        <i class="fas fa-check-circle me-1 text-primary"></i> {{ $dosen['status_hadir'] }}
                    </span>
                </div>
                <h1 class="text-white fw-bolder fs-2x mb-1">
                    {{ $dosen['nama'] }}
                </h1>
                <p class="text-gray-400 fs-6 fw-semibold mb-0">
                    NIP: <span class="text-white fw-bold me-4">{{ $dosen['nip'] }}</span>
                    Bidang Keahlian: <span class="text-primary fw-bold">{{ $dosen['bidang'] }}</span>
                </p>
            </div>
            <!-- <div class="d-flex align-items-center gap-3">
                <button type="button" class="btn btn-sm btn-primary fw-bolder shadow-sm px-4 py-3" onclick="#">
                    <i class="fas fa-calendar-plus me-1"></i> Atur Jadwal Konsultasi
                </button>
            </div> -->
        </div>
    </div>
</div>

<!-- 2. Ringkasan Statistik Dosen (4 KPI Cards) -->
<div class="row g-5 g-xl-8 mb-7">
    <!-- Mahasiswa Bimbingan Skripsi -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-body p-6 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-gray-600 fw-bolder fs-7 text-uppercase ls-1">BIMBINGAN SKRIPSI</span>
                        <div class="symbol symbol-45px bg-light-primary">
                            <span class="symbol-label">
                                <i class="fas fa-book-reader text-primary fs-2"></i>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline mb-2">
                        <span class="fs-2qx fw-bolder text-dark me-2">{{ $statistik['mahasiswa_skripsi'] }}</span>
                        <span class="fs-7 text-muted fw-bold">Mahasiswa</span>
                    </div>
                    <div class="text-muted fs-8">Mahasiswa aktif dalam bimbingan tugas akhir.</div>
                </div>
                <div class="pt-4 border-top border-gray-200 d-flex justify-content-between align-items-center mt-3">
                    <span class="badge badge-light-primary fw-bolder fs-8">Pembimbing Utama</span>
                    <i class="fas fa-arrow-right text-gray-400 fs-7"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Mahasiswa Perwalian (DPA) -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-body p-6 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-gray-600 fw-bolder fs-7 text-uppercase ls-1">MAHASISWA PERWALIAN</span>
                        <div class="symbol symbol-45px bg-light-primary">
                            <span class="symbol-label">
                                <i class="fas fa-user-friends text-primary fs-2"></i>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline mb-2">
                        <span class="fs-2qx fw-bolder text-dark me-2">{{ $statistik['mahasiswa_perwalian'] }}</span>
                        <span class="fs-7 text-muted fw-bold">Mahasiswa</span>
                    </div>
                    <div class="text-muted fs-8">Perwalian akademik & evaluasi kartu rencana studi.</div>
                </div>
                <div class="pt-4 border-top border-gray-200 d-flex justify-content-between align-items-center mt-3">
                    <span class="badge badge-light-primary fw-bolder fs-8">Dosen Wali (DPA)</span>
                    <i class="fas fa-arrow-right text-gray-400 fs-7"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Menunggu Review & Validasi -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-body p-6 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-gray-600 fw-bolder fs-7 text-uppercase ls-1">MENUNGGU REVIEW</span>
                        <div class="symbol symbol-45px bg-light-warning">
                            <span class="symbol-label">
                                <i class="fas fa-file-signature text-warning fs-2"></i>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline mb-2">
                        <span class="fs-2qx fw-bolder text-warning me-2">{{ $statistik['menunggu_review'] }}</span>
                        <span class="fs-7 text-muted fw-bold">Draf Dokumen</span>
                    </div>
                    <div class="text-muted fs-8">Revisi bab dan proposal siap diperiksa.</div>
                </div>
                <div class="pt-4 border-top border-gray-200 d-flex justify-content-between align-items-center mt-3">
                    <span class="badge badge-light-warning fw-bolder fs-8">Perlu Respon</span>
                    <i class="fas fa-arrow-right text-gray-400 fs-7"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Sesi Bimbingan Pekan Ini -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-body p-6 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-gray-600 fw-bolder fs-7 text-uppercase ls-1">JADWAL PEKAN INI</span>
                        <div class="symbol symbol-45px bg-light-primary">
                            <span class="symbol-label">
                                <i class="fas fa-calendar-check text-primary fs-2"></i>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline mb-2">
                        <span class="fs-2qx fw-bolder text-dark me-2">{{ $statistik['sesi_pekan_ini'] }}</span>
                        <span class="fs-7 text-muted fw-bold">Sesi Terjadwal</span>
                    </div>
                    <div class="text-muted fs-8">Konsultasi tatap muka & sesi daring terjadwal.</div>
                </div>
                <div class="pt-4 border-top border-gray-200 d-flex justify-content-between align-items-center mt-3">
                    <span class="badge badge-light-primary fw-bolder fs-8">Terorganisir</span>
                    <i class="fas fa-arrow-right text-gray-400 fs-7"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. Daftar Mahasiswa Bimbingan dan Perwalian (Tabbed Table Card) -->
<div class="card card-custom bg-body border border-gray-200 shadow-sm mb-7">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center">
                <i class="fas fa-users-cog text-primary fs-3 me-3"></i>
                <div class="d-flex flex-column">
                    <h3 class="fw-bolder text-dark fs-5 mb-0">Daftar Mahasiswa Bimbingan & Perwalian</h3>
                    <span class="text-muted fs-8">Kelola kemajuan draf skripsi serta status studi mahasiswa perwalian</span>
                </div>
            </div>
        </div>
        <div class="card-toolbar">
            <ul class="nav nav-pills nav-pills-sm nav-light">
                <li class="nav-item">
                    <a class="nav-link btn btn-active-light-primary fw-bolder px-4 me-2 active" data-bs-toggle="tab" href="#kt_tab_skripsi">
                        <i class="fas fa-book-reader me-1 text-primary"></i> Mahasiswa Skripsi ({{ count($mahasiswa_bimbingan) }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-active-light-primary fw-bolder px-4" data-bs-toggle="tab" href="#kt_tab_perwalian">
                        <i class="fas fa-user-friends me-1 text-primary"></i> Perwalian / DPA ({{ count($mahasiswa_perwalian) }})
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="card-body pt-0">
        <div class="tab-content">
            <!-- Tab 1: Mahasiswa Bimbingan Skripsi -->
            <div class="tab-pane fade show active" id="kt_tab_skripsi" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-row-bordered table-row-gray-200 align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bolder fs-7 text-gray-600 text-uppercase border-bottom border-gray-200 bg-light-primary bg-opacity-25">
                                <th class="ps-4 rounded-start min-w-200px">Mahasiswa</th>
                                <th class="min-w-250px">Judul Skripsi / Topik</th>
                                <th class="min-w-150px">Bab Saat Ini</th>
                                <th class="min-w-150px">Progres</th>
                                <th class="min-w-150px">Status Draf</th>
                                <th class="min-w-100px text-end pe-4 rounded-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="fs-7">
                            @foreach ($mahasiswa_bimbingan as $mhs)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-40px symbol-circle bg-light-primary text-primary fw-bolder me-3 d-flex align-items-center justify-content-center">
                                                {{ strtoupper(substr($mhs['nama'], 0, 1)) }}
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="text-dark fw-bolder fs-6">{{ $mhs['nama'] }}</span>
                                                <span class="text-muted fs-8">NIM: {{ $mhs['nim'] }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-dark fw-semibold fs-7 line-clamp-2 max-w-350px">
                                            {{ $mhs['judul'] }}
                                        </div>
                                        <span class="text-muted fs-8">Update: {{ $mhs['tanggal_update'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-light fw-bolder text-gray-800 fs-8 px-2 py-1 border border-gray-300">
                                            {{ $mhs['bab'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column w-100 me-2">
                                            <div class="d-flex justify-content-between mb-1 fs-8 fw-bold">
                                                <span>{{ $mhs['progres'] }}%</span>
                                            </div>
                                            <div class="progress h-6px bg-light-primary rounded-pill">
                                                <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: {{ $mhs['progres'] }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-{{ $mhs['badge_status'] }} fw-bolder fs-8 px-3 py-2">
                                            {{ $mhs['status_draf'] }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button type="button" class="btn btn-sm btn-light-primary fw-bolder fs-8 py-2 px-3" onclick="#">
                                            <i class="fas fa-external-link-alt me-1"></i> Review
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 2: Mahasiswa Perwalian (DPA) -->
            <div class="tab-pane fade" id="kt_tab_perwalian" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-row-bordered table-row-gray-200 align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bolder fs-7 text-gray-600 text-uppercase border-bottom border-gray-200 bg-light-primary bg-opacity-25">
                                <th class="ps-4 rounded-start min-w-200px">Mahasiswa</th>
                                <th class="min-w-125px">Angkatan & Semester</th>
                                <th class="min-w-100px">IPK Kumulatif</th>
                                <th class="min-w-100px">SKS Lulus</th>
                                <th class="min-w-150px">Status KRS</th>
                                <th class="min-w-100px text-end pe-4 rounded-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="fs-7">
                            @foreach ($mahasiswa_perwalian as $perwalian)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-40px symbol-circle bg-light-primary text-primary fw-bolder me-3 d-flex align-items-center justify-content-center">
                                                {{ strtoupper(substr($perwalian['nama'], 0, 1)) }}
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="text-dark fw-bolder fs-6">{{ $perwalian['nama'] }}</span>
                                                <span class="text-muted fs-8">NIM: {{ $perwalian['nim'] }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bolder text-dark">{{ $perwalian['semester'] }}</span>
                                        <div class="text-muted fs-8">Angkatan {{ $perwalian['angkatan'] }}</div>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-primary fw-bolder fs-7 px-2.5 py-1">
                                            {{ $perwalian['ipk'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-gray-700 fs-7">{{ $perwalian['sks_lulus'] }} SKS</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-{{ $perwalian['badge_status'] }} fw-bolder fs-8 px-3 py-2">
                                            {{ $perwalian['status_krs'] }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button type="button" class="btn btn-sm btn-light-primary fw-bolder fs-8 py-2 px-3" onclick="Swal.fire({icon: 'primary', title: 'Data Akademik Mahasiswa', text: 'KHS dan KRS mahasiswa perwalian {{ $perwalian['nama'] }} akan terintegrasi pada modul berikutnya.', confirmButtonText: 'Tutup', customClass: {confirmButton: 'btn btn-primary'}})">
                                            <i class="fas fa-eye me-1"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 4. Grid Dua Kolom: Jadwal Bimbingan Terdekat & Aktivitas Terkini -->
<div class="row g-5 g-xl-8">
    <!-- Kolom Kiri: Jadwal Bimbingan Terdekat -->
    <div class="col-xl-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-header border-0 pt-6">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder text-dark fs-5">
                        <i class="fas fa-calendar-alt text-primary me-2"></i> Jadwal Bimbingan Terdekat
                    </span>
                    <span class="text-muted fs-8 mt-1">Daftar sesi konsultasi mahasiswa yang telah disetujui</span>
                </h3>
            </div>
            <div class="card-body pt-2">
                <div class="d-flex flex-column gap-4">
                    @foreach ($jadwal_bimbingan as $jadwal)
                        <div class="p-4 rounded-3 border border-gray-200 bg-light-primary bg-opacity-20 d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge badge-light-{{ $jadwal['badge'] }} fw-bolder fs-8">
                                    {{ $jadwal['jenis'] }}
                                </span>
                                <span class="badge badge-primary fw-bolder fs-9">{{ $jadwal['status'] }}</span>
                            </div>
                            <div class="fw-bolder text-dark fs-6 mb-1">{{ $jadwal['mahasiswa'] }} ({{ $jadwal['nim'] }})</div>
                            <div class="text-muted fs-7 mb-3">{{ $jadwal['topik'] }}</div>
                            <div class="d-flex flex-wrap align-items-center justify-content-between border-top border-gray-200 pt-2 text-gray-700 fs-8 fw-semibold">
                                <div>
                                    <i class="far fa-clock text-primary me-1"></i> {{ $jadwal['tanggal'] }} &bull; {{ $jadwal['waktu'] }}
                                </div>
                                <div>
                                    <i class="fas fa-map-marker-alt text-warning me-1"></i> {{ $jadwal['tempat'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Log Aktivitas Mahasiswa & Riwayat Bimbingan -->
    <div class="col-xl-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-header border-0 pt-6">
                <h3 class="card-title align-items-start flex-column">
                    <span class="fw-bolder text-dark fs-5">
                        <i class="fas fa-history text-primary me-2"></i> Aktivitas & Log Bimbingan
                    </span>
                    <span class="text-muted fs-8 mt-1">Pemberitahuan unggahan draf, revisi, dan pengajuan terbaru</span>
                </h3>
            </div>
            <div class="card-body pt-2">
                <div class="timeline-label">
                    @foreach ($aktivitas_terbaru as $aktivitas)
                        <div class="d-flex align-items-start py-3 border-bottom border-gray-100">
                            <div class="symbol symbol-35px me-3">
                                <span class="symbol-label bg-light-{{ $aktivitas['badge'] }}">
                                    <i class="{{ $aktivitas['icon'] }} text-{{ $aktivitas['badge'] }} fs-5"></i>
                                </span>
                            </div>
                            <div class="d-flex flex-column flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="fw-bolder text-dark fs-7">{{ $aktivitas['judul'] }}</span>
                                    <span class="text-muted fs-8">{{ $aktivitas['waktu'] }}</span>
                                </div>
                                <span class="text-primary fw-bold fs-8 mb-1">{{ $aktivitas['mahasiswa'] }}</span>
                                <span class="text-muted fs-8">{{ $aktivitas['keterangan'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
