<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BimbinganSkripsiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_mahasiswa_dapat_mengakses_halaman_bimbingan_skripsi(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/mahasiswa/bimbingan-skripsi');

        $response->assertStatus(200);
        $response->assertSee('Bimbingan Skripsi');
        $response->assertSee('Dr. Ir. Teuku Muhammad, M.T.');
        $response->assertSee('Tabel Bimbingan Skripsi');
    }
}
