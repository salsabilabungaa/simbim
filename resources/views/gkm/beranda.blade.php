@extends('layouts.gkm')

@section('title', 'Dasbor Rekap Perwalian GKM')

@section('content')
<!-- Header Card -->
<div class="card card-flush pb-0 bgi-position-y-center bgi-no-repeat mb-7 shadow-sm border-0" style="background: linear-gradient(135deg, #1e1e2d 0%, #151521 55%, #1c2438 100%);">
    <div class="card-body pt-8 pb-8">
        <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-5">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge badge-light-info fw-bolder px-3 py-1 fs-8">GUGUS KENDALI MUTU (GKM)</span>
                    <span class="badge badge-light-primary fw-bolder px-3 py-1 fs-8">
                        <i class="fas fa-university me-1 text-primary"></i> {{ $gkm['program_studi'] }} &mdash; {{ $gkm['universitas'] }}
                    </span>
                </div>
                <h1 class="text-white fw-bolder fs-2x mb-2">Dasbor Rekap Perwalian Akademik (KRS/IRS)</h1>
                <div class="text-gray-300 fs-7 mb-2 mw-700px">
                    Pemantauan dan pengarsipan bimbingan akademik/perwalian mahasiswa bersama dosen wali pada Program Studi Pendidikan Sistem dan Teknologi Informasi (PSTI) UPI sebagai bukti fisik akreditasi.
                </div>
                <div class="text-gray-400 fs-8">
                    Petugas Audit: <strong class="text-white">{{ $gkm['nama'] }}</strong> &bull; NIP: {{ $gkm['nip'] }} &bull; {{ $gkm['jabatan'] }}
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <div class="btn-group" role="group">
                    <a href="{{ route('gkm.ekspor.pdf', array_filter(['tahun_akademik' => $selectedTahunAkademik])) }}"
                       class="btn btn-sm btn-danger fw-bolder px-4 py-3"
                       title="Unduh Laporan Akreditasi Prodi PSTI UPI (PDF)">
                        <i class="fas fa-file-pdf me-1"></i> Ekspor PDF Akreditasi
                    </a>
                    <a href="{{ route('gkm.ekspor.excel', array_filter(['tahun_akademik' => $selectedTahunAkademik])) }}"
                       class="btn btn-sm btn-success fw-bolder px-4 py-3"
                       title="Unduh Laporan Akreditasi Prodi PSTI UPI (Excel .xlsx)">
                        <i class="fas fa-file-excel me-1"></i> Ekspor Excel
                    </a>
                    <button type="button"
                            class="btn btn-sm btn-info fw-bolder px-3 py-3"
                            data-bs-toggle="modal"
                            data-bs-target="#modal_ekspor_akreditasi"
                            title="Pilih filter angkatan & periode ekspor">
                        <i class="fas fa-sliders-h"></i>
                    </button>
                </div>

                @if (auth()->check() && auth()->user()->isAdmin())
                    <a href="{{ route('admin.monitoring.gkm') }}" class="btn btn-sm btn-primary fw-bolder px-4 py-3">
                        <i class="fas fa-arrow-left me-1"></i> Panel Monitoring Admin
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- 4 KPI Cards -->
<div class="row g-5 g-xl-8 mb-7">
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm bg-body h-100 border-start border-4 border-primary">
            <div class="card-body py-5">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-gray-500 fw-bold fs-7 text-uppercase">Total Mahasiswa PSTI</span>
                    <span class="badge badge-light-primary p-2"><i class="fas fa-users text-primary fs-4"></i></span>
                </div>
                <div class="text-dark fw-bolder fs-2qx mb-1">{{ $summary['total_mahasiswa'] }}</div>
                <span class="text-muted fs-8">{{ count($summary['rekap_per_angkatan']) }} Angkatan &bull; {{ $selectedTahunAkademik ?? 'Semua Periode' }}</span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm bg-body h-100 border-start border-4 border-success">
            <div class="card-body py-5">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-gray-500 fw-bold fs-7 text-uppercase">Sudah Selesai KRS/IRS</span>
                    <span class="badge badge-light-success p-2"><i class="fas fa-check-circle text-success fs-4"></i></span>
                </div>
                <div class="text-success fw-bolder fs-2qx mb-1">{{ $summary['sudah_selesai'] }} <span class="fs-6 text-gray-600">Mhs</span></div>
                <span class="badge badge-light-success fw-bolder fs-8">Kepatuhan {{ $summary['kepatuhan_persen'] }}%</span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm bg-body h-100 border-start border-4 border-warning">
            <div class="card-body py-5">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-gray-500 fw-bold fs-7 text-uppercase">Belum / Proses KRS/IRS</span>
                    <span class="badge badge-light-warning p-2"><i class="fas fa-hourglass-half text-warning fs-4"></i></span>
                </div>
                <div class="text-warning fw-bolder fs-2qx mb-1">{{ $summary['belum_atau_proses'] }} <span class="fs-6 text-gray-600">Mhs</span></div>
                <span class="text-muted fs-8">{{ $summary['dalam_proses'] }} Menunggu ACC, {{ $summary['belum_selesai'] }} Belum Mulai</span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm bg-body h-100 border-start border-4 border-info">
            <div class="card-body py-5">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-gray-500 fw-bold fs-7 text-uppercase">Arsip Log Konsultasi</span>
                    <span class="badge badge-light-info p-2"><i class="fas fa-lock text-info fs-4"></i></span>
                </div>
                <div class="text-info fw-bolder fs-2qx mb-1">{{ $summary['total_konsultasi'] }} <span class="fs-6 text-gray-600">Sesi</span></div>
                <span class="text-muted fs-8">Tercatat View-Only untuk Akreditasi</span>
            </div>
        </div>
    </div>
