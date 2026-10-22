<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_edit_page_can_be_rendered(): void
    {
        $user = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi@mhs.usk.ac.id',
            'nim' => '2108107010099',
            'program_studi' => 'Informatika',
        ]);

        $response = $this->actingAs($user)->get('/profile/edit');

        $response->assertStatus(200);
        $response->assertSee('Pengaturan Profil Pengguna');
        $response->assertSee('Budi Santoso');
        $response->assertSee('2108107010099');
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create([
            'name' => 'Budi Lama',
            'email' => 'budi.lama@mhs.usk.ac.id',
            'nim' => '2108107010001',
            'program_studi' => 'Informatika',
        ]);

        $response = $this->actingAs($user)->put('/profile/update', [
            'name' => 'Budi Baru',
            'email' => 'budi.baru@mhs.usk.ac.id',
            'nim' => '2108107010002',
            'program_studi' => 'Sistem Informasi',
        ]);

        $response->assertRedirect(route('berandaMahasiswa'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertSame('Budi Baru', $user->name);
        $this->assertSame('budi.baru@mhs.usk.ac.id', $user->email);
        $this->assertSame('2108107010002', $user->nim);
        $this->assertSame('Sistem Informasi', $user->program_studi);
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
            'program_studi' => 'Informatika',
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
}
