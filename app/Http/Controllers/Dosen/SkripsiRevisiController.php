<?php

namespace App\Http\Controllers\Dosen;

use App\Models\SkripsiDokumen;
use App\Models\SkripsiDokumenKomentar;
use App\Models\User;
use App\Services\SkripsiRevisiService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class SkripsiRevisiController extends BaseDosenController
{
    public function __construct(
        protected SkripsiRevisiService $revisiService
    ) {}

    /**
     * Tampilkan ruang sesi diskusi revisi skripsi mahasiswa: dokumen yang bisa dikomentari langsung + diskusi dua arah.
     */
    public function show(SkripsiDokumen $dokumen): View
    {
        $user = Auth::user();
        $this->authorizeDosenAccess($user, $dokumen);

        return view('dosen.bimbingan.diskusi_revisi', [
            'dosen' => $this->resolveDosenProfile($user),
            'notifikasi' => $this->defaultNotifikasi(),
            'workspace' => $this->revisiService->serializeWorkspace($dokumen),
        ]);
    }

    /**
     * Simpan komentar langsung dosen pembimbing pada potongan teks atau bagian dokumen.
     */
    public function storeKomentar(Request $request, SkripsiDokumen $dokumen): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        $this->authorizeDosenAccess($user, $dokumen);

        $validated = $request->validate([
            'lokasi_dokumen' => ['nullable', 'string', 'max:150'],
            'kutipan_teks' => ['nullable', 'string', 'max:2000'],
            'isi_komentar' => ['required', 'string', 'max:3000'],
            'status' => ['nullable', Rule::in(['Perlu Revisi', 'Disetujui', 'Catatan', 'Selesai'])],
            'parent_id' => ['nullable', 'integer'],
        ], [
            'isi_komentar.required' => 'Isi komentar dokumen wajib diisi.',
        ]);

        $komentar = $this->revisiService->storeKomentar($dokumen, $user, $validated);
        $formatted = $this->revisiService->formatKomentar($komentar);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Komentar dokumen berhasil ditambahkan.',
                'komentar' => $formatted,
            ], 201);
        }

        return back()->with('success', 'Komentar dokumen berhasil disimpan dan tersinkronisasi ke mahasiswa.');
    }

    /**
     * Tandai komentar selesai / buka kembali oleh dosen pembimbing.
     */
    public function toggleResolveKomentar(
        Request $request,
        SkripsiDokumen $dokumen,
        SkripsiDokumenKomentar $komentar
    ): JsonResponse|RedirectResponse {
        $user = Auth::user();
        $this->authorizeDosenAccess($user, $dokumen);

        $updated = $this->revisiService->toggleResolveKomentar($dokumen, $komentar);
        $formatted = $this->revisiService->formatKomentar($updated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Status komentar berhasil diperbarui.',
                'komentar' => $formatted,
            ]);
        }

        return back()->with('success', 'Status komentar dokumen berhasil diperbarui.');
    }

    /**
     * Kirim pesan pada kolom diskusi dua arah antara dosen pembimbing dan mahasiswa.
     */
    public function storeDiskusi(Request $request, SkripsiDokumen $dokumen): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        $this->authorizeDosenAccess($user, $dokumen);

        $validated = $request->validate([
            'pesan' => ['required', 'string', 'max:2000'],
            'referensi_komentar_id' => ['nullable', 'integer'],
        ], [
            'pesan.required' => 'Pesan diskusi tidak boleh kosong.',
        ]);

        $pesan = $this->revisiService->storeDiskusiPesan($dokumen, $user, $validated);
        $formatted = $this->revisiService->formatDiskusiPesan($pesan);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Pesan diskusi berhasil dikirim.',
                'pesan' => $formatted,
            ], 201);
        }

        return back()->with('success', 'Pesan diskusi dua arah berhasil dikirim.');
    }

    /**
     * Perbarui keputusan review draf skripsi mahasiswa.
     */
    public function updateStatus(Request $request, SkripsiDokumen $dokumen): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        $this->authorizeDosenAccess($user, $dokumen);

        $validated = $request->validate([
            'status_draf' => [
                'required',
                Rule::in([
                    'Menunggu Review Dosen',
                    'Draf Sedang Direview',
                    'Revisi Diperlukan',
                    'Bimbingan Terjadwal',
                    'Siap Sidang Skripsi',
                ]),
            ],
            'progres_persen' => ['nullable', 'integer', 'min:0', 'max:100'],
            'catatan_akhir_dosen' => ['nullable', 'string', 'max:3000'],
        ]);

        $updated = $this->revisiService->updateDraftStatus($dokumen, $validated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Status review draf berhasil diperbarui.',
                'status_draf' => $updated->status_draf,
                'badge_status' => $updated->badge_status,
                'progres_persen' => $updated->progres_persen,
            ]);
        }

        return back()->with('success', 'Keputusan review draf berhasil disimpan.');
    }

    /**
     * Unduh berkas draf skripsi mahasiswa.
     */
    public function downloadFile(SkripsiDokumen $dokumen): Response
    {
        $this->authorizeDosenAccess(Auth::user(), $dokumen);

        if (! empty($dokumen->file_path) && Storage::disk('local')->exists($dokumen->file_path)) {
            return Storage::disk('local')->download($dokumen->file_path, $dokumen->nama_file);
        }

        $safeBase = pathinfo((string) $dokumen->nama_file, PATHINFO_FILENAME) ?: 'Draf_Skripsi';
        $content = $dokumen->judul_skripsi."\n\n".($dokumen->konten_naskah ?? '');

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$safeBase.'.txt"',
        ]);
    }

    /**
     * Tampilkan pratinjau PDF asli secara inline apabila mahasiswa mengunggah berkas PDF.
     */
    public function previewFile(SkripsiDokumen $dokumen): Response
    {
        $this->authorizeDosenAccess(Auth::user(), $dokumen);

        if (empty($dokumen->file_path) || ! Storage::disk('local')->exists($dokumen->file_path)) {
            abort(404);
        }

        return response()->file(Storage::disk('local')->path($dokumen->file_path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$dokumen->nama_file.'"',
        ]);
    }

    protected function authorizeDosenAccess(?User $user, SkripsiDokumen $dokumen): void
    {
        abort_if(! $user, 403);

        if ($user->isAdmin()) {
            return;
        }

        abort_if(
            (int) $dokumen->dosen_pembimbing_id !== (int) $user->id,
            403,
            'Anda tidak berwenang mengakses sesi revisi skripsi mahasiswa ini.'
        );
    }
}
