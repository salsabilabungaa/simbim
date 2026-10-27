<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: User belum login diarahkan ke /login jika mengakses beranda mahasiswa.
     */
    public function test_guest_is_redirected_to_login_when_accessing_beranda(): void
    {
        $response = $this->get('/mahasiswa/beranda');

        $response->assertRedirect('/login');
    }

    /**
     * Test 2: User belum login diarahkan ke /login jika mengakses halaman profil.
     */
    public function test_guest_is_redirected_to_login_when_accessing_profile(): void
    {
        $response = $this->get('/profile/edit');

        $response->assertRedirect('/login');
    }

    /**
     * Test 3: Halaman login dapat dirender dengan benar.
     */
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('SIMBIM');
        $response->assertSee('Email atau NIM');
        $response->assertSee('Password');
        $response->assertSee('Masuk ke SIMBIM');
    }

    /**
     * Test 4: User dapat login menggunakan Email dan Password yang valid.
     */
    public function test_users_can_authenticate_using_email(): void
    {
        $user = User::factory()->create([
            'email' => 'andi.pratama@mhs.usk.ac.id',
            'nim' => '2108107010001',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'login' => 'andi.pratama@mhs.usk.ac.id',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('berandaMahasiswa'));
    }

    /**
     * Test 5: User dapat login menggunakan NIM dan Password yang valid.
     */
    public function test_users_can_authenticate_using_nim(): void
    {
        $user = User::factory()->create([
            'email' => 'andi.pratama@mhs.usk.ac.id',
            'nim' => '2108107010001',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'login' => '2108107010001',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('berandaMahasiswa'));
    }

    /**
     * Test 6: Form login juga menerima nama field 'email' (kompatibilitas backward).
     */
    public function test_users_can_authenticate_passing_email_field_name(): void
    {
        $user = User::factory()->create([
            'email' => 'andi.pratama@mhs.usk.ac.id',
            'nim' => '2108107010001',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => '2108107010001',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('berandaMahasiswa'));
    }

    /**
     * Test 7: User gagal login jika password salah.
     */
    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'andi.pratama@mhs.usk.ac.id',
            'nim' => '2108107010001',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => '2108107010001',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email' => 'Email atau password salah!']);
    }

    /**
     * Test 8: User gagal login jika Email atau NIM tidak ditemukan.
     */
    public function test_users_can_not_authenticate_with_non_existent_account(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'nonexistent@mhs.usk.ac.id',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email' => 'Email atau password salah!']);
    }

    /**
     * Test 9: Form login mewajibkan input login dan password.
     */
    public function test_login_requires_credentials(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => '',
            'password' => '',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email', 'password']);
    }

    /**
     * Test 10: Pengguna yang sudah login diarahkan ke beranda jika mengakses /login.
     */
    public function test_authenticated_user_is_redirected_away_from_login(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect(route('berandaMahasiswa'));
    }

    /**
     * Test 11: Pengguna dapat logout dan diarahkan ke /login.
     */
    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHas('success');
    }

    /**
     * Test 12: Pengguna dengan password plain-text di DB tetap bisa login dan otomatis di-upgrade ke hash.
     */
    public function test_users_with_plain_text_passwords_can_authenticate_and_are_upgraded(): void
    {
        $user = User::factory()->create([
            'email' => 'legacy@mhs.usk.ac.id',
            'nim' => '2108107010099',
        ]);
        DB::table('users')->where('id', $user->id)->update(['password' => '12345']);

        $response = $this->post('/login', [
            'email' => '2108107010099',
            'password' => '12345',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('berandaMahasiswa'));

        $updatedUser = User::find($user->id);
        $this->assertTrue(str_starts_with($updatedUser->getRawOriginal('password'), '$2y$'));
    }
}
