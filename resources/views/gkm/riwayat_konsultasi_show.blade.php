@extends('layouts.gkm')

@section('title', 'Detail Log Konsultasi (View Only)')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-6">
    <div>
        <a href="{{ route('gkm.riwayat-konsultasi') }}" class="text-muted text-hover-primary fs-7 mb-1 d-inline-block">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Riwayat &amp; Log Konsultasi
        </a>
        <h2 class="text-dark fw-bolder mb-0">Arsip Detail Sesi Konsultasi Perwalian</h2>
    </div>
    <span class="badge badge-light-warning fw-bolder fs-7 px-4 py-2 border border-warning border-dashed">
        <i class="fas fa-lock me-2 text-warning"></i> MODE VIEW ONLY — TIDAK DAPAT DIUBAH
    </span>
</div>

<div class="card card-flush shadow-sm mb-7">
    <div class="card-header pt-6 border-bottom">
        <div class="card-title flex-column align-items-start">
            <span class="text-muted fs-8 text-uppercase fw-bolder mb-1">Topik Konsultasi Akademik</span>
            <h3 class="fw-bolder text-primary fs-3 mb-0">{{ $konsultasi->topik }}</h3>
        </div>
        <div class="card-toolbar">
            <span class="badge badge-light-{{ $konsultasi->status_badge }} fw-bolder fs-7 px-4 py-2">
                {{ $konsultasi->status_sesi }}
            </span>
        </div>
    </div>

    <div class="card-body py-7">
        <div class="row g-6 mb-7">
            <div class="col-md-6">
                <div class="p-5 rounded bg-light border border-gray-200 h-100">
                    <div class="text-muted fs-8 text-uppercase fw-bolder mb-2">Identitas Mahasiswa</div>
                    <div class="fw-bolder text-dark fs-5 mb-1">{{ $konsultasi->mahasiswa?->name ?? '-' }}</div>
                    <div class="text-gray-700 fs-7">NIM: <span class="font-monospace fw-bold">{{ $konsultasi->mahasiswa?->nomor_induk ?? '-' }}</span></div>
                    <div class="text-gray-700 fs-7">Program Studi: <strong>{{ config('simbim.prodi') }}</strong></div>
                    <div class="text-gray-700 fs-7">Universitas: <strong>{{ config('simbim.universitas') }}</strong></div>
                    @if ($konsultasi->perwalianMahasiswa)
                        <div class="mt-3 pt-3 border-top border-gray-300 fs-8 text-muted">
                            Angkatan {{ $konsultasi->perwalianMahasiswa->angkatan }} &bull; Semester {{ $konsultasi->perwalianMahasiswa->semester_berjalan }} &bull;
                            IPK {{ number_format((float) $konsultasi->perwalianMahasiswa->ipk, 2) }} &bull; {{ $konsultasi->perwalianMahasiswa->sks_diambil }} SKS &bull;
                            Status KRS: <strong class="text-dark">{{ $konsultasi->perwalianMahasiswa->status_krs }}</strong>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="p-5 rounded bg-light border border-gray-200 h-100">
                    <div class="text-muted fs-8 text-uppercase fw-bolder mb-2">Dosen Pembimbing Akademik (Wali) &amp; Waktu Sesi</div>
                    <div class="fw-bolder text-dark fs-5 mb-1">{{ $konsultasi->dosenWali?->name ?? '-' }}</div>
                    <div class="text-gray-700 fs-7">NIP: <span class="font-monospace fw-bold">{{ $konsultasi->dosenWali?->nomor_induk ?? '-' }}</span></div>
                    <div class="mt-3 pt-3 border-top border-gray-300 fs-7 text-gray-800">
                        <div><i class="far fa-calendar-alt text-primary me-2"></i>{{ $konsultasi->tanggal_konsultasi?->format('d F Y') ?? '-' }} ({{ $konsultasi->waktu_konsultasi ?? '-' }})</div>
                        <div class="mt-1"><i class="fas fa-video text-info me-2"></i>{{ $konsultasi->metode }} &bull; {{ $konsultasi->tahun_akademik }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <h5 class="fw-bolder text-dark fs-6 mb-2"><i class="fas fa-comment-dots text-primary me-2"></i>Catatan / Pengajuan Komunikasi Mahasiswa</h5>
            <div class="p-5 rounded bg-light-primary border border-primary border-dashed text-gray-800 fs-6">
                {{ $konsultasi->catatan_mahasiswa ?? 'Mahasiswa tidak menuliskan catatan pengajuan tambahan.' }}
            </div>
        </div>

        <div class="mb-6">
            <h5 class="fw-bolder text-dark fs-6 mb-2"><i class="fas fa-chalkboard-teacher text-success me-2"></i>Arahan &amp; Catatan Evaluasi Dosen Wali</h5>
            <div class="p-5 rounded bg-light-success border border-success border-dashed text-gray-800 fs-6">
                {{ $konsultasi->catatan_dosen_wali }}
            </div>
        </div>

        <div class="d-flex flex-wrap align-items-center justify-content-between p-4 rounded bg-light border border-gray-200">
            <div class="fs-7 text-gray-700">
                <i class="fas fa-paperclip text-danger me-2"></i>
                <strong>Lampiran Dokumen KRS/IRS:</strong> {{ $konsultasi->lampiran_krs ?? 'Tidak ada lampiran berkas.' }}
            </div>
            <div class="text-muted fs-8">
                ID Log Arsip: <span class="font-monospace fw-bold">#KONS-{{ str_pad((string) $konsultasi->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection

