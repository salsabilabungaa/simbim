<!-- Modal Ekspor Laporan Akreditasi Prodi PSTI UPI -->
<div class="modal fade" id="modal_ekspor_akreditasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-550px">
        <div class="modal-content rounded">
            <div class="modal-header border-0 pb-0 justify-content-between">
                <div>
                    <h3 class="fw-bolder text-dark mb-1">Ekspor Laporan Akreditasi GKM</h3>
                    <span class="text-muted fs-8">Prodi {{ config('simbim.prodi') }} &mdash; {{ config('simbim.universitas') }}</span>
                </div>
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="fas fa-times fs-4"></i>
                </div>
            </div>
            <div class="modal-body px-8 py-6">
                <form id="form_ekspor_akreditasi" method="GET" action="{{ route('gkm.ekspor.pdf') }}">
                    <div class="mb-5">
                        <label class="form-label fw-bolder text-gray-700 fs-7">Program Studi &amp; Universitas</label>
                        <input type="text" class="form-control form-control-solid fw-bold text-gray-800" value="{{ config('simbim.prodi') }} — {{ config('simbim.universitas') }}" readonly />
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-bolder text-gray-700 fs-7">Cakupan Angkatan Mahasiswa</label>
                        <select name="angkatan" id="ekspor_angkatan" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#modal_ekspor_akreditasi" data-placeholder="Semua Angkatan Prodi PSTI UPI">
                            <option value="">Semua Angkatan Prodi PSTI UPI</option>
                            @foreach ($daftarAngkatan as $angkatanItem)
                                <option value="{{ $angkatanItem }}" {{ ($defaultAngkatan ?? '') === $angkatanItem ? 'selected' : '' }}>
                                    Angkatan {{ $angkatanItem }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text fs-8 text-muted">
                            Biarkan pilihan &ldquo;Semua Angkatan&rdquo; untuk mengunduh laporan lengkap seluruh mahasiswa PSTI UPI.
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-bolder text-gray-700 fs-7">Periode Tahun Akademik</label>
                        <select name="tahun_akademik" id="ekspor_tahun_akademik" class="form-select form-select-solid" data-control="select2" data-dropdown-parent="#modal_ekspor_akreditasi" data-placeholder="Semua Periode Akademik">
                            <option value="">Semua Periode Akademik</option>
                            @foreach ($daftarTahunAkademik as $tahunItem)
                                <option value="{{ $tahunItem }}" {{ ($defaultTahun ?? '') === $tahunItem ? 'selected' : '' }}>
                                    {{ $tahunItem }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4 mb-6">
                        <i class="fas fa-file-alt fs-2 text-info me-3 mt-1"></i>
                        <div class="fs-8 text-gray-700">
                            Dokumen ekspor berisi 3 bagian terintegrasi: <strong>Rekapitulasi per Angkatan PSTI UPI</strong>, <strong>Daftar Status KRS/IRS Mahasiswa</strong>, serta <strong>Arsip Log Konsultasi View-Only</strong>.
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-end gap-3 pt-3 border-top">
                        <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit"
                                class="btn btn-success fw-bolder"
                                formaction="{{ route('gkm.ekspor.excel') }}">
                            <i class="fas fa-file-excel me-1"></i> Unduh Excel (.xlsx)
                        </button>
                        <button type="submit"
                                class="btn btn-danger fw-bolder"
                                formaction="{{ route('gkm.ekspor.pdf') }}">
                            <i class="fas fa-file-pdf me-1"></i> Unduh PDF (.pdf)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
