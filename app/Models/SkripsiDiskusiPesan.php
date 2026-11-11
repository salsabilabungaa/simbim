<?php

namespace App\Models;

use Database\Factories\SkripsiDiskusiPesanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'skripsi_dokumen_id',
    'user_id',
    'referensi_komentar_id',
    'peran_pengirim',
    'pesan',
])]
class SkripsiDiskusiPesan extends Model
{
    /** @use HasFactory<SkripsiDiskusiPesanFactory> */
    use HasFactory;

    public function dokumen(): BelongsTo
    {
        return $this->belongsTo(SkripsiDokumen::class, 'skripsi_dokumen_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function referensiKomentar(): BelongsTo
    {
        return $this->belongsTo(SkripsiDokumenKomentar::class, 'referensi_komentar_id');
    }
}
