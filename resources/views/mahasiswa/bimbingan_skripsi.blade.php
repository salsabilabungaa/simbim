@extends('layouts.mahasiswa')

@section('title', 'Bimbingan Skripsi — SIMBIM')
@section('page-title', 'Bimbingan Skripsi')

@section('content')
<!-- Tombol Akses Ajukan Bimbingan Skripsi (Pojok Kanan Atas Konten) -->
<div class="d-flex justify-content-end mb-6">
    <button type="button" class="btn btn-primary fw-bolder px-5 py-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modal_ajukan_skripsi">
        <i class="fas fa-calendar-plus fs-4 me-2"></i> Ajukan Bimbingan Skripsi
    </button>
</div>

<!-- 1. Ringkasan Kartu Statistik Skripsi (4 Cards) -->
<div class="row g-5 g-xl-8 mb-7">
    <!-- Kartu 1: Persentase Progres Skripsi -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-body p-6">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-gray-600 fw-bolder fs-7 text-uppercase ls-1">PROGRES SKRIPSI</span>
                    <div class="symbol symbol-45px bg-light-primary">
                        <span class="symbol-label">
                            <i class="fas fa-chart-line text-primary fs-2"></i>
                        </span>
                    </div>
                </div>
                <div class="d-flex align-items-baseline mb-2">
                    <span class="fs-2x fw-bolder text-dark me-2">{{ $ringkasan['persen_progres'] }}%</span>
                    <span class="fs-7 text-muted fw-bold">Selesai</span>
                </div>
                <div class="progress h-8px bg-light-primary mb-2 rounded-pill">
                    <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: {{ $ringkasan['persen_progres'] }}%"></div>
                </div>
                <span class="badge badge-light-info fw-bolder fs-9 px-2 py-1">
                    {{ $ringkasan['bab_aktif'] }}
                </span>
            </div>
        </div>
    </div>

    <!-- Kartu 2: Total Sesi Bimbingan -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-body p-6">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-gray-600 fw-bolder fs-7 text-uppercase ls-1">TOTAL SESI SKRIPSI</span>
                    <div class="symbol symbol-45px bg-light-success">
                        <span class="symbol-label">
                            <i class="fas fa-comments text-success fs-2"></i>
                        </span>
                    </div>
                </div>
                <div class="d-flex align-items-baseline mb-2">
                    <span class="fs-2x fw-bolder text-dark me-2">{{ $ringkasan['total_sesi'] }}</span>
                    <span class="fs-7 text-muted fw-bold">Sesi Terlaksana</span>
                </div>
                <span class="badge badge-light-success fw-bolder fs-8 px-2 py-1">
                    <i class="fas fa-check me-1"></i> Memenuhi Syarat Sidang
                </span>
            </div>
        </div>
    </div>

    <!-- Kartu 3: Profil Pembimbing Utama -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-body p-6">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-gray-600 fw-bolder fs-7 text-uppercase ls-1">PEMBIMBING UTAMA</span>
                    <div class="symbol symbol-45px bg-light-info">
                        <span class="symbol-label">
                            <i class="fas fa-user-tie text-info fs-2"></i>
                        </span>
                    </div>
                </div>
                <div class="fw-bolder text-dark fs-6 mb-1 text-truncate">{{ $dosen_pembimbing['nama'] }}</div>
                <div class="text-muted fs-8 mb-2">NIP: {{ $dosen_pembimbing['nip'] }}</div>
                <span class="badge badge-light-{{ $dosen_pembimbing['badge_status'] }} fw-bolder fs-8 px-2 py-1">
                    <i class="fas fa-circle text-{{ $dosen_pembimbing['badge_status'] }} fs-9 me-1"></i> {{ $dosen_pembimbing['status_hadir'] }}
                </span>
            </div>
        </div>
    </div>

    <!-- Kartu 4: Jadwal Bimbingan Terdekat -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-body p-6 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-gray-600 fw-bolder fs-7 text-uppercase ls-1">JADWAL TERDEKAT</span>
                        <span class="badge badge-danger fw-bolder fs-9">{{ $jadwal_terdekat['sisa'] }}</span>
                    </div>
                    <div class="fw-bolder text-dark fs-6 mb-1">{{ $jadwal_terdekat['tanggal'] }}</div>
                    <div class="text-primary fw-bold fs-7 mb-2">
                        <i class="far fa-clock me-1"></i> {{ $jadwal_terdekat['waktu'] }}
                    </div>
                </div>
                <div class="pt-2 border-top border-gray-200 d-flex align-items-center justify-content-between">
                    <span class="badge badge-light-warning fw-bolder fs-9">
                        <i class="fas fa-map-marker-alt me-1 text-warning"></i> {{ $jadwal_terdekat['tempat'] }}
                    </span>
                    @if (!empty($jadwal_terdekat['is_h1']))
                        <button type="button" class="btn btn-warning btn-sm fw-bolder px-2.5 py-1 fs-9" onclick="remindDosenPembimbing(this)">
                            <i class="fas fa-bell me-1"></i> REMIND DOSEN
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 2. Card Tunggal Terpadu (Tabel Bimbingan, Review Dokumen & Diskusi Skripsi) -->
<div class="card card-custom bg-body border border-gray-200 shadow-sm mb-7">
    <div class="card-header border-0 pt-4 pb-0">
        <!-- Nav Tabs (Matching screenshot style: Semester Ganjil, Semester Genap) -->
        <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-5 fw-bolder" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active text-active-primary pb-3 me-6 cursor-pointer" data-bs-toggle="tab" href="#tab_tabel_bimbingan" role="tab">
                    <i class="fas fa-list-alt me-2 text-primary"></i> Tabel Bimbingan Skripsi
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link text-active-primary pb-3 me-6 cursor-pointer" data-bs-toggle="tab" href="#tab_review_dokumen" role="tab">
                    <i class="fas fa-file-alt me-2 text-info"></i> Review Dokumen
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link text-active-primary pb-3 cursor-pointer" data-bs-toggle="tab" href="#tab_diskusi_skripsi" role="tab">
                    <i class="fas fa-comments me-2 text-success"></i> Diskusi Skripsi
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body pt-4">
        <div class="tab-content">
            <!-- Tab 1: Tabel Bimbingan Skripsi -->
            <div class="tab-pane fade show active" id="tab_tabel_bimbingan" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <tr class="fw-bolder text-muted bg-light">
                                <th class="ps-4 min-w-140px rounded-start">Tanggal & Waktu</th>
                                <th class="min-w-220px">BAB / Topik Bimbingan</th>
                                <th class="min-w-180px text-center">Upload Dokumen Draft</th>
                                <th class="min-w-120px text-center">Status</th>
                                <th class="min-w-180px text-center">Dokumentasi (Webcam)</th>
                                <th class="min-w-140px text-end pe-4 rounded-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($riwayat_bimbingan as $item)
                            <tr>
                                <td class="ps-4">
                                    @if ($item['status'] === 'Proses' || empty($item['tanggal']))
                                        <span class="badge badge-light-secondary text-muted fw-bold fs-8">
                                            <i class="fas fa-clock me-1 text-muted"></i> Menunggu Dosen
                                        </span>
                                    @else
                                        <div class="d-flex flex-column">
                                            <span class="text-dark fw-bolder fs-6">{{ $item['tanggal'] }}</span>
                                            <span class="text-muted fs-8">{{ $item['waktu'] }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-dark fw-bold fs-7">{{ $item['topik'] }}</span>
                                </td>
                                <td class="text-center">
                                    <div id="doc_draft_container_{{ $item['id'] }}">
                                        @if (!empty($item['dokumen']))
                                            <a href="#" class="badge badge-light-primary border border-primary border-dashed fw-bolder px-3 py-2 fs-8 text-primary text-hover-underline me-1" onclick="event.preventDefault(); alert('Mengunduh {{ addslashes($item['dokumen']) }}...');">
                                                <i class="fas fa-file-pdf text-danger me-1 fs-6"></i> {{ $item['dokumen'] }}
                                            </a>
                                            @if ($item['status'] === 'ACC' || $item['status'] === 'Selesai')
                                                <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm h-25px w-25px" title="Ganti File Draft" onclick="openUploadModal({{ $item['id'] }}, '{{ addslashes($item['topik']) }}')">
                                                    <i class="fas fa-pencil-alt fs-9"></i>
                                                </button>
                                            @endif
                                        @else
                                            @if ($item['status'] === 'ACC' || $item['status'] === 'Selesai')
                                                <button type="button" class="btn btn-sm btn-light-info fw-bolder px-3 py-1.5 fs-8" onclick="openUploadModal({{ $item['id'] }}, '{{ addslashes($item['topik']) }}')">
                                                    <i class="fas fa-file-upload me-1"></i> Upload Draft
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-light fw-bold px-3 py-1.5 fs-8 text-muted cursor-not-allowed" disabled title="Upload draft hanya dapat dilakukan jika status bimbingan telah di-ACC Dosen">
                                                    <i class="fas fa-file-upload me-1 opacity-50"></i> Upload Draft
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if ($item['status'] === 'Proses')
                                        <span class="badge badge-light-warning fw-bolder px-3 py-2 fs-8">
                                            <i class="fas fa-spinner fa-spin me-1 text-warning"></i> Proses
                                        </span>
                                    @elseif ($item['status'] === 'ACC')
                                        <span class="badge badge-light-primary fw-bolder px-3 py-2 fs-8">
                                            <i class="fas fa-check-double me-1 text-primary"></i> ACC
                                        </span>
                                    @else
                                        <span class="badge badge-light-success fw-bolder px-3 py-2 fs-8">
                                            <i class="fas fa-check-circle me-1 text-success"></i> Selesai
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2" id="doc_preview_container_skripsi_{{ $item['id'] }}">
                                        @if (!empty($item['foto']))
                                            <div class="symbol symbol-40px symbol-2px cursor-pointer" onclick="viewDokumentasi('{{ asset($item['foto']) }}', '{{ addslashes($item['topik']) }}')">
                                                <img src="{{ asset($item['foto']) }}" alt="Foto Webcam" class="rounded border border-2 border-primary" style="object-fit: cover;" />
                                            </div>
                                            <span class="badge badge-light-success fs-9 fw-bolder">Ada Foto</span>
                                        @else
                                            <span class="badge badge-light-secondary text-muted fs-9 fw-bold">Belum Ada</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <div id="doc_action_container_skripsi_{{ $item['id'] }}">
                                        @if ($item['status'] === 'Proses' || $item['status'] === 'ACC')
                                            <button type="button" class="btn btn-sm btn-light fw-bold px-3 py-1.5 text-muted cursor-not-allowed" disabled title="Dokumentasi hanya dapat diunggah jika status bimbingan Selesai">
                                                <i class="fas fa-camera me-1 opacity-50"></i> Ambil Foto
                                            </button>
                                        @elseif (!empty($item['foto']))
                                            <button type="button" class="btn btn-sm btn-light-success fw-bolder px-3 py-1.5" onclick="viewDokumentasi('{{ asset($item['foto']) }}', '{{ addslashes($item['topik']) }}')">
                                                <i class="fas fa-check-circle me-1"></i> Terkirim
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-light-primary fw-bolder px-3 py-1.5" onclick="openWebcamModal({{ $item['id'] }}, '{{ addslashes($item['topik']) }}')">
                                                <i class="fas fa-camera me-1"></i> Ambil Foto
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 2: Review Dokumen -->
            <div class="tab-pane fade" id="tab_review_dokumen" role="tabpanel">
                <div class="d-flex align-items-center justify-content-between p-4 bg-light-primary rounded mb-5 border border-primary border-dashed">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file-pdf text-danger fs-2x me-4"></i>
                        <div class="d-flex flex-column">
                            <span class="text-dark fw-bolder fs-6">{{ $review_dokumen['nama_file'] }}</span>
                            <span class="text-muted fs-7">Versi: {{ $review_dokumen['versi'] }} • Diunggah: {{ $review_dokumen['tanggal_upload'] }}</span>
                        </div>
                    </div>
                </div>

                <h5 class="fw-bolder text-dark fs-6 mb-4">Catatan Review Dosen Pembimbing:</h5>
                <div class="d-flex flex-column gap-3 mb-3">
                    @foreach ($review_dokumen['catatan_review'] as $rev)
                    <div class="p-4 rounded-3 bg-light border border-gray-200">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bolder fs-7 text-primary">{{ $rev['halaman'] }}</span>
                            <span class="badge badge-light-{{ $rev['badge'] }} fw-bolder px-3 py-2 fs-8">{{ $rev['status'] }}</span>
                        </div>
                        <p class="text-gray-700 fs-7 fw-semibold mb-0">
                            "{{ $rev['catatan'] }}"
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Tab 3: Diskusi Skripsi -->
            <div class="tab-pane fade" id="tab_diskusi_skripsi" role="tabpanel">
                <div class="d-flex flex-column gap-3 scroll-y mh-300px pe-2 mb-4" id="chat_thread_container">
                    @foreach ($diskusi_skripsi as $chat)
                    <div class="p-4 rounded-3 bg-light border border-gray-200">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center">
                                <span class="fw-bolder fs-7 text-dark me-2">{{ $chat['pengirim'] }}</span>
                                <span class="badge badge-light-{{ $chat['badge_role'] }} fw-bolder fs-8">{{ $chat['role'] }}</span>
                            </div>
                            <span class="text-muted fs-8">{{ $chat['waktu'] }}</span>
                        </div>
                        <p class="text-gray-700 fs-7 mb-0">
                            {{ $chat['pesan'] }}
                        </p>
                    </div>
                    @endforeach
                </div>

                <form id="form_send_chat" onsubmit="sendDiskusiPesan(event)">
                    <div class="input-group input-group-solid">
                        <input type="text" id="input_pesan_diskusi" class="form-control form-control-solid fs-7" placeholder="Tulis pesan diskusi mengenai draf skripsi..." required />
                        <button type="submit" class="btn btn-primary fw-bolder px-6">
                            <i class="fas fa-paper-plane me-1"></i> Kirim Pesan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 3. Section Feedback Pembimbing & Progress BAB Skripsi -->
<div class="row g-5 g-xl-8">
    <!-- Left Column (8 Columns): Feedback Pembimbing -->
    <div class="col-xl-8">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm mb-7">
            <div class="card-header border-0 pt-6">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder fs-4 text-dark">Feedback & Catatan Dosen Pembimbing</span>
                    <span class="text-muted mt-1 fw-bold fs-7">Arahan dan revisi resmi dari Dosen Pembimbing Skripsi</span>
                </h3>
            </div>
            <div class="card-body pt-3">
                <div class="d-flex flex-column gap-4">
                    @foreach ($catatan_pembimbing as $c)
                    <div class="p-5 rounded-3 bg-light border border-gray-200">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-35px me-3">
                                    <span class="symbol-label bg-light-primary">
                                        <i class="fas fa-user-tie text-primary fs-5"></i>
                                    </span>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ $dosen_pembimbing['nama'] }}</span>
                                    <span class="text-muted fs-8">{{ $c['bab'] }} • {{ $c['tanggal'] }}</span>
                                </div>
                            </div>
                            <span class="badge badge-light-{{ $c['badge'] }} fw-bolder px-3 py-2 fs-8">
                                Terverifikasi
                            </span>
                        </div>
                        <p class="text-gray-700 fs-7 fw-semibold mb-0 mt-2">
                            "{{ $c['catatan'] }}"
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column (4 Columns): Progres Pengerjaan BAB Skripsi -->
    <div class="col-xl-4">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm mb-7">
            <div class="card-header border-0 pt-6">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder fs-4 text-dark">Progres BAB Skripsi</span>
                    <span class="text-muted mt-1 fw-bold fs-7">Status penyelesaian tiap bab</span>
                </h3>
            </div>
            <div class="card-body pt-2">
                <div class="d-flex flex-column gap-4">
                    @foreach ($progres_bab as $pb)
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bolder fs-7 text-dark">{{ $pb['bab'] }}</span>
                            <span class="badge badge-light-{{ $pb['badge'] }} fw-bolder fs-9">{{ $pb['status'] }}</span>
                        </div>
                        <div class="progress h-8px bg-light rounded-pill">
                            <div class="progress-bar bg-{{ $pb['badge'] }} rounded-pill" role="progressbar" style="width: {{ $pb['persen'] }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajukan Bimbingan Skripsi -->
