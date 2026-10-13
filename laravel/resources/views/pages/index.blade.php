<x-base-layout>
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_content" class="app-content flex-column-fluid py-3 py-lg-6">
            <div id="kt_app_content_container" class="app-container container-fluid">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4 mb-5 mb-xl-8">
                    <div>
                        <h1 class="text-gray-900 fw-bolder fs-2x mb-1">Dashboard Mahasiswa</h1>
                        <p class="text-muted fs-6 mb-0">Pantau progres skripsi dan aktivitas bimbingan Anda.</p>
                    </div>
                    <button type="button" class="btn btn-primary flex-shrink-0" data-bs-toggle="modal" data-bs-target="#modal_ajukan_bimbingan">
                        <i class="ki-duotone ki-plus fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Ajukan Bimbingan Baru
                    </button>
                </div>

                <section class="card bg-dark mb-5 mb-xl-8" aria-labelledby="dashboard-welcome-title">
                    <div class="card-body p-6 p-lg-9">
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <span class="badge badge-light-primary px-3 py-2">Program Studi Informatika</span>
                            <span class="badge badge-light-info px-3 py-2">Semester 7</span>
                            <span class="badge badge-light-success px-3 py-2">Mahasiswa Aktif</span>
                        </div>
                        <h2 id="dashboard-welcome-title" class="text-white fw-bold fs-2x mb-3">Selamat Pagi, Andi Pratama <span aria-hidden="true">👋</span></h2>
                        <div class="d-flex flex-column flex-lg-row flex-wrap gap-2 gap-lg-6 fs-6 text-gray-400">
                            <span>NIM: <strong class="text-white">2108107010001</strong></span>
                            <span>Pembimbing: <strong class="text-info">Dr. Siti Rahmawati, M.Kom.</strong></span>
                            <span>Dosen Wali: <strong class="text-success">Prof. Dr. Ir. Ahmad Rizal, M.T.</strong></span>
                        </div>
                    </div>
                </section>

                <div class="row g-5 g-xl-8">
                    <div class="col-12 col-xl-6">
                        <section class="card card-xl-stretch h-100" aria-labelledby="thesis-progress-title">
                            <div class="card-body p-6 p-lg-8">
                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-6">
                                    <h2 id="thesis-progress-title" class="card-title fw-bolder text-dark mb-0">Progres Skripsi</h2>
                                    <span class="badge badge-light-primary fs-8 px-3 py-2">Sedang Berjalan</span>
                                </div>

                                <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                                    <span class="fs-2hx fw-bolder text-dark">65%</span>
                                    <span class="badge badge-light-success fw-bold fs-8 px-2 py-1">+7% bulan ini</span>
                                </div>
                                <div class="progress h-8px bg-light-primary mb-5" role="progressbar" aria-label="Progres skripsi" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar bg-primary" style="width: 65%"></div>
                                </div>

                                <div class="d-flex align-items-start gap-3 mb-5">
                                    <span class="symbol symbol-40px flex-shrink-0">
                                        <span class="symbol-label bg-light-primary">
                                            <i class="ki-duotone ki-book-open fs-2 text-primary">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                    </span>
                                    <div>
                                        <h3 class="fw-bold text-gray-800 fs-6 mb-1">Bab III — Metodologi Penelitian</h3>
                                        <p class="text-muted fs-7 mb-0">Draft revisi ke-2 sedang dikaji oleh dosen pembimbing.</p>
                                    </div>
                                </div>

                                <div class="d-flex flex-column flex-sm-row justify-content-between gap-2 fs-8 text-gray-500">
                                    <span>Target selesai: 25 Oktober 2026</span>
                                    <span>65 dari 100%</span>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div class="col-12 col-xl-6">
                        <section class="card card-xl-stretch h-100" aria-labelledby="mentoring-summary-title">
                            <div class="card-body p-6 p-lg-8">
                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-6">
                                    <h2 id="mentoring-summary-title" class="card-title fw-bolder text-dark mb-0">Rekapan Bimbingan</h2>
                                    <span class="text-muted fs-7">Bulan ini</span>
                                </div>

                                <div class="d-flex align-items-center gap-3 mb-6">
                                    <span class="fs-2hx fw-bolder text-dark">6</span>
                                    <div>
                                        <div class="fw-bold text-gray-800">Sesi terlaksana</div>
                                        <div class="text-muted fs-7">Seluruh sesi bulan ini sudah tercatat.</div>
                                    </div>
                                </div>

                                <div class="d-flex flex-column gap-3">
                                    <div class="bg-light-info rounded p-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                                        <span class="fw-bold text-info">Bimbingan Skripsi</span>
                                        <span class="badge badge-info align-self-start align-self-sm-center">4 sesi terlaksana</span>
                                    </div>
                                    <div class="bg-light-primary rounded p-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                                        <span class="fw-bold text-primary">Bimbingan Akademik</span>
                                        <span class="badge badge-primary align-self-start align-self-sm-center">2 sesi terlaksana</span>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_ajukan_bimbingan" tabindex="-1" aria-labelledby="modal_ajukan_bimbingan_title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 id="modal_ajukan_bimbingan_title" class="fw-bolder mb-0">Form Pengajuan Bimbingan Baru</h2>
                    <button type="button" class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Tutup">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </button>
                </div>
                <form>
                    <div class="modal-body py-8 px-6 px-lg-10">
                        <div class="mb-5">
                            <label for="bimbingan_judul" class="required fs-6 fw-bold mb-2">Judul / Topik Bimbingan</label>
                            <input id="bimbingan_judul" type="text" class="form-control form-control-solid" name="judul" placeholder="Contoh: Revisi Bab 3 Metodologi Penelitian" required />
                        </div>
                        <div class="mb-5">
                            <label for="bimbingan_catatan" class="fs-6 fw-bold mb-2">Catatan Mahasiswa</label>
                            <textarea id="bimbingan_catatan" class="form-control form-control-solid" rows="3" name="catatan" placeholder="Tuliskan poin-poin yang ingin didiskusikan..."></textarea>
                        </div>
                        <div>
                            <label for="bimbingan_file" class="required fs-6 fw-bold mb-2">Upload File Draft (PDF/DOCX)</label>
                            <input id="bimbingan_file" type="file" class="form-control form-control-solid" name="file_draft" accept=".pdf,.docx" required />
                        </div>
                    </div>
                    <div class="modal-footer flex-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary">Kirim Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-base-layout>
