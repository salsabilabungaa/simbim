@extends('layouts.mahasiswa')

@section('title', 'Dashboard Mahasiswa')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    .simbim-card {
        border-radius: 1rem;
        border: 1px solid rgba(226, 232, 240, 0.8);
        background: #ffffff;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.04);
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .simbim-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
        border-color: rgba(203, 213, 225, 0.9);
    }
    .hero-banner {
        background: linear-gradient(135deg, #1e1e2d 0%, #151521 50%, #222238 100%);
        border-radius: 1.25rem;
        position: relative;
        overflow: hidden;
    }
    .hero-glow-1 {
        position: absolute;
        top: -40px;
        right: -40px;
        width: 240px;
        height: 240px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.25) 0%, rgba(59, 130, 246, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .hero-glow-2 {
        position: absolute;
        bottom: -60px;
        left: 20%;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, rgba(16, 185, 129, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .kpi-icon-box {
        width: 52px;
        height: 52px;
        border-radius: 0.85rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .pulse-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #10b981;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulseRing 1.8s infinite;
    }
    @keyframes pulseRing {
        0% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }
        70% {
            box-shadow: 0 0 0 7px rgba(16, 185, 129, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }
    .milestone-step {
        position: relative;
        padding-bottom: 0.25rem;
    }
    .badge-soft-primary { background-color: #eef2ff; color: #4338ca; }
    .badge-soft-success { background-color: #ecfdf5; color: #047857; }
    .badge-soft-warning { background-color: #fffbeb; color: #b45309; }
    .badge-soft-info { background-color: #f0f9ff; color: #0369a1; }
    .badge-soft-purple { background-color: #f5f3ff; color: #6d28d9; }
</style>
@endpush

@section('content')
<!-- 1. Banner Greeting Hero Modern -->
<div class="hero-banner mb-7 p-6 p-md-8 text-white shadow-sm">
    <div class="hero-glow-1"></div>
    <div class="hero-glow-2"></div>
    <div class="position-relative z-index-1">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
            <div>
                <!-- Badges Status Mahasiswa -->
                <div class="d-flex align-items-center flex-wrap gap-2 mb-3">
                    <span class="badge bg-white bg-opacity-15 text-white fw-bold px-3 py-2 rounded-pill fs-8 backdrop-blur">
                        <i class="fas fa-graduation-cap me-1.5 text-primary"></i> Program Studi {{ $mahasiswa['program_studi'] }}
                    </span>
                    <span class="badge bg-white bg-opacity-15 text-white fw-bold px-3 py-2 rounded-pill fs-8 backdrop-blur">
                        <i class="fas fa-layer-group me-1.5 text-info"></i> {{ $mahasiswa['semester'] }}
                    </span>
                    <span class="badge bg-success bg-opacity-25 text-success-light fw-bold px-3 py-2 rounded-pill fs-8 border border-success border-opacity-25">
                        <span class="pulse-dot me-1.5"></span> {{ $mahasiswa['status'] }}
                    </span>
                </div>

                <!-- Sapaan & Nama -->
                <h1 class="text-white fw-bolder fs-2x fs-md-3x mb-2 tracking-tight">
                    {{ $mahasiswa['sapaan'] }}, {{ $mahasiswa['nama'] }} 👋
                </h1>
                
                <!-- Info Ringkas Akademik -->
                <div class="d-flex flex-wrap align-items-center gap-y-2 text-gray-300 fs-7 fw-normal">
                    <span class="d-inline-flex align-items-center me-4">
                        <i class="fas fa-id-card me-2 text-gray-400"></i> NIM: <strong class="text-white ms-1">{{ $mahasiswa['nim'] }}</strong>
                    </span>
                    <span class="d-inline-flex align-items-center me-4">
                        <i class="fas fa-user-tie me-2 text-info"></i> Pembimbing: <strong class="text-white ms-1">{{ $dosen_pembimbing['nama'] }}</strong>
                    </span>
                    <span class="d-inline-flex align-items-center">
                        <i class="fas fa-user-shield me-2 text-success"></i> Dosen Wali: <strong class="text-white ms-1">{{ $dosen_wali['nama'] }}</strong>
                    </span>
                </div>
            </div>

            <!-- Tombol Aksi Cepat / Shortcut CTA -->
            <div class="d-flex flex-wrap align-items-center gap-2 mt-2 mt-lg-0">
                <a href="{{ route('mahasiswa.bimbingan-skripsi') }}" class="btn btn-primary fw-bold px-4 py-3 rounded-3 shadow-sm d-inline-flex align-items-center">
                    <i class="fas fa-calendar-plus me-2 fs-5"></i>
                    <span>Ajukan Bimbingan</span>
                </a>
                <a href="{{ route('mahasiswa.bimbingan-akademik') }}" class="btn btn-white btn-color-gray-700 btn-active-primary fw-bold px-4 py-3 rounded-3 shadow-sm d-inline-flex align-items-center bg-white">
                    <i class="fas fa-user-graduate me-2 fs-5 text-primary"></i>
                    <span>Bimbingan Akademik</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- 2. Ringkasan Utama (4 KPI Summary Cards) -->
<div class="row g-4 g-xl-6 mb-7">
    <!-- Kartu 1: Progres Skripsi -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="simbim-card p-5 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-gray-500 fw-bold fs-8 text-uppercase tracking-wider">Progres Skripsi</span>
                    <div class="kpi-icon-box bg-light-primary text-primary">
                        <i class="fas fa-chart-line fs-2"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline mb-2">
                    <span class="fs-2x fw-bolder text-gray-900 me-2">{{ $bab['persen'] }}%</span>
                    <span class="badge badge-light-success fw-bold fs-9">
                        <i class="fas fa-arrow-up me-1"></i> +7% bln ini
                    </span>
                </div>
                <div class="progress h-8px bg-light-primary rounded-pill mb-2">
                    <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: {{ $bab['persen'] }}%" aria-valuenow="{{ $bab['persen'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="text-gray-800 fw-bold fs-7 text-truncate" title="{{ $bab['posisi'] }}">
                    <i class="fas fa-bookmark text-primary me-1 fs-8"></i> {{ $bab['posisi'] }}
                </div>
            </div>
            <div class="pt-3 mt-3 border-top border-gray-100 d-flex justify-content-between align-items-center fs-8 text-gray-500">
                <span>Target: <strong>{{ $bab['target_selesai'] }}</strong></span>
                <i class="fas fa-chevron-right fs-9 text-gray-400"></i>
            </div>
        </div>
    </div>

    <!-- Kartu 2: Rekapan Sesi Bimbingan -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="simbim-card p-5 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-gray-500 fw-bold fs-8 text-uppercase tracking-wider">Sesi Bulan Ini</span>
                    <div class="kpi-icon-box bg-light-success text-success">
                        <i class="fas fa-comments fs-2"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline mb-2">
                    <span class="fs-2x fw-bolder text-gray-900 me-2">{{ $rekapan_bimbingan['total_bulan_ini'] }}</span>
                    <span class="fs-7 text-gray-500 fw-semibold">Sesi Terlaksana</span>
                </div>
                <div class="d-flex align-items-center gap-1 mb-2">
                    <span class="badge badge-soft-success fw-bold fs-9 px-2.5 py-1 rounded-pill">
                        <i class="fas fa-check-circle me-1 text-success"></i> {{ $rekapan_bimbingan['status_kuota'] }}
                    </span>
                </div>
                <div class="text-gray-600 fs-8">
                    {{ $rekapan_bimbingan['skripsi']['total'] }} Skripsi • {{ $rekapan_bimbingan['akademik']['total'] }} Akademik
                </div>
            </div>
            <div class="pt-3 mt-3 border-top border-gray-100 d-flex justify-content-between align-items-center fs-8 text-gray-500">
                <span>Status Kuota: <strong class="text-success">Terpenuhi</strong></span>
                <i class="fas fa-chevron-right fs-9 text-gray-400"></i>
            </div>
        </div>
    </div>

    <!-- Kartu 3: Jadwal Bimbingan Terdekat -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="simbim-card p-5 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-gray-500 fw-bold fs-8 text-uppercase tracking-wider">Jadwal Terdekat</span>
                    <div class="kpi-icon-box bg-light-warning text-warning">
                        <i class="fas fa-calendar-alt fs-2"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline mb-2">
                    <span class="fs-4 fw-bolder text-gray-900 text-truncate" title="{{ $pertemuan['sisa'] }}">
                        {{ $pertemuan['sisa'] }}
                    </span>
                </div>
                <div class="d-flex align-items-center gap-1 mb-2">
                    <span class="badge badge-light-warning fw-bold fs-9 px-2.5 py-1 rounded-pill">
                        <i class="fas fa-clock me-1 text-warning"></i> {{ $pertemuan['status'] }}
                    </span>
                </div>
                <div class="text-gray-700 fw-semibold fs-8 text-truncate" title="{{ $pertemuan['judul'] }}">
                    {{ $pertemuan['judul'] }}
                </div>
            </div>
            <div class="pt-3 mt-3 border-top border-gray-100 d-flex justify-content-between align-items-center fs-8 text-gray-500">
                <span>{{ $pertemuan['waktu'] }}</span>
                <i class="fas fa-chevron-right fs-9 text-gray-400"></i>
            </div>
        </div>
    </div>

    <!-- Kartu 4: Status Berkas & Dokumen -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="simbim-card p-5 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-gray-500 fw-bold fs-8 text-uppercase tracking-wider">Berkas Skripsi</span>
                    <div class="kpi-icon-box bg-light-info text-info">
                        <i class="fas fa-file-invoice fs-2"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline mb-2">
                    <span class="fs-2x fw-bolder text-gray-900 me-2">{{ count($dokumen) }}</span>
                    <span class="fs-7 text-gray-500 fw-semibold">Dokumen Aktif</span>
                </div>
                <div class="d-flex align-items-center gap-1 mb-2">
                    <span class="badge badge-soft-primary fw-bold fs-9 px-2.5 py-1 rounded-pill">
                        <i class="fas fa-sync-alt me-1 text-primary"></i> 1 Dalam Tinjauan
                    </span>
                </div>
                <div class="text-gray-600 fs-8 text-truncate">
                    Update draft: {{ $dokumen[0]['waktu'] ?? 'Baru saja' }}
                </div>
            </div>
            <div class="pt-3 mt-3 border-top border-gray-100 d-flex justify-content-between align-items-center fs-8 text-gray-500">
                <span>{{ $dokumen[0]['status'] ?? 'Draft Aktif' }}</span>
                <i class="fas fa-chevron-right fs-9 text-gray-400"></i>
            </div>
        </div>
    </div>
</div>

<!-- 3. Section Detail Progres & Jadwal Terdekat (Grid 7 & 5) -->
<div class="row g-4 g-xl-6 mb-7">
    <!-- Kolom Kiri (7): Visualisasi Progres & Milestone Skripsi -->
    <div class="col-12 col-lg-7">
        <div class="simbim-card p-6 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-4 pb-3 border-bottom border-gray-100">
                    <div>
                        <h2 class="fs-4 fw-bolder text-gray-900 mb-1">Rincian Progres Skripsi</h2>
                        <span class="text-gray-500 fs-7">Pantauan perkembangan tahapan bab dan status pengerjaan</span>
                    </div>
                    <span class="badge badge-light-primary fw-bold px-3 py-2 rounded-pill fs-8">
                        {{ $bab['persen'] }}% dari 100%
                    </span>
                </div>

                <!-- Box Posisi Bab Saat Ini -->
                <div class="p-4 rounded-3 bg-light-primary border border-primary border-opacity-10 mb-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary text-white fw-bold px-2.5 py-1 rounded-pill me-2 fs-8">Aktif</span>
                            <span class="fw-bolder fs-6 text-gray-900">{{ $bab['posisi'] }}</span>
                        </div>
                        <span class="badge badge-soft-warning fw-bold fs-8">
                            <i class="fas fa-hourglass-half me-1"></i> Sedang Ditinjau
                        </span>
                    </div>
                    <p class="text-gray-700 fs-7 mb-0">
                        {{ $bab['keterangan'] }}
                    </p>
                </div>

                <!-- Milestone Stepper Bab 1 s/d Bab 5 -->
                <div class="mb-4">
                    <span class="text-gray-500 fw-bold fs-8 text-uppercase tracking-wider d-block mb-3">Tahapan Bab Skripsi</span>
                    <div class="d-flex flex-column gap-2.5">
                        <!-- Bab 1 -->
                        <div class="d-flex align-items-center justify-content-between p-2.5 rounded-2 bg-light border border-gray-100">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success text-white rounded-circle p-1 me-2.5 d-flex align-items-center justify-content-center" style="width: 22px; height: 22px;">
                                    <i class="fas fa-check fs-9"></i>
                                </span>
                                <span class="fw-bold fs-7 text-gray-800">Bab I — Pendahuluan</span>
                            </div>
                            <span class="badge badge-light-success fw-bold fs-9">ACC Pembimbing</span>
                        </div>
                        <!-- Bab 2 -->
                        <div class="d-flex align-items-center justify-content-between p-2.5 rounded-2 bg-light border border-gray-100">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success text-white rounded-circle p-1 me-2.5 d-flex align-items-center justify-content-center" style="width: 22px; height: 22px;">
                                    <i class="fas fa-check fs-9"></i>
                                </span>
                                <span class="fw-bold fs-7 text-gray-800">Bab II — Tinjauan Pustaka</span>
                            </div>
                            <span class="badge badge-light-success fw-bold fs-9">ACC Pembimbing</span>
                        </div>
                        <!-- Bab 3 (Aktif) -->
                        <div class="d-flex align-items-center justify-content-between p-2.5 rounded-2 bg-white border border-primary border-opacity-30 shadow-xs">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary text-white rounded-circle p-1 me-2.5 d-flex align-items-center justify-content-center" style="width: 22px; height: 22px;">
                                    <i class="fas fa-pencil-alt fs-9"></i>
                                </span>
                                <span class="fw-bolder fs-7 text-primary">Bab III — Metodologi Penelitian</span>
                            </div>
                            <span class="badge badge-light-primary fw-bold fs-9">Revisi ke-2 (65%)</span>
                        </div>
                        <!-- Bab 4 -->
                        <div class="d-flex align-items-center justify-content-between p-2.5 rounded-2 bg-light border border-gray-100 opacity-60">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-gray-400 text-white rounded-circle p-1 me-2.5 d-flex align-items-center justify-content-center" style="width: 22px; height: 22px;">
                                    <span class="fs-9">4</span>
                                </span>
                                <span class="fw-semibold fs-7 text-gray-600">Bab IV — Hasil & Pembahasan</span>
                            </div>
                            <span class="badge badge-light-secondary text-gray-500 fw-bold fs-9">Belum Dimulai</span>
                        </div>
                        <!-- Bab 5 -->
                        <div class="d-flex align-items-center justify-content-between p-2.5 rounded-2 bg-light border border-gray-100 opacity-60">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-gray-400 text-white rounded-circle p-1 me-2.5 d-flex align-items-center justify-content-center" style="width: 22px; height: 22px;">
                                    <span class="fs-9">5</span>
                                </span>
                                <span class="fw-semibold fs-7 text-gray-600">Bab V — Penutup & Kesimpulan</span>
                            </div>
                            <span class="badge badge-light-secondary text-gray-500 fw-bold fs-9">Belum Dimulai</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Card Link -->
            <div class="pt-3 mt-3 border-top border-gray-100 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <span class="text-gray-500 fs-8">Target penyelesaian: <strong>{{ $bab['target_selesai'] }}</strong></span>
                <a href="{{ route('mahasiswa.bimbingan-skripsi') }}" class="btn btn-sm btn-light-primary fw-bold d-inline-flex align-items-center">
                    <span>Kelola Bimbingan Skripsi</span>
                    <i class="fas fa-arrow-right ms-2 fs-8"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan (5): Jadwal Bimbingan Terdekat & Interaktif -->
    <div class="col-12 col-lg-5">
        <div class="simbim-card p-6 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-gray-100">
                    <div>
                        <h2 class="fs-4 fw-bolder text-gray-900 mb-1">Pertemuan Terdekat</h2>
                        <span class="text-gray-500 fs-7">Jadwal bimbingan tatap muka berikutnya</span>
                    </div>
                    <span class="badge badge-soft-warning fw-bold px-3 py-1.5 rounded-pill fs-8">
                        <i class="fas fa-bell me-1 text-warning"></i> H-1
                    </span>
                </div>

                <!-- Kartu Jadwal Interaktif -->
                <div class="p-4 rounded-3 border border-warning border-opacity-30 mb-4" style="background: linear-gradient(180deg, #fffdfa 0%, #fffbf2 100%);">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="badge bg-warning text-dark fw-bold px-2.5 py-1 rounded-pill fs-8">
                            {{ $pertemuan['tipe'] }}
                        </span>
                        <span class="text-gray-600 fw-bold fs-8">
                            <i class="fas fa-check-circle text-success me-1"></i> {{ $pertemuan['status'] }}
                        </span>
                    </div>

                    <h3 class="fs-5 fw-bolder text-gray-900 mb-2">
                        {{ $pertemuan['judul'] }}
                    </h3>

                    <div class="d-flex flex-column gap-2 text-gray-700 fs-7 mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-day text-primary me-2.5 fs-6 w-20px text-center"></i>
                            <span class="fw-bold text-gray-800">{{ $pertemuan['tanggal'] }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-clock text-warning me-2.5 fs-6 w-20px text-center"></i>
                            <span>{{ $pertemuan['waktu'] }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-map-marker-alt text-danger me-2.5 fs-6 w-20px text-center"></i>
                            <span class="text-truncate">{{ $pertemuan['tempat'] }}</span>
                        </div>
                    </div>

                    <!-- Tombol Pengingat Dosen -->
                    <button type="button" id="btnRemindDosen" onclick="remindDosen(this)" class="btn btn-warning w-100 fw-bolder text-dark py-2.5 rounded-2 shadow-xs d-flex align-items-center justify-content-center">
                        <i class="fas fa-paper-plane me-2"></i>
                        <span>Kirim Pengingat Bimbingan ke Dosen</span>
                    </button>
                </div>

                <!-- Info Kuota Bimbingan Bulan Ini -->
                <div class="p-3.5 rounded-2 bg-light border border-gray-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold text-gray-700 fs-8">Pemenuhan Kuota Bulan Ini</span>
                        <span class="badge badge-light-success fw-bold fs-9">{{ $rekapan_bimbingan['total_bulan_ini'] }}/6 Sesi</span>
                    </div>
                    <div class="progress h-6px bg-gray-200 rounded-pill">
                        <div class="progress-bar bg-success rounded-pill" style="width: 100%"></div>
                    </div>
                </div>
            </div>

            <!-- Footer Action -->
            <div class="pt-3 mt-3 border-top border-gray-100 d-flex justify-content-between align-items-center">
                <span class="text-gray-500 fs-8">Kebutuhan sidang: Min. 8 sesi</span>
                <a href="{{ route('mahasiswa.bimbingan-skripsi') }}" class="text-primary fw-bold fs-8 text-hover-underline">Lihat Semua Jadwal</a>
            </div>
        </div>
    </div>
</div>

<!-- 4. Section Dosen Pembimbing & Dosen Wali (Card Elegan) -->
<div class="row g-4 g-xl-6 mb-7">
    <!-- Dosen Pembimbing Utama -->
    <div class="col-12 col-md-6">
        <div class="simbim-card p-6 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-start justify-content-between gap-3 mb-4 pb-3 border-bottom border-gray-100">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-50px me-3">
                            <span class="symbol-label bg-light-primary text-primary fw-bolder fs-3 rounded-circle">
                                <i class="fas fa-user-tie fs-2 text-primary"></i>
                            </span>
                        </div>
                        <div>
                            <span class="badge badge-soft-primary fw-bold fs-9 px-2.5 py-0.5 rounded-pill mb-1">Pembimbing Utama</span>
                            <h3 class="fs-5 fw-bolder text-gray-900 mb-0">{{ $dosen_pembimbing['nama'] }}</h3>
                            <span class="text-gray-400 fs-8">NIP: {{ $dosen_pembimbing['nip'] }}</span>
                        </div>
                    </div>
                    <span class="badge badge-light-{{ $dosen_pembimbing['badge_status'] }} fw-bold px-2.5 py-1.5 fs-8 text-nowrap rounded-pill">
                        <span class="pulse-dot me-1"></span> {{ $dosen_pembimbing['status_hadir'] }}
                    </span>
                </div>

                <div class="d-flex flex-column gap-2.5 text-gray-700 fs-7 mb-2">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-briefcase text-primary me-3 fs-6 w-20px text-center"></i>
                        <span class="fw-semibold text-gray-800">{{ $dosen_pembimbing['jabatan'] }}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-lightbulb text-warning me-3 fs-6 w-20px text-center"></i>
                        <span class="text-gray-600">{{ $dosen_pembimbing['bidang'] }}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-building text-info me-3 fs-6 w-20px text-center"></i>
                        <span class="text-gray-700">{{ $dosen_pembimbing['ruangan'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Contact Toolbar -->
            <div class="pt-3 mt-4 border-top border-gray-100 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <a href="mailto:{{ $dosen_pembimbing['email'] }}" class="btn btn-sm btn-light-danger fw-bold px-3 py-2 rounded-2 text-truncate" style="max-width: 100%;">
                    <i class="fas fa-envelope me-1.5"></i> {{ $dosen_pembimbing['email'] }}
                </a>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $dosen_pembimbing['telepon']) }}" target="_blank" class="btn btn-sm btn-light-success fw-bold px-3 py-2 rounded-2">
                    <i class="fab fa-whatsapp me-1.5"></i> {{ $dosen_pembimbing['telepon'] }}
                </a>
            </div>
        </div>
    </div>

    <!-- Dosen Wali Akademik -->
    <div class="col-12 col-md-6">
        <div class="simbim-card p-6 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-start justify-content-between gap-3 mb-4 pb-3 border-bottom border-gray-100">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-50px me-3">
                            <span class="symbol-label bg-light-success text-success fw-bolder fs-3 rounded-circle">
                                <i class="fas fa-user-shield fs-2 text-success"></i>
                            </span>
                        </div>
                        <div>
                            <span class="badge badge-soft-success fw-bold fs-9 px-2.5 py-0.5 rounded-pill mb-1">Dosen Wali Akademik</span>
                            <h3 class="fs-5 fw-bolder text-gray-900 mb-0">{{ $dosen_wali['nama'] }}</h3>
                            <span class="text-gray-400 fs-8">NIP: {{ $dosen_wali['nip'] }}</span>
                        </div>
                    </div>
                    <span class="badge badge-light-{{ $dosen_wali['badge_status'] }} fw-bold px-2.5 py-1.5 fs-8 text-nowrap rounded-pill">
                        <span class="pulse-dot me-1"></span> {{ $dosen_wali['status_hadir'] }}
                    </span>
                </div>

                <div class="d-flex flex-column gap-2.5 text-gray-700 fs-7 mb-2">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-briefcase text-success me-3 fs-6 w-20px text-center"></i>
                        <span class="fw-semibold text-gray-800">{{ $dosen_wali['jabatan'] }}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-microchip text-info me-3 fs-6 w-20px text-center"></i>
                        <span class="text-gray-600">{{ $dosen_wali['bidang'] }}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-building text-primary me-3 fs-6 w-20px text-center"></i>
                        <span class="text-gray-700">{{ $dosen_wali['ruangan'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Contact Toolbar -->
            <div class="pt-3 mt-4 border-top border-gray-100 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <a href="mailto:{{ $dosen_wali['email'] }}" class="btn btn-sm btn-light-danger fw-bold px-3 py-2 rounded-2 text-truncate" style="max-width: 100%;">
                    <i class="fas fa-envelope me-1.5"></i> {{ $dosen_wali['email'] }}
                </a>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $dosen_wali['telepon']) }}" target="_blank" class="btn btn-sm btn-light-success fw-bold px-3 py-2 rounded-2">
                    <i class="fab fa-whatsapp me-1.5"></i> {{ $dosen_wali['telepon'] }}
                </a>
            </div>
        </div>
    </div>
</div>

<!-- 5. Section Feedback Dosen & Berkas Dokumen Terkini (Grid 7 & 5) -->
<div class="row g-4 g-xl-6 mb-7">
    <!-- Kolom Kiri (7): Feedback & Catatan Dosen Pembimbing Terbaru -->
    <div class="col-12 col-lg-7">
        <div class="simbim-card p-6 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-gray-100">
                    <div>
                        <h2 class="fs-4 fw-bolder text-gray-900 mb-1">Feedback & Catatan Pembimbing</h2>
                        <span class="text-gray-500 fs-7">Arahan dan revisi langsung dari dosen pembimbing</span>
                    </div>
                    <span class="badge badge-light-primary fw-bold fs-8 px-3 py-1.5 rounded-pill">
                        <i class="fas fa-comment-dots text-primary me-1"></i> {{ count($feedback_terbaru) }} Catatan
                    </span>
                </div>

                <div class="d-flex flex-column gap-3 mb-2">
                    @foreach ($feedback_terbaru as $fb)
                    <div class="p-4 rounded-3 bg-light border border-gray-200">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-2">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-35px me-2.5">
                                    <span class="symbol-label bg-white border border-gray-200 text-primary">
                                        <i class="fas fa-user-edit fs-6"></i>
                                    </span>
                                </div>
                                <div>
                                    <h4 class="fs-7 fw-bolder text-gray-900 mb-0">{{ $fb['dosen'] }}</h4>
                                    <span class="text-gray-500 fs-8">{{ $fb['bab'] }} • {{ $fb['tanggal'] }}</span>
                                </div>
                            </div>
                            <span class="badge badge-light-{{ $fb['badge'] }} fw-bold px-2.5 py-1 fs-9 rounded-pill">
                                {{ $fb['status'] }}
                            </span>
                        </div>
                        <div class="p-3 bg-white rounded-2 border border-gray-100 mt-2">
                            <p class="text-gray-700 fs-7 mb-0 fst-italic">
                                "{{ $fb['catatan'] }}"
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Footer Action -->
            <div class="pt-3 mt-3 border-top border-gray-100 d-flex justify-content-between align-items-center">
                <span class="text-gray-500 fs-8">Periksa catatan lengkap di menu bimbingan</span>
                <a href="{{ route('mahasiswa.bimbingan-skripsi') }}" class="text-primary fw-bold fs-8 text-hover-underline">Lihat Riwayat Revisi</a>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan (5): Berkas & Draft Dokumen Terkini -->
    <div class="col-12 col-lg-5">
        <div class="simbim-card p-6 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-gray-100">
                    <div>
                        <h2 class="fs-4 fw-bolder text-gray-900 mb-1">Berkas Draft Terkini</h2>
                        <span class="text-gray-500 fs-7">Status unggahan naskah skripsi</span>
                    </div>
                    <span class="badge badge-light-info fw-bold fs-8 px-3 py-1.5 rounded-pill">
                        {{ count($dokumen) }} Berkas
                    </span>
                </div>

                <div class="d-flex flex-column gap-2.5 mb-2">
                    @foreach ($dokumen as $dok)
                    <div class="p-3 rounded-2 bg-light border border-gray-100 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center me-2 text-truncate">
                            <div class="symbol symbol-35px me-2.5 flex-shrink-0">
                                <span class="symbol-label bg-white border border-gray-200 text-{{ str_contains($dok['nama'], '.pdf') ? 'danger' : 'primary' }}">
                                    <i class="fas {{ str_contains($dok['nama'], '.pdf') ? 'fa-file-pdf' : 'fa-file-word' }} fs-5"></i>
                                </span>
                            </div>
                            <div class="d-flex flex-column text-truncate">
                                <span class="fw-bolder fs-7 text-gray-900 text-truncate" title="{{ $dok['nama'] }}">{{ $dok['nama'] }}</span>
                                <span class="text-gray-500 fs-9">{{ $dok['waktu'] }}</span>
                            </div>
                        </div>
                        <span class="badge badge-light-{{ $dok['badge'] }} fw-bold px-2 py-1 fs-9 rounded-pill flex-shrink-0">
                            {{ $dok['status'] }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Footer Upload Link -->
            <div class="pt-3 mt-3 border-top border-gray-100 d-flex justify-content-between align-items-center">
                <span class="text-gray-500 fs-8">Format didukung: PDF, DOCX</span>
                <a href="{{ route('mahasiswa.bimbingan-skripsi') }}" class="btn btn-sm btn-light-primary fw-bold">
                    <i class="fas fa-upload me-1 fs-8"></i> Unggah Draft Baru
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function remindDosen(btn) {
        if (!btn) return;
        btn.disabled = true;
        btn.className = "btn btn-success w-100 fw-bolder py-2.5 rounded-2 shadow-xs d-flex align-items-center justify-content-center";
        btn.innerHTML = '<i class="fas fa-check-circle me-2"></i><span>Pengingat Terkirim ke Dosen</span>';
        alert("Notifikasi pengingat bimbingan H-1 berhasil dikirimkan kepada dosen pembimbing!");
    }
</script>
@endpush
