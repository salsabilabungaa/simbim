@extends('layouts.admin')

@section('title', 'Pemantauan Data Mahasiswa (Global Access)')

@section('content')
<!-- Header Banner -->
<div class="card card-flush pb-0 mb-6 shadow-sm">
    <div class="card-body pt-6 pb-6">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge badge-light-danger fw-bolder fs-8">HAK AKSES GLOBAL ADMIN</span>
                    <span class="text-muted fs-8">&bull; Role Mahasiswa</span>
                </div>
                <h2 class="text-dark fw-bolder mb-1">Data & Pemantauan Mahasiswa</h2>
                <span class="text-muted fs-7">
                    Memantau perkembangan bimbingan akademik, progres bab skripsi, dan riwayat konsultasi seluruh mahasiswa.
                </span>
            </div>
        </div>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="row g-5 g-xl-8 mb-6">
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm bg-body">
            <div class="card-body py-5">
                <div class="text-gray-500 fw-bold fs-7 mb-1 text-uppercase">Total Mahasiswa</div>
                <div class="text-dark fw-bolder fs-2x mb-1">{{ $daftarMahasiswa instanceof \Illuminate\Pagination\LengthAwarePaginator ? $daftarMahasiswa->total() : count($daftarMahasiswa) }}</div>
                <span class="text-muted fs-8">Terdaftar di Prodi {{ config('simbim.prodi') }} &mdash; {{ config('simbim.universitas') }}</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm bg-body">
            <div class="card-body py-5">
                <div class="text-gray-500 fw-bold fs-7 mb-1 text-uppercase">Bimbingan Skripsi Aktif</div>
                <div class="text-dark fw-bolder fs-2x mb-1 text-primary">{{ $aktifSkripsi ?? count($daftarMahasiswa) }}</div>
                <span class="text-muted fs-8">Sedang menyusun Bab I - V</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm bg-body">
            <div class="card-body py-5">
                <div class="text-gray-500 fw-bold fs-7 mb-1 text-uppercase">Rata-rata Progres Skripsi</div>
                <div class="text-dark fw-bolder fs-2x mb-1 text-success">{{ $rataProgres ?? 0 }}%</div>
                <span class="text-muted fs-8">Metodologi &amp; Pengujian Sistem</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm bg-body">
            <div class="card-body py-5">
                <div class="text-gray-500 fw-bold fs-7 mb-1 text-uppercase">Siap Sempro / Sidang</div>
                <div class="text-dark fw-bolder fs-2x mb-1 text-warning">{{ $siapSidang ?? 0 }} Mahasiswa</div>
                <span class="text-muted fs-8">Bab IV &amp; V mendekati selesai</span>
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card card-flush shadow-sm">
    <div class="card-header pt-6">
        <h3 class="card-title fw-bolder text-gray-800">Daftar Progres Bimbingan Mahasiswa</h3>
    </div>
    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table table-row-bordered table-row-gray-200 align-middle gs-0 gy-4">
                <thead>
                    <tr class="fw-bolder text-muted fs-7 text-uppercase border-bottom">
                        <th class="min-w-220px">Mahasiswa & NIM</th>
                        <th class="min-w-200px">Dosen Pembimbing & Wali</th>
                        <th class="min-w-260px">Judul Skripsi & Bab Aktif</th>
                        <th class="min-w-160px">Progres Skripsi</th>
                        <th class="min-w-140px">Sesi Bimbingan</th>
                        <th class="min-w-140px text-end">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($daftarMahasiswa as $mhs)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40px symbol-circle me-3 bg-light-primary text-primary fw-bolder d-flex align-items-center justify-content-center">
                                        {{ strtoupper(substr($mhs['nama'], 0, 1)) }}
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark fw-bolder fs-6">{{ $mhs['nama'] }}</span>
                                        <span class="text-muted fs-7 font-monospace">{{ $mhs['nim'] }} &bull; {{ $mhs['semester'] }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-dark fw-bold fs-7"><i class="fas fa-chalkboard-teacher text-primary me-1 fs-8"></i> {{ $mhs['pembimbing'] }}</span>
                                    <span class="text-muted fs-8"><i class="fas fa-user-tie text-success me-1 fs-8"></i> {{ $mhs['dosen_wali'] }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-dark fw-bold fs-7 text-truncate" style="max-width: 250px;" title="{{ $mhs['judul_skripsi'] }}">
                                        {{ $mhs['judul_skripsi'] }}
                                    </span>
                                    <span class="text-primary fw-semibold fs-8 mt-1">
                                        <i class="fas fa-bookmark me-1 text-primary fs-9"></i> {{ $mhs['bab_aktif'] }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column w-100 me-2">
                                    <div class="d-flex flex-stack mb-1">
                                        <span class="text-muted fs-8 fw-bold">Penyelesaian</span>
                                        <span class="text-dark fs-8 fw-bolder">{{ $mhs['progres_persen'] }}%</span>
                                    </div>
                                    <div class="progress h-6px bg-light-primary rounded-pill">
                                        <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: {{ $mhs['progres_persen'] }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="badge badge-light-primary fw-bolder fs-8 align-self-start mb-1">
                                        {{ $mhs['sesi_skripsi'] }} Sesi Skripsi
                                    </span>
                                    <span class="text-muted fs-8">{{ $mhs['sesi_akademik'] }} Sesi Akademik</span>
                                </div>
                            </td>
                            <td class="text-end">
                                <span class="badge badge-light-{{ $mhs['badge'] }} fw-bolder fs-8 px-3 py-2">
                                    {{ $mhs['status_kelayakan'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @include('partials.pagination_footer', ['paginator' => $daftarMahasiswa, 'label' => 'mahasiswa'])
    </div>
</div>
@endsection

