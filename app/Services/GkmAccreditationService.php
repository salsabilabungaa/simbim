<?php

namespace App\Services;

use App\Models\KonsultasiAkademik;
use App\Models\PerwalianMahasiswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class GkmAccreditationService
{
    /**
     * Ambil daftar opsi filter yang tersedia di basis data untuk Prodi PSTI UPI.
     *
     * @return array{
     *     daftarAngkatan: array<int, string>,
     *     daftarTahunAkademik: array<int, string>,
     *     daftarDosenWali: Collection<int, User>
     * }
     */
    public function getAvailableFilters(): array
    {
        $daftarAngkatan = PerwalianMahasiswa::query()
            ->select('angkatan')
            ->distinct()
            ->orderBy('angkatan')
            ->pluck('angkatan')
            ->filter(fn (?string $val) => filled($val))
            ->values()
            ->all();

        $daftarTahunAkademik = PerwalianMahasiswa::query()
            ->select('tahun_akademik')
            ->distinct()
            ->orderByDesc('tahun_akademik')
            ->pluck('tahun_akademik')
            ->filter(fn (?string $val) => filled($val))
            ->values()
            ->all();

        $daftarDosenWali = User::query()
            ->select(['id', 'name', 'nomor_induk', 'program_studi'])
            ->where('role', 'dosen')
            ->orderBy('name')
            ->get();

        return [
            'daftarAngkatan' => $daftarAngkatan,
            'daftarTahunAkademik' => $daftarTahunAkademik,
            'daftarDosenWali' => $daftarDosenWali,
        ];
    }

    /**
     * Hitung ringkasan KPI, rekap per angkatan pada Prodi PSTI UPI, dan payload grafik ApexCharts.
     *
     * @param  array{tahun_akademik?: ?string, angkatan?: ?string, prodi?: ?string}  $filters
     * @return array{
     *     total_mahasiswa: int,
     *     sudah_selesai: int,
     *     dalam_proses: int,
     *     belum_selesai: int,
     *     belum_atau_proses: int,
     *     kepatuhan_persen: float,
     *     total_konsultasi: int,
     *     rekap_per_angkatan: array<int, array{
     *         angkatan: string,
     *         label: string,
     *         total: int,
     *         selesai: int,
     *         proses: int,
     *         belum: int,
     *         belum_atau_proses: int,
     *         kepatuhan_persen: float,
     *         total_konsultasi: int
     *     }>,
     *     rekap_per_prodi: array<int, array{
     *         program_studi: string,
     *         total: int,
     *         selesai: int,
     *         proses: int,
     *         belum: int,
     *         belum_atau_proses: int,
     *         kepatuhan_persen: float,
     *         total_konsultasi: int
     *     }>,
     *     chart_data: array{
     *         categories: array<int, string>,
     *         selesai: array<int, int>,
     *         proses: array<int, int>,
     *         belum: array<int, int>,
     *         donut: array<int, int>
     *     }
     * }
     */
    public function getDashboardSummary(array $filters = []): array
    {
        $perwalianRows = PerwalianMahasiswa::query()
            ->when(! empty($filters['tahun_akademik']), fn (Builder $q) => $q->where('tahun_akademik', $filters['tahun_akademik']))
            ->when(! empty($filters['angkatan']), fn (Builder $q) => $q->where('angkatan', $filters['angkatan']))
            ->when(! empty($filters['prodi']), fn (Builder $q) => $q->where('program_studi', $filters['prodi']))
            ->selectRaw('angkatan, status_perwalian, COUNT(*) as aggregate_count')
            ->groupBy('angkatan', 'status_perwalian')
            ->get();

        $konsultasiCounts = KonsultasiAkademik::query()
            ->join('perwalian_mahasiswas', 'konsultasi_akademiks.perwalian_mahasiswa_id', '=', 'perwalian_mahasiswas.id')
            ->when(! empty($filters['tahun_akademik']), fn ($q) => $q->where('konsultasi_akademiks.tahun_akademik', $filters['tahun_akademik']))
            ->when(! empty($filters['angkatan']), fn ($q) => $q->where('perwalian_mahasiswas.angkatan', $filters['angkatan']))
            ->when(! empty($filters['prodi']), fn ($q) => $q->where('konsultasi_akademiks.program_studi', $filters['prodi']))
            ->selectRaw('perwalian_mahasiswas.angkatan as angkatan, COUNT(*) as aggregate_count')
            ->groupBy('perwalian_mahasiswas.angkatan')
            ->pluck('aggregate_count', 'angkatan');

        $angkatanBuckets = [];

        foreach ($perwalianRows as $row) {
            $angkatan = (string) $row->angkatan;
            if (! isset($angkatanBuckets[$angkatan])) {
                $angkatanBuckets[$angkatan] = [
                    'angkatan' => $angkatan,
                    'label' => 'Angkatan '.$angkatan,
                    'total' => 0,
                    'selesai' => 0,
                    'proses' => 0,
                    'belum' => 0,
                    'belum_atau_proses' => 0,
                    'kepatuhan_persen' => 0.0,
                    'total_konsultasi' => (int) ($konsultasiCounts[$angkatan] ?? 0),
                ];
            }

            $count = (int) $row->aggregate_count;
            $status = (string) $row->status_perwalian;
            $angkatanBuckets[$angkatan]['total'] += $count;

            if ($status === 'selesai') {
                $angkatanBuckets[$angkatan]['selesai'] += $count;
            } elseif ($status === 'proses') {
                $angkatanBuckets[$angkatan]['proses'] += $count;
            } else {
                $angkatanBuckets[$angkatan]['belum'] += $count;
            }
        }

        ksort($angkatanBuckets);

        $totalMahasiswa = 0;
        $sudahSelesai = 0;
        $dalamProses = 0;
        $belumSelesai = 0;
        $totalKonsultasi = (int) $konsultasiCounts->sum();

        $categories = [];
        $selesaiSeries = [];
        $prosesSeries = [];
        $belumSeries = [];

        foreach ($angkatanBuckets as &$bucket) {
            $bucket['belum_atau_proses'] = $bucket['proses'] + $bucket['belum'];
            $bucket['kepatuhan_persen'] = $bucket['total'] > 0
                ? round(($bucket['selesai'] / $bucket['total']) * 100, 1)
                : 0.0;

            $totalMahasiswa += $bucket['total'];
            $sudahSelesai += $bucket['selesai'];
            $dalamProses += $bucket['proses'];
            $belumSelesai += $bucket['belum'];

            $categories[] = $bucket['label'];
            $selesaiSeries[] = $bucket['selesai'];
            $prosesSeries[] = $bucket['proses'];
            $belumSeries[] = $bucket['belum'];
        }
        unset($bucket);

        $belumAtauProses = $dalamProses + $belumSelesai;
        $kepatuhanPersen = $totalMahasiswa > 0
            ? round(($sudahSelesai / $totalMahasiswa) * 100, 1)
            : 0.0;

        $prodiName = config('simbim.prodi', 'Pendidikan Sistem dan Teknologi Informasi (PSTI)');

        return [
            'total_mahasiswa' => $totalMahasiswa,
            'sudah_selesai' => $sudahSelesai,
            'dalam_proses' => $dalamProses,
            'belum_selesai' => $belumSelesai,
            'belum_atau_proses' => $belumAtauProses,
            'kepatuhan_persen' => $kepatuhanPersen,
            'total_konsultasi' => $totalKonsultasi,
            'rekap_per_angkatan' => array_values($angkatanBuckets),
            'rekap_per_prodi' => [
                [
                    'program_studi' => $prodiName,
                    'total' => $totalMahasiswa,
                    'selesai' => $sudahSelesai,
                    'proses' => $dalamProses,
                    'belum' => $belumSelesai,
                    'belum_atau_proses' => $belumAtauProses,
                    'kepatuhan_persen' => $kepatuhanPersen,
                    'total_konsultasi' => $totalKonsultasi,
                ],
            ],
            'chart_data' => [
                'categories' => $categories,
                'selesai' => $selesaiSeries,
                'proses' => $prosesSeries,
                'belum' => $belumSeries,
                'donut' => [$sudahSelesai, $dalamProses, $belumSelesai],
            ],
        ];
    }

    /**
     * Bangun query daftar status perwalian mahasiswa dengan eager loading.
     *
     * @param  array{tahun_akademik?: ?string, angkatan?: ?string, prodi?: ?string, status_perwalian?: ?string, search?: ?string}  $filters
     * @return Builder<PerwalianMahasiswa>
     */
    public function getPerwalianQuery(array $filters = []): Builder
    {
        return PerwalianMahasiswa::query()
            ->with([
                'mahasiswa:id,name,email,nomor_induk,program_studi',
                'dosenWali:id,name,email,nomor_induk',
            ])
            ->withCount('konsultasis')
            ->filterByGkmCriteria($filters)
            ->orderBy('angkatan')
            ->orderByRaw("CASE status_perwalian WHEN 'belum' THEN 1 WHEN 'proses' THEN 2 WHEN 'selesai' THEN 3 ELSE 4 END")
            ->orderBy('id');
    }

    /**
     * Bangun query log konsultasi bimbingan akademik (view-only) dengan eager loading.
     *
     * @param  array{
     *     tahun_akademik?: ?string,
     *     angkatan?: ?string,
     *     prodi?: ?string,
     *     status_sesi?: ?string,
     *     dosen_wali_id?: ?string,
     *     tanggal_dari?: ?string,
     *     tanggal_sampai?: ?string,
     *     search?: ?string
     * }  $filters
     * @return Builder<KonsultasiAkademik>
     */
    public function getKonsultasiQuery(array $filters = []): Builder
    {
        return KonsultasiAkademik::query()
            ->with([
                'mahasiswa:id,name,email,nomor_induk,program_studi',
                'dosenWali:id,name,email,nomor_induk',
                'perwalianMahasiswa:id,angkatan,semester_berjalan,ipk,sks_diambil,status_perwalian,status_krs',
            ])
            ->filterByGkmCriteria($filters)
            ->orderByDesc('tanggal_konsultasi')
            ->orderByDesc('id');
    }

    /**
     * Hitung ringkasan statistik log konsultasi akademik.
     *
     * @param  array<string, mixed>  $filters
     * @return array{total: int, acc_krs: int, selesai: int, tindak_lanjut: int}
     */
    public function getKonsultasiSummary(array $filters = []): array
    {
        $rows = KonsultasiAkademik::query()
            ->filterByGkmCriteria($filters)
            ->selectRaw('status_sesi, COUNT(*) as aggregate_count')
            ->groupBy('status_sesi')
            ->pluck('aggregate_count', 'status_sesi');

        $accKrs = (int) ($rows['ACC KRS/IRS'] ?? 0);
        $selesai = (int) ($rows['Selesai'] ?? 0);
        $tindakLanjut = (int) ($rows['Perlu Tindak Lanjut'] ?? 0);

        return [
            'total' => (int) $rows->sum(),
            'acc_krs' => $accKrs,
            'selesai' => $selesai,
            'tindak_lanjut' => $tindakLanjut,
        ];
    }

    /**
     * Kumpulkan dataset lengkap untuk kebutuhan ekspor laporan akreditasi (PDF & Excel).
     *
     * @param  array{tahun_akademik?: ?string, angkatan?: ?string, prodi?: ?string}  $filters
     * @return array{
     *     universitas_label: string,
     *     prodi_label: string,
     *     angkatan_label: string,
     *     tahun_akademik_label: string,
     *     summary: array<string, mixed>,
     *     perwalians: Collection<int, PerwalianMahasiswa>,
     *     konsultasis: Collection<int, KonsultasiAkademik>
     * }
     */
    public function getExportDataset(array $filters = []): array
    {
        $normalizedFilters = [
            'tahun_akademik' => ! empty($filters['tahun_akademik']) ? (string) $filters['tahun_akademik'] : null,
            'angkatan' => ! empty($filters['angkatan']) ? (string) $filters['angkatan'] : null,
            'prodi' => ! empty($filters['prodi']) ? (string) $filters['prodi'] : null,
        ];

        $summary = $this->getDashboardSummary($normalizedFilters);
        $perwalians = $this->getPerwalianQuery($normalizedFilters)->get();
        $konsultasis = $this->getKonsultasiQuery($normalizedFilters)->get();

        return [
            'universitas_label' => config('simbim.universitas', 'Universitas Pendidikan Indonesia (UPI)'),
            'prodi_label' => $normalizedFilters['prodi'] ?? config('simbim.prodi', 'Pendidikan Sistem dan Teknologi Informasi (PSTI)'),
            'angkatan_label' => $normalizedFilters['angkatan'] ? 'Angkatan '.$normalizedFilters['angkatan'] : 'Semua Angkatan',
            'tahun_akademik_label' => $normalizedFilters['tahun_akademik'] ?? 'Semua Periode Akademik',
            'summary' => $summary,
            'perwalians' => $perwalians,
            'konsultasis' => $konsultasis,
        ];
    }
}
