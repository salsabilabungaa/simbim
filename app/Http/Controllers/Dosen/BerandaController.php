<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class BerandaController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $dosen = [
            'nama' => $user?->name ?? 'Dr. Siti Rahmawati, M.Kom.',
            'nip' => $user?->nomor_induk ?? '198503152010122001',
            'jabatan' => 'Lektor Kepala — Pembimbing Utama',
            'program_studi' => $user?->program_studi ?? 'Informatika',
            'bidang' => 'Software Engineering & Intelligence Systems',
            'email' => $user?->email ?? 'siti.rahmawati@usk.ac.id',
            'telepon' => '+62 812-6900-8821',
            'ruangan' => 'Gedung Fasilkom Lt. 2 R.204',
            'status_hadir' => 'Tersedia di Kampus',
        ];

        $statistik = [
            'mahasiswa_skripsi' => 8,
            'mahasiswa_perwalian' => 24,
            'menunggu_review' => 3,
            'sesi_pekan_ini' => 5,
        ];

        $jadwal_bimbingan = [
            [
                'id' => 1,
                'mahasiswa' => 'Andi Pratama',
                'nim' => '2108107010001',
                'jenis' => 'Bimbingan Skripsi',
                'topik' => 'Review Draf Revisi Bab III & Instrumen Pengujian',
                'tanggal' => 'Jumat, 24 Oktober 2026',
                'waktu' => '09:30 - 10:30 WIB',
                'tempat' => 'Ruang Dosen 204 (Luring)',
                'badge' => 'primary',
                'status' => 'Terkonfirmasi',
            ],
            [
                'id' => 2,
                'mahasiswa' => 'Cut Nadia Zahra',
                'nim' => '2108107010034',
                'jenis' => 'Bimbingan Skripsi',
                'topik' => 'Konsultasi Bab I Latar Belakang & Rumusan Masalah',
                'tanggal' => 'Jumat, 24 Oktober 2026',
                'waktu' => '14:00 - 15:00 WIB',
                'tempat' => 'Google Meet (Daring)',
                'badge' => 'info',
                'status' => 'Terkonfirmasi',
            ],
            [
                'id' => 3,
                'mahasiswa' => 'Muhammad Fikri',
                'nim' => '2208107010012',
                'jenis' => 'Konsultasi Akademik',
                'topik' => 'Perencanaan KRS Semester Genap & Evaluasi IPK',
                'tanggal' => 'Senin, 27 Oktober 2026',
                'waktu' => '10:00 - 11:00 WIB',
                'tempat' => 'Ruang Dosen 204 (Luring)',
                'badge' => 'success',
                'status' => 'Menunggu Konfirmasi',
            ],
        ];

        $mahasiswa_bimbingan = [
            [
                'nama' => 'Andi Pratama',
                'nim' => '2108107010001',
                'judul' => 'Pengembangan Sistem Informasi Manajemen Bimbingan Skripsi Berbasis Web',
                'bab' => 'Bab III (Metodologi)',
                'progres' => 65,
                'status_draf' => 'Menunggu Review Dosen',
                'badge_status' => 'warning',
                'tanggal_update' => '22 Okt 2026',
            ],
            [
                'nama' => 'Cut Nadia Zahra',
                'nim' => '2108107010034',
                'judul' => 'Penerapan Algoritma Random Forest pada Klasifikasi Kepuasan Mahasiswa',
                'bab' => 'Bab I (Pendahuluan)',
                'progres' => 25,
                'status_draf' => 'Revisi Diperlukan',
                'badge_status' => 'danger',
                'tanggal_update' => '21 Okt 2026',
            ],
            [
                'nama' => 'Rahmat Hidayat',
                'nim' => '2008107010045',
                'judul' => 'Analisis Sentimen Opini Publik Menggunakan Deep Learning Transformer',
                'bab' => 'Bab V (Kesimpulan & ACC Sidang)',
                'progres' => 95,
                'status_draf' => 'Siap Sidang Skripsi',
                'badge_status' => 'success',
                'tanggal_update' => '20 Okt 2026',
            ],
            [
                'nama' => 'Siti Aisyah Putri',
                'nim' => '2108107010088',
                'judul' => 'Implementasi Arsitektur Microservices pada Aplikasi E-Commerce UMKM',
                'bab' => 'Bab IV (Implementasi & Evaluasi)',
                'progres' => 80,
                'status_draf' => 'Draf Sedang Direview',
                'badge_status' => 'primary',
                'tanggal_update' => '19 Okt 2026',
            ],
        ];

        $mahasiswa_perwalian = [
            [
                'nama' => 'Muhammad Fikri',
                'nim' => '2208107010012',
                'angkatan' => '2022',
                'semester' => 'Semester 5',
                'ipk' => '3.82',
                'sks_lulus' => 88,
                'status_krs' => 'KRS Disetujui',
                'badge_status' => 'success',
            ],
            [
                'nama' => 'Dinda Maulida',
                'nim' => '2208107010065',
                'angkatan' => '2022',
                'semester' => 'Semester 5',
                'ipk' => '3.65',
                'sks_lulus' => 86,
                'status_krs' => 'KRS Disetujui',
                'badge_status' => 'success',
            ],
            [
                'nama' => 'Rizky Kurniawan',
                'nim' => '2308107010023',
                'angkatan' => '2023',
                'semester' => 'Semester 3',
                'ipk' => '3.40',
                'sks_lulus' => 44,
                'status_krs' => 'Perlu Konsultasi SKS',
                'badge_status' => 'warning',
            ],
            [
                'nama' => 'Nabila Putri',
                'nim' => '2408107010009',
                'angkatan' => '2024',
                'semester' => 'Semester 1',
                'ipk' => '-',
                'sks_lulus' => 20,
                'status_krs' => 'KRS Baru Diajukan',
                'badge_status' => 'info',
            ],
        ];

        $aktivitas_terbaru = [
            [
                'judul' => 'Draf Revisi Bab III Diunggah',
                'mahasiswa' => 'Andi Pratama (2108107010001)',
                'waktu' => '30 menit yang lalu',
                'keterangan' => 'Mengunggah file revisi Bab III beserta catatan perbaikan instrumen.',
                'icon' => 'fas fa-file-upload',
                'badge' => 'primary',
            ],
            [
                'judul' => 'Pengajuan Jadwal Konsultasi',
                'mahasiswa' => 'Cut Nadia Zahra (2108107010034)',
                'waktu' => '2 jam yang lalu',
                'keterangan' => 'Mengajukan jadwal bimbingan daring untuk Bab I Latar Belakang.',
                'icon' => 'fas fa-calendar-plus',
                'badge' => 'info',
            ],
            [
                'judul' => 'Persetujuan ACC Draf Bab IV',
                'mahasiswa' => 'Siti Aisyah Putri (2108107010088)',
                'waktu' => 'Kemarin, 16:45 WIB',
                'keterangan' => 'Dosen memberikan catatan perbaikan pada diagram perancangan.',
                'icon' => 'fas fa-check-circle',
                'badge' => 'success',
            ],
            [
                'judul' => 'Catatan Riwayat Bimbingan Terbit',
                'mahasiswa' => 'Rahmat Hidayat (2008107010045)',
                'waktu' => '2 hari yang lalu',
                'keterangan' => 'Sesi tatap muka persiapan draf final menuju sidang tugas akhir.',
                'icon' => 'fas fa-comments',
                'badge' => 'dark',
            ],
        ];

        $notifikasi = [
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
            [
                'judul' => 'Konsultasi KRS Mahasiswa Perwalian',
                'deskripsi' => 'Rizky Kurniawan mengajukan konsultasi IPK',
                'waktu' => 'Kemarin',
                'badge' => 'success',
                'icon' => 'fas fa-user-graduate',
            ],
        ];

        return view('dosen.beranda', compact(
            'dosen',
            'statistik',
            'jadwal_bimbingan',
            'mahasiswa_bimbingan',
            'mahasiswa_perwalian',
            'aktivitas_terbaru',
            'notifikasi'
        ));
    }
}
