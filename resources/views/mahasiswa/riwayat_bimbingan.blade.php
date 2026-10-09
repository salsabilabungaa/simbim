@extends('layouts.mahasiswa')

@section('title', 'Riwayat Bimbingan Mahasiswa')

@section('content')
<div class="d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xxl">
            
            <!-- 1. Card Filter & Pencarian -->
            <div class="card card-custom bg-body border border-gray-200 shadow-sm mb-7">
                <div class="card-body p-5">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <div class="input-group input-group-solid">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-search text-gray-400"></i></span>
                                <input type="text" id="filter_search" class="form-control bg-light border-0 fs-7" placeholder="Cari topik bimbingan atau nama dosen..." onkeyup="filterTabelRiwayat()">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select id="filter_kategori" class="form-select form-select-solid bg-light border-0 fs-7" onchange="filterTabelRiwayat()">
                                <option value="">Semua Kategori (Skripsi & Akademik)</option>
                                <option value="Skripsi">Bimbingan Skripsi</option>
                                <option value="Akademik">Bimbingan Akademik</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="filter_status" class="form-select form-select-solid bg-light border-0 fs-7" onchange="filterTabelRiwayat()">
                                <option value="">Semua Status Bimbingan</option>
                                <option value="ACC">ACC</option>
                                <option value="Selesai">Selesai</option>
                                <option value="Revisi">Revisi</option>
                            </select>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="button" class="btn btn-light btn-active-light-primary fw-bold fs-7 w-100" onclick="resetFilterRiwayat()">
                                <i class="fas fa-undo me-1"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Tabel Daftar Riwayat Bimbingan -->
            <div class="card card-custom bg-body border border-gray-200 shadow-sm mb-7">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-4 text-dark">Daftar Sesi Bimbingan</span>
                        <span class="text-muted mt-1 fw-bold fs-7">Rekam jejak seluruh kegiatan bimbingan akademik dan tugas akhir</span>
                    </h3>
                    <div class="card-toolbar d-flex align-items-center gap-3">
                        <span class="badge badge-light-primary fw-bolder fs-7" id="counter_riwayat">Menampilkan {{ count($riwayat) }} Sesi</span>
                        <button type="button" class="btn btn-primary btn-sm fw-bolder px-3 py-2 fs-7" data-bs-toggle="modal" data-bs-target="#modalCetakLogbook">
                            <i class="fas fa-print me-1"></i> Cetak Logbook
                        </button>
                    </div>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-7 gy-4" id="tabel_riwayat_bimbingan">
                            <thead>
                                <tr class="text-start text-muted fw-bolder fs-8 text-uppercase ls-1 bg-light">
                                    <th class="ps-4 min-w-50px rounded-start">NO</th>
                                    <th class="min-w-125px">TANGGAL & WAKTU</th>
                                    <th class="min-w-100px">JENIS</th>
                                    <th class="min-w-200px">DOSEN PEMBIMBING / WALI</th>
                                    <th class="min-w-250px">TOPIK / CATATAN</th>
                                    <th class="min-w-130px text-center">DOKUMEN</th>
                                    <th class="min-w-110px text-center">FOTO SESI</th>
                                    <th class="min-w-90px text-center">STATUS</th>
                                    <th class="text-end pe-4 min-w-100px rounded-end">AKSI</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-800 fw-bold">
                                @forelse ($riwayat as $index => $item)
                                    <tr class="row-riwayat" data-kategori="{{ $item['kategori'] }}" data-status="{{ $item['status'] }}" data-search="{{ strtolower($item['topik'] . ' ' . $item['dosen'] . ' ' . $item['catatan']) }}">
                                        <td class="ps-4 fw-bolder text-gray-500">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="fw-bolder text-dark">{{ $item['tanggal'] }}</div>
                                            <div class="text-muted fs-8"><i class="far fa-clock me-1 text-primary"></i>{{ $item['waktu'] }}</div>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-{{ $item['badge_kategori'] }} fw-bolder px-2.5 py-1 fs-8">
                                                <i class="fas {{ $item['kategori'] == 'Skripsi' ? 'fa-book-reader' : 'fa-user-graduate' }} me-1 text-{{ $item['badge_kategori'] }}"></i>
                                                {{ $item['kategori'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-35px symbol-circle me-3 bg-light-primary">
                                                    <span class="symbol-label fw-bolder text-primary fs-7">
                                                        {{ strtoupper(substr($item['dosen'], 0, 2)) }}
                                                    </span>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark fw-bolder fs-7 hover-primary">{{ $item['dosen'] }}</span>
                                                    <span class="text-muted fs-8">{{ $item['role_dosen'] }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bolder text-dark mb-1 text-truncate max-w-250px" title="{{ $item['topik'] }}">
                                                {{ $item['topik'] }}
                                            </div>
                                            <div class="text-muted fs-8 text-truncate max-w-250px" title="{{ $item['catatan'] }}">
                                                <i class="fas fa-comment-dots me-1 text-gray-400"></i>{{ $item['catatan'] }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if ($item['dokumen'])
                                                <a href="#" class="btn btn-light-primary btn-sm px-2.5 py-1 fs-8 fw-bold" onclick="simulasiDownloadDokumen('{{ $item['dokumen'] }}'); return false;">
                                                    <i class="fas fa-file-pdf text-danger me-1"></i> {{ Str::limit($item['dokumen'], 15) }}
                                                </a>
                                            @else
                                                <span class="text-muted fs-8 fw-bold">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($item['foto'])
                                                <button type="button" class="btn btn-light-success btn-sm px-2.5 py-1 fs-8 fw-bold" onclick="previewFotoRiwayat('{{ asset($item['foto']) }}', '{{ $item['topik'] }}')">
                                                    <i class="fas fa-camera text-success me-1"></i> Lihat Foto
                                                </button>
                                            @else
                                                <span class="badge badge-light text-muted fs-8 fw-bold px-2 py-1">Tidak ada</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-light-{{ $item['badge_status'] }} fw-bolder px-2.5 py-1 fs-8">
                                                {{ $item['status'] }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <button type="button" class="btn btn-light btn-active-light-primary btn-sm fw-bolder px-3 py-1.5 fs-8" onclick="bukaModalDetailRiwayat({{ json_encode($item) }})">
                                                <i class="fas fa-info-circle me-1"></i> Detail
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-8 text-muted">Belum ada data riwayat bimbingan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- MODAL 1: Detail Bimbingan -->
<div class="modal fade" id="modalDetailRiwayat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-3">
            <div class="modal-header pb-0 border-0 justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge badge-light-primary fw-bolder px-3 py-1.5 fs-7" id="mdl_kategori">Skripsi</span>
                    <span class="badge badge-light-success fw-bolder px-3 py-1.5 fs-7" id="mdl_status">ACC</span>
                </div>
                <button type="button" class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="fas fa-times fs-4"></i>
                </button>
            </div>
            <div class="modal-body p-6">
                <h3 class="fw-bolder text-dark mb-4 fs-4" id="mdl_topik">Topik Bimbingan</h3>
                
                <div class="row g-4 mb-6">
                    <div class="col-md-6">
                        <div class="p-4 rounded bg-light border border-gray-200">
                            <span class="text-muted fs-8 fw-bold text-uppercase d-block mb-1">DOSEN PEMBIMBING / WALI</span>
                            <div class="fw-bolder text-dark fs-6" id="mdl_dosen">Dr. Ir. Teuku Muhammad, M.T.</div>
                            <div class="text-primary fs-8 fw-bold" id="mdl_role_dosen">Dosen Pembimbing Utama</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-4 rounded bg-light border border-gray-200">
                            <span class="text-muted fs-8 fw-bold text-uppercase d-block mb-1">WAKTU & LOKASI</span>
                            <div class="fw-bolder text-dark fs-6" id="mdl_waktu">11 Okt 2026 (09.30 WIB)</div>
                            <div class="text-muted fs-8 fw-bold" id="mdl_ruangan"><i class="fas fa-map-marker-alt me-1 text-warning"></i> Ruang Dosen 2.04</div>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h5 class="fw-bolder text-dark mb-2 fs-6"><i class="fas fa-comment-alt text-primary me-2"></i>Catatan & Masukan Dosen</h5>
                    <div class="p-4 rounded bg-light-primary border border-primary border-dashed text-dark fs-7 fw-bold" id="mdl_catatan">
                        Catatan akan ditampilkan di sini.
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <h5 class="fw-bolder text-dark mb-2 fs-6"><i class="fas fa-paperclip text-info me-2"></i>Dokumen Terlampir</h5>
                        <div id="mdl_dokumen_wrapper" class="p-3 rounded bg-light border border-gray-200 d-flex align-items-center justify-content-between">
                            <span class="text-muted fs-7">Tidak ada dokumen</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5 class="fw-bolder text-dark mb-2 fs-6"><i class="fas fa-camera text-success me-2"></i>Bukti Foto Sesi</h5>
                        <div id="mdl_foto_wrapper" class="p-3 rounded bg-light border border-gray-200 d-flex align-items-center justify-content-between">
                            <span class="text-muted fs-7">Tidak ada foto</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top p-4 d-flex justify-content-between">
                <button type="button" class="btn btn-light-primary fw-bold fs-7" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Cetak Lembar Ini
                </button>
                <button type="button" class="btn btn-secondary fw-bold fs-7 px-5" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL 2: Preview Foto -->
<div class="modal fade" id="modalPreviewFotoRiwayat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3">
            <div class="modal-header p-4">
                <h5 class="modal-title fw-bolder fs-6" id="mdl_preview_judul">Foto Dokumentasi Sesi</h5>
                <button type="button" class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="fas fa-times fs-4"></i>
                </button>
            </div>
            <div class="modal-body text-center p-4">
                <img id="mdl_preview_img" src="" alt="Dokumentasi Bimbingan" class="img-fluid rounded border shadow-sm max-h-350px">
            </div>
        </div>
    </div>
</div>

<!-- MODAL 3: Cetak Logbook Bimbingan -->
<div class="modal fade" id="modalCetakLogbook" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-3">
            <div class="modal-header border-0 pb-0 justify-content-between align-items-center">
                <h3 class="fw-bolder text-dark mb-0 fs-4"><i class="fas fa-file-invoice text-primary me-2"></i>Pratinjau Logbook Bimbingan</h3>
                <button type="button" class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="fas fa-times fs-4"></i>
                </button>
            </div>
            <div class="modal-body p-6" id="printable_logbook">
                <div class="text-center pb-4 mb-4 border-bottom border-gray-300">
                    <h2 class="fw-bolder text-dark mb-1">KARTU / LOGBOOK BIMBINGAN MAHASISWA</h2>
                    <p class="text-muted fs-7 mb-0">UNIVERSITAS SYIAH KUALA — FAKULTAS ILMU KOMPUTER</p>
                </div>

                <div class="row mb-5 fs-7">
                    <div class="col-6">
                        <table class="table table-borderless table-sm">
                            <tr><td class="fw-bold text-muted w-100px">Nama</td><td class="fw-bolder text-dark">: {{ $mahasiswa['nama'] }}</td></tr>
                            <tr><td class="fw-bold text-muted">NIM</td><td class="fw-bolder text-dark">: {{ $mahasiswa['nim'] }}</td></tr>
                            <tr><td class="fw-bold text-muted">Prodi</td><td class="fw-bolder text-dark">: {{ $mahasiswa['program_studi'] }}</td></tr>
                        </table>
                    </div>
                    <div class="col-6">
                        <table class="table table-borderless table-sm">
                            <tr><td class="fw-bold text-muted w-120px">Total Sesi</td><td class="fw-bolder text-dark">: {{ count($riwayat) }} Sesi Terdaftar</td></tr>
                            <tr><td class="fw-bold text-muted">Tanggal Cetak</td><td class="fw-bolder text-dark">: {{ date('d F Y') }}</td></tr>
                            <tr><td class="fw-bold text-muted">Status Logbook</td><td class="fw-bolder text-success">: Terverifikasi Sistem</td></tr>
                        </table>
                    </div>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle fs-8">
                        <thead class="bg-light text-uppercase fw-bolder">
                            <tr>
                                <th class="text-center w-40px">No</th>
                                <th class="w-100px">Tanggal</th>
                                <th class="w-90px">Jenis</th>
                                <th>Dosen Pembimbing/Wali</th>
                                <th>Topik & Catatan Masukan</th>
                                <th class="text-center w-80px">Status</th>
                            </tr>
                        </thead>
                        <tbody class="fw-bold">
                            @foreach ($riwayat as $idx => $r)
                                <tr>
                                    <td class="text-center">{{ $idx + 1 }}</td>
                                    <td>{{ $r['tanggal'] }}</td>
                                    <td>{{ $r['kategori'] }}</td>
                                    <td>{{ $r['dosen'] }}</td>
                                    <td>
                                        <div class="fw-bolder">{{ $r['topik'] }}</div>
                                        <div class="text-muted fs-9">{{ $r['catatan'] }}</div>
                                    </td>
                                    <td class="text-center"><span class="badge badge-light-{{ $r['badge_status'] }}">{{ $r['status'] }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row pt-4 text-center fs-8 fw-bold">
                    <div class="col-6">
                        <p class="mb-5">Mengetahui,<br>Dosen Pembimbing Utama</p>
                        <br><br>
                        <u><b>Dr. Ir. Teuku Muhammad, M.T.</b></u><br>
                        NIP. 19810314 200604 1 003
                    </div>
                    <div class="col-6">
                        <p class="mb-5">Banda Aceh, {{ date('d F Y') }}<br>Mahasiswa</p>
                        <br><br>
                        <u><b>{{ $mahasiswa['nama'] }}</b></u><br>
                        NIM. {{ $mahasiswa['nim'] }}
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top p-4">
                <button type="button" class="btn btn-primary fw-bolder fs-7 px-6" onclick="cetakLogbookDirect()">
                    <i class="fas fa-print me-2"></i> Cetak / Simpan PDF
                </button>
                <button type="button" class="btn btn-secondary fw-bold fs-7 px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function filterTabelRiwayat() {
        const query = document.getElementById('filter_search').value.toLowerCase();
        const kategori = document.getElementById('filter_kategori').value;
        const status = document.getElementById('filter_status').value;

        const rows = document.querySelectorAll('.row-riwayat');
        let count = 0;

        rows.forEach(row => {
            const rowSearch = row.getAttribute('data-search');
            const rowKategori = row.getAttribute('data-kategori');
            const rowStatus = row.getAttribute('data-status');

            const matchSearch = rowSearch.includes(query);
            const matchKategori = !kategori || rowKategori === kategori;
            const matchStatus = !status || rowStatus === status;

            if (matchSearch && matchKategori && matchStatus) {
                row.style.display = '';
                count++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('counter_riwayat').innerText = `Menampilkan ${count} Sesi`;
    }

    function resetFilterRiwayat() {
        document.getElementById('filter_search').value = '';
        document.getElementById('filter_kategori').value = '';
        document.getElementById('filter_status').value = '';
        filterTabelRiwayat();
    }

    function previewFotoRiwayat(src, judul) {
        document.getElementById('mdl_preview_img').src = src;
        document.getElementById('mdl_preview_judul').innerText = 'Foto Dokumentasi: ' + judul;
        var myModal = new bootstrap.Modal(document.getElementById('modalPreviewFotoRiwayat'));
        myModal.show();
    }

    function bukaModalDetailRiwayat(data) {
        document.getElementById('mdl_kategori').className = `badge badge-light-${data.badge_kategori} fw-bolder px-3 py-1.5 fs-7`;
        document.getElementById('mdl_kategori').innerText = data.kategori;

        document.getElementById('mdl_status').className = `badge badge-light-${data.badge_status} fw-bolder px-3 py-1.5 fs-7`;
        document.getElementById('mdl_status').innerText = data.status;

        document.getElementById('mdl_topik').innerText = data.topik;
        document.getElementById('mdl_dosen').innerText = data.dosen;
        document.getElementById('mdl_role_dosen').innerText = data.role_dosen;
        document.getElementById('mdl_waktu').innerText = `${data.tanggal} (${data.waktu})`;
        document.getElementById('mdl_ruangan').innerHTML = `<i class="fas fa-map-marker-alt me-1 text-warning"></i> ${data.ruangan}`;
        document.getElementById('mdl_catatan').innerText = data.catatan;

        // Dokumen
        const docWrapper = document.getElementById('mdl_dokumen_wrapper');
        if (data.dokumen) {
            docWrapper.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-file-pdf text-danger fs-3 me-2"></i>
                    <span class="fw-bold text-dark fs-7">${data.dokumen}</span>
                </div>
                <button type="button" class="btn btn-light-primary btn-sm fw-bold fs-8" onclick="simulasiDownloadDokumen('${data.dokumen}')">
                    <i class="fas fa-download me-1"></i> Unduh
                </button>
            `;
        } else {
            docWrapper.innerHTML = `<span class="text-muted fs-7">Tidak ada dokumen diunggah</span>`;
        }

        // Foto
        const fotoWrapper = document.getElementById('mdl_foto_wrapper');
        if (data.foto) {
            fotoWrapper.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-camera text-success fs-3 me-2"></i>
                    <span class="fw-bold text-dark fs-7">Foto Sesi Bimbingan</span>
                </div>
                <button type="button" class="btn btn-light-success btn-sm fw-bold fs-8" onclick="previewFotoRiwayat('${window.location.origin}/${data.foto}', '${data.topik}')">
                    <i class="fas fa-eye me-1"></i> Lihat Foto
                </button>
            `;
        } else {
            fotoWrapper.innerHTML = `<span class="text-muted fs-7">Belum ada foto dokumentasi</span>`;
        }

        var myModal = new bootstrap.Modal(document.getElementById('modalDetailRiwayat'));
        myModal.show();
    }

    function simulasiDownloadDokumen(filename) {
        alert('Mengunduh dokumen: ' + filename);
    }

    function cetakLogbookDirect() {
        window.print();
    }
</script>
@endpush
@endsection
