<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class PengajuanBimbinganController extends Controller
{
    /**
     * Menampilkan daftar permohonan / pengajuan jadwal bimbingan dari mahasiswa.
     */
    public function index(): View
    {
        $user = Auth::user();

        $dosen = [
            'nama' => $user?->name ?? 'Dr. Siti Rahmawati, M.Kom.',
            'nip' => $user?->nomor_induk ?? '198503152010122001',
            'jabatan' => 'Lektor Kepala — Pembimbing Utama',
            'program_studi' => $user?->program_studi ?? 'Pendidikan Sistem dan Teknologi Informasi (PSTI)',
            'bidang' => 'Software Engineering & Intelligence Systems',
            'email' => $user?->email ?? 'siti.rahmawati@usk.ac.id',
            'telepon' => '+62 812-6900-8821',
            'ruangan' => 'Gedung Fasilkom Lt. 2 R.204',
            'status_hadir' => 'Tersedia di Kampus',
        ];

        $statistik = [
            'total_pengajuan' => 6,
            'menunggu_konfirmasi' => 3,
            'terkonfirmasi' => 2,
            'selesai_ditolak' => 1,
        ];

        $daftar_pengajuan = [
            [
                'id' => 101,
                'nama' => 'Andi Pratama',
                'nim' => '2108107010001',
                'program_studi' => 'PSTI',
                'jenis_bimbingan' => 'skripsi',
                'kategori_label' => 'Bimbingan Skripsi',
                'topik' => 'Review Draf Revisi Bab III & Instrumen Pengujian',
                'waktu_pengajuan' => '23 Okt 2026, 14:20 WIB',
                'jadwal_usulan' => 'Jumat, 24 Oktober 2026',
                'jam_usulan' => '09:30 - 10:30 WIB',
                'metode' => 'Luring (Tatap Muka)',
                'tempat' => 'Ruang Dosen 204 Fasilkom',
                'status' => 'menunggu',
                'badge_status' => 'warning',
                'status_label' => 'Menunggu Konfirmasi',
                'catatan_mahasiswa' => 'Mohon izin bimbingan tatap muka untuk mereview perbaikan instrumen pengujian skripsi dan flowchart sistem bab 3 yang sudah disesuaikan.',
                'file_lampiran' => 'Draf_Bab3_Revisi_Andi.pdf',
            ],
            [
                'id' => 102,
                'nama' => 'Cut Nadia Zahra',
                'nim' => '2108107010034',
                'program_studi' => 'PSTI',
                'jenis_bimbingan' => 'skripsi',
                'kategori_label' => 'Bimbingan Skripsi',
                'topik' => 'Konsultasi Bab I Latar Belakang & Identifikasi Masalah',
                'waktu_pengajuan' => '23 Okt 2026, 16:45 WIB',
                'jadwal_usulan' => 'Jumat, 24 Oktober 2026',
                'jam_usulan' => '14:00 - 15:00 WIB',
                'metode' => 'Daring (Online)',
                'tempat' => 'Google Meet (meet.google.com/sim-bimb-dosen)',
                'status' => 'menunggu',
                'badge_status' => 'warning',
                'status_label' => 'Menunggu Konfirmasi',
                'catatan_mahasiswa' => 'Izin bimbingan secara online karena saya sedang menyelesaikan survei data kuisioner di luar kota bu.',
                'file_lampiran' => 'Bab1_Pendahuluan_Nadia.pdf',
            ],
            [
                'id' => 103,
                'nama' => 'Muhammad Fikri',
                'nim' => '2208107010012',
                'program_studi' => 'PSTI',
                'jenis_bimbingan' => 'akademik',
                'kategori_label' => 'Bimbingan Akademik (DPA)',
                'topik' => 'Perencanaan KRS Semester Genap & Rencana Magang MBKM',
                'waktu_pengajuan' => '22 Okt 2026, 09:15 WIB',
                'jadwal_usulan' => 'Senin, 27 Oktober 2026',
                'jam_usulan' => '10:00 - 11:00 WIB',
                'metode' => 'Luring (Tatap Muka)',
                'tempat' => 'Ruang Dosen 204 Fasilkom',
                'status' => 'menunggu',
                'badge_status' => 'warning',
                'status_label' => 'Menunggu Konfirmasi',
                'catatan_mahasiswa' => 'Ingin konsultasi pemilihan mata kuliah peminatan dan sinkronisasi konversi SKS untuk program magang bersertifikat.',
                'file_lampiran' => 'Transkrip_KRS_Fikri.pdf',
            ],
            [
                'id' => 104,
                'nama' => 'Dimas Bagus Wicaksono',
                'nim' => '2108107010055',
                'program_studi' => 'PSTI',
                'jenis_bimbingan' => 'skripsi',
                'kategori_label' => 'Bimbingan Skripsi',
                'topik' => 'Diskusi Algoritma Rekomendasi Content-Based Filtering Bab II',
                'waktu_pengajuan' => '20 Okt 2026, 11:00 WIB',
                'jadwal_usulan' => 'Selasa, 28 Oktober 2026',
                'jam_usulan' => '13:00 - 14:00 WIB',
                'metode' => 'Daring (Online)',
                'tempat' => 'Zoom Meeting ID: 882 1092 3341',
                'status' => 'disetujui',
                'badge_status' => 'success',
                'status_label' => 'Terkonfirmasi',
                'catatan_mahasiswa' => 'Draft kajian pustaka dan perbandingan 5 jurnal acuan telah dirangkum dalam lampiran.',
                'file_lampiran' => 'Bab2_Tinjauan_Pustaka_Dimas.pdf',
            ],
            [
                'id' => 105,
                'nama' => 'Siti Aisyah Putri',
                'nim' => '2108107010088',
                'program_studi' => 'PSTI',
                'jenis_bimbingan' => 'skripsi',
                'kategori_label' => 'Bimbingan Skripsi',
                'topik' => 'Evaluasi Pengujian Arsitektur Microservices Bab IV',
                'waktu_pengajuan' => '18 Okt 2026, 08:30 WIB',
                'jadwal_usulan' => 'Rabu, 22 Oktober 2026',
                'jam_usulan' => '10:00 - 11:30 WIB',
                'metode' => 'Luring (Tatap Muka)',
                'tempat' => 'Laboratorium Rekayasa Perangkat Lunak',
                'status' => 'disetujui',
                'badge_status' => 'primary',
                'status_label' => 'Terkonfirmasi',
                'catatan_mahasiswa' => 'Pengujian performa API latency dan stress-testing server telah siap dipresentasikan.',
                'file_lampiran' => 'Bab4_Hasil_Pengujian_Aisyah.pdf',
            ],
            [
                'id' => 106,
                'nama' => 'Rizky Kurniawan',
                'nim' => '2308107010023',
                'program_studi' => 'PSTI',
                'jenis_bimbingan' => 'akademik',
                'kategori_label' => 'Bimbingan Akademik (DPA)',
                'topik' => 'Konsultasi Perbaikan Indeks Prestasi & Remedial',
                'waktu_pengajuan' => '17 Okt 2026, 13:00 WIB',
                'jadwal_usulan' => 'Kamis, 23 Oktober 2026',
                'jam_usulan' => '11:00 - 12:00 WIB',
                'metode' => 'Luring (Tatap Muka)',
                'tempat' => 'Ruang Dosen 204 Fasilkom',
                'status' => 'ditolak',
                'badge_status' => 'danger',
                'status_label' => 'Perlu Reschedule',
                'catatan_mahasiswa' => 'Jadwal bertepatan dengan rapat senat fakultas. Silakan ajukan ulang untuk hari Jumat atau Senin.',
                'file_lampiran' => null,
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
        ];

        return view('dosen.bimbingan.pengajuan', compact(
            'dosen',
            'statistik',
            'daftar_pengajuan',
            'notifikasi'
        ));
    }
}
