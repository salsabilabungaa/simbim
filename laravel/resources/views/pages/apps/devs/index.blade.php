<x-base-layout>
    <!--begin::Content Wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid py-3 py-lg-6">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-fluid">

                <!--begin::Welcome Card-->
                <div class="card bg-dark mb-5 mb-xl-10 text-white">
                    <div class="card-body p-9">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge badge-light-primary me-2 px-3 py-2">Program Studi Informatika</span>
                            <span class="badge badge-light-purple me-2 px-3 py-2">Semester 7</span>
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
                <!--end::Welcome Card-->

                <!--begin::Row-->
                <div class="row g-5 g-xl-8">
                    <!--begin::Col Progres-->
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
                    <!--end::Col Progres-->

                    <!--begin::Col Rekapan-->
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
                    <!--end::Col Rekapan-->
                </div>
                <!--end::Row-->

            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content Wrapper-->

    <!--begin::Modal Ajukan Bimbingan (Interaktif Pop-up)-->
    <div class="modal fade" id="modal_ajukan_bimbingan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bolder">Form Pengajuan Bimbingan Baru</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">X</div>
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
    <!--end::Modal Ajukan Bimbingan-->
</x-base-layout>