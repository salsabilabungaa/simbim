@extends('layouts.mahasiswa')

@section('title', 'Dashboard Mahasiswa')
@section('page-title', 'Dashboard')

@section('content')
<!-- 1. Banner Greeting Hero (Sapaan Awal Mahasiswa) -->
<div class="card mb-7 shadow-sm border-0 overflow-hidden" style="background: linear-gradient(135deg, #1e1e2d 0%, #1b1b28 50%, #2b2b40 100%);">
    <div class="card-body p-7 p-lg-9 position-relative">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between position-relative z-index-1">
            <div>
                <div class="d-flex align-items-center mb-2 flex-wrap gap-2">
                    <span class="badge badge-light-primary fw-bolder px-3 py-2 fs-8">
                        <i class="fas fa-graduation-cap me-1 text-primary"></i> Program Studi {{ $mahasiswa['program_studi'] }}
                    </span>
                    <span class="badge badge-light-info fw-bolder px-3 py-2 fs-8">
                        <i class="fas fa-user-graduate me-1 text-info"></i> {{ $mahasiswa['semester'] }}
                    </span>
                    <span class="badge badge-light-success fw-bolder px-3 py-2 fs-8">
                        <i class="fas fa-check-circle me-1 text-success"></i> {{ $mahasiswa['status'] }}
                    </span>
                </div>
                <h1 class="text-white fw-bolder fs-2x mb-1">
                    {{ $mahasiswa['nama'] }} 
                </h1>
                <p class="text-gray-400 fs-6 fw-semibold mb-0">
                    NIM: <span class="text-white fw-bold me-4">{{ $mahasiswa['nim'] }}</span>
                    Pembimbing: <span class="text-info fw-bold me-4">{{ $dosen_pembimbing['nama'] }}</span>
                    Dosen Wali: <span class="text-success fw-bold">{{ $dosen_wali['nama'] }}</span>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- 2. Ringkasan Utama: Progres Skripsi %, Rekapan Bimbingan Bulan Ini -->
