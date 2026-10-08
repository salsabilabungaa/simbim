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
                'pembimbing' => 'Dr. Siti Rahmawati, M.Kom.',
            ],
            'bab' => [
                'posisi' => 'Bab III — Metodologi Penelitian',
                'persen' => 65,
                'keterangan' => 'Draft sedang direvisi sesuai masukan pembimbing.',
            ],
            'pertemuan' => [
                'judul' => 'Bimbingan Bab III',
                'tanggal' => 'Kamis, 9 Oktober 2026',
                'waktu' => '09.00 – 10.30 WIB',
                'tempat' => 'Ruang Dosen 2.04 / Google Meet',
                'sisa' => '1 hari lagi',
            ],
            'dokumen' => [
                [
                    'nama' => 'Bab3_Metodologi_Revisi.pdf',
                    'waktu' => '2 jam yang lalu',
                    'status' => 'Menunggu tinjauan',
                    'badge' => 'warning',
                ],
                [
                    'nama' => 'Instrumen_Penelitian.docx',
                    'waktu' => 'Kemarin',
                    'status' => 'Direvisi',
                    'badge' => 'info',
                ],
                [
                    'nama' => 'Bab2_Tinjauan_Pustaka.pdf',
                    'waktu' => '4 hari yang lalu',
                    'status' => 'Disetujui',
                    'badge' => 'success',
                ],
            ],
            'progres_bulanan' => [
                'labels' => ['Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt'],
                'data' => [12, 28, 40, 48, 58, 65],
            ],
            'aktivitas' => [
                [
                    'waktu' => '08:42',
                    'warna' => 'success',
                    'judul' => 'Dokumen disetujui',
                    'deskripsi' => 'Dr. Siti Rahmawati menyetujui Bab II Tinjauan Pustaka.',
                ],
                [
                    'waktu' => '10:15',
                    'warna' => 'primary',
                    'judul' => 'Balasan diskusi',
                    'deskripsi' => 'Pembimbing membalas diskusi tentang teknik pengumpulan data.',
                ],
                [
                    'waktu' => '13:20',
                    'warna' => 'warning',
                    'judul' => 'Dokumen diunggah',
                    'deskripsi' => 'Anda mengunggah Bab3_Metodologi_Revisi.pdf.',
                ],
                [
                    'waktu' => '15:05',
                    'warna' => 'info',
                    'judul' => 'Jadwal bimbingan',
                    'deskripsi' => 'Pertemuan Kamis, 9 Oktober 2026 dikonfirmasi pembimbing.',
                ],
                [
                    'waktu' => '16:40',
                    'warna' => 'danger',
                    'judul' => 'Revisi diminta',
                    'deskripsi' => 'Pembimbing meminta perbaikan rumusan masalah pada Bab I.',
                ],
            ],
            'notifikasi' => [
                [
                    'judul' => 'Bab II telah disetujui',
                    'waktu' => '8 menit yang lalu',
                ],
                [
                    'judul' => 'Balasan baru dari pembimbing',
                    'waktu' => '1 jam yang lalu',
                ],
                [
                    'judul' => 'Pengingat bimbingan besok pukul 09.00',
                    'waktu' => '3 jam yang lalu',
                ],
            ],
        ]);
    }
}
