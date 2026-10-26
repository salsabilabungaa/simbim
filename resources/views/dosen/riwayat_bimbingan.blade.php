@extends('layouts.dosen')

@section('title', 'Riwayat Bimbingan Mahasiswa — SIMBIM Dosen')
@section('page-title', 'Riwayat Bimbingan')

@section('content')
<!-- 1. Header Banner & Action Bar -->
<div class="card mb-7 shadow-sm border-0" style="background: linear-gradient(135deg, #1e1e2d 0%, #151521 50%, #222736 100%);">
    <div class="card-body p-6 p-lg-8">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge badge-light-primary fw-bolder px-3 py-2 fs-8">
                        <i class="fas fa-history me-1 text-primary"></i> Arsip Logbook
                    </span>
                    <span class="badge badge-light-success fw-bolder px-3 py-2 fs-8">
                        Tahun Akademik 2026/2027
                    </span>
                </div>
                <h1 class="text-white fw-bolder fs-2x mb-1">Riwayat & Logbook Bimbingan</h1>
                <p class="text-gray-400 fs-6 fw-semibold mb-0">
                    Arsip rekam jejak pelaksanaan bimbingan skripsi dan konsultasi perwalian akademik mahasiswa secara terpadu.
                </p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <button type="button" class="btn btn-sm btn-primary fw-bolder px-4 py-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modal_cetak_logbook">
                    <i class="fas fa-print me-1"></i> Cetak / Ekspor Logbook
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 2. 4 Kartu Statistik Ringkasan Riwayat -->
<div class="row g-5 g-xl-8 mb-7">
    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-body p-6 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-gray-600 fw-bolder fs-7 text-uppercase ls-1">TOTAL SESI TERLAKSANA</span>
                        <div class="symbol symbol-45px bg-light-primary">
                            <span class="symbol-label">
                                <i class="fas fa-clipboard-check text-primary fs-2"></i>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline mb-2">
                        <span class="fs-2qx fw-bolder text-dark me-2">{{ $statistik['total_sesi'] }}</span>
                        <span class="fs-7 text-muted fw-bold">Pertemuan</span>
                    </div>
                </div>
                <span class="badge badge-light-primary fw-bolder fs-8 align-self-start px-2 py-1">
                    <i class="fas fa-check-double me-1 text-primary"></i> Skripsi & Akademik
                </span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-body p-6 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-gray-600 fw-bolder fs-7 text-uppercase ls-1">SESI SKRIPSI</span>
                        <div class="symbol symbol-45px bg-light-info">
                            <span class="symbol-label">
                                <i class="fas fa-book-reader text-info fs-2"></i>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline mb-2">
                        <span class="fs-2qx fw-bolder text-info me-2">{{ $statistik['sesi_skripsi'] }}</span>
                        <span class="fs-7 text-muted fw-bold">Sesi Konsultasi TA</span>
                    </div>
                </div>
                <span class="badge badge-light-info fw-bolder fs-8 align-self-start px-2 py-1">
                    <i class="fas fa-file-signature me-1 text-info"></i> Bab I s.d Bab V
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
                        <div class="symbol symbol-45px bg-light-success">
                            <span class="symbol-label">
                                <i class="fas fa-user-friends text-success fs-2"></i>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline mb-2">
                        <span class="fs-2qx fw-bolder text-success me-2">{{ $statistik['sesi_akademik'] }}</span>
                        <span class="fs-7 text-muted fw-bold">Sesi DPA</span>
                    </div>
                </div>
                <span class="badge badge-light-success fw-bolder fs-8 align-self-start px-2 py-1">
                    <i class="fas fa-user-graduate me-1 text-success"></i> KRS & Evaluasi IPK
                </span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-body p-6 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-gray-600 fw-bolder fs-7 text-uppercase ls-1">ACC SIDANG SKRIPSI</span>
                        <div class="symbol symbol-45px bg-light-warning">
                            <span class="symbol-label">
                                <i class="fas fa-award text-warning fs-2"></i>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline mb-2">
                        <span class="fs-2qx fw-bolder text-warning me-2">{{ $statistik['mahasiswa_acc'] }}</span>
                        <span class="fs-7 text-muted fw-bold">Mahasiswa Lulus TA</span>
                    </div>
                </div>
                <span class="badge badge-light-warning fw-bolder fs-8 align-self-start px-2 py-1">
                    <i class="fas fa-check-circle me-1 text-warning"></i> Naskah Telah Lengkap
                </span>
            </div>
        </div>
    </div>
