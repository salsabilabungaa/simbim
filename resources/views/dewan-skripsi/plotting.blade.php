@extends('layouts.dewan-skripsi')

@section('title', 'Plotting Dosen & Jadwal Sidang — Dewan Skripsi')
@section('page_title', 'Plotting Dosen & Jadwal Sidang')

@section('content')
<div class="card card-flush shadow-sm mb-6">
    <div class="card-body py-6 px-8 bg-light-info rounded border border-info border-dashed">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
            <div>
                <h3 class="text-info fw-bolder mb-1">
                    <i class="fas fa-user-friends text-info me-2"></i> Plotting Dosen Pembimbing, Dosen Penguji & Jadwal Sidang
                </h3>
                <p class="text-gray-700 fs-7 mb-0">
                    Menetapkan (menjodohkan) mahasiswa dengan Dosen Pembimbing (Utama & Pendamping) serta Dosen Penguji Sidang (Ketua & Anggota), serta menyusun jadwal waktu dan ruangan pelaksanaan sidang.
                </p>
            </div>
            <div class="d-flex gap-2">
                <span class="badge badge-info fs-7 py-2 px-4">
                    <i class="fas fa-users me-1"></i> {{ $dosens->count() }} Dosen Tersedia
                </span>
            </div>
        </div>
    </div>
</div>

