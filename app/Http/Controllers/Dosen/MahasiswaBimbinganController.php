<?php

namespace App\Http\Controllers\Dosen;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class MahasiswaBimbinganController extends BaseDosenController
{
    /**
     * Menampilkan daftar seluruh mahasiswa bimbingan dosen (Skripsi dan Perwalian Akademik).
     */
    public function index(): View
    {
        $user = Auth::user();
        $dosen = $this->resolveDosenProfile($user);
        $prodi = (string) config('simbim.prodi');

        $dbSkripsi = $this->fetchDatabaseSkripsiBimbingan($user);
        $dbPerwalian = $this->fetchDatabasePerwalian($user);

        $daftar_mahasiswa = [
            [
                'id' => 1,
                'nama' => 'Andi Pratama',
                'nim' => '2108107010001',
                'angkatan' => '2021',
                'program_studi' => $prodi,
                'jenis_bimbingan' => 'skripsi',
                'kategori_label' => 'Bimbingan Skripsi',
                'peran_dosen' => 'Pembimbing Utama',
                'judul' => 'Pengembangan Sistem Informasi Manajemen Bimbingan Skripsi Berbasis Web',
                'bab_aktif' => 'Bab III (Metodologi Penelitian)',
                'progres' => 65,
                'status_draf' => 'Menunggu Review Dosen',
                'badge_status' => 'warning',
                'jumlah_sesi' => 6,
                'tanggal_update' => '22 Okt 2026',
                'berkas_draf' => 'Draf_Bab_III_Andi_Pratama.pdf',
            ],
            [
                'id' => 2,
                'nama' => 'Cut Nadia Zahra',
                'nim' => '2108107010034',
                'angkatan' => '2021',
                'program_studi' => $prodi,
                'jenis_bimbingan' => 'skripsi',
                'kategori_label' => 'Bimbingan Skripsi',
                'peran_dosen' => 'Pembimbing Utama',
                'judul' => 'Penerapan Algoritma Random Forest pada Klasifikasi Kepuasan Mahasiswa',
                'bab_aktif' => 'Bab I (Pendahuluan)',
                'progres' => 25,
                'status_draf' => 'Revisi Diperlukan',
                'badge_status' => 'danger',
                'jumlah_sesi' => 2,
                'tanggal_update' => '21 Okt 2026',
                'berkas_draf' => 'Bab1_Pendahuluan_Nadia.pdf',
            ],
            [
                'id' => 3,
                'nama' => 'Rahmat Hidayat',
                'nim' => '2008107010045',
                'angkatan' => '2020',
                'program_studi' => $prodi,
                'jenis_bimbingan' => 'skripsi',
                'kategori_label' => 'Bimbingan Skripsi',
                'peran_dosen' => 'Pembimbing Utama',
                'judul' => 'Analisis Sentimen Opini Publik Menggunakan Deep Learning Transformer',
                'bab_aktif' => 'Bab V (Kesimpulan & ACC Sidang)',
                'progres' => 95,
                'status_draf' => 'Siap Sidang Skripsi',
                'badge_status' => 'success',
                'jumlah_sesi' => 12,
                'tanggal_update' => '20 Okt 2026',
                'berkas_draf' => 'Naskah_Lengkap_Rahmat_Final.pdf',
            ],
            [
                'id' => 4,
                'nama' => 'Siti Aisyah Putri',
                'nim' => '2108107010088',
                'angkatan' => '2021',
                'program_studi' => $prodi,
                'jenis_bimbingan' => 'skripsi',
                'kategori_label' => 'Bimbingan Skripsi',
                'peran_dosen' => 'Pembimbing Pendamping',
                'judul' => 'Implementasi Arsitektur Microservices pada Aplikasi E-Commerce UMKM',
                'bab_aktif' => 'Bab IV (Implementasi & Evaluasi)',
                'progres' => 80,
                'status_draf' => 'Draf Sedang Direview',
                'badge_status' => 'primary',
                'jumlah_sesi' => 8,
                'tanggal_update' => '19 Okt 2026',
                'berkas_draf' => 'Bab4_Implementasi_Aisyah.pdf',
            ],
            [
                'id' => 5,
                'nama' => 'Dimas Bagus Wicaksono',
                'nim' => '2108107010055',
                'angkatan' => '2021',
                'program_studi' => $prodi,
                'jenis_bimbingan' => 'skripsi',
                'kategori_label' => 'Bimbingan Skripsi',
                'peran_dosen' => 'Pembimbing Utama',
                'judul' => 'Sistem Rekomendasi Pemilihan Topik Tugas Akhir Berbasis Content-Based Filtering',
                'bab_aktif' => 'Bab II (Tinjauan Pustaka)',
                'progres' => 45,
                'status_draf' => 'Bimbingan Terjadwal',
                'badge_status' => 'info',
                'jumlah_sesi' => 4,
                'tanggal_update' => '18 Okt 2026',
                'berkas_draf' => 'Bab2_Tinjauan_Dimas.pdf',
            ],
            [
                'id' => 6,
                'nama' => 'Farah Annisa',
                'nim' => '2108107010072',
                'angkatan' => '2021',
                'program_studi' => $prodi,
                'jenis_bimbingan' => 'skripsi',
                'kategori_label' => 'Bimbingan Skripsi',
                'peran_dosen' => 'Pembimbing Pendamping',
                'judul' => 'Analisis Keamanan Jaringan IoT Menggunakan Intrusion Detection System',
                'bab_aktif' => 'Bab III (Perancangan Sistem)',
                'progres' => 60,
                'status_draf' => 'Revisi Diperlukan',
                'badge_status' => 'warning',
                'jumlah_sesi' => 5,
                'tanggal_update' => '15 Okt 2026',
                'berkas_draf' => 'Bab3_Desain_Farah.pdf',
            ],
            [
                'id' => 7,
                'nama' => 'Muhammad Fikri',
                'nim' => '2208107010012',
                'angkatan' => '2022',
                'program_studi' => $prodi,
                'jenis_bimbingan' => 'akademik',
                'kategori_label' => 'Bimbingan Akademik (DPA)',
                'peran_dosen' => 'Dosen Pembimbing Akademik',
                'judul' => 'Konsultasi Evaluasi IPK & Perencanaan Magang MBKM',
                'bab_aktif' => 'Semester 5 — 88 SKS (IPK: 3.82)',
                'progres' => 70,
                'status_draf' => 'KRS Disetujui',
                'badge_status' => 'success',
                'jumlah_sesi' => 5,
                'tanggal_update' => '10 Okt 2026',
                'berkas_draf' => 'KRS_Semester5_Fikri.pdf',
            ],
            [
                'id' => 8,
                'nama' => 'Dinda Maulida',
                'nim' => '2208107010065',
                'angkatan' => '2022',
                'program_studi' => $prodi,
                'jenis_bimbingan' => 'akademik',
                'kategori_label' => 'Bimbingan Akademik (DPA)',
                'peran_dosen' => 'Dosen Pembimbing Akademik',
                'judul' => 'Konsultasi Pengambilan SKS Pilihan & Minat Studi',
                'bab_aktif' => 'Semester 5 — 86 SKS (IPK: 3.65)',
                'progres' => 70,
                'status_draf' => 'KRS Disetujui',
                'badge_status' => 'success',
                'jumlah_sesi' => 4,
                'tanggal_update' => '08 Okt 2026',
                'berkas_draf' => 'KRS_Semester5_Dinda.pdf',
            ],
            [
                'id' => 9,
                'nama' => 'Rizky Kurniawan',
                'nim' => '2308107010023',
                'angkatan' => '2023',
                'program_studi' => $prodi,
                'jenis_bimbingan' => 'akademik',
                'kategori_label' => 'Bimbingan Akademik (DPA)',
                'peran_dosen' => 'Dosen Pembimbing Akademik',
                'judul' => 'Konsultasi Remedial Nilai Mata Kuliah Dasar',
                'bab_aktif' => 'Semester 3 — 44 SKS (IPK: 3.40)',
                'progres' => 40,
                'status_draf' => 'Perlu Konsultasi SKS',
                'badge_status' => 'warning',
                'jumlah_sesi' => 3,
                'tanggal_update' => '12 Okt 2026',
                'berkas_draf' => 'Kartu_Hasil_Studi_Rizky.pdf',
            ],
            [
                'id' => 10,
                'nama' => 'Nabila Putri',
                'nim' => '2408107010009',
                'angkatan' => '2024',
                'program_studi' => $prodi,
                'jenis_bimbingan' => 'akademik',
                'kategori_label' => 'Bimbingan Akademik (DPA)',
                'peran_dosen' => 'Dosen Pembimbing Akademik',
                'judul' => 'Bimbingan Adaptasi Perkuliahan Mahasiswa Baru & Paket SKS',
                'bab_aktif' => 'Semester 1 — 20 SKS (IPK: -)',
                'progres' => 20,
                'status_draf' => 'KRS Baru Diajukan',
                'badge_status' => 'info',
                'jumlah_sesi' => 1,
                'tanggal_update' => '05 Okt 2026',
                'berkas_draf' => 'Formulir_KRS_Nabila.pdf',
            ],
        ];

        $akademikRows = array_values(array_filter(
            $daftar_mahasiswa,
            fn (array $item): bool => $item['jenis_bimbingan'] === 'akademik'
        ));

        if (! empty($dbPerwalian)) {
            $akademikRows = array_map(fn (array $item, int $idx): array => [
                'id' => 500 + $idx,
                'dokumen_id' => null,
                'nama' => $item['nama'],
                'nim' => $item['nim'],
                'angkatan' => $item['angkatan'],
                'program_studi' => $item['program_studi'] ?? $prodi,
                'jenis_bimbingan' => 'akademik',
                'kategori_label' => 'Bimbingan Akademik (DPA)',
                'peran_dosen' => 'Dosen Pembimbing Akademik',
                'judul' => 'Perwalian Akademik & Validasi KRS Mahasiswa',
                'bab_aktif' => $item['semester'].' — '.$item['sks_lulus'].' SKS (IPK: '.$item['ipk'].')',
                'progres' => $item['badge_status'] === 'success' ? 100 : 50,
                'status_draf' => $item['status_krs'],
                'badge_status' => $item['badge_status'],
                'jumlah_sesi' => 2,
                'tanggal_update' => 'Semester Berjalan',
                'berkas_draf' => 'KRS_'.$item['nim'].'.pdf',
            ], $dbPerwalian, array_keys($dbPerwalian));
        }

        $skripsiRows = ! empty($dbSkripsi)
            ? $dbSkripsi
            : array_values(array_filter(
                $daftar_mahasiswa,
                fn (array $item): bool => $item['jenis_bimbingan'] === 'skripsi'
            ));

        $daftar_mahasiswa = array_merge($skripsiRows, $akademikRows);

        $statistik = [
            'total_mahasiswa' => count($daftar_mahasiswa),
            'bimbingan_skripsi' => count($skripsiRows),
            'perwalian_akademik' => count($akademikRows),
            'siap_sidang' => count(array_filter(
                $skripsiRows,
                fn (array $item): bool => ($item['status_draf'] ?? '') === 'Siap Sidang Skripsi'
            )),
        ];

        $notifikasi = $this->defaultNotifikasi();

        return view('dosen.bimbingan.mahasiswa', compact(
            'dosen',
            'statistik',
            'daftar_mahasiswa',
            'notifikasi'
        ));
    }
}
