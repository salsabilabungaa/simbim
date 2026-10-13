<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>SIMBIM — Dashboard Mahasiswa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    
    <!-- Memanggil CSS Metronic dari folder public -->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
</head>
<body id="kt_body" class="bg-body">

    <div class="container my-10">
        <!-- Welcome Card -->
        <div class="card bg-dark mb-8 text-white">
            <div class="card-body p-9">
                <div class="d-flex align-items-center mb-3">
                    <span class="badge badge-light-primary me-2 px-3 py-2">Program Studi Informatika</span>
                    <span class="badge badge-light-info me-2 px-3 py-2">Semester 7</span>
                    <span class="badge badge-light-success px-3 py-2">Mahasiswa Aktif</span>
                </div>
                <h1 class="text-white fw-bold fs-2x mb-3">Selamat Pagi, Andi Pratama 👋</h1>
                <div class="fs-6 text-gray-400">
                    <span class="me-4">NIM: <strong class="text-white">2108107010001</strong></span>
                    <span class="me-4">Pembimbing: <strong class="text-info">Dr. Siti Rahmawati, M.Kom.</strong></span>
                    <span>Dosen Wali: <strong class="text-success">Prof. Dr. Ir. Ahmad Rizal, M.T.</strong></span>
                </div>
            </div>
        </div>

        <!-- Row Konten -->
        <div class="row g-5 g-xl-8">
            <!-- Progres Skripsi -->
            <div class="col-xl-6">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-5">
                            <h3 class="card-title fw-bolder text-dark">PROGRES SKRIPSI</h3>
                            <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#modal_ajukan_bimbingan">
                                + Ajukan Bimbingan
                            </button>
                        </div>
                        
                        <div class="d-flex align-items-center mb-3">
                            <span class="fs-2hx fw-bolder text-dark me-3">65%</span>
                            <span class="badge badge-light-success fw-bold fs-8 px-2 py-1">+7% bulan ini</span>
                        </div>

                        <div class="progress h-8px bg-light-primary mb-5">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>

                        <div class="fw-bold text-gray-800 fs-6 mb-1">📘 Bab III — Metodologi Penelitian</div>
                        <div class="text-muted fs-7 mb-4">Draft revisi ke-2 sedang dikaji oleh dosen pembimbing.</div>
                        <div class="d-flex justify-content-between fs-8 text-gray-500">
                            <span>Target Selesai: 25 Oktober 2026</span>
                            <span>65 dari 100%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rekapan Bimbingan -->
            <div class="col-xl-6">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-body">
                        <h3 class="card-title fw-bolder text-dark mb-5">REKAPAN BIMBINGAN BULAN INI</h3>
                        <div class="d-flex align-items-center mb-5">
                            <span class="fs-2hx fw-bolder text-dark me-3">6</span>
                            <span class="text-muted fs-7 fw-bold">Total Sesi Bulan Ini Terpenuhi</span>
                        </div>

                        <div class="bg-light-info rounded p-4 mb-3 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-info">Bimbingan Skripsi</span>
                            <span class="badge badge-info">4 Sesi Terlaksana</span>
                        </div>

                        <div class="bg-light-primary rounded p-4 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-primary">Bimbingan Akademik</span>
                            <span class="badge badge-primary">2 Sesi Terlaksana</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pop-up Ajukan Bimbingan -->
    <div class="modal fade" id="modal_ajukan_bimbingan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bolder">Form Pengajuan Bimbingan Baru</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="#" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body py-10 px-lg-17">
                        <div class="mb-5">
                            <label class="required fs-6 fw-bold mb-2">Judul / Topik Bimbingan</label>
                            <input type="text" class="form-control form-control-solid" name="judul" placeholder="Contoh: Revisi Bab 3 Metodologi Penelitian" required />
                        </div>
                        <div class="mb-5">
                            <label class="required fs-6 fw-bold mb-2">Catatan Mahasiswa</label>
                            <textarea class="form-control form-control-solid" rows="3" name="catatan" placeholder="Tuliskan poin-poin yang ingin didiskusikan..."></textarea>
                        </div>
                        <div class="mb-5">
                            <label class="required fs-6 fw-bold mb-2">Upload File Draft (PDF/DOCX)</label>
                            <input type="file" class="form-control form-control-solid" name="file_draft" accept=".pdf,.docx" required />
                        </div>
                    </div>
                    <div class="modal-footer flex-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS Metronic -->
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
</body>
</html>