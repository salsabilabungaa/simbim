<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Models\KonsultasiAkademik;
use App\Models\SkripsiDokumen;
use App\Models\SkripsiDokumenKomentar;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class BerandaController extends BaseMahasiswaController
{
    public function index(): View
    {
        $user = Auth::user();
        $context = $this->resolveMahasiswaContext($user);
        $perwalian = $context['perwalian'];
        $dokumen = $context['dokumen'];
        $isPersistedStudent = $context['is_persisted_student'];
        $ruangProdi = (string) config('simbim.ruang_prodi');

        $dosenPembimbing = $this->resolveDosenPembimbing(false, $dokumen, $isPersistedStudent);
        $dosenWali = $this->resolveDosenWali($perwalian, $isPersistedStudent, $user);

        if ($isPersistedStudent) {
            $konsultasiLogs = $this->fetchMahasiswaKonsultasi($perwalian, $user);
            $akademikTotal = $konsultasiLogs->count();
            $skripsiTotal = (int) ($dokumen?->jumlah_sesi ?? 0);
            $totalSesi = $skripsiTotal + $akademikTotal;

            $skripsiPersen = $skripsiTotal > 0 ? min(100, (int) round(($skripsiTotal / 4) * 100)) : 0;
            $akademikPersen = $akademikTotal > 0 ? min(100, (int) round(($akademikTotal / 2) * 100)) : 0;

            [$statusKuota, $badgeKuota] = match (true) {
                $totalSesi === 0 => ['Belum Ada Sesi', 'secondary'],
                $totalSesi >= 6 => ['Bulan Ini Terpenuhi', 'success'],
                default => ['Dalam Progres', 'primary'],
            };

            if ($dokumen !== null) {
                $bab = [
                    'posisi' => $dokumen->bab_aktif,
                    'persen' => (int) $dokumen->progres_persen,
                    'keterangan' => $dokumen->catatan_akhir_dosen
                        ?: ('Draf '.$dokumen->nama_file.' ('.$dokumen->versi.') — '.$dokumen->status_draf),
                    'target_selesai' => 'Diperbarui '.($dokumen->updated_at?->translatedFormat('d M Y') ?? '-'),
                    'badge_delta' => $dokumen->status_draf,
                    'badge_delta_color' => $dokumen->badge_status ?: SkripsiDokumen::badgeForStatus($dokumen->status_draf),
                ];

                $feedbackTerbaru = $dokumen->komentars
                    ->sortByDesc('id')
                    ->take(3)
                    ->map(fn (SkripsiDokumenKomentar $kom): array => [
                        'dosen' => $kom->user?->name ?? $dosenPembimbing['nama'],
                        'bab' => $kom->lokasi_dokumen ?: $dokumen->bab_aktif,
                        'tanggal' => $kom->created_at?->translatedFormat('d M Y - H:i') ?? '-',
                        'catatan' => $kom->isi_komentar,
                        'status' => $kom->is_resolved ? 'Selesai' : $kom->status,
                        'badge' => $kom->is_resolved ? 'success' : SkripsiDokumenKomentar::badgeForStatus($kom->status),
                    ])
                    ->values()
                    ->all();

                if ($feedbackTerbaru === [] && ! empty($dokumen->catatan_akhir_dosen)) {
                    $feedbackTerbaru = [
                        [
                            'dosen' => $dosenPembimbing['nama'],
                            'bab' => $dokumen->bab_aktif,
                            'tanggal' => $dokumen->updated_at?->translatedFormat('d M Y - H:i') ?? '-',
                            'catatan' => $dokumen->catatan_akhir_dosen,
                            'status' => $dokumen->status_draf,
                            'badge' => $dokumen->badge_status ?: 'warning',
                        ],
                    ];
                }
            } else {
                $bab = [
                    'posisi' => 'Belum Ada Draf Skripsi',
                    'persen' => 0,
                    'keterangan' => 'Belum ada unggahan draf skripsi atau pengajuan bimbingan tugas akhir pada akun ini.',
                    'target_selesai' => 'Belum Ditentukan',
                    'badge_delta' => 'Belum Dimulai',
                    'badge_delta_color' => 'secondary',
                ];
                $feedbackTerbaru = [];
            }

            $notifikasi = [];
            if ($dokumen !== null && $dokumen->komentars->isNotEmpty()) {
                $latestKom = $dokumen->komentars->sortByDesc('id')->first();
                $notifikasi[] = [
                    'judul' => 'Komentar review pada '.$latestKom->lokasi_dokumen,
                    'waktu' => $latestKom->created_at?->diffForHumans() ?? 'Baru saja',
                ];
            }
            if ($konsultasiLogs->isNotEmpty()) {
                $latestLog = $konsultasiLogs->first();
                $notifikasi[] = [
                    'judul' => $latestLog->topik.' ('.$latestLog->status_sesi.')',
                    'waktu' => $latestLog->tanggal_konsultasi?->translatedFormat('d M Y') ?? 'Baru saja',
                ];
            }
            if ($notifikasi === []) {
                $notifikasi[] = [
                    'judul' => 'Akun mahasiswa aktif — belum ada riwayat sesi atau draf bimbingan',
                    'waktu' => 'Hari ini',
                ];
            }

            return view('mahasiswa.beranda', [
                'mahasiswa' => $context['mahasiswa'],
                'dosen_pembimbing' => $dosenPembimbing,
                'dosen_wali' => $dosenWali,
                'bab' => $bab,
                'rekapan_bimbingan' => [
                    'skripsi' => [
                        'label' => 'Bimbingan Skripsi',
                        'total' => $skripsiTotal,
                        'target' => 4,
                        'persen' => $skripsiPersen,
                        'status' => $skripsiTotal.' Sesi Terlaksana',
                        'badge' => $skripsiTotal > 0 ? 'success' : 'secondary',
                    ],
                    'akademik' => [
                        'label' => 'Bimbingan Akademik',
                        'total' => $akademikTotal,
                        'target' => 2,
                        'persen' => $akademikPersen,
                        'status' => $akademikTotal.' Sesi Terlaksana',
                        'badge' => $akademikTotal > 0 ? 'info' : 'secondary',
                    ],
                    'total_bulan_ini' => $totalSesi,
                    'status_kuota' => $statusKuota,
                    'badge_kuota' => $badgeKuota,
                ],
                'pertemuan' => [
                    'judul' => $dokumen?->bab_aktif ?? ($konsultasiLogs->first()?->topik ?? 'Belum ada jadwal bimbingan'),
                    'tanggal' => 'Belum Terjadwal',
                    'waktu' => '-',
                    'tempat' => $ruangProdi,
                    'tipe' => '-',
                    'sisa' => 'Menunggu Jadwal',
                    'status' => 'Menunggu Pengajuan',
                    'is_h1' => false,
                ],
                'feedback_terbaru' => $feedbackTerbaru,
                'notifikasi' => $notifikasi,
            ]);
        }

        $akademikTotal = $perwalian ? KonsultasiAkademik::where('perwalian_mahasiswa_id', $perwalian->id)->count() : 2;
        if ($akademikTotal === 0) {
            $akademikTotal = 2;
        }

        return view('mahasiswa.beranda', [
            'mahasiswa' => $context['mahasiswa'],
            'dosen_pembimbing' => $dosenPembimbing,
            'dosen_wali' => $dosenWali,
            'bab' => [
                'posisi' => 'Bab III — Metodologi Penelitian',
                'persen' => 65,
                'keterangan' => 'Draft revisi ke-2 sedang dikaji oleh dosen pembimbing.',
                'target_selesai' => '25 Oktober 2026',
                'badge_delta' => '+7% bulan ini',
                'badge_delta_color' => 'success',
            ],
            'rekapan_bimbingan' => [
                'skripsi' => [
                    'label' => 'Bimbingan Skripsi',
                    'total' => 4,
                    'target' => 4,
                    'persen' => 100,
                    'status' => '4 Sesi Terlaksana',
                    'badge' => 'success',
                ],
                'akademik' => [
                    'label' => 'Bimbingan Akademik',
                    'total' => $akademikTotal,
                    'target' => 2,
                    'persen' => 100,
                    'status' => $akademikTotal.' Sesi Terlaksana',
                    'badge' => 'info',
                ],
                'total_bulan_ini' => 4 + $akademikTotal,
                'status_kuota' => 'Bulan Ini Terpenuhi',
                'badge_kuota' => 'success',
            ],
            'pertemuan' => [
                'judul' => 'Bimbingan Bab III (Metodologi & Data)',
                'tanggal' => 'Kamis, 9 Oktober 2026',
                'waktu' => '09.00 – 10.30 WIB',
                'tempat' => $ruangProdi.' / Google Meet',
                'tipe' => 'Luring / Tatap Muka',
                'sisa' => 'Besok, 09:00 WIB (H-1)',
                'status' => 'Dikonfirmasi',
                'is_h1' => true,
            ],
            'feedback_terbaru' => [
                [
                    'dosen' => 'Dr. Siti Rahmawati, M.Kom.',
                    'bab' => 'Bab III Metodologi',
                    'tanggal' => '08 Okt 2026 - 14:30',
                    'catatan' => 'Perjelas diagram alir pengolahan data pada sub-bab 3.2. Tambahkan variabel dependen dan independen pada tabel pengujian.',
                    'status' => 'Perlu Revisi',
                    'badge' => 'warning',
                ],
                [
                    'dosen' => 'Dr. Siti Rahmawati, M.Kom.',
                    'bab' => 'Bab II Tinjauan Pustaka',
                    'tanggal' => '04 Okt 2026 - 10:15',
                    'catatan' => 'Struktur referensi sudah sangat baik dan sesuai format IEEE 2026. Lanjutkan ke penyusunan sampel instrumen.',
                    'status' => 'Disetujui',
                    'badge' => 'success',
                ],
            ],
            'notifikasi' => [
                [
                    'judul' => 'Catatan Revisi Bab III telah diperbarui',
                    'waktu' => '15 menit yang lalu',
                ],
                [
                    'judul' => 'Konfirmasi jadwal bimbingan besok 09:00 WIB',
                    'waktu' => '1 jam yang lalu',
                ],
                [
                    'judul' => 'Bab II disetujui oleh Dr. Siti Rahmawati',
                    'waktu' => '1 hari yang lalu',
                ],
            ],
        ]);
    }
}
