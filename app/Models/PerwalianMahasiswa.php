<?php

namespace App\Models;

use Database\Factories\PerwalianMahasiswaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'mahasiswa_id',
    'dosen_wali_id',
    'program_studi',
    'angkatan',
    'semester_berjalan',
    'tahun_akademik',
    'ipk',
    'sks_diambil',
    'status_perwalian',
    'status_krs',
    'tanggal_persetujuan_krs',
    'catatan_akhir_wali',
])]
class PerwalianMahasiswa extends Model
{
    /** @use HasFactory<PerwalianMahasiswaFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'semester_berjalan' => 'integer',
            'ipk' => 'decimal:2',
            'sks_diambil' => 'integer',
            'tanggal_persetujuan_krs' => 'date',
        ];
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function dosenWali(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_wali_id');
    }

    public function konsultasis(): HasMany
    {
        return $this->hasMany(KonsultasiAkademik::class, 'perwalian_mahasiswa_id')
            ->orderByDesc('tanggal_konsultasi')
            ->orderByDesc('id');
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
            $query->where('angkatan', $filters['angkatan']);
        }

        if (! empty($filters['status_perwalian'])) {
            $query->where('status_perwalian', $filters['status_perwalian']);
        }

        if (! empty($filters['search'])) {
            $keyword = trim((string) $filters['search']);
            $query->where(function (Builder $q) use ($keyword) {
                $q->where('program_studi', 'like', "%{$keyword}%")
                    ->orWhere('status_krs', 'like', "%{$keyword}%")
                    ->orWhereHas('mahasiswa', function (Builder $sub) use ($keyword) {
                        $sub->where('name', 'like', "%{$keyword}%")
                            ->orWhere('nomor_induk', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('dosenWali', function (Builder $sub) use ($keyword) {
                        $sub->where('name', 'like', "%{$keyword}%")
                            ->orWhere('nomor_induk', 'like', "%{$keyword}%");
                    });
            });
        }

        return $query;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_perwalian) {
            'selesai' => 'Sudah Selesai KRS/IRS',
            'proses' => 'Proses / Menunggu ACC',
            'belum' => 'Belum Bimbingan KRS/IRS',
            default => ucfirst((string) $this->status_perwalian),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status_perwalian) {
            'selesai' => 'success',
            'proses' => 'warning',
            'belum' => 'danger',
            default => 'secondary',
        };
    }
}
