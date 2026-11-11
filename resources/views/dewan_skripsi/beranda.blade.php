@extends('layouts.dewan_skripsi')

@section('title', 'Beranda Dewan Skripsi')

@section('content')
<!-- Header Card -->
<div class="card card-flush pb-0 bgi-position-y-center bgi-no-repeat mb-8" style="background-color: #1e1e2d;">
    <div class="card-body pt-8 pb-8">
        <div class="d-flex flex-wrap flex-sm-nowrap align-items-center justify-content-between">
            <div>
                <span class="badge badge-light-warning fw-bolder mb-2 px-3 py-1 fs-8">DEWAN SKRIPSI & KOMISI TUGAS AKHIR</span>
                <h2 class="text-white fw-bolder fs-2x mb-1">{{ $dewan['nama'] }}</h2>
                <div class="text-gray-400 fs-7">
                    NIP: {{ $dewan['nip'] }} &bull; {{ $dewan['jabatan'] }} &bull; Prodi {{ $dewan['program_studi'] }}
                </div>
            </div>
            @if (auth()->check() && auth()->user()->isAdmin())
                <a href="{{ route('admin.monitoring.dewan-skripsi') }}" class="btn btn-primary fw-bolder">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Panel Monitoring Admin
                </a>
            @endif
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-5 g-xl-8 mb-8">
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm bg-body">
            <div class="card-body py-5">
                <div class="text-gray-500 fw-bold fs-7 mb-1 text-uppercase">Total Pengajuan Judul</div>
                <div class="text-dark fw-bolder fs-2x mb-1">{{ $statistik['total_proposal'] }}</div>
                <span class="text-muted fs-8">Proposal masuk periode ini</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm bg-body">
            <div class="card-body py-5">
                <div class="text-gray-500 fw-bold fs-7 mb-1 text-uppercase">Sidang Terjadwal Bulan Ini</div>
                <div class="text-dark fw-bolder fs-2x mb-1 text-primary">{{ $statistik['sidang_bulan_ini'] }} Mahasiswa</div>
                <span class="text-muted fs-8">Sempro & Komprehensif</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm bg-body">
            <div class="card-body py-5">
                <div class="text-gray-500 fw-bold fs-7 mb-1 text-uppercase">Menunggu Tim Penguji</div>
                <div class="text-dark fw-bolder fs-2x mb-1 text-warning">{{ $statistik['menunggu_penetapan_penguji'] }} Pengajuan</div>
                <span class="text-muted fs-8">Perlu ploting dosen penguji</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm bg-body">
            <div class="card-body py-5">
                <div class="text-gray-500 fw-bold fs-7 mb-1 text-uppercase">Mahasiswa Lulus Sidang</div>
                <div class="text-dark fw-bolder fs-2x mb-1 text-success">{{ $statistik['lulus_sidang'] }} Mahasiswa</div>
                <span class="text-muted fs-8">Telah menyelesaikan revisi</span>
            </div>
        </div>
    </div>
</div>

<!-- Table Agenda -->
<div class="card card-flush shadow-sm">
    <div class="card-header pt-6">
        <h3 class="card-title fw-bolder text-gray-800">Agenda Seminar & Sidang Mendatang</h3>
    </div>
    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table table-row-bordered table-row-gray-200 align-middle gs-0 gy-4">
                <thead>
                    <tr class="fw-bolder text-muted fs-7 text-uppercase border-bottom">
                        <th class="min-w-180px">Mahasiswa & NIM</th>
                        <th class="min-w-140px">Jenis Sidang</th>
                        <th class="min-w-260px">Judul Skripsi</th>
                        <th class="min-w-180px">Jadwal & Ruang</th>
                        <th class="min-w-100px text-end">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jadwal_sidang as $item)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40px symbol-circle me-3 bg-light-warning text-warning fw-bolder d-flex align-items-center justify-content-center">
                                        {{ strtoupper(substr($item['mahasiswa'], 0, 1)) }}
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark fw-bolder fs-6">{{ $item['mahasiswa'] }}</span>
                                        <span class="text-muted fs-7 font-monospace">{{ $item['nim'] }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-light-primary fw-bolder fs-8">{{ $item['jenis'] }}</span>
                            </td>
                            <td>
                                <span class="text-dark fw-bold fs-7 text-truncate d-inline-block" style="max-width: 280px;" title="{{ $item['judul'] }}">
                                    {{ $item['judul'] }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-column fs-8">
                                    <span class="text-dark fw-bold">{{ $item['tanggal'] }}</span>
                                    <span class="text-muted">{{ $item['waktu'] }} &bull; {{ $item['ruang'] }}</span>
                                </div>
                            </td>
                            <td class="text-end">
                                <span class="badge badge-light-{{ $item['badge'] }} fw-bolder fs-8 px-3 py-2">
                                    {{ $item['status'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @include('partials.pagination_footer', ['paginator' => $jadwal_sidang, 'label' => 'jadwal sidang'])
    </div>
</div>
@endsection