<div class="card card-flush shadow-sm">
    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
        <div class="card-title">
            <h4 class="fw-bolder fs-5 text-gray-900 mb-0">
                <i class="fas fa-tasks text-primary me-2"></i> Daftar Mahasiswa & Penetapan Sidang
            </h4>
        </div>

        <div class="card-toolbar flex-wrap gap-3">
            <!-- Filter Status Plotting -->
            <div class="d-flex flex-wrap gap-1">
                <a href="{{ route('dewan-skripsi.plotting', ['status_plotting' => 'semua', 'search' => $search]) }}" class="btn btn-sm {{ $statusPlotting === 'semua' ? 'btn-primary' : 'btn-light' }} fw-bold">
                    Semua
                </a>
                <a href="{{ route('dewan-skripsi.plotting', ['status_plotting' => 'belum_lengkap', 'search' => $search]) }}" class="btn btn-sm {{ $statusPlotting === 'belum_lengkap' ? 'btn-danger' : 'btn-light' }} fw-bold">
                    Dosen Belum Lengkap
                </a>
                <a href="{{ route('dewan-skripsi.plotting', ['status_plotting' => 'lengkap', 'search' => $search]) }}" class="btn btn-sm {{ $statusPlotting === 'lengkap' ? 'btn-success' : 'btn-light' }} fw-bold">
                    Dosen Lengkap
                </a>
                <a href="{{ route('dewan-skripsi.plotting', ['status_plotting' => 'belum_terjadwal', 'search' => $search]) }}" class="btn btn-sm {{ $statusPlotting === 'belum_terjadwal' ? 'btn-warning' : 'btn-light' }} fw-bold">
                    Belum Ada Jadwal
                </a>
                <a href="{{ route('dewan-skripsi.plotting', ['status_plotting' => 'terjadwal', 'search' => $search]) }}" class="btn btn-sm {{ $statusPlotting === 'terjadwal' ? 'btn-info' : 'btn-light' }} fw-bold">
                    Sudah Terjadwal
                </a>
            </div>

            <!-- Search Form -->
            <form action="{{ route('dewan-skripsi.plotting') }}" method="GET" class="d-flex align-items-center position-relative my-1">
                <input type="hidden" name="status_plotting" value="{{ $statusPlotting }}" />
                <span class="svg-icon svg-icon-1 position-absolute ms-3">
                    <i class="fas fa-search text-gray-500"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm form-control-solid w-200px w-lg-250px ps-10" placeholder="Cari nama / NIM..." />
                @if($search)
                    <a href="{{ route('dewan-skripsi.plotting', ['status_plotting' => $statusPlotting]) }}" class="btn btn-sm btn-icon btn-light ms-1">
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
                        <th class="min-w-220px">Judul Skripsi</th>
                        <th class="min-w-200px">Dosen Pembimbing</th>
                        <th class="min-w-200px">Dosen Penguji</th>
                        <th class="min-w-180px">Jadwal & Ruang Sidang</th>
                        <th class="min-w-120px text-end">Aksi Plotting</th>
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
                                        <span class="text-gray-900 fw-bolder fs-6">{{ $item->mahasiswa?->name ?? 'Mahasiswa' }}</span>
                                        <span class="text-muted fs-8">NIM: {{ $item->mahasiswa?->nim ?? '-' }}</span>
                                        <span class="badge badge-light-primary fs-9 mt-0.5 align-self-start">{{ $item->tahap }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-dark fw-bold" style="max-width: 250px; line-height: 1.35;">
                                    {{ $item->judul }}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column fs-8">
                                    <div class="mb-1">
                                        <span class="badge badge-light-info fw-bold me-1">P1</span>
                                        @if($item->pembimbing_1_id || $item->pembimbing_1_nama)
                                            <span class="text-gray-900">{{ $item->nama_pembimbing_1 }}</span>
                                        @else
                                            <span class="text-danger fst-italic">Belum diplot</span>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="badge badge-light-secondary fw-bold me-1">P2</span>
                                        @if($item->pembimbing_2_id || $item->pembimbing_2_nama)
                                            <span class="text-gray-900">{{ $item->nama_pembimbing_2 }}</span>
                                        @else
                                            <span class="text-danger fst-italic">Belum diplot</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column fs-8">
                                    <div class="mb-1">
                                        <span class="badge badge-light-warning fw-bold me-1">U1</span>
                                        @if($item->penguji_1_id || $item->penguji_1_nama)
                                            <span class="text-gray-900">{{ $item->nama_penguji_1 }}</span>
                                        @else
                                            <span class="text-danger fst-italic">Belum diplot</span>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="badge badge-light-secondary fw-bold me-1">U2</span>
                                        @if($item->penguji_2_id || $item->penguji_2_nama)
                                            <span class="text-gray-900">{{ $item->nama_penguji_2 }}</span>
                                        @else
                                            <span class="text-danger fst-italic">Belum diplot</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($item->jadwal_sidang)
                                    <div class="d-flex flex-column fs-8">
                                        <div class="text-gray-900 fw-bold mb-0.5">
                                            <i class="far fa-calendar-alt text-primary me-1"></i>
                                            {{ $item->jadwal_sidang->translatedFormat('d M Y - H:i') }} WIB
                                        </div>
                                        <div class="text-muted">
                                            <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                            {{ $item->ruangan_sidang ?? 'Ruang Sidang' }}
                                        </div>
                                        <span class="badge badge-light-success fs-9 mt-1 align-self-start">{{ $item->status_sidang }}</span>
                                    </div>
                                @else
                                    <span class="badge badge-light-warning fs-8 text-warning">
                                        <i class="far fa-clock me-1"></i> Belum ada jadwal
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-light-primary fw-bolder px-3" data-bs-toggle="modal" data-bs-target="#modal_plotting_{{ $item->id }}">
                                    <i class="fas fa-edit me-1"></i> Atur Plotting
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Atur Plotting Dosen & Jadwal Sidang -->
                        <div class="modal fade" id="modal_plotting_{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content rounded-4 border-0 shadow-lg">
                                    <form action="{{ route('dewan-skripsi.plotting.update', $item->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header bg-info text-white py-4 px-6 rounded-top-4">
                                            <h5 class="modal-title text-white fw-bolder">
                                                <i class="fas fa-user-edit me-2"></i> Penetapan Plotting Dosen & Jadwal Sidang
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body p-6">
                                            <!-- Info Mahasiswa -->
                                            <div class="alert alert-light-primary d-flex align-items-center p-4 mb-5 border border-primary">
                                                <div class="symbol symbol-45px me-3">
                                                    <img src="{{ $item->mahasiswa?->avatar_url ?? asset('assets/media/avatars/300-1.jpg') }}" alt="" />
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <h6 class="mb-0 fw-bolder text-dark">{{ $item->mahasiswa?->name }} (NIM: {{ $item->mahasiswa?->nim }})</h6>
                                                    <span class="text-muted fs-8">{{ $item->judul }}</span>
                                                </div>
                                            </div>

                                            <h6 class="fw-bolder text-dark mb-3 border-bottom pb-2">
                                                <i class="fas fa-user-tie text-info me-1"></i> 1. Penetapan Dosen Pembimbing (Menjodohkan Pembimbing)
                                            </h6>
                                            <div class="row g-3 mb-5">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold fs-7 required">Dosen Pembimbing 1 (Utama)</label>
                                                    <select name="pembimbing_1_id" class="form-select form-select-solid" required>
                                                        <option value="">-- Pilih Dosen Pembimbing 1 --</option>
                                                        @foreach($dosens as $dosen)
                                                            <option value="{{ $dosen->id }}" {{ $item->pembimbing_1_id == $dosen->id ? 'selected' : '' }}>
                                                                {{ $dosen->name }} (NIP: {{ $dosen->nip ?? '-' }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold fs-7">Dosen Pembimbing 2 (Pendamping)</label>
                                                    <select name="pembimbing_2_id" class="form-select form-select-solid">
                                                        <option value="">-- Pilih Dosen Pembimbing 2 --</option>
                                                        @foreach($dosens as $dosen)
                                                            <option value="{{ $dosen->id }}" {{ $item->pembimbing_2_id == $dosen->id ? 'selected' : '' }}>
                                                                {{ $dosen->name }} (NIP: {{ $dosen->nip ?? '-' }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <h6 class="fw-bolder text-dark mb-3 border-bottom pb-2">
                                                <i class="fas fa-user-check text-warning me-1"></i> 2. Penetapan Dosen Penguji Sidang
                                            </h6>
                                            <div class="row g-3 mb-5">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold fs-7">Dosen Penguji 1 (Ketua Penguji)</label>
                                                    <select name="penguji_1_id" class="form-select form-select-solid">
                                                        <option value="">-- Pilih Dosen Penguji 1 --</option>
                                                        @foreach($dosens as $dosen)
                                                            <option value="{{ $dosen->id }}" {{ $item->penguji_1_id == $dosen->id ? 'selected' : '' }}>
                                                                {{ $dosen->name }} (NIP: {{ $dosen->nip ?? '-' }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold fs-7">Dosen Penguji 2 (Anggota Penguji)</label>
                                                    <select name="penguji_2_id" class="form-select form-select-solid">
                                                        <option value="">-- Pilih Dosen Penguji 2 --</option>
                                                        @foreach($dosens as $dosen)
                                                            <option value="{{ $dosen->id }}" {{ $item->penguji_2_id == $dosen->id ? 'selected' : '' }}>
                                                                {{ $dosen->name }} (NIP: {{ $dosen->nip ?? '-' }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <h6 class="fw-bolder text-dark mb-3 border-bottom pb-2">
                                                <i class="far fa-calendar-alt text-primary me-1"></i> 3. Pembuatan Jadwal Pelaksanaan Sidang
                                            </h6>
                                            <div class="row g-3 mb-3">
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold fs-7">Jenis Sidang</label>
                                                    <select name="jenis_sidang" class="form-select form-select-solid">
                                                        <option value="Sidang Skripsi / Munaqasyah" {{ $item->jenis_sidang === 'Sidang Skripsi / Munaqasyah' ? 'selected' : '' }}>Sidang Skripsi / Munaqasyah</option>
                                                        <option value="Seminar Proposal" {{ $item->jenis_sidang === 'Seminar Proposal' ? 'selected' : '' }}>Seminar Proposal</option>
                                                        <option value="Seminar Hasil" {{ $item->jenis_sidang === 'Seminar Hasil' ? 'selected' : '' }}>Seminar Hasil</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold fs-7">Tanggal & Waktu Sidang</label>
                                                    <input type="datetime-local" name="jadwal_sidang" class="form-control form-control-solid" value="{{ $item->jadwal_sidang ? $item->jadwal_sidang->format('Y-m-d\TH:i') : '' }}" />
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold fs-7">Ruangan Sidang</label>
                                                    <input type="text" name="ruangan_sidang" class="form-control form-control-solid" placeholder="Contoh: Ruang Sidang 1, Lt. 3" value="{{ $item->ruangan_sidang }}" />
                                                </div>
                                            </div>

                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold fs-7">Metode Pelaksanaan</label>
                                                    <select name="metode_sidang" class="form-select form-select-solid">
                                                        <option value="Offline / Luring" {{ $item->metode_sidang === 'Offline / Luring' ? 'selected' : '' }}>Offline / Luring</option>
                                                        <option value="Online / Daring (Zoom)" {{ $item->metode_sidang === 'Online / Daring (Zoom)' ? 'selected' : '' }}>Online / Daring (Zoom)</option>
                                                        <option value="Hybrid" {{ $item->metode_sidang === 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold fs-7">Status Sidang</label>
                                                    <select name="status_sidang" class="form-select form-select-solid">
                                                        <option value="Belum Sidang" {{ $item->status_sidang === 'Belum Sidang' ? 'selected' : '' }}>Belum Sidang</option>
                                                        <option value="Dijadwalkan" {{ $item->status_sidang === 'Dijadwalkan' ? 'selected' : '' }}>Dijadwalkan</option>
                                                        <option value="Berlangsung" {{ $item->status_sidang === 'Berlangsung' ? 'selected' : '' }}>Berlangsung</option>
                                                        <option value="Selesai" {{ $item->status_sidang === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer bg-light py-3 px-6 rounded-bottom-4 d-flex justify-content-between">
                                            <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-info fw-bolder px-5">
                                                <i class="fas fa-save me-1.5"></i> Simpan Plotting & Jadwal
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10">
                                <span class="text-muted fw-bold">Tidak ada data mahasiswa pada kriteria plotting ini.</span>
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

