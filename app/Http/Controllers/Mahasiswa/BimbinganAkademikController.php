<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Models\KonsultasiAkademik;
use App\Models\PerwalianMahasiswa;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BimbinganAkademikController extends BaseMahasiswaController
{
    public function index(): View
    {
        $user = Auth::user();
        $context = $this->resolveMahasiswaContext($user);
        $mahasiswa = $context['mahasiswa'];
        $perwalian = $context['perwalian'];
        $dokumen = $context['dokumen'];
        $isPersistedStudent = $context['is_persisted_student'];

        $dbLogs = $this->fetchMahasiswaKonsultasi($perwalian, $user);

        if ($isPersistedStudent) {
            $riwayatBimbingan = $dbLogs->values()->map(function (KonsultasiAkademik $log, int $idx): array {
                [$statusLabel, $badge] = match ($log->status_sesi) {
                    'ACC KRS/IRS' => ['ACC', 'primary'],
                    'Perlu Tindak Lanjut' => ['Proses', 'warning'],
                    default => ['Selesai', 'success'],
                };

                return [
                    'id' => $idx + 1,
                    'tanggal' => $log->tanggal_konsultasi?->translatedFormat('d M Y'),
                    'waktu' => $log->waktu_konsultasi ?: '09.30 - 10.30 WIB',
                    'topik' => $log->topik,
                    'status' => $statusLabel,
                    'badge' => $badge,
                    'catatan' => $log->catatan_dosen_wali ?: ($log->catatan_mahasiswa ?? '-'),
                    'foto' => null,
                ];
            })->all();

            $catatanDosenWali = $dbLogs
                ->filter(fn (KonsultasiAkademik $log): bool => filled($log->catatan_dosen_wali))
                ->values()
                ->map(fn (KonsultasiAkademik $log): array => [
                    'tanggal' => $log->tanggal_konsultasi?->translatedFormat('d M Y') ?? '-',
                    'topik' => $log->topik,
                    'catatan' => $log->catatan_dosen_wali,
                    'badge' => match ($log->status_sesi) {
                        'ACC KRS/IRS' => 'primary',
                        'Perlu Tindak Lanjut' => 'warning',
                        default => 'success',
                    },
                ])->all();

            if ($catatanDosenWali === [] && ! empty($perwalian?->catatan_akhir_wali)) {
                $catatanDosenWali[] = [
                    'tanggal' => $perwalian->tanggal_persetujuan_krs?->translatedFormat('d M Y') ?? '-',
                    'topik' => 'Catatan Akhir Perwalian '.$perwalian->tahun_akademik,
                    'catatan' => $perwalian->catatan_akhir_wali,
                    'badge' => $perwalian->status_perwalian === 'selesai' ? 'success' : 'warning',
                ];
            }

            $latestLog = $dbLogs->first();
            $jadwalTerdekat = $latestLog !== null
                ? [
                    'judul' => $latestLog->topik,
                    'tanggal' => $latestLog->tanggal_konsultasi?->translatedFormat('l, d M Y') ?? 'Menunggu Konfirmasi',
                    'waktu' => $latestLog->waktu_konsultasi ?: '09.30 - 10.30 WIB',
                    'tempat' => $latestLog->metode ?: config('simbim.ruang_prodi'),
                    'metode' => $latestLog->metode ?: 'Luring / Tatap Muka',
                    'sisa' => $latestLog->status_sesi,
                    'status' => $latestLog->status_sesi,
                    'is_h1' => $latestLog->status_sesi === 'Perlu Tindak Lanjut',
                ]
                : [
                    'judul' => 'Belum ada jadwal konsultasi akademik',
                    'tanggal' => 'Belum Ada Jadwal',
                    'waktu' => '-',
                    'tempat' => (string) config('simbim.ruang_prodi'),
                    'metode' => '-',
                    'sisa' => 'Menunggu Pengajuan',
                    'status' => 'Belum Terjadwal',
                    'is_h1' => false,
                ];

            $notifikasi = $latestLog !== null
                ? [
                    [
                        'judul' => 'Status sesi terbaru: '.$latestLog->topik.' ('.$latestLog->status_sesi.')',
                        'waktu' => $latestLog->tanggal_konsultasi?->translatedFormat('d M Y') ?? 'Baru saja',
                    ],
                ]
                : [
                    [
                        'judul' => 'Belum ada pengajuan perwalian atau catatan konsultasi akademik',
                        'waktu' => 'Hari ini',
                    ],
                ];
        } else {
            $riwayatBimbingan = $dbLogs->isNotEmpty()
                ? $dbLogs->values()->map(function (KonsultasiAkademik $log, int $idx): array {
                    [$statusLabel, $badge] = match ($log->status_sesi) {
                        'ACC KRS/IRS' => ['ACC', 'primary'],
                        'Perlu Tindak Lanjut' => ['Proses', 'warning'],
                        default => ['Selesai', 'success'],
                    };

                    return [
                        'id' => $idx + 1,
                        'tanggal' => $log->tanggal_konsultasi?->translatedFormat('d M Y'),
                        'waktu' => $log->waktu_konsultasi ?: '09.30 - 10.30 WIB',
                        'topik' => $log->topik,
                        'status' => $statusLabel,
                        'badge' => $badge,
                        'catatan' => $log->catatan_dosen_wali ?: ($log->catatan_mahasiswa ?? '-'),
                        'foto' => null,
                    ];
                })->all()
                : [
                    [
                        'id' => 1,
                        'tanggal' => null,
                        'waktu' => null,
                        'topik' => 'Konsultasi KRS & Perencanaan Studi Semester',
                        'status' => 'Proses',
                        'badge' => 'warning',
                        'catatan' => 'Pengajuan baru dikirim. Menunggu Dosen Wali menentukan tanggal & tempat.',
                        'foto' => null,
                    ],
                    [
                        'id' => 2,
                        'tanggal' => '12 Okt 2026',
                        'waktu' => '10.00 - 11.30 WIB',
                        'topik' => 'Pengajuan Rekomendasi Magang / Program MBKM',
                        'status' => 'ACC',
                        'badge' => 'primary',
                        'catatan' => 'Jadwal bimbingan telah disetujui Dosen Wali. Harap hadir tepat waktu.',
                        'foto' => null,
                    ],
                    [
                        'id' => 3,
                        'tanggal' => '15 Sep 2026',
                        'waktu' => '14.00 - 15.00 WIB',
                        'topik' => 'Evaluasi Hasil Belajar & Peningkatan IPK',
                        'status' => 'Selesai',
                        'badge' => 'success',
                        'catatan' => 'Bimbingan selesai. Silakan unggah foto dokumentasi kegiatan bimbingan.',
                        'foto' => null,
                    ],
                    [
                        'id' => 4,
                        'tanggal' => '02 Agu 2026',
                        'waktu' => '09.30 - 10.30 WIB',
                        'topik' => 'Pengajuan Beasiswa & Surat Rekomendasi',
                        'status' => 'Selesai',
                        'badge' => 'success',
                        'catatan' => 'Bimbingan selesai & foto dokumentasi telah terverifikasi.',
                        'foto' => 'assets/media/svg/avatars/004-boy-1.svg',
                    ],
                ];

            $catatanDosenWali = [
                [
                    'tanggal' => '15 Sep 2026',
                    'topik' => 'Evaluasi Akademik & MBKM',
                    'catatan' => 'Pertahankan tren peningkatan IPK. Pastikan mata kuliah wajib kelulusan Prodi PSTI diambil sesuai rencana studi.',
                    'badge' => 'success',
                ],
                [
                    'tanggal' => '02 Agu 2026',
                    'topik' => 'Rekomendasi Beasiswa',
                    'catatan' => 'Mahasiswa memenuhi kualifikasi akademik dan keaktifan organisasi untuk diajukan dalam Beasiswa Prestasi 2026.',
                    'badge' => 'info',
                ],
            ];

            $jadwalTerdekat = [
                'judul' => 'Konsultasi Perencanaan KRS '.$mahasiswa['semester'].' & Persiapan Kelulusan',
                'tanggal' => 'Jumat, 10 Oktober 2026',
                'waktu' => '10.00 – 11.30 WIB',
                'tempat' => config('simbim.ruang_prodi').' / Zoom Meeting',
                'metode' => 'Luring / Tatap Muka',
                'sisa' => 'Besok, 10:00 WIB (H-1)',
                'status' => 'Dikonfirmasi',
                'is_h1' => true,
            ];

            $notifikasi = [
                [
                    'judul' => 'Jadwal bimbingan akademik dikonfirmasi Dosen Wali',
                    'waktu' => '20 menit yang lalu',
                ],
                [
                    'judul' => 'KRS '.$mahasiswa['semester'].' telah diperbarui',
                    'waktu' => '2 hari yang lalu',
                ],
            ];
        }

        $disetujuiCount = count(array_filter(
            $riwayatBimbingan,
            fn (array $item): bool => in_array($item['status'], ['ACC', 'Selesai'], true)
        ));
        $menungguCount = count($riwayatBimbingan) - $disetujuiCount;
        $sksLulus = (int) $mahasiswa['total_sks'];
        $persenSks = $sksLulus > 0 ? min(100, (int) round(($sksLulus / 144) * 100)) : 0;

        if ($perwalian !== null) {
            $statusKrsSub = $perwalian->sks_diambil.' SKS • '.($perwalian->tahun_akademik ?: 'Semester Berjalan');
            [$statusKrsBadgeLabel, $statusKrsBadge] = match ($perwalian->status_perwalian) {
                'selesai' => ['Disetujui Dosen Wali', 'success'],
                'proses' => ['Menunggu Validasi DPA', 'warning'],
                default => ['Belum Mengisi KRS/IRS', 'danger'],
            };
        } elseif ($isPersistedStudent) {
            $statusKrsSub = 'Belum ada pengajuan KRS/IRS';
            $statusKrsBadgeLabel = 'Belum Diajukan';
            $statusKrsBadge = 'secondary';
        } else {
            $statusKrsSub = 'Disetujui 21 SKS';
            $statusKrsBadgeLabel = 'Disetujui Dosen Wali';
            $statusKrsBadge = 'success';
        }

        [$targetLabel, $targetBadge] = match (true) {
            count($riwayatBimbingan) === 0 => ['Belum Ada Sesi', 'secondary'],
            $disetujuiCount >= 2 => ['Target Sesi Terpenuhi', 'success'],
            default => ['Dalam Proses Bimbingan', 'warning'],
        };

        $riwayatBimbinganPaginated = $this->paginateItems($riwayatBimbingan, 6);

        return view('mahasiswa.bimbingan_akademik', [
            'mahasiswa' => $mahasiswa,
            'dosen_wali' => $this->resolveDosenWali($perwalian, $isPersistedStudent, $user),
            'dosen_options' => $this->fetchDosenOptions(),
            'can_submit_pengajuan' => $isPersistedStudent,
            'ringkasan' => [
                'total_sesi' => count($riwayatBimbingan),
                'disetujui' => $disetujuiCount,
                'menunggu' => $menungguCount,
                'ipk_kumulatif' => number_format((float) $mahasiswa['ipk'], 2),
                'sks_lulus' => $sksLulus,
                'status_target_label' => $targetLabel,
                'status_target_badge' => $targetBadge,
                'status_krs_header' => $perwalian !== null
                    ? 'STATUS KRS SEMESTER '.$perwalian->semester_berjalan
                    : ($isPersistedStudent ? 'STATUS KRS / IRS' : 'STATUS KRS SEMESTER 7'),
                'status_krs' => $perwalian?->status_perwalian === 'selesai' ? 'ACC Dosen Wali' : $mahasiswa['status_krs'],
                'status_krs_sub' => $statusKrsSub,
                'status_krs_badge_label' => $statusKrsBadgeLabel,
                'status_krs_badge' => $statusKrsBadge,
            ],
            'evaluasi_sks' => $isPersistedStudent
                ? [
                    'persen' => $persenSks,
                    'wajib_prodi' => $sksLulus,
                    'pilihan_mbkm' => 0,
                    'skripsi_sks' => $dokumen !== null ? 6 : 0,
                ]
                : [
                    'persen' => 82,
                    'wajib_prodi' => 100,
                    'pilihan_mbkm' => 18,
                    'skripsi_sks' => 6,
                ],
            'jadwal_terdekat' => $jadwalTerdekat,
            'riwayat_bimbingan' => $riwayatBimbinganPaginated,
            'catatan_dosen_wali' => $catatanDosenWali,
            'notifikasi' => $notifikasi,
        ]);
    }

    /**
     * Simpan pengajuan bimbingan akademik baru dari akun mahasiswa agar tersinkron ke Dosen Wali dan GKM.
     */
    public function storePengajuan(Request $request): RedirectResponse
    {
        $user = Auth::user();
        abort_if(! $user || ! $user->exists, 403);

        $perwalian = PerwalianMahasiswa::where('mahasiswa_id', $user->id)
            ->latest('updated_at')
            ->first();

        $assignedDosenWaliId = $perwalian?->dosen_wali_id ?? $user->dosen_wali_id;

        $validated = $request->validate([
            'dosen_wali_id' => [
                $assignedDosenWaliId ? 'nullable' : 'required',
                'integer',
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('role', 'dosen')),
            ],
            'kategori_topik' => ['required', 'string', 'max:255'],
            'detail_agenda' => ['required', 'string', 'max:2000'],
        ], [
            'dosen_wali_id.required' => 'Silakan pilih Dosen Wali Prodi PSTI untuk pengajuan pertama Anda.',
            'kategori_topik.required' => 'Kategori topik bimbingan wajib dipilih.',
            'detail_agenda.required' => 'Detail agenda konsultasi wajib diisi.',
        ]);

        $effectiveDosenWaliId = $assignedDosenWaliId ?: (! empty($validated['dosen_wali_id']) ? (int) $validated['dosen_wali_id'] : null);
        $prodiPsti = $user->program_studi ?: (string) config('simbim.prodi', 'Pendidikan Sistem dan Teknologi Informasi (PSTI)');
        $tahunAkademik = 'Ganjil 2026/2027';

        if ($perwalian === null) {
            $perwalian = PerwalianMahasiswa::firstOrCreate(
                [
                    'mahasiswa_id' => $user->id,
                    'tahun_akademik' => $tahunAkademik,
                ],
                [
                    'dosen_wali_id' => $effectiveDosenWaliId,
                    'program_studi' => $prodiPsti,
                    'angkatan' => (string) now()->year,
                    'semester_berjalan' => 1,
                    'ipk' => 0.00,
                    'sks_diambil' => 0,
                    'status_perwalian' => 'proses',
                    'status_krs' => 'Menunggu Konsultasi KRS/IRS',
                ]
            );
        } else {
            $updates = [];
            if ($perwalian->dosen_wali_id === null && $effectiveDosenWaliId !== null) {
                $updates['dosen_wali_id'] = $effectiveDosenWaliId;
            }
            if ($perwalian->status_perwalian === 'belum') {
                $updates['status_perwalian'] = 'proses';
                $updates['status_krs'] = 'Menunggu Konsultasi KRS/IRS';
            }
            if ($updates !== []) {
                $perwalian->update($updates);
            }
        }

        KonsultasiAkademik::create([
            'perwalian_mahasiswa_id' => $perwalian->id,
            'mahasiswa_id' => $user->id,
            'dosen_wali_id' => $perwalian->dosen_wali_id ?: $effectiveDosenWaliId,
            'program_studi' => $prodiPsti,
            'tahun_akademik' => $perwalian->tahun_akademik ?: $tahunAkademik,
            'tanggal_konsultasi' => now()->toDateString(),
            'waktu_konsultasi' => 'Menunggu Konfirmasi',
            'metode' => 'Menunggu Penjadwalan Dosen Wali',
            'topik' => trim($validated['kategori_topik']),
            'catatan_mahasiswa' => trim($validated['detail_agenda']),
            'catatan_dosen_wali' => 'Pengajuan bimbingan akademik baru dikirim. Menunggu konfirmasi jadwal Dosen Wali.',
            'status_sesi' => 'Perlu Tindak Lanjut',
        ]);

        return back()->with('success', 'Pengajuan bimbingan akademik berhasil dikirim dan disinkronkan ke Dosen Wali.');
    }
}
