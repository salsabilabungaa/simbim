@extends('layouts.dewan-skripsi')

@section('title', 'Validasi Pendaftaran Sidang (Approval) — Dewan Skripsi')
@section('page_title', 'Validasi Pendaftaran Sidang (Approval)')

@section('content')
<div class="card card-flush shadow-sm mb-6">
    <div class="card-body py-6 px-8 bg-light-warning rounded border border-warning border-dashed">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
            <div>
                <h3 class="text-warning fw-bolder mb-1">
                    <i class="fas fa-clipboard-check text-warning me-2"></i> Validasi & Persetujuan (Approval) Pendaftaran Sidang
                </h3>
                <p class="text-gray-800 fs-7 mb-0">
                    Meninjau rekam jejak bimbingan aktif mahasiswa dan konfirmasi persetujuan (ACC) dari dosen pembimbing sebelum menyetujui (ACC) atau menolak pengajuan sidang tugas akhir.
                </p>
            </div>
            <div class="d-flex gap-2">
                @php
                    $pendingCount = \App\Models\Skripsi::where('status_pendaftaran_sidang', 'Menunggu Persetujuan')->count();
                @endphp
                <span class="badge badge-warning fs-7 py-2 px-4 fw-bolder text-dark">
                    <i class="fas fa-hourglass-half me-1"></i> {{ $pendingCount }} Pengajuan Menunggu Validasi
                </span>
            </div>
        </div>
    </div>
</div>

