<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Models\KonsultasiAkademik;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class RiwayatBimbinganController extends BaseMahasiswaController
{
    public function index(): View
    {
        $user = Auth::user();
        $context = $this->resolveMahasiswaContext($user);
        $mahasiswa = $context['mahasiswa'];
        $perwalian = $context['perwalian'];
        $dokumen = $context['dokumen'];
        $isPersistedStudent = $context['is_persisted_student'];

        $ruangProdi = (string) config('simbim.ruang_prodi');
        $ruangWali = (string) config('simbim.ruang_prodi').' R.108';
        $dbLogs = $this->fetchMahasiswaKonsultasi($perwalian, $user);

        if ($isPersistedStudent) {
            $skripsiRiwayat = [];
            if ($dokumen !== null) {
                [$skripsiStatus, $skripsiBadge] = match ($dokumen->status_draf) {
                    'Siap Sidang Skripsi' => ['Selesai', 'success'],
                    'Draf Sedang Direview', 'Bimbingan Terjadwal' => ['ACC', 'primary'],
                    'Revisi Diperlukan' => ['Revisi', 'danger'],
                    default => ['Proses', 'warning'],
                };

                $skripsiRiwayat[] = [
                    'id' => 100 + $dokumen->id,
                    'tanggal' => $dokumen->updated_at?->translatedFormat('d M Y') ?? '-',
                    'waktu' => ($dokumen->updated_at?->translatedFormat('H.i') ?? '-').' WIB',
                    'kategori' => 'Skripsi',
                    'badge_kategori' => 'primary',
                    'dosen' => $dokumen->dosenPembimbing?->name ?? 'Dosen Pembimbing PSTI',
                    'role_dosen' => $dokumen->peran_dosen ?: 'Dosen Pembimbing Utama',
                    'topik' => $dokumen->bab_aktif.' — '.$dokumen->judul_skripsi,
                    'dokumen' => $dokumen->nama_file,
                    'download_url' => route('mahasiswa.bimbingan-skripsi.download', $dokumen->id),
                    'status' => $skripsiStatus,
                    'badge_status' => $skripsiBadge,
                    'catatan' => $dokumen->catatan_akhir_dosen ?: ('Draf versi '.$dokumen->versi.' ('.$dokumen->status_draf.')'),
                    'foto' => null,
                    'ruangan' => $ruangProdi,
                ];
            }

            $akademikRiwayat = $dbLogs->values()->map(function (KonsultasiAkademik $log, int $idx) use ($ruangWali): array {
                [$statusLabel, $badge] = match ($log->status_sesi) {
                    'ACC KRS/IRS' => ['ACC', 'primary'],
                    'Perlu Tindak Lanjut' => ['Revisi', 'danger'],
                    default => ['Selesai', 'success'],
                };

                return [
                    'id' => 200 + $idx,
                    'tanggal' => $log->tanggal_konsultasi?->translatedFormat('d M Y') ?? '-',
                    'waktu' => $log->waktu_konsultasi ?: '10.00 - 11.00 WIB',
                    'kategori' => 'Akademik',
                    'badge_kategori' => 'info',
                    'dosen' => $log->dosenWali?->name ?? 'Dosen Wali PSTI UPI',
                    'role_dosen' => 'Dosen Wali / DPA',
                    'topik' => $log->topik,
                    'dokumen' => $log->lampiran_krs ?: null,
                    'download_url' => null,
                    'status' => $statusLabel,
                    'badge_status' => $badge,
                    'catatan' => $log->catatan_dosen_wali ?: ($log->catatan_mahasiswa ?? '-'),
                    'foto' => null,
                    'ruangan' => $log->metode ? $ruangWali.' ('.$log->metode.')' : $ruangWali,
                ];
            })->all();

            $riwayat = array_merge($skripsiRiwayat, $akademikRiwayat);

            $dosenList = [];
            $assignedWali = $perwalian?->dosenWali ?? $user?->dosenWali;
            if ($dokumen?->dosenPembimbing !== null) {
                $dosenList[$dokumen->dosenPembimbing->name] = 'Dosen Pembimbing Skripsi';
            }
            if ($assignedWali !== null) {
                $dosenList[$assignedWali->name] = 'Dosen Wali (DPA)';
            }

            $penandatangan = [
                'nama' => $dokumen?->dosenPembimbing?->name ?? ($assignedWali?->name ?? 'Dosen Pembimbing / Wali PSTI UPI'),
                'nip' => $dokumen?->dosenPembimbing?->nomor_induk ?? ($assignedWali?->nomor_induk ?? '-'),
            ];

            $notifikasi = $riwayat !== []
                ? [
                    [
                        'judul' => 'Tercatat '.count($riwayat).' sesi bimbingan pada logbook akun Anda',
                        'waktu' => 'Diperbarui otomatis',
                    ],
                ]
                : [
                    [
                        'judul' => 'Belum ada riwayat sesi bimbingan pada logbook akun baru Anda',
                        'waktu' => 'Hari ini',
                    ],
                ];
        } else {
            $skripsiRiwayat = [
                [
                    'id' => 1,
                    'tanggal' => '11 Okt 2026',
                    'waktu' => '09.30 - 11.00 WIB',
                    'kategori' => 'Skripsi',
                    'badge_kategori' => 'primary',
                    'dosen' => 'Dr. Ir. Teuku Muhammad, M.T.',
                    'role_dosen' => 'Dosen Pembimbing Utama',
                    'topik' => 'BAB III: Review Flowchart & Class Diagram Sistem Bimbingan',
                    'dokumen' => 'Draft_BAB3_Diagram_v1.2.pdf',
                    'download_url' => null,
                    'status' => 'ACC',
                    'badge_status' => 'primary',
                    'catatan' => 'Jadwal dan perancangan diagram telah disetujui. Dokumentasi foto sesi bimbingan terverifikasi.',
                    'foto' => 'assets/media/svg/avatars/001-boy.svg',
                    'ruangan' => $ruangProdi.' R.204',
                ],
                [
                    'id' => 3,
                    'tanggal' => '28 Sep 2026',
                    'waktu' => '13.30 - 15.00 WIB',
                    'kategori' => 'Skripsi',
                    'badge_kategori' => 'primary',
                    'dosen' => 'Dr. Ir. Teuku Muhammad, M.T.',
                    'role_dosen' => 'Dosen Pembimbing Utama',
                    'topik' => 'BAB II: Tinjauan Pustaka & Matriks Comparative Literature',
                    'dokumen' => 'Draft_BAB2_ACC.pdf',
                    'download_url' => null,
                    'status' => 'Selesai',
                    'badge_status' => 'success',
                    'catatan' => 'BAB II dinyatakan ACC dengan catatan kecil pada penulisan sitasi standar IEEE.',
                    'foto' => null,
                    'ruangan' => 'Zoom Online Meeting',
                ],
                [
                    'id' => 5,
                    'tanggal' => '10 Sep 2026',
                    'waktu' => '10.00 - 11.30 WIB',
                    'kategori' => 'Skripsi',
                    'badge_kategori' => 'primary',
                    'dosen' => 'Dr. Ir. Teuku Muhammad, M.T.',
                    'role_dosen' => 'Dosen Pembimbing Utama',
                    'topik' => 'BAB I: Pendahuluan, Latar Belakang & Rumusan Masalah',
                    'dokumen' => 'Draft_BAB1_Final.pdf',
                    'download_url' => null,
                    'status' => 'Selesai',
                    'badge_status' => 'success',
                    'catatan' => 'BAB I disetujui penuh. Mahasiswa diizinkan melanjutkan penyusunan BAB II.',
                    'foto' => 'assets/media/svg/avatars/002-boy-2.svg',
                    'ruangan' => $ruangProdi.' R.204',
                ],
                [
                    'id' => 6,
                    'tanggal' => '25 Agu 2026',
                    'waktu' => '13.00 - 14.30 WIB',
                    'kategori' => 'Skripsi',
                    'badge_kategori' => 'primary',
                    'dosen' => 'Dr. Ir. Teuku Muhammad, M.T.',
                    'role_dosen' => 'Dosen Pembimbing Utama',
                    'topik' => 'Pengajuan Judul & Proposal Tugas Akhir',
                    'dokumen' => 'Proposal_Skripsi_Final.pdf',
                    'download_url' => null,
                    'status' => 'Selesai',
                    'badge_status' => 'success',
                    'catatan' => 'Judul skripsi disetujui tim DPA dan Pembimbing Utama.',
                    'foto' => null,
                    'ruangan' => $ruangProdi.' R.204',
                ],
                [
                    'id' => 8,
                    'tanggal' => '02 Agu 2026',
                    'waktu' => '11.00 - 12.00 WIB',
                    'kategori' => 'Skripsi',
                    'badge_kategori' => 'primary',
                    'dosen' => 'Dr. Ir. Teuku Muhammad, M.T.',
                    'role_dosen' => 'Dosen Pembimbing Utama',
                    'topik' => 'BAB I: Review Batasan Masalah & Lingkup Penelitian',
                    'dokumen' => 'Draft_BAB1_Rev1.pdf',
                    'download_url' => null,
                    'status' => 'Revisi',
                    'badge_status' => 'danger',
                    'catatan' => 'Perbaiki batasan masalah pada bagian scope latency microservices.',
                    'foto' => null,
                    'ruangan' => $ruangProdi.' R.204',
                ],
            ];

            $akademikRiwayat = $dbLogs->isNotEmpty()
                ? $dbLogs->values()->map(function (KonsultasiAkademik $log, int $idx) use ($ruangWali): array {
                    [$statusLabel, $badge] = match ($log->status_sesi) {
                        'ACC KRS/IRS' => ['ACC', 'primary'],
                        'Perlu Tindak Lanjut' => ['Revisi', 'danger'],
                        default => ['Selesai', 'success'],
                    };

                    return [
                        'id' => 200 + $idx,
                        'tanggal' => $log->tanggal_konsultasi?->translatedFormat('d M Y') ?? '-',
                        'waktu' => $log->waktu_konsultasi ?: '10.00 - 11.00 WIB',
                        'kategori' => 'Akademik',
                        'badge_kategori' => 'info',
                        'dosen' => $log->dosenWali?->name ?? 'Dosen Wali PSTI UPI',
                        'role_dosen' => 'Dosen Wali / DPA',
                        'topik' => $log->topik,
                        'dokumen' => $log->lampiran_krs ?: 'Log_Perwalian_'.$log->id.'.pdf',
                        'download_url' => null,
                        'status' => $statusLabel,
                        'badge_status' => $badge,
                        'catatan' => $log->catatan_dosen_wali ?: ($log->catatan_mahasiswa ?? '-'),
                        'foto' => null,
                        'ruangan' => $log->metode ? $ruangWali.' ('.$log->metode.')' : $ruangWali,
                    ];
                })->all()
                : [
                    [
                        'id' => 2,
                        'tanggal' => '04 Okt 2026',
                        'waktu' => '14.00 - 15.15 WIB',
                        'kategori' => 'Akademik',
                        'badge_kategori' => 'info',
                        'dosen' => 'Prof. Dr. Eng. Ir. Herman, M.T.',
                        'role_dosen' => 'Dosen Wali / DPA',
                        'topik' => 'Evaluasi Pengisian KRS Semester 7 & Persiapan Sidang Komprehensif',
                        'dokumen' => 'Form_KRS_Semester_7_Approved.pdf',
                        'download_url' => null,
                        'status' => 'Selesai',
                        'badge_status' => 'success',
                        'catatan' => 'KRS Semester 7 sebanyak 24 SKS telah disetujui & ditandatangani secara digital.',
                        'foto' => 'assets/media/svg/avatars/002-boy-2.svg',
                        'ruangan' => $ruangWali,
                    ],
                    [
                        'id' => 4,
                        'tanggal' => '20 Sep 2026',
                        'waktu' => '10.30 - 11.30 WIB',
                        'kategori' => 'Akademik',
                        'badge_kategori' => 'info',
                        'dosen' => 'Prof. Dr. Eng. Ir. Herman, M.T.',
                        'role_dosen' => 'Dosen Wali / DPA',
                        'topik' => 'Konsultasi Permohonan Surat Rekomendasi Beasiswa Unggulan UPI',
                        'dokumen' => 'Surat_Rekomendasi_Beasiswa.pdf',
                        'download_url' => null,
                        'status' => 'Selesai',
                        'badge_status' => 'success',
                        'catatan' => 'Surat Rekomendasi Beasiswa disetujui dan ditandatangani Dosen Wali.',
                        'foto' => 'assets/media/svg/avatars/004-boy-3.svg',
                        'ruangan' => $ruangWali,
                    ],
                    [
                        'id' => 7,
                        'tanggal' => '15 Agu 2026',
                        'waktu' => '09.00 - 10.00 WIB',
                        'kategori' => 'Akademik',
                        'badge_kategori' => 'info',
                        'dosen' => 'Prof. Dr. Eng. Ir. Herman, M.T.',
                        'role_dosen' => 'Dosen Wali / DPA',
                        'topik' => 'Evaluasi KHS Semester 6 & Perencanaan SKS Semester 7',
                        'dokumen' => 'KHS_Semester_6_Verified.pdf',
                        'download_url' => null,
                        'status' => 'Selesai',
                        'badge_status' => 'success',
                        'catatan' => 'IPK Semester 6 dicapai 3.82. Mempertahankan predikat Cumlaude.',
                        'foto' => null,
                        'ruangan' => $ruangWali,
                    ],
                ];

            $riwayat = array_merge($skripsiRiwayat, $akademikRiwayat);
            $waliName = $perwalian?->dosenWali?->name ?? 'Prof. Dr. Eng. Ir. Herman, M.T.';
            $dosenList = [
                'Dr. Ir. Teuku Muhammad, M.T.' => 'Dosen Pembimbing Skripsi',
                $waliName => 'Dosen Wali (DPA)',
            ];
            $penandatangan = [
                'nama' => 'Dr. Ir. Teuku Muhammad, M.T.',
                'nip' => '19810314 200604 1 003',
            ];
            $notifikasi = [
                [
                    'judul' => 'Dokumentasi Bimbingan BAB III disetujui Dosen Pembimbing',
                    'waktu' => '2 jam yang lalu',
                ],
                [
                    'judul' => 'KRS '.$mahasiswa['semester'].' tercatat pada sistem perwalian',
                    'waktu' => '5 hari yang lalu',
                ],
            ];
        }

        return view('mahasiswa.riwayat_bimbingan', [
            'mahasiswa' => $mahasiswa,
            'ringkasan' => [
                'total_sesi' => count($riwayat),
                'bimbingan_skripsi' => count($skripsiRiwayat),
                'bimbingan_akademik' => count($akademikRiwayat),
                'total_acc' => count(array_filter($riwayat, fn (array $item): bool => in_array($item['status'], ['ACC', 'Selesai'], true))),
                'total_revisi' => count(array_filter($riwayat, fn (array $item): bool => $item['status'] === 'Revisi')),
            ],
            'riwayat' => $riwayat,
            'dosen_list' => $dosenList,
            'penandatangan' => $penandatangan,
            'notifikasi' => $notifikasi,
        ]);
    }
}