</div>

<!-- Grafik Rekap Perwalian (ApexCharts) -->
<div class="row g-5 g-xl-8 mb-7">
    <!-- Kolom Kiri: Grafik Perbandingan Per Angkatan Prodi PSTI UPI -->
    <div class="col-xl-8">
        <div class="card card-flush shadow-sm h-100">
            <div class="card-header pt-6 pb-2 border-0">
                <div class="card-title flex-column align-items-start">
                    <h3 class="fw-bolder text-dark fs-4 mb-1">Grafik Status Bimbingan Akademik (KRS/IRS) per Angkatan &mdash; PSTI UPI</h3>
                    <span class="text-muted fs-7">Perbandingan mahasiswa yang sudah menyelesaikan, sedang proses, dan belum melaksanakan perwalian pada setiap angkatan</span>
                </div>
                <div class="card-toolbar">
                    <span class="badge badge-light-primary fw-bolder fs-8 px-3 py-2">{{ $selectedTahunAkademik ?? 'Semua Periode' }}</span>
                </div>
            </div>
            <div class="card-body pt-2">
                <div id="chart_perwalian_per_angkatan" style="height: 290px;"></div>

                <!-- Ringkasan Numerik per Angkatan -->
                <div class="row g-3 mt-2 pt-3 border-top border-gray-200">
                    @foreach ($summary['rekap_per_angkatan'] as $angkatanStat)
                        <div class="col-md-6 col-xl-3">
                            <div class="p-3 rounded bg-light border border-gray-200 h-100">
                                <div class="fw-bolder text-dark fs-8 text-truncate mb-1">
                                    {{ $angkatanStat['label'] }}
                                </div>
                                <div class="d-flex align-items-baseline justify-content-between mb-1">
                                    <span class="fw-bolder text-primary fs-5">{{ $angkatanStat['kepatuhan_persen'] }}%</span>
                                    <span class="text-muted fs-9">{{ $angkatanStat['selesai'] }}/{{ $angkatanStat['total'] }} Selesai</span>
                                </div>
                                <div class="progress h-5px bg-gray-300 rounded">
                                    <div class="progress-bar bg-success rounded" style="width: {{ $angkatanStat['kepatuhan_persen'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Grafik Proporsi Keseluruhan -->
    <div class="col-xl-4">
        <div class="card card-flush shadow-sm h-100">
            <div class="card-header pt-6 pb-2 border-0">
                <div class="card-title flex-column align-items-start">
                    <h3 class="fw-bolder text-dark fs-4 mb-1">Proporsi Kepatuhan KRS/IRS</h3>
                    <span class="text-muted fs-7">Persentase penyelesaian bimbingan akademik PSTI UPI</span>
                </div>
            </div>
            <div class="card-body pt-2 d-flex flex-column justify-content-between">
                <div id="chart_proporsi_perwalian" style="height: 240px;"></div>

                <div class="d-flex flex-column gap-2 pt-3 border-top border-gray-200">
                    <div class="d-flex justify-content-between align-items-center fs-7">
                        <span class="text-gray-700 fw-bold"><span class="badge badge-dot bg-success me-2"></span>Sudah Selesai (ACC KRS/IRS)</span>
                        <span class="fw-bolder text-success">{{ $summary['sudah_selesai'] }} Mhs</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center fs-7">
                        <span class="text-gray-700 fw-bold"><span class="badge badge-dot bg-warning me-2"></span>Dalam Proses / Revisi KRS</span>
                        <span class="fw-bolder text-warning">{{ $summary['dalam_proses'] }} Mhs</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center fs-7">
                        <span class="text-gray-700 fw-bold"><span class="badge badge-dot bg-danger me-2"></span>Belum Bimbingan Akademik</span>
                        <span class="fw-bolder text-danger">{{ $summary['belum_selesai'] }} Mhs</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter & Tabel Daftar Mahasiswa Rekap Perwalian -->
