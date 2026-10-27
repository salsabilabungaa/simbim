<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'andi.pratama@mhs.usk.ac.id'],
            [
                'name' => 'Andi Pratama',
                'nim' => '2108107010001',
                'program_studi' => 'Informatika',
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'nim' => '2108107010002',
                'program_studi' => 'Informatika',
                'password' => Hash::make('password'),
            ]
        );
    }
}
