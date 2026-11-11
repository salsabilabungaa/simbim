<?php

namespace Database\Factories;

use App\Models\SkripsiDokumen;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SkripsiDokumen>
 */
class SkripsiDokumenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mahasiswa_id' => User::factory()->mahasiswa(),
            'dosen_pembimbing_id' => User::factory()->dosen(),
            'program_studi' => config('simbim.prodi', 'Pendidikan Sistem dan Teknologi Informasi (PSTI)'),
            'peran_dosen' => 'Pembimbing Utama',
            'judul_skripsi' => 'Pengembangan '.fake()->sentence(6).' pada Prodi PSTI UPI',
            'bab_aktif' => 'Bab III (Metodologi Penelitian)',
            'nama_file' => 'Draft_BAB3_'.fake()->lexify('???').'_v1.0.pdf',
            'versi' => 'v1.0',
            'progres_persen' => 65,
            'status_draf' => 'Menunggu Review Dosen',
            'badge_status' => 'warning',
            'jumlah_sesi' => 4,
            'konten_naskah' => "## BAB III — METODOLOGI PENELITIAN\n\n### 3.1 Desain Arsitektur Sistem\nSistem dirancang menggunakan pendekatan arsitektur modular untuk mendukung sinkronisasi bimbingan tugas akhir mahasiswa Prodi PSTI UPI.\n\n### 3.2 Instrumen Pengujian\nPengujian dilakukan menggunakan skenario uji fungsional dan evaluasi kegunaan sistem oleh dosen pembimbing serta mahasiswa.",
            'catatan_akhir_dosen' => null,
        ];
    }
}
