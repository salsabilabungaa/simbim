@extends('layouts.admin')

@section('title', 'Pemantauan Data Dosen (Global Access)')

@section('content')
<!-- Header Banner -->
<div class="card card-flush pb-0 mb-6 shadow-sm">
    <div class="card-body pt-6 pb-6">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge badge-light-danger fw-bolder fs-8">HAK AKSES GLOBAL ADMIN</span>
                    <span class="text-muted fs-8">&bull; Role Dosen</span>
                </div>
                <h2 class="text-dark fw-bolder mb-1">Data & Pemantauan Dosen Pembimbing / Wali</h2>
                <span class="text-muted fs-7">
                    Memantau kuota bimbingan skripsi & perwalian, pengajuan review mahasiswa masuk, serta aktivitas pengujian bimbingan.
                </span>
            </div>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-5 g-xl-8 mb-6">
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm bg-body">
            <div class="card-body py-5">
                <div class="text-gray-500 fw-bold fs-7 mb-1 text-uppercase">Total Dosen</div>
                <div class="text-dark fw-bolder fs-2x mb-1">{{ $daftarDosen instanceof \Illuminate\Pagination\LengthAwarePaginator ? $daftarDosen->total() : count($daftarDosen) }}</div>
                <span class="text-muted fs-8">Dosen pembimbing &amp; wali aktif {{ config('simbim.prodi') }} &mdash; {{ config('simbim.universitas') }}</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm bg-body">
            <div class="card-body py-5">
                <div class="text-gray-500 fw-bold fs-7 mb-1 text-uppercase">Total Mahasiswa Skripsi</div>
                <div class="text-dark fw-bolder fs-2x mb-1 text-primary">31 Mahasiswa</div>
                <span class="text-muted fs-8">Dalam bimbingan skripsi aktif</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm bg-body">
            <div class="card-body py-5">
                <div class="text-gray-500 fw-bold fs-7 mb-1 text-uppercase">Total Perwalian Akademik</div>
                <div class="text-dark fw-bolder fs-2x mb-1 text-success">90 Mahasiswa</div>
                <span class="text-muted fs-8">Perwalian KRS & Akademik</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm bg-body">
            <div class="card-body py-5">
                <div class="text-gray-500 fw-bold fs-7 mb-1 text-uppercase">Pengajuan Menunggu Review</div>
                <div class="text-dark fw-bolder fs-2x mb-1 text-warning">6 Antrean</div>
                <span class="text-muted fs-8">Draft bab menunggu konfirmasi dosen</span>
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card card-flush shadow-sm">
    <div class="card-header pt-6">
        <h3 class="card-title fw-bolder text-gray-800">Daftar Dosen Pembimbing & Akademik</h3>
    </div>
    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table table-row-bordered table-row-gray-200 align-middle gs-0 gy-4">
                <thead>
                    <tr class="fw-bolder text-muted fs-7 text-uppercase border-bottom">
                        <th class="min-w-220px">Nama & NIP</th>
                        <th class="min-w-180px">Jabatan & Bidang</th>
                        <th class="min-w-130px text-center">Bimbingan Skripsi</th>
                        <th class="min-w-130px text-center">Perwalian KRS</th>
                        <th class="min-w-140px text-center">Menunggu Review</th>
                        <th class="min-w-150px text-end">Status Hadir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($daftarDosen as $dsn)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40px symbol-circle me-3 bg-light-success text-success fw-bolder d-flex align-items-center justify-content-center">
                                        {{ strtoupper(substr($dsn['nama'], 0, 1)) }}
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark fw-bolder fs-6">{{ $dsn['nama'] }}</span>
                                        <span class="text-muted fs-7 font-monospace">NIP: {{ $dsn['nip'] }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-dark fw-bold fs-7">{{ $dsn['jabatan'] }}</span>
                                    <span class="text-muted fs-8 text-truncate" style="max-width: 200px;">{{ $dsn['bidang'] }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-light-primary fw-bolder fs-7 px-3 py-2">
                                    {{ $dsn['total_mhs_skripsi'] }} Mahasiswa
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-light-success fw-bolder fs-7 px-3 py-2">
                                    {{ $dsn['total_mhs_perwalian'] }} Mahasiswa
                                </span>
                            </td>
                            <td class="text-center">
                                @if ($dsn['pengajuan_menunggu'] > 0)
                                    <span class="badge badge-light-warning fw-bolder fs-7 px-3 py-2">
                                        {{ $dsn['pengajuan_menunggu'] }} Pengajuan
                                    </span>
                                @else
                                    <span class="badge badge-light fw-bold fs-8 px-2 py-1 text-muted">
                                        Nihil
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <span class="badge badge-light-{{ $dsn['badge'] }} fw-bolder fs-8 px-3 py-2">
                                    {{ $dsn['status_kehadiran'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @include('partials.pagination_footer', ['paginator' => $daftarDosen, 'label' => 'dosen'])
    </div>
</div>
@endsection

