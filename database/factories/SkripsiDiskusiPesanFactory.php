<?php

namespace Database\Factories;

use App\Models\SkripsiDiskusiPesan;
use App\Models\SkripsiDokumen;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SkripsiDiskusiPesan>
 */
class SkripsiDiskusiPesanFactory extends Factory
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
            'referensi_komentar_id' => null,
            'peran_pengirim' => 'dosen',
            'pesan' => fake()->sentence(),
        ];
    }
}
