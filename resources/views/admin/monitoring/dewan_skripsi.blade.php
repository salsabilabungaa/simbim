@extends('layouts.admin')

@section('title', 'Pemantauan Data Dewan Skripsi (Global Access)')

@section('content')
<!-- Header Banner -->
<div class="card card-flush pb-0 mb-6 shadow-sm">
    <div class="card-body pt-6 pb-6">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge badge-light-danger fw-bolder fs-8">HAK AKSES GLOBAL ADMIN</span>
                    <span class="text-muted fs-8">&bull; Role Dewan Skripsi</span>
                </div>
                <h2 class="text-dark fw-bolder mb-1">Data & Pemantauan Dewan Skripsi</h2>
                <span class="text-muted fs-7">
                    Memantau jadwal seminar proposal (Sempro), sidang meja hijau, serta penetapan susunan dewan penguji tugas akhir.
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
                <div class="text-gray-500 fw-bold fs-7 mb-1 text-uppercase">Total Pengajuan Proposal</div>
                <div class="text-dark fw-bolder fs-2x mb-1">{{ $rekapProposal['total_pengajuan'] }}</div>
                <span class="text-muted fs-8">Tahun Akademik 2026/2027</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm bg-body">
            <div class="card-body py-5">
                <div class="text-gray-500 fw-bold fs-7 mb-1 text-uppercase">Proposal Disetujui</div>
                <div class="text-dark fw-bolder fs-2x mb-1 text-success">{{ $rekapProposal['disetujui'] }}</div>
                <span class="text-muted fs-8">Judul dan rumusan disetujui</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm bg-body">
            <div class="card-body py-5">
                <div class="text-gray-500 fw-bold fs-7 mb-1 text-uppercase">Perlu Revisi Judul</div>
                <div class="text-dark fw-bolder fs-2x mb-1 text-danger">{{ $rekapProposal['revisi'] }}</div>
                <span class="text-muted fs-8">Dikembalikan ke mahasiswa</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm bg-body">
            <div class="card-body py-5">
                <div class="text-gray-500 fw-bold fs-7 mb-1 text-uppercase">Sidang Terjadwal Bulan Ini</div>
                <div class="text-dark fw-bolder fs-2x mb-1 text-warning">{{ $rekapProposal['sidang_bulan_ini'] }} Sesi</div>
                <span class="text-muted fs-8">Sempro & Komprehensif</span>
            </div>
        </div>
    </div>
</div>

<!-- Agenda Table -->
<div class="card card-flush shadow-sm mb-6">
    <div class="card-header pt-6">
        <h3 class="card-title fw-bolder text-gray-800">Jadwal Sidang & Penetapan Tim Penguji</h3>
    </div>
    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table table-row-bordered table-row-gray-200 align-middle gs-0 gy-4">
                <thead>
                    <tr class="fw-bolder text-muted fs-7 text-uppercase border-bottom">
                        <th class="min-w-180px">Mahasiswa & NIM</th>
                        <th class="min-w-160px">Jenis Sidang</th>
                        <th class="min-w-180px">Waktu & Tempat</th>
                        <th class="min-w-220px">Susunan Tim Penguji</th>
                        <th class="min-w-130px text-end">Status Berkas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($agendaSidang as $item)
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
                                <span class="badge badge-light-primary fw-bolder fs-8">
                                    {{ $item['jenis'] }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-dark fw-bold fs-7">{{ $item['tanggal'] }}</span>
                                    <span class="text-muted fs-8">{{ $item['waktu'] }} &bull; {{ $item['ruang'] }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column fs-8">
                                    <span class="text-dark fw-bold">Ketua: {{ $item['ketua_penguji'] }}</span>
                                    <span class="text-muted">Penguji 1: {{ $item['penguji_1'] }}</span>
                                    <span class="text-muted">Penguji 2: {{ $item['penguji_2'] }}</span>
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
        @include('partials.pagination_footer', ['paginator' => $agendaSidang, 'label' => 'agenda sidang'])
    </div>
</div>
@endsection

