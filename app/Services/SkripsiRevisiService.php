<?php

namespace App\Services;

use App\Models\SkripsiDiskusiPesan;
use App\Models\SkripsiDokumen;
use App\Models\SkripsiDokumenKomentar;
use App\Models\User;
use App\Support\DocumentTextExtractor;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SkripsiRevisiService
{
    public function __construct(
        protected DocumentTextExtractor $textExtractor
    ) {}

    /**
     * Susun data terstruktur dokumen, sorotan komentar langsung, dan kolom diskusi dua arah.
     *
     * @return array{
     *     dokumen: array<string, mixed>,
     *     sections: array<int, array{id: string, type: string, text: string, heading: string}>,
     *     komentars: array<int, array<string, mixed>>,
     *     diskusi: array<int, array<string, mixed>>
     * }
     */
    public function serializeWorkspace(SkripsiDokumen $dokumen): array
    {
        $dokumen->loadMissing([
            'mahasiswa:id,name,email,nomor_induk,program_studi',
            'dosenPembimbing:id,name,email,nomor_induk,program_studi',
            'komentars.user:id,name,role',
            'komentars.balasan.user:id,name,role',
            'diskusiPesans.user:id,name,role',
        ]);

        // Sinkronkan konten naskah otomatis dari berkas unggahan fisik apabila konten naskah kosong
        $effectiveKonten = (string) ($dokumen->konten_naskah ?? '');
        if (trim($effectiveKonten) === '' && ! empty($dokumen->file_path) && Storage::disk('local')->exists($dokumen->file_path)) {
            $ext = pathinfo((string) $dokumen->nama_file, PATHINFO_EXTENSION);
            $extracted = $this->textExtractor->extract(Storage::disk('local')->path($dokumen->file_path), $ext);
            if ($extracted !== null) {
                $effectiveKonten = $extracted;
                $dokumen->update(['konten_naskah' => $extracted]);
            }
        }

        $sections = $this->parseDocumentSections($effectiveKonten);

        $komentars = $dokumen->komentars->map(
            fn (SkripsiDokumenKomentar $komentar): array => $this->formatKomentar($komentar, $effectiveKonten)
        )->values()->all();

        $diskusi = $dokumen->diskusiPesans->map(
            fn (SkripsiDiskusiPesan $pesan): array => $this->formatDiskusiPesan($pesan)
        )->values()->all();

        return [
            'dokumen' => [
                'id' => $dokumen->id,
                'mahasiswa_id' => $dokumen->mahasiswa_id,
                'mahasiswa_nama' => $dokumen->mahasiswa?->name ?? 'Mahasiswa PSTI',
                'mahasiswa_nim' => $dokumen->mahasiswa?->nomor_induk ?? '-',
                'program_studi' => $dokumen->program_studi ?: (string) config('simbim.prodi'),
                'dosen_nama' => $dokumen->dosenPembimbing?->name ?? 'Dr. Siti Rahmawati, M.Kom.',
                'peran_dosen' => $dokumen->peran_dosen,
                'judul_skripsi' => $dokumen->judul_skripsi,
                'bab_aktif' => $dokumen->bab_aktif,
                'nama_file' => $dokumen->nama_file,
                'has_uploaded_file' => filled($dokumen->file_path) && Storage::disk('local')->exists((string) $dokumen->file_path),
                'is_pdf' => str_ends_with(strtolower((string) $dokumen->nama_file), '.pdf'),
                'versi' => $dokumen->versi,
                'progres_persen' => $dokumen->progres_persen,
                'status_draf' => $dokumen->status_draf,
                'badge_status' => $dokumen->badge_status ?: SkripsiDokumen::badgeForStatus($dokumen->status_draf),
                'jumlah_sesi' => $dokumen->jumlah_sesi,
                'konten_naskah' => $effectiveKonten,
                'catatan_akhir_dosen' => $dokumen->catatan_akhir_dosen,
                'updated_label' => $dokumen->updated_at?->translatedFormat('d M Y, H:i').' WIB',
            ],
            'sections' => $sections,
            'komentars' => $komentars,
            'diskusi' => $diskusi,
        ];
    }

    /**
     * Pecah konten naskah skripsi menjadi blok heading/paragraf untuk render gaya dokumen interaktif.
     *
     * @return array<int, array{id: string, type: string, text: string, heading: string}>
     */
    public function parseDocumentSections(?string $konten): array
    {
        $rawText = trim((string) $konten);
        if ($rawText === '') {
            return [];
        }

        $blocks = preg_split("/\r\n\r\n|\n\n|\r\r/", $rawText) ?: [$rawText];
        $sections = [];
        $currentHeading = 'Bagian Utama';

        foreach (array_values($blocks) as $idx => $block) {
            $trimmed = trim($block);
            if ($trimmed === '') {
                continue;
            }

            $lines = preg_split("/\r\n|\n|\r/", $trimmed) ?: [$trimmed];
            $firstLine = trim((string) ($lines[0] ?? ''));

            if (str_starts_with($firstLine, '### ')) {
                $subHeading = trim(substr($firstLine, 4));
                $currentHeading = $subHeading;
                $sections[] = [
                    'id' => 'sec-'.($idx + 1).'-h3',
                    'type' => 'h3',
                    'text' => $subHeading,
                    'heading' => $currentHeading,
                ];
                $remaining = trim(implode("\n", array_slice($lines, 1)));
                if ($remaining !== '') {
                    $sections[] = [
                        'id' => 'sec-'.($idx + 1).'-p',
                        'type' => 'p',
                        'text' => $remaining,
                        'heading' => $currentHeading,
                    ];
                }

                continue;
            }

            if (str_starts_with($firstLine, '## ')) {
                $mainHeading = trim(substr($firstLine, 3));
                $currentHeading = $mainHeading;
                $sections[] = [
                    'id' => 'sec-'.($idx + 1).'-h2',
                    'type' => 'h2',
                    'text' => $mainHeading,
                    'heading' => $currentHeading,
                ];
                $remaining = trim(implode("\n", array_slice($lines, 1)));
                if ($remaining !== '') {
                    $sections[] = [
                        'id' => 'sec-'.($idx + 1).'-p',
                        'type' => 'p',
                        'text' => $remaining,
                        'heading' => $currentHeading,
                    ];
                }

                continue;
            }

            $sections[] = [
                'id' => 'sec-'.($idx + 1),
                'type' => 'p',
                'text' => $trimmed,
                'heading' => $currentHeading,
            ];
        }

        return $sections;
    }

    /**
     * Format satu komentar dokumen beserta balasannya.
     *
     * @return array<string, mixed>
     */
    public function formatKomentar(SkripsiDokumenKomentar $komentar, ?string $dokumenText = null): array
    {
        $komentar->loadMissing(['user:id,name,role', 'balasan.user:id,name,role']);

        $isDosen = $komentar->peran_pengirim === 'dosen';
        $quote = trim((string) ($komentar->kutipan_teks ?? ''));
        $docBody = $dokumenText ?? (string) ($komentar->dokumen?->konten_naskah ?? '');
        $quoteInDoc = $quote !== '' && $docBody !== '' && str_contains($docBody, $quote);

        return [
            'id' => $komentar->id,
            'user_id' => $komentar->user_id,
            'parent_id' => $komentar->parent_id,
            'pengirim' => $komentar->user?->name ?? ($isDosen ? 'Dosen Pembimbing' : 'Mahasiswa'),
            'peran_pengirim' => $komentar->peran_pengirim,
            'role_label' => $isDosen ? 'Dosen Pembimbing' : 'Mahasiswa',
            'badge_role' => $isDosen ? 'primary' : 'info',
            'lokasi_dokumen' => $komentar->lokasi_dokumen,
            'kutipan_teks' => $komentar->kutipan_teks,
            'quote_in_doc' => $quoteInDoc,
            'isi_komentar' => $komentar->isi_komentar,
            'status' => $komentar->is_resolved ? 'Selesai' : $komentar->status,
            'badge' => $komentar->is_resolved ? 'success' : SkripsiDokumenKomentar::badgeForStatus($komentar->status),
            'is_resolved' => (bool) $komentar->is_resolved,
            'waktu' => $komentar->created_at?->translatedFormat('d M Y, H:i').' WIB',
            'balasan' => $komentar->balasan->map(function (SkripsiDokumenKomentar $reply): array {
                $replyIsDosen = $reply->peran_pengirim === 'dosen';

                return [
                    'id' => $reply->id,
                    'pengirim' => $reply->user?->name ?? ($replyIsDosen ? 'Dosen Pembimbing' : 'Mahasiswa'),
                    'peran_pengirim' => $reply->peran_pengirim,
                    'role_label' => $replyIsDosen ? 'Dosen Pembimbing' : 'Mahasiswa',
                    'badge_role' => $replyIsDosen ? 'primary' : 'info',
                    'isi_komentar' => $reply->isi_komentar,
                    'waktu' => $reply->created_at?->translatedFormat('d M Y, H:i').' WIB',
                ];
            })->values()->all(),
        ];
    }

    /**
     * Format satu pesan kolom diskusi dua arah.
     *
     * @return array<string, mixed>
     */
    public function formatDiskusiPesan(SkripsiDiskusiPesan $pesan): array
    {
        $pesan->loadMissing('user:id,name,role');
        $isDosen = $pesan->peran_pengirim === 'dosen';

        return [
            'id' => $pesan->id,
            'user_id' => $pesan->user_id,
            'pengirim' => $pesan->user?->name ?? ($isDosen ? 'Dosen Pembimbing' : 'Mahasiswa'),
            'peran_pengirim' => $pesan->peran_pengirim,
            'role' => $isDosen ? 'Dosen Pembimbing' : 'Mahasiswa',
            'badge_role' => $isDosen ? 'primary' : 'info',
            'pesan' => $pesan->pesan,
            'referensi_komentar_id' => $pesan->referensi_komentar_id,
            'waktu' => $pesan->created_at?->translatedFormat('d M Y, H:i').' WIB',
        ];
    }

    /**
     * Simpan komentar langsung atau balasan komentar pada dokumen skripsi.
     *
     * @param  array{lokasi_dokumen?: ?string, kutipan_teks?: ?string, isi_komentar: string, status?: ?string, parent_id?: ?int}  $data
     */
    public function storeKomentar(SkripsiDokumen $dokumen, User $user, array $data): SkripsiDokumenKomentar
    {
        $peran = ($user->isDosen() || $user->isAdmin()) ? 'dosen' : 'mahasiswa';
        $parentId = ! empty($data['parent_id']) ? (int) $data['parent_id'] : null;

        if ($parentId !== null) {
            $parentExists = $dokumen->komentars()->where('id', $parentId)->exists();
            if (! $parentExists) {
                $parentId = null;
            }
        }

        $status = $data['status'] ?? ($peran === 'dosen' ? 'Perlu Revisi' : 'Catatan');
        $isResolved = in_array($status, ['Disetujui', 'Selesai'], true);

        $komentar = SkripsiDokumenKomentar::create([
            'skripsi_dokumen_id' => $dokumen->id,
            'user_id' => $user->id,
            'parent_id' => $parentId,
            'peran_pengirim' => $peran,
            'lokasi_dokumen' => trim((string) ($data['lokasi_dokumen'] ?? 'Bagian Terpilih Dokumen')) ?: 'Bagian Terpilih Dokumen',
            'kutipan_teks' => ! empty($data['kutipan_teks']) ? trim((string) $data['kutipan_teks']) : null,
            'isi_komentar' => trim($data['isi_komentar']),
            'status' => $status,
            'is_resolved' => $isResolved,
        ]);

        $dokumen->touch();

        return $komentar;
    }

    /**
     * Ubah status selesai/buka kembali suatu komentar dokumen.
     */
    public function toggleResolveKomentar(SkripsiDokumen $dokumen, SkripsiDokumenKomentar $komentar): SkripsiDokumenKomentar
    {
        abort_if((int) $komentar->skripsi_dokumen_id !== (int) $dokumen->id, 404);

        $nextResolved = ! $komentar->is_resolved;
        $komentar->update([
            'is_resolved' => $nextResolved,
            'status' => $nextResolved ? 'Selesai' : 'Perlu Revisi',
        ]);

        $dokumen->touch();

        return $komentar->fresh();
    }

    /**
     * Simpan pesan diskusi dua arah antara dosen pembimbing dan mahasiswa.
     *
     * @param  array{pesan: string, referensi_komentar_id?: ?int}  $data
     */
    public function storeDiskusiPesan(SkripsiDokumen $dokumen, User $user, array $data): SkripsiDiskusiPesan
    {
        $peran = ($user->isDosen() || $user->isAdmin()) ? 'dosen' : 'mahasiswa';
        $refId = ! empty($data['referensi_komentar_id']) ? (int) $data['referensi_komentar_id'] : null;

        if ($refId !== null) {
            $refExists = $dokumen->semuaKomentars()->where('id', $refId)->exists();
            if (! $refExists) {
                $refId = null;
            }
        }

        $pesan = SkripsiDiskusiPesan::create([
            'skripsi_dokumen_id' => $dokumen->id,
            'user_id' => $user->id,
            'referensi_komentar_id' => $refId,
            'peran_pengirim' => $peran,
            'pesan' => trim($data['pesan']),
        ]);

        $dokumen->touch();

        return $pesan;
    }

    /**
     * Perbarui keputusan/status review draf oleh dosen pembimbing.
     *
     * @param  array{status_draf: string, progres_persen?: ?int, catatan_akhir_dosen?: ?string}  $data
     */
    public function updateDraftStatus(SkripsiDokumen $dokumen, array $data): SkripsiDokumen
    {
        $dokumen->update([
            'status_draf' => $data['status_draf'],
            'badge_status' => SkripsiDokumen::badgeForStatus($data['status_draf']),
            'progres_persen' => isset($data['progres_persen']) ? (int) $data['progres_persen'] : $dokumen->progres_persen,
            'catatan_akhir_dosen' => array_key_exists('catatan_akhir_dosen', $data)
                ? $data['catatan_akhir_dosen']
                : $dokumen->catatan_akhir_dosen,
        ]);

        return $dokumen->fresh();
    }

    /**
     * Buat entri draf awal skripsi untuk mahasiswa baru agar langsung tersinkronisasi dengan dosen pembimbing.
     *
     * @param  array{judul_skripsi: string, dosen_pembimbing_id?: ?int, bab_aktif?: ?string, detail_agenda?: ?string}  $data
     */
    public function createStudentInitialDraft(
        User $mahasiswa,
        array $data,
        ?UploadedFile $file = null
    ): SkripsiDokumen {
        $prodi = $mahasiswa->program_studi ?: (string) config('simbim.prodi', 'Pendidikan Sistem dan Teknologi Informasi (PSTI)');
        $detailAgenda = isset($data['detail_agenda']) ? trim((string) $data['detail_agenda']) : '';
        $namaFile = 'Pengajuan_Draf_Awal.txt';
        $filePath = null;
        $kontenNaskah = $detailAgenda !== '' ? $detailAgenda : null;

        if ($file !== null) {
            $filePath = Storage::disk('local')->putFile('skripsi_drafts', $file);
            $namaFile = $file->getClientOriginalName();
            $extracted = $this->textExtractor->extract($file);
            $kontenNaskah = $extracted;
        }

        return SkripsiDokumen::create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_pembimbing_id' => ! empty($data['dosen_pembimbing_id']) ? (int) $data['dosen_pembimbing_id'] : null,
            'program_studi' => $prodi,
            'peran_dosen' => 'Pembimbing Utama',
            'judul_skripsi' => trim($data['judul_skripsi']),
            'bab_aktif' => trim((string) ($data['bab_aktif'] ?? 'Bab I (Pendahuluan)')) ?: 'Bab I (Pendahuluan)',
            'nama_file' => $namaFile,
            'file_path' => $filePath,
            'versi' => 'v1.0',
            'progres_persen' => $file !== null ? 20 : 10,
            'status_draf' => 'Menunggu Review Dosen',
            'badge_status' => SkripsiDokumen::badgeForStatus('Menunggu Review Dosen'),
            'jumlah_sesi' => 1,
            'konten_naskah' => $kontenNaskah,
            'catatan_akhir_dosen' => $detailAgenda !== '' ? $detailAgenda : null,
        ]);
    }

    /**
     * Simpan unggahan revisi berkas atau teks naskah terbaru dari mahasiswa agar langsung tersinkronisasi ke dosen.
     */
    public function storeStudentRevisionUpload(
        SkripsiDokumen $dokumen,
        ?UploadedFile $file,
        ?string $kontenNaskah,
        ?string $babAktif
    ): SkripsiDokumen {
        $updates = [
            'status_draf' => 'Menunggu Review Dosen',
            'badge_status' => SkripsiDokumen::badgeForStatus('Menunggu Review Dosen'),
            'versi' => $this->incrementVersion($dokumen->versi),
        ];

        if (! empty($babAktif)) {
            $updates['bab_aktif'] = trim($babAktif);
        }

        if ($file !== null) {
            $storedPath = Storage::disk('local')->putFile('skripsi_drafts', $file);
            $updates['nama_file'] = $file->getClientOriginalName();
            $updates['file_path'] = $storedPath;

            if ($kontenNaskah !== null && trim($kontenNaskah) !== '') {
                $updates['konten_naskah'] = trim($kontenNaskah);
            } else {
                // Prioritaskan ekstraksi teks dari berkas yang diunggah mahasiswa agar naskah selalu sinkron
                $extracted = $this->textExtractor->extract($file);
                $updates['konten_naskah'] = $extracted;
            }
        } elseif ($kontenNaskah !== null && trim($kontenNaskah) !== '') {
            $updates['konten_naskah'] = trim($kontenNaskah);
        }

        $dokumen->update($updates);

        return $dokumen->fresh();
    }

    protected function incrementVersion(string $currentVersion): string
    {
        if (preg_match('/^v(\d+)\.(\d+)$/i', trim($currentVersion), $matches)) {
            return 'v'.$matches[1].'.'.((int) $matches[2] + 1);
        }

        return 'v1.1';
    }
}
