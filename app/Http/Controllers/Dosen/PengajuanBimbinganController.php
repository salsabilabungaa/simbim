<?php

namespace App\Http\Controllers\Dosen;

use App\Models\KonsultasiAkademik;
use App\Models\SkripsiDokumen;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class PengajuanBimbinganController extends BaseDosenController
{
    /**
     * Menampilkan daftar permohonan / pengajuan jadwal bimbingan dari mahasiswa.
     */
    public function index(): View
    {
        $user = Auth::user();
        $dosen = $this->resolveDosenProfile($user);
        $prodi = (string) config('simbim.prodi');
        $ruangDosen = (string) config('simbim.ruang_prodi');

        $daftar_pengajuan = [];

        if ($user?->exists) {
            try {
                $dbSkripsi = SkripsiDokumen::with('mahasiswa:id,name,nomor_induk,program_studi')
                    ->where('dosen_pembimbing_id', $user->id)
                    ->orderByDesc('updated_at')
                    ->get();

                foreach ($dbSkripsi as $dok) {
                    $isWaiting = $dok->status_draf === 'Menunggu Review Dosen';
                    $daftar_pengajuan[] = [
                        'id' => 5000 + $dok->id,
                        'nama' => $dok->mahasiswa?->name ?? 'Mahasiswa PSTI',
                        'nim' => $dok->mahasiswa?->nomor_induk ?? '-',
                        'program_studi' => $dok->program_studi ?: $prodi,
                        'jenis_bimbingan' => 'skripsi',
                        'kategori_label' => 'Bimbingan Skripsi',
                        'topik' => $dok->bab_aktif.' — '.$dok->judul_skripsi,
                        'waktu_pengajuan' => $dok->updated_at?->translatedFormat('d M Y, H:i').' WIB',
                        'jadwal_usulan' => $dok->updated_at?->translatedFormat('l, d M Y') ?? '-',
                        'jam_usulan' => ($dok->updated_at?->translatedFormat('H:i') ?? '-').' WIB',
                        'metode' => 'Luring / Review Draf Online',
                        'tempat' => $ruangDosen,
                        'status' => $isWaiting ? 'menunggu' : 'disetujui',
                        'badge_status' => $isWaiting ? 'warning' : 'success',
                        'status_label' => $dok->status_draf,
                        'catatan_mahasiswa' => $dok->catatan_akhir_dosen ?: 'Pengajuan review draf skripsi versi '.$dok->versi,
                        'file_lampiran' => $dok->nama_file,
                    ];
                }

                $dbKonsultasi = KonsultasiAkademik::with('mahasiswa:id,name,nomor_induk,program_studi')
                    ->where('dosen_wali_id', $user->id)
                    ->orderByDesc('updated_at')
                    ->get();

                foreach ($dbKonsultasi as $log) {
                    $isWaiting = $log->status_sesi === 'Perlu Tindak Lanjut';
                    $daftar_pengajuan[] = [
                        'id' => 6000 + $log->id,
                        'nama' => $log->mahasiswa?->name ?? 'Mahasiswa PSTI',
                        'nim' => $log->mahasiswa?->nomor_induk ?? '-',
                        'program_studi' => $log->program_studi ?: $prodi,
                        'jenis_bimbingan' => 'akademik',
                        'kategori_label' => 'Bimbingan Akademik (DPA)',
                        'topik' => $log->topik,
                        'waktu_pengajuan' => $log->created_at?->translatedFormat('d M Y, H:i').' WIB',
                        'jadwal_usulan' => $log->tanggal_konsultasi?->translatedFormat('l, d M Y') ?? '-',
                        'jam_usulan' => $log->waktu_konsultasi ?: 'Menunggu Konfirmasi',
                        'metode' => $log->metode ?: 'Luring (Tatap Muka)',
                        'tempat' => $ruangDosen,
                        'status' => $isWaiting ? 'menunggu' : 'disetujui',
                        'badge_status' => $isWaiting ? 'warning' : 'success',
                        'status_label' => $isWaiting ? 'Menunggu Konfirmasi' : $log->status_sesi,
                        'catatan_mahasiswa' => $log->catatan_mahasiswa ?: $log->catatan_dosen_wali,
                        'file_lampiran' => $log->lampiran_krs,
                    ];
                }
            } catch (\Throwable) {
                $daftar_pengajuan = [];
            }
        }

        if ($daftar_pengajuan === []) {
            $daftar_pengajuan = [
                [
                    'id' => 101,
                    'nama' => 'Andi Pratama',
                    'nim' => '2108107010001',
                    'program_studi' => $prodi,
                    'jenis_bimbingan' => 'skripsi',
                    'kategori_label' => 'Bimbingan Skripsi',
                    'topik' => 'Review Draf Revisi Bab III & Instrumen Pengujian',
                    'waktu_pengajuan' => '23 Okt 2026, 14:20 WIB',
                    'jadwal_usulan' => 'Jumat, 24 Oktober 2026',
                    'jam_usulan' => '09:30 - 10:30 WIB',
                    'metode' => 'Luring (Tatap Muka)',
                    'tempat' => $ruangDosen,
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
                    'program_studi' => $prodi,
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
                    'program_studi' => $prodi,
                    'jenis_bimbingan' => 'akademik',
                    'kategori_label' => 'Bimbingan Akademik (DPA)',
                    'topik' => 'Perencanaan KRS Semester Genap & Rencana Magang MBKM',
                    'waktu_pengajuan' => '22 Okt 2026, 09:15 WIB',
                    'jadwal_usulan' => 'Senin, 27 Oktober 2026',
                    'jam_usulan' => '10:00 - 11:00 WIB',
                    'metode' => 'Luring (Tatap Muka)',
                    'tempat' => $ruangDosen,
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
                    'program_studi' => $prodi,
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
                    'program_studi' => $prodi,
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
                    'program_studi' => $prodi,
                    'jenis_bimbingan' => 'akademik',
                    'kategori_label' => 'Bimbingan Akademik (DPA)',
                    'topik' => 'Konsultasi Perbaikan Indeks Prestasi & Remedial',
                    'waktu_pengajuan' => '17 Okt 2026, 13:00 WIB',
                    'jadwal_usulan' => 'Kamis, 23 Oktober 2026',
                    'jam_usulan' => '11:00 - 12:00 WIB',
                    'metode' => 'Luring (Tatap Muka)',
                    'tempat' => $ruangDosen,
                    'status' => 'ditolak',
                    'badge_status' => 'danger',
                    'status_label' => 'Perlu Reschedule',
                    'catatan_mahasiswa' => 'Jadwal bertepatan dengan rapat senat fakultas. Silakan ajukan ulang untuk hari Jumat atau Senin.',
                    'file_lampiran' => null,
                ],
            ];
        }

        $statistik = [
            'total_pengajuan' => count($daftar_pengajuan),
            'menunggu_konfirmasi' => count(array_filter($daftar_pengajuan, fn (array $p): bool => $p['status'] === 'menunggu')),
            'terkonfirmasi' => count(array_filter($daftar_pengajuan, fn (array $p): bool => $p['status'] === 'disetujui')),
            'selesai_ditolak' => count(array_filter($daftar_pengajuan, fn (array $p): bool => $p['status'] === 'ditolak')),
        ];

        $notifikasi = $this->defaultNotifikasi();

        return view('dosen.bimbingan.pengajuan', compact(
            'dosen',
            'statistik',
            'daftar_pengajuan',
            'notifikasi'
        ));
    }
}