</div>

<!-- 3. Filter & Pencarian Riwayat -->
<div class="card card-custom border border-gray-200 shadow-sm mb-7">
    <div class="card-body p-5">
        <div class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-solid">
                    <span class="input-group-text bg-light border-0"><i class="fas fa-search text-gray-500"></i></span>
                    <input type="text" id="filter_search_riwayat" class="form-control bg-light border-0 fs-7" placeholder="Cari nama mahasiswa, NIM, topik, atau catatan..." onkeyup="filterTabelLogbook()" />
                </div>
            </div>
            <div class="col-md-3">
                <select id="filter_kategori_riwayat" class="form-select form-select-solid bg-light border-0 fs-7" onchange="filterTabelLogbook()">
                    <option value="">Semua Kategori (Skripsi & Akademik)</option>
                    <option value="skripsi">Bimbingan Skripsi</option>
                    <option value="akademik">Perwalian Akademik</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="filter_status_riwayat" class="form-select form-select-solid bg-light border-0 fs-7" onchange="filterTabelLogbook()">
                    <option value="">Semua Status Sesi</option>
                    <option value="ACC">ACC</option>
                    <option value="Selesai">Selesai</option>
                    <option value="Revisi">Revisi</option>
                </select>
            </div>
            <div class="col-md-2 text-end">
                <button type="button" class="btn btn-light btn-active-light-primary fw-bold fs-7 w-100" onclick="resetFilterLogbook()">
                    <i class="fas fa-undo me-1"></i> Reset
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 4. Tabel Daftar Riwayat Sesi Bimbingan -->
<div class="card card-custom border border-gray-200 shadow-sm mb-7">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <h3 class="card-label fw-bolder fs-4 text-dark mb-0">
                Logbook Sesi Bimbingan
                <span class="text-muted fs-7 fw-normal ms-2 d-block d-sm-inline mt-1 mt-sm-0">Arsip kronologis bimbingan yang telah divalidasi</span>
            </h3>
        </div>
        <div class="card-toolbar">
            <span class="badge badge-light-primary fw-bolder fs-7" id="counter_logbook">Menampilkan {{ count($riwayat) }} Sesi</span>
        </div>
    </div>

    <div class="card-body pt-2">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-7 gy-4" id="tabel_riwayat_dosen">
                <thead>
                    <tr class="text-start text-muted fw-bolder fs-8 text-uppercase ls-1 bg-light">
                        <th class="ps-4 min-w-40px rounded-start">NO</th>
                        <th class="min-w-130px">TANGGAL & WAKTU</th>
                        <th class="min-w-180px">MAHASISWA</th>
                        <th class="min-w-140px">JENIS & PERAN</th>
                        <th class="min-w-220px">BAB & TOPIK</th>
                        <th class="min-w-220px">ARAHAN / CATATAN DOSEN</th>
                        <th class="min-w-100px text-center">BUKTI & BERKAS</th>
                        <th class="min-w-90px text-center">STATUS</th>
                        <th class="text-end pe-4 min-w-90px rounded-end">AKSI</th>
                    </tr>
                </thead>
                <tbody class="text-gray-800 fw-semibold">
                    @forelse ($riwayat as $index => $item)
                        <tr class="row-logbook" data-kategori="{{ $item['kategori_key'] }}" data-status="{{ $item['status'] }}" data-search="{{ strtolower($item['mahasiswa'] . ' ' . $item['nim'] . ' ' . $item['topik'] . ' ' . $item['catatan'] . ' ' . $item['bab']) }}">
                            <td class="ps-4 fw-bolder text-gray-500">{{ $index + 1 }}</td>
                            
                            <!-- Tanggal & Waktu -->
                            <td>
                                <div class="fw-bolder text-dark fs-7">{{ $item['tanggal'] }}</div>
                                <div class="text-muted fs-8"><i class="far fa-clock me-1 text-primary"></i>{{ $item['waktu'] }}</div>
                                <span class="badge badge-light fs-9 text-muted mt-1">{{ $item['metode'] }}</span>
                            </td>

                            <!-- Mahasiswa -->
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-35px symbol-circle me-3 bg-light-{{ $item['badge_kategori'] }}">
                                        <span class="symbol-label fw-bolder text-{{ $item['badge_kategori'] }} fs-7">
                                            {{ strtoupper(substr($item['mahasiswa'], 0, 2)) }}
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark fw-bolder fs-7 text-hover-primary">{{ $item['mahasiswa'] }}</span>
                                        <span class="text-muted fs-8">NIM: <span class="fw-bold text-gray-700">{{ $item['nim'] }}</span> &bull; '{{ substr($item['angkatan'], -2) }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Jenis & Peran -->
                            <td>
                                <span class="badge badge-light-{{ $item['badge_kategori'] }} fw-bolder px-2.5 py-1 fs-8 mb-1 d-inline-block">
                                    {{ $item['kategori'] }}
                                </span>
                                <div class="text-muted fs-8 fw-bold">{{ $item['peran_dosen'] }}</div>
                            </td>

                            <!-- Bab & Topik -->
                            <td>
                                <div class="text-primary fw-bolder fs-8 mb-1">{{ $item['bab'] }}</div>
                                <div class="text-dark fw-bold fs-7" style="line-height: 1.35; max-width: 250px;">
                                    {{ $item['topik'] }}
                                </div>
                            </td>

                            <!-- Catatan Dosen -->
                            <td>
                                <div class="text-gray-700 fs-8 fst-italic p-2 rounded bg-light-secondary border border-gray-100" style="line-height: 1.4; max-width: 260px;">
                                    "{{ Str::limit($item['catatan'], 95) }}"
                                </div>
                            </td>

                            <!-- Bukti & Berkas -->
                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center gap-1">
                                    @if ($item['dokumen'])
                                        <button type="button" class="btn btn-icon btn-light-primary btn-sm" title="Draf: {{ $item['dokumen'] }}" onclick="Swal.fire({icon: 'info', title: 'Unduh Berkas', text: 'Mengunduh {{ $item['dokumen'] }}...', timer: 1500, showConfirmButton: false})">
                                            <i class="fas fa-file-pdf fs-6 text-danger"></i>
                                        </button>
                                    @endif
                                    @if ($item['foto'])
                                        <button type="button" class="btn btn-icon btn-light-success btn-sm" title="Foto Bukti Pertemuan" onclick="Swal.fire({title: 'Foto Dokumentasi Sesi', text: 'Mahasiswa: {{ $item['mahasiswa'] }} ({{ $item['tanggal'] }})', imageUrl: 'https://images.unsplash.com/photo-1531403009284-440f080d1e12?w=600&auto=format&fit=crop&q=80', imageWidth: 400, imageHeight: 250, imageAlt: 'Foto Sesi', confirmButtonText: 'Tutup', customClass: {confirmButton: 'btn btn-primary'}})">
                                            <i class="fas fa-camera fs-6 text-success"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="text-center">
                                <span class="badge badge-light-{{ $item['badge_status'] }} fw-bolder fs-8 px-2.5 py-1">
                                    @if($item['status'] === 'ACC')
                                        <i class="fas fa-check-double me-1 text-success"></i>
                                    @elseif($item['status'] === 'Selesai')
                                        <i class="fas fa-check me-1 text-info"></i>
                                    @else
                                        <i class="fas fa-pen me-1 text-warning"></i>
                                    @endif
                                    {{ $item['status'] }}
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="text-end pe-4">
                                <button type="button" class="btn btn-icon btn-light-primary btn-sm" title="Lihat Detail Logbook" onclick="showDetailLogbook({{ json_encode($item) }})">
                                    <i class="fas fa-eye fs-6"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-10 text-muted">
                                <i class="fas fa-history fs-2x mb-3 text-gray-400 d-block"></i>
                                Belum ada arsip riwayat bimbingan yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail Logbook Sesi -->
