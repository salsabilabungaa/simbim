@extends('layouts.dewan-skripsi')

@section('title', 'Dasbor Progres Skripsi — Dewan Skripsi')
@section('page_title', 'Dasbor Progres Skripsi')

@section('content')
<!-- Header Banner Info Role Dewan Skripsi -->
<div class="card card-flush shadow-sm mb-6 bg-gradient border-0" style="background: linear-gradient(120deg, #1e1e2d 0%, #2c3e50 100%);">
    <div class="card-body py-6 px-8 text-white">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
            <div>
                <div class="d-flex align-items-center mb-2">
                    <span class="badge badge-warning fw-bolder fs-8 px-3 py-1 me-2 text-uppercase ls-1">
                        <i class="fas fa-shield-alt me-1 text-dark"></i> Dewan Skripsi
                    </span>
                    <span class="text-white-50 fs-7">Sistem Informasi Pengelolaan Tugas Akhir (SIMBIM)</span>
                </div>
                <h2 class="text-white fw-bolder mb-1">Dasbor Pemantauan Progres Skripsi Mahasiswa</h2>
                <p class="text-gray-300 fs-7 mb-0">
                    Kelola alur penyelesaian tugas akhir secara end-to-end: penetapan dosen pembimbing & penguji, approval pendaftaran sidang, hingga verifikasi berkas pasca-sidang dan kelulusan yudisium.
                </p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('dewan-skripsi.plotting') }}" class="btn btn-sm btn-info fw-bolder">
                    <i class="fas fa-user-friends me-1"></i> Plotting Dosen & Jadwal
                </a>
                <a href="{{ route('dewan-skripsi.validasi-sidang') }}" class="btn btn-sm btn-warning fw-bolder">
                    <i class="fas fa-clipboard-check me-1"></i> Validasi Sidang ({{ $stats['pending_approval'] }})
                </a>
                <a href="{{ route('dewan-skripsi.verifikasi-pasca-sidang') }}" class="btn btn-sm btn-success fw-bolder">
                    <i class="fas fa-file-signature me-1"></i> Pasca-Sidang ({{ $stats['pending_verifikasi'] }})
                </a>
            </div>
        </div>
    </div>
</div>

<!-- 5 Stat Cards: Status Tahap Tugas Akhir Mahasiswa Secara Detail -->
<div class="row g-4 mb-6">
    <!-- Stat 1: Total Mahasiswa -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card card-flush shadow-sm h-100 border-start border-4 border-dark">
            <div class="card-body p-5">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted fw-bold fs-8 text-uppercase">Total Skripsi</span>
                    <span class="badge badge-light-dark p-2"><i class="fas fa-book fs-6 text-dark"></i></span>
                </div>
                <div class="fs-2hx fw-bolder text-gray-900 mb-1">{{ $stats['total'] }}</div>
                <span class="text-muted fs-8">Mahasiswa terdaftar</span>
            </div>
        </div>
    </div>

    <!-- Stat 2: Tahap Penyusunan Proposal -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card card-flush shadow-sm h-100 border-start border-4 border-secondary">
            <div class="card-body p-5">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted fw-bold fs-8 text-uppercase">1. Proposal</span>
                    <span class="badge badge-light-secondary p-2"><i class="fas fa-file-alt fs-6 text-gray-600"></i></span>
                </div>
                <div class="fs-2hx fw-bolder text-gray-700 mb-1">{{ $stats['proposal'] }}</div>
                <span class="text-muted fs-8">Penyusunan awal</span>
            </div>
        </div>
    </div>

    <!-- Stat 3: Tahap Bimbingan Aktif -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card card-flush shadow-sm h-100 border-start border-4 border-info">
            <div class="card-body p-5">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-info fw-bold fs-8 text-uppercase">2. Bimbingan Aktif</span>
                    <span class="badge badge-light-info p-2"><i class="fas fa-chalkboard-teacher fs-6 text-info"></i></span>
                </div>
                <div class="fs-2hx fw-bolder text-info mb-1">{{ $stats['bimbingan_aktif'] }}</div>
                <span class="text-muted fs-8">Proses draft naskah</span>
            </div>
        </div>
    </div>

    <!-- Stat 4: Tahap Siap Sidang -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card card-flush shadow-sm h-100 border-start border-4 border-primary">
            <div class="card-body p-5">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-primary fw-bold fs-8 text-uppercase">3. Siap Sidang</span>
                    <span class="badge badge-light-primary p-2"><i class="fas fa-user-check fs-6 text-primary"></i></span>
                </div>
                <div class="fs-2hx fw-bolder text-primary mb-1">{{ $stats['siap_sidang'] }}</div>
                <span class="text-muted fs-8">ACC / Menunggu Ujian</span>
            </div>
        </div>
    </div>

    <!-- Stat 5: Tahap Revisi Final -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card card-flush shadow-sm h-100 border-start border-4 border-danger">
            <div class="card-body p-5">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-danger fw-bold fs-8 text-uppercase">4. Revisi Final</span>
                    <span class="badge badge-light-danger p-2"><i class="fas fa-pencil-alt fs-6 text-danger"></i></span>
                </div>
                <div class="fs-2hx fw-bolder text-danger mb-1">{{ $stats['revisi_final'] }}</div>
                <span class="text-muted fs-8">Pasca-sidang</span>
            </div>
        </div>
    </div>

    <!-- Stat 6: Tahap Lulus / Selesai -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card card-flush shadow-sm h-100 border-start border-4 border-success">
            <div class="card-body p-5">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-success fw-bold fs-8 text-uppercase">5. Lulus/Selesai</span>
                    <span class="badge badge-light-success p-2"><i class="fas fa-award fs-6 text-success"></i></span>
                </div>
                <div class="fs-2hx fw-bolder text-success mb-1">{{ $stats['lulus'] }}</div>
                <span class="text-muted fs-8">Siap yudisium</span>
            </div>
        </div>
    </div>
