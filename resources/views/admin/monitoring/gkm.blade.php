@extends('layouts.admin')

@section('title', 'Pemantauan Data GKM (Global Access)')

@section('content')
<!-- Header Banner -->
<div class="card card-flush pb-0 mb-6 shadow-sm">
    <div class="card-body pt-6 pb-6">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge badge-light-danger fw-bolder fs-8">HAK AKSES GLOBAL ADMIN</span>
                    <span class="text-muted fs-8">&bull; Role GKM (Gugus Kendali Mutu)</span>
                </div>
                <h2 class="text-dark fw-bolder mb-1">Data & Audit Kendali Mutu (GKM)</h2>
                <span class="text-muted fs-7">
                    Memantau rekap perwalian akademik (KRS/IRS), jejak log konsultasi view-only, dan kesiapan dokumen akreditasi Prodi PSTI &mdash; {{ config('simbim.universitas') }}.
                </span>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('gkm.riwayat-konsultasi') }}" target="_blank" class="btn btn-light-primary fw-bolder">
                    <i class="fas fa-clipboard-list me-1"></i> Log Konsultasi (View Only)
                </a>
            </div>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-5 g-xl-8 mb-6">
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm bg-body">
            <div class="card-body py-5">
                <div class="text-gray-500 fw-bold fs-7 mb-1 text-uppercase">Kepatuhan Perwalian KRS/IRS</div>
                <div class="text-dark fw-bolder fs-2x mb-1 text-success">{{ $rekapAudit['indeks_mutu_keseluruhan'] }}</div>
                <span class="text-muted fs-8">{{ $gkmSummary['total_mahasiswa'] ?? 0 }} Mahasiswa terdata</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm bg-body">
            <div class="card-body py-5">
                <div class="text-gray-500 fw-bold fs-7 mb-1 text-uppercase">Arsip Log Konsultasi</div>
                <div class="text-dark fw-bolder fs-2x mb-1">{{ $rekapAudit['total_sesi_teraudit'] }}</div>
                <span class="text-muted fs-8">Tercatat View-Only pada SIMBIM</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm bg-body">
            <div class="card-body py-5">
                <div class="text-gray-500 fw-bold fs-7 mb-1 text-uppercase">KRS/IRS Disetujui</div>
                <div class="text-dark fw-bolder fs-2x mb-1 text-primary">{{ $rekapAudit['sesi_sesuai_sop'] }} Mhs</div>
                <span class="text-muted fs-8">Telah menyelesaikan bimbingan wali</span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm bg-body">
            <div class="card-body py-5">
                <div class="text-gray-500 fw-bold fs-7 mb-1 text-uppercase">Belum / Proses Perwalian</div>
                <div class="text-dark fw-bolder fs-2x mb-1 text-warning">{{ $rekapAudit['deviasi_tercatat'] }} Mhs</div>
                <span class="text-muted fs-8">Menunggu ACC atau belum mengajukan</span>
            </div>
        </div>
    </div>
</div>

<!-- Indikator Mutu Table -->
<div class="card card-flush shadow-sm">
    <div class="card-header pt-6">
        <h3 class="card-title fw-bolder text-gray-800">Indikator Kinerja & Standar Mutu Bimbingan</h3>
    </div>
    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table table-row-bordered table-row-gray-200 align-middle gs-0 gy-4">
                <thead>
                    <tr class="fw-bolder text-muted fs-7 text-uppercase border-bottom">
                        <th class="min-w-250px">Indikator Parameter Mutu</th>
                        <th class="min-w-120px">Target Standar</th>
                        <th class="min-w-120px">Realisasi Capaian</th>
                        <th class="min-w-140px">Status Mutu</th>
                        <th class="min-w-260px">Catatan Evaluasi GKM</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($indikatorMutu as $item)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="bullet bullet-vertical h-35px bg-{{ $item['badge'] }} me-3"></span>
                                    <span class="text-dark fw-bolder fs-6">{{ $item['parameter'] }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-light-dark fw-bold fs-7">{{ $item['target'] }}</span>
                            </td>
                            <td>
                                <span class="text-dark fw-bolder fs-6">{{ $item['capaian'] }}</span>
                            </td>
                            <td>
                                <span class="badge badge-light-{{ $item['badge'] }} fw-bolder fs-8 px-3 py-2">
                                    {{ $item['status'] }}
                                </span>
                            </td>
                            <td>
                                <span class="text-gray-600 fs-7">{{ $item['evaluasi'] }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @include('partials.pagination_footer', ['paginator' => $indikatorMutu, 'label' => 'indikator mutu'])
    </div>
</div>
@endsection

