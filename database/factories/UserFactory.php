<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'role' => 'mahasiswa',
            'nomor_induk' => (string) fake()->unique()->numerify('2108107010###'),
            'program_studi' => 'Pendidikan Sistem dan Teknologi Informasi (PSTI)',
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function mahasiswa(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'mahasiswa',
            'nomor_induk' => (string) fake()->unique()->numerify('2108107010###'),
            'program_studi' => 'Pendidikan Sistem dan Teknologi Informasi (PSTI)',
        ]);
    }

    public function dosen(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'dosen',
            'nomor_induk' => (string) fake()->unique()->numerify('19850315 201012 2 ###'),
            'program_studi' => 'Pendidikan Sistem dan Teknologi Informasi (PSTI)',
        ]);
    }
}
