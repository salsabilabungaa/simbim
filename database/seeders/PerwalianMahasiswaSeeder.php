<?php

namespace Database\Seeders;

use App\Models\KonsultasiAkademik;
use App\Models\PerwalianMahasiswa;
use App\Models\User;
use Illuminate\Database\Seeder;

class PerwalianMahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tahunAkademik = 'Ganjil 2026/2027';
        $prodiPsti = config('simbim.prodi', 'Pendidikan Sistem dan Teknologi Informasi (PSTI)');

        // Bersihkan record perwalian/konsultasi non-PSTI lama agar data tersinkronisasi 100% ke PSTI UPI
        KonsultasiAkademik::where('program_studi', '!=', $prodiPsti)->delete();
        PerwalianMahasiswa::where('program_studi', '!=', $prodiPsti)->delete();

        User::whereIn('program_studi', [
            'Informatika',
            'Sistem Informasi',
            'Teknik Komputer',
            'Teknik Elektro',
            'Pusat TI & Administrasi Akademik',
            'Pusat Teknologi Informasi dan Komunikasi',
            'Gugus Kendali Mutu PSTI',
        ])->update(['program_studi' => $prodiPsti]);

        // Daftar Dosen Wali Prodi PSTI — Universitas Pendidikan Indonesia (UPI)
        $dosenWaliMap = [
            'siti' => User::updateOrCreate(
                ['email' => 'dosen@simbim.ac.id'],
                [
                    'name' => 'Dr. Siti Rahmawati, M.Kom.',
                    'role' => 'dosen',
                    'nomor_induk' => '198503152010122001',
                    'program_studi' => $prodiPsti,
                    'password' => 'password',
                ]
            ),
            'rizal' => User::updateOrCreate(
                ['email' => 'ahmad.rizal@upi.edu'],
                [
                    'name' => 'Prof. Dr. Ir. Ahmad Rizal, M.T.',
                    'role' => 'dosen',
                    'nomor_induk' => '197805122003121002',
                    'program_studi' => $prodiPsti,
                    'password' => 'password',
                ]
            ),
            'teuku' => User::updateOrCreate(
                ['email' => 'teuku.muhammad@upi.edu'],
                [
                    'name' => 'Dr. Ir. Teuku Muhammad, M.T.',
                    'role' => 'dosen',
                    'nomor_induk' => '198103142006041003',
                    'program_studi' => $prodiPsti,
                    'password' => 'password',
                ]
            ),
            'hendra' => User::updateOrCreate(
                ['email' => 'hendra.putra@upi.edu'],
                [
                    'name' => 'Dr. Ir. Hendra Putra, M.Eng.',
                    'role' => 'dosen',
                    'nomor_induk' => '198001012005011003',
                    'program_studi' => $prodiPsti,
                    'password' => 'password',
                ]
            ),
        ];

        $datasetMahasiswa = [
            // Angkatan 2021 (Semester 7)
            [
                'email' => 'mahasiswa@simbim.ac.id',
                'name' => 'Andi Pratama',
                'nim' => '2108107010001',
                'dosen_key' => 'rizal',
                'angkatan' => '2021',
                'semester' => 7,
                'ipk' => 3.82,
                'sks' => 21,
                'status_perwalian' => 'selesai',
                'status_krs' => 'KRS/IRS Disetujui',
                'tgl_acc' => '2026-09-04',
                'catatan_akhir' => 'KRS Semester 7 Prodi PSTI UPI disetujui (21 SKS). Fokus penyelesaian Metodologi Skripsi.',
                'konsultasi' => [
                    [
                        'tanggal' => '2026-08-26',
                        'waktu' => '09:30 - 10:15 WIB',
                        'metode' => 'Luring (Gedung Prodi PSTI UPI R.302)',
                        'topik' => 'Evaluasi IPK Semester 6 & Perencanaan Beban SKS Semester 7',
                        'catatan_mhs' => 'Mohon arahan pengambilan mata kuliah peminatan PSTI sekaligus tugas akhir di semester 7.',
                        'catatan_dsn' => 'IPK 3.82 sangat baik. Disarankan mengambil maksimal 21 SKS agar fokus penulisan skripsi.',
                        'status_sesi' => 'Selesai',
                        'lampiran' => 'KHS_Sem6_AndiPratama_PSTI.pdf',
                    ],
                    [
                        'tanggal' => '2026-09-04',
                        'waktu' => '10:00 - 10:45 WIB',
                        'metode' => 'Luring (Gedung Prodi PSTI UPI R.302)',
                        'topik' => 'Validasi & Pengesahan KRS/IRS Semester Ganjil 2026/2027',
                        'catatan_mhs' => 'Draft KRS 21 SKS sudah diinput di portal akademik UPI tanpa bentrok jadwal.',
                        'catatan_dsn' => 'Susunan KRS/IRS telah sesuai peta kurikulum Prodi PSTI UPI. Resmi di-ACC.',
                        'status_sesi' => 'ACC KRS/IRS',
                        'lampiran' => 'KRS_ACC_2108107010001_PSTI.pdf',
                    ],
                ],
            ],
            [
                'email' => 'aisyah.psti@upi.edu',
                'name' => 'Siti Aisyah Putri',
                'nim' => '2108108010088',
                'dosen_key' => 'siti',
                'angkatan' => '2021',
                'semester' => 7,
                'ipk' => 3.79,
                'sks' => 19,
                'status_perwalian' => 'selesai',
                'status_krs' => 'KRS/IRS Disetujui',
                'tgl_acc' => '2026-09-03',
                'catatan_akhir' => 'Konversi SKS MBKM Magang Industri disinkronkan dengan IRS Semester 7 Prodi PSTI UPI.',
                'konsultasi' => [
                    [
                        'tanggal' => '2026-09-03',
                        'waktu' => '09:00 - 09:45 WIB',
                        'metode' => 'Daring (Zoom Meeting)',
                        'topik' => 'Rekognisi SKS MBKM & Pengesahan IRS Semester 7 PSTI UPI',
                        'catatan_mhs' => 'Mengajukan pengesahan IRS 19 SKS konversi program magang bersertifikat.',
                        'catatan_dsn' => 'Berkas kontrak magang lengkap. IRS 19 SKS Prodi PSTI resmi disetujui.',
                        'status_sesi' => 'ACC KRS/IRS',
                        'lampiran' => 'IRS_MBKM_2108108010088_PSTI.pdf',
                    ],
                ],
            ],
            [
                'email' => 'dimas.bagus@upi.edu',
                'name' => 'Dimas Bagus Wicaksono',
                'nim' => '2108109010055',
                'dosen_key' => 'teuku',
                'angkatan' => '2021',
                'semester' => 7,
                'ipk' => 3.45,
                'sks' => 18,
                'status_perwalian' => 'selesai',
                'status_krs' => 'KRS/IRS Disetujui',
                'tgl_acc' => '2026-09-05',
                'catatan_akhir' => 'KRS Tugas Akhir & Sistem Informasi Pendidikan disetujui.',
                'konsultasi' => [
                    [
                        'tanggal' => '2026-09-05',
                        'waktu' => '09:00 - 09:45 WIB',
                        'metode' => 'Luring (Gedung Prodi PSTI UPI R.204)',
                        'topik' => 'Konsultasi Paket Mata Kuliah Teknologi Informasi & Pengesahan KRS Semester 7',
                        'catatan_mhs' => 'Melaporkan progres praktikum dan pengambilan SKS skripsi Prodi PSTI UPI.',
                        'catatan_dsn' => 'Semua prasyarat terpenuhi. KRS 18 SKS resmi disetujui.',
                        'status_sesi' => 'ACC KRS/IRS',
                        'lampiran' => 'KRS_ACC_2108109010055_PSTI.pdf',
                    ],
                ],
            ],
            [
                'email' => 'rahmat.hidayat@upi.edu',
                'name' => 'Rahmat Hidayat',
                'nim' => '2108106010045',
                'dosen_key' => 'hendra',
                'angkatan' => '2021',
                'semester' => 7,
                'ipk' => 3.58,
                'sks' => 18,
                'status_perwalian' => 'selesai',
                'status_krs' => 'KRS/IRS Disetujui',
                'tgl_acc' => '2026-09-04',
                'catatan_akhir' => 'KRS Tugas Akhir Prodi PSTI UPI disahkan.',
                'konsultasi' => [
                    [
                        'tanggal' => '2026-09-04',
                        'waktu' => '14:30 - 15:15 WIB',
                        'metode' => 'Luring (Gedung Prodi PSTI UPI R.206)',
                        'topik' => 'Validasi SKS Lulus 120 SKS & Pengesahan KRS Semester 7',
                        'catatan_mhs' => 'Memastikan syarat minimal 120 SKS terpenuhi untuk mengambil Tugas Akhir PSTI.',
                        'catatan_dsn' => 'Transkrip sementara diverifikasi (122 SKS lulus). KRS disahkan.',
                        'status_sesi' => 'ACC KRS/IRS',
                        'lampiran' => 'KRS_ACC_2108106010045_PSTI.pdf',
                    ],
                ],
            ],

            // Angkatan 2022 (Semester 5)
            [
                'email' => 'nadia.zahra@upi.edu',
                'name' => 'Cut Nadia Zahra',
                'nim' => '2208107010034',
                'dosen_key' => 'siti',
                'angkatan' => '2022',
                'semester' => 5,
                'ipk' => 3.68,
                'sks' => 22,
                'status_perwalian' => 'selesai',
                'status_krs' => 'KRS/IRS Disetujui',
                'tgl_acc' => '2026-09-05',
                'catatan_akhir' => 'Pengambilan mata kuliah pilihan Data Mining & Sistem Informasi Pendidikan disetujui.',
                'konsultasi' => [
                    [
                        'tanggal' => '2026-09-05',
                        'waktu' => '11:00 - 11:45 WIB',
                        'metode' => 'Daring (Google Meet)',
                        'topik' => 'Konsultasi Pemilihan Konsentrasi PSTI & Pengesahan KRS Semester 5',
                        'catatan_mhs' => 'Mengajukan 22 SKS dengan pilihan jalur minat Rekayasa Sistem Informasi Pendidikan.',
                        'catatan_dsn' => 'Disetujui. Pastikan nilai prasyarat Probabilitas & Statistika dipertahankan.',
                        'status_sesi' => 'ACC KRS/IRS',
                        'lampiran' => 'KRS_ACC_2208107010034_PSTI.pdf',
                    ],
                ],
            ],
            [
                'email' => 'dinda.maulida@upi.edu',
                'name' => 'Dinda Maulida',
                'nim' => '2208108010065',
                'dosen_key' => 'siti',
                'angkatan' => '2022',
                'semester' => 5,
                'ipk' => 3.65,
                'sks' => 22,
                'status_perwalian' => 'selesai',
                'status_krs' => 'KRS/IRS Disetujui',
                'tgl_acc' => '2026-09-06',
                'catatan_akhir' => 'KRS lengkap dan telah memenuhi prasyarat Tata Kelola Sistem Informasi PSTI.',
                'konsultasi' => [
                    [
                        'tanggal' => '2026-09-06',
                        'waktu' => '10:30 - 11:15 WIB',
                        'metode' => 'Luring (Gedung Prodi PSTI UPI R.204)',
                        'topik' => 'Konsultasi KRS Semester 5 & Pemilihan Topik Riset Teknologi Pendidikan',
                        'catatan_mhs' => 'Ingin mengambil mata kuliah Tata Kelola TI dan Pengembangan Aplikasi Web Pendidikan.',
                        'catatan_dsn' => 'Kombinasi mata kuliah sudah tepat untuk peminatan PSTI UPI. KRS di-ACC.',
                        'status_sesi' => 'ACC KRS/IRS',
                        'lampiran' => 'KRS_ACC_2208108010065_PSTI.pdf',
                    ],
                ],
            ],
            [
                'email' => 'farah.annisa@upi.edu',
                'name' => 'Farah Annisa',
                'nim' => '2208109010072',
                'dosen_key' => 'teuku',
                'angkatan' => '2022',
                'semester' => 5,
                'ipk' => 3.71,
                'sks' => 22,
                'status_perwalian' => 'selesai',
                'status_krs' => 'KRS/IRS Disetujui',
                'tgl_acc' => '2026-09-08',
                'catatan_akhir' => 'KRS 22 SKS valid tanpa bentrok jadwal praktikum laboratorium PSTI UPI.',
                'konsultasi' => [
                    [
                        'tanggal' => '2026-09-08',
                        'waktu' => '13:00 - 13:45 WIB',
                        'metode' => 'Luring (Gedung Prodi PSTI UPI R.204)',
                        'topik' => 'Evaluasi Studi & Pengesahan KRS Keamanan Sistem & Jaringan',
                        'catatan_mhs' => 'Memilih praktikum Keamanan Informasi dan Infrastruktur Jaringan Pendidikan.',
                        'catatan_dsn' => 'Pilihan sesuai minat riset PSTI. KRS disetujui untuk semester Ganjil 2026/2027.',
                        'status_sesi' => 'ACC KRS/IRS',
                        'lampiran' => 'KRS_ACC_2208109010072_PSTI.pdf',
                    ],
                ],
            ],
            [
                'email' => 'tiara.salma@upi.edu',
                'name' => 'Tiara Salma',
                'nim' => '2208106010028',
                'dosen_key' => 'hendra',
                'angkatan' => '2022',
                'semester' => 5,
                'ipk' => 3.31,
                'sks' => 20,
                'status_perwalian' => 'proses',
                'status_krs' => 'Menunggu Verifikasi Prasyarat Lab PSTI',
                'tgl_acc' => null,
                'catatan_akhir' => 'Menunggu unggahan nilai praktikum Basis Data Terdistribusi dari koordinator lab PSTI.',
                'konsultasi' => [
                    [
                        'tanggal' => '2026-09-11',
                        'waktu' => '10:00 - 10:30 WIB',
                        'metode' => 'Daring (Google Meet)',
                        'topik' => 'Verifikasi Nilai Prasyarat Praktikum Basis Data PSTI',
                        'catatan_mhs' => 'Nilai praktikum belum muncul di SIAK UPI sehingga pengajuan KRS tertahan.',
                        'catatan_dsn' => 'Silakan lampirkan surat keterangan lulus praktikum Lab PSTI agar KRS segera di-ACC.',
                        'status_sesi' => 'Perlu Tindak Lanjut',
                        'lampiran' => null,
                    ],
                ],
            ],

            // Angkatan 2023 (Semester 3)
            [
                'email' => 'fikri.psti@upi.edu',
                'name' => 'Muhammad Fikri',
                'nim' => '2308107010012',
                'dosen_key' => 'siti',
                'angkatan' => '2023',
                'semester' => 3,
                'ipk' => 3.54,
                'sks' => 20,
                'status_perwalian' => 'selesai',
                'status_krs' => 'KRS/IRS Disetujui',
                'tgl_acc' => '2026-09-07',
                'catatan_akhir' => 'KRS 20 SKS tervalidasi untuk semester 3 Prodi PSTI UPI.',
                'konsultasi' => [
                    [
                        'tanggal' => '2026-09-07',
                        'waktu' => '13:30 - 14:15 WIB',
                        'metode' => 'Luring (Gedung Prodi PSTI UPI R.204)',
                        'topik' => 'Pengisian KRS Semester 3 & Strategi Praktikum Pemrograman PSTI',
                        'catatan_mhs' => 'Berkonsultasi mengenai jadwal praktikum Struktur Data dan Basis Data.',
                        'catatan_dsn' => 'Jadwal tidak bentrok, 20 SKS disetujui untuk semester Ganjil 2026/2027.',
                        'status_sesi' => 'ACC KRS/IRS',
                        'lampiran' => 'KRS_ACC_2308107010012_PSTI.pdf',
                    ],
                ],
            ],
            [
                'email' => 'rizky.kurniawan@upi.edu',
                'name' => 'Rizky Kurniawan',
                'nim' => '2308107010023',
                'dosen_key' => 'siti',
                'angkatan' => '2023',
                'semester' => 3,
                'ipk' => 2.74,
                'sks' => 16,
                'status_perwalian' => 'proses',
                'status_krs' => 'Menunggu Revisi SKS (Remedial)',
                'tgl_acc' => null,
                'catatan_akhir' => 'Diminta mengurangi 1 mata kuliah pilihan dan memprioritaskan mengulang Algoritma Dasar.',
                'konsultasi' => [
                    [
                        'tanggal' => '2026-09-09',
                        'waktu' => '14:00 - 14:45 WIB',
                        'metode' => 'Luring (Gedung Prodi PSTI UPI R.204)',
                        'topik' => 'Evaluasi IPS Rendah & Penyesuaian Batas Maksimal SKS KRS',
                        'catatan_mhs' => 'IPS semester lalu 2.65 sehingga jatah SKS maksimal pada SIAK UPI adalah 18 SKS.',
                        'catatan_dsn' => 'Wajib memprioritaskan mengulang mata kuliah wajib dasar PSTI. Kirim ulang draft KRS revisi.',
                        'status_sesi' => 'Perlu Tindak Lanjut',
                        'lampiran' => 'Draft_Revisi_KRS_Rizky_PSTI.pdf',
                    ],
                ],
            ],
            [
                'email' => 'farhan.akbar@upi.edu',
                'name' => 'Farhan Akbar Nasution',
                'nim' => '2308108010041',
                'dosen_key' => 'rizal',
                'angkatan' => '2023',
                'semester' => 3,
                'ipk' => 3.12,
                'sks' => 20,
                'status_perwalian' => 'proses',
                'status_krs' => 'Menunggu Validasi Dosen Wali',
                'tgl_acc' => null,
                'catatan_akhir' => 'Sudah mengunggah rencana IRS, menunggu sesi konsultasi verifikasi kurikulum PSTI.',
                'konsultasi' => [
                    [
                        'tanggal' => '2026-09-10',
                        'waktu' => '15:00 - 15:30 WIB',
                        'metode' => 'Daring (Zoom Meeting)',
                        'topik' => 'Pengajuan Validasi Awal KRS Semester 3 & Mata Kuliah Pilihan UPI',
                        'catatan_mhs' => 'Sudah mengisi 20 SKS namun butuh konfirmasi terkait mata kuliah pilihan MBKM UPI.',
                        'catatan_dsn' => 'Menunggu persetujuan jadwal MKU Universitas sebelum validasi final IRS diberikan.',
                        'status_sesi' => 'Perlu Tindak Lanjut',
                        'lampiran' => 'Pengajuan_MKU_Farhan_PSTI.pdf',
                    ],
                ],
            ],
            [
                'email' => 'yoga.pratama@upi.edu',
                'name' => 'Yoga Pratama Wijaya',
                'nim' => '2308109010030',
                'dosen_key' => 'teuku',
                'angkatan' => '2023',
                'semester' => 3,
                'ipk' => 2.95,
                'sks' => 0,
                'status_perwalian' => 'belum',
                'status_krs' => 'Belum Mengajukan Perwalian',
                'tgl_acc' => null,
                'catatan_akhir' => 'Perlu dihubungi oleh dosen wali PSTI sebelum penutupan masa pengisian KRS UPI.',
                'konsultasi' => [],
            ],
            [
                'email' => 'ilham.saputra@upi.edu',
                'name' => 'Ilham Saputra',
                'nim' => '2308106010017',
                'dosen_key' => 'hendra',
                'angkatan' => '2023',
                'semester' => 3,
                'ipk' => 2.88,
                'sks' => 0,
                'status_perwalian' => 'belum',
                'status_krs' => 'Belum Mengisi KRS/IRS',
                'tgl_acc' => null,
                'catatan_akhir' => 'Belum menjadwalkan perwalian akademik semester Ganjil 2026/2027.',
                'konsultasi' => [],
            ],

            // Angkatan 2024 (Semester 1)
            [
                'email' => 'naufal.hadi@upi.edu',
                'name' => 'Naufal Hadiansyah',
                'nim' => '2408108010019',
                'dosen_key' => 'rizal',
                'angkatan' => '2024',
                'semester' => 1,
                'ipk' => 3.40,
                'sks' => 0,
                'status_perwalian' => 'belum',
                'status_krs' => 'Belum Mengisi KRS/IRS',
                'tgl_acc' => null,
                'catatan_akhir' => 'Mahasiswa baru Angkatan 2024 belum melakukan pengajuan jadwal bimbingan akademik.',
                'konsultasi' => [],
            ],
        ];

        foreach ($datasetMahasiswa as $item) {
            $mahasiswa = User::updateOrCreate(
                ['email' => $item['email']],
                [
                    'name' => $item['name'],
                    'role' => 'mahasiswa',
                    'nomor_induk' => $item['nim'],
                    'program_studi' => $prodiPsti,
                    'password' => 'password',
                ]
            );

            $dosenWali = $dosenWaliMap[$item['dosen_key']];

            $perwalian = PerwalianMahasiswa::updateOrCreate(
                [
                    'mahasiswa_id' => $mahasiswa->id,
                    'tahun_akademik' => $tahunAkademik,
                ],
                [
                    'dosen_wali_id' => $dosenWali->id,
                    'program_studi' => $prodiPsti,
                    'angkatan' => $item['angkatan'],
                    'semester_berjalan' => $item['semester'],
                    'ipk' => $item['ipk'],
                    'sks_diambil' => $item['sks'],
                    'status_perwalian' => $item['status_perwalian'],
                    'status_krs' => $item['status_krs'],
                    'tanggal_persetujuan_krs' => $item['tgl_acc'],
                    'catatan_akhir_wali' => $item['catatan_akhir'],
                ]
            );

            foreach ($item['konsultasi'] as $log) {
                KonsultasiAkademik::updateOrCreate(
                    [
                        'perwalian_mahasiswa_id' => $perwalian->id,
                        'tanggal_konsultasi' => $log['tanggal'],
                        'topik' => $log['topik'],
                    ],
                    [
                        'mahasiswa_id' => $mahasiswa->id,
                        'dosen_wali_id' => $dosenWali->id,
                        'program_studi' => $prodiPsti,
                        'tahun_akademik' => $tahunAkademik,
                        'waktu_konsultasi' => $log['waktu'],
                        'metode' => $log['metode'],
                        'catatan_mahasiswa' => $log['catatan_mhs'],
                        'catatan_dosen_wali' => $log['catatan_dsn'],
                        'status_sesi' => $log['status_sesi'],
                        'lampiran_krs' => $log['lampiran'],
                    ]
                );
            }
        }
    }
}
