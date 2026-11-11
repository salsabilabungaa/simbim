<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $prodiPsti = config('simbim.prodi', 'Pendidikan Sistem dan Teknologi Informasi (PSTI)');

        User::updateOrCreate(
            ['email' => 'admin@simbim.ac.id'],
            [
                'name' => 'Administrator SIMBIM PSTI UPI',
                'role' => 'admin',
                'nomor_induk' => 'ADM001',
                'program_studi' => $prodiPsti,
                'password' => 'password',
            ]
        );

        User::updateOrCreate(
            ['email' => 'mahasiswa@simbim.ac.id'],
            [
                'name' => 'Andi Pratama',
                'role' => 'mahasiswa',
                'nomor_induk' => '2108107010001',
                'program_studi' => $prodiPsti,
                'password' => 'password',
            ]
        );

        User::updateOrCreate(
            ['email' => 'dosen@simbim.ac.id'],
            [
                'name' => 'Dr. Siti Rahmawati, M.Kom.',
                'role' => 'dosen',
                'nomor_induk' => '198503152010122001',
                'program_studi' => $prodiPsti,
                'password' => 'password',
            ]
        );

        User::updateOrCreate(
            ['email' => 'dewan@simbim.ac.id'],
            [
                'name' => 'Prof. Dr. Ir. Dewan Skripsi, M.Sc.',
                'role' => 'dewan_skripsi',
                'nomor_induk' => '197908202005011002',
                'program_studi' => $prodiPsti,
                'password' => 'password',
            ]
        );

        User::updateOrCreate(
            ['email' => 'gkm@simbim.ac.id'],
            [
                'name' => 'Dr. Ratna Wijaya, S.T., M.T.',
                'role' => 'gkm',
                'nomor_induk' => '198204122008122003',
                'program_studi' => $prodiPsti,
                'password' => 'password',
            ]
        );

        $this->call([
            PerwalianMahasiswaSeeder::class,
            SkripsiRevisiSeeder::class,
        ]);
    }
}
