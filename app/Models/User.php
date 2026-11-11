<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'nomor_induk', 'program_studi', 'dosen_wali_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }

    public function isDosen(): bool
    {
        return $this->role === 'dosen';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDewanSkripsi(): bool
    {
        return $this->role === 'dewan_skripsi';
    }

    public function isGkm(): bool
    {
        return $this->role === 'gkm';
    }

    public function dosenWali(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_wali_id');
    }

    public function mahasiswaBimbinganAkademik(): HasMany
    {
        return $this->hasMany(User::class, 'dosen_wali_id');
    }

    public function perwalianMahasiswas(): HasMany
    {
        return $this->hasMany(PerwalianMahasiswa::class, 'mahasiswa_id');
    }

    public function perwalianBimbingan(): HasMany
    {
        return $this->hasMany(PerwalianMahasiswa::class, 'dosen_wali_id');
    }

    public function konsultasiSebagaiMahasiswa(): HasMany
    {
        return $this->hasMany(KonsultasiAkademik::class, 'mahasiswa_id');
    }

    public function konsultasiSebagaiDosenWali(): HasMany
    {
        return $this->hasMany(KonsultasiAkademik::class, 'dosen_wali_id');
    }

    public function skripsiDokumens(): HasMany
    {
        return $this->hasMany(SkripsiDokumen::class, 'mahasiswa_id');
    }

    public function skripsiBimbingan(): HasMany
    {
        return $this->hasMany(SkripsiDokumen::class, 'dosen_pembimbing_id');
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Administrator',
            'mahasiswa' => 'Mahasiswa',
            'dosen' => 'Dosen',
            'dewan_skripsi' => 'Dewan Skripsi',
            'gkm' => 'GKM (Kendali Mutu)',
            default => ucfirst(str_replace('_', ' ', $this->role ?? 'Pengguna')),
        };
    }

    public function getRoleBadgeAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'dark',
            'mahasiswa' => 'primary',
            'dosen' => 'success',
            'dewan_skripsi' => 'warning',
            'gkm' => 'info',
            default => 'secondary',
        };
    }
}
