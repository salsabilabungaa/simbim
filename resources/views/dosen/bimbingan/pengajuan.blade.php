@extends('layouts.dosen')

@section('title', 'Pengajuan Bimbingan — SIMBIM Dosen')
@section('page-title', 'Pengajuan Bimbingan')

@section('content')
<!-- Header Banner & Action Bar -->
<div class="card mb-7 shadow-sm border-0" style="background: linear-gradient(135deg, #1e1e2d 0%, #151521 50%, #222736 100%);">
    <div class="card-body p-6 p-lg-8">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge badge-light-warning fw-bolder px-3 py-2 fs-8">
                        <i class="fas fa-calendar-check me-1 text-warning"></i> Permohonan Jadwal
                    </span>
                    <span class="badge badge-light-primary fw-bolder px-3 py-2 fs-8">
                        Respon Cepat Dosen
                    </span>
                </div>
                <h1 class="text-white fw-bolder fs-2x mb-1">Pengajuan Bimbingan Mahasiswa</h1>
                <p class="text-gray-400 fs-6 fw-semibold mb-0">
                    Tinjau, setujui, jadwalkan ulang, atau beri arahan pada permohonan bimbingan skripsi dan perwalian akademik.
                </p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <button type="button" class="btn btn-sm btn-primary fw-bolder px-4 py-3 shadow-sm" onclick="#">
                    <i class="fas fa-calendar-plus me-1"></i> Atur Kuota & Jadwal Luang
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 4 Kartu Ringkasan Status Pengajuan -->
<div class="row g-5 g-xl-8 mb-7">
    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-body p-6 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-gray-600 fw-bolder fs-7 text-uppercase ls-1">TOTAL PENGAJUAN</span>
                        <div class="symbol symbol-45px bg-light-primary">
                            <span class="symbol-label">
                                <i class="fas fa-inbox text-primary fs-2"></i>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline mb-2">
                        <span class="fs-2qx fw-bolder text-dark me-2">{{ $statistik['total_pengajuan'] }}</span>
                        <span class="fs-7 text-muted fw-bold">Permohonan</span>
                    </div>
                </div>
                <span class="badge badge-light-primary fw-bolder fs-8 align-self-start px-2 py-1">
                    <i class="fas fa-history me-1 text-primary"></i> Semua Riwayat Masuk
                </span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-body p-6 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-gray-600 fw-bolder fs-7 text-uppercase ls-1">MENUNGGU RESPON</span>
                        <div class="symbol symbol-45px bg-light-warning">
                            <span class="symbol-label">
                                <i class="fas fa-hourglass-half text-warning fs-2"></i>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline mb-2">
                        <span class="fs-2qx fw-bolder text-warning me-2">{{ $statistik['menunggu_konfirmasi'] }}</span>
                        <span class="fs-7 text-muted fw-bold">Perlu Konfirmasi</span>
                    </div>
                </div>
                <span class="badge badge-light-warning fw-bolder fs-8 align-self-start px-2 py-1">
                    <i class="fas fa-exclamation-circle me-1 text-warning"></i> Segera Ditindaklanjuti
                </span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-body p-6 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-gray-600 fw-bolder fs-7 text-uppercase ls-1">JADWAL TERKONFIRMASI</span>
                        <div class="symbol symbol-45px bg-light-success">
                            <span class="symbol-label">
                                <i class="fas fa-calendar-check text-success fs-2"></i>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline mb-2">
                        <span class="fs-2qx fw-bolder text-success me-2">{{ $statistik['terkonfirmasi'] }}</span>
                        <span class="fs-7 text-muted fw-bold">Sesi Siap Jalan</span>
                    </div>
                </div>
                <span class="badge badge-light-success fw-bolder fs-8 align-self-start px-2 py-1">
                    <i class="fas fa-check-circle me-1 text-success"></i> Disetujui Dosen
                </span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-custom bg-body border border-gray-200 shadow-sm h-100">
            <div class="card-body p-6 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-gray-600 fw-bolder fs-7 text-uppercase ls-1">RESCHEDULE / SELESAI</span>
                        <div class="symbol symbol-45px bg-light-danger">
                            <span class="symbol-label">
                                <i class="fas fa-calendar-times text-danger fs-2"></i>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline mb-2">
                        <span class="fs-2qx fw-bolder text-danger me-2">{{ $statistik['selesai_ditolak'] }}</span>
                        <span class="fs-7 text-muted fw-bold">Jadwal Ulang</span>
                    </div>
                </div>
                <span class="badge badge-light-danger fw-bolder fs-8 align-self-start px-2 py-1">
                    <i class="fas fa-sync me-1 text-danger"></i> Perlu Penyesuaian
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Filter Toolbar & Daftar Pengajuan -->
<div class="card card-custom border border-gray-200 shadow-sm mb-7">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                <span class="svg-icon svg-icon-1 position-absolute ms-4">
                    <i class="fas fa-search text-gray-500"></i>
                </span>
                <input type="text" id="search_pengajuan" class="form-control form-control-solid w-250px w-md-350px ps-12" placeholder="Cari nama, NIM, topik usulan..." />
            </div>
        </div>
        <div class="card-toolbar">
            <div class="d-flex justify-content-end align-items-center gap-2 flex-wrap">
                <div class="btn-group" role="group" aria-label="Filter Status Pengajuan">
                    <button type="button" class="btn btn-sm btn-outline-primary active filter-status-btn" data-status="all">Semua ({{ count($daftar_pengajuan) }})</button>
                    <button type="button" class="btn btn-sm btn-outline-primary filter-status-btn" data-status="menunggu">Menunggu ({{ $statistik['menunggu_konfirmasi'] }})</button>
                    <button type="button" class="btn btn-sm btn-outline-primary filter-status-btn" data-status="disetujui">Terkonfirmasi ({{ $statistik['terkonfirmasi'] }})</button>
                    <!-- <button type="button" class="btn btn-sm btn-outline-primary filter-status-btn" data-status="ditolak">Reschedule ({{ $statistik['selesai_ditolak'] }})</button>
                </div> -->
            </div>
        </div>
    </div>

    <div class="card-body pt-2">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="table_pengajuan">
                <thead>
                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0 border-bottom border-gray-300">
                        <th class="min-w-200px">Mahasiswa & Waktu Kirim</th>
                        <th class="min-w-240px">Topik & Lampiran</th>
                        <!-- <th class="min-w-180px">Usulan Jadwal & Ruang</th> -->
                        <th class="min-w-130px">Status</th>
                        <th class="min-w-140px text-end">Aksi Konfirmasi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 fw-semibold">
                    @forelse ($daftar_pengajuan as $item)
                        <tr class="pengajuan-row" data-status="{{ $item['status'] }}" data-search="{{ strtolower($item['nama'] . ' ' . $item['nim'] . ' ' . $item['topik'] . ' ' . $item['tempat']) }}">
                            <!-- Mahasiswa -->
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-45px symbol-circle me-3 bg-light-{{ $item['badge_status'] }}">
                                        <span class="symbol-label text-{{ $item['badge_status'] }} fw-bolder fs-5">
                                            {{ strtoupper(substr($item['nama'], 0, 2)) }}
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark fw-bolder text-hover-primary fs-6">{{ $item['nama'] }}</span>
                                        <span class="text-muted fs-7">NIM: <span class="fw-bold text-gray-800">{{ $item['nim'] }}</span></span>
                                        <span class="text-muted fs-8">Diajukan: {{ $item['waktu_pengajuan'] }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Topik & Lampiran -->
                            <td>
                                <span class="badge badge-light-{{ $item['jenis_bimbingan'] === 'skripsi' ? 'primary' : 'info' }} fw-bolder fs-8 mb-1">
                                    {{ $item['kategori_label'] }}
                                </span>
                                <div class="text-dark fw-bold fs-7 mb-2" style="max-width: 320px; line-height: 1.4;">
                                    {{ $item['topik'] }}
                                </div>
                                @if ($item['file_lampiran'])
                                    <button type="button" class="btn btn-sm btn-light-primary py-1 px-3 fs-8 fw-bold" onclick="Swal.fire({icon: 'info', title: 'Unduh Berkas', text: 'Mengunduh {{ $item['file_lampiran'] }}...', timer: 1500, showConfirmButton: false})">
                                        <i class="fas fa-paperclip me-1 text-primary"></i> {{ $item['file_lampiran'] }}
                                    </button>
                                @else
                                    <span class="text-muted fs-8 italic">Tanpa lampiran berkas</span>
                                @endif
                            </td>

                            <!-- Usulan Jadwal & Ruang
                            <td>
                                <div class="fw-bolder text-gray-900 fs-7 mb-1">
                                    <i class="far fa-calendar-alt text-primary me-1"></i> {{ $item['jadwal_usulan'] }}
                                </div>
                                <div class="text-primary fw-bold fs-8 mb-1">
                                    <i class="far fa-clock me-1"></i> {{ $item['jam_usulan'] }}
                                </div>
                                <div class="text-muted fs-8">
                                    <i class="fas fa-map-marker-alt me-1 text-danger"></i> {{ $item['tempat'] }}
                                </div>
                            </td> -->

                            <!-- Status -->
                            <td>
                                <span class="badge badge-light-{{ $item['badge_status'] }} fw-bolder fs-8 px-3 py-2">
                                    {{ $item['status_label'] }}
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="text-end">
                                <div class="d-flex justify-content-end align-items-center gap-1">
                                    @if ($item['status'] === 'menunggu')
                                        <!-- <button type="button" class="btn btn-icon btn-light-success btn-sm" title="Setujui Jadwal" onclick="setujuiJadwal({{ json_encode($item) }})">
                                            <i class="fas fa-check fs-6"></i>
                                        </button> -->
                                        <button type="button" class="btn btn-icon btn-light-warning btn-sm" title="Jadwalkan Ulang / Reschedule" onclick="bukaModalReschedule({{ json_encode($item) }})">
                                            <i class="fas fa-calendar-alt fs-6"></i>
                                        </button>
                                        <button type="button" class="btn btn-icon btn-light-danger btn-sm" title="Tolak Pengajuan" onclick="tolakPengajuan({{ json_encode($item) }})">
                                            <i class="fas fa-times fs-6"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-light-primary fw-bolder py-1 px-3" onclick="showDetailPengajuan({{ json_encode($item) }})">
                                            <i class="fas fa-eye me-1"></i> Detail
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-muted">
                                <i class="fas fa-inbox fs-2x mb-3 text-gray-400 d-block"></i>
                                Tidak ada pengajuan bimbingan yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Reschedule Jadwal Bimbingan -->
<div class="modal fade" id="modal_reschedule" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-600px">
        <div class="modal-content rounded">
            <div class="modal-header border-0 pb-0">
                <h3 class="fw-bolder text-dark mb-0">Jadwalkan Ulang Bimbingan</h3>
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="fas fa-times fs-4"></i>
                </div>
            </div>
            <div class="modal-body px-8 py-6">
                <div class="alert alert-light-warning d-flex align-items-center p-4 mb-5 rounded">
                    <i class="fas fa-calendar-alt fs-2 text-warning me-3"></i>
                    <div>
                        <div class="fw-bolder text-gray-800" id="reschedule_mhs_nama">-</div>
                        <div class="text-muted fs-8" id="reschedule_mhs_topik">-</div>
                    </div>
                </div>

                <form id="form_reschedule" onsubmit="submitReschedule(event)">
                    <div class="mb-4">
                        <label class="form-label fw-bolder text-gray-700 fs-7 required">Tanggal Baru</label>
                        <input type="date" class="form-control form-control-solid" id="reschedule_tanggal" required value="{{ date('Y-m-d', strtotime('+2 days')) }}" />
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label fw-bolder text-gray-700 fs-7 required">Jam Mulai</label>
                            <input type="time" class="form-control form-control-solid" id="reschedule_jam_mulai" value="10:00" required />
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bolder text-gray-700 fs-7 required">Jam Selesai</label>
                            <input type="time" class="form-control form-control-solid" id="reschedule_jam_selesai" value="11:00" required />
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bolder text-gray-700 fs-7 required">Ruang / Tautan Pertemuan</label>
                        <input type="text" class="form-control form-control-solid" id="reschedule_tempat" value="Ruang Dosen 204 Fasilkom (Luring)" required />
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-bolder text-gray-700 fs-7">Catatan untuk Mahasiswa</label>
                        <textarea class="form-control form-control-solid" id="reschedule_catatan" rows="3" placeholder="Alasan perubahan jadwal atau berkas yang perlu disiapkan..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary fw-bolder">
                            <i class="fas fa-check me-1"></i> Konfirmasi Perubahan Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Pengajuan -->
<div class="modal fade" id="modal_detail_pengajuan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-600px">
        <div class="modal-content rounded">
            <div class="modal-header border-0 pb-0 justify-content-between">
                <h3 class="fw-bolder text-dark mb-0">Rincian Pengajuan Bimbingan</h3>
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="fas fa-times fs-4"></i>
                </div>
            </div>
            <div class="modal-body px-8 py-6">
                <div class="rounded border border-gray-200 p-5 mb-5 bg-light-secondary">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="text-muted fs-8 d-block text-uppercase fw-bolder">Mahasiswa</span>
                            <span class="fw-bolder text-gray-800 fs-7" id="pengajuan_detail_nama">-</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted fs-8 d-block text-uppercase fw-bolder">NIM</span>
                            <span class="fw-bold text-gray-800 fs-7" id="pengajuan_detail_nim">-</span>
                        </div>
                        <div class="col-12">
                            <span class="text-muted fs-8 d-block text-uppercase fw-bolder">Topik Konsultasi</span>
                            <span class="fw-bold text-gray-900 fs-7" id="pengajuan_detail_topik">-</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted fs-8 d-block text-uppercase fw-bolder">Jadwal</span>
                            <span class="fw-bolder text-primary fs-7" id="pengajuan_detail_jadwal">-</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted fs-8 d-block text-uppercase fw-bolder">Metode & Tempat</span>
                            <span class="fw-bolder text-gray-800 fs-7" id="pengajuan_detail_tempat">-</span>
                        </div>
                        <div class="col-12">
                            <span class="text-muted fs-8 d-block text-uppercase fw-bolder">Catatan Mahasiswa</span>
                            <p class="text-gray-700 fs-7 mb-0 fst-italic" id="pengajuan_detail_catatan">-</p>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let activePengajuanItem = null;

    // Filter Pencarian
    document.getElementById('search_pengajuan').addEventListener('input', function() {
        filterPengajuanTable();
    });

    // Filter Status Buttons
    document.querySelectorAll('.filter-status-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-status-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            filterPengajuanTable();
        });
    });

    function filterPengajuanTable() {
        const query = document.getElementById('search_pengajuan').value.toLowerCase().trim();
        const activeBtn = document.querySelector('.filter-status-btn.active');
        const status = activeBtn ? activeBtn.getAttribute('data-status') : 'all';

        const rows = document.querySelectorAll('#table_pengajuan tbody tr.pengajuan-row');
        rows.forEach(row => {
            const rowStatus = row.getAttribute('data-status');
            const rowSearch = row.getAttribute('data-search');

            const matchesStatus = (status === 'all' || rowStatus === status);
            const matchesQuery = (!query || rowSearch.includes(query));

            if (matchesStatus && matchesQuery) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function setujuiJadwal(item) {
        Swal.fire({
            title: 'Setujui Jadwal Bimbingan?',
            html: 'Konfirmasi jadwal bimbingan untuk <b>' + item.nama + '</b> pada hari <b>' + item.jadwal_usulan + '</b> (' + item.jam_usulan + ').',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-light'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Jadwal Dikonfirmasi',
                    text: 'Notifikasi dan tiket jadwal bimbingan telah dikirimkan ke mahasiswa ' + item.nama + '.',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
            }
        });
    }

    function bukaModalReschedule(item) {
        activePengajuanItem = item;
        document.getElementById('reschedule_mhs_nama').innerText = item.nama + ' (' + item.nim + ')';
        document.getElementById('reschedule_mhs_topik').innerText = item.topik;
        document.getElementById('reschedule_tempat').value = item.tempat;

        const modal = new bootstrap.Modal(document.getElementById('modal_reschedule'));
        modal.show();
    }

    function submitReschedule(e) {
        e.preventDefault();
        const tgl = document.getElementById('reschedule_tanggal').value;
        const mulai = document.getElementById('reschedule_jam_mulai').value;
        const selesai = document.getElementById('reschedule_jam_selesai').value;
        const tempat = document.getElementById('reschedule_tempat').value;

        bootstrap.Modal.getInstance(document.getElementById('modal_reschedule')).hide();

        Swal.fire({
            icon: 'success',
            title: 'Jadwal Berhasil Diubah',
            html: 'Permintaan reschedule untuk tanggal <b>' + tgl + ' (' + mulai + ' - ' + selesai + ' WIB)</b> di ' + tempat + ' telah diteruskan ke mahasiswa.',
            confirmButtonText: 'Selesai',
            customClass: { confirmButton: 'btn btn-primary' }
        });
    }

    function tolakPengajuan(item) {
        Swal.fire({
            title: 'Tolak / Batalkan Permohonan?',
            input: 'textarea',
            inputLabel: 'Alasan penolakan / instruksi arahan untuk mahasiswa:',
            inputPlaceholder: 'Tuliskan catatan kenapa jadwal ini belum dapat disetujui...',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Kirim Penolakan',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-light'
            },
            preConfirm: (catatan) => {
                if (!catatan) {
                    Swal.showValidationMessage('Harap sertakan alasan untuk mahasiswa!');
                }
                return catatan;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'info',
                    title: 'Pengajuan Ditolak',
                    text: 'Catatan penolakan telah dikirimkan ke akun mahasiswa ' + item.nama + '.',
                    confirmButtonText: 'Selesai',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
            }
        });
    }

    function showDetailPengajuan(item) {
        document.getElementById('pengajuan_detail_nama').innerText = item.nama;
        document.getElementById('pengajuan_detail_nim').innerText = item.nim;
        document.getElementById('pengajuan_detail_topik').innerText = item.topik;
        document.getElementById('pengajuan_detail_jadwal').innerText = item.jadwal_usulan + ' (' + item.jam_usulan + ')';
        document.getElementById('pengajuan_detail_tempat').innerText = item.tempat;
        document.getElementById('pengajuan_detail_catatan').innerText = item.catatan_mahasiswa || '-';

        const modal = new bootstrap.Modal(document.getElementById('modal_detail_pengajuan'));
        modal.show();
    }

    function aturSlotWaktu() {
        Swal.fire({
            icon: 'info',
            title: 'Atur Ketersediaan Waktu',
            text: 'Pengaturan kuota bimbingan mingguan dan jam konsultasi dosen akan tersinkronisasi dengan kalender Google Workspace.',
            confirmButtonText: 'Tutup',
            customClass: { confirmButton: 'btn btn-primary' }
        });
    }
</script>
@endpush