</div>

<!-- Main Table: Pemantauan Progres Tugas Akhir Mahasiswa -->
<div class="card card-flush shadow-sm">
    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
        <div class="card-title d-flex flex-column">
            <h3 class="fw-bolder fs-4 text-gray-900 mb-1">
                <i class="fas fa-list-alt text-primary me-2"></i> Daftar Detail Tahap Skripsi Mahasiswa
            </h3>
            <span class="text-muted fs-7">Memantau secara langsung status setiap mahasiswa dari proposal hingga kelulusan</span>
        </div>

        <div class="card-toolbar flex-wrap gap-3">
            <!-- Filter Tahap Skripsi Buttons -->
            <div class="d-flex flex-wrap gap-1">
                <a href="{{ route('dewan-skripsi.dashboard', ['tahap' => 'semua', 'search' => $search]) }}" class="btn btn-sm {{ $selectedTahap === 'semua' ? 'btn-primary' : 'btn-light' }} fw-bold">
                    Semua ({{ $stats['total'] }})
                </a>
                <a href="{{ route('dewan-skripsi.dashboard', ['tahap' => 'Penyusunan Proposal', 'search' => $search]) }}" class="btn btn-sm {{ $selectedTahap === 'Penyusunan Proposal' ? 'btn-secondary' : 'btn-light' }} fw-bold">
                    Proposal ({{ $stats['proposal'] }})
                </a>
                <a href="{{ route('dewan-skripsi.dashboard', ['tahap' => 'Proses Bimbingan Aktif', 'search' => $search]) }}" class="btn btn-sm {{ $selectedTahap === 'Proses Bimbingan Aktif' ? 'btn-info' : 'btn-light' }} fw-bold">
                    Bimbingan ({{ $stats['bimbingan_aktif'] }})
                </a>
                <a href="{{ route('dewan-skripsi.dashboard', ['tahap' => 'Siap Sidang', 'search' => $search]) }}" class="btn btn-sm {{ $selectedTahap === 'Siap Sidang' ? 'btn-primary' : 'btn-light' }} fw-bold">
                    Siap Sidang ({{ $stats['siap_sidang'] }})
                </a>
                <a href="{{ route('dewan-skripsi.dashboard', ['tahap' => 'Revisi Final', 'search' => $search]) }}" class="btn btn-sm {{ $selectedTahap === 'Revisi Final' ? 'btn-danger' : 'btn-light' }} fw-bold">
                    Revisi Final ({{ $stats['revisi_final'] }})
                </a>
                <a href="{{ route('dewan-skripsi.dashboard', ['tahap' => 'Lulus/Selesai', 'search' => $search]) }}" class="btn btn-sm {{ $selectedTahap === 'Lulus/Selesai' ? 'btn-success' : 'btn-light' }} fw-bold">
                    Lulus ({{ $stats['lulus'] }})
                </a>
            </div>

            <!-- Search Form -->
            <form action="{{ route('dewan-skripsi.dashboard') }}" method="GET" class="d-flex align-items-center position-relative my-1">
                <input type="hidden" name="tahap" value="{{ $selectedTahap }}" />
                <span class="svg-icon svg-icon-1 position-absolute ms-3">
                    <i class="fas fa-search text-gray-500"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm form-control-solid w-200px w-lg-250px ps-10" placeholder="Cari Mahasiswa/NIM/Judul..." />
                @if($search)
                    <a href="{{ route('dewan-skripsi.dashboard', ['tahap' => $selectedTahap]) }}" class="btn btn-sm btn-icon btn-light ms-1" title="Hapus pencarian">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table table-row-bordered table-row-dashed align-middle gs-4 gy-4">
                <thead class="bg-light fw-bolder fs-7 text-uppercase text-gray-700">
                    <tr>
                        <th class="w-50px">No</th>
                        <th class="min-w-200px">Mahasiswa</th>
                        <th class="min-w-250px">Judul Skripsi</th>
                        <th class="min-w-180px">Dosen Pembimbing</th>
                        <th class="min-w-180px">Dosen Penguji</th>
                        <th class="min-w-160px text-center">Status Tahap</th>
                        <th class="min-w-140px text-center">Rekam Jejak</th>
                        <th class="min-w-120px text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="fs-7 fw-bold text-gray-600">
                    @forelse($skripsis as $index => $item)
                        <tr>
                            <td>{{ $skripsis->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40px me-3">
                                        <img src="{{ $item->mahasiswa?->avatar_url ?? asset('assets/media/avatars/300-1.jpg') }}" alt="{{ $item->mahasiswa?->name }}" />
                                    </div>
                                    <div class="d-flex flex-column">
                                        <a href="#" class="text-gray-900 text-hover-primary fw-bolder mb-1 fs-6">
                                            {{ $item->mahasiswa?->name ?? 'Mahasiswa' }}
                                        </a>
                                        <span class="text-muted fs-8">NIM: {{ $item->mahasiswa?->nim ?? '-' }}</span>
                                        <span class="badge badge-light fs-9 text-gray-600 align-self-start mt-0.5">
                                            {{ $item->mahasiswa?->program_studi ?? 'Informatika' }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-dark fw-bold mb-1" style="max-width: 320px; line-height: 1.4;">
                                    {{ $item->judul }}
                                </div>
                                @if($item->bidang_minat)
                                    <span class="badge badge-light-primary fw-bold fs-9">
                                        <i class="fas fa-tag me-1 text-primary"></i> {{ $item->bidang_minat }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column fs-8">
                                    <div class="mb-1">
                                        <span class="badge badge-light-info fw-bolder me-1">P1</span>
                                        <span class="text-gray-800">{{ $item->nama_pembimbing_1 }}</span>
                                    </div>
                                    <div>
                                        <span class="badge badge-light-secondary fw-bolder me-1">P2</span>
                                        <span class="text-gray-800">{{ $item->nama_pembimbing_2 }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column fs-8">
                                    <div class="mb-1">
                                        <span class="badge badge-light-warning fw-bolder me-1">U1</span>
                                        <span class="text-gray-800">{{ $item->nama_penguji_1 }}</span>
                                    </div>
                                    <div>
                                        <span class="badge badge-light-secondary fw-bolder me-1">U2</span>
                                        <span class="text-gray-800">{{ $item->nama_penguji_2 }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                @php
                                    $badgeTahap = match($item->tahap) {
                                        'Penyusunan Proposal' => 'badge-light-secondary text-gray-800',
                                        'Proses Bimbingan Aktif' => 'badge-light-info text-info',
                                        'Siap Sidang' => 'badge-light-primary text-primary',
                                        'Revisi Final' => 'badge-light-danger text-danger',
                                        'Lulus/Selesai' => 'badge-light-success text-success',
                                        default => 'badge-light-dark',
                                    };
                                    $iconTahap = match($item->tahap) {
                                        'Penyusunan Proposal' => 'fa-file-alt',
                                        'Proses Bimbingan Aktif' => 'fa-chalkboard-teacher',
                                        'Siap Sidang' => 'fa-user-check',
                                        'Revisi Final' => 'fa-pencil-alt',
                                        'Lulus/Selesai' => 'fa-graduation-cap',
                                        default => 'fa-info-circle',
                                    };
                                @endphp
                                <span class="badge {{ $badgeTahap }} fw-bolder fs-8 py-2 px-3">
                                    <i class="fas {{ $iconTahap }} me-1"></i> {{ $item->tahap }}
                                </span>

                                @if($item->jadwal_sidang)
                                    <div class="mt-1 text-muted fs-9">
                                        <i class="far fa-calendar-alt text-primary me-1"></i>
                                        {{ $item->jadwal_sidang->translatedFormat('d M Y, H:i') }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex flex-column align-items-center">
                                    <span class="badge badge-light fw-bold text-gray-800 mb-1">
                                        <i class="fas fa-comments text-info me-1"></i> {{ $item->total_bimbingan }}x Bimbingan
                                    </span>
                                    @if($item->acc_pembimbing)
                                        <span class="badge badge-success fs-9 py-0.5 px-2">
                                            <i class="fas fa-check-circle me-1"></i> ACC Pembimbing
                                        </span>
                                    @else
                                        <span class="badge badge-light-warning fs-9 py-0.5 px-2 text-warning">
                                            Belum ACC Sidang
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light btn-active-light-primary dropdown-toggle fw-bold" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Aksi
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li>
                                            <button class="dropdown-item py-2" data-bs-toggle="modal" data-bs-target="#modal_detail_{{ $item->id }}">
                                                <i class="fas fa-eye text-primary me-2"></i> Detail Progres Lengkap
                                            </button>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ route('dewan-skripsi.plotting') }}?search={{ $item->mahasiswa?->nim }}">
                                                <i class="fas fa-user-friends text-info me-2"></i> Atur Plotting & Jadwal
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ route('dewan-skripsi.validasi-sidang') }}?search={{ $item->mahasiswa?->nim }}">
                                                <i class="fas fa-clipboard-check text-warning me-2"></i> Validasi Sidang
                                            </a>
                                        </li>
                                        @if(in_array($item->tahap, ['Revisi Final', 'Lulus/Selesai', 'Siap Sidang']))
                                            <li>
                                                <a class="dropdown-item py-2" href="{{ route('dewan-skripsi.verifikasi-pasca-sidang') }}?search={{ $item->mahasiswa?->nim }}">
                                                    <i class="fas fa-file-signature text-success me-2"></i> Verifikasi Nilai / Lulus
                                                </a>
                                            </li>
                                        @endif
                                        @if($item->status_kelulusan === 'Lulus/Selesai')
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item py-2 text-success fw-bold" href="{{ route('dewan-skripsi.cetak-surat', $item->id) }}" target="_blank">
                                                    <i class="fas fa-print text-success me-2"></i> Cetak Surat Keterangan Lulus (SKL)
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Detail Progres Lengkap Mahasiswa -->
                        <div class="modal fade" id="modal_detail_{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content rounded-4 border-0 shadow-lg">
                                    <div class="modal-header bg-primary text-white py-4 px-6 rounded-top-4">
                                        <h5 class="modal-title text-white fw-bolder">
                                            <i class="fas fa-info-circle me-2"></i> Detail Riwayat & Progres Skripsi
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-6">
                                        <!-- Header Data Mahasiswa -->
                                        <div class="d-flex align-items-center pb-4 mb-4 border-bottom">
                                            <div class="symbol symbol-50px me-4">
                                                <img src="{{ $item->mahasiswa?->avatar_url ?? asset('assets/media/avatars/300-1.jpg') }}" alt="{{ $item->mahasiswa?->name }}" />
                                            </div>
                                            <div>
                                                <h4 class="fw-bolder text-dark mb-0">{{ $item->mahasiswa?->name }}</h4>
                                                <span class="text-muted fs-7">NIM: {{ $item->mahasiswa?->nim }} | Program Studi {{ $item->mahasiswa?->program_studi }}</span>
                                                <div class="mt-1">
                                                    <span class="badge {{ $badgeTahap }} fw-bolder px-3 py-1">
                                                        Tahap: {{ $item->tahap }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Judul Skripsi -->
                                        <div class="mb-4">
                                            <label class="fw-bold fs-7 text-muted text-uppercase">Judul Tugas Akhir / Skripsi</label>
                                            <p class="fs-6 fw-bolder text-gray-900 bg-light p-3 rounded">
                                                {{ $item->judul }}
                                            </p>
                                        </div>

                                        <!-- Susunan Dosen -->
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <div class="p-3 border rounded">
                                                    <span class="fw-bold fs-8 text-primary text-uppercase d-block mb-1">Dosen Pembimbing</span>
                                                    <div class="fs-7 mb-1"><strong>1. Pembimbing Utama:</strong> {{ $item->nama_pembimbing_1 }}</div>
                                                    <div class="fs-7"><strong>2. Pembimbing Pendamping:</strong> {{ $item->nama_pembimbing_2 }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="p-3 border rounded">
                                                    <span class="fw-bold fs-8 text-warning text-uppercase d-block mb-1">Dosen Penguji Sidang</span>
                                                    <div class="fs-7 mb-1"><strong>1. Ketua Penguji:</strong> {{ $item->nama_penguji_1 }}</div>
                                                    <div class="fs-7"><strong>2. Anggota Penguji:</strong> {{ $item->nama_penguji_2 }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Status Sidang & Kelulusan -->
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <div class="p-3 bg-light rounded">
                                                    <span class="fw-bold fs-8 text-muted text-uppercase d-block mb-1">Jadwal Pelaksanaan Sidang</span>
                                                    <div class="fs-7">
                                                        <i class="far fa-calendar-check text-primary me-1"></i>
                                                        <strong>Waktu:</strong> {{ $item->jadwal_sidang?->translatedFormat('l, d F Y - H:i') ?? 'Belum dijadwalkan' }} WIB
                                                    </div>
                                                    <div class="fs-7 mt-1">
                                                        <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                                        <strong>Ruang:</strong> {{ $item->ruangan_sidang ?? 'Belum ditentukan' }}
                                                    </div>
                                                    <div class="fs-7 mt-1">
                                                        <i class="fas fa-info-circle text-info me-1"></i>
                                                        <strong>Status Sidang:</strong> {{ $item->status_sidang }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="p-3 bg-light rounded">
                                                    <span class="fw-bold fs-8 text-muted text-uppercase d-block mb-1">Status Kelulusan & Nilai</span>
                                                    <div class="fs-7">
                                                        <strong>Nilai Akhir Sidang:</strong> 
                                                        @if($item->nilai_akhir)
                                                            <span class="badge badge-success fs-7 fw-bolder">{{ $item->nilai_akhir }} ({{ $item->nilai_huruf }})</span>
                                                        @else
                                                            <span class="text-muted">Belum ada rekap nilai</span>
                                                        @endif
                                                    </div>
                                                    <div class="fs-7 mt-1">
                                                        <strong>Status Kelulusan:</strong>
                                                        <span class="badge {{ $item->status_kelulusan === 'Lulus/Selesai' ? 'badge-success' : 'badge-warning' }} fw-bold">
                                                            {{ $item->status_kelulusan }}
                                                        </span>
                                                    </div>
                                                    @if($item->no_surat_keterangan)
                                                        <div class="fs-7 mt-1">
                                                            <strong>No. SKL:</strong> <span class="text-primary font-monospace">{{ $item->no_surat_keterangan }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        @if($item->catatan_validasi_sidang)
                                            <div class="alert alert-light-primary border border-primary p-3 mb-3">
                                                <div class="fw-bold fs-8 text-primary text-uppercase mb-1">Catatan Validasi Sidang Dewan Skripsi:</div>
                                                <div class="fs-7 text-gray-800">{{ $item->catatan_validasi_sidang }}</div>
                                            </div>
                                        @endif

                                        @if($item->catatan_dewan)
                                            <div class="alert alert-light-success border border-success p-3 mb-0">
                                                <div class="fw-bold fs-8 text-success text-uppercase mb-1">Catatan Keputusan Dewan Pasca-Sidang:</div>
                                                <div class="fs-7 text-gray-800">{{ $item->catatan_dewan }}</div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer bg-light py-3 px-6 rounded-bottom-4">
                                        <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Tutup</button>
                                        <a href="{{ route('dewan-skripsi.plotting') }}?search={{ $item->mahasiswa?->nim }}" class="btn btn-primary fw-bolder">
                                            <i class="fas fa-edit me-1"></i> Kelola Plotting & Sidang
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-folder-open fs-2hx text-muted mb-3"></i>
                                    <span class="text-gray-600 fw-bold fs-6">Tidak ada data skripsi yang sesuai dengan kriteria filter.</span>
                                    <span class="text-muted fs-8">Coba ganti filter tahap atau kata kunci pencarian.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center flex-wrap pt-4">
            <span class="text-muted fs-7">
                Menampilkan {{ $skripsis->firstItem() ?? 0 }} sampai {{ $skripsis->lastItem() ?? 0 }} dari {{ $skripsis->total() }} skripsi
            </span>
            <div>
                {{ $skripsis->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

