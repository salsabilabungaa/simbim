<?php

namespace App\Models;

use Database\Factories\KonsultasiAkademikFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'perwalian_mahasiswa_id',
    'mahasiswa_id',
    'dosen_wali_id',
    'program_studi',
    'tahun_akademik',
    'tanggal_konsultasi',
    'waktu_konsultasi',
    'metode',
    'topik',
    'catatan_mahasiswa',
    'catatan_dosen_wali',
    'status_sesi',
    'lampiran_krs',
])]
class KonsultasiAkademik extends Model
{
    /** @use HasFactory<KonsultasiAkademikFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_konsultasi' => 'date',
        ];
    }

    public function perwalianMahasiswa(): BelongsTo
    {
        return $this->belongsTo(PerwalianMahasiswa::class, 'perwalian_mahasiswa_id');
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function dosenWali(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_wali_id');
    }

    public function scopeFilterByGkmCriteria(Builder $query, array $filters): Builder
    {
        if (! empty($filters['tahun_akademik'])) {
            $query->where('tahun_akademik', $filters['tahun_akademik']);
        }

        if (! empty($filters['prodi'])) {
            $query->where('program_studi', $filters['prodi']);
        }

        if (! empty($filters['angkatan'])) {
            $angkatan = (string) $filters['angkatan'];
            $query->whereHas('perwalianMahasiswa', fn (Builder $sub) => $sub->where('angkatan', $angkatan));
        }

        if (! empty($filters['status_sesi'])) {
            $query->where('status_sesi', $filters['status_sesi']);
        }

        if (! empty($filters['dosen_wali_id'])) {
            $query->where('dosen_wali_id', (int) $filters['dosen_wali_id']);
        }

        if (! empty($filters['tanggal_dari'])) {
            $query->whereDate('tanggal_konsultasi', '>=', $filters['tanggal_dari']);
        }

        if (! empty($filters['tanggal_sampai'])) {
            $query->whereDate('tanggal_konsultasi', '<=', $filters['tanggal_sampai']);
        }

        if (! empty($filters['search'])) {
            $keyword = trim((string) $filters['search']);
            $query->where(function (Builder $q) use ($keyword) {
                $q->where('topik', 'like', "%{$keyword}%")
                    ->orWhere('catatan_mahasiswa', 'like', "%{$keyword}%")
                    ->orWhere('catatan_dosen_wali', 'like', "%{$keyword}%")
                    ->orWhere('program_studi', 'like', "%{$keyword}%")
                    ->orWhereHas('mahasiswa', function (Builder $sub) use ($keyword) {
                        $sub->where('name', 'like', "%{$keyword}%")
                            ->orWhere('nomor_induk', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('dosenWali', function (Builder $sub) use ($keyword) {
                        $sub->where('name', 'like', "%{$keyword}%")
                            ->orWhere('nomor_induk', 'like', "%{$keyword}%");
                    });
            });
        }

        return $query;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status_sesi) {
            'ACC KRS/IRS' => 'success',
            'Selesai' => 'primary',
            'Perlu Tindak Lanjut' => 'warning',
            default => 'secondary',
        };
    }
}
