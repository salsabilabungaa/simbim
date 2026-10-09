<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class BerandaController extends Controller
{
    public function index(): View
    {
        return view('mahasiswa.beranda', [
            'mahasiswa' => [
                'nama' => 'Andi Pratama',
                'nim' => '2108107010001',
                'status' => 'Mahasiswa Aktif',
                'program_studi' => 'Informatika',
                'semester' => 'Semester 7',
                'pembimbing' => 'Dr. Siti Rahmawati, M.Kom.',
                'sapaan' => 'Selamat Pagi',
            ],
            'dosen_pembimbing' => [
                'nama' => 'Dr. Siti Rahmawati, M.Kom.',
                'nip' => '19850315 201012 2 001',
                'jabatan' => 'Lektor Kepala — Pembimbing Utama',
                'bidang' => 'Software Engineering & Intelligence Systems',
                'email' => 'siti.rahmawati@usk.ac.id',
                'telepon' => '+62 812-6900-8821',
                'ruangan' => 'Gedung Fasilkom Lt. 2 R.204',
                'status_hadir' => 'Tersedia di Kampus',
                'badge_status' => 'success',
            ],
            'dosen_wali' => [
                'nama' => 'Prof. Dr. Ir. Ahmad Rizal, M.T.',
                'nip' => '19780512 200312 1 002',
                'jabatan' => 'Guru Besar — Dosen Wali Akademik',
                'bidang' => 'Distributed Systems & Cloud Computing',
                'email' => 'ahmad.rizal@usk.ac.id',
                'telepon' => '+62 811-6800-4321',
                'ruangan' => 'Gedung Fasilkom Lt. 3 R.302',
                'status_hadir' => 'Tersedia di Kampus',
                'badge_status' => 'success',
            ],
            'bab' => [
                'posisi' => 'Bab III — Metodologi Penelitian',
                'persen' => 65,
                'keterangan' => 'Draft revisi ke-2 sedang dikaji oleh dosen pembimbing.',
                'target_selesai' => '25 Oktober 2026',
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
                    'total' => 2,
                    'target' => 2,
                    'persen' => 100,
                    'status' => '2 Sesi Terlaksana',
                    'badge' => 'info',
                ],
                'total_bulan_ini' => 6,
                'status_kuota' => 'Bulan Ini Terpenuhi',
            ],
            'pertemuan' => [
                'judul' => 'Bimbingan Bab III (Metodologi & Data)',
                'tanggal' => 'Kamis, 9 Oktober 2026',
                'waktu' => '09.00 – 10.30 WIB',
                'tempat' => 'Ruang Dosen 2.04 / Google Meet',
                'tipe' => 'Luring / Tatap Muka',
                'sisa' => 'Besok, 09:00 WIB (H-1)',
                'status' => 'Dikonfirmasi',
                'is_h1' => true,
            ],
            'dokumen' => [
                [
                    'nama' => 'Bab3_Metodologi_Revisi2.pdf',
                    'waktu' => '2 jam yang lalu',
                    'status' => 'Menunggu Tinjauan',
                    'badge' => 'warning',
                ],
                [
                    'nama' => 'Instrumen_Pengujian.docx',
                    'waktu' => 'Kemarin',
                    'status' => 'Perlu Perbaikan',
                    'badge' => 'danger',
                ],
                [
                    'nama' => 'Bab2_Tinjauan_Pustaka_ACC.pdf',
                    'waktu' => '4 hari yang lalu',
                    'status' => 'Disetujui',
                    'badge' => 'success',
                ],
            ],
            'progres_bulanan' => [
                'labels' => ['Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt'],
                'data' => [15, 30, 42, 50, 58, 65],
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
            'aktivitas' => [
                [
                    'waktu' => '08:42',
                    'warna' => 'success',
                    'judul' => 'Dokumen Bab II Disetujui',
                    'deskripsi' => 'Dr. Siti Rahmawati menyetujui Bab II Tinjauan Pustaka.',
                ],
                [
                    'waktu' => '10:15',
                    'warna' => 'primary',
                    'judul' => 'Feedback Baru Diterima',
                    'deskripsi' => 'Catatan revisi untuk Bab III telah ditambahkan pembimbing.',
                ],
                [
                    'waktu' => '13:20',
                    'warna' => 'warning',
                    'judul' => 'Draft Bab III Diunggah',
                    'deskripsi' => 'Anda telah mengunggah berkas Bab3_Metodologi_Revisi2.pdf.',
                ],
                [
                    'waktu' => '15:05',
                    'warna' => 'info',
                    'judul' => 'Jadwal Bimbingan Dikonfirmasi',
                    'deskripsi' => 'Pertemuan bimbingan Kamis, 9 Okt 2026 dikonfirmasi.',
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
