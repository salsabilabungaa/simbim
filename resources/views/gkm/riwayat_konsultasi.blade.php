@extends('layouts.gkm')

@section('title', 'Riwayat & Log Konsultasi Akademik (View Only)')

@section('content')
<!-- Header Banner -->
<div class="card card-flush pb-0 mb-7 shadow-sm border-0" style="background: linear-gradient(135deg, #1e1e2d 0%, #151521 55%, #1c2438 100%);">
    <div class="card-body pt-8 pb-8">
        <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-5">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge badge-light-warning fw-bolder px-3 py-1 fs-8">
                        <i class="fas fa-lock me-1 text-warning"></i> MODE VIEW ONLY — ARSIP TERKUNCI
                    </span>
                    <span class="badge badge-light-info fw-bolder px-3 py-1 fs-8">
                        <i class="fas fa-university me-1 text-info"></i> {{ config('simbim.prodi') }} &mdash; {{ config('simbim.universitas') }}
                    </span>
                </div>
                <h1 class="text-white fw-bolder fs-2x mb-2">Riwayat &amp; Log Konsultasi Perwalian</h1>
                <p class="text-gray-300 fs-7 mb-0 mw-700px">
                    Jejak rekam kronologis komunikasi bimbingan akademik (tanggal, topik, catatan mahasiswa &amp; arahan dosen wali) Prodi PSTI UPI untuk kebutuhan audit akreditasi.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Integrity Notice Bar -->
<div class="notice d-flex bg-light-warning rounded border-warning border border-dashed mb-7 p-5">
    <span class="svg-icon svg-icon-2tx svg-icon-warning me-4 mt-1">
        <i class="fas fa-shield-alt fs-1 text-warning"></i>
    </span>
    <div class="d-flex flex-stack flex-grow-1">
        <div class="fw-semibold">
            <h5 class="text-gray-900 fw-bolder mb-1">Akses Baca Saja (View-Only Audit Trail)</h5>
            <div class="fs-7 text-gray-700">
                Seluruh catatan komunikasi antara mahasiswa dan dosen wali pada halaman ini <strong>tidak dapat diubah, ditambah, ataupun dihapus</strong> oleh GKM untuk menjaga keaslian dan integritas bukti fisik akreditasi Prodi PSTI UPI.
            </div>
        </div>
    </div>
</div>

<!-- 4 KPI Ringkasan Log Konsultasi -->
<div class="row g-5 g-xl-8 mb-7">
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm bg-body h-100 border-start border-4 border-primary">
            <div class="card-body py-5">
                <div class="text-gray-500 fw-bold fs-7 text-uppercase mb-1">Total Log Tercatat</div>
                <div class="text-dark fw-bolder fs-2qx">{{ $statistik['total'] }} <span class="fs-6 text-muted">Sesi</span></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm bg-body h-100 border-start border-4 border-success">
            <div class="card-body py-5">
                <div class="text-gray-500 fw-bold fs-7 text-uppercase mb-1">Pengesahan ACC KRS/IRS</div>
                <div class="text-success fw-bolder fs-2qx">{{ $statistik['acc_krs'] }} <span class="fs-6 text-muted">Sesi</span></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm bg-body h-100 border-start border-4 border-info">
            <div class="card-body py-5">
                <div class="text-gray-500 fw-bold fs-7 text-uppercase mb-1">Konsultasi Terlaksana</div>
                <div class="text-info fw-bolder fs-2qx">{{ $statistik['selesai'] }} <span class="fs-6 text-muted">Sesi</span></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-flush shadow-sm bg-body h-100 border-start border-4 border-warning">
            <div class="card-body py-5">
                <div class="text-gray-500 fw-bold fs-7 text-uppercase mb-1">Perlu Tindak Lanjut</div>
                <div class="text-warning fw-bolder fs-2qx">{{ $statistik['tindak_lanjut'] }} <span class="fs-6 text-muted">Sesi</span></div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Panel -->
