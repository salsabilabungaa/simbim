<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skripsi extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'skripsis';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'mahasiswa_id',
        'judul',
        'bidang_minat',
        'tahap',
        'pembimbing_1_id',
        'pembimbing_1_nama',
        'pembimbing_2_id',
        'pembimbing_2_nama',
        'penguji_1_id',
        'penguji_1_nama',
        'penguji_2_id',
        'penguji_2_nama',
        'total_bimbingan',
        'acc_pembimbing',
        'catatan_pembimbing',
        'file_syarat_sidang',
        'status_pendaftaran_sidang',
        'catatan_validasi_sidang',
        'tanggal_validasi_sidang',
        'jenis_sidang',
        'jadwal_sidang',
        'ruangan_sidang',
        'metode_sidang',
        'status_sidang',
        'file_revisi_final',
        'catatan_revisi_final',
        'tanggal_unggah_revisi',
        'nilai_pembimbing_1',
        'nilai_pembimbing_2',
        'nilai_penguji_1',
        'nilai_penguji_2',
        'nilai_akhir',
        'nilai_huruf',
        'status_kelulusan',
        'tanggal_lulus',
        'no_surat_keterangan',
        'catatan_dewan',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'acc_pembimbing' => 'boolean',
            'jadwal_sidang' => 'datetime',
            'tanggal_validasi_sidang' => 'datetime',
            'tanggal_unggah_revisi' => 'datetime',
            'tanggal_lulus' => 'date',
            'nilai_pembimbing_1' => 'float',
            'nilai_pembimbing_2' => 'float',
            'nilai_penguji_1' => 'float',
            'nilai_penguji_2' => 'float',
            'nilai_akhir' => 'float',
        ];
    }

    /**
     * Mahasiswa pemilik tugas akhir skripsi.
     */
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    /**
     * Dosen Pembimbing Utama.
     */
    public function pembimbing1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembimbing_1_id');
    }

    /**
     * Dosen Pembimbing Pendamping.
     */
    public function pembimbing2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pembimbing_2_id');
    }

    /**
     * Dosen Penguji 1.
     */
    public function penguji1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penguji_1_id');
    }

    /**
     * Dosen Penguji 2.
     */
    public function penguji2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penguji_2_id');
    }

    /**
     * Label nama dosen pembimbing 1.
     */
    public function getNamaPembimbing1Attribute(): string
    {
        return $this->pembimbing1?->name ?? $this->pembimbing_1_nama ?? 'Belum Ditentukan';
    }

    /**
     * Label nama dosen pembimbing 2.
     */
    public function getNamaPembimbing2Attribute(): string
    {
        return $this->pembimbing2?->name ?? $this->pembimbing_2_nama ?? 'Belum Ditentukan';
    }

    /**
     * Label nama dosen penguji 1.
     */
    public function getNamaPenguji1Attribute(): string
    {
        return $this->penguji1?->name ?? $this->penguji_1_nama ?? 'Belum Ditentukan';
    }

    /**
     * Label nama dosen penguji 2.
     */
    public function getNamaPenguji2Attribute(): string
    {
        return $this->penguji2?->name ?? $this->penguji_2_nama ?? 'Belum Ditentukan';
    }

    /**
     * Hitung nilai akhir dan konversi nilai huruf.
     */
    public function hitungNilaiDanHuruf(?float $p1, ?float $p2, ?float $u1, ?float $u2): array
    {
        $scores = array_filter([$p1, $p2, $u1, $u2], fn ($val) => ! is_null($val) && $val > 0);

        if (empty($scores)) {
            return ['nilai_akhir' => null, 'nilai_huruf' => null];
        }

        $avg = round(array_sum($scores) / count($scores), 2);

        $huruf = match (true) {
            $avg >= 87 => 'A',
            $avg >= 78 => 'AB',
            $avg >= 69 => 'B',
            $avg >= 60 => 'BC',
            $avg >= 51 => 'C',
            default => 'D',
        };

        return ['nilai_akhir' => $avg, 'nilai_huruf' => $huruf];
    }
}