<div class="card card-flush shadow-sm">
    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
        <div class="card-title">
            <h4 class="fw-bolder fs-5 text-gray-900 mb-0">
                <i class="fas fa-user-check text-warning me-2"></i> Daftar Pengajuan Sidang Skripsi
            </h4>
        </div>

        <div class="card-toolbar flex-wrap gap-3">
            <div class="d-flex flex-wrap gap-1">
                <a href="{{ route('dewan-skripsi.validasi-sidang', ['status_pendaftaran' => 'semua', 'search' => $search]) }}" class="btn btn-sm {{ $filterStatus === 'semua' ? 'btn-primary' : 'btn-light' }} fw-bold">
                    Semua
                </a>
                <a href="{{ route('dewan-skripsi.validasi-sidang', ['status_pendaftaran' => 'Menunggu Persetujuan', 'search' => $search]) }}" class="btn btn-sm {{ $filterStatus === 'Menunggu Persetujuan' ? 'btn-warning' : 'btn-light' }} fw-bold">
                    Menunggu Approval
                </a>
                <a href="{{ route('dewan-skripsi.validasi-sidang', ['status_pendaftaran' => 'Disetujui', 'search' => $search]) }}" class="btn btn-sm {{ $filterStatus === 'Disetujui' ? 'btn-success' : 'btn-light' }} fw-bold">
                    Disetujui (ACC)
                </a>
                <a href="{{ route('dewan-skripsi.validasi-sidang', ['status_pendaftaran' => 'Ditolak', 'search' => $search]) }}" class="btn btn-sm {{ $filterStatus === 'Ditolak' ? 'btn-danger' : 'btn-light' }} fw-bold">
                    Ditolak
                </a>
            </div>

            <!-- Search Form -->
            <form action="{{ route('dewan-skripsi.validasi-sidang') }}" method="GET" class="d-flex align-items-center position-relative my-1">
                <input type="hidden" name="status_pendaftaran" value="{{ $filterStatus }}" />
                <span class="svg-icon svg-icon-1 position-absolute ms-3">
                    <i class="fas fa-search text-gray-500"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm form-control-solid w-200px w-lg-250px ps-10" placeholder="Cari nama / NIM / judul..." />
                @if($search)
                    <a href="{{ route('dewan-skripsi.validasi-sidang', ['status_pendaftaran' => $filterStatus]) }}" class="btn btn-sm btn-icon btn-light ms-1">
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
                        <th class="w-40px">No</th>
                        <th class="min-w-200px">Mahasiswa</th>
                        <th class="min-w-220px">Judul Skripsi</th>
                        <th class="min-w-160px text-center">Rekam Jejak Bimbingan</th>
                        <th class="min-w-180px">Persetujuan Pembimbing</th>
                        <th class="min-w-150px text-center">Status Validasi</th>
                        <th class="min-w-160px text-end">Aksi Keputusan</th>
                    </tr>
                </thead>
                <tbody class="fs-7 fw-bold text-gray-600">
                    @forelse($skripsis as $index => $item)
                        <tr>
                            <td>{{ $skripsis->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40px me-3">
                                        <img src="{{ $item->mahasiswa?->avatar_url ?? asset('assets/media/avatars/300-1.jpg') }}" alt="" />
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-900 fw-bolder fs-6">{{ $item->mahasiswa?->name }}</span>
                                        <span class="text-muted fs-8">NIM: {{ $item->mahasiswa?->nim }}</span>
                                        <span class="badge badge-light fs-9 text-gray-600 align-self-start mt-0.5">
                                            Tahap: {{ $item->tahap }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-dark fw-bold mb-1" style="max-width: 280px; line-height: 1.35;">
                                    {{ $item->judul }}
                                </div>
                                <div class="text-muted fs-8">
                                    <strong>P1:</strong> {{ $item->nama_pembimbing_1 }}
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex flex-column align-items-center">
                                    @php
                                        $memenuhiSyarat = $item->total_bimbingan >= 8;
                                    @endphp
                                    <span class="badge {{ $memenuhiSyarat ? 'badge-light-success text-success' : 'badge-light-danger text-danger' }} fs-7 fw-bolder mb-1 px-3 py-1">
                                        <i class="fas {{ $memenuhiSyarat ? 'fa-check' : 'fa-exclamation-triangle' }} me-1"></i>
                                        {{ $item->total_bimbingan }}x Bimbingan
                                    </span>
                                    <span class="text-muted fs-9">
                                        {{ $memenuhiSyarat ? 'Syarat Min. 8x Terpenuhi' : 'Kurang dari Min. 8x' }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    @if($item->acc_pembimbing)
                                        <div class="badge badge-light-success text-success fw-bolder fs-8 align-self-start mb-1">
                                            <i class="fas fa-check-circle me-1 text-success"></i> Disetujui (ACC)
                                        </div>
                                        @if($item->catatan_pembimbing)
                                            <span class="text-gray-700 fs-9 fst-italic" style="max-width: 220px;">
                                                "{{ Str::limit($item->catatan_pembimbing, 60) }}"
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge badge-light-warning text-warning fw-bolder fs-8 align-self-start">
                                            <i class="fas fa-clock me-1"></i> Belum Ada Rekomendasi
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                @php
                                    $badgeStatus = match($item->status_pendaftaran_sidang) {
                                        'Disetujui' => 'badge-success',
                                        'Menunggu Persetujuan' => 'badge-warning text-dark',
                                        'Ditolak' => 'badge-danger',
                                        default => 'badge-light-secondary text-gray-700',
                                    };
                                @endphp
                                <span class="badge {{ $badgeStatus }} fw-bolder fs-8 px-3 py-1.5">
                                    {{ $item->status_pendaftaran_sidang }}
                                </span>
                                @if($item->tanggal_validasi_sidang)
                                    <div class="text-muted fs-9 mt-1">
                                        {{ $item->tanggal_validasi_sidang->format('d/m/Y H:i') }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <!-- Tombol Detail / Review Rekam Jejak -->
                                    <button type="button" class="btn btn-sm btn-icon btn-light-info" data-bs-toggle="modal" data-bs-target="#modal_review_{{ $item->id }}" title="Review Rekam Jejak Bimbingan">
                                        <i class="fas fa-search-plus fs-6"></i>
                                    </button>

                                    <!-- Tombol ACC / Setujui -->
                                    <button type="button" class="btn btn-sm btn-icon btn-light-success" data-bs-toggle="modal" data-bs-target="#modal_acc_{{ $item->id }}" title="Setujui (ACC) Sidang">
                                        <i class="fas fa-check fs-6"></i>
                                    </button>

                                    <!-- Tombol Tolak -->
                                    <button type="button" class="btn btn-sm btn-icon btn-light-danger" data-bs-toggle="modal" data-bs-target="#modal_reject_{{ $item->id }}" title="Tolak Pengajuan">
                                        <i class="fas fa-times fs-6"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal 1: Review Rekam Jejak Bimbingan & Berkas -->
                        <div class="modal fade" id="modal_review_{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content rounded-4 border-0 shadow-lg">
                                    <div class="modal-header bg-primary text-white py-4 px-6 rounded-top-4">
                                        <h5 class="modal-title text-white fw-bolder">
                                            <i class="fas fa-clipboard-list me-2"></i> Rekam Jejak Bimbingan & Persyaratan Sidang
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-6">
                                        <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                                            <div class="symbol symbol-45px me-3">
                                                <img src="{{ $item->mahasiswa?->avatar_url ?? asset('assets/media/avatars/300-1.jpg') }}" alt="" />
                                            </div>
                                            <div>
                                                <h5 class="fw-bolder text-dark mb-0">{{ $item->mahasiswa?->name }} (NIM: {{ $item->mahasiswa?->nim }})</h5>
                                                <span class="text-muted fs-8">{{ $item->judul }}</span>
                                            </div>
                                        </div>

                                        <div class="row g-4 mb-4">
                                            <div class="col-md-6">
                                                <div class="p-3 bg-light rounded">
                                                    <span class="fw-bold fs-8 text-primary text-uppercase d-block mb-1">Status Rekam Jejak Bimbingan</span>
                                                    <div class="fs-6 fw-bolder text-dark mb-1">
                                                        Total Sesi Bimbingan: <span class="text-primary">{{ $item->total_bimbingan }} Kali</span>
                                                    </div>
                                                    <div class="fs-8 text-muted">
                                                        Standar minimum jurusan: <strong>8 kali tatap muka bimbingan</strong>.
                                                        @if($item->total_bimbingan >= 8)
                                                            <span class="badge badge-success fs-9 ms-1">Memenuhi Standar</span>
                                                        @else
                                                            <span class="badge badge-danger fs-9 ms-1">Belum Memenuhi</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="p-3 bg-light rounded">
                                                    <span class="fw-bold fs-8 text-success text-uppercase d-block mb-1">Rekomendasi Dosen Pembimbing</span>
                                                    <div class="fs-7 fw-bolder text-dark mb-1">
                                                        Status ACC: 
                                                        @if($item->acc_pembimbing)
                                                            <span class="badge badge-success fs-8">Disetujui untuk Sidang</span>
                                                        @else
                                                            <span class="badge badge-warning fs-8">Belum Direkomendasikan</span>
                                                        @endif
                                                    </div>
                                                    <div class="fs-8 text-gray-700 fst-italic">
                                                        "{{ $item->catatan_pembimbing ?? 'Belum ada catatan persetujuan tertulis dari dosen pembimbing.' }}"
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Log Histori Draft Bimbingan Mahasiswa jika ada di tabel bimbingans -->
                                        <h6 class="fw-bolder text-dark mb-2">Riwayat Unggahan Draft Skripsi:</h6>
                                        <div class="table-responsive border rounded mb-3">
                                            <table class="table table-sm table-striped align-middle mb-0 gs-3">
                                                <thead class="bg-light fs-8 text-muted fw-bold">
                                                    <tr>
                                                        <th>Bab / Bagian</th>
                                                        <th>Waktu Unggah</th>
                                                        <th>Status Draft</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="fs-8">
                                                    @forelse($item->mahasiswa?->bimbingans ?? [] as $b)
                                                        <tr>
                                                            <td class="fw-bold text-dark">{{ $b->bab }}</td>
                                                            <td>{{ $b->created_at?->translatedFormat('d M Y, H:i') }}</td>
                                                            <td><span class="badge badge-light-primary">{{ $b->status }}</span></td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3" class="text-center text-muted py-2">
                                                                Tercatat {{ $item->total_bimbingan }} sesi bimbingan logbook manual dengan dosen pembimbing.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                        @if($item->catatan_validasi_sidang)
                                            <div class="alert alert-light-info border border-info p-3 mb-0">
                                                <span class="fw-bold fs-8 text-info text-uppercase d-block mb-1">Catatan Validasi Dewan Sebelumnya:</span>
                                                <span class="fs-7 text-dark">{{ $item->catatan_validasi_sidang }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer bg-light py-3 px-6 rounded-bottom-4">
                                        <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Tutup</button>
                                        <button type="button" class="btn btn-success fw-bolder" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#modal_acc_{{ $item->id }}">
                                            <i class="fas fa-check me-1"></i> Lanjut ACC Sidang
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal 2: ACC / Setujui Pendaftaran Sidang -->
                        <div class="modal fade" id="modal_acc_{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow-lg">
                                    <form action="{{ route('dewan-skripsi.validasi-sidang.approve', $item->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header bg-success text-white py-4 px-6 rounded-top-4">
                                            <h5 class="modal-title text-white fw-bolder">
                                                <i class="fas fa-check-circle me-2"></i> Setujui (ACC) Pendaftaran Sidang
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-6">
                                            <p class="fs-7 text-gray-700 mb-4">
                                                Apakah Anda yakin ingin menyetujui pendaftaran sidang mahasiswa <strong>{{ $item->mahasiswa?->name }}</strong>? Status mahasiswa akan otomatis beralih ke <strong>"Siap Sidang"</strong>.
                                            </p>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold fs-7">Catatan Persetujuan Dewan Skripsi (Opsional)</label>
                                                <textarea name="catatan_validasi_sidang" rows="2" class="form-control form-control-solid fs-7" placeholder="Contoh: Berkas dan rekam jejak bimbingan telah diverifikasi lengkap. Disetujui maju sidang.">Berkas bimbingan telah memenuhi syarat dan disetujui untuk pelaksanaan sidang tugas akhir.</textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold fs-7">Jadwalkan Sidang Sekaligus (Opsional)</label>
                                                <input type="datetime-local" name="jadwal_sidang" class="form-control form-control-solid fs-7" value="{{ $item->jadwal_sidang ? $item->jadwal_sidang->format('Y-m-d\TH:i') : '' }}" />
                                            </div>

                                            <div class="mb-2">
                                                <label class="form-label fw-bold fs-7">Ruangan Sidang (Opsional)</label>
                                                <input type="text" name="ruangan_sidang" class="form-control form-control-solid fs-7" placeholder="Contoh: Ruang Sidang 2 Fasilkom" value="{{ $item->ruangan_sidang }}" />
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light py-3 px-6 rounded-bottom-4 d-flex justify-content-between">
                                            <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-success fw-bolder px-5">
                                                <i class="fas fa-check me-1"></i> Konfirmasi ACC Sidang
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal 3: Tolak Pengajuan Sidang -->
                        <div class="modal fade" id="modal_reject_{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow-lg">
                                    <form action="{{ route('dewan-skripsi.validasi-sidang.reject', $item->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header bg-danger text-white py-4 px-6 rounded-top-4">
                                            <h5 class="modal-title text-white fw-bolder">
                                                <i class="fas fa-times-circle me-2"></i> Tolak Pengajuan Sidang
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-6">
                                            <p class="fs-7 text-gray-700 mb-3">
                                                Silakan masukkan alasan/catatan penolakan pendaftaran sidang untuk mahasiswa <strong>{{ $item->mahasiswa?->name }}</strong>:
                                            </p>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold fs-7 required">Alasan Penolakan / Catatan Perbaikan</label>
                                                <textarea name="catatan_validasi_sidang" rows="3" class="form-control form-control-solid fs-7" placeholder="Contoh: Pertemuan bimbingan belum mencapai syarat minimal 8 kali. Silakan lengkapi bimbingan dengan dosen pembimbing 2 terlebih dahulu..." required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light py-3 px-6 rounded-bottom-4 d-flex justify-content-between">
                                            <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger fw-bolder px-5">
                                                <i class="fas fa-times me-1"></i> Simpan Penolakan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10">
                                <span class="text-muted fw-bold">Tidak ada pengajuan pendaftaran sidang pada status ini.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap pt-4">
            <span class="text-muted fs-7">
                Menampilkan {{ $skripsis->firstItem() ?? 0 }} sampai {{ $skripsis->lastItem() ?? 0 }} dari {{ $skripsis->total() }} pengajuan
            </span>
            <div>{{ $skripsis->links() }}</div>
        </div>
    </div>
</div>
@endsection

