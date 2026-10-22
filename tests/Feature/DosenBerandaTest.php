<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class DosenBerandaTest extends TestCase
{
    public function test_tamu_diarahkan_ke_login_saat_mengakses_beranda_dosen(): void
    {
        $response = $this->get('/dosen/beranda');

        $response->assertRedirect('/login');
    }

    public function test_dosen_dapat_mengakses_dashboard_beranda(): void
    {
        $dosen = User::factory()->dosen()->make();

        $response = $this->actingAs($dosen)->get('/dosen/beranda');

        $response->assertStatus(200);
        $response->assertSee('Portal Akademik', false);
        $response->assertSee('BIMBINGAN SKRIPSI');
        $response->assertSee('MAHASISWA PERWALIAN');
        $response->assertSee('Daftar Mahasiswa Bimbingan', false);
        $response->assertSee('Jadwal Bimbingan Terdekat');
        $response->assertSee('Aktivitas', false);
    }

    public function test_dosen_yang_login_mengakses_root_diarahkan_ke_dashboard_dosen(): void
    {
        $dosen = User::factory()->dosen()->make();

        $response = $this->actingAs($dosen)->get('/');

        $response->assertRedirect(route('dosen.beranda'));
    }
}
