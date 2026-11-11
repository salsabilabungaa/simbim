<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\KonsultasiAkademik;
use App\Models\PerwalianMahasiswa;
use App\Models\SkripsiDokumen;
use App\Models\User;
use Illuminate\Support\Collection;

abstract class BaseMahasiswaController extends Controller
{
    /**
     * Tentukan apakah pengguna merupakan akun mahasiswa nyata yang tersimpan di basis data.
     */
    protected function isPersistedStudent(?User $user): bool
    {
        return $user !== null && $user->exists && $user->isMahasiswa();
    }

    /**
     * Ambil data profil mahasiswa aktif beserta rekaman perwalian dan skripsi dari basis data.
     *
     * @return array{
     *     mahasiswa: array<string, mixed>,
     *     perwalian: ?PerwalianMahasiswa,
     *     dokumen: ?SkripsiDokumen,
     *     is_persisted_student: bool
     * }
     */
    protected function resolveMahasiswaContext(?User $user): array
    {
        $isPersistedStudent = $this->isPersistedStudent($user);
        $perwalian = null;
        $dokumen = null;

        if ($user?->exists) {
            try {
                $user->loadMissing('dosenWali:id,name,nomor_induk,email');

                $perwalian = PerwalianMahasiswa::with('dosenWali')
                    ->where('mahasiswa_id', $user->id)
                    ->latest('updated_at')
                    ->first();

                $dokumen = SkripsiDokumen::with([
                    'dosenPembimbing',
                    'mahasiswa',
                    'komentars.user',
                    'komentars.balasan.user',
                    'diskusiPesans.user',
                ])
                    ->where('mahasiswa_id', $user->id)
                    ->latest('updated_at')
                    ->first();
            } catch (\Throwable) {
                $perwalian = null;
                $dokumen = null;
            }
        }

        if ($perwalian !== null) {
            $statusKrsText = match ($perwalian->status_perwalian) {
                'selesai' => 'Disetujui ('.(int) ($perwalian->sks_diambil ?: 0).' SKS)',
                'proses' => 'Proses Konsultasi / Revisi KRS',
                default => 'Belum Mengisi KRS',
            };
            $semesterLabel = 'Semester '.$perwalian->semester_berjalan;
            $angkatanLabel = (string) $perwalian->angkatan;
            $ipkVal = $perwalian->ipk !== null ? (float) $perwalian->ipk : 0.0;
            $totalSks = (int) $perwalian->sks_diambil;
        } elseif ($isPersistedStudent) {
            $statusKrsText = 'Belum Mengisi KRS';
            $semesterLabel = 'Belum Perwalian';
            $angkatanLabel = (string) now()->year;
            $ipkVal = 0.0;
            $totalSks = 0;
        } else {
            $statusKrsText = 'Disetujui (21 SKS)';
            $semesterLabel = 'Semester 7';
            $angkatanLabel = '2021';
            $ipkVal = 3.82;
            $totalSks = 118;
        }

        $pembimbingNama = $dokumen?->dosenPembimbing?->name
            ?? ($isPersistedStudent ? 'Belum Ditetapkan' : 'Dr. Siti Rahmawati, M.Kom.');

        return [
            'mahasiswa' => [
                'nama' => $user?->name ?? 'Andi Pratama',
                'nim' => $user?->nomor_induk ?: ($isPersistedStudent ? '-' : '2108107010001'),
                'status' => 'Mahasiswa Aktif',
                'program_studi' => $perwalian?->program_studi ?: ($user?->program_studi ?: (string) config('simbim.prodi')),
                'semester' => $semesterLabel,
                'angkatan' => $angkatanLabel,
                'ipk' => $ipkVal,
                'has_perwalian' => $perwalian !== null,
                'total_sks' => $totalSks,
                'status_krs' => $statusKrsText,
                'pembimbing' => $pembimbingNama,
                'sapaan' => 'Selamat Pagi',
            ],
            'perwalian' => $perwalian,
            'dokumen' => $dokumen,
            'is_persisted_student' => $isPersistedStudent,
        ];
    }

    /**
     * Susun profil dosen wali akademik terintegrasi dengan basis data dan Prodi PSTI — UPI.
     *
     * @return array<string, mixed>
     */
    protected function resolveDosenWali(?PerwalianMahasiswa $perwalian, bool $isPersistedStudent = false, ?User $user = null): array
    {
        $wali = $perwalian?->dosenWali ?? $user?->dosenWali;

        if ($wali !== null) {
            return [
                'is_assigned' => true,
                'nama' => $wali->name,
                'nip' => $wali->nomor_induk ?: '-',
                'jabatan' => 'Dosen Wali Akademik — '.config('simbim.prodi_singkat').' '.config('simbim.universitas_singkat'),
                'bidang' => 'Program Studi '.config('simbim.prodi'),
                'email' => $wali->email ?: '',
                'telepon' => '-',
                'ruangan' => (string) config('simbim.ruang_prodi'),
                'status_hadir' => 'Aktif Membimbing',
                'badge_status' => 'success',
            ];
        }

        if ($isPersistedStudent) {
            return [
                'is_assigned' => false,
                'nama' => 'Belum Ditetapkan',
                'nip' => '-',
                'jabatan' => 'Dosen Wali Akademik — '.config('simbim.prodi_singkat').' '.config('simbim.universitas_singkat'),
                'bidang' => 'Menunggu pengajuan perwalian atau penetapan Dosen Wali Prodi PSTI',
                'email' => '',
                'telepon' => '-',
                'ruangan' => (string) config('simbim.ruang_prodi'),
                'status_hadir' => 'Belum Ditentukan',
                'badge_status' => 'secondary',
            ];
        }

        return [
            'is_assigned' => true,
            'nama' => 'Prof. Dr. Ir. Ahmad Rizal, M.T.',
            'nip' => '19780512 200312 1 002',
            'jabatan' => 'Dosen Wali Akademik — '.config('simbim.prodi_singkat').' '.config('simbim.universitas_singkat'),
            'bidang' => 'Distributed Systems & Cloud Computing',
            'email' => 'ahmad.rizal@'.config('simbim.email_domain'),
            'telepon' => '+62 811-6800-4321',
            'ruangan' => (string) config('simbim.ruang_prodi').' R.302',
            'status_hadir' => 'Tersedia di Kampus',
            'badge_status' => 'success',
        ];
    }

