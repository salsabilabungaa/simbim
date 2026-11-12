<?php

namespace Database\Seeders;

use App\Models\Skripsi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SkripsiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prodi = (string) config('simbim.prodi', 'Pendidikan Sistem dan Teknologi Informasi (PSTI)');

        // 1. Akun Dewan Skripsi
        $dewan = User::updateOrCreate(
            ['email' => 'dewanskripsi@simbim.ac.id'],
            [
                'name' => 'Dewan Skripsi',
                'role' => 'dewan_skripsi',
                'nomor_induk' => '197908202005011004',
                'nip' => '197908202005011004',
                'program_studi' => $prodi,
                'password' => 'password',
            ]
        );

        User::updateOrCreate(
            ['email' => 'dskripsi@simbim.ac.id'],
            [
                'name' => 'Dewan Skripsi',
                'role' => 'dewan_skripsi',
                'nomor_induk' => '197908202005011003',
                'nip' => '197908202005011003',
                'program_studi' => $prodi,
                'password' => 'password',
            ]
        );

        // Akun dewan alternatif / existing
        User::updateOrCreate(
            ['email' => 'dewan@simbim.ac.id'],
            [
                'name' => 'Prof. Dr. Ir. Dewan Skripsi, M.Sc.',
                'role' => 'dewan_skripsi',
                'nomor_induk' => '197908202005011002',
                'nip' => '197908202005011002',
                'program_studi' => $prodi,
                'password' => 'password',
            ]
        );

        // 2. Akun Dosen
        $dosen1 = User::updateOrCreate(
            ['email' => 'dosen@simbim.ac.id'],
            [
                'name' => 'Dr. Siti Rahmawati, M.Kom.',
                'role' => 'dosen',
                'nomor_induk' => '198503152010122001',
                'nip' => '198503152010122001',
                'program_studi' => $prodi,
                'password' => 'password',
            ]
        );

        $dosen2 = User::updateOrCreate(
            ['email' => 'teuku.muhammad@simbim.ac.id'],
            [
                'name' => 'Dr. Ir. Teuku Muhammad, M.T.',
                'role' => 'dosen',
                'nomor_induk' => '198005122008011003',
                'nip' => '198005122008011003',
                'program_studi' => $prodi,
                'password' => 'password',
            ]
        );

        $dosen3 = User::updateOrCreate(
            ['email' => 'ahmad.rizal@simbim.ac.id'],
            [
                'name' => 'Prof. Dr. Ir. Ahmad Rizal, M.T.',
                'role' => 'dosen',
                'nomor_induk' => '197211181997021001',
                'nip' => '197211181997021001',
                'program_studi' => $prodi,
                'password' => 'password',
            ]
        );

        $dosen4 = User::updateOrCreate(
            ['email' => 'hendra@simbim.ac.id'],
            [
                'name' => 'Dr. Ir. Hendra, M.T.',
                'role' => 'dosen',
                'nomor_induk' => '198909142019031011',
                'nip' => '198909142019031011',
                'program_studi' => $prodi,
                'password' => 'password',
            ]
        );

        // 3. Mahasiswa & Data Skripsi 5 Tahapan Lengkap

        // Mhs 1: Andi Pratama - Proses Bimbingan Aktif
        $mhs1 = User::updateOrCreate(
            ['email' => 'mahasiswa@simbim.ac.id'],
            [
                'name' => 'Andi Pratama',
                'role' => 'mahasiswa',
                'nomor_induk' => '2108107010001',
                'nim' => '2108107010001',
                'program_studi' => $prodi,
                'password' => 'password',
            ]
        );

        Skripsi::updateOrCreate(
            ['mahasiswa_id' => $mhs1->id],
            [
                'judul' => 'Rancang Bangun Sistem Informasi Bimbingan Tugas Akhir Berbasis Web Menggunakan Framework Laravel',
                'bidang_minat' => 'Software Engineering',
                'tahap' => 'Proses Bimbingan Aktif',
                'pembimbing_1_id' => $dosen1->id,
                'pembimbing_1_nama' => $dosen1->name,
                'pembimbing_2_id' => $dosen2->id,
                'pembimbing_2_nama' => $dosen2->name,
                'penguji_1_id' => $dosen3->id,
                'penguji_1_nama' => $dosen3->name,
                'penguji_2_id' => $dosen4->id,
                'penguji_2_nama' => $dosen4->name,
                'total_bimbingan' => 8,
                'acc_pembimbing' => false,
                'status_pendaftaran_sidang' => 'Belum Mendaftar',
                'status_sidang' => 'Belum Sidang',
                'status_kelulusan' => 'Belum Lulus',
            ]
        );

        // Mhs 2: Siti Sarah - Siap Sidang (Menunggu Approval Pendaftaran Sidang Dewan)
        $mhs2 = User::updateOrCreate(
            ['email' => 'siti.sarah@simbim.ac.id'],
            [
                'name' => 'Siti Sarah',
                'role' => 'mahasiswa',
                'nomor_induk' => '2108107010014',
                'nim' => '2108107010014',
                'program_studi' => $prodi,
                'password' => 'password',
            ]
        );

        Skripsi::updateOrCreate(
            ['mahasiswa_id' => $mhs2->id],
            [
                'judul' => 'Implementasi Algoritma YOLOv8 untuk Deteksi Kematangan Buah Kopi PSTI Berbasis Citra Digital',
                'bidang_minat' => 'Artificial Intelligence',
                'tahap' => 'Siap Sidang',
                'pembimbing_1_id' => $dosen1->id,
                'pembimbing_1_nama' => $dosen1->name,
                'pembimbing_2_id' => $dosen4->id,
                'pembimbing_2_nama' => $dosen4->name,
                'penguji_1_id' => $dosen2->id,
                'penguji_1_nama' => $dosen2->name,
                'penguji_2_id' => $dosen3->id,
                'penguji_2_nama' => $dosen3->name,
                'total_bimbingan' => 10,
                'acc_pembimbing' => true,
                'catatan_pembimbing' => 'Naskah skripsi Bab 1 s/d Bab 5 sudah lengkap dan layak untuk diajukan ke Sidang Skripsi.',
                'file_syarat_sidang' => 'berkas_syarat_sidang_siti_sarah.pdf',
                'status_pendaftaran_sidang' => 'Menunggu Persetujuan',
                'status_sidang' => 'Belum Sidang',
                'status_kelulusan' => 'Belum Lulus',
            ]
        );

        // Mhs 3: Budi Santoso - Penyusunan Proposal (Plotting Belum Lengkap)
        $mhs3 = User::updateOrCreate(
            ['email' => 'budi.santoso@simbim.ac.id'],
            [
                'name' => 'Budi Santoso',
                'role' => 'mahasiswa',
                'nomor_induk' => '2108107010025',
                'nim' => '2108107010025',
                'program_studi' => $prodi,
                'password' => 'password',
            ]
        );

        Skripsi::updateOrCreate(
            ['mahasiswa_id' => $mhs3->id],
            [
                'judul' => 'Pengembangan Model NLP IndoBERT untuk Analisis Kepuasan Pengguna Aplikasi Mobile Learning',
                'bidang_minat' => 'Natural Language Processing',
                'tahap' => 'Penyusunan Proposal',
                'pembimbing_1_id' => $dosen2->id,
                'pembimbing_1_nama' => $dosen2->name,
                'pembimbing_2_id' => null,
                'pembimbing_2_nama' => null,
                'penguji_1_id' => null,
                'penguji_1_nama' => null,
                'penguji_2_id' => null,
                'penguji_2_nama' => null,
                'total_bimbingan' => 3,
                'acc_pembimbing' => false,
                'status_pendaftaran_sidang' => 'Belum Mendaftar',
                'status_sidang' => 'Belum Sidang',
                'status_kelulusan' => 'Belum Lulus',
            ]
        );

        // Mhs 4: Cut Meutia - Revisi Final (Pasca-Sidang, Verifikasi & Rekap Nilai)
        $mhs4 = User::updateOrCreate(
            ['email' => 'cut.meutia@simbim.ac.id'],
            [
                'name' => 'Cut Meutia',
                'role' => 'mahasiswa',
                'nomor_induk' => '2108107010033',
                'nim' => '2108107010033',
                'program_studi' => $prodi,
                'password' => 'password',
            ]
        );

        Skripsi::updateOrCreate(
            ['mahasiswa_id' => $mhs4->id],
            [
                'judul' => 'Audit Keamanan Sistem Informasi Akademik Menggunakan Kerangka Kerja NIST CSF 2.0',
                'bidang_minat' => 'Cyber Security & Governance',
                'tahap' => 'Revisi Final',
                'pembimbing_1_id' => $dosen1->id,
                'pembimbing_1_nama' => $dosen1->name,
                'pembimbing_2_id' => $dosen2->id,
                'pembimbing_2_nama' => $dosen2->name,
                'penguji_1_id' => $dosen3->id,
                'penguji_1_nama' => $dosen3->name,
                'penguji_2_id' => $dosen4->id,
                'penguji_2_nama' => $dosen4->name,
                'total_bimbingan' => 12,
                'acc_pembimbing' => true,
                'status_pendaftaran_sidang' => 'Disetujui',
                'jadwal_sidang' => Carbon::now()->subDays(5)->setTime(10, 0),
                'ruangan_sidang' => 'Ruang Sidang Utama Lt. 2',
                'status_sidang' => 'Selesai',
                'file_revisi_final' => 'revisi_final_cut_meutia.pdf',
                'catatan_revisi_final' => 'Telah diperbaiki sesuai catatan dewan penguji: penambahan diagram arsitektur dan penyesuaian mitigasi risiko.',
                'tanggal_unggah_revisi' => Carbon::now()->subDays(2),
                'status_kelulusan' => 'Revisi Sidang',
            ]
        );

        // Mhs 5: Fajar Ramadhan - Lulus / Selesai (SKL Dikeluarkan, Siap Yudisium)
        $mhs5 = User::updateOrCreate(
            ['email' => 'fajar.ramadhan@simbim.ac.id'],
            [
                'name' => 'Fajar Ramadhan',
                'role' => 'mahasiswa',
                'nomor_induk' => '2108107010042',
                'nim' => '2108107010042',
                'program_studi' => $prodi,
                'password' => 'password',
            ]
        );

        Skripsi::updateOrCreate(
            ['mahasiswa_id' => $mhs5->id],
            [
                'judul' => 'Penerapan Algoritma Dijkstra Pada Sistem Informasi Navigasi Kampus PSTI UPI',
                'bidang_minat' => 'Geographic Information System',
                'tahap' => 'Lulus/Selesai',
                'pembimbing_1_id' => $dosen1->id,
                'pembimbing_1_nama' => $dosen1->name,
                'pembimbing_2_id' => $dosen3->id,
                'pembimbing_2_nama' => $dosen3->name,
                'penguji_1_id' => $dosen2->id,
                'penguji_1_nama' => $dosen2->name,
                'penguji_2_id' => $dosen4->id,
                'penguji_2_nama' => $dosen4->name,
                'total_bimbingan' => 14,
                'acc_pembimbing' => true,
                'status_pendaftaran_sidang' => 'Disetujui',
                'jadwal_sidang' => Carbon::now()->subDays(20)->setTime(9, 0),
                'ruangan_sidang' => 'Ruang Sidang 1, Lt. 3',
                'status_sidang' => 'Selesai',
                'file_revisi_final' => 'naskah_final_fajar_ramadhan.pdf',
                'catatan_revisi_final' => 'Semua perbaikan dewan penguji telah diverifikasi dan disahkan.',
                'tanggal_unggah_revisi' => Carbon::now()->subDays(15),
                'nilai_pembimbing_1' => 88.5,
                'nilai_pembimbing_2' => 89.0,
                'nilai_penguji_1' => 87.0,
                'nilai_penguji_2' => 88.0,
                'nilai_akhir' => 88.13,
                'nilai_huruf' => 'A',
                'status_kelulusan' => 'Lulus/Selesai',
                'tanggal_lulus' => Carbon::now()->subDays(10)->toDateString(),
                'no_surat_keterangan' => 'SKL/FT-PSTI/SKR/2026/089',
                'catatan_dewan' => 'Mahasiswa dinyatakan Lulus Sidang Tugas Akhir dan berhak mendaftar Yudisium Sarjana Komputer.',
            ]
        );

        // Mhs 6: Rizky Maulana - Siap Sidang (Jadwal Sidang Ditetapkan)
        $mhs6 = User::updateOrCreate(
            ['email' => 'rizky.maulana@simbim.ac.id'],
            [
                'name' => 'Rizky Maulana',
                'role' => 'mahasiswa',
                'nomor_induk' => '2108107010055',
                'nim' => '2108107010055',
                'program_studi' => $prodi,
                'password' => 'password',
            ]
        );

        Skripsi::updateOrCreate(
            ['mahasiswa_id' => $mhs6->id],
            [
                'judul' => 'Sistem Deteksi Intrusi Jaringan Internet of Things (IoT) Berbasis Deep Autoencoder',
                'bidang_minat' => 'Cyber Security',
                'tahap' => 'Siap Sidang',
                'pembimbing_1_id' => $dosen2->id,
                'pembimbing_1_nama' => $dosen2->name,
                'pembimbing_2_id' => $dosen4->id,
                'pembimbing_2_nama' => $dosen4->name,
                'penguji_1_id' => $dosen1->id,
                'penguji_1_nama' => $dosen1->name,
                'penguji_2_id' => $dosen3->id,
                'penguji_2_nama' => $dosen3->name,
                'total_bimbingan' => 9,
                'acc_pembimbing' => true,
                'status_pendaftaran_sidang' => 'Disetujui',
                'jadwal_sidang' => Carbon::now()->addDays(3)->setTime(9, 30),
                'ruangan_sidang' => 'Ruang Multimedia Utama PSTI UPI',
                'jenis_sidang' => 'Sidang Skripsi / Munaqasyah',
                'status_sidang' => 'Dijadwalkan',
                'status_kelulusan' => 'Belum Lulus',
            ]
        );
    }
}
