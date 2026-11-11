<?php

namespace App\Http\Controllers\Dosen;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class RiwayatBimbinganController extends BaseDosenController
{
    /**
     * Menampilkan riwayat dan arsip logbook bimbingan mahasiswa oleh dosen.
     */
    public function index(): View
    {
        $user = Auth::user();
        $dosen = $this->resolveDosenProfile($user);
        $prodi = (string) config('simbim.prodi');
        $metodeLuring = 'Luring ('.config('simbim.ruang_prodi').')';

        $statistik = [
            'total_sesi' => 48,
            'sesi_skripsi' => 32,
            'sesi_akademik' => 16,
            'mahasiswa_acc' => 6,
        ];

        $riwayat = [
            [
                'id' => 1,
                'tanggal' => 'Jumat, 24 Okt 2026',
                'waktu' => '09:30 - 10:30 WIB',
                'mahasiswa' => 'Andi Pratama',
                'nim' => '2108107010001',
                'angkatan' => '2021',
                'prodi' => $prodi,
                'kategori' => 'Skripsi',
                'kategori_key' => 'skripsi',
                'badge_kategori' => 'primary',
                'peran_dosen' => 'Pembimbing Utama',
                'bab' => 'Bab III (Metodologi)',
                'topik' => 'Review Flowchart, Class Diagram & Instrumen Pengujian Sistem',
                'catatan' => 'Diagram alir sistem dan relasi tabel telah valid. Revisi minor pada instrumen pengujian kuesioner kelayakan. Disetujui untuk lanjut implementasi Bab IV.',
                'metode' => $metodeLuring,
                'status' => 'ACC',
                'badge_status' => 'success',
                'dokumen' => 'Draf_Bab_III_Andi_ACC.pdf',
                'foto' => 'dokumentasi_sesi_andi.jpg',
            ],
            [
                'id' => 2,
                'tanggal' => 'Jumat, 24 Okt 2026',
                'waktu' => '14:00 - 15:00 WIB',
                'mahasiswa' => 'Cut Nadia Zahra',
                'nim' => '2108107010034',
                'angkatan' => '2021',
                'prodi' => $prodi,
                'kategori' => 'Skripsi',
                'kategori_key' => 'skripsi',
                'badge_kategori' => 'primary',
                'peran_dosen' => 'Pembimbing Utama',
                'bab' => 'Bab I (Pendahuluan)',
                'topik' => 'Konsultasi Rumusan Masalah & Justifikasi Dataset Random Forest',
                'catatan' => 'Latar belakang perlu diperkuat dengan data statistik kepuasan 2 semester terakhir. Rumusan masalah disederhanakan menjadi 3 poin.',
                'metode' => 'Daring (Google Meet)',
                'status' => 'Revisi',
                'badge_status' => 'warning',
                'dokumen' => 'Revisi_Bab_I_Nadia.pdf',
                'foto' => 'meet_cut_nadia.png',
            ],
            [
                'id' => 3,
                'tanggal' => 'Rabu, 22 Okt 2026',
                'waktu' => '10:00 - 11:30 WIB',
                'mahasiswa' => 'Siti Aisyah Putri',
                'nim' => '2108107010088',
                'angkatan' => '2021',
                'prodi' => $prodi,
                'kategori' => 'Skripsi',
                'kategori_key' => 'skripsi',
                'badge_kategori' => 'primary',
                'peran_dosen' => 'Pembimbing Pendamping',
                'bab' => 'Bab IV (Implementasi)',
                'topik' => 'Pengujian Load Balancing API & Dokumentasi Latensi Server',
                'catatan' => 'Hasil benchmark stress testing memuaskan. Tambahkan grafik komparasi performa sebelum dan sesudah microservices.',
                'metode' => 'Luring (Lab Komputer RPL)',
                'status' => 'Selesai',
                'badge_status' => 'info',
                'dokumen' => 'Bab4_Hasil_Pengujian_Siti.pdf',
                'foto' => 'sesi_lab_aisyah.jpg',
            ],
            [
                'id' => 4,
                'tanggal' => 'Selasa, 20 Okt 2026',
                'waktu' => '13:30 - 14:45 WIB',
                'mahasiswa' => 'Rahmat Hidayat',
                'nim' => '2008107010045',
                'angkatan' => '2020',
                'prodi' => $prodi,
                'kategori' => 'Skripsi',
                'kategori_key' => 'skripsi',
                'badge_kategori' => 'primary',
                'peran_dosen' => 'Pembimbing Utama',
                'bab' => 'Bab V (Penutup)',
                'topik' => 'Pemeriksaan Naskah Lengkap & Rekomendasi ACC Sidang Skripsi',
                'catatan' => 'Seluruh revisi dari Bab I s.d Bab V telah diperiksa dan disetujui. Naskah siap dijadwalkan untuk Sidang Ujian Tugas Akhir.',
                'metode' => $metodeLuring,
                'status' => 'ACC',
                'badge_status' => 'success',
                'dokumen' => 'Naskah_Lengkap_Rahmat_Final_ACC.pdf',
                'foto' => 'acc_sidang_rahmat.jpg',
            ],
            [
                'id' => 5,
                'tanggal' => 'Senin, 19 Okt 2026',
                'waktu' => '10:00 - 11:00 WIB',
                'mahasiswa' => 'Muhammad Fikri',
                'nim' => '2208107010012',
                'angkatan' => '2022',
                'prodi' => $prodi,
                'kategori' => 'Akademik',
                'kategori_key' => 'akademik',
                'badge_kategori' => 'success',
                'peran_dosen' => 'Dosen Pembimbing Akademik',
                'bab' => 'Semester 5 (88 SKS)',
                'topik' => 'Konsultasi Perencanaan Magang Bersertifikat MBKM & Konversi 20 SKS',
                'catatan' => 'Disetujui mengambil program Magang Pengembang Web MBKM. Konversi mata kuliah Pilihan 1, Pilihan 2, dan Proyek Perangkat Lunak disahkan.',
                'metode' => $metodeLuring,
                'status' => 'Selesai',
                'badge_status' => 'info',
                'dokumen' => 'Konversi_SKS_MBKM_Fikri.pdf',
                'foto' => 'perwalian_fikri.jpg',
            ],
            [
                'id' => 6,
                'tanggal' => 'Kamis, 15 Okt 2026',
                'waktu' => '11:00 - 12:00 WIB',
                'mahasiswa' => 'Dimas Bagus Wicaksono',
                'nim' => '2108107010055',
                'angkatan' => '2021',
                'prodi' => $prodi,
                'kategori' => 'Skripsi',
                'kategori_key' => 'skripsi',
                'badge_kategori' => 'primary',
                'peran_dosen' => 'Pembimbing Utama',
                'bab' => 'Bab II (Kajian Teori)',
                'topik' => 'Sintesis Literatur Content-Based Filtering & Matriks Cosine Similarity',
                'catatan' => 'Perkuat komparasi dengan pendekatan Collaborative Filtering. Perbaiki tata cara penulisan kutipan APA Style edisi ke-7.',
                'metode' => 'Daring (Zoom Meeting)',
                'status' => 'Revisi',
                'badge_status' => 'warning',
                'dokumen' => 'Bab2_Matriks_Kajian_Dimas.pdf',
                'foto' => 'zoom_dimas.png',
            ],
            [
                'id' => 7,
                'tanggal' => 'Selasa, 13 Okt 2026',
                'waktu' => '09:00 - 10:00 WIB',
                'mahasiswa' => 'Dinda Maulida',
                'nim' => '2208107010065',
                'angkatan' => '2022',
                'prodi' => $prodi,
                'kategori' => 'Akademik',
                'kategori_key' => 'akademik',
                'badge_kategori' => 'success',
                'peran_dosen' => 'Dosen Pembimbing Akademik',
                'bab' => 'Semester 5 (86 SKS)',
                'topik' => 'Evaluasi Indeks Prestasi Kumulatif (IPK 3.65) & Persiapan Proposal',
                'catatan' => 'Performa akademik sangat baik. Mahasiswa diarahkan mulai mengidentifikasi masalah penelitian untuk tugas akhir di semester 6.',
                'metode' => $metodeLuring,
                'status' => 'Selesai',
                'badge_status' => 'info',
                'dokumen' => 'Transkrip_KRS_Dinda.pdf',
                'foto' => 'konsultasi_dinda.jpg',
            ],
            [
                'id' => 8,
                'tanggal' => 'Senin, 12 Okt 2026',
                'waktu' => '13:00 - 14:00 WIB',
                'mahasiswa' => 'Rizky Kurniawan',
                'nim' => '2308107010023',
                'angkatan' => '2023',
                'prodi' => $prodi,
                'kategori' => 'Akademik',
                'kategori_key' => 'akademik',
                'badge_kategori' => 'success',
                'peran_dosen' => 'Dosen Pembimbing Akademik',
                'bab' => 'Semester 3 (44 SKS)',
                'topik' => 'Konsultasi Perbaikan Nilai Mata Kuliah Struktur Data & Remedial',
                'catatan' => 'Diberikan arahan mengikuti modul responsi mingguan lab. Target semester 3 IPK minimal 3.30.',
                'metode' => $metodeLuring,
                'status' => 'Selesai',
                'badge_status' => 'info',
                'dokumen' => 'Evaluasi_Nilai_Rizky.pdf',
                'foto' => 'perwalian_rizky.jpg',
            ],
        ];

        $dbKonsultasi = $this->fetchDatabaseKonsultasi($user);
        if (! empty($dbKonsultasi)) {
            $skripsiLogs = array_values(array_filter(
                $riwayat,
                fn (array $item): bool => ($item['kategori_key'] ?? '') === 'skripsi'
            ));
            $riwayat = array_merge($dbKonsultasi, $skripsiLogs);
            $statistik = [
                'total_sesi' => count($riwayat),
                'sesi_skripsi' => count($skripsiLogs),
                'sesi_akademik' => count($dbKonsultasi),
                'mahasiswa_acc' => count(array_filter($riwayat, fn (array $item): bool => ($item['status'] ?? '') === 'ACC')),
            ];
        }

        $notifikasi = $this->defaultNotifikasi();

        return view('dosen.riwayat_bimbingan', compact(
            'dosen',
            'statistik',
            'riwayat',
            'notifikasi'
        ));
    }
}