<div class="row g-5 g-xl-8 mb-7">
    <!-- Kartu 1: Progres Skripsi di Skala Persen -->
    <div class="col-xl-6 col-md-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-body p-6 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-gray-600 fw-bolder fs-7 text-uppercase ls-1">PROGRES SKRIPSI</span>
                        <div class="symbol symbol-45px bg-light-primary">
                            <span class="symbol-label">
                                <i class="fas fa-chart-line text-primary fs-2"></i>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline mb-2">
                        <span class="fs-3x fw-bolder text-dark me-3">{{ $bab['persen'] }}%</span>
                        <span class="badge badge-light-success fs-8 fw-bolder">
                            <i class="fas fa-arrow-up me-1 text-success"></i> +7% bulan ini
                        </span>
                    </div>
                    <div class="progress h-10px bg-light-primary mb-3 rounded-pill">
                        <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: {{ $bab['persen'] }}%" aria-valuenow="{{ $bab['persen'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex align-items-center text-gray-800 fw-bolder fs-6 mb-1">
                        <i class="fas fa-bookmark text-primary me-2 fs-7"></i> {{ $bab['posisi'] }}
                    </div>
                    <div class="text-muted fs-7">{{ $bab['keterangan'] }}</div>
                </div>
                <div class="pt-4 mt-3 border-top border-gray-200 d-flex justify-content-between align-items-center">
                    <span class="text-gray-500 fs-8 fw-bold">Target Selesai: {{ $bab['target_selesai'] }}</span>
                    <span class="badge badge-light-primary fw-bolder fs-8">65 dari 100%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Kartu 2: Rekapan Bimbingan Bulan Ini (Skripsi & Akademik) -->
    <div class="col-xl-6 col-md-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-body p-6 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-gray-600 fw-bolder fs-7 text-uppercase ls-1">REKAPAN BIMBINGAN BULAN INI</span>
                        <div class="symbol symbol-45px bg-light-success">
                            <span class="symbol-label">
                                <i class="fas fa-comments text-success fs-2"></i>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline mb-3">
                        <span class="fs-3x fw-bolder text-dark me-2">{{ $rekapan_bimbingan['total_bulan_ini'] }}</span>
                        <span class="fs-6 text-muted fw-bold">Total Sesi</span>
                        <span class="badge badge-light-success ms-auto fw-bolder fs-8 px-2.5 py-1">
                            {{ $rekapan_bimbingan['status_kuota'] }}
                        </span>
                    </div>

                    <div class="p-3 rounded bg-light mb-2 border border-gray-100">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bolder fs-7 text-dark">
                                <i class="fas fa-book-reader text-primary me-1"></i> Bimbingan Skripsi
                            </span>
                            <span class="badge badge-light-success fw-bolder fs-8">{{ $rekapan_bimbingan['skripsi']['status'] }}</span>
                        </div>
                        <div class="progress h-6px bg-light-primary rounded-pill">
                            <div class="progress-bar bg-primary rounded-pill" style="width: 100%"></div>
                        </div>
                    </div>

                    <div class="p-3 rounded bg-light border border-gray-100">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bolder fs-7 text-dark">
                                <i class="fas fa-user-graduate text-info me-1"></i> Bimbingan Akademik
                            </span>
                            <span class="badge badge-light-info fw-bolder fs-8">{{ $rekapan_bimbingan['akademik']['status'] }}</span>
                        </div>
                        <div class="progress h-6px bg-light-info rounded-pill">
                            <div class="progress-bar bg-info rounded-pill" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. Section Dosen Pembimbing & Dosen Wali (Posisinya Di Antara Bagan Utama dan Feedback) -->
<div class="row g-5 g-xl-8 mb-7">
    <!-- Bagan Dosen Pembimbing Utama -->
    <div class="col-md-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-header border-0 pt-6">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder fs-4 text-dark">Dosen Pembimbing</span>
                    <span class="text-muted mt-1 fw-bold fs-7">Informasi profil dosen pembimbing utama</span>
                </h3>
            </div>
            <div class="card-body pt-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h4 class="text-dark fw-bolder mb-0 fs-5">{{ $dosen_pembimbing['nama'] }}</h4>
                    <span class="badge badge-light-{{ $dosen_pembimbing['badge_status'] }} fw-bolder px-2.5 py-1 fs-8">
                        <i class="fas fa-circle text-{{ $dosen_pembimbing['badge_status'] }} fs-9 me-1"></i> {{ $dosen_pembimbing['status_hadir'] }}
                    </span>
                </div>
                <div class="text-muted fs-7 fw-semibold mb-3">NIP: {{ $dosen_pembimbing['nip'] }}</div>

                <div class="text-start border-top border-gray-200 pt-3">
                    <div class="d-flex align-items-center mb-2.5">
                        <i class="fas fa-briefcase text-primary me-3 fs-6 w-20px"></i>
                        <span class="text-gray-800 fw-bold fs-7">{{ $dosen_pembimbing['jabatan'] }}</span>
                    </div>
                    <div class="d-flex align-items-center mb-2.5">
                        <i class="fas fa-lightbulb text-warning me-3 fs-6 w-20px"></i>
                        <span class="text-gray-800 fs-7">{{ $dosen_pembimbing['bidang'] }}</span>
                    </div>
                    <div class="d-flex align-items-center mb-2.5">
                        <i class="fas fa-envelope text-danger me-3 fs-6 w-20px"></i>
                        <a href="mailto:{{ $dosen_pembimbing['email'] }}" class="text-primary text-hover-underline fs-7 fw-bold">{{ $dosen_pembimbing['email'] }}</a>
                    </div>
                    <div class="d-flex align-items-center mb-2.5">
                        <i class="fas fa-phone-alt text-success me-3 fs-6 w-20px"></i>
                        <span class="text-gray-800 fs-7 fw-bold">{{ $dosen_pembimbing['telepon'] }}</span>
                    </div>
                    <div class="d-flex align-items-center mb-1">
                        <i class="fas fa-building text-info me-3 fs-6 w-20px"></i>
                        <span class="text-gray-800 fs-7">{{ $dosen_pembimbing['ruangan'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bagan Dosen Wali (Posisinya Di Antara Dosen Pembimbing & Feedback) -->
    <div class="col-md-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-header border-0 pt-6">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder fs-4 text-dark">Dosen Wali Akademik</span>
                    <span class="text-muted mt-1 fw-bold fs-7">Informasi profil dosen wali akademik</span>
                </h3>
            </div>
            <div class="card-body pt-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h4 class="text-dark fw-bolder mb-0 fs-5">{{ $dosen_wali['nama'] }}</h4>
                    <span class="badge badge-light-{{ $dosen_wali['badge_status'] }} fw-bolder px-2.5 py-1 fs-8">
                        <i class="fas fa-circle text-{{ $dosen_wali['badge_status'] }} fs-9 me-1"></i> {{ $dosen_wali['status_hadir'] }}
                    </span>
                </div>
                <div class="text-muted fs-7 fw-semibold mb-3">NIP: {{ $dosen_wali['nip'] }}</div>

                <div class="text-start border-top border-gray-200 pt-3">
                    <div class="d-flex align-items-center mb-2.5">
                        <i class="fas fa-user-shield text-success me-3 fs-6 w-20px"></i>
                        <span class="text-gray-800 fw-bold fs-7">{{ $dosen_wali['jabatan'] }}</span>
                    </div>
                    <div class="d-flex align-items-center mb-2.5">
                        <i class="fas fa-microchip text-info me-3 fs-6 w-20px"></i>
                        <span class="text-gray-800 fs-7">{{ $dosen_wali['bidang'] }}</span>
                    </div>
                    <div class="d-flex align-items-center mb-2.5">
                        <i class="fas fa-envelope text-danger me-3 fs-6 w-20px"></i>
                        <a href="mailto:{{ $dosen_wali['email'] }}" class="text-primary text-hover-underline fs-7 fw-bold">{{ $dosen_wali['email'] }}</a>
                    </div>
                    <div class="d-flex align-items-center mb-2.5">
                        <i class="fas fa-phone-alt text-success me-3 fs-6 w-20px"></i>
                        <span class="text-gray-800 fs-7 fw-bold">{{ $dosen_wali['telepon'] }}</span>
                    </div>
                    <div class="d-flex align-items-center mb-1">
                        <i class="fas fa-building text-primary me-3 fs-6 w-20px"></i>
                        <span class="text-gray-800 fs-7">{{ $dosen_wali['ruangan'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 4. Section Feedback & Catatan Dosen Pembimbing Terbaru (Posisinya Setelah Dosen Pembimbing & Dosen Wali) -->
<div class="card card-custom bg-body border border-gray-200 shadow-sm mb-7">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bolder fs-4 text-dark">Feedback & Catatan Pembimbing Terbaru</span>
            <span class="text-muted mt-1 fw-bold fs-7">Masukan dan arahan dari dosen pembimbing untuk revisi draft</span>
        </h3>
        <div class="card-toolbar">
            <span class="badge badge-light-info fw-bolder fs-8 px-3 py-2">
                <i class="fas fa-comment-dots text-info me-1"></i> {{ count($feedback_terbaru) }} Catatan
            </span>
        </div>
    </div>
    <div class="card-body pt-3">
        <div class="d-flex flex-column gap-4">
            @foreach ($feedback_terbaru as $fb)
            <div class="p-5 rounded-3 bg-light border border-gray-200">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-35px me-3">
                            <span class="symbol-label bg-light-primary">
                                <i class="fas fa-user-tie text-primary fs-5"></i>
                            </span>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="text-dark fw-bolder fs-6">{{ $fb['dosen'] }}</span>
                            <span class="text-muted fs-8">{{ $fb['bab'] }} • {{ $fb['tanggal'] }}</span>
                        </div>
                    </div>
                    <span class="badge badge-light-{{ $fb['badge'] }} fw-bolder px-3 py-2 fs-8">
                        {{ $fb['status'] }}
                    </span>
                </div>
                <p class="text-gray-700 fs-7 fw-semibold mb-0 mt-2">
                    "{{ $fb['catatan'] }}"
                </p>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function remindDosen(btn) {
        if (!btn) return;
        btn.disabled = true;
        btn.className = "btn btn-success btn-sm fw-bolder px-3 py-1.5 fs-8";
        btn.innerHTML = '<i class="fas fa-check me-1"></i> PENGINGAT TERKIRIM';
        alert("Notifikasi pengingat bimbingan H-1 berhasil dikirimkan kepada dosen pembimbing!");
    }
</script>
@endpush
