<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class BimbinganSkripsiTest extends TestCase
{
    public function test_tamu_diarahkan_ke_login_saat_mengakses_bimbingan_skripsi(): void
    {
        $response = $this->get('/mahasiswa/bimbingan-skripsi');

        $response->assertRedirect('/login');
    }

    public function test_mahasiswa_dapat_mengakses_halaman_bimbingan_skripsi(): void
    {
        $user = User::factory()->make();

        $response = $this->actingAs($user)->get('/mahasiswa/bimbingan-skripsi');

        $response->assertStatus(200);
        $response->assertSee('Bimbingan Skripsi');
        $response->assertSee('Dr. Ir. Teuku Muhammad, M.T.');
        $response->assertSee('Tabel Bimbingan Skripsi');
    }
}
