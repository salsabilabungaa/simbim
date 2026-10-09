<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class BimbinganSkripsiController extends Controller
{
    public function index(): View
    {
        return view('mahasiswa.bimbingan_skripsi', [
            'mahasiswa' => [
                'nama' => 'Andi Pratama',
                'nim' => '2108107010001',
                'status' => 'Mahasiswa Aktif',
                'program_studi' => 'Informatika',
                'semester' => 'Semester 7',
                'judul_skripsi' => 'Rancang Bangun Sistem Informasi Bimbingan Tugas Akhir Berbasis Web Menggunakan Arsitektur Microservices',
            ],
            'dosen_pembimbing' => [
                'nama' => 'Dr. Ir. Teuku Muhammad, M.T.',
                'nip' => '19810314 200604 1 003',
                'jabatan' => 'Lektor Kepala — Pembimbing Utama',
                'bidang' => 'Software Engineering & Cloud Infrastructure',
                'email' => 'teuku.muhammad@usk.ac.id',
                'telepon' => '+62 812-6900-1122',
                'ruangan' => 'Gedung Fasilkom Lt. 2 R.204',
                'status_hadir' => 'Tersedia di Kampus',
                'badge_status' => 'success',
            ],
            'ringkasan' => [
                'total_sesi' => 12,
                'persen_progres' => 65,
                'bab_aktif' => 'BAB III (Metodologi Penelitian)',
                'target_selesai' => 'Desember 2026',
            ],
            'jadwal_terdekat' => [
                'judul' => 'Review Draft BAB III & Evaluasi Diagram Arsitektur System',
                'tanggal' => 'Sabtu, 11 Oktober 2026',
                'waktu' => '09.30 – 11.00 WIB',
                'tempat' => 'Ruang Dosen 2.04 / Zoom',
                'sisa' => 'Besok, 09:30 WIB (H-1)',
                'is_h1' => true,
            ],
            'riwayat_bimbingan' => [
                [
                    'id' => 1,
                    'tanggal' => null,
                    'waktu' => null,
                    'topik' => 'BAB III: Metodologi Penelitian & Perancangan System Diagram',
                    'dokumen' => null,
                    'status' => 'Proses',
                    'badge' => 'warning',
                    'catatan' => 'Pengajuan bimbingan baru dikirim. Menunggu konfirmasi jadwal Dosen Pembimbing.',
                    'foto' => null,
                ],
                [
                    'id' => 2,
                    'tanggal' => '11 Okt 2026',
                    'waktu' => '09.30 - 11.00 WIB',
                    'topik' => 'BAB III: Review Flowchart & Class Diagram Sistem',
                    'dokumen' => null,
                    'status' => 'ACC',
                    'badge' => 'primary',
                    'catatan' => 'Jadwal telah disetujui Dosen Pembimbing. Harap siapkan & unggah printout draft diagram.',
                    'foto' => null,
                ],
                [
                    'id' => 3,
                    'tanggal' => '28 Sep 2026',
                    'waktu' => '13.30 - 15.00 WIB',
                    'topik' => 'BAB II: Tinjauan Pustaka & Matriks Comparative Literature',
                    'dokumen' => 'Draft_BAB2_ACC.pdf',
                    'status' => 'Selesai',
                    'badge' => 'success',
                    'catatan' => 'BAB II dinyatakan ACC dengan catatan kecil pada penulisan sitasi IEEE.',
                    'foto' => null,
                ],
                [
                    'id' => 4,
                    'tanggal' => '10 Sep 2026',
                    'waktu' => '10.00 - 11.30 WIB',
                    'topik' => 'BAB I: Pendahuluan, Rumusan Masalah & Latar Belakang',
                    'dokumen' => 'Draft_BAB1_Final.pdf',
                    'status' => 'Selesai',
                    'badge' => 'success',
                    'catatan' => 'BAB I disetujui penuh. Melanjutkan pengerjaan BAB II.',
                    'foto' => 'assets/media/svg/avatars/002-boy-2.svg',
                ],
            ],
            'progres_bab' => [
                ['bab' => 'BAB I: Pendahuluan', 'persen' => 100, 'status' => 'ACC & Selesai', 'badge' => 'success'],
                ['bab' => 'BAB II: Tinjauan Pustaka', 'persen' => 100, 'status' => 'ACC & Selesai', 'badge' => 'success'],
                ['bab' => 'BAB III: Metodologi Penelitian', 'persen' => 60, 'status' => 'Dalam Pengerjaan', 'badge' => 'primary'],
                ['bab' => 'BAB IV: Hasil & Pembahasan', 'persen' => 0, 'status' => 'Belum Dimulai', 'badge' => 'secondary'],
                ['bab' => 'BAB V: Kesimpulan & Saran', 'persen' => 0, 'status' => 'Belum Dimulai', 'badge' => 'secondary'],
            ],
            'catatan_pembimbing' => [
                [
                    'tanggal' => '28 Sep 2026',
                    'bab' => 'BAB II Tinjauan Pustaka',
                    'catatan' => 'Perbaiki sitasi jurnal internasional 3 tahun terakhir pada bagian arsitektur REST API. Struktur kajian sudah baik.',
                    'badge' => 'success',
                ],
                [
                    'tanggal' => '10 Sep 2026',
                    'bab' => 'BAB I Pendahuluan',
                    'catatan' => 'Latar belakang masalah sudah tajam. Rumusan masalah & tujuan penelitian telah sesuai dengan ruang lingkup skripsi.',
                    'badge' => 'info',
                ],
            ],
            'review_dokumen' => [
                'nama_file' => 'Draft_BAB3_Diagram_v1.pdf',
                'versi' => 'v1.2',
                'tanggal_upload' => '09 Okt 2026, 14.30 WIB',
                'catatan_review' => [
                    [
                        'halaman' => 'Halaman 12 (Paragraf 2)',
                        'catatan' => 'Tolong tambahkan diagram alur pengujian performa sistem dan latency microservices.',
                        'status' => 'Perlu Revisi',
                        'badge' => 'danger',
                    ],
                    [
                        'halaman' => 'Halaman 18 (Diagram 3.2)',
                        'catatan' => 'Class diagram sudah sesuai dengan kebutuhan ERD data.',
                        'status' => 'Disetujui',
                        'badge' => 'success',
                    ],
                    [
                        'halaman' => 'Halaman 24 (Daftar Pustaka)',
                        'catatan' => 'Format sitasi gunakan standar IEEE.',
                        'status' => 'Perlu Revisi',
                        'badge' => 'warning',
                    ],
                ],
            ],
            'diskusi_skripsi' => [
                [
                    'pengirim' => 'Dr. Ir. Teuku Muhammad, M.T.',
                    'role' => 'Dosen Pembimbing',
                    'badge_role' => 'primary',
                    'waktu' => 'Kemarin, 16.20 WIB',
                    'pesan' => 'Draft BAB III sudah saya baca. Ada beberapa poin diagram arsitektur yang perlu disesuaikan sebelum bimbingan besok.',
                ],
                [
                    'pengirim' => 'Andi Pratama',
                    'role' => 'Mahasiswa',
                    'badge_role' => 'info',
                    'waktu' => 'Kemarin, 17.05 WIB',
                    'pesan' => 'Baik Pak, untuk diagram class diagram apakah perlu ditambahkan relasi ke modul authentication?',
                ],
                [
                    'pengirim' => 'Dr. Ir. Teuku Muhammad, M.T.',
                    'role' => 'Dosen Pembimbing',
                    'badge_role' => 'primary',
                    'waktu' => 'Hari Ini, 08.15 WIB',
                    'pesan' => 'Ya betul, tambahkan relasi token JWT di modul authentication agar arsitektur microservices-nya lengkap.',
                ],
            ],
            'notifikasi' => [
                [
                    'judul' => 'Jadwal Bimbingan Skripsi BAB III telah disetujui',
                    'waktu' => '1 jam yang lalu',
                ],
                [
                    'judul' => 'Dosen Pembimbing memberikan catatan pada BAB II',
                    'waktu' => '3 hari yang lalu',
                ],
            ],
        ]);
    }
}
