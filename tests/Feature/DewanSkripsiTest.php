<?php

namespace Tests\Feature;

use App\Models\Skripsi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DewanSkripsiTest extends TestCase
{
    use DatabaseTransactions;

    protected User $dewanUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dewanUser = User::where('email', 'dskripsi@simbim.ac.id')->first()
            ?? User::where('role', 'dewan_skripsi')->first()
            ?? User::factory()->create([
                'name' => 'Dewan Skripsi',
                'email' => 'dskripsi@simbim.ac.id',
                'role' => 'dewan_skripsi',
                'nomor_induk' => '197908202005011003',
                'password' => 'password',
            ]);

        $this->actingAs($this->dewanUser);
    }

    /**
     * Test Akun & Role: Dewan Skripsi dapat login dengan kredensial yang ditentukan:
     * Nama: Dewan Skripsi, Email: dskripsi@simbim.ac.id, Kata sandi: password.
     */
    public function test_dewan_skripsi_can_login_with_designated_credentials(): void
    {
        auth()->logout();

        $response = $this->post('/login', [
            'login' => 'dskripsi@simbim.ac.id',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertEquals('dewan_skripsi', auth()->user()->role);
        $this->assertEquals('Dewan Skripsi', auth()->user()->name);
        $response->assertRedirect(route('dewan-skripsi.beranda'));
    }

    public function test_dewan_skripsi_can_login_with_dewanskripsi_email(): void
    {
        auth()->logout();

        $response = $this->post('/login', [
            'login' => 'dewanskripsi@simbim.ac.id',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertEquals('dewan_skripsi', auth()->user()->role);
        $this->assertEquals('Dewan Skripsi', auth()->user()->name);
        $response->assertRedirect(route('dewan-skripsi.beranda'));
    }

    /**
     * Test Keamanan: Tamu tidak terautentikasi dialihkan ke halaman login.
     */
    public function test_tamu_mengakses_dewan_skripsi_dialihkan_ke_login(): void
    {
        auth()->logout();

        $response = $this->get(route('dewan-skripsi.dashboard'));

        $response->assertRedirect('/login');
    }

    /**
     * Test Keamanan: Role non-dewan (misal mahasiswa) ditolak dari portal dewan skripsi.
     */
    public function test_mahasiswa_tidak_dapat_mengakses_portal_dewan_skripsi(): void
    {
        $mhs = User::factory()->create(['role' => 'mahasiswa']);

        $response = $this->actingAs($mhs)->get(route('dewan-skripsi.dashboard'));

        $response->assertRedirect(route('mahasiswa.beranda'));
    }

    /**
     * Test 1: Dasbor Progres Skripsi dapat diakses dan menampilkan statistik serta tahapan skripsi.
     */
    public function test_dasbor_progres_skripsi_can_be_rendered(): void
    {
        $mhs = User::factory()->create(['name' => 'Budi Santoso', 'nim' => '2108107010099']);
        Skripsi::create([
            'mahasiswa_id' => $mhs->id,
            'judul' => 'Sistem Rekomendasi Skripsi Menggunakan Collaborative Filtering',
            'tahap' => 'Proses Bimbingan Aktif',
            'total_bimbingan' => 6,
        ]);

        $response = $this->get(route('dewan-skripsi.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dasbor Pemantauan Progres Skripsi Mahasiswa');
        $response->assertSee('Penyusunan Proposal');
        $response->assertSee('Proses Bimbingan Aktif');
        $response->assertSee('Siap Sidang');
        $response->assertSee('Revisi Final');
        $response->assertSee('Lulus/Selesai');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Sistem Rekomendasi Skripsi Menggunakan Collaborative Filtering');
    }

    /**
     * Test 2: Filter tahap skripsi pada dasbor progres.
     */
    public function test_dasbor_progres_can_be_filtered_by_tahap(): void
    {
        $mhs1 = User::factory()->create(['name' => 'Mahasiswa Proposal', 'nim' => '2108107010011']);
        $mhs2 = User::factory()->create(['name' => 'Mahasiswa Siap Sidang', 'nim' => '2108107010022']);

        Skripsi::create([
            'mahasiswa_id' => $mhs1->id,
            'judul' => 'Judul Proposal Baru',
            'tahap' => 'Penyusunan Proposal',
        ]);

        Skripsi::create([
            'mahasiswa_id' => $mhs2->id,
            'judul' => 'Judul Siap Sidang Lengkap',
            'tahap' => 'Siap Sidang',
        ]);

        $response = $this->get(route('dewan-skripsi.dashboard', ['tahap' => 'Siap Sidang']));

        $response->assertStatus(200);
        $response->assertSee('Mahasiswa Siap Sidang');
        $response->assertDontSee('Mahasiswa Proposal');
    }

    /**
     * Test 3: Halaman Plotting Dosen & Jadwal dapat dirender.
     */
    public function test_plotting_dosen_and_jadwal_page_can_be_rendered(): void
    {
        $dosen = User::firstOrCreate(
            ['name' => 'Dr. Teuku Muhammad'],
            [
                'email' => 'teuku_test_plotting@simbim.ac.id',
                'role' => 'dosen',
                'nomor_induk' => '198005122008011999',
                'password' => 'password',
            ]
        );
        $mhs = User::where('nim', '2108107010001')->first() ?? User::factory()->create(['name' => 'Andi Pratama', 'nim' => '2108107010001']);

        Skripsi::create([
            'mahasiswa_id' => $mhs->id,
            'judul' => 'Rancang Bangun Sistem Informasi Bimbingan',
            'tahap' => 'Penyusunan Proposal',
        ]);

        $response = $this->get(route('dewan-skripsi.plotting'));

        $response->assertStatus(200);
        $response->assertSee('Plotting Dosen Pembimbing');
        $response->assertSee('Andi Pratama');
        $response->assertSee('Dr. Teuku Muhammad');
    }

    /**
     * Test 4: Dewan Skripsi dapat menetapkan plotting dosen pembimbing & penguji serta jadwal sidang.
     */
    public function test_dewan_skripsi_can_update_plotting_and_schedule(): void
    {
        $p1 = User::factory()->create(['name' => 'Dosen Pembimbing 1', 'role' => 'dosen']);
        $p2 = User::factory()->create(['name' => 'Dosen Pembimbing 2', 'role' => 'dosen']);
        $u1 = User::factory()->create(['name' => 'Dosen Penguji 1', 'role' => 'dosen']);
        $u2 = User::factory()->create(['name' => 'Dosen Penguji 2', 'role' => 'dosen']);

        $mhs = User::factory()->create(['name' => 'Mahasiswa Test Plotting']);
        $skripsi = Skripsi::create([
            'mahasiswa_id' => $mhs->id,
            'judul' => 'Judul Uji Coba Plotting Dosen',
            'tahap' => 'Penyusunan Proposal',
        ]);

        $response = $this->post(route('dewan-skripsi.plotting.update', $skripsi->id), [
            'pembimbing_1_id' => $p1->id,
            'pembimbing_2_id' => $p2->id,
            'penguji_1_id' => $u1->id,
            'penguji_2_id' => $u2->id,
            'jadwal_sidang' => '2026-12-01 10:00:00',
            'ruangan_sidang' => 'Ruang Sidang 1, Lt. 3',
            'jenis_sidang' => 'Sidang Skripsi / Munaqasyah',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $skripsi->refresh();
        $this->assertEquals($p1->id, $skripsi->pembimbing_1_id);
        $this->assertEquals($p2->id, $skripsi->pembimbing_2_id);
        $this->assertEquals($u1->id, $skripsi->penguji_1_id);
        $this->assertEquals($u2->id, $skripsi->penguji_2_id);
        $this->assertEquals('Ruang Sidang 1, Lt. 3', $skripsi->ruangan_sidang);
        $this->assertEquals('Dijadwalkan', $skripsi->status_sidang);
    }

    /**
     * Test 5: Halaman Validasi Pendaftaran Sidang (Approval) dapat dirender.
     */
    public function test_validasi_pendaftaran_sidang_page_can_be_rendered(): void
    {
        $mhs = User::factory()->create(['name' => 'Siti Sarah']);
        Skripsi::create([
            'mahasiswa_id' => $mhs->id,
            'judul' => 'Implementasi YOLOv8 Deteksi Kopi Gayo',
            'tahap' => 'Siap Sidang',
            'total_bimbingan' => 10,
            'acc_pembimbing' => true,
            'status_pendaftaran_sidang' => 'Menunggu Persetujuan',
        ]);

        $response = $this->get(route('dewan-skripsi.validasi-sidang'));

        $response->assertStatus(200);
        $response->assertSee('Validasi');
        $response->assertSee('Pendaftaran Sidang');
        $response->assertSee('Siti Sarah');
        $response->assertSee('10x Bimbingan');
        $response->assertSee('Menunggu Persetujuan');
    }

    /**
     * Test 6: Dewan Skripsi dapat menyetujui (ACC) pengajuan sidang mahasiswa.
     */
    public function test_dewan_skripsi_can_approve_sidang_registration(): void
    {
        $mhs = User::factory()->create(['name' => 'Mahasiswa Siap ACC']);
        $skripsi = Skripsi::create([
            'mahasiswa_id' => $mhs->id,
            'judul' => 'Judul Skripsi ACC Sidang',
            'tahap' => 'Proses Bimbingan Aktif',
            'total_bimbingan' => 9,
            'acc_pembimbing' => true,
            'status_pendaftaran_sidang' => 'Menunggu Persetujuan',
        ]);

        $response = $this->post(route('dewan-skripsi.validasi-sidang.approve', $skripsi->id), [
            'catatan_validasi_sidang' => 'Rekam jejak bimbingan telah diverifikasi lengkap dan disetujui.',
            'jadwal_sidang' => '2026-12-10 09:00:00',
            'ruangan_sidang' => 'Ruang Sidang Utama',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $skripsi->refresh();
        $this->assertEquals('Disetujui', $skripsi->status_pendaftaran_sidang);
        $this->assertEquals('Siap Sidang', $skripsi->tahap);
        $this->assertEquals('Dijadwalkan', $skripsi->status_sidang);
        $this->assertNotNull($skripsi->tanggal_validasi_sidang);
    }

    /**
     * Test 7: Dewan Skripsi dapat menolak pengajuan sidang jika belum memenuhi syarat.
     */
    public function test_dewan_skripsi_can_reject_sidang_registration_with_reason(): void
    {
        $mhs = User::factory()->create(['name' => 'Mahasiswa Belum Cukup Bimbingan']);
        $skripsi = Skripsi::create([
            'mahasiswa_id' => $mhs->id,
            'judul' => 'Judul Ditolak Sidang',
            'tahap' => 'Proses Bimbingan Aktif',
            'total_bimbingan' => 4,
            'status_pendaftaran_sidang' => 'Menunggu Persetujuan',
        ]);

        $response = $this->post(route('dewan-skripsi.validasi-sidang.reject', $skripsi->id), [
            'catatan_validasi_sidang' => 'Jumlah bimbingan baru 4 kali, syarat minimal adalah 8 kali pertemuan.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('warning');

        $skripsi->refresh();
        $this->assertEquals('Ditolak', $skripsi->status_pendaftaran_sidang);
        $this->assertStringContainsString('Jumlah bimbingan baru 4 kali', $skripsi->catatan_validasi_sidang);
    }

    /**
     * Test 8: Halaman Verifikasi Berkas Pasca-Sidang dapat dirender.
     */
    public function test_verifikasi_pasca_sidang_page_can_be_rendered(): void
    {
        $mhs = User::factory()->create(['name' => 'Cut Meutia']);
        Skripsi::create([
            'mahasiswa_id' => $mhs->id,
            'judul' => 'Audit Keamanan Sistem Informasi Akademik NIST',
            'tahap' => 'Revisi Final',
            'status_sidang' => 'Selesai',
            'file_revisi_final' => 'revisi_final_cut_meutia.pdf',
            'status_kelulusan' => 'Revisi Sidang',
        ]);

        $response = $this->get(route('dewan-skripsi.verifikasi-pasca-sidang'));

        $response->assertStatus(200);
        $response->assertSee('Ruang Verifikasi Berkas Pasca-Sidang');
        $response->assertSee('Cut Meutia');
        $response->assertSee('revisi_final_cut_meutia.pdf');
    }

    /**
     * Test 9: Dewan Skripsi dapat menginput rekap nilai sidang dan otomatis menghitung rata-rata & huruf.
     */
    public function test_dewan_skripsi_can_input_exam_score_recap(): void
    {
        $mhs = User::factory()->create(['name' => 'Mahasiswa Input Nilai']);
        $skripsi = Skripsi::create([
            'mahasiswa_id' => $mhs->id,
            'judul' => 'Judul Input Nilai Sidang',
            'tahap' => 'Revisi Final',
            'status_sidang' => 'Selesai',
        ]);

        $response = $this->post(route('dewan-skripsi.verifikasi-pasca-sidang.nilai', $skripsi->id), [
            'nilai_pembimbing_1' => 88.0,
            'nilai_pembimbing_2' => 89.0,
            'nilai_penguji_1' => 87.0,
            'nilai_penguji_2' => 88.0,
            'catatan_dewan' => 'Sidang skripsi berjalan sangat baik.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $skripsi->refresh();
        $this->assertEquals(88.0, $skripsi->nilai_akhir);
        $this->assertEquals('A', $skripsi->nilai_huruf);
        $this->assertEquals('Selesai', $skripsi->status_sidang);
    }

    /**
     * Test 10: Dewan Skripsi dapat mengeluarkan status resmi "Lulus/Selesai" sebelum mahasiswa mendaftar yudisium.
     */
    public function test_dewan_skripsi_can_issue_lulus_selesai_status_with_skl(): void
    {
        $mhs = User::factory()->create(['name' => 'Fajar Ramadhan']);
        $skripsi = Skripsi::create([
            'mahasiswa_id' => $mhs->id,
            'judul' => 'Penerapan Algoritma Dijkstra Pada Evakuasi Tsunami',
            'tahap' => 'Revisi Final',
            'nilai_akhir' => 88.5,
            'nilai_huruf' => 'A',
            'status_kelulusan' => 'Revisi Sidang',
        ]);

        $response = $this->post(route('dewan-skripsi.verifikasi-pasca-sidang.lulus', $skripsi->id), [
            'no_surat_keterangan' => 'SKL/FT-USK/SKR/2026/101',
            'catatan_dewan' => 'Telah menyelesaikan seluruh revisi naskah dan dinyatakan Lulus/Selesai.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $skripsi->refresh();
        $this->assertEquals('Lulus/Selesai', $skripsi->status_kelulusan);
        $this->assertEquals('Lulus/Selesai', $skripsi->tahap);
        $this->assertEquals('SKL/FT-USK/SKR/2026/101', $skripsi->no_surat_keterangan);
        $this->assertEquals(Carbon::now()->toDateString(), $skripsi->tanggal_lulus->toDateString());
    }

    /**
     * Test 11: Cetak Surat Keterangan Lulus (SKL) resmi untuk pendaftaran yudisium.
     */
    public function test_cetak_surat_keterangan_lulus_can_be_viewed(): void
    {
        $mhs = User::where('nim', '2108107010042')->first() ?? User::factory()->create(['name' => 'Fajar Ramadhan', 'nim' => '2108107010042']);
        $skripsi = Skripsi::create([
            'mahasiswa_id' => $mhs->id,
            'judul' => 'Penerapan Algoritma Dijkstra Pada Evakuasi Tsunami',
            'tahap' => 'Lulus/Selesai',
            'status_kelulusan' => 'Lulus/Selesai',
            'nilai_akhir' => 88.5,
            'nilai_huruf' => 'A',
            'no_surat_keterangan' => 'SKL/FT-USK/SKR/2026/089',
            'pembimbing_1_nama' => 'Dr. Ir. Teuku Muhammad, M.T.',
        ]);

        $response = $this->get(route('dewan-skripsi.cetak-surat', $skripsi->id));

        $response->assertStatus(200);
        $response->assertSee('SURAT KETERANGAN LULUS SIDANG SKRIPSI');
        $response->assertSee('SKL/FT-USK/SKR/2026/089');
        $response->assertSee('Fajar Ramadhan');
        $response->assertSee('2108107010042');
        $response->assertSee('Yudisium Sarjana Komputer');
    }
}
