<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\KonsultasiAkademik;
use App\Models\PerwalianMahasiswa;
use App\Models\SkripsiDokumen;
use App\Models\User;
use Illuminate\Support\Collection;

abstract class BaseDosenController extends Controller
{
    /**
     * Susun data profil dosen aktif yang terintegrasi dengan Prodi PSTI — UPI.
     *
     * @return array<string, string>
     */
    protected function resolveDosenProfile(?User $user): array
    {
        return [
            'nama' => $user?->name ?? 'Dr. Siti Rahmawati, M.Kom.',
            'nip' => $user?->nomor_induk ?? '198503152010122001',
            'jabatan' => 'Lektor Kepala — Pembimbing Utama',
            'program_studi' => $user?->program_studi ?: (string) config('simbim.prodi'),
            'bidang' => 'Software Engineering & Intelligence Systems',
            'email' => $user?->email ?? 'siti.rahmawati@'.config('simbim.email_domain'),
            'telepon' => '+62 812-6900-8821',
            'ruangan' => (string) config('simbim.ruang_prodi'),
            'status_hadir' => 'Tersedia di Kampus',
        ];
    }

    /**
     * Daftar notifikasi standar pada portal dosen.
     *
     * @return array<int, array<string, string>>
     */
    protected function defaultNotifikasi(): array
    {
        return [
            [
                'judul' => 'Draf Baru Menunggu Review',
                'deskripsi' => 'Andi Pratama mengunggah revisi Bab III',
                'waktu' => '30 menit yang lalu',
                'badge' => 'primary',
                'icon' => 'fas fa-file-alt',
            ],
            [
                'judul' => 'Pengajuan Bimbingan Baru',
                'deskripsi' => 'Cut Nadia meminta jadwal konsultasi',
                'waktu' => '2 jam yang lalu',
                'badge' => 'warning',
                'icon' => 'fas fa-calendar-alt',
            ],
        ];
    }

    /**
     * Ambil daftar mahasiswa perwalian langsung dari basis data apabila dosen tersimpan di database.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function fetchDatabasePerwalian(?User $user): array
    {
        if (! $user?->exists) {
            return [];
        }

        try {
            /** @var Collection<int, PerwalianMahasiswa> $records */
            $records = PerwalianMahasiswa::with('mahasiswa')
                ->where('dosen_wali_id', $user->id)
                ->orderBy('angkatan')
                ->get();

            if ($records->isEmpty()) {
                return [];
            }

