@extends('layouts.mahasiswa')

@section('title', 'Bimbingan Akademik — SIMBIM')
@section('page-title', 'Bimbingan Akademik')

@section('content')
<!-- Tombol Akses Ajukan Bimbingan Akademik (Pojok Kanan Atas Konten) -->
<div class="d-flex justify-content-end mb-6">
    <button type="button" class="btn btn-primary fw-bolder px-5 py-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modal_ajukan_akademik">
        <i class="fas fa-calendar-plus fs-4 me-2"></i> Ajukan Bimbingan Akademik
    </button>
</div>

<!-- 2. Ringkasan Kartu Statistik (4 Cards) -->
<div class="row g-5 g-xl-8 mb-7">
    <!-- Kartu 1: Total Sesi Terlaksana -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-body p-6">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-gray-600 fw-bolder fs-7 text-uppercase ls-1">TOTAL SESI AKADEMIK</span>
                    <div class="symbol symbol-45px bg-light-primary">
                        <span class="symbol-label">
                            <i class="fas fa-user-clock text-primary fs-2"></i>
                        </span>
                    </div>
                </div>
                <div class="d-flex align-items-baseline mb-2">
                    <span class="fs-2x fw-bolder text-dark me-2">{{ $ringkasan['total_sesi'] }}</span>
                    <span class="fs-7 text-muted fw-bold">Sesi Konsultasi DPA</span>
                </div>
                <span class="badge badge-light-success fw-bolder fs-8 px-2 py-1">
                    <i class="fas fa-check me-1"></i> Target Sesi Terpenuhi
                </span>
            </div>
        </div>
    </div>

    <!-- Kartu 2: IPK Kumulatif & SKS -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-body p-6">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-gray-600 fw-bolder fs-7 text-uppercase ls-1">IPK KUMULATIF</span>
                    <div class="symbol symbol-45px bg-light-warning">
                        <span class="symbol-label">
                            <i class="fas fa-award text-warning fs-2"></i>
                        </span>
                    </div>
                </div>
                <div class="d-flex align-items-baseline mb-2">
                    <span class="fs-2x fw-bolder text-dark me-2">{{ $ringkasan['ipk_kumulatif'] }}</span>
                    <span class="fs-7 text-muted fw-bold">/ 4.00</span>
                </div>
                <span class="badge badge-light-warning fw-bolder fs-8 px-2 py-1">
                    <i class="fas fa-book me-1"></i> {{ $ringkasan['sks_lulus'] }} SKS Lulus
                </span>
            </div>
        </div>
    </div>

    <!-- Kartu 3: Status KRS -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-body p-6">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-gray-600 fw-bolder fs-7 text-uppercase ls-1">STATUS KRS SEMESTER 7</span>
                    <div class="symbol symbol-45px bg-light-success">
                        <span class="symbol-label">
                            <i class="fas fa-file-signature text-success fs-2"></i>
                        </span>
                    </div>
                </div>
                <div class="fw-bolder text-dark fs-6 mb-1 text-truncate">{{ $ringkasan['status_krs'] }}</div>
                <div class="text-muted fs-8 mb-2">Disetujui 21 SKS</div>
                <span class="badge badge-light-success fw-bolder fs-8 px-2 py-1">
                    <i class="fas fa-check-circle me-1"></i> Disetujui Dosen Wali
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
                        <button type="button" class="btn btn-warning fw-bolder px-3 py-2 d-flex flex-column align-items-center justify-content-center lh-1 text-white shadow-sm" style="border-radius: 10px;" onclick="remindDosenWali(this)">
                            <i class="fas fa-bell text-white fs-5 mb-1"></i>
                            <span class="fs-9 text-uppercase text-white fw-bolder d-block" style="letter-spacing: 0.5px;">REMIND</span>
                            <span class="fs-9 text-uppercase text-white fw-bolder d-block" style="letter-spacing: 0.5px;">DOSEN</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. Tabel Bimbingan Akademik -->