<div class="modal fade" id="modal_detail_logbook" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content rounded">
            <div class="modal-header border-0 pb-0 justify-content-between">
                <h3 class="fw-bolder text-dark mb-0">Detail Sesi Bimbingan</h3>
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="fas fa-times fs-4"></i>
                </div>
            </div>
            <div class="modal-body px-8 py-6">
                <!-- Info Mahasiswa -->
                <div class="d-flex align-items-center p-4 rounded bg-light-primary mb-5">
                    <div class="symbol symbol-50px symbol-circle me-4 bg-white text-primary fw-bolder fs-3 d-flex align-items-center justify-content-center shadow-sm" id="modal_logbook_avatar">
                        AP
                    </div>
                    <div>
                        <h4 class="fw-bolder text-gray-900 mb-1" id="modal_logbook_nama">-</h4>
                        <div class="text-muted fs-8">
                            <span id="modal_logbook_nim">-</span> &bull; <span id="modal_logbook_prodi">PSTI</span> &bull; Angkatan <span id="modal_logbook_angkatan">-</span>
                        </div>
                    </div>
                </div>

                <div class="rounded border border-gray-200 p-5 mb-5 bg-light-secondary">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="text-muted fs-8 d-block text-uppercase fw-bolder">Tanggal & Jam</span>
                            <span class="fw-bolder text-gray-900 fs-7" id="modal_logbook_waktu">-</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted fs-8 d-block text-uppercase fw-bolder">Kategori Bimbingan</span>
                            <span class="badge badge-light-primary fw-bolder fs-8" id="modal_logbook_kategori">-</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted fs-8 d-block text-uppercase fw-bolder">Bab / Tahapan</span>
                            <span class="fw-bolder text-primary fs-7" id="modal_logbook_bab">-</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted fs-8 d-block text-uppercase fw-bolder">Status Keputusan</span>
                            <span class="badge badge-light-success fw-bolder fs-8" id="modal_logbook_status">-</span>
                        </div>
                        <div class="col-12">
                            <span class="text-muted fs-8 d-block text-uppercase fw-bolder">Topik Konsultasi</span>
                            <span class="fw-bold text-gray-900 fs-7" id="modal_logbook_topik">-</span>
                        </div>
                        <div class="col-12">
                            <span class="text-muted fs-8 d-block text-uppercase fw-bolder">Catatan & Arahan Dosen</span>
                            <p class="text-gray-800 fs-7 mb-0 p-3 rounded bg-white border border-gray-200" id="modal_logbook_catatan">-</p>
                        </div>
                    </div>
                </div>

                <!-- Dokumen Lampiran -->
                <div class="d-flex align-items-center justify-content-between p-3 rounded border border-gray-200 bg-white mb-6">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file-pdf fs-2 text-danger me-3"></i>
                        <div>
                            <div class="fw-bolder text-gray-900 fs-7" id="modal_logbook_file">Dokumen_Draf_Sesi.pdf</div>
                            <span class="text-muted fs-8">Berkas draf bimbingan terverifikasi</span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-light-primary fw-bolder" onclick="Swal.fire({icon: 'success', title: 'Unduh Berkas', text: 'Mengunduh dokumen draf bimbingan...', timer: 1500, showConfirmButton: false})">
                        <i class="fas fa-download me-1"></i> Unduh
                    </button>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cetak / Export Logbook -->