    /**
     * Susun profil dosen pembimbing skripsi Prodi PSTI — UPI.
     *
     * @return array<string, mixed>
     */
    protected function resolveDosenPembimbing(
        bool $skripsiUtama = false,
        ?SkripsiDokumen $dokumen = null,
        bool $isPersistedStudent = false
    ): array {
        $pembimbing = $dokumen?->dosenPembimbing;

        if ($pembimbing !== null) {
            return [
                'is_assigned' => true,
                'nama' => $pembimbing->name,
                'nip' => $pembimbing->nomor_induk ?: '-',
                'jabatan' => 'Dosen Pembimbing — '.($dokumen?->peran_dosen ?: 'Pembimbing Utama'),
                'bidang' => 'Program Studi '.config('simbim.prodi'),
                'email' => $pembimbing->email ?: '',
                'telepon' => '-',
                'ruangan' => (string) config('simbim.ruang_prodi'),
                'status_hadir' => 'Aktif Membimbing',
                'badge_status' => 'success',
            ];
        }

        if ($isPersistedStudent) {
            return [
                'is_assigned' => false,
                'nama' => 'Belum Ditetapkan',
                'nip' => '-',
                'jabatan' => 'Dosen Pembimbing Skripsi — '.config('simbim.prodi_singkat').' '.config('simbim.universitas_singkat'),
                'bidang' => 'Belum ada draf skripsi atau pembimbing yang ditugaskan',
                'email' => '',
                'telepon' => '-',
                'ruangan' => (string) config('simbim.ruang_prodi'),
                'status_hadir' => 'Belum Ditentukan',
                'badge_status' => 'secondary',
            ];
        }

        if ($skripsiUtama) {
            return [
                'is_assigned' => true,
                'nama' => 'Dr. Ir. Teuku Muhammad, M.T.',
                'nip' => '19810314 200604 1 003',
                'jabatan' => 'Lektor Kepala — Pembimbing Utama',
                'bidang' => 'Software Engineering & Cloud Infrastructure',
                'email' => 'teuku.muhammad@'.config('simbim.email_domain'),
                'telepon' => '+62 812-6900-1122',
                'ruangan' => (string) config('simbim.ruang_prodi').' R.204',
                'status_hadir' => 'Tersedia di Kampus',
                'badge_status' => 'success',
            ];
        }

        return [
            'is_assigned' => true,
            'nama' => 'Dr. Siti Rahmawati, M.Kom.',
            'nip' => '19850315 201012 2 001',
            'jabatan' => 'Lektor Kepala — Pembimbing Utama',
            'bidang' => 'Software Engineering & Intelligence Systems',
            'email' => 'siti.rahmawati@'.config('simbim.email_domain'),
            'telepon' => '+62 812-6900-8821',
            'ruangan' => (string) config('simbim.ruang_prodi').' R.204',
            'status_hadir' => 'Tersedia di Kampus',
            'badge_status' => 'success',
        ];
    }

    /**
     * Ambil catatan konsultasi perwalian akademik mahasiswa dari database jika ada.
     *
     * @return Collection<int, KonsultasiAkademik>
     */
    protected function fetchMahasiswaKonsultasi(?PerwalianMahasiswa $perwalian, ?User $user = null): Collection
    {
        if (! $perwalian && ! $user?->exists) {
            return collect();
        }

        try {
            return KonsultasiAkademik::with('dosenWali')
                ->when(
                    $perwalian !== null,
                    fn ($q) => $q->where('perwalian_mahasiswa_id', $perwalian->id),
                    fn ($q) => $q->where('mahasiswa_id', $user?->id)
                )
                ->orderByDesc('tanggal_konsultasi')
                ->orderByDesc('id')
                ->get();
        } catch (\Throwable) {
            return collect();
        }
    }

    /**
     * Ambil daftar dosen Prodi PSTI untuk opsi pengajuan perwalian / skripsi mahasiswa baru.
     *
     * @return Collection<int, User>
     */
    protected function fetchDosenOptions(): Collection
    {
        try {
            return User::query()
                ->select(['id', 'name', 'nomor_induk', 'email'])
                ->where('role', 'dosen')
                ->orderBy('name')
                ->get();
        } catch (\Throwable) {
            return collect();
        }
    }
}