<div class="modal fade" id="modal_ajukan_skripsi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0 pb-0">
                <h3 class="modal-title fw-bolder fs-3 text-dark">Ajukan Bimbingan Skripsi Baru</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times fs-4"></i>
                </div>
            </div>
            <div class="modal-body scroll-y px-10 py-7">
                <div class="alert alert-light-info d-flex align-items-center p-4 mb-5 border-dashed border-info rounded-3">
                    <i class="fas fa-info-circle text-info fs-3 me-3"></i>
                    <div class="fs-7 text-gray-700 fw-semibold">
                        <strong>Informasi:</strong> Waktu, tanggal, dan tempat bimbingan akan ditentukan dan dikonfirmasi langsung oleh Dosen Pembimbing setelah pengajuan Anda dikirim.
                    </div>
                </div>

                <form id="form_ajukan_skripsi" action="#" method="POST">
                    @csrf
                    <div class="mb-5">
                        <label class="form-label fw-bold fs-6 required">BAB / Topik Bimbingan Skripsi</label>
                        <select class="form-select form-select-solid" name="kategori_topik" required>
                            <option value="BAB I: Pendahuluan & Latar Belakang">BAB I: Pendahuluan & Latar Belakang</option>
                            <option value="BAB II: Tinjauan Pustaka & Research Gap">BAB II: Tinjauan Pustaka & Research Gap</option>
                            <option value="BAB III: Metodologi Penelitian & Perancangan System Diagram" selected>BAB III: Metodologi Penelitian & Perancangan System Diagram</option>
                            <option value="BAB IV: Hasil & Pembahasan Sistem">BAB IV: Hasil & Pembahasan Sistem</option>
                            <option value="BAB V: Kesimpulan & Saran">BAB V: Kesimpulan & Saran</option>
                            <option value="Persiapan Seminar Hasil / Draf Final">Persiapan Seminar Hasil / Draf Final Skripsi</option>
                        </select>
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-bold fs-6 required">Detail / Catatan Revisi yang Diskusi</label>
                        <textarea class="form-control form-control-solid" name="detail_agenda" rows="4" required placeholder="Tuliskan poin-poin revisi atau draf yang ingin dikonsultasikan dengan Dosen Pembimbing..."></textarea>
                    </div>

                    <div class="text-end pt-5">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary fw-bolder">
                            <i class="fas fa-paper-plane me-1"></i> Kirim Pengajuan Bimbingan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Webcam Capture Dokumentasi -->
