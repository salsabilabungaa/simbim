<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class BimbinganAkademikController extends Controller
{
    public function index(): View
    {
        return view('mahasiswa.bimbingan_akademik', [
            'mahasiswa' => [
                'nama' => 'Andi Pratama',
                'nim' => '2108107010001',
                'status' => 'Mahasiswa Aktif',
                'program_studi' => 'Informatika',
                'semester' => 'Semester 7',
                'ipk' => 3.82,
                'total_sks' => 118,
                'status_krs' => 'Disetujui (21 SKS)',
                'sapaan' => 'Selamat Pagi',
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
            'ringkasan' => [
                'total_sesi' => 6,
                'disetujui' => 5,
                'menunggu' => 1,
                'ipk_kumulatif' => '3.82',
                'sks_lulus' => 118,
                'status_krs' => 'ACC Dosen Wali',
            ],
            'jadwal_terdekat' => [
                'judul' => 'Konsultasi Perencanaan KRS Semester 7 & Persiapan Kelulusan',
                'tanggal' => 'Jumat, 10 Oktober 2026',
                'waktu' => '10.00 – 11.30 WIB',
                'tempat' => 'Ruang Dosen 3.02 / Zoom Meeting',
                'metode' => 'Luring / Tatap Muka',
                'sisa' => 'Besok, 10:00 WIB (H-1)',
                'status' => 'Dikonfirmasi',
                'is_h1' => true,
            ],
            'riwayat_bimbingan' => [
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
            ],
            'catatan_dosen_wali' => [
                [
                    'tanggal' => '15 Sep 2026',
                    'topik' => 'Evaluasi Semester 6 & MBKM',
                    'catatan' => 'Pertahankan tren peningkatan IPK. Pastikan matakuliah wajib kelulusan (Metodologi & Skripsi) diambil sesuai rencana studi.',
                    'badge' => 'success',
                ],
                [
                    'tanggal' => '02 Agu 2026',
                    'topik' => 'Rekomendasi Beasiswa',
                    'catatan' => 'Mahasiswa memenuhi kualifikasi akademik dan keaktifan organisasi untuk diajukan dalam Beasiswa Prestasi 2026.',
                    'badge' => 'info',
                ],
            ],
            'notifikasi' => [
                [
                    'judul' => 'Jadwal bimbingan akademik dikonfirmasi Dosen Wali',
                    'waktu' => '20 menit yang lalu',
                ],
                [
                    'judul' => 'KRS Semester 7 telah disetujui',
                    'waktu' => '2 hari yang lalu',
                ],
            ],
        ]);
    }
}
