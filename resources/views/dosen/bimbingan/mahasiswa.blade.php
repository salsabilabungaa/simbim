@extends('layouts.dosen')

@section('title', 'Mahasiswa Bimbingan — SIMBIM Dosen')
@section('page-title', 'Mahasiswa Bimbingan')

@section('content')
<!-- Header Banner & Action Bar -->
<div class="card mb-7 shadow-sm border-0" style="background: linear-gradient(135deg, #1e1e2d 0%, #151521 50%, #222736 100%);">
    <div class="card-body p-6 p-lg-8">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge badge-light-primary fw-bolder px-3 py-2 fs-8">
                        <i class="fas fa-users me-1 text-primary"></i> Data Mahasiswa
                    </span>
                    <span class="badge badge-light-success fw-bolder px-3 py-2 fs-8">
                        Semester Genap 2026/2027
                    </span>
                </div>
                <h1 class="text-white fw-bolder fs-2x mb-1">Daftar Mahasiswa Bimbingan</h1>
                <p class="text-gray-400 fs-6 fw-semibold mb-0">
                    Kelola dan pantau progres bimbingan tugas akhir skripsi serta perwalian akademik mahasiswa asuhan Anda.
                </p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <button type="button" class="btn btn-sm btn-light-primary fw-bolder px-4 py-3" onclick="exportDataMahasiswa()">
                    <i class="fas fa-file-export me-1"></i> Export Rekap Data
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 4 Kartu Statistik Ringkasan -->
<div class="row g-5 g-xl-8 mb-7">
    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-body p-6 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-gray-600 fw-bolder fs-7 text-uppercase ls-1">TOTAL MAHASISWA</span>
                        <div class="symbol symbol-45px bg-light-primary">
                            <span class="symbol-label">
                                <i class="fas fa-user-graduate text-primary fs-2"></i>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline mb-2">
                        <span class="fs-2qx fw-bolder text-dark me-2">{{ $statistik['total_mahasiswa'] }}</span>
                        <span class="fs-7 text-muted fw-bold">Mahasiswa Aktif</span>
                    </div>
                </div>
                <span class="badge badge-light-primary fw-bolder fs-8 align-self-start px-2 py-1">
                    <i class="fas fa-check-double me-1 text-primary"></i> Semua Kategori Bimbingan
                </span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-body p-6 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-gray-600 fw-bolder fs-7 text-uppercase ls-1">BIMBINGAN SKRIPSI</span>
                        <div class="symbol symbol-45px bg-light-warning">
                            <span class="symbol-label">
                                <i class="fas fa-book-reader text-warning fs-2"></i>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline mb-2">
                        <span class="fs-2qx fw-bolder text-dark me-2">{{ $statistik['bimbingan_skripsi'] }}</span>
                        <span class="fs-7 text-muted fw-bold">Mahasiswa TA</span>
                    </div>
                </div>
                <span class="badge badge-light-warning fw-bolder fs-8 align-self-start px-2 py-1">
                    <i class="fas fa-edit me-1 text-warning"></i> Sedang Menyusun Draf
                </span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-body p-6 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-gray-600 fw-bolder fs-7 text-uppercase ls-1">PERWALIAN AKADEMIK</span>
                        <div class="symbol symbol-45px bg-light-info">
                            <span class="symbol-label">
                                <i class="fas fa-user-friends text-info fs-2"></i>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline mb-2">
                        <span class="fs-2qx fw-bolder text-dark me-2">{{ $statistik['perwalian_akademik'] }}</span>
                        <span class="fs-7 text-muted fw-bold">Anak Wali</span>
                    </div>
                </div>
                <span class="badge badge-light-info fw-bolder fs-8 align-self-start px-2 py-1">
                    <i class="fas fa-clipboard-check me-1 text-info"></i> Konsultasi DPA & KRS
                </span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-body p-6 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-gray-600 fw-bolder fs-7 text-uppercase ls-1">SIAP SIDANG SKRIPSI</span>
                        <div class="symbol symbol-45px bg-light-success">
                            <span class="symbol-label">
                                <i class="fas fa-award text-success fs-2"></i>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline mb-2">
                        <span class="fs-2qx fw-bolder text-dark me-2">{{ $statistik['siap_sidang'] }}</span>
                        <span class="fs-7 text-muted fw-bold">ACC Lengkap</span>
                    </div>
                </div>
                <span class="badge badge-light-success fw-bolder fs-8 align-self-start px-2 py-1">
                    <i class="fas fa-check-circle me-1 text-success"></i> Menunggu Jadwal Sidang
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Filter, Pencarian, & Tabel Mahasiswa Bimbingan -->
<div class="card card-custom border border-gray-200 shadow-sm mb-7">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                <span class="svg-icon svg-icon-1 position-absolute ms-4">
                    <i class="fas fa-search text-gray-500"></i>
                </span>
                <input type="text" id="search_mahasiswa" class="form-control form-control-solid w-250px w-md-350px ps-12" placeholder="Cari nama, NIM, atau topik..." />
            </div>
        </div>
        <div class="card-toolbar">
            <div class="d-flex justify-content-end align-items-center gap-2 flex-wrap">
                <div class="btn-group" role="group" aria-label="Filter Kategori">
                    <button type="button" class="btn btn-sm btn-outline-primary active filter-btn" data-filter="all">Semua ({{ count($daftar_mahasiswa) }})</button>
                    <button type="button" class="btn btn-sm btn-outline-primary filter-btn" data-filter="skripsi">Bimbingan Skripsi</button>
                    <button type="button" class="btn btn-sm btn-outline-primary filter-btn" data-filter="akademik">Perwalian Akademik</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="table_mahasiswa">
                <thead>
                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0 border-bottom border-gray-300">
                        <th class="min-w-200px">Mahasiswa</th>
                        <th class="min-w-140px">Jenis & Peran</th>
                        <th class="min-w-250px">Topik / Judul Skripsi</th>
                        <th class="min-w-150px">Progres & Bab</th>
                        <th class="min-w-140px">Status Draf</th>
                        <th class="min-w-100px text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 fw-semibold">
                    @forelse ($daftar_mahasiswa as $mhs)
                        <tr class="mahasiswa-row" data-jenis="{{ $mhs['jenis_bimbingan'] }}" data-search="{{ strtolower($mhs['nama'] . ' ' . $mhs['nim'] . ' ' . $mhs['judul'] . ' ' . $mhs['bab_aktif']) }}">
                            <!-- Kolom Mahasiswa -->
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-45px symbol-circle me-3 bg-light-{{ $mhs['jenis_bimbingan'] === 'skripsi' ? 'primary' : 'info' }}">
                                        <span class="symbol-label text-{{ $mhs['jenis_bimbingan'] === 'skripsi' ? 'primary' : 'info' }} fw-bolder fs-5">
                                            {{ strtoupper(substr($mhs['nama'], 0, 2)) }}
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark fw-bolder text-hover-primary fs-6">{{ $mhs['nama'] }}</span>
                                        <span class="text-muted fs-7">NIM: <span class="fw-bold text-gray-700">{{ $mhs['nim'] }}</span> &bull; Angkatan {{ $mhs['angkatan'] }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Kolom Jenis & Peran -->
                            <td>
                                <span class="badge badge-light-{{ $mhs['jenis_bimbingan'] === 'skripsi' ? 'primary' : 'info' }} fw-bolder fs-8 mb-1 d-inline-block">
                                    {{ $mhs['kategori_label'] }}
                                </span>
                                <div class="text-muted fs-8 fw-bold">{{ $mhs['peran_dosen'] }}</div>
                            </td>

                            <!-- Kolom Topik / Judul Skripsi -->
                            <td>
                                <div class="text-dark fw-bold fs-7 mb-1" style="max-width: 320px; line-height: 1.4;">
                                    {{ $mhs['judul'] }}
                                </div>
                                <div class="text-muted fs-8">
                                    <i class="far fa-clock me-1"></i> Update terakhir: {{ $mhs['tanggal_update'] }} &bull; Sesi: <span class="fw-bold text-gray-800">{{ $mhs['jumlah_sesi'] }}x</span>
                                </div>
                            </td>

                            <!-- Kolom Progres & Bab -->
                            <td>
                                <div class="d-flex flex-column w-100">
                                    <div class="d-flex justify-content-between w-100 mb-1">
                                        <span class="text-gray-700 fs-8 fw-bolder">{{ $mhs['bab_aktif'] }}</span>
                                        <span class="text-muted fs-8 fw-bold">{{ $mhs['progres'] }}%</span>
                                    </div>
                                    <div class="progress h-6px w-100 bg-light">
                                        <div class="progress-bar bg-{{ $mhs['badge_status'] }}" role="progressbar" style="width: {{ $mhs['progres'] }}%" aria-valuenow="{{ $mhs['progres'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </td>

                            <!-- Kolom Status Draf -->
                            <td>
                                <span class="badge badge-light-{{ $mhs['badge_status'] }} fw-bolder fs-8 px-3 py-2">
                                    {{ $mhs['status_draf'] }}
                                </span>
                            </td>

                            <!-- Kolom Aksi -->
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <button type="button" class="btn btn-icon btn-light-primary btn-sm" title="Lihat Detail & Catatan" onclick="showDetailMahasiswa({{ json_encode($mhs) }})">
                                        <i class="fas fa-eye fs-6"></i>
                                    </button>
                                    <button type="button" class="btn btn-icon btn-light-success btn-sm" title="Review Draf / Beri Catatan" onclick="reviewDrafMahasiswa({{ json_encode($mhs) }})">
                                        <i class="fas fa-pen-alt fs-6"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-muted">
                                <i class="fas fa-folder-open fs-2x mb-3 text-gray-400 d-block"></i>
                                Belum ada data mahasiswa bimbingan yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail Mahasiswa Bimbingan -->
<div class="modal fade" id="modal_detail_mahasiswa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content rounded">
            <div class="modal-header pb-0 border-0 justify-content-end">
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="fas fa-times fs-4"></i>
                </div>
            </div>
            <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-12">
                <div class="mb-6 text-center">
                    <div class="symbol symbol-65px symbol-circle bg-light-primary text-primary fw-bolder fs-2hx mb-3 mx-auto d-flex align-items-center justify-content-center" id="detail_avatar">
                        AP
                    </div>
                    <h2 class="fw-bolder mb-1 text-dark" id="detail_nama">Nama Mahasiswa</h2>
                    <div class="text-muted fw-bold fs-6" id="detail_nim">NIM: 2108107010001</div>
                </div>

                <div class="rounded border border-gray-200 p-5 mb-5 bg-light-secondary">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="text-muted fs-8 d-block text-uppercase fw-bolder">Program Studi</span>
                            <span class="fw-bolder text-gray-800 fs-7" id="detail_prodi">-</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted fs-8 d-block text-uppercase fw-bolder">Jenis Bimbingan</span>
                            <span class="badge badge-light-primary fw-bolder fs-8" id="detail_jenis">-</span>
                        </div>
                        <div class="col-12">
                            <span class="text-muted fs-8 d-block text-uppercase fw-bolder">Judul / Topik Bimbingan</span>
                            <span class="fw-bold text-gray-900 fs-7" id="detail_judul">-</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted fs-8 d-block text-uppercase fw-bolder">Bab Aktif</span>
                            <span class="fw-bolder text-primary fs-7" id="detail_bab">-</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted fs-8 d-block text-uppercase fw-bolder">Status Draf</span>
                            <span class="badge badge-light-warning fw-bolder fs-8" id="detail_status">-</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between p-4 rounded bg-light-primary mb-6">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file-pdf fs-2 text-danger me-3"></i>
                        <div>
                            <div class="fw-bolder text-gray-900 fs-7" id="detail_file">Berkas_Draf_Terbaru.pdf</div>
                            <span class="text-muted fs-8">Dokumen terunggah dari mahasiswa</span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary fw-bolder" onclick="Swal.fire({icon: 'success', title: 'Unduh Berkas', text: 'Mengunduh draf naskah skripsi mahasiswa...', timer: 1500, showConfirmButton: false})">
                        <i class="fas fa-download me-1"></i> Unduh
                    </button>
                </div>

                <div class="d-flex justify-content-end gap-3">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary fw-bolder" onclick="pindahKeReview()">
                        <i class="fas fa-pen-alt me-1"></i> Berikan Catatan Review
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form Review Draf Mahasiswa -->
<div class="modal fade" id="modal_review_draf" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content rounded">
            <div class="modal-header border-0 pb-0">
                <h3 class="fw-bolder text-dark mb-0">Review Draf & Catatan Bimbingan</h3>
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="fas fa-times fs-4"></i>
                </div>
            </div>
            <div class="modal-body px-8 py-6">
                <div class="alert alert-light-primary d-flex align-items-center p-4 mb-5 rounded">
                    <i class="fas fa-info-circle fs-2 text-primary me-3"></i>
                    <div>
                        <div class="fw-bolder text-gray-800" id="review_mhs_nama">-</div>
                        <div class="text-muted fs-8" id="review_mhs_sub">-</div>
                    </div>
                </div>

                <form id="form_review_draf" onsubmit="submitReviewDraf(event)">
                    <div class="mb-4">
                        <label class="form-label fw-bolder text-gray-700 fs-7 required">Keputusan Review</label>
                        <select class="form-select form-select-solid" id="review_status_select" required>
                            <option value="Draf Sedang Direview">Draf Sedang Direview</option>
                            <option value="Revisi Diperlukan">Revisi Diperlukan</option>
                            <option value="Bimbingan Terjadwal">Bimbingan Terjadwal</option>
                            <option value="Siap Sidang Skripsi">Disetujui / Siap Sidang (ACC)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bolder text-gray-700 fs-7 required">Catatan Perbaikan / Arahan Pembimbing</label>
                        <textarea class="form-control form-control-solid" id="review_catatan" rows="4" placeholder="Tuliskan catatan detail terkait bab yang direvisi, metodologi, referensi, atau arahan bimbingan..." required></textarea>
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-bolder text-gray-700 fs-7">Unggah Berkas Koreksi (Opsional)</label>
                        <input type="file" class="form-control form-control-solid" id="review_file" accept=".pdf,.doc,.docx" />
                        <span class="text-muted fs-8 mt-1 d-block">Format PDF, DOC, atau DOCX (Maks. 10MB)</span>
                    </div>

                    <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary fw-bolder">
                            <i class="fas fa-paper-plane me-1"></i> Simpan & Kirim Masukan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentMahasiswaSelected = null;

    // Filter Pencarian Mahasiswa
    document.getElementById('search_mahasiswa').addEventListener('input', function() {
        filterTable();
    });

    // Filter Tombol Kategori
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            filterTable();
        });
    });

    function filterTable() {
        const query = document.getElementById('search_mahasiswa').value.toLowerCase().trim();
        const activeFilterBtn = document.querySelector('.filter-btn.active');
        const filterType = activeFilterBtn ? activeFilterBtn.getAttribute('data-filter') : 'all';

        const rows = document.querySelectorAll('#table_mahasiswa tbody tr.mahasiswa-row');
        rows.forEach(row => {
            const rowJenis = row.getAttribute('data-jenis');
            const rowSearch = row.getAttribute('data-search');

            const matchesCategory = (filterType === 'all' || rowJenis === filterType);
            const matchesQuery = (!query || rowSearch.includes(query));

            if (matchesCategory && matchesQuery) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function showDetailMahasiswa(mhs) {
        currentMahasiswaSelected = mhs;
        document.getElementById('detail_avatar').innerText = mhs.nama.substring(0, 2).toUpperCase();
        document.getElementById('detail_nama').innerText = mhs.nama;
        document.getElementById('detail_nim').innerText = 'NIM: ' + mhs.nim + ' • Angkatan ' + mhs.angkatan;
        document.getElementById('detail_prodi').innerText = mhs.program_studi;
        document.getElementById('detail_jenis').innerText = mhs.kategori_label;
        document.getElementById('detail_judul').innerText = mhs.judul;
        document.getElementById('detail_bab').innerText = mhs.bab_aktif + ' (' + mhs.progres + '%)';
        document.getElementById('detail_status').innerText = mhs.status_draf;
        document.getElementById('detail_file').innerText = mhs.berkas_draf || 'Dokumen_Bimbingan.pdf';

        const modal = new bootstrap.Modal(document.getElementById('modal_detail_mahasiswa'));
        modal.show();
    }

    function reviewDrafMahasiswa(mhs) {
        currentMahasiswaSelected = mhs;
        document.getElementById('review_mhs_nama').innerText = mhs.nama + ' (' + mhs.nim + ')';
        document.getElementById('review_mhs_sub').innerText = mhs.kategori_label + ' • ' + mhs.bab_aktif;
        document.getElementById('review_catatan').value = '';

        const modal = new bootstrap.Modal(document.getElementById('modal_review_draf'));
        modal.show();
    }

    function pindahKeReview() {
        bootstrap.Modal.getInstance(document.getElementById('modal_detail_mahasiswa')).hide();
        setTimeout(() => {
            if (currentMahasiswaSelected) {
                reviewDrafMahasiswa(currentMahasiswaSelected);
            }
        }, 300);
    }

    function submitReviewDraf(e) {
        e.preventDefault();
        const status = document.getElementById('review_status_select').value;
        const modal = bootstrap.Modal.getInstance(document.getElementById('modal_review_draf'));
        modal.hide();

        Swal.fire({
            icon: 'success',
            title: 'Masukan Berhasil Dikirim',
            html: 'Catatan bimbingan dan perubahan status menjadi <b>' + status + '</b> telah diteruskan ke mahasiswa.',
            confirmButtonText: 'Selesai',
            customClass: { confirmButton: 'btn btn-primary' }
        });
    }

    function exportDataMahasiswa() {
        Swal.fire({
            icon: 'info',
            title: 'Export Rekap Mahasiswa',
            text: 'Mempersiapkan dokumen rekapitulasi progres bimbingan mahasiswa (Excel/PDF)...',
            timer: 1800,
            showConfirmButton: false
        });
    }
</script>
@endpush
