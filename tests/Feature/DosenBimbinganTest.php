<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class DosenBimbinganTest extends TestCase
{
    public function test_tamu_tidak_dapat_mengakses_halaman_bimbingan_dosen(): void
    {
        $responseMahasiswa = $this->get('/dosen/bimbingan/mahasiswa');
        $responseMahasiswa->assertRedirect('/login');

        $responsePengajuan = $this->get('/dosen/bimbingan/pengajuan');
        $responsePengajuan->assertRedirect('/login');
    }

    public function test_mahasiswa_tidak_dapat_mengakses_halaman_bimbingan_dosen(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->make();

        $responseMahasiswa = $this->actingAs($mahasiswa)->get('/dosen/bimbingan/mahasiswa');
        $this->assertTrue(in_array($responseMahasiswa->getStatusCode(), [403, 302]));

        $responsePengajuan = $this->actingAs($mahasiswa)->get('/dosen/bimbingan/pengajuan');
        $this->assertTrue(in_array($responsePengajuan->getStatusCode(), [403, 302]));
    }

    public function test_dosen_dapat_mengakses_halaman_mahasiswa_bimbingan(): void
    {
        $dosen = User::factory()->dosen()->make();

        $response = $this->actingAs($dosen)->get('/dosen/bimbingan/mahasiswa');

        $response->assertStatus(200);
        $response->assertSee('Daftar Mahasiswa Bimbingan');
        $response->assertSee('BIMBINGAN SKRIPSI');
        $response->assertSee('PERWALIAN AKADEMIK');
        $response->assertSee('Mahasiswa Bimbingan');
    }

    public function test_dosen_dapat_mengakses_halaman_pengajuan_bimbingan(): void
    {
        $dosen = User::factory()->dosen()->make();

        $response = $this->actingAs($dosen)->get('/dosen/bimbingan/pengajuan');

        $response->assertStatus(200);
        $response->assertSee('Pengajuan Bimbingan Mahasiswa');
        $response->assertSee('TOTAL PENGAJUAN');
        $response->assertSee('Pengajuan Bimbingan');
    }

    public function test_aside_dosen_menampilkan_menu_beranda_dan_dropdown_bimbingan_tanpa_profile_sidebar(): void
    {
        $dosen = User::factory()->dosen()->make();

        $response = $this->actingAs($dosen)->get('/dosen/beranda');

        $response->assertStatus(200);
        $response->assertSee('Beranda');
        $response->assertSee('Bimbingan');
        $response->assertSee('Mahasiswa Bimbingan');
        $response->assertSee('Pengajuan Bimbingan');
        $response->assertSee(route('dosen.bimbingan.mahasiswa'));
        $response->assertSee(route('dosen.bimbingan.pengajuan'));

        // Profile dosen di sidebar telah dihapus
        $response->assertDontSee('Profile Dosen');

        // Header menampilkan popout dengan nama, Edit Profil, dan Sign Out sesuai gambar
        $response->assertSee('Edit Profil');
        $response->assertSee('Sign Out');
    }
}
