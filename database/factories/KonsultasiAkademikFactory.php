<?php

namespace Database\Factories;

use App\Models\KonsultasiAkademik;
use App\Models\PerwalianMahasiswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KonsultasiAkademik>
 */
class KonsultasiAkademikFactory extends Factory
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
            'perwalian_mahasiswa_id' => PerwalianMahasiswa::factory(),
            'mahasiswa_id' => User::factory()->mahasiswa()->state(['program_studi' => $prodi]),
            'dosen_wali_id' => User::factory()->dosen()->state(['program_studi' => $prodi]),
            'program_studi' => $prodi,
            'tahun_akademik' => 'Ganjil 2026/2027',
            'tanggal_konsultasi' => now()->subDays(fake()->numberBetween(1, 30)),
            'waktu_konsultasi' => '10:00 - 11:00 WIB',
            'metode' => fake()->randomElement(['Luring (Ruang Dosen PSTI UPI)', 'Daring (Zoom/Meet)']),
            'topik' => 'Konsultasi Pengisian KRS/IRS & Evaluasi Beban Studi Semester PSTI UPI',
            'catatan_mahasiswa' => 'Mengajukan rencana pengambilan 21 SKS termasuk mata kuliah wajib Prodi PSTI.',
            'catatan_dosen_wali' => 'Rencana studi disetujui. Pastikan jadwal praktikum laboratorium PSTI tidak bentrok.',
            'status_sesi' => 'ACC KRS/IRS',
            'lampiran_krs' => 'KRS_2026_Ganjil_PSTI.pdf',
        ];
    }
}
