<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AdminTest extends TestCase
{
    protected function createMockUser(string $role): User
    {
        $user = new User;
        $user->id = 999;
        $user->name = 'Test '.ucfirst($role);
        $user->email = "test_{$role}@simbim.ac.id";
        $user->role = $role;
        $user->nomor_induk = 'ID_'.strtoupper($role);
        $user->program_studi = config('simbim.prodi', 'Pendidikan Sistem dan Teknologi Informasi (PSTI)');

        return $user;
    }

    public function test_tamu_mengakses_admin_diarahkan_ke_login(): void
    {
        $response = $this->get('/admin/beranda');
        $response->assertRedirect('/login');
    }

    public function test_mahasiswa_mengakses_admin_dialihkan_ke_beranda_mahasiswa(): void
    {
        $mahasiswa = $this->createMockUser('mahasiswa');

        $response = $this->actingAs($mahasiswa)->get('/admin/beranda');
        $response->assertRedirect(route('mahasiswa.beranda'));
    }

    public function test_dosen_mengakses_admin_dialihkan_ke_beranda_dosen(): void
    {
        $dosen = $this->createMockUser('dosen');

        $response = $this->actingAs($dosen)->get('/admin/beranda');
        $response->assertRedirect(route('dosen.beranda'));
    }

    public function test_admin_dapat_mengakses_dashboard_admin(): void
    {
        $admin = $this->createMockUser('admin');

        $response = $this->actingAs($admin)->get('/admin/beranda');
        $response->assertStatus(200);
        $response->assertSee('Selamat Datang');
        $response->assertSee('Hak Akses Global');
    }

    public function test_admin_dapat_mengakses_form_tambah_akun_manual(): void
    {
        $admin = $this->createMockUser('admin');

        $response = $this->actingAs($admin)->get('/admin/users/create');
        $response->assertStatus(200);
        $response->assertSee('Tambah Akun Pengguna Baru');
        $response->assertSee('Pilih Peran / Role Akun');
        $response->assertSee('Mahasiswa');
        $response->assertSee('Dosen');
        $response->assertSee('Dewan Skripsi');
        $response->assertSee('GKM (Gugus Kendali Mutu)');
        $response->assertSee('Dosen Pembimbing Akademik (Dosen Wali)');
        $response->assertSee('dosen_wali_id');
        $response->assertDontSee('role_admin');
        $response->assertDontSee('Program Studi &amp; Universitas', false);
    }

    public function test_admin_dapat_mengakses_form_import_akun(): void
    {
        $admin = $this->createMockUser('admin');

        $response = $this->actingAs($admin)->get('/admin/users/import');
        $response->assertStatus(200);
        $response->assertSee('Import Akun Pengguna Massal');
        $response->assertSee('Unduh Template Excel');
        $response->assertSee('Unduh Template CSV');
    }

    public function test_admin_dapat_mengunduh_template_csv_dan_excel(): void
    {
        $admin = $this->createMockUser('admin');

        $resCsv = $this->actingAs($admin)->get('/admin/users/template/csv');
        $resCsv->assertStatus(200);
        $resCsv->assertHeader('content-disposition');

        $resXlsx = $this->actingAs($admin)->get('/admin/users/template/xlsx');
        $resXlsx->assertStatus(200);
        $resXlsx->assertHeader('content-disposition');
    }

    public function test_admin_memiliki_hak_akses_global_ke_portal_mahasiswa(): void
    {
        $admin = $this->createMockUser('admin');

        $response = $this->actingAs($admin)->get('/mahasiswa/beranda');
        $response->assertStatus(200);
        $response->assertSee('HAK AKSES GLOBAL ADMIN');
    }

    public function test_admin_memiliki_hak_akses_global_ke_portal_dosen(): void
    {
        $admin = $this->createMockUser('admin');

        $response = $this->actingAs($admin)->get('/dosen/beranda');
        $response->assertStatus(200);
        $response->assertSee('HAK AKSES GLOBAL ADMIN');
    }

    public function test_admin_memiliki_hak_akses_global_ke_portal_dewan_skripsi(): void
    {
        $admin = $this->createMockUser('admin');

        $response = $this->actingAs($admin)->get('/dewan-skripsi/beranda');
        $response->assertStatus(200);
        $response->assertSee('HAK AKSES GLOBAL ADMIN');
    }

    public function test_admin_memiliki_hak_akses_global_ke_portal_gkm(): void
    {
        $admin = $this->createMockUser('admin');

        $response = $this->actingAs($admin)->get('/gkm/beranda');
        $response->assertStatus(200);
        $response->assertSee('HAK AKSES GLOBAL ADMIN');
    }

    public function test_admin_dapat_mengakses_keempat_halaman_monitoring_khusus(): void
    {
        $admin = $this->createMockUser('admin');

        $resMhs = $this->actingAs($admin)->get('/admin/monitoring/mahasiswa');
        $resMhs->assertStatus(200);
        $resMhs->assertSee('Pemantauan Mahasiswa');
        $resMhs->assertDontSee('Buka Tampilan Portal Mahasiswa');

        $resDsn = $this->actingAs($admin)->get('/admin/monitoring/dosen');
        $resDsn->assertStatus(200);
        $resDsn->assertSee('Pemantauan Dosen');
        $resDsn->assertDontSee('Buka Tampilan Portal Dosen');

        $resDewan = $this->actingAs($admin)->get('/admin/monitoring/dewan-skripsi');
        $resDewan->assertStatus(200);
        $resDewan->assertSee('Pemantauan Dewan Skripsi');
        $resDewan->assertDontSee('Buka Tampilan Portal Dewan Skripsi');

        $resGkm = $this->actingAs($admin)->get('/admin/monitoring/gkm');
        $resGkm->assertStatus(200);
        $resGkm->assertSee('Audit Kendali Mutu');
        $resGkm->assertDontSee('Buka Tampilan Portal GKM');
    }

    public function test_dewan_skripsi_dapat_mengakses_portal_dewan_skripsi(): void
    {
        $dewan = $this->createMockUser('dewan_skripsi');

        $response = $this->actingAs($dewan)->get('/dewan-skripsi/beranda');
        $response->assertStatus(200);
        $response->assertSee('DEWAN SKRIPSI');
    }

    public function test_gkm_dapat_mengakses_portal_gkm(): void
    {
        $gkm = $this->createMockUser('gkm');

        $response = $this->actingAs($gkm)->get('/gkm/beranda');
        $response->assertStatus(200);
        $response->assertSee('GUGUS KENDALI MUTU (GKM)');
    }

    public function test_root_mengarahkan_sesuai_role(): void
    {
        $admin = $this->createMockUser('admin');
        $response = $this->actingAs($admin)->get('/');
        $response->assertRedirect(route('admin.beranda'));

        $dewan = $this->createMockUser('dewan_skripsi');
        $response = $this->actingAs($dewan)->get('/');
        $response->assertRedirect(route('dewan-skripsi.beranda'));

        $gkm = $this->createMockUser('gkm');
        $response = $this->actingAs($gkm)->get('/');
        $response->assertRedirect(route('gkm.beranda'));
    }

    public function test_admin_bisa_login_dengan_berbagai_format(): void
    {
        // 1. Login dengan email lengkap
        $resEmail = $this->post('/login', [
            'login' => 'admin@simbim.ac.id',
            'password' => 'password',
        ]);
        $resEmail->assertRedirect(route('admin.beranda'));
        $this->post('/logout');

        // 2. Login dengan username singkat 'admin'
        $resUsername = $this->post('/login', [
            'login' => 'admin',
            'password' => 'password',
        ]);
        $resUsername->assertRedirect(route('admin.beranda'));
        $this->post('/logout');

        // 3. Login dengan nomor induk 'ADM001'
        $resNip = $this->post('/login', [
            'login' => 'ADM001',
            'password' => 'password',
        ]);
        $resNip->assertRedirect(route('admin.beranda'));
        $this->post('/logout');
    }

    public function test_dewan_skripsi_bisa_login_dengan_berbagai_format(): void
    {
        // 1. Login dengan email lengkap
        $resEmail = $this->post('/login', [
            'login' => 'dewan@simbim.ac.id',
            'password' => 'password',
        ]);
        $resEmail->assertRedirect(route('dewan-skripsi.beranda'));
        $this->post('/logout');

        // 2. Login dengan username singkat 'dewan'
        $resUsername = $this->post('/login', [
            'login' => 'dewan',
            'password' => 'password',
        ]);
        $resUsername->assertRedirect(route('dewan-skripsi.beranda'));
        $this->post('/logout');

        // 3. Login dengan alias 'dewan skripsi'
        $resAlias = $this->post('/login', [
            'login' => 'dewan skripsi',
            'password' => 'password',
        ]);
        $resAlias->assertRedirect(route('dewan-skripsi.beranda'));
        $this->post('/logout');
    }

    public function test_gkm_bisa_login_dengan_berbagai_format(): void
    {
        // 1. Login dengan email lengkap
        $resEmail = $this->post('/login', [
            'login' => 'gkm@simbim.ac.id',
            'password' => 'password',
        ]);
        $resEmail->assertRedirect(route('gkm.beranda'));
        $this->post('/logout');

        // 2. Login dengan username singkat 'gkm'
        $resUsername = $this->post('/login', [
            'login' => 'gkm',
            'password' => 'password',
        ]);
        $resUsername->assertRedirect(route('gkm.beranda'));
        $this->post('/logout');
    }

    public function test_seluruh_portal_role_tersinkronisasi_dengan_prodi_psti_dan_upi(): void
    {
        $prodiPsti = config('simbim.prodi', 'Pendidikan Sistem dan Teknologi Informasi (PSTI)');
        $universitas = config('simbim.universitas', 'Universitas Pendidikan Indonesia (UPI)');

        $admin = $this->createMockUser('admin');
        $this->actingAs($admin)->get('/admin/monitoring/mahasiswa')
            ->assertStatus(200)
            ->assertSee($prodiPsti)
            ->assertSee($universitas);

        $this->actingAs($admin)->get('/admin/monitoring/dosen')
            ->assertStatus(200)
            ->assertSee($prodiPsti);

        $this->actingAs($admin)->get('/admin/monitoring/dewan-skripsi')
            ->assertStatus(200)
            ->assertSee('Ruang Sidang 1 Prodi PSTI UPI Lt. 2')
            ->assertDontSee('Fasilkom');

        $dosen = $this->createMockUser('dosen');
        $this->actingAs($dosen)->get('/dosen/beranda')
            ->assertStatus(200)
            ->assertSee($prodiPsti)
            ->assertDontSee('usk.ac.id')
            ->assertDontSee('Fasilkom');

        $this->actingAs($dosen)->get('/dosen/riwayat-bimbingan')
            ->assertStatus(200)
            ->assertSee($prodiPsti)
            ->assertSee($universitas);

        $mahasiswa = $this->createMockUser('mahasiswa');
        $this->actingAs($mahasiswa)->get('/mahasiswa/beranda')
            ->assertStatus(200)
            ->assertSee($prodiPsti)
            ->assertDontSee('usk.ac.id')
            ->assertDontSee('Fasilkom');

        $this->actingAs($mahasiswa)->get('/mahasiswa/riwayat-bimbingan')
            ->assertStatus(200)
            ->assertSee('UNIVERSITAS PENDIDIKAN INDONESIA (UPI)')
            ->assertDontSee('UNIVERSITAS SYIAH KUALA')
            ->assertDontSee('Banda Aceh');
    }
}
