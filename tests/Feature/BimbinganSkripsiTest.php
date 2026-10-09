<?php

namespace Tests\Feature;

use Tests\TestCase;

class BimbinganSkripsiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_mahasiswa_dapat_mengakses_halaman_bimbingan_skripsi(): void
    {
        $response = $this->get('/mahasiswa/bimbingan-skripsi');

        $response->assertStatus(200);
        $response->assertSee('Bimbingan Skripsi');
        $response->assertSee('Dr. Ir. Teuku Muhammad, M.T.');
        $response->assertSee('Tabel Bimbingan Skripsi');
    }
}