<div class="modal fade" id="modal_webcam_capture" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0 pb-0">
                <h3 class="modal-title fw-bolder fs-3 text-dark">
                    <i class="fas fa-camera text-primary me-2"></i> Ambil Dokumentasi Bimbingan Skripsi (Webcam)
                </h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" onclick="closeWebcamModal()" aria-label="Close">
                    <i class="fas fa-times fs-4"></i>
                </div>
            </div>
            <div class="modal-body scroll-y px-8 py-6">
                <p class="text-muted fs-7 mb-4">
                    Topik Sesi: <strong id="webcam_topik_title" class="text-dark">---</strong>
                </p>

                <!-- Camera Feed Container -->
                <div class="position-relative bg-dark rounded-4 overflow-hidden d-flex align-items-center justify-content-center mb-5" style="min-height: 320px;">
                    <video id="webcam_stream" autoplay playsinline class="w-100 h-100 rounded-4" style="max-height: 340px; object-fit: cover; display: block;"></video>
                    <img id="captured_result_img" class="w-100 h-100 rounded-4" style="max-height: 340px; object-fit: cover; display: none;" alt="Hasil Capture" />
                    <canvas id="webcam_canvas" style="display: none;"></canvas>

                    <div id="live_camera_badge" class="position-absolute top-0 start-0 m-4 badge badge-danger fw-bolder px-3 py-2 fs-8">
                        <i class="fas fa-circle text-white fs-9 me-1 animation-blink"></i> LIVE WEBCAM
                    </div>
                </div>

                <div class="alert alert-light-warning d-flex align-items-center p-3 mb-4 rounded-3 border border-warning border-dashed">
                    <i class="fas fa-camera-retro text-warning fs-4 me-3"></i>
                    <div class="fs-8 text-gray-700 fw-semibold">
                        Pastikan wajah Anda dan Dosen Pembimbing terlihat jelas di kamera sebelum menekan tombol ambil foto.
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-2">
                    <button type="button" class="btn btn-light fw-bold" onclick="closeWebcamModal()">Batal</button>
                    <div>
                        <button type="button" id="btn_retake_photo" class="btn btn-light-warning fw-bolder me-2" style="display: none;" onclick="retakePhoto()">
                            <i class="fas fa-redo me-1"></i> Foto Ulang
                        </button>
                        <button type="button" id="btn_capture_photo" class="btn btn-primary fw-bolder px-6" onclick="captureSnapshot()">
                            <i class="fas fa-camera me-2"></i> Ambil Foto Snapshot
                        </button>
                        <button type="button" id="btn_save_photo" class="btn btn-success fw-bolder px-6" style="display: none;" onclick="saveDokumentasiPhoto()">
                            <i class="fas fa-check me-2"></i> Simpan Dokumentasi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload Dokumen Draft Skripsi -->
