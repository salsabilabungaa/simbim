<?php

namespace Database\Factories;

use App\Models\PerwalianMahasiswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PerwalianMahasiswa>
 */
class PerwalianMahasiswaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $prodi = config('simbim.prodi', 'Pendidikan Sistem dan Teknologi Informasi (PSTI)');

        return [
            'mahasiswa_id' => User::factory()->mahasiswa()->state(['program_studi' => $prodi]),
            'dosen_wali_id' => User::factory()->dosen()->state(['program_studi' => $prodi]),
            'program_studi' => $prodi,
            'angkatan' => fake()->randomElement(['2021', '2022', '2023', '2024']),
            'semester_berjalan' => fake()->randomElement([1, 3, 5, 7]),
            'tahun_akademik' => 'Ganjil 2026/2027',
            'ipk' => fake()->randomFloat(2, 2.75, 3.95),
            'sks_diambil' => fake()->randomElement([18, 20, 21, 22, 24]),
            'status_perwalian' => 'selesai',
            'status_krs' => 'KRS/IRS Disetujui',
            'tanggal_persetujuan_krs' => now()->subDays(fake()->numberBetween(1, 20)),
            'catatan_akhir_wali' => 'Pengambilan SKS telah sesuai dengan peta kurikulum Prodi PSTI UPI.',
        ];
    }

    public function selesai(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_perwalian' => 'selesai',
            'status_krs' => 'KRS/IRS Disetujui',
            'tanggal_persetujuan_krs' => now()->subDays(5),
        ]);
    }

    public function proses(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_perwalian' => 'proses',
            'status_krs' => 'Menunggu Validasi Dosen Wali',
            'tanggal_persetujuan_krs' => null,
        ]);
    }

    public function belum(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_perwalian' => 'belum',
            'status_krs' => 'Belum Mengisi KRS/IRS',
            'sks_diambil' => 0,
            'tanggal_persetujuan_krs' => null,
        ]);
    }
}