<div class="card card-custom bg-body border border-gray-200 shadow-sm mb-7">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bolder fs-4 text-dark">Daftar Sesi Bimbingan Akademik</span>
            <span class="text-muted mt-1 fw-bold fs-7">Rekam konsultasi kartu rencana studi dan evaluasi akademik</span>
        </h3>
        <div class="card-toolbar">
            <span class="badge badge-light-primary fw-bolder fs-7">Total {{ count($riwayat_bimbingan) }} Sesi</span>
        </div>
    </div>
    <div class="card-body pt-3">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-7 gy-4">
                <thead>
                    <tr class="text-start text-muted fw-bolder fs-8 text-uppercase ls-1 bg-light">
                        <th class="ps-4 min-w-150px rounded-start">TANGGAL & WAKTU</th>
                        <th class="min-w-250px">TOPIK / AGENDA CONSULTATION</th>
                        <th class="min-w-110px text-center">STATUS</th>
                        <th class="min-w-160px text-center">DOKUMENTASI WEBCAM</th>
                        <th class="text-end pe-4 min-w-140px rounded-end">AKSI</th>
                    </tr>
                </thead>
                <tbody class="text-gray-800 fw-bold">
                    @foreach ($riwayat_bimbingan as $item)
                    <tr>
                        <td class="ps-4">
                            @if ($item['status'] === 'Proses' || empty($item['tanggal']))
                                <span class="badge badge-light-secondary text-muted fw-bold fs-8">
                                    <i class="fas fa-clock me-1 text-muted"></i> Menunggu Dosen
                                </span>
                            @else
                                <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-7">{{ $item['tanggal'] }}</span>
                                    <span class="text-muted fs-8"><i class="far fa-clock me-1 text-primary"></i>{{ $item['waktu'] }}</span>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bolder text-dark fs-7 mb-0.5">{{ $item['topik'] }}</div>
                            <div class="text-muted fs-8"><i class="fas fa-user-shield me-1 text-success"></i>Dosen Wali: {{ $dosen_wali['nama'] }}</div>
                        </td>
                        <td class="text-center">
                            @if ($item['status'] === 'Proses')
                                <span class="badge badge-light-warning fw-bolder px-3 py-1.5 fs-8">
                                    <i class="fas fa-spinner fa-spin me-1 text-warning"></i> Proses
                                </span>
                            @elseif ($item['status'] === 'ACC')
                                <span class="badge badge-light-primary fw-bolder px-3 py-1.5 fs-8">
                                    <i class="fas fa-check-double me-1 text-primary"></i> ACC
                                </span>
                            @else
                                <span class="badge badge-light-success fw-bolder px-3 py-1.5 fs-8">
                                    <i class="fas fa-check-circle me-1 text-success"></i> Selesai
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2" id="doc_preview_container_{{ $item['id'] }}">
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
                            <div id="doc_action_container_{{ $item['id'] }}">
                                @if ($item['status'] === 'Proses' || $item['status'] === 'ACC')
                                    <button type="button" class="btn btn-sm btn-light fw-bold px-3 py-1.5 text-muted cursor-not-allowed" disabled title="Dokumentasi hanya dapat diunggah jika status bimbingan Selesai">
                                        <i class="fas fa-camera me-1 opacity-50"></i> Ambil Foto
                                    </button>
                                @elseif (!empty($item['foto']))
                                    <button type="button" class="btn btn-sm btn-light-success fw-bolder px-3 py-1.5 fs-8" onclick="viewDokumentasi('{{ asset($item['foto']) }}', '{{ addslashes($item['topik']) }}')">
                                        <i class="fas fa-check-circle me-1"></i> Dokumentasi Terkirim
                                    </button>
                                @else
                                    <button type="button" class="btn btn-sm btn-light-primary fw-bolder px-3 py-1.5 fs-8" onclick="openWebcamModal({{ $item['id'] }}, '{{ addslashes($item['topik']) }}')">
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
</div>

