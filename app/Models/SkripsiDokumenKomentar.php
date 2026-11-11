<?php

namespace App\Models;

use Database\Factories\SkripsiDokumenKomentarFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'skripsi_dokumen_id',
    'user_id',
    'parent_id',
    'peran_pengirim',
    'lokasi_dokumen',
    'kutipan_teks',
    'isi_komentar',
    'status',
    'is_resolved',
])]
class SkripsiDokumenKomentar extends Model
{
    /** @use HasFactory<SkripsiDokumenKomentarFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_resolved' => 'boolean',
        ];
    }

    public function dokumen(): BelongsTo
    {
        return $this->belongsTo(SkripsiDokumen::class, 'skripsi_dokumen_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function balasan(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('created_at')
            ->orderBy('id');
    }

    public static function badgeForStatus(string $status): string
    {
        return match ($status) {
            'Disetujui', 'Selesai' => 'success',
            'Perlu Revisi' => 'danger',
            'Catatan' => 'info',
            default => 'warning',
        };
    }
}
