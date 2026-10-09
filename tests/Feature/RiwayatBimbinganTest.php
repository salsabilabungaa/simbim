<?php

namespace Tests\Feature;

use Tests\TestCase;

class RiwayatBimbinganTest extends TestCase
{
    public function test_mahasiswa_riwayat_bimbingan_page_can_be_rendered(): void
    {
        $response = $this->get('/mahasiswa/riwayat-bimbingan');

        $response->assertStatus(200);
        $response->assertSee('Logbook Bimbingan');
        $response->assertSee('Daftar Sesi Bimbingan');
    }
}