<div class="card card-flush shadow-sm mb-7">
    <div class="card-body p-6">
        <form method="GET" action="{{ route('gkm.riwayat-konsultasi') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bolder fs-8 text-uppercase text-muted">Cari Kata Kunci</label>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="form-control form-control-solid fs-7" placeholder="Nama mahasiswa, NIM, topik, atau catatan..." />
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bolder fs-8 text-uppercase text-muted">Angkatan</label>
                <select name="angkatan" class="form-select form-select-solid fs-7" data-control="select2" data-placeholder="Semua Angkatan">
                    <option value="">Semua Angkatan</option>
                    @foreach ($daftarAngkatan as $angkatanOption)
                        <option value="{{ $angkatanOption }}" {{ ($filters['angkatan'] ?? '') === $angkatanOption ? 'selected' : '' }}>
                            Angkatan {{ $angkatanOption }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bolder fs-8 text-uppercase text-muted">Periode Akademik</label>
                <select name="tahun_akademik" class="form-select form-select-solid fs-7" data-control="select2" data-placeholder="Semua Periode">
                    <option value="">Semua Periode</option>
                    @foreach ($daftarTahunAkademik as $tahunOption)
                        <option value="{{ $tahunOption }}" {{ ($filters['tahun_akademik'] ?? '') === $tahunOption ? 'selected' : '' }}>
                            {{ $tahunOption }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bolder fs-8 text-uppercase text-muted">Dosen Wali</label>
                <select name="dosen_wali_id" class="form-select form-select-solid fs-7" data-control="select2" data-placeholder="Semua Dosen">
                    <option value="">Semua Dosen</option>
                    @foreach ($daftarDosenWali as $dosen)
                        <option value="{{ $dosen->id }}" {{ (string) ($filters['dosen_wali_id'] ?? '') === (string) $dosen->id ? 'selected' : '' }}>
                            {{ $dosen->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bolder fs-8 text-uppercase text-muted">Status Sesi</label>
                <select name="status_sesi" class="form-select form-select-solid fs-7" data-control="select2" data-placeholder="Semua Status">
                    <option value="">Semua Status</option>
                    <option value="ACC KRS/IRS" {{ ($filters['status_sesi'] ?? '') === 'ACC KRS/IRS' ? 'selected' : '' }}>ACC KRS/IRS</option>
                    <option value="Selesai" {{ ($filters['status_sesi'] ?? '') === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="Perlu Tindak Lanjut" {{ ($filters['status_sesi'] ?? '') === 'Perlu Tindak Lanjut' ? 'selected' : '' }}>Perlu Tindak Lanjut</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bolder fs-8 text-uppercase text-muted">Dari Tanggal</label>
                <input type="date" name="tanggal_dari" value="{{ $filters['tanggal_dari'] ?? '' }}" class="form-control form-control-solid fs-7" />
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bolder fs-8 text-uppercase text-muted">Sampai Tanggal</label>
                <input type="date" name="tanggal_sampai" value="{{ $filters['tanggal_sampai'] ?? '' }}" class="form-control form-control-solid fs-7" />
            </div>
            <div class="col-md-6 d-flex justify-content-md-end gap-2">
                @if (collect($filters)->filter()->isNotEmpty())
                    <a href="{{ route('gkm.riwayat-konsultasi') }}" class="btn btn-sm btn-light border fw-bold">
                        <i class="fas fa-undo me-1"></i> Reset Filter
                    </a>
                @endif
                <button type="submit" class="btn btn-sm btn-primary fw-bolder px-5">
                    <i class="fas fa-filter me-1"></i> Tampilkan Log
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Riwayat & Log Konsultasi (View Only) -->
<div class="card card-flush shadow-sm">
    <div class="card-header pt-6 border-0">
        <div class="card-title flex-column align-items-start">
            <h3 class="fw-bolder text-gray-900 fs-4 mb-1">Arsip Jejak Rekam Konsultasi Mahasiswa &amp; Dosen Wali</h3>
            <span class="text-muted fs-7">Menampilkan tanggal, topik, serta ringkasan catatan komunikasi bimbingan akademik PSTI UPI</span>
        </div>
        <div class="card-toolbar">
            <span class="badge badge-light-warning fw-bolder fs-8 px-3 py-2">
                <i class="fas fa-lock me-1 text-warning"></i> Read-Only Record
            </span>
        </div>
    </div>

    <div class="card-body pt-2">
        <div class="table-responsive">
            <table class="table table-row-bordered table-row-gray-200 align-top gs-0 gy-4">
                <thead>
                    <tr class="fw-bolder text-muted fs-7 text-uppercase border-bottom bg-light">
                        <th class="ps-4 w-45px rounded-start">No</th>
                        <th class="min-w-145px">Tanggal &amp; Metode</th>
                        <th class="min-w-170px">Mahasiswa &amp; Angkatan</th>
                        <th class="min-w-160px">Dosen Wali</th>
                        <th class="min-w-300px">Topik &amp; Catatan Komunikasi</th>
                        <th class="min-w-115px text-center">Status Sesi</th>
                        <th class="min-w-120px text-end pe-4 rounded-end">Detail (View Only)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarKonsultasi as $index => $log)
                        <tr>
                            <td class="ps-4 text-gray-600 fw-bold fs-7">
                                {{ $daftarKonsultasi->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="fw-bolder text-dark fs-7">
                                    {{ $log->tanggal_konsultasi?->format('d M Y') ?? '-' }}
                                </div>
                                <div class="text-muted fs-8">
                                    <i class="far fa-clock me-1 text-primary"></i>{{ $log->waktu_konsultasi ?? '-' }}
                                </div>
                                <span class="badge badge-light-dark fs-9 mt-1">{{ $log->metode }}</span>
                            </td>
                            <td>
                                <div class="fw-bolder text-dark fs-6">{{ $log->mahasiswa?->name ?? '-' }}</div>
                                <div class="text-muted fs-8 font-monospace">{{ $log->mahasiswa?->nomor_induk ?? '-' }}</div>
                                <span class="badge badge-light-primary fs-9 mt-1">
                                    {{ config('simbim.prodi_singkat') }} {{ config('simbim.universitas_singkat') }}
                                    @if ($log->perwalianMahasiswa)
                                        &bull; Angk. {{ $log->perwalianMahasiswa->angkatan }}
                                    @endif
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-gray-800 fs-7">{{ $log->dosenWali?->name ?? '-' }}</div>
                                <div class="text-muted fs-8 font-monospace">{{ $log->dosenWali?->nomor_induk ?? '-' }}</div>
                                <div class="text-muted fs-9 mt-1">{{ $log->tahun_akademik }}</div>
                            </td>
                            <td>
                                <div class="fw-bolder text-primary fs-7 mb-2">{{ $log->topik }}</div>
                                @if ($log->catatan_mahasiswa)
                                    <div class="text-gray-700 fs-8 mb-1">
                                        <strong>Mahasiswa:</strong> {{ Str::limit($log->catatan_mahasiswa, 110) }}
                                    </div>
                                @endif
                                <div class="p-2 rounded bg-light border border-gray-200 text-gray-800 fs-8">
                                    <strong>Dosen Wali:</strong> {{ Str::limit($log->catatan_dosen_wali, 130) }}
                                </div>
                                @if ($log->lampiran_krs)
                                    <div class="text-muted fs-9 mt-1">
                                        <i class="fas fa-paperclip me-1"></i>{{ $log->lampiran_krs }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge badge-light-{{ $log->status_badge }} fw-bolder fs-8 px-3 py-2">
                                    {{ $log->status_sesi }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('gkm.riwayat-konsultasi.show', $log) }}"
                                   class="btn btn-sm btn-icon btn-light-primary"
                                   title="Buka Halaman Detail Arsip">
                                    <i class="fas fa-external-link-alt fs-7"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-muted">
                                <i class="fas fa-clipboard-check fs-2x mb-3 text-gray-400 d-block"></i>
                                Belum ada log konsultasi bimbingan akademik yang cocok dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap pt-4">
            <span class="text-muted fs-7">
                Menampilkan {{ $daftarKonsultasi->firstItem() ?? 0 }} sampai {{ $daftarKonsultasi->lastItem() ?? 0 }} dari {{ $daftarKonsultasi->total() }} rekaman konsultasi
            </span>
            <div>
                {{ $daftarKonsultasi->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
