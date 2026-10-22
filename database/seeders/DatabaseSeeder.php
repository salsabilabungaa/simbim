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
        User::firstOrCreate(
            ['email' => 'mahasiswa@simbim.ac.id'],
            [
                'name' => 'Andi Pratama',
                'role' => 'mahasiswa',
                'nomor_induk' => '2108107010001',
                'program_studi' => 'Informatika',
                'password' => 'password',
            ]
        );

        User::firstOrCreate(
            ['email' => 'dosen@simbim.ac.id'],
            [
                'name' => 'Dr. Siti Rahmawati, M.Kom.',
                'role' => 'dosen',
                'nomor_induk' => '198503152010122001',
                'program_studi' => 'Informatika',
                'password' => 'password',
            ]
        );
    }
}
