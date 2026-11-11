<?php

namespace Database\Seeders;

use App\Models\SkripsiDiskusiPesan;
use App\Models\SkripsiDokumen;
use App\Models\SkripsiDokumenKomentar;
use App\Models\User;
use Illuminate\Database\Seeder;

class SkripsiRevisiSeeder extends Seeder
{
    /**
     * Seed data dokumen revisi skripsi, komentar langsung per bagian, dan diskusi dua arah.
     */
    public function run(): void
    {
        $prodiPsti = config('simbim.prodi', 'Pendidikan Sistem dan Teknologi Informasi (PSTI)');

        $dosenUtama = User::firstOrCreate(
            ['email' => 'dosen@simbim.ac.id'],
            [
                'name' => 'Dr. Siti Rahmawati, M.Kom.',
                'role' => 'dosen',
                'nomor_induk' => '198503152010122001',
                'program_studi' => $prodiPsti,
                'password' => 'password',
            ]
        );

        $dokumenSeeds = [
            [
                'email' => 'mahasiswa@simbim.ac.id',
                'name' => 'Andi Pratama',
                'nim' => '2108107010001',
                'judul_skripsi' => 'Rancang Bangun Sistem Informasi Bimbingan Tugas Akhir Berbasis Web Menggunakan Arsitektur Microservices',
                'bab_aktif' => 'Bab III (Metodologi Penelitian)',
                'nama_file' => 'Draft_BAB3_Diagram_v1.2.pdf',
                'versi' => 'v1.2',
                'progres_persen' => 65,
                'status_draf' => 'Menunggu Review Dosen',
                'jumlah_sesi' => 6,
                'konten_naskah' => "## BAB III — METODOLOGI PENELITIAN\n\n### 3.1 Pendekatan Pengembangan Sistem\nPenelitian ini menerapkan pendekatan Design Science Research (DSR) yang dipadukan dengan siklus pengembangan perangkat lunak iteratif pada Program Studi Pendidikan Sistem dan Teknologi Informasi (PSTI) Universitas Pendidikan Indonesia (UPI). Tahapan diawali dengan identifikasi masalah alur perwalian dan bimbingan tugas akhir secara terintegrasi.\n\n### 3.2 Alur Pengolahan Data & Arsitektur Microservices\nPada sub-bab 3.2, alur pengolahan data dirancang memisahkan layanan autentikasi terpusat dengan layanan manajemen logbook bimbingan menggunakan gerbang API. Namun diagram alur pengolahan data beserta penentuan variabel dependen dan independen pengujian latensi perlu diperinci agar keterhubungan antar layanan mudah diverifikasi.\n\n### 3.3 Pemodelan Class Diagram & Skema Basis Data (ERD)\nPemodelan struktur basis data relasional menggambarkan entitas Mahasiswa, DosenPembimbing, SkripsiDokumen, dan LogKonsultasi secara ternormalisasi. Rancangan Class diagram telah disesuaikan dengan kebutuhan Entity Relationship Diagram (ERD) data pada modul autentikasi berbasis token serta riwayat revisi dokumen.\n\n### 3.4 Instrumen Pengujian & Daftar Pustaka Acuan\nPengujian dilakukan memakai metode black-box testing dan uji beban server (stress testing). Penyusunan daftar pustaka masih menggunakan campuran gaya penulisan sitasi yang nantinya akan diseragamkan mengikuti standar format sitasi IEEE tahun 2026.",
                'komentars' => [
                    [
                        'lokasi' => 'Halaman 12 (Sub-bab 3.2)',
                        'kutipan' => 'diagram alur pengolahan data beserta penentuan variabel dependen dan independen pengujian latensi perlu diperinci',
                        'komentar' => 'Tolong perjelas diagram alur pengolahan data pada sub-bab 3.2 dan tambahkan tabel indikator variabel dependen dan independen.',
                        'status' => 'Perlu Revisi',
                        'resolved' => false,
                    ],
                    [
                        'lokasi' => 'Halaman 18 (Diagram 3.2)',
                        'kutipan' => 'Class diagram telah disesuaikan dengan kebutuhan Entity Relationship Diagram (ERD) data pada modul autentikasi berbasis token',
                        'komentar' => 'Class diagram sudah sesuai dengan kebutuhan ERD data dan relasi modul autentikasi.',
                        'status' => 'Disetujui',
                        'resolved' => true,
                    ],
                    [
                        'lokasi' => 'Halaman 24 (Daftar Pustaka)',
                        'kutipan' => 'campuran gaya penulisan sitasi yang nantinya akan diseragamkan mengikuti standar format sitasi IEEE tahun 2026',
                        'komentar' => 'Format sitasi pada Daftar Pustaka wajib diseragamkan menggunakan standar IEEE.',
                        'status' => 'Perlu Revisi',
                        'resolved' => false,
                    ],
                ],
                'diskusi' => [
                    [
                        'peran' => 'dosen',
                        'pesan' => 'Draft BAB III v1.2 sudah saya periksa. Silakan cek tiga sorotan komentar langsung pada dokumen sebelum sesi bimbingan.',
                    ],
                    [
                        'peran' => 'mahasiswa',
                        'pesan' => 'Baik Bu, untuk diagram class diagram apakah perlu ditambahkan relasi khusus ke modul log komentar dokumen revisi?',
                    ],
                    [
                        'peran' => 'dosen',
                        'pesan' => 'Ya betul Andi, tambahkan relasi komentar langsung dan riwayat revisi agar sinkronisasi dokumen terlihat jelas di bab perancangan.',
                    ],
                ],
            ],
            [
                'email' => 'nadia.zahra@upi.edu',
                'name' => 'Cut Nadia Zahra',
                'nim' => '2208107010034',
                'judul_skripsi' => 'Penerapan Algoritma Random Forest pada Klasifikasi Kepuasan Mahasiswa Prodi PSTI UPI',
                'bab_aktif' => 'Bab I (Pendahuluan)',
                'nama_file' => 'Bab1_Pendahuluan_Nadia.pdf',
                'versi' => 'v1.0',
                'progres_persen' => 25,
                'status_draf' => 'Revisi Diperlukan',
                'jumlah_sesi' => 2,
                'konten_naskah' => "## BAB I — PENDAHULUAN\n\n### 1.1 Latar Belakang Masalah\nEvaluasi kepuasan layanan akademik mahasiswa pada Program Studi PSTI UPI membutuhkan analisis data umpan balik perkuliahan secara terukur. Selama dua semester terakhir jumlah data evaluasi terus meningkat namun masih diolah secara deskriptif sederhana.\n\n### 1.2 Rumusan Masalah\nRumusan masalah penelitian saat ini masih memuat lima butir pertanyaan yang tumpang tindih antara proses pra-pemrosesan dataset survei dengan perbandingan akurasi model klasifikasi Random Forest.",
                'komentars' => [
                    [
                        'lokasi' => 'Halaman 3 (Sub-bab 1.1)',
                        'kutipan' => 'dua semester terakhir jumlah data evaluasi terus meningkat namun masih diolah secara deskriptif sederhana',
                        'komentar' => 'Sertakan angka kuantitatif rekap kuesioner 2 semester terakhir agar urgensi penelitian lebih kuat.',
                        'status' => 'Perlu Revisi',
                        'resolved' => false,
                    ],
                    [
                        'lokasi' => 'Halaman 5 (Sub-bab 1.2)',
                        'kutipan' => 'memuat lima butir pertanyaan yang tumpang tindih',
                        'komentar' => 'Sederhanakan rumusan masalah menjadi maksimal 3 poin yang langsung terukur di hasil pengujian.',
                        'status' => 'Perlu Revisi',
                        'resolved' => false,
                    ],
                ],
                'diskusi' => [
                    [
                        'peran' => 'dosen',
                        'pesan' => 'Nadia, fokus perbaikan Bab I ada di data pendukung latar belakang dan perampingan rumusan masalah.',
                    ],
                    [
                        'peran' => 'mahasiswa',
                        'pesan' => 'Siap Bu, data rekap survei Prodi PSTI sudah saya ajukan ke bagian akademik untuk dilampirkan di Bab I revisi.',
                    ],
                ],
            ],
            [
                'email' => 'rahmat.hidayat@upi.edu',
                'name' => 'Rahmat Hidayat',
                'nim' => '2108106010045',
                'judul_skripsi' => 'Analisis Sentimen Opini Publik Layanan Akademik PSTI UPI Menggunakan Deep Learning Transformer',
                'bab_aktif' => 'Bab V (Kesimpulan & ACC Sidang)',
                'nama_file' => 'Naskah_Lengkap_Rahmat_Final.pdf',
                'versi' => 'v2.4',
                'progres_persen' => 95,
                'status_draf' => 'Siap Sidang Skripsi',
                'jumlah_sesi' => 12,
                'konten_naskah' => "## BAB V — KESIMPULAN DAN SARAN\n\n### 5.1 Kesimpulan\nModel IndoBERT berhasil mencapai nilai F1-Score sebesar 91,4% pada klasifikasi sentimen tiga kelas terhadap opini layanan akademik digital mahasiswa PSTI UPI. Seluruh pertanyaan penelitian pada Bab I telah terjawab berdasarkan pengujian skenario validasi silang.\n\n### 5.2 Saran Pengembangan\nPengembangan berikutnya disarankan mengintegrasikan mekanisme pemantauan sentimen secara real-time melalui dashboard kendali mutu program studi.",
                'komentars' => [
                    [
                        'lokasi' => 'Halaman 78 (Sub-bab 5.1)',
                        'kutipan' => 'Model IndoBERT berhasil mencapai nilai F1-Score sebesar 91,4% pada klasifikasi sentimen tiga kelas',
                        'komentar' => 'Kesimpulan konsisten dengan tabel evaluasi di Bab IV. Naskah disetujui untuk pendaftaran sidang akhir.',
                        'status' => 'Disetujui',
                        'resolved' => true,
                    ],
                ],
                'diskusi' => [
                    [
                        'peran' => 'dosen',
                        'pesan' => 'Naskah lengkap Bab I s.d. Bab V sudah layak sidang. Silakan lengkapi berkas pendaftaran sidang ke Dewan Skripsi.',
                    ],
                ],
            ],
            [
                'email' => 'aisyah.psti@upi.edu',
                'name' => 'Siti Aisyah Putri',
                'nim' => '2108108010088',
                'judul_skripsi' => 'Implementasi Arsitektur Microservices pada Aplikasi E-Commerce UMKM Binaan Mahasiswa PSTI',
                'bab_aktif' => 'Bab IV (Implementasi & Evaluasi)',
                'nama_file' => 'Bab4_Implementasi_Aisyah.pdf',
                'versi' => 'v1.5',
                'progres_persen' => 80,
                'status_draf' => 'Draf Sedang Direview',
                'jumlah_sesi' => 8,
                'konten_naskah' => "## BAB IV — IMPLEMENTASI DAN EVALUASI\n\n### 4.1 Pengujian Load Balancing API\nHasil uji beban pada 500 request per detik menunjukkan rata-rata latensi respons menurun dari 420 ms pada arsitektur monolitik menjadi 145 ms pada arsitektur microservices berbasis container.\n\n### 4.2 Pembahasan Efisiensi Sumber Daya\nDistribusi beban layanan keranjang belanja dan pembayaran berjalan stabil tanpa kegagalan transaksi selama skenario spike testing berlangsung.",
                'komentars' => [
                    [
                        'lokasi' => 'Halaman 54 (Sub-bab 4.1)',
                        'kutipan' => 'rata-rata latensi respons menurun dari 420 ms pada arsitektur monolitik menjadi 145 ms',
                        'komentar' => 'Tambahkan grafik perbandingan throughput antara skenario 100, 250, dan 500 RPS agar visual lebih informatif.',
                        'status' => 'Catatan',
                        'resolved' => false,
                    ],
                ],
                'diskusi' => [
                    [
                        'peran' => 'mahasiswa',
                        'pesan' => 'Bu Siti, hasil benchmark Locust untuk 500 RPS sudah saya sertakan di sub-bab 4.1.',
                    ],
                ],
            ],
            [
                'email' => 'dimas.bagus@upi.edu',
                'name' => 'Dimas Bagus Wicaksono',
                'nim' => '2108109010055',
                'judul_skripsi' => 'Sistem Rekomendasi Pemilihan Topik Tugas Akhir Berbasis Content-Based Filtering',
                'bab_aktif' => 'Bab II (Tinjauan Pustaka)',
                'nama_file' => 'Bab2_Tinjauan_Dimas.pdf',
                'versi' => 'v1.1',
                'progres_persen' => 45,
                'status_draf' => 'Bimbingan Terjadwal',
                'jumlah_sesi' => 4,
                'konten_naskah' => "## BAB II — TINJAUAN PUSTAKA\n\n### 2.1 Algoritma Content-Based Filtering\nPendekatan Content-Based Filtering memanfaatkan pembobotan TF-IDF dan kemiripan kosinus (cosine similarity) untuk mencocokkan profil minat mata kuliah mahasiswa dengan deskripsi topik tugas akhir.\n\n### 2.2 Perbandingan Penelitian Terdahulu\nStudi komparasi mencakup lima jurnal internasional terbitan 2022 hingga 2025 pada domain sistem rekomendasi pendidikan tinggi.",
                'komentars' => [
                    [
                        'lokasi' => 'Halaman 19 (Sub-bab 2.2)',
                        'kutipan' => 'lima jurnal internasional terbitan 2022 hingga 2025 pada domain sistem rekomendasi pendidikan tinggi',
                        'komentar' => 'Sertakan kolom pembeda keterbatasan metode pada tabel matriks tinjauan pustaka terhadap pendekatan Collaborative Filtering.',
                        'status' => 'Perlu Revisi',
                        'resolved' => false,
                    ],
                ],
                'diskusi' => [
                    [
                        'peran' => 'dosen',
                        'pesan' => 'Matriks tinjauan pustaka sudah rapi Dimas, tinggal pertegas research gap dibanding collaborative filtering.',
                    ],
                ],
            ],
            [
                'email' => 'farah.annisa@upi.edu',
                'name' => 'Farah Annisa',
                'nim' => '2208109010072',
                'judul_skripsi' => 'Analisis Keamanan Jaringan IoT Laboratorium PSTI Menggunakan Intrusion Detection System',
                'bab_aktif' => 'Bab III (Perancangan Sistem)',
                'nama_file' => 'Bab3_Desain_Farah.pdf',
                'versi' => 'v1.1',
                'progres_persen' => 60,
                'status_draf' => 'Revisi Diperlukan',
                'jumlah_sesi' => 5,
                'konten_naskah' => "## BAB III — PERANCANGAN SISTEM KEAMANAN IOT\n\n### 3.1 Topologi Pengujian IDS Suricata\nSensor jaringan diposisikan pada titik cermin lalu lintas switch laboratorium PSTI untuk menangkap paket anomali protokol MQTT dan HTTP tanpa mengganggu latensi perangkat sensor.\n\n### 3.2 Skenario Simulasi Serangan\nSimulasi meliputi serangan SYN flood, brute-force broker MQTT, dan port scanning terkendali pada jaringan uji terisolasi.",
                'komentars' => [
                    [
                        'lokasi' => 'Halaman 27 (Sub-bab 3.1)',
                        'kutipan' => 'titik cermin lalu lintas switch laboratorium PSTI untuk menangkap paket anomali protokol MQTT',
                        'komentar' => 'Gambarkan port mirroring SPAN beserta spesifikasi perangkat uji pada gambar topologi 3.1.',
                        'status' => 'Perlu Revisi',
                        'resolved' => false,
                    ],
                ],
                'diskusi' => [
                    [
                        'peran' => 'mahasiswa',
                        'pesan' => 'Baik Bu, topologi SPAN switch akan saya perjelas di revisi berikutnya.',
                    ],
                ],
            ],
        ];

        foreach ($dokumenSeeds as $item) {
            $mahasiswa = User::where('email', $item['email'])->first();
            if (! $mahasiswa) {
                $mahasiswa = User::create([
                    'name' => $item['name'],
                    'email' => $item['email'],
                    'role' => 'mahasiswa',
                    'nomor_induk' => $item['nim'],
                    'program_studi' => $prodiPsti,
                    'password' => 'password',
                ]);
            }

            $dokumen = SkripsiDokumen::updateOrCreate(
                ['mahasiswa_id' => $mahasiswa->id],
                [
                    'dosen_pembimbing_id' => $dosenUtama->id,
                    'program_studi' => $prodiPsti,
                    'peran_dosen' => 'Pembimbing Utama',
                    'judul_skripsi' => $item['judul_skripsi'],
                    'bab_aktif' => $item['bab_aktif'],
                    'nama_file' => $item['nama_file'],
                    'versi' => $item['versi'],
                    'progres_persen' => $item['progres_persen'],
                    'status_draf' => $item['status_draf'],
                    'badge_status' => SkripsiDokumen::badgeForStatus($item['status_draf']),
                    'jumlah_sesi' => $item['jumlah_sesi'],
                    'konten_naskah' => $item['konten_naskah'],
                ]
            );

            // Hanya seed komentar & diskusi awal apabila dokumen belum memiliki interaksi agar komentar pengguna tetap terjaga
            if ($dokumen->semuaKomentars()->count() === 0) {
                foreach ($item['komentars'] as $komentarData) {
                    SkripsiDokumenKomentar::create([
                        'skripsi_dokumen_id' => $dokumen->id,
                        'user_id' => $dosenUtama->id,
                        'peran_pengirim' => 'dosen',
                        'lokasi_dokumen' => $komentarData['lokasi'],
                        'kutipan_teks' => $komentarData['kutipan'],
                        'isi_komentar' => $komentarData['komentar'],
                        'status' => $komentarData['status'],
                        'is_resolved' => $komentarData['resolved'],
                    ]);
                }
            }

            if ($dokumen->diskusiPesans()->count() === 0) {
                foreach ($item['diskusi'] as $pesanData) {
                    $isDosen = $pesanData['peran'] === 'dosen';
                    SkripsiDiskusiPesan::create([
                        'skripsi_dokumen_id' => $dokumen->id,
                        'user_id' => $isDosen ? $dosenUtama->id : $mahasiswa->id,
                        'peran_pengirim' => $pesanData['peran'],
                        'pesan' => $pesanData['pesan'],
                    ]);
                }
            }
        }
    }
}
