<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_tamu_mengakses_root_diarahkan_ke_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_tamu_mengakses_beranda_mahasiswa_diarahkan_ke_login(): void
    {
        $response = $this->get('/mahasiswa/beranda');

        $response->assertRedirect('/login');
    }

    public function test_user_yang_sudah_login_mengakses_root_diarahkan_ke_beranda(): void
    {
        $user = User::factory()->make();

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect(route('mahasiswa.beranda'));
    }

    public function test_halaman_login_dapat_diakses(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Selamat Datang');
        $response->assertSee('Email atau NIM / NIP');
        $response->assertSee('Masuk ke SIMBIM');
    }

    public function test_halaman_register_dapat_diakses(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Pendaftaran Akun');
        $response->assertSee('Nama Lengkap');
        $response->assertSee('Mahasiswa');
        $response->assertSee('Dosen');
        $response->assertSee('Daftar Akun Sekarang');
    }

    public function test_pengguna_dapat_login_dengan_email(): void
    {
        $user = User::factory()->create([
            'email' => 'auth_test_user@simbim.ac.id',
            'password' => 'password123',
        ]);

        $response = $this->post('/login', [
            'login' => 'auth_test_user@simbim.ac.id',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('mahasiswa.beranda'));
    }

    public function test_pengguna_dapat_login_dengan_nim(): void
    {
        $user = User::factory()->create([
            'nomor_induk' => '2108107010099',
            'password' => 'password123',
        ]);

        $response = $this->post('/login', [
            'login' => '2108107010099',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('mahasiswa.beranda'));
    }

    public function test_login_gagal_jika_password_salah(): void
    {
        $user = User::factory()->create([
            'email' => 'test@simbim.ac.id',
            'password' => 'password123',
        ]);

        $response = $this->from('/login')->post('/login', [
            'login' => 'test@simbim.ac.id',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('login');
    }

    public function test_pendaftaran_akun_baru_berhasil(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'role' => 'mahasiswa',
            'nomor_induk' => '2108107010088',
            'email' => 'budi.santoso@mhs.usk.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => '1',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'budi.santoso@mhs.usk.ac.id',
            'nomor_induk' => '2108107010088',
            'role' => 'mahasiswa',
            'program_studi' => 'PSTI (Pendidikan Sistem dan Teknologi Informasi)',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('mahasiswa.beranda'));
    }

    public function test_login_mahasiswa_simbim_diarahkan_ke_dashboard_mahasiswa(): void
    {
        $response = $this->post('/login', [
            'login' => 'mahasiswa@simbim.ac.id',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertEquals('mahasiswa', auth()->user()->role);
        $response->assertRedirect(route('mahasiswa.beranda'));
    }

    public function test_login_dosen_simbim_diarahkan_ke_dashboard_dosen(): void
    {
        $response = $this->post('/login', [
            'login' => 'dosen@simbim.ac.id',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertEquals('dosen', auth()->user()->role);
        $response->assertRedirect(route('dosen.beranda'));
    }

    public function test_dosen_mencoba_akses_halaman_mahasiswa_dialihkan_ke_dashboard_dosen(): void
    {
        $dosen = User::where('email', 'dosen@simbim.ac.id')->first()
            ?? User::factory()->dosen()->create();

        $response = $this->actingAs($dosen)->get('/mahasiswa/beranda');

        $response->assertRedirect(route('dosen.beranda'));
    }

    public function test_mahasiswa_mencoba_akses_halaman_dosen_dialihkan_ke_dashboard_mahasiswa(): void
    {
        $mahasiswa = User::where('email', 'mahasiswa@simbim.ac.id')->first()
            ?? User::factory()->mahasiswa()->create();

        $response = $this->actingAs($mahasiswa)->get('/dosen/beranda');

        $response->assertRedirect(route('mahasiswa.beranda'));
    }

    public function test_dosen_login_diarahkan_ke_dashboard_dosen(): void
    {
        $dosen = User::factory()->dosen()->create([
            'email' => 'dosen_auth_test_custom@simbim.ac.id',
            'password' => 'password123',
        ]);

        $response = $this->post('/login', [
            'login' => 'dosen_auth_test_custom@simbim.ac.id',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($dosen);
        $response->assertRedirect(route('dosen.beranda'));
    }

    public function test_pendaftaran_dosen_berhasil_dan_diarahkan_ke_dashboard_dosen(): void
    {
        $response = $this->post('/register', [
            'name' => 'Dr. Ir. Hendra, M.T.',
            'role' => 'dosen',
            'nomor_induk' => '198701012012121001',
            'program_studi' => 'Informatika',
            'email' => 'hendra@usk.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => '1',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'hendra@usk.ac.id',
            'nomor_induk' => '198701012012121001',
            'role' => 'dosen',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dosen.beranda'));
    }

    public function test_pengguna_dapat_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}
