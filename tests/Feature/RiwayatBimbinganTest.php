<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class RiwayatBimbinganTest extends TestCase
{
    public function test_tamu_diarahkan_ke_login_saat_mengakses_riwayat_bimbingan(): void
    {
        $response = $this->get('/mahasiswa/riwayat-bimbingan');

        $response->assertRedirect('/login');
    }

    public function test_mahasiswa_riwayat_bimbingan_page_can_be_rendered(): void
    {
        $user = User::factory()->make();

        $response = $this->actingAs($user)->get('/mahasiswa/riwayat-bimbingan');

        $response->assertStatus(200);
        $response->assertSee('Logbook Bimbingan');
        $response->assertSee('Daftar Sesi Bimbingan');
    }
}
