<?php

namespace Database\Factories;

use App\Models\SkripsiDokumen;
use App\Models\SkripsiDokumenKomentar;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SkripsiDokumenKomentar>
 */
class SkripsiDokumenKomentarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'skripsi_dokumen_id' => SkripsiDokumen::factory(),
            'user_id' => User::factory()->dosen(),
            'parent_id' => null,
            'peran_pengirim' => 'dosen',
            'lokasi_dokumen' => 'Halaman 12 (Paragraf 2)',
            'kutipan_teks' => 'pendekatan arsitektur modular untuk mendukung sinkronisasi bimbingan',
            'isi_komentar' => fake()->sentence(),
            'status' => 'Perlu Revisi',
            'is_resolved' => false,
        ];
    }
}