<div class="modal fade" id="modal_cetak_logbook" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-550px">
        <div class="modal-content rounded">
            <div class="modal-header border-0 pb-0 justify-content-between">
                <h3 class="fw-bolder text-dark mb-0">Cetak Rekap Logbook Bimbingan</h3>
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="fas fa-times fs-4"></i>
                </div>
            </div>
            <div class="modal-body px-8 py-6">
                <form id="form_cetak_logbook" onsubmit="submitCetakLogbook(event)">
                    <div class="mb-4">
                        <label class="form-label fw-bolder text-gray-700 fs-7 required">Pilih Mahasiswa</label>
                        <select class="form-select form-select-solid" id="cetak_pilih_mhs" required>
                            <option value="semua">Semua Mahasiswa Bimbingan (Rekap Lengkap Dosen)</option>
                            <option value="2108107010001">Andi Pratama (2108107010001) - Skripsi</option>
                            <option value="2108107010034">Cut Nadia Zahra (2108107010034) - Skripsi</option>
                            <option value="2008107010045">Rahmat Hidayat (2008107010045) - Skripsi</option>
                            <option value="2108107010088">Siti Aisyah Putri (2108107010088) - Skripsi</option>
                            <option value="2208107010012">Muhammad Fikri (2208107010012) - Akademik</option>
                        </select>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label class="form-label fw-bolder text-gray-700 fs-7 required">Tahun Akademik</label>
                            <select class="form-select form-select-solid" id="cetak_semester">
                                <option value="2026_genap" selected>Genap 2026/2027</option>
                                <option value="2026_ganjil">Ganjil 2026/2027</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-bolder text-gray-700 fs-7 required">Format Dokumen</label>
                            <select class="form-select form-select-solid" id="cetak_format">
                                <option value="pdf" selected>PDF Resmi (Siap Tanda Tangan)</option>
                                <option value="excel">Excel (.xlsx)</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary fw-bolder">
                            <i class="fas fa-file-export me-1"></i> Unduh & Cetak Logbook
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
    // Live Filter Logbook
    function filterTabelLogbook() {
        const query = document.getElementById('filter_search_riwayat').value.toLowerCase().trim();
        const kategori = document.getElementById('filter_kategori_riwayat').value.toLowerCase();
        const status = document.getElementById('filter_status_riwayat').value;

        const rows = document.querySelectorAll('#tabel_riwayat_dosen tbody tr.row-logbook');
        let visibleCount = 0;

        rows.forEach(row => {
            const rowKategori = row.getAttribute('data-kategori');
            const rowStatus = row.getAttribute('data-status');
            const rowSearch = row.getAttribute('data-search');

            const matchesKategori = (!kategori || rowKategori === kategori);
            const matchesStatus = (!status || rowStatus === status);
            const matchesQuery = (!query || rowSearch.includes(query));

            if (matchesKategori && matchesStatus && matchesQuery) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('counter_logbook').innerText = 'Menampilkan ' + visibleCount + ' Sesi';
    }

    function resetFilterLogbook() {
        document.getElementById('filter_search_riwayat').value = '';
        document.getElementById('filter_kategori_riwayat').value = '';
        document.getElementById('filter_status_riwayat').value = '';
        filterTabelLogbook();
    }

    function showDetailLogbook(item) {
        document.getElementById('modal_logbook_avatar').innerText = item.mahasiswa.substring(0, 2).toUpperCase();
        document.getElementById('modal_logbook_nama').innerText = item.mahasiswa;
        document.getElementById('modal_logbook_nim').innerText = 'NIM: ' + item.nim;
        document.getElementById('modal_logbook_prodi').innerText = item.prodi;
        document.getElementById('modal_logbook_angkatan').innerText = item.angkatan;
        document.getElementById('modal_logbook_waktu').innerText = item.tanggal + ' (' + item.waktu + ')';
        document.getElementById('modal_logbook_kategori').innerText = item.kategori + ' - ' + item.peran_dosen;
        document.getElementById('modal_logbook_bab').innerText = item.bab;
        document.getElementById('modal_logbook_status').innerText = item.status;
        document.getElementById('modal_logbook_topik').innerText = item.topik;
        document.getElementById('modal_logbook_catatan').innerText = item.catatan;
        document.getElementById('modal_logbook_file').innerText = item.dokumen || 'Naskah_Bimbingan.pdf';

        const modal = new bootstrap.Modal(document.getElementById('modal_detail_logbook'));
        modal.show();
    }

    function submitCetakLogbook(e) {
        e.preventDefault();
        const format = document.getElementById('cetak_format').value.toUpperCase();
        bootstrap.Modal.getInstance(document.getElementById('modal_cetak_logbook')).hide();

        Swal.fire({
            icon: 'success',
            title: 'Logbook Sedang Diproses',
            text: 'Dokumen rekapitulasi riwayat bimbingan berformat ' + format + ' sedang di-generate dan akan segera diunduh.',
            confirmButtonText: 'Selesai',
            customClass: { confirmButton: 'btn btn-primary' }
        });
    }
</script>
@endpush
