<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use DatabaseTransactions;

    public function test_profile_edit_page_can_be_rendered(): void
    {
        $user = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi_profile_test@simbim.ac.id',
            'nim' => '2108107010099',
            'program_studi' => 'Pendidikan Sistem dan Teknologi Informasi (PSTI)',
        ]);

        $response = $this->actingAs($user)->get('/profile/edit');

        $response->assertStatus(200);
        $response->assertSee('Pengaturan Profil Pengguna');
        $response->assertSee('Budi Santoso');
        $response->assertSee('2108107010099');
    }

    public function test_profile_edit_page_can_be_rendered_for_dewan_skripsi(): void
    {
        $dewan = User::where('email', 'dewanskripsi@simbim.ac.id')->first()
            ?? User::factory()->dewanSkripsi()->create([
                'name' => 'Dewan Skripsi',
                'email' => 'dewanskripsi@simbim.ac.id',
            ]);

        $response = $this->actingAs($dewan)->get('/profile/edit');

        $response->assertStatus(200);
        $response->assertSee('Pengaturan Profil Pengguna');
        $response->assertSee('Dewan Skripsi');
        $response->assertSee('Portal Dewan Skripsi');
    }

    public function test_profile_edit_page_can_be_rendered_for_dosen(): void
    {
        $dosen = User::factory()->dosen()->create([
            'name' => 'Dr. Dosen Tester, M.Kom.',
        ]);

        $response = $this->actingAs($dosen)->get('/profile/edit');

        $response->assertStatus(200);
        $response->assertSee('Pengaturan Profil Pengguna');
        $response->assertSee('Dr. Dosen Tester, M.Kom.');
    }

    public function test_profile_edit_page_can_be_rendered_for_admin(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Admin Tester SIMBIM',
        ]);

        $response = $this->actingAs($admin)->get('/profile/edit');

        $response->assertStatus(200);
        $response->assertSee('Pengaturan Profil Pengguna');
        $response->assertSee('Admin Tester SIMBIM');
    }

    public function test_profile_edit_page_can_be_rendered_for_gkm(): void
    {
        $gkm = User::factory()->gkm()->create([
            'name' => 'GKM Tester SIMBIM',
        ]);

        $response = $this->actingAs($gkm)->get('/profile/edit');

        $response->assertStatus(200);
        $response->assertSee('Pengaturan Profil Pengguna');
        $response->assertSee('GKM Tester SIMBIM');
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create([
            'name' => 'Budi Lama',
            'email' => 'budi.lama.test@simbim.ac.id',
            'nim' => '2108107099001',
            'program_studi' => 'PSTI',
        ]);

        $response = $this->actingAs($user)->put('/profile/update', [
            'name' => 'Budi Baru',
            'email' => 'budi.baru.test@simbim.ac.id',
            'nim' => '2108107099002',
            'program_studi' => 'Pendidikan Sistem dan Teknologi Informasi',
        ]);

        $response->assertRedirect(route('berandaMahasiswa'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertSame('Budi Baru', $user->name);
        $this->assertSame('budi.baru.test@simbim.ac.id', $user->email);
        $this->assertSame('2108107099002', $user->nomor_induk);
        $this->assertSame('Pendidikan Sistem dan Teknologi Informasi', $user->program_studi);
    }

    public function test_dewan_skripsi_profile_information_can_be_updated(): void
    {
        $dewan = User::factory()->dewanSkripsi()->create([
            'name' => 'Dewan Lama',
            'email' => 'dewan.lama.test@simbim.ac.id',
            'nomor_induk' => '198001012005011999',
            'program_studi' => 'PSTI',
        ]);

        $response = $this->actingAs($dewan)->put('/profile/update', [
            'name' => 'Dewan Baru Terupdate',
            'email' => 'dewan.baru.test@simbim.ac.id',
            'nip' => '198001012005011999',
            'program_studi' => 'PSTI UPI',
        ]);

        $response->assertRedirect(route('dewan-skripsi.beranda'));
        $response->assertSessionHas('success');

        $dewan->refresh();
        $this->assertSame('Dewan Baru Terupdate', $dewan->name);
        $this->assertSame('dewan.baru.test@simbim.ac.id', $dewan->email);
    }

    public function test_profile_photo_can_be_uploaded_and_stored(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $file = UploadedFile::fake()->image('profil.jpg', 300, 300);

        $response = $this->actingAs($user)->put('/profile/update', [
            'name' => $user->name,
            'email' => $user->email,
            'nim' => '2108107010005',
            'program_studi' => 'PSTI',
            'foto_profil' => $file,
        ]);

        $response->assertRedirect(route('berandaMahasiswa'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertNotNull($user->foto_profil);
        Storage::disk('public')->assertExists($user->foto_profil);
    }

    public function test_profile_update_requires_valid_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/profile/update', [
            'name' => '',
            'email' => 'invalid-email',
            'nim' => '',
            'program_studi' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'nim', 'program_studi']);
    }

    public function test_tamu_tidak_dapat_mengakses_halaman_edit_profil(): void
    {
        $response = $this->get('/profile/edit');

        $response->assertRedirect('/login');
    }
}
