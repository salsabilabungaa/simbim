@extends('layouts.dewan-skripsi')

@section('title', 'Verifikasi Berkas Pasca-Sidang & Kelulusan — Dewan Skripsi')
@section('page_title', 'Verifikasi Berkas Pasca-Sidang & Kelulusan')

@section('content')
<div class="card card-flush shadow-sm mb-6">
    <div class="card-body py-6 px-8 bg-light-success rounded border border-success border-dashed">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
            <div>
                <h3 class="text-success fw-bolder mb-1">
                    <i class="fas fa-file-signature text-success me-2"></i> Ruang Verifikasi Berkas Pasca-Sidang & Pengeluaran Kelulusan
                </h3>
                <p class="text-gray-800 fs-7 mb-0">
                    Mengelola berkas revisi final mahasiswa setelah pelaksanaan sidang, menginput rekapitulasi nilai sidang dari dosen penguji & pembimbing, dan mengeluarkan status resmi <strong>"Lulus/Selesai"</strong> beserta Surat Keterangan Lulus (SKL) sebelum mahasiswa mendaftar yudisium.
                </p>
            </div>
            <div class="d-flex gap-2">
                @php
                    $revisiCount = \App\Models\Skripsi::where('tahap', 'Revisi Final')->where('status_kelulusan', '!=', 'Lulus/Selesai')->count();
                    $lulusCount = \App\Models\Skripsi::where('status_kelulusan', 'Lulus/Selesai')->count();
                @endphp
                <span class="badge badge-warning fs-7 py-2 px-3 fw-bold text-dark">
                    <i class="fas fa-pencil-alt me-1"></i> {{ $revisiCount }} Perlu Verifikasi Revisi
                </span>
                <span class="badge badge-success fs-7 py-2 px-3 fw-bold">
                    <i class="fas fa-graduation-cap me-1"></i> {{ $lulusCount }} Telah Lulus
                </span>
            </div>
        </div>
    </div>
</div>