<div class="modal fade" id="modal_upload_dokumen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0 pb-0">
                <h3 class="modal-title fw-bolder fs-4 text-dark">
                    <i class="fas fa-file-upload text-primary me-2"></i> Upload Dokumen Draft Skripsi
                </h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times fs-4"></i>
                </div>
            </div>
            <div class="modal-body scroll-y px-8 py-6">
                <p class="text-muted fs-7 mb-4">
                    Topik Bimbingan: <strong id="upload_topik_title" class="text-dark">---</strong>
                </p>

                <form id="form_upload_dokumen" onsubmit="submitUploadDokumen(event)">
                    <div class="mb-5">
                        <label class="form-label fw-bold fs-6 required">Pilih File Draft (PDF / DOCX)</label>
                        <input type="file" id="input_file_draft" class="form-control form-control-solid" accept=".pdf,.docx,.doc" required />
                        <div class="form-text fs-8 text-muted mt-1">Format diperbolehkan: .pdf, .docx, .doc (Maksimal 15 MB).</div>
                    </div>

                    <div class="text-end pt-3">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary fw-bolder">
                            <i class="fas fa-upload me-1"></i> Unggah Dokumen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal View Photo Dokumentasi -->
<div class="modal fade" id="modal_view_dokumentasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0 pb-0">
                <h4 class="modal-title fw-bolder fs-5 text-dark" id="view_foto_title">Foto Dokumentasi Bimbingan</h4>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times fs-4"></i>
                </div>
            </div>
            <div class="modal-body text-center p-6">
                <img id="view_foto_img" src="" class="img-fluid rounded-3 border border-gray-300 mb-3 shadow-sm" alt="Foto Dokumentasi Full" />
                <span class="badge badge-light-success fw-bolder px-3 py-2 fs-8">
                    <i class="fas fa-check-circle me-1 text-success"></i> Tervalidasi Sistem SIMBIM
                </span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let activeBimbinganId = null;
    let activeUploadBimbinganId = null;
    let webcamStreamTrack = null;
    let capturedDataUrl = null;

    function openUploadModal(id, topik) {
        activeUploadBimbinganId = id;
        document.getElementById('upload_topik_title').innerText = topik;
        document.getElementById('input_file_draft').value = '';
        const modal = new bootstrap.Modal(document.getElementById('modal_upload_dokumen'));
        modal.show();
    }

    function submitUploadDokumen(e) {
        e.preventDefault();
        const fileInput = document.getElementById('input_file_draft');
        if (!fileInput.files || fileInput.files.length === 0) return;

        const fileName = fileInput.files[0].name;
        const targetContainer = document.getElementById('doc_draft_container_' + activeUploadBimbinganId);
        if (targetContainer) {
            targetContainer.innerHTML = `
                <a href="#" class="badge badge-light-primary border border-primary border-dashed fw-bolder px-3 py-2 fs-8 text-primary text-hover-underline me-1" onclick="event.preventDefault(); alert('Mengunduh ${fileName}...');">
                    <i class="fas fa-file-pdf text-danger me-1 fs-6"></i> ${fileName}
                </a>
                <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm h-25px w-25px" title="Ganti File Draft" onclick="openUploadModal(${activeUploadBimbinganId}, 'Draft Skripsi')">
                    <i class="fas fa-pencil-alt fs-9"></i>
                </button>
            `;
        }

        const modalEl = document.getElementById('modal_upload_dokumen');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) modalInstance.hide();

        alert("Dokumen draft '" + fileName + "' berhasil diunggah untuk sesi bimbingan skripsi ini!");
    }

    function sendDiskusiPesan(e) {
        e.preventDefault();
        const input = document.getElementById('input_pesan_diskusi');
        if (!input || !input.value.trim()) return;

        const pesan = input.value.trim();
        const chatContainer = document.getElementById('chat_thread_container');

        const newChatHtml = `
            <div class="p-3 rounded-3 bg-light-primary border border-primary border-dashed">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <div class="d-flex align-items-center">
                        <span class="fw-bolder fs-8 text-dark me-2">Andi Pratama</span>
                        <span class="badge badge-light-info fw-bolder fs-9">Mahasiswa</span>
                    </div>
                    <span class="text-muted fs-9">Baru saja</span>
                </div>
                <p class="text-gray-800 fs-8 mb-0">
                    ${pesan}
                </p>
            </div>
        `;

        chatContainer.insertAdjacentHTML('beforeend', newChatHtml);
        chatContainer.scrollTop = chatContainer.scrollHeight;
        input.value = '';
    }

    function remindDosenPembimbing(btn) {
        if (!btn) return;
        btn.disabled = true;
        btn.className = "btn btn-success btn-sm fw-bolder px-2.5 py-1 fs-9";
        btn.innerHTML = '<i class="fas fa-check me-1"></i> TERKIRIM';
        alert("Notifikasi pengingat bimbingan skripsi H-1 telah dikirimkan kepada Dr. Ir. Teuku Muhammad, M.T.!");
    }

    function openWebcamModal(id, topik) {
        activeBimbinganId = id;
        capturedDataUrl = null;
        document.getElementById('webcam_topik_title').innerText = topik;

        document.getElementById('webcam_stream').style.display = 'block';
        document.getElementById('captured_result_img').style.display = 'none';
        document.getElementById('live_camera_badge').style.display = 'inline-block';

        document.getElementById('btn_capture_photo').style.display = 'inline-block';
        document.getElementById('btn_retake_photo').style.display = 'none';
        document.getElementById('btn_save_photo').style.display = 'none';

        const webcamModal = new bootstrap.Modal(document.getElementById('modal_webcam_capture'));
        webcamModal.show();

        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: { width: 1280, height: 720 } })
                .then(function(stream) {
                    webcamStreamTrack = stream;
                    const videoEl = document.getElementById('webcam_stream');
                    videoEl.srcObject = stream;
                })
                .catch(function(err) {
                    console.log("Webcam access warning / fallback mode:", err);
                    simulateWebcamFeed();
                });
        } else {
            simulateWebcamFeed();
        }
    }

    function simulateWebcamFeed() {
        const videoEl = document.getElementById('webcam_stream');
        videoEl.poster = "https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=600&q=80";
    }

    function captureSnapshot() {
        const video = document.getElementById('webcam_stream');
        const canvas = document.getElementById('webcam_canvas');
        const context = canvas.getContext('2d');

        canvas.width = 640;
        canvas.height = 480;

        if (video.srcObject && video.readyState === 4) {
            context.drawImage(video, 0, 0, 640, 480);
            capturedDataUrl = canvas.toDataURL('image/jpeg');
        } else {
            context.fillStyle = "#1e1e2d";
            context.fillRect(0, 0, 640, 480);

            context.fillStyle = "#3699ff";
            context.font = "bold 24px Poppins, sans-serif";
            context.fillText("SIMBIM SKRIPSI DOCUMENTATION", 110, 200);

            context.fillStyle = "#ffffff";
            context.font = "16px Poppins, sans-serif";
            context.fillText("Mahasiswa: Andi Pratama (2108107010001)", 130, 240);
            context.fillText("Waktu Foto: " + new Date().toLocaleString('id-ID'), 130, 270);
            context.fillText("Status: Dokumentasi Bimbingan Skripsi Valid", 130, 300);

            capturedDataUrl = canvas.toDataURL('image/jpeg');
        }

        const resultImg = document.getElementById('captured_result_img');
        resultImg.src = capturedDataUrl;
        resultImg.style.display = 'block';

        video.style.display = 'none';
        document.getElementById('live_camera_badge').style.display = 'none';

        document.getElementById('btn_capture_photo').style.display = 'none';
        document.getElementById('btn_retake_photo').style.display = 'inline-block';
        document.getElementById('btn_save_photo').style.display = 'inline-block';
    }

    function retakePhoto() {
        document.getElementById('webcam_stream').style.display = 'block';
        document.getElementById('captured_result_img').style.display = 'none';
        document.getElementById('live_camera_badge').style.display = 'inline-block';

        document.getElementById('btn_capture_photo').style.display = 'inline-block';
        document.getElementById('btn_retake_photo').style.display = 'none';
        document.getElementById('btn_save_photo').style.display = 'none';
        capturedDataUrl = null;
    }

    function closeWebcamModal() {
        if (webcamStreamTrack) {
            webcamStreamTrack.getTracks().forEach(track => track.stop());
            webcamStreamTrack = null;
        }
        const modalEl = document.getElementById('modal_webcam_capture');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) {
            modalInstance.hide();
        }
    }

    function saveDokumentasiPhoto() {
        if (!capturedDataUrl || !activeBimbinganId) return;

        const targetContainer = document.getElementById('doc_preview_container_skripsi_' + activeBimbinganId);
        if (targetContainer) {
            targetContainer.innerHTML = `
                <div class="symbol symbol-40px symbol-2px cursor-pointer" onclick="viewDokumentasi('${capturedDataUrl}', 'Foto Dokumentasi Terbaru')">
                    <img src="${capturedDataUrl}" alt="Foto Webcam" class="rounded border border-2 border-success" style="object-fit: cover;" />
                </div>
                <span class="badge badge-light-success fs-9 fw-bolder">Ada Foto</span>
            `;
        }

        const actionContainer = document.getElementById('doc_action_container_skripsi_' + activeBimbinganId);
        if (actionContainer) {
            actionContainer.innerHTML = `
                <button type="button" class="btn btn-sm btn-light-success fw-bolder px-3 py-1.5" onclick="viewDokumentasi('${capturedDataUrl}', 'Foto Dokumentasi Terbaru')">
                    <i class="fas fa-check-circle me-1"></i> Dokumentasi Terkirim
                </button>
            `;
        }

        closeWebcamModal();
        alert("Dokumentasi foto webcam bimbingan skripsi berhasil disimpan & terkirim!");
    }

    function viewDokumentasi(imgSrc, title) {
        document.getElementById('view_foto_title').innerText = title || "Dokumentasi Foto Bimbingan Skripsi";
        document.getElementById('view_foto_img').src = imgSrc;
        const viewModal = new bootstrap.Modal(document.getElementById('modal_view_dokumentasi'));
        viewModal.show();
    }
</script>
@endpush
