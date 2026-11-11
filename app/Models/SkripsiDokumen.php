<?php

namespace App\Models;

use Database\Factories\SkripsiDokumenFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'mahasiswa_id',
    'dosen_pembimbing_id',
    'program_studi',
    'peran_dosen',
    'judul_skripsi',
    'bab_aktif',
    'nama_file',
    'file_path',
    'versi',
    'progres_persen',
    'status_draf',
    'badge_status',
    'jumlah_sesi',
    'konten_naskah',
    'catatan_akhir_dosen',
])]
class SkripsiDokumen extends Model
{
    /** @use HasFactory<SkripsiDokumenFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'progres_persen' => 'integer',
            'jumlah_sesi' => 'integer',
        ];
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function dosenPembimbing(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_pembimbing_id');
    }

    public function komentars(): HasMany
    {
        return $this->hasMany(SkripsiDokumenKomentar::class, 'skripsi_dokumen_id')
            ->whereNull('parent_id')
            ->orderBy('created_at')
            ->orderBy('id');
    }

    public function semuaKomentars(): HasMany
    {
        return $this->hasMany(SkripsiDokumenKomentar::class, 'skripsi_dokumen_id')
            ->orderBy('created_at')
            ->orderBy('id');
    }

    public function diskusiPesans(): HasMany
    {
        return $this->hasMany(SkripsiDiskusiPesan::class, 'skripsi_dokumen_id')
            ->orderBy('created_at')
            ->orderBy('id');
    }

    public static function badgeForStatus(string $status): string
    {
        return match ($status) {
            'Siap Sidang Skripsi' => 'success',
            'Revisi Diperlukan' => 'danger',
            'Draf Sedang Direview' => 'primary',
            'Bimbingan Terjadwal' => 'info',
            default => 'warning',
        };
    }
}