<div class="card card-flush shadow-sm">
    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
        <div class="card-title">
            <h4 class="fw-bolder fs-5 text-gray-900 mb-0">
                <i class="fas fa-tasks text-success me-2"></i> Daftar Mahasiswa Pasca-Sidang Tugas Akhir
            </h4>
        </div>

        <div class="card-toolbar flex-wrap gap-3">
            <div class="d-flex flex-wrap gap-1">
                <a href="{{ route('dewan-skripsi.verifikasi-pasca-sidang', ['status_kelulusan' => 'semua', 'search' => $search]) }}" class="btn btn-sm {{ $filterStatus === 'semua' ? 'btn-primary' : 'btn-light' }} fw-bold">
                    Semua
                </a>
                <a href="{{ route('dewan-skripsi.verifikasi-pasca-sidang', ['status_kelulusan' => 'belum_lulus', 'search' => $search]) }}" class="btn btn-sm {{ $filterStatus === 'belum_lulus' ? 'btn-warning' : 'btn-light' }} fw-bold">
                    Menunggu Verifikasi Nilai & Kelulusan
                </a>
                <a href="{{ route('dewan-skripsi.verifikasi-pasca-sidang', ['status_kelulusan' => 'lulus', 'search' => $search]) }}" class="btn btn-sm {{ $filterStatus === 'lulus' ? 'btn-success' : 'btn-light' }} fw-bold">
                    Lulus / Selesai (Siap Yudisium)
                </a>
            </div>

            <!-- Search Form -->
            <form action="{{ route('dewan-skripsi.verifikasi-pasca-sidang') }}" method="GET" class="d-flex align-items-center position-relative my-1">
                <input type="hidden" name="status_kelulusan" value="{{ $filterStatus }}" />
                <span class="svg-icon svg-icon-1 position-absolute ms-3">
                    <i class="fas fa-search text-gray-500"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm form-control-solid w-200px w-lg-250px ps-10" placeholder="Cari nama / NIM..." />
                @if($search)
                    <a href="{{ route('dewan-skripsi.verifikasi-pasca-sidang', ['status_kelulusan' => $filterStatus]) }}" class="btn btn-sm btn-icon btn-light ms-1">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table table-row-bordered table-row-dashed align-middle gs-4 gy-4">
                <thead class="bg-light fw-bolder fs-7 text-uppercase text-gray-700">
                    <tr>
                        <th class="w-40px">No</th>
                        <th class="min-w-180px">Mahasiswa</th>
                        <th class="min-w-200px">Judul Skripsi</th>
                        <th class="min-w-200px">Dokumen Revisi Final</th>
                        <th class="min-w-180px text-center">Rekap Nilai Sidang</th>
                        <th class="min-w-160px text-center">Status Kelulusan</th>
                        <th class="min-w-180px text-end">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="fs-7 fw-bold text-gray-600">
                    @forelse($skripsis as $index => $item)
                        <tr>
                            <td>{{ $skripsis->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-35px me-2.5">
                                        <img src="{{ $item->mahasiswa?->avatar_url ?? asset('assets/media/avatars/300-1.jpg') }}" alt="" />
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-900 fw-bolder fs-6">{{ $item->mahasiswa?->name }}</span>
                                        <span class="text-muted fs-8">NIM: {{ $item->mahasiswa?->nim }}</span>
                                        <span class="badge badge-light-primary fs-9 mt-0.5 align-self-start">{{ $item->tahap }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-dark fw-bold mb-1" style="max-width: 250px; line-height: 1.35;">
                                    {{ $item->judul }}
                                </div>
                                <div class="text-muted fs-8">
                                    <i class="far fa-calendar-check text-primary me-1"></i> Sidang: {{ $item->jadwal_sidang?->format('d/m/Y') ?? 'Belum ada jadwal' }}
                                </div>
                            </td>
                            <td>
                                @if($item->file_revisi_final || $item->catatan_revisi_final)
                                    <div class="d-flex flex-column fs-8">
                                        <div class="d-flex align-items-center text-success fw-bold mb-1">
                                            <i class="fas fa-file-pdf text-danger me-1.5 fs-6"></i>
                                            <span>{{ $item->file_revisi_final ? basename($item->file_revisi_final) : 'Naskah_Revisi_Final.pdf' }}</span>
                                        </div>
                                        @if($item->catatan_revisi_final)
                                            <span class="text-gray-700 fs-9 fst-italic" style="max-width: 220px;">
                                                "{{ Str::limit($item->catatan_revisi_final, 70) }}"
                                            </span>
                                        @endif
                                        @if($item->tanggal_unggah_revisi)
                                            <span class="text-muted fs-9 mt-1">
                                                Diunggah: {{ $item->tanggal_unggah_revisi->diffForHumans() }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="badge badge-light-warning text-warning fs-8">
                                        <i class="fas fa-hourglass-start me-1"></i> Menunggu Unggahan Revisi
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->nilai_akhir)
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="d-flex align-items-center gap-1 mb-1">
                                            <span class="fs-5 fw-bolder text-dark">{{ $item->nilai_akhir }}</span>
                                            <span class="badge badge-success fs-7 fw-bolder px-2 py-0.5">{{ $item->nilai_huruf }}</span>
                                        </div>
                                        <span class="text-muted fs-9">
                                            P1: {{ $item->nilai_pembimbing_1 }} | U1: {{ $item->nilai_penguji_1 }}
                                        </span>
                                    </div>
                                @else
                                    <span class="badge badge-light-secondary text-gray-700 fs-8">
                                        Belum Diinput
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->status_kelulusan === 'Lulus/Selesai')
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="badge badge-success fw-bolder fs-8 px-3 py-1.5 mb-1">
                                            <i class="fas fa-check-double me-1"></i> Lulus/Selesai
                                        </span>
                                        <span class="badge badge-light-primary fs-9 font-monospace">
                                            {{ $item->no_surat_keterangan }}
                                        </span>
                                        <span class="text-muted fs-9 mt-0.5">Siap Daftar Yudisium</span>
                                    </div>
                                @else
                                    <span class="badge badge-warning fw-bold fs-8 text-dark px-3 py-1.5">
                                        {{ $item->status_kelulusan }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1 flex-wrap">
                                    <!-- Tombol Input / Edit Nilai Sidang -->
                                    <button type="button" class="btn btn-sm btn-light-info fw-bold" data-bs-toggle="modal" data-bs-target="#modal_nilai_{{ $item->id }}" title="Input / Edit Nilai Sidang">
                                        <i class="fas fa-calculator me-1"></i> Nilai
                                    </button>

                                    <!-- Tombol Keluarkan Kelulusan -->
                                    @if($item->status_kelulusan !== 'Lulus/Selesai')
                                        <button type="button" class="btn btn-sm btn-success fw-bolder" data-bs-toggle="modal" data-bs-target="#modal_lulus_{{ $item->id }}" title="Keluarkan Status Lulus/Selesai">
                                            <i class="fas fa-graduation-cap me-1"></i> Luluskan
                                        </button>
                                    @else
                                        <!-- Tombol Cetak SKL -->
                                        <a href="{{ route('dewan-skripsi.cetak-surat', $item->id) }}" target="_blank" class="btn btn-sm btn-light-success fw-bold" title="Cetak Surat Keterangan Lulus (SKL)">
                                            <i class="fas fa-print me-1"></i> SKL
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Modal 1: Input / Rekap Nilai Sidang -->
                        <div class="modal fade" id="modal_nilai_{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow-lg">
                                    <form action="{{ route('dewan-skripsi.verifikasi-pasca-sidang.nilai', $item->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header bg-info text-white py-4 px-6 rounded-top-4">
                                            <h5 class="modal-title text-white fw-bolder">
                                                <i class="fas fa-calculator me-2"></i> Input Rekap Nilai Sidang Skripsi
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-6">
                                            <div class="alert alert-light-primary p-3 mb-4 border border-primary">
                                                <span class="fw-bolder text-dark d-block mb-1">{{ $item->mahasiswa?->name }} ({{ $item->mahasiswa?->nim }})</span>
                                                <span class="fs-8 text-muted">{{ $item->judul }}</span>
                                            </div>

                                            <div class="row g-3 mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold fs-7 required">Nilai Pembimbing 1</label>
                                                    <input type="number" step="0.01" min="0" max="100" name="nilai_pembimbing_1" class="form-control form-control-solid" value="{{ $item->nilai_pembimbing_1 ?? 85.00 }}" required />
                                                    <div class="fs-9 text-muted mt-0.5">{{ $item->nama_pembimbing_1 }}</div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold fs-7">Nilai Pembimbing 2</label>
                                                    <input type="number" step="0.01" min="0" max="100" name="nilai_pembimbing_2" class="form-control form-control-solid" value="{{ $item->nilai_pembimbing_2 ?? 85.00 }}" />
                                                    <div class="fs-9 text-muted mt-0.5">{{ $item->nama_pembimbing_2 }}</div>
                                                </div>
                                            </div>

                                            <div class="row g-3 mb-4">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold fs-7 required">Nilai Penguji 1 (Ketua)</label>
                                                    <input type="number" step="0.01" min="0" max="100" name="nilai_penguji_1" class="form-control form-control-solid" value="{{ $item->nilai_penguji_1 ?? 86.00 }}" required />
                                                    <div class="fs-9 text-muted mt-0.5">{{ $item->nama_penguji_1 }}</div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold fs-7">Nilai Penguji 2 (Anggota)</label>
                                                    <input type="number" step="0.01" min="0" max="100" name="nilai_penguji_2" class="form-control form-control-solid" value="{{ $item->nilai_penguji_2 ?? 85.00 }}" />
                                                    <div class="fs-9 text-muted mt-0.5">{{ $item->nama_penguji_2 }}</div>
                                                </div>
                                            </div>

                                            <div class="mb-2">
                                                <label class="form-label fw-bold fs-7">Catatan Berita Acara / Dewan Sidang</label>
                                                <textarea name="catatan_dewan" rows="2" class="form-control form-control-solid fs-7" placeholder="Contoh: Sidang berjalan lancar. Nilai diakumulasikan dari penguji dan pembimbing.">{{ $item->catatan_dewan }}</textarea>
                                            </div>

                                            <div class="p-3 bg-light rounded text-muted fs-8 mt-3">
                                                <i class="fas fa-info-circle text-info me-1"></i>
                                                Sistem akan secara otomatis menghitung nilai rata-rata akhir dan mengonversikan nilai huruf mutu (A, AB, B, BC, C, D).
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light py-3 px-6 rounded-bottom-4 d-flex justify-content-between">
                                            <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-info fw-bolder px-5">
                                                <i class="fas fa-save me-1.5"></i> Simpan Rekap Nilai
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal 2: Keluarkan Status "Lulus/Selesai" untuk Yudisium -->
                        <div class="modal fade" id="modal_lulus_{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow-lg">
                                    <form action="{{ route('dewan-skripsi.verifikasi-pasca-sidang.lulus', $item->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header bg-success text-white py-4 px-6 rounded-top-4">
                                            <h5 class="modal-title text-white fw-bolder">
                                                <i class="fas fa-award me-2"></i> Pengeluaran Status Resmi "Lulus/Selesai"
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-6">
                                            <p class="fs-7 text-gray-700 mb-3">
                                                Anda akan menetapkan mahasiswa <strong>{{ $item->mahasiswa?->name }}</strong> berstatus <strong>"Lulus/Selesai"</strong>. Mahasiswa ini akan memperoleh Surat Keterangan Lulus (SKL) resmi untuk mendaftar <strong>Yudisium Sarjana</strong>.
                                            </p>

                                            <!-- Nilai info -->
                                            <div class="p-3 bg-light-success rounded border border-success border-dashed mb-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="fs-7 fw-bold text-dark">Nilai Akhir Sidang:</span>
                                                    <span class="badge badge-success fs-7 fw-bolder">
                                                        {{ $item->nilai_akhir ?? '87.50' }} ({{ $item->nilai_huruf ?? 'A' }})
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold fs-7">Nomor Surat Keterangan Lulus (SKL)</label>
                                                <input type="text" name="no_surat_keterangan" class="form-control form-control-solid font-monospace fs-7" value="{{ $item->no_surat_keterangan ?: 'SKL/FT-USK/SKR/' . date('Y') . '/' . str_pad($item->id, 3, '0', STR_PAD_LEFT) }}" required />
                                            </div>

                                            <div class="mb-2">
                                                <label class="form-label fw-bold fs-7">Catatan Dewan Skripsi</label>
                                                <textarea name="catatan_dewan" rows="2" class="form-control form-control-solid fs-7">Mahasiswa telah menyelesaikan seluruh rangkaian ujian dan revisi skripsi dengan baik serta dinyatakan Lulus/Selesai untuk mendaftar yudisium.</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light py-3 px-6 rounded-bottom-4 d-flex justify-content-between">
                                            <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-success fw-bolder px-5">
                                                <i class="fas fa-check-circle me-1.5"></i> Terbitkan Status Lulus/Selesai
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10">
                                <span class="text-muted fw-bold">Tidak ada data mahasiswa pasca-sidang pada filter ini.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap pt-4">
            <span class="text-muted fs-7">
                Menampilkan {{ $skripsis->firstItem() ?? 0 }} sampai {{ $skripsis->lastItem() ?? 0 }} dari {{ $skripsis->total() }} mahasiswa
            </span>
            <div>{{ $skripsis->links() }}</div>
        </div>
    </div>
</div>
@endsection