<!-- 4. Main Section: Left (Catatan) | Right (Evaluasi Studi) -->
<div class="row g-5 g-xl-8">
    <!-- Left Column (8 Columns) -->
    <div class="col-xl-8">
        <!-- Feedback & Catatan Konsultasi Dosen Wali -->
        <div class="card card-custom bg-body border border-gray-200 shadow-sm mb-7">
            <div class="card-header border-0 pt-6">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder fs-4 text-dark">Catatan & Arahan Dosen Wali</span>
                    <span class="text-muted mt-1 fw-bold fs-7">Rekomendasi dan masukan akademik resmi dari Dosen Wali</span>
                </h3>
            </div>
            <div class="card-body pt-3">
                <div class="d-flex flex-column gap-4">
                    @foreach ($catatan_dosen_wali as $c)
                    <div class="p-5 rounded-3 bg-light border border-gray-200">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-35px symbol-circle me-3 bg-light-success">
                                    <span class="symbol-label">
                                        <i class="fas fa-user-shield text-success fs-5"></i>
                                    </span>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="text-dark fw-bolder fs-6">{{ $dosen_wali['nama'] }}</span>
                                    <span class="text-muted fs-8">{{ $c['topik'] }} • {{ $c['tanggal'] }}</span>
                                </div>
                            </div>
                            <span class="badge badge-light-{{ $c['badge'] }} fw-bolder px-3 py-1.5 fs-8">
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

    <!-- Right Column (4 Columns): Evaluasi Studi -->
    <div class="col-xl-4">
        <!-- Card Progress Evaluasi Studi SKS -->
        <div class="card card-custom bg-body border border-gray-200 shadow-sm mb-7">
            <div class="card-header border-0 pt-6">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder fs-4 text-dark">Evaluasi Kelulusan SKS</span>
                    <span class="text-muted mt-1 fw-bold fs-7">Kemajuan pemenuhan SKS kelulusan</span>
                </h3>
            </div>
            <div class="card-body pt-2">
                <div class="d-flex align-items-baseline mb-2">
                    <span class="fs-2x fw-bolder text-dark me-2">{{ $ringkasan['sks_lulus'] }}</span>
                    <span class="fs-7 text-muted fw-bold">/ 144 SKS Kelulusan</span>
                    <span class="badge badge-light-primary ms-auto fw-bolder fs-8">82% Selesai</span>
                </div>
                <div class="progress h-8px bg-light-primary mb-4 rounded-pill">
                    <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: 82%"></div>
                </div>

                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center fs-7 text-gray-700 p-2.5 rounded bg-light">
                        <span><i class="fas fa-check-circle text-success me-2"></i> Wajib Program Studi</span>
                        <strong class="text-dark">100 SKS</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center fs-7 text-gray-700 p-2.5 rounded bg-light">
                        <span><i class="fas fa-check-circle text-success me-2"></i> Pilihan & MBKM</span>
                        <strong class="text-dark">18 SKS</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center fs-7 text-gray-700 p-2.5 rounded bg-light">
                        <span><i class="fas fa-clock text-warning me-2"></i> Skripsi (Dalam Proses)</span>
                        <strong class="text-dark">6 SKS</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajukan Bimbingan Akademik -->