<div class="card card-flush shadow-sm">
    <div class="card-header pt-6 border-0">
        <div class="card-title flex-column align-items-start">
            <h3 class="fw-bolder text-gray-900 fs-4 mb-1">Daftar Status Bimbingan Akademik (Pengisian KRS/IRS) Mahasiswa PSTI UPI</h3>
            <span class="text-muted fs-7">Menampilkan mahasiswa yang sudah maupun belum menyelesaikan bimbingan perwalian bersama dosen wali</span>
        </div>
        <div class="card-toolbar">
            <a href="{{ route('gkm.ekspor.excel', array_filter(['angkatan' => $selectedAngkatan, 'tahun_akademik' => $selectedTahunAkademik])) }}" class="btn btn-sm btn-light-success fw-bolder me-2">
                <i class="fas fa-file-excel me-1"></i> Unduh Excel Sesuai Filter
            </a>
            <a href="{{ route('gkm.ekspor.pdf', array_filter(['angkatan' => $selectedAngkatan, 'tahun_akademik' => $selectedTahunAkademik])) }}" class="btn btn-sm btn-light-danger fw-bolder">
                <i class="fas fa-file-pdf me-1"></i> Unduh PDF Sesuai Filter
            </a>
        </div>
    </div>

    <div class="card-body pt-3">
        <!-- Quick Status Pills + Form Filter -->
        <form method="GET" action="{{ route('gkm.beranda') }}" class="mb-6">
            <div class="d-flex flex-wrap gap-2 mb-4">
                @php
                    $basePillParams = array_filter([
                        'angkatan' => $selectedAngkatan,
                        'tahun_akademik' => $selectedTahunAkademik ?? '',
                        'search' => $search,
                    ], fn ($val) => $val !== null);
                @endphp
                <a href="{{ route('gkm.beranda', $basePillParams) }}"
                   class="btn btn-sm {{ empty($selectedStatus) ? 'btn-dark' : 'btn-light' }} fw-bolder">
                    Semua ({{ $summary['total_mahasiswa'] }})
                </a>
                <a href="{{ route('gkm.beranda', array_merge($basePillParams, ['status_perwalian' => 'selesai'])) }}"
                   class="btn btn-sm {{ $selectedStatus === 'selesai' ? 'btn-success' : 'btn-light-success' }} fw-bolder">
                    Sudah Selesai KRS/IRS ({{ $summary['sudah_selesai'] }})
                </a>
                <a href="{{ route('gkm.beranda', array_merge($basePillParams, ['status_perwalian' => 'proses'])) }}"
                   class="btn btn-sm {{ $selectedStatus === 'proses' ? 'btn-warning' : 'btn-light-warning' }} fw-bolder">
                    Proses / Menunggu ACC ({{ $summary['dalam_proses'] }})
                </a>
                <a href="{{ route('gkm.beranda', array_merge($basePillParams, ['status_perwalian' => 'belum'])) }}"
                   class="btn btn-sm {{ $selectedStatus === 'belum' ? 'btn-danger' : 'btn-light-danger' }} fw-bolder">
                    Belum Selesai ({{ $summary['belum_selesai'] }})
                </a>
            </div>

            <div class="row g-3 align-items-center bg-light p-4 rounded border border-gray-200">
                @if ($selectedStatus)
                    <input type="hidden" name="status_perwalian" value="{{ $selectedStatus }}">
                @endif

                <div class="col-md-4">
                    <div class="input-group input-group-solid bg-white border border-gray-300 rounded">
                        <span class="input-group-text bg-transparent border-0"><i class="fas fa-search text-gray-500"></i></span>
                        <input type="text" name="search" value="{{ $search }}" class="form-control bg-transparent border-0 fs-7" placeholder="Cari nama mahasiswa, NIM, atau dosen wali..." />
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="angkatan" class="form-select form-select-solid bg-white border border-gray-300 fs-7" data-control="select2" data-placeholder="Semua Angkatan">
                        <option value="">Semua Angkatan</option>
                        @foreach ($daftarAngkatan as $angkatanOption)
                            <option value="{{ $angkatanOption }}" {{ $selectedAngkatan === $angkatanOption ? 'selected' : '' }}>
                                Angkatan {{ $angkatanOption }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="tahun_akademik" class="form-select form-select-solid bg-white border border-gray-300 fs-7" data-control="select2" data-placeholder="Semua Periode Akademik">
                        <option value="" {{ $selectedTahunAkademik === null ? 'selected' : '' }}>Semua Periode Akademik</option>
                        @foreach ($daftarTahunAkademik as $tahunOption)
                            <option value="{{ $tahunOption }}" {{ $selectedTahunAkademik === $tahunOption ? 'selected' : '' }}>
                                {{ $tahunOption }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary fw-bolder flex-grow-1">Terapkan</button>
                    @if ($selectedAngkatan || $selectedStatus || $search)
                        <a href="{{ route('gkm.beranda') }}" class="btn btn-sm btn-light border" title="Reset Filter">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Tabel Mahasiswa -->
        <div class="table-responsive">
            <table class="table table-row-bordered table-row-gray-200 align-middle gs-0 gy-4">
                <thead>
                    <tr class="fw-bolder text-muted fs-7 text-uppercase border-bottom bg-light">
                        <th class="ps-4 w-50px rounded-start">No</th>
                        <th class="min-w-200px">Mahasiswa &amp; NIM</th>
                        <th class="min-w-150px">Program Studi</th>
                        <th class="min-w-180px">Dosen Wali</th>
                        <th class="min-w-210px">Status Bimbingan (KRS/IRS)</th>
                        <th class="min-w-100px text-center">Log Sesi</th>
                        <th class="min-w-140px text-end pe-4 rounded-end">Aksi (View Only)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarPerwalian as $index => $item)
                        <tr>
                            <td class="ps-4 text-gray-600 fw-bold fs-7">
                                {{ $daftarPerwalian->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40px symbol-circle me-3 bg-light-{{ $item->status_badge }} text-{{ $item->status_badge }} fw-bolder d-flex align-items-center justify-content-center">
                                        {{ strtoupper(substr($item->mahasiswa?->name ?? 'M', 0, 1)) }}
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark fw-bolder fs-6">{{ $item->mahasiswa?->name ?? '-' }}</span>
                                        <span class="text-muted fs-8">
                                            NIM: <span class="font-monospace fw-bold text-gray-700">{{ $item->mahasiswa?->nomor_induk ?? '-' }}</span>
                                            &bull; Angk. {{ $item->angkatan }} (Sem {{ $item->semester_berjalan }})
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-light-dark fw-bold fs-8">{{ config('simbim.prodi_singkat') }} &mdash; {{ config('simbim.universitas_singkat') }}</span>
                                <div class="text-muted fs-9 mt-1">{{ $item->tahun_akademik }}</div>
                            </td>
                            <td>
                                <div class="text-gray-800 fw-bold fs-7">{{ $item->dosenWali?->name ?? '-' }}</div>
                                <div class="text-muted fs-8 font-monospace">{{ $item->dosenWali?->nomor_induk ?? '-' }}</div>
                            </td>
                            <td>
                                <span class="badge badge-light-{{ $item->status_badge }} fw-bolder fs-8 px-3 py-1 mb-1">
                                    {{ $item->status_label }}
                                </span>
                                <div class="text-gray-700 fw-semibold fs-8">{{ $item->status_krs }}</div>
                                <div class="text-muted fs-9">
                                    IPK {{ number_format((float) $item->ipk, 2) }} &bull; {{ $item->sks_diambil }} SKS
                                    @if ($item->tanggal_persetujuan_krs)
                                        &bull; ACC: {{ $item->tanggal_persetujuan_krs->format('d M Y') }}
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-light-info fw-bolder fs-7 px-3 py-2">
                                    {{ $item->konsultasis_count }} Sesi
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('gkm.riwayat-konsultasi', ['search' => $item->mahasiswa?->nomor_induk, 'tahun_akademik' => $item->tahun_akademik]) }}"
                                   class="btn btn-sm btn-light-primary fw-bolder fs-8">
                                    <i class="fas fa-eye me-1"></i> Log Konsultasi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-muted">
                                <i class="fas fa-folder-open fs-2x mb-3 text-gray-400 d-block"></i>
                                Tidak ada data perwalian mahasiswa yang sesuai dengan filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap pt-4">
            <span class="text-muted fs-7">
                Menampilkan {{ $daftarPerwalian->firstItem() ?? 0 }} sampai {{ $daftarPerwalian->lastItem() ?? 0 }} dari {{ $daftarPerwalian->total() }} mahasiswa perwalian
            </span>
            <div>
                {{ $daftarPerwalian->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@include('gkm.partials.modal_ekspor', [
    'daftarAngkatan' => $daftarAngkatan,
    'daftarTahunAkademik' => $daftarTahunAkademik,
    'defaultAngkatan' => $selectedAngkatan ?? '',
    'defaultTahun' => $selectedTahunAkademik ?? '',
])
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof ApexCharts === 'undefined') {
            return;
        }

        const chartData = @json($summary['chart_data']);

        const barElement = document.getElementById('chart_perwalian_per_angkatan');
        if (barElement) {
            const barChart = new ApexCharts(barElement, {
                series: [
                    { name: 'Sudah Selesai KRS/IRS', data: chartData.selesai },
                    { name: 'Dalam Proses / Revisi', data: chartData.proses },
                    { name: 'Belum Bimbingan', data: chartData.belum }
                ],
                chart: {
                    type: 'bar',
                    height: 280,
                    stacked: true,
                    toolbar: { show: false },
                    fontFamily: 'inherit'
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '42%',
                        borderRadius: 5
                    }
                },
                colors: ['#50CD89', '#FFC700', '#F1416C'],
                dataLabels: { enabled: true },
                xaxis: {
                    categories: chartData.categories,
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    title: { text: 'Jumlah Mahasiswa' },
                    labels: {
                        formatter: function (val) {
                            return Math.round(val);
                        }
                    }
                },
                legend: { position: 'top', horizontalAlign: 'left' },
                grid: { borderColor: '#EFF2F5', strokeDashArray: 4 }
            });
            barChart.render();
        }

        const donutElement = document.getElementById('chart_proporsi_perwalian');
        if (donutElement) {
            const donutChart = new ApexCharts(donutElement, {
                series: chartData.donut,
                labels: ['Sudah Selesai KRS/IRS', 'Dalam Proses', 'Belum Bimbingan'],
                chart: {
                    type: 'donut',
                    height: 235,
                    fontFamily: 'inherit'
                },
                colors: ['#50CD89', '#FFC700', '#F1416C'],
                legend: { show: false },
                dataLabels: { enabled: true },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total Mhs',
                                    fontWeight: 700
                                }
                            }
                        }
                    }
                }
            });
            donutChart.render();
        }
    });
</script>
@endpush
