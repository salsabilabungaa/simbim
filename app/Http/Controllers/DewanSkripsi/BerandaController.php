<?php

namespace App\Http\Controllers\DewanSkripsi;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class BerandaController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $dewan = [
            'nama' => $user?->name ?? 'Prof. Dr. Ir. Dewan Skripsi, M.Sc.',
            'nip' => $user?->nomor_induk ?? '197908202005011002',
            'jabatan' => 'Ketua Komisi Tugas Akhir / Dewan Skripsi',
            'program_studi' => $user?->program_studi ?: (string) config('simbim.prodi'),
        ];

        $statistik = [
            'total_proposal' => 38,
            'sidang_bulan_ini' => 12,
            'menunggu_penetapan_penguji' => 4,
            'lulus_sidang' => 26,
        ];

        $jadwal_sidang = [
            [
                'id' => 1,
                'mahasiswa' => 'Muhammad Rifqi Ramadhan',
                'nim' => '2108107010012',
                'jenis' => 'Seminar Proposal (Sempro)',
                'judul' => 'Implementasi Computer Vision YOLOv8 untuk Klasifikasi Sarana Ruang Kelas Cerdas PSTI UPI Berbasis Mobile',
                'tanggal' => 'Kamis, 05 November 2026',
                'waktu' => '09:00 - 10:30 WIB',
                'ruang' => 'Ruang Sidang 1 Prodi PSTI UPI Lt. 2',
                'ketua_penguji' => 'Prof. Dr. Ir. Dewan Skripsi, M.Sc.',
                'penguji_1' => 'Dr. Siti Rahmawati, M.Kom.',
                'penguji_2' => 'Dr. Ir. Hendra, M.T.',
                'status' => 'Ditetapkan',
                'badge' => 'primary',
            ],
            [
                'id' => 2,
                'mahasiswa' => 'Dinda Putri Ayu',
                'nim' => '2108107010055',
                'jenis' => 'Sidang Meja Hijau / Komprehensif',
                'judul' => 'Analisis Sentimen Opini Publik Layanan Akademik PSTI UPI pada Media Sosial Menggunakan Pendekatan IndoBERT',
                'tanggal' => 'Jumat, 06 November 2026',
                'waktu' => '14:00 - 16:00 WIB',
                'ruang' => 'Ruang Multimedia Utama PSTI UPI',
                'ketua_penguji' => 'Prof. Dr. Ir. Dewan Skripsi, M.Sc.',
                'penguji_1' => 'Prof. Dr. Ir. Ahmad Rizal, M.T.',
                'penguji_2' => 'Dr. Ir. Teuku Muhammad, M.T.',
                'status' => 'Siap Ujian',
                'badge' => 'success',
            ],
        ];

        $jadwal_sidang = $this->paginateItems($jadwal_sidang, 6);

        return view('dewan_skripsi.beranda', compact('dewan', 'statistik', 'jadwal_sidang'));
    }
}