            return $records->map(function (PerwalianMahasiswa $record): array {
                [$statusLabel, $badge] = match ($record->status_perwalian) {
                    'selesai' => ['KRS Disetujui', 'success'],
                    'proses' => ['Perlu Konsultasi SKS', 'warning'],
                    default => ['Belum Mengajukan KRS', 'danger'],
                };

                return [
                    'nama' => $record->mahasiswa?->name ?? 'Mahasiswa PSTI',
                    'nim' => $record->mahasiswa?->nomor_induk ?? '-',
                    'angkatan' => (string) $record->angkatan,
                    'semester' => 'Semester '.$record->semester_berjalan,
                    'ipk' => $record->ipk !== null ? number_format((float) $record->ipk, 2) : '-',
                    'sks_lulus' => (int) $record->sks_diambil,
                    'status_krs' => $statusLabel,
                    'badge_status' => $badge,
                    'program_studi' => $record->program_studi ?: (string) config('simbim.prodi'),
                ];
            })->all();
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Ambil log konsultasi perwalian tersinkronisasi dari database untuk ditampilkan di riwayat dosen.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function fetchDatabaseKonsultasi(?User $user): array
    {
        if (! $user?->exists) {
            return [];
        }

        try {
            /** @var Collection<int, KonsultasiAkademik> $logs */
            $logs = KonsultasiAkademik::with(['mahasiswa', 'perwalianMahasiswa'])
                ->where('dosen_wali_id', $user->id)
                ->orderByDesc('tanggal_konsultasi')
                ->get();

            if ($logs->isEmpty()) {
                return [];
            }

            return $logs->values()->map(function (KonsultasiAkademik $log, int $idx): array {
                [$statusText, $badgeStatus] = match ($log->status_sesi) {
                    'ACC KRS/IRS' => ['ACC', 'success'],
                    'Perlu Tindak Lanjut' => ['Revisi', 'warning'],
                    default => ['Selesai', 'info'],
                };
                $perwalian = $log->perwalianMahasiswa;

                return [
                    'id' => 1000 + $idx,
                    'tanggal' => $log->tanggal_konsultasi?->translatedFormat('l, d M Y') ?? '-',
                    'waktu' => $log->waktu_konsultasi ?: '09:30 - 10:30 WIB',
                    'mahasiswa' => $log->mahasiswa?->name ?? 'Mahasiswa PSTI',
                    'nim' => $log->mahasiswa?->nomor_induk ?? '-',
                    'angkatan' => (string) ($perwalian?->angkatan ?? '2022'),
                    'prodi' => $perwalian?->program_studi ?: (string) config('simbim.prodi'),
                    'kategori' => 'Akademik',
                    'kategori_key' => 'akademik',
                    'badge_kategori' => 'success',
                    'peran_dosen' => 'Dosen Pembimbing Akademik',
                    'bab' => 'Semester '.($perwalian?->semester_berjalan ?? '-').' ('.($perwalian?->sks_diambil ?? 0).' SKS)',
                    'topik' => $log->topik,
                    'catatan' => $log->catatan_dosen_wali ?: ($log->catatan_mahasiswa ?? '-'),
                    'metode' => $log->metode ?: 'Luring ('.config('simbim.ruang_prodi').')',
                    'status' => $statusText,
                    'badge_status' => $badgeStatus,
                    'dokumen' => $log->lampiran_krs ?: 'Log_Perwalian_'.$log->id.'.pdf',
                    'foto' => 'konsultasi_perwalian.jpg',
                ];
            })->all();
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Ambil daftar draf skripsi mahasiswa bimbingan dosen pembimbing dari basis data.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function fetchDatabaseSkripsiBimbingan(?User $user): array
    {
        if (! $user?->exists) {
            return [];
        }

        try {
            $query = SkripsiDokumen::with('mahasiswa:id,name,nomor_induk,program_studi')
                ->withCount(['komentars', 'diskusiPesans'])
                ->orderByDesc('updated_at');

            if (! $user->isAdmin()) {
                $query->where('dosen_pembimbing_id', $user->id);
            }

            /** @var Collection<int, SkripsiDokumen> $records */
            $records = $query->get();

            if ($records->isEmpty()) {
                return [];
            }

            return $records->map(function (SkripsiDokumen $dok): array {
                $nim = (string) ($dok->mahasiswa?->nomor_induk ?? '2108107010001');
                $angkatan = strlen($nim) >= 2 && is_numeric(substr($nim, 0, 2))
                    ? '20'.substr($nim, 0, 2)
                    : '2021';

                return [
                    'id' => $dok->id,
                    'dokumen_id' => $dok->id,
                    'nama' => $dok->mahasiswa?->name ?? 'Mahasiswa PSTI',
                    'nim' => $nim,
                    'angkatan' => $angkatan,
                    'program_studi' => $dok->program_studi ?: (string) config('simbim.prodi'),
                    'jenis_bimbingan' => 'skripsi',
                    'kategori_label' => 'Bimbingan Skripsi',
                    'peran_dosen' => $dok->peran_dosen ?: 'Pembimbing Utama',
                    'judul' => $dok->judul_skripsi,
                    'bab' => $dok->bab_aktif,
                    'bab_aktif' => $dok->bab_aktif,
                    'progres' => (int) $dok->progres_persen,
                    'status_draf' => $dok->status_draf,
                    'badge_status' => $dok->badge_status ?: SkripsiDokumen::badgeForStatus($dok->status_draf),
                    'jumlah_sesi' => (int) $dok->jumlah_sesi,
                    'tanggal_update' => $dok->updated_at?->translatedFormat('d M Y') ?? '-',
                    'berkas_draf' => $dok->nama_file,
                    'versi' => $dok->versi,
                    'komentars_count' => (int) $dok->komentars_count,
                    'diskusi_pesans_count' => (int) $dok->diskusi_pesans_count,
                ];
            })->all();
        } catch (\Throwable) {
            return [];
        }
    }
}
