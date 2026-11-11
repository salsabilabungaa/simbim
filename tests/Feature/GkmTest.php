<?php

namespace Tests\Feature;

use App\Models\KonsultasiAkademik;
use App\Models\PerwalianMahasiswa;
use App\Models\User;
use App\Services\UserImportService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GkmTest extends TestCase
{
    use DatabaseTransactions;

    public function test_tamu_diarahkan_ke_login_saat_mengakses_rute_gkm(): void
    {
        $this->get('/gkm/beranda')->assertRedirect('/login');
        $this->get('/gkm/riwayat-konsultasi')->assertRedirect('/login');
        $this->get('/gkm/ekspor/pdf')->assertRedirect('/login');
        $this->get('/gkm/ekspor/excel')->assertRedirect('/login');
    }

    public function test_role_selain_gkm_dan_admin_tidak_bisa_mengakses_halaman_gkm(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->create();
        $dosen = User::factory()->dosen()->create();
        $dewan = User::factory()->dewanSkripsi()->create();

        $this->actingAs($mahasiswa)->get('/gkm/beranda')->assertRedirect(route('mahasiswa.beranda'));
        $this->actingAs($dosen)->get('/gkm/riwayat-konsultasi')->assertRedirect(route('dosen.beranda'));
        $this->actingAs($dewan)->get('/gkm/ekspor/pdf')->assertRedirect(route('dewan-skripsi.beranda'));
        $this->actingAs($mahasiswa)->get('/gkm/ekspor/excel')->assertRedirect(route('mahasiswa.beranda'));
    }

    public function test_gkm_dapat_mengakses_dasbor_rekap_perwalian_dengan_grafik_dan_tabel_krs(): void
    {
        $gkm = User::factory()->gkm()->create();
        $prodiPsti = config('simbim.prodi', 'Pendidikan Sistem dan Teknologi Informasi (PSTI)');
        $dosenWali = User::factory()->dosen()->create([
            'name' => 'Dr. Dosen Wali Uji',
            'program_studi' => $prodiPsti,
        ]);
        $mhsSelesai = User::factory()->mahasiswa()->create([
            'name' => 'Mahasiswa Sudah KRS',
            'nomor_induk' => '2108107019901',
            'program_studi' => $prodiPsti,
        ]);
        $mhsBelum = User::factory()->mahasiswa()->create([
            'name' => 'Mahasiswa Belum KRS',
            'nomor_induk' => '2108107019902',
            'program_studi' => $prodiPsti,
        ]);

        PerwalianMahasiswa::factory()->selesai()->create([
            'mahasiswa_id' => $mhsSelesai->id,
            'dosen_wali_id' => $dosenWali->id,
            'program_studi' => $prodiPsti,
            'angkatan' => '2021',
            'tahun_akademik' => 'Ganjil 2026/2027',
        ]);

        PerwalianMahasiswa::factory()->belum()->create([
            'mahasiswa_id' => $mhsBelum->id,
            'dosen_wali_id' => $dosenWali->id,
            'program_studi' => $prodiPsti,
            'angkatan' => '2023',
            'tahun_akademik' => 'Ganjil 2026/2027',
        ]);

        $response = $this->actingAs($gkm)->get(route('gkm.beranda'));

        $response->assertStatus(200);
        $response->assertSee('GUGUS KENDALI MUTU (GKM)');
        $response->assertSee($prodiPsti);
        $response->assertSee('Universitas Pendidikan Indonesia (UPI)');
        $response->assertSee('Dasbor Rekap Perwalian Akademik (KRS/IRS)');
        $response->assertSee('chart_perwalian_per_angkatan');
        $response->assertSee('chart_proporsi_perwalian');
        $response->assertSee('Mahasiswa Sudah KRS');
        $response->assertSee('Sudah Selesai KRS/IRS');

        // Dengan paginasi 6 baris per halaman, mahasiswa Angkatan 2023 tampil saat membuka halaman berikutnya atau filter angkatan
        $responseAngkatan = $this->actingAs($gkm)->get(route('gkm.beranda', ['angkatan' => '2023']));
        $responseAngkatan->assertStatus(200);
        $responseAngkatan->assertSee('Mahasiswa Belum KRS');
        $responseAngkatan->assertSee('Belum Bimbingan KRS/IRS');
    }

    public function test_dasbor_rekap_perwalian_dapat_difilter_berdasarkan_status_angkatan_dan_pencarian(): void
    {
        $gkm = User::factory()->gkm()->create();
        $dosen = User::factory()->dosen()->create();
        $prodiPsti = config('simbim.prodi', 'Pendidikan Sistem dan Teknologi Informasi (PSTI)');

        $mhsSelesai = User::factory()->mahasiswa()->create([
            'name' => 'Budi Santoso Filter',
            'nomor_induk' => '2108107019911',
            'program_studi' => $prodiPsti,
        ]);
        $mhsBelum = User::factory()->mahasiswa()->create([
            'name' => 'Sari Melati Filter',
            'nomor_induk' => '2108107019912',
            'program_studi' => $prodiPsti,
        ]);

        PerwalianMahasiswa::factory()->selesai()->create([
            'mahasiswa_id' => $mhsSelesai->id,
            'dosen_wali_id' => $dosen->id,
            'program_studi' => $prodiPsti,
            'angkatan' => '2021',
            'tahun_akademik' => 'Ganjil 2026/2027',
        ]);
        PerwalianMahasiswa::factory()->belum()->create([
            'mahasiswa_id' => $mhsBelum->id,
            'dosen_wali_id' => $dosen->id,
            'program_studi' => $prodiPsti,
            'angkatan' => '2023',
            'tahun_akademik' => 'Ganjil 2026/2027',
        ]);

        $resBelum = $this->actingAs($gkm)->get(route('gkm.beranda', [
            'status_perwalian' => 'belum',
            'angkatan' => '2023',
        ]));
        $resBelum->assertStatus(200);
        $resBelum->assertSee('Sari Melati Filter');
        $resBelum->assertDontSee('Budi Santoso Filter');

        $resSearch = $this->actingAs($gkm)->get(route('gkm.beranda', [
            'search' => '2108107019911',
        ]));
        $resSearch->assertStatus(200);
        $resSearch->assertSee('Budi Santoso Filter');
        $resSearch->assertDontSee('Sari Melati Filter');
    }

    public function test_gkm_dapat_memantau_riwayat_dan_log_konsultasi_dalam_mode_view_only(): void
    {
        $gkm = User::factory()->gkm()->create();
        $prodiPsti = config('simbim.prodi', 'Pendidikan Sistem dan Teknologi Informasi (PSTI)');
        $dosen = User::factory()->dosen()->create(['name' => 'Dr. Wali Konsultasi']);
        $mhs = User::factory()->mahasiswa()->create([
            'name' => 'Mahasiswa Log Uji',
            'nomor_induk' => '2108107019921',
            'program_studi' => $prodiPsti,
        ]);

        $perwalian = PerwalianMahasiswa::factory()->selesai()->create([
            'mahasiswa_id' => $mhs->id,
            'dosen_wali_id' => $dosen->id,
            'program_studi' => $prodiPsti,
            'angkatan' => '2022',
        ]);

        $konsultasi = KonsultasiAkademik::factory()->create([
            'perwalian_mahasiswa_id' => $perwalian->id,
            'mahasiswa_id' => $mhs->id,
            'dosen_wali_id' => $dosen->id,
            'program_studi' => $prodiPsti,
            'topik' => 'Topik Konsultasi Unik Akreditasi 2026',
            'catatan_mahasiswa' => 'Catatan pengajuan KRS mahasiswa uji.',
            'catatan_dosen_wali' => 'Arahan persetujuan dosen wali untuk audit akreditasi.',
            'status_sesi' => 'ACC KRS/IRS',
        ]);

        $resIndex = $this->actingAs($gkm)->get(route('gkm.riwayat-konsultasi', [
            'angkatan' => '2022',
            'search' => 'Topik Konsultasi Unik',
        ]));

        $resIndex->assertStatus(200);
        $resIndex->assertSee('MODE VIEW ONLY');
        $resIndex->assertSee('Mahasiswa Log Uji');
        $resIndex->assertSee('Dr. Wali Konsultasi');
        $resIndex->assertSee('Topik Konsultasi Unik Akreditasi 2026');
        $resIndex->assertSee('Arahan persetujuan dosen wali untuk audit akreditasi.');
        $resIndex->assertDontSee('fa-eye');
        $resIndex->assertSee('riwayat-konsultasi/'.$konsultasi->id);

        // Halaman detail tunggal tetap dapat diakses secara View Only
        $resShow = $this->actingAs($gkm)->get(route('gkm.riwayat-konsultasi.show', $konsultasi));
        $resShow->assertStatus(200);
        $resShow->assertSee('MODE VIEW ONLY');
        $resShow->assertSee($prodiPsti);
        $resShow->assertSee('Universitas Pendidikan Indonesia (UPI)');
        $resShow->assertSee('Catatan pengajuan KRS mahasiswa uji.');
        $resShow->assertSee('Arahan persetujuan dosen wali untuk audit akreditasi.');

        // Pastikan tidak ada endpoint mutasi (POST/PUT/PATCH/DELETE) yang dapat mengubah log konsultasi
        $this->actingAs($gkm)->post('/gkm/riwayat-konsultasi', ['topik' => 'Ubah'])->assertStatus(405);
        $this->actingAs($gkm)->put('/gkm/riwayat-konsultasi/'.$konsultasi->id, ['topik' => 'Ubah'])->assertStatus(405);
        $this->actingAs($gkm)->patch('/gkm/riwayat-konsultasi/'.$konsultasi->id, ['topik' => 'Ubah'])->assertStatus(405);
        $this->actingAs($gkm)->delete('/gkm/riwayat-konsultasi/'.$konsultasi->id)->assertStatus(405);
    }

    public function test_gkm_dapat_mengekspor_laporan_akreditasi_psti_upi_ke_pdf(): void
    {
        $gkm = User::factory()->gkm()->create();

        $response = $this->actingAs($gkm)->get(route('gkm.ekspor.pdf'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $disposition = (string) $response->headers->get('content-disposition');
        $this->assertStringContainsString('Laporan_Akreditasi_GKM_psti_upi_', $disposition);
        $this->assertStringContainsString('.pdf', $disposition);

        $content = $response->getContent();
        $this->assertStringStartsWith('%PDF-1.4', $content);
        $this->assertStringEndsWith('%%EOF', $content);

        // Uji filter ekspor per angkatan tertentu
        $filtered = $this->actingAs($gkm)->get(route('gkm.ekspor.pdf', ['angkatan' => '2022']));
        $filtered->assertStatus(200);
        $this->assertStringContainsString('Laporan_Akreditasi_GKM_psti_upi_angkatan_2022_', (string) $filtered->headers->get('content-disposition'));
    }

    public function test_gkm_dapat_mengekspor_laporan_akreditasi_psti_upi_ke_excel(): void
    {
        $gkm = User::factory()->gkm()->create();

        $response = $this->actingAs($gkm)->get(route('gkm.ekspor.excel'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $disposition = (string) $response->headers->get('content-disposition');
        $this->assertStringContainsString('Laporan_Akreditasi_GKM_psti_upi_', $disposition);
        $this->assertStringContainsString('.xlsx', $disposition);

        $binary = $response->getContent();
        $this->assertStringStartsWith("PK\x03\x04", $binary);

        $tempPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'gkm_export_feature_'.uniqid('', true).'.xlsx';
        file_put_contents($tempPath, $binary);

        try {
            $parser = new UserImportService;
            $rows = $parser->parseXlsx($tempPath);

            $this->assertNotEmpty($rows);
            $this->assertStringContainsString('REKAPITULASI PERWALIAN PRODI PSTI UPI', $rows[0][0]);
            $flattened = implode(' ', array_map(fn ($r) => implode(' ', $r), $rows));
            $this->assertStringContainsString('Pendidikan Sistem dan Teknologi Informasi (PSTI)', $flattened);
            $this->assertStringContainsString('Universitas Pendidikan Indonesia (UPI)', $flattened);
            $this->assertStringContainsString('Angkatan', $flattened);
        } finally {
            @unlink($tempPath);
        }
    }

    public function test_admin_memiliki_hak_akses_global_ke_semua_fitur_gkm(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('gkm.beranda'))
            ->assertStatus(200)
            ->assertSee('HAK AKSES GLOBAL ADMIN');

        $this->actingAs($admin)
            ->get(route('gkm.riwayat-konsultasi'))
            ->assertStatus(200)
            ->assertSee('HAK AKSES GLOBAL ADMIN');

        $this->actingAs($admin)->get(route('gkm.ekspor.pdf'))->assertStatus(200);
        $this->actingAs($admin)->get(route('gkm.ekspor.excel'))->assertStatus(200);
    }

    public function test_data_perwalian_dan_konsultasi_database_tersinkronisasi_di_portal_dosen_dan_mahasiswa(): void
    {
        $prodiPsti = config('simbim.prodi', 'Pendidikan Sistem dan Teknologi Informasi (PSTI)');
        $dosenWali = User::factory()->dosen()->create([
            'name' => 'Dr. Wali Sinkronisasi PSTI',
            'program_studi' => $prodiPsti,
        ]);
        $mahasiswa = User::factory()->mahasiswa()->create([
            'name' => 'Mahasiswa Sinkronisasi UPI',
            'nomor_induk' => '2208107019999',
            'program_studi' => $prodiPsti,
        ]);

        $perwalian = PerwalianMahasiswa::factory()->selesai()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_wali_id' => $dosenWali->id,
            'program_studi' => $prodiPsti,
            'angkatan' => '2022',
            'semester_berjalan' => 5,
            'sks_diambil' => 22,
        ]);

        KonsultasiAkademik::factory()->create([
            'perwalian_mahasiswa_id' => $perwalian->id,
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_wali_id' => $dosenWali->id,
            'program_studi' => $prodiPsti,
            'topik' => 'Konsultasi Sinkronisasi KRS PSTI UPI 2026',
            'catatan_dosen_wali' => 'Disetujui 22 SKS untuk semester berjalan PSTI UPI.',
            'status_sesi' => 'ACC KRS/IRS',
        ]);

        $this->actingAs($dosenWali)
            ->get(route('dosen.beranda'))
            ->assertStatus(200)
            ->assertSee('Mahasiswa Sinkronisasi UPI')
            ->assertSee($prodiPsti);

        $this->actingAs($dosenWali)
            ->get(route('dosen.riwayat-bimbingan'))
            ->assertStatus(200)
            ->assertSee('Konsultasi Sinkronisasi KRS PSTI UPI 2026')
            ->assertSee($prodiPsti);

        $this->actingAs($mahasiswa)
            ->get(route('mahasiswa.bimbingan-akademik'))
            ->assertStatus(200)
            ->assertSee('Dr. Wali Sinkronisasi PSTI')
            ->assertSee('Konsultasi Sinkronisasi KRS PSTI UPI 2026')
            ->assertSee($prodiPsti);

        $this->actingAs($mahasiswa)
            ->get(route('mahasiswa.riwayat-bimbingan'))
            ->assertStatus(200)
            ->assertSee('Konsultasi Sinkronisasi KRS PSTI UPI 2026')
            ->assertSee('UNIVERSITAS PENDIDIKAN INDONESIA (UPI)');
    }
}
