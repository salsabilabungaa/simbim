@extends('layouts.dosen')

@section('title', 'Diskusi & Komentar Dokumen Skripsi — SIMBIM Dosen')
@section('page-title', 'Sesi Diskusi Revisi Skripsi')

@section('content')
@php
    $dok = $workspace['dokumen'];
    $dokumenId = $dok['id'];
@endphp

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center p-4 mb-5 shadow-sm" role="alert">
        <i class="fas fa-check-circle fs-3 text-success me-3"></i>
        <div class="fw-bold fs-7">{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger d-flex align-items-center p-4 mb-5 shadow-sm">
        <i class="fas fa-exclamation-triangle fs-3 text-danger me-3"></i>
        <div class="fs-7 fw-bold">{{ $errors->first() }}</div>
    </div>
@endif

<!-- Header Sesi Mahasiswa -->
<div class="card mb-6 shadow-sm border-0" style="background: linear-gradient(135deg, #1e1e2d 0%, #151521 55%, #1c2438 100%);">
    <div class="card-body p-6 p-lg-7">
        <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-4">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge badge-light-primary fw-bolder px-3 py-1 fs-8">
                        <i class="fas fa-book-open me-1"></i> Sesi Diskusi Revisi & Komentar Dokumen
                    </span>
                    <span class="badge badge-light-{{ $dok['badge_status'] }} fw-bolder px-3 py-1 fs-8" id="top_status_draf_badge">
                        {{ $dok['status_draf'] }}
                    </span>
                    <span class="badge badge-light-info fw-bolder px-3 py-1 fs-8">
                        {{ $dok['program_studi'] }}
                    </span>
                </div>
                <h1 class="text-white fw-bolder fs-2x mb-1">{{ $dok['mahasiswa_nama'] }} <span class="text-gray-400 fs-4 fw-bold">({{ $dok['mahasiswa_nim'] }})</span></h1>
                <p class="text-gray-300 fs-7 fw-semibold mb-1 mw-750px">
                    <strong class="text-white">Judul Skripsi:</strong> {{ $dok['judul_skripsi'] }}
                </p>
                <div class="text-gray-400 fs-8">
                    {{ $dok['bab_aktif'] }} &bull; Progres: <strong class="text-white" id="top_progres_label">{{ $dok['progres_persen'] }}%</strong> &bull; Versi Draf: <strong class="text-white">{{ $dok['versi'] }}</strong>
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="{{ route('dosen.bimbingan.mahasiswa') }}" class="btn btn-sm btn-light fw-bolder px-4 py-3">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Mahasiswa
                </a>
            </div>
        </div>

        <!-- Bar Keputusan Review Dosen Pembimbing -->
        <form id="form_update_status_draf"
              method="POST"
              action="{{ route('dosen.bimbingan.skripsi.status.update', $dokumenId) }}"
              onsubmit="submitStatusUpdate(event)"
              class="mt-5 pt-4 border-top border-gray-700">
            @csrf
            @method('PATCH')
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-gray-300 fw-bold fs-8 mb-1">Keputusan Status Draf</label>
                    <select name="status_draf" id="status_draf_input" class="form-select form-select-sm form-select-solid fs-7" data-control="select2" data-placeholder="Pilih Keputusan Status">
                        @foreach (['Menunggu Review Dosen', 'Draf Sedang Direview', 'Revisi Diperlukan', 'Bimbingan Terjadwal', 'Siap Sidang Skripsi'] as $opt)
                            <option value="{{ $opt }}" {{ $dok['status_draf'] === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-gray-300 fw-bold fs-8 mb-1">Progres (%)</label>
                    <input type="number" name="progres_persen" id="progres_persen_input" min="0" max="100" value="{{ $dok['progres_persen'] }}" class="form-control form-control-sm form-control-solid fs-7" />
                </div>
                <div class="col-md-4">
                    <label class="form-label text-gray-300 fw-bold fs-8 mb-1">Ringkasan Arahan Akhir (Opsional)</label>
                    <input type="text" name="catatan_akhir_dosen" id="catatan_akhir_input" value="{{ $dok['catatan_akhir_dosen'] }}" class="form-control form-control-sm form-control-solid fs-7" placeholder="Ringkasan status revisi untuk mahasiswa..." />
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-sm btn-primary fw-bolder py-2" id="btn_save_status_draf">
                        <i class="fas fa-save me-1"></i> Simpan Status
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Workspace Utama: Kiri = Dokumen yang Bisa Dikomen Langsung | Kanan = Komentar Langsung & Kolom Diskusi Dua Arah -->
@include('partials.skripsi_dokumen_viewer', [
    'roleMode' => 'dosen',
    'dokumen' => $dok,
    'sections' => $workspace['sections'],
    'komentars' => $workspace['komentars'],
    'storeKomentarUrl' => route('dosen.bimbingan.skripsi.komentar.store', $dokumenId),
    'resolveUrlPattern' => route('dosen.bimbingan.skripsi.komentar.resolve', ['dokumen' => $dokumenId, 'komentar' => '__COMMENT_ID__']),
    'downloadUrl' => route('dosen.bimbingan.skripsi.download', $dokumenId),
    'previewUrl' => route('dosen.bimbingan.skripsi.preview', $dokumenId),
    'currentUserName' => $dosen['nama'] ?? auth()->user()?->name,
    'rightSlotPartial' => 'partials.skripsi_diskusi_panel',
    'rightSlotData' => [
        'roleMode' => 'dosen',
        'diskusi' => $workspace['diskusi'],
        'storeDiskusiUrl' => route('dosen.bimbingan.skripsi.diskusi.store', $dokumenId),
        'panelTitle' => 'Kolom Diskusi Dua Arah',
        'panelSubtitle' => 'Pesan langsung antara '.$dosen['nama'].' & '.$dok['mahasiswa_nama'],
    ],
])
@endsection

@push('scripts')
<script>
    async function submitStatusUpdate(e) {
        e.preventDefault();
        const btn = document.getElementById('btn_save_status_draf');
        const payload = {
            status_draf: document.getElementById('status_draf_input').value,
            progres_persen: Number(document.getElementById('progres_persen_input').value),
            catatan_akhir_dosen: document.getElementById('catatan_akhir_input').value,
        };

        if (btn) btn.disabled = true;
        try {
            const res = await fetch(e.target.action, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify(payload),
            });
            if (!res.ok) throw new Error('Gagal memperbarui status.');
            const data = await res.json();

            const topBadge = document.getElementById('top_status_draf_badge');
            const docBadge = document.getElementById('doc_header_status');
            const topProgres = document.getElementById('top_progres_label');

            if (topBadge) {
                topBadge.className = 'badge badge-light-' + data.badge_status + ' fw-bolder px-3 py-1 fs-8';
                topBadge.textContent = data.status_draf;
            }
            if (docBadge) {
                docBadge.className = 'badge badge-light-' + data.badge_status + ' fw-bolder fs-9 ms-1';
                docBadge.textContent = data.status_draf;
            }
            if (topProgres) {
                topProgres.textContent = data.progres_persen + '%';
            }

            if (window.Swal) {
                Swal.fire({
                    icon: 'success',
                    title: 'Status Review Disimpan',
                    text: 'Status draf diperbarui ke "' + data.status_draf + '" dan tersinkronisasi ke portal mahasiswa.',
                    timer: 1800,
                    showConfirmButton: false,
                });
            }
        } catch (err) {
            e.target.submit();
        } finally {
            if (btn) btn.disabled = false;
        }
    }
</script>
@endpush