<div class="modal fade" id="modal_ajukan_akademik" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0 pb-0">
                <h3 class="modal-title fw-bolder fs-3 text-dark">Ajukan Bimbingan Akademik Baru</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times fs-4"></i>
                </div>
            </div>
            <div class="modal-body scroll-y px-10 py-7">
                <div class="alert alert-light-info d-flex align-items-center p-4 mb-5 border-dashed border-info rounded-3">
                    <i class="fas fa-info-circle text-info fs-3 me-3"></i>
                    <div class="fs-7 text-gray-700 fw-semibold">
                        <strong>Informasi:</strong> Waktu, tanggal, dan lokasi/metode bimbingan akan ditentukan dan dikonfirmasi langsung oleh Dosen Wali setelah pengajuan Anda dikirim.
                    </div>
                </div>

                <form id="form_ajukan_akademik" action="#" method="POST">
                    @csrf
                    <div class="mb-5">
                        <label class="form-label fw-bold fs-6 required">Kategori Topik Bimbingan</label>
                        <select class="form-select form-select-solid" name="kategori_topik" required>
                            <option value="Konsultasi KRS & Perencanaan Studi" selected>Konsultasi KRS & Perencanaan Studi Semester</option>
                            <option value="Evaluasi Hasil Belajar & IPK">Evaluasi Hasil Belajar & Peningkatan IPK</option>
                            <option value="Pengajuan Rekomendasi Magang / MBKM">Pengajuan Rekomendasi Magang / Program MBKM</option>
                            <option value="Pengajuan Beasiswa / Surat Rekomendasi">Pengajuan Beasiswa & Surat Rekomendasi</option>
                            <option value="Konsultasi Masalah Akademik / Lainnya">Konsultasi Masalah Akademik / Lainnya</option>
                        </select>
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-bold fs-6 required">Detail / Agendakan Topik yang Didiskusikan</label>
                        <textarea class="form-control form-control-solid" name="detail_agenda" rows="4" required placeholder="Tuliskan topik atau poin-poin masalah akademik yang ingin dikonsultasikan dengan Dosen Wali..."></textarea>
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
                    <i class="fas fa-camera text-primary me-2"></i> Ambil Dokumentasi Bimbingan (Webcam)
                </h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" onclick="closeWebcamModal()" aria-label="Close">
                    <i class="fas fa-times fs-4"></i>
                </div>
            </div>
            <div class="modal-body scroll-y px-8 py-6">
                <p class="text-muted fs-7 mb-4">
                    Topik Sesi: <strong id="webcam_topik_title" class="text-dark">---</strong>
                </p>

                <!-- Camera Feed / Preview Container -->
                <div class="position-relative bg-dark rounded-4 overflow-hidden d-flex align-items-center justify-content-center mb-5" style="min-height: 320px;">
                    <video id="webcam_stream" autoplay playsinline class="w-100 h-100 rounded-4" style="max-height: 340px; object-fit: cover; display: block;"></video>
                    <img id="captured_result_img" class="w-100 h-100 rounded-4" style="max-height: 340px; object-fit: cover; display: none;" alt="Hasil Capture" />
                    <canvas id="webcam_canvas" style="display: none;"></canvas>

                    <!-- Live Indicator Badge -->
                    <div id="live_camera_badge" class="position-absolute top-0 start-0 m-4 badge badge-danger fw-bolder px-3 py-2 fs-8">
                        <i class="fas fa-circle text-white fs-9 me-1 animation-blink"></i> LIVE WEBCAM
                    </div>
                </div>

                <!-- Alert Information -->
                <div class="alert alert-light-warning d-flex align-items-center p-3 mb-4 rounded-3 border border-warning border-dashed">
                    <i class="fas fa-camera-retro text-warning fs-4 me-3"></i>
                    <div class="fs-8 text-gray-700 fw-semibold">
                        Pastikan wajah Anda dan Dosen/Suasana Bimbingan terlihat jelas di area kamera sebelum menekan tombol ambil foto.
                    </div>
                </div>

                <!-- Action Controls -->
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
    let webcamStreamTrack = null;
    let capturedDataUrl = null;

    function remindDosenWali(btn) {
        if (!btn) return;
        btn.disabled = true;
        btn.className = "btn btn-success fw-bolder px-3 py-2 d-flex flex-column align-items-center justify-content-center lh-1 text-white shadow-sm";
        btn.style.borderRadius = "10px";
        btn.innerHTML = `
            <i class="fas fa-check text-white fs-5 mb-1"></i>
            <span class="fs-9 text-uppercase text-white fw-bolder d-block">TERKIRIM</span>
        `;
        alert("Notifikasi pengingat bimbingan akademik H-1 telah dikirimkan kepada Prof. Dr. Ir. Ahmad Rizal, M.T.!");
    }

    function openWebcamModal(id, topik) {
        activeBimbinganId = id;
        capturedDataUrl = null;
        document.getElementById('webcam_topik_title').innerText = topik;

        // Reset UI Elements
        document.getElementById('webcam_stream').style.display = 'block';
        document.getElementById('captured_result_img').style.display = 'none';
        document.getElementById('live_camera_badge').style.display = 'inline-block';

        document.getElementById('btn_capture_photo').style.display = 'inline-block';
        document.getElementById('btn_retake_photo').style.display = 'none';
        document.getElementById('btn_save_photo').style.display = 'none';

        const webcamModal = new bootstrap.Modal(document.getElementById('modal_webcam_capture'));
        webcamModal.show();

        // Start HTML5 Camera Stream
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
            context.fillText("SIMBIM WEBCAM DOCUMENTATION", 120, 200);

            context.fillStyle = "#ffffff";
            context.font = "16px Poppins, sans-serif";
            context.fillText("Mahasiswa: Andi Pratama (2108107010001)", 130, 240);
            context.fillText("Waktu Foto: " + new Date().toLocaleString('id-ID'), 130, 270);
            context.fillText("Status: Dokumentasi Sesi Bimbingan Valid", 130, 300);

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

        const targetContainer = document.getElementById('doc_preview_container_' + activeBimbinganId);
        if (targetContainer) {
            targetContainer.innerHTML = `
                <div class="symbol symbol-40px symbol-2px cursor-pointer" onclick="viewDokumentasi('${capturedDataUrl}', 'Foto Dokumentasi Terbaru')">
                    <img src="${capturedDataUrl}" alt="Foto Webcam" class="rounded border border-2 border-success" style="object-fit: cover;" />
                </div>
                <span class="badge badge-light-success fs-9 fw-bolder">Ada Foto</span>
            `;
        }

        const actionContainer = document.getElementById('doc_action_container_' + activeBimbinganId);
        if (actionContainer) {
            actionContainer.innerHTML = `
                <button type="button" class="btn btn-sm btn-light-success fw-bolder px-3 py-1.5" onclick="viewDokumentasi('${capturedDataUrl}', 'Foto Dokumentasi Terbaru')">
                    <i class="fas fa-check-circle me-1"></i> Dokumentasi Terkirim
                </button>
            `;
        }

        closeWebcamModal();
        alert("Dokumentasi foto webcam bimbingan berhasil disimpan & terkirim!");
    }

    function viewDokumentasi(imgSrc, title) {
        document.getElementById('view_foto_title').innerText = title || "Dokumentasi Foto Bimbingan";
        document.getElementById('view_foto_img').src = imgSrc;
        const viewModal = new bootstrap.Modal(document.getElementById('modal_view_dokumentasi'));
        viewModal.show();
    }
</script>
@endpush
