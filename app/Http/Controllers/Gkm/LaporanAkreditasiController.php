<?php

namespace App\Http\Controllers\Gkm;

use App\Http\Controllers\Controller;
use App\Services\Exports\SimplePdfExporter;
use App\Services\Exports\SimpleXlsxExporter;
use App\Services\GkmAccreditationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LaporanAkreditasiController extends Controller
{
    public function __construct(
        protected GkmAccreditationService $accreditationService,
        protected SimplePdfExporter $pdfExporter,
        protected SimpleXlsxExporter $xlsxExporter
    ) {}

    /**
     * Unduh Laporan Akreditasi Rekap Perwalian & Riwayat Konsultasi Prodi PSTI UPI dalam format PDF.
     */
    public function exportPdf(Request $request): Response
    {
        $validated = $request->validate([
            'angkatan' => ['nullable', 'string', 'max:10'],
            'tahun_akademik' => ['nullable', 'string', 'max:50'],
        ]);

        $dataset = $this->accreditationService->getExportDataset($validated);
        $summary = $dataset['summary'];
        $user = Auth::user();
        $printedAt = now()->format('d/m/Y H:i').' WIB';

        $angkatanRows = [];
        foreach ($summary['rekap_per_angkatan'] as $idx => $item) {
            $angkatanRows[] = [
                (string) ($idx + 1),
                $item['label'],
                (string) $item['total'],
                (string) $item['selesai'],
                (string) $item['proses'],
                (string) $item['belum'],
                $item['kepatuhan_persen'].'%',
                (string) $item['total_konsultasi'].' Sesi',
            ];
        }

        $perwalianRows = [];
        foreach ($dataset['perwalians'] as $idx => $row) {
            $perwalianRows[] = [
                (string) ($idx + 1),
                $row->mahasiswa?->nomor_induk ?? '-',
                $row->mahasiswa?->name ?? '-',
                config('simbim.prodi_singkat', 'PSTI').' - UPI',
                'Angk. '.$row->angkatan.' / Sem '.$row->semester_berjalan,
                $row->dosenWali?->name ?? '-',
                $row->status_label.' - '.$row->status_krs.' ('.$row->sks_diambil.' SKS)',
                $row->konsultasis_count.' Sesi'.($row->tanggal_persetujuan_krs ? ' | '.$row->tanggal_persetujuan_krs->format('d/m/Y') : ''),
            ];
        }

        $konsultasiRows = [];
        foreach ($dataset['konsultasis'] as $idx => $log) {
            $catatanRingkas = 'Topik: '.$log->topik;
            if (! empty($log->catatan_mahasiswa)) {
                $catatanRingkas .= ' | Mhs: '.$log->catatan_mahasiswa;
            }
            $catatanRingkas .= ' | Wali: '.$log->catatan_dosen_wali;

            $konsultasiRows[] = [
                (string) ($idx + 1),
                ($log->tanggal_konsultasi ? $log->tanggal_konsultasi->format('d/m/Y') : '-').($log->waktu_konsultasi ? ' ('.$log->waktu_konsultasi.')' : ''),
                ($log->mahasiswa?->name ?? '-').' ('.($log->mahasiswa?->nomor_induk ?? '-').')',
                'Angk. '.($log->perwalianMahasiswa?->angkatan ?? '-'),
                $log->dosenWali?->name ?? '-',
                $catatanRingkas,
                $log->status_sesi,
            ];
        }

        $pdfBinary = $this->pdfExporter->generateAccreditationReport([
            'title' => 'LAPORAN AKREDITASI BIMBINGAN AKADEMIK & PERWALIAN (GKM)',
            'subtitle' => 'Prodi '.$dataset['prodi_label'].' — '.$dataset['universitas_label'],
            'printed_at' => $printedAt,
            'petugas_nama' => $user?->name ?? 'Ketua Gugus Kendali Mutu (GKM)',
            'petugas_nip' => $user?->nomor_induk ?? '-',
            'metadata' => [
                ['label' => 'Program Studi & Institusi', 'value' => $dataset['prodi_label'].' — UPI'],
                ['label' => 'Cakupan Angkatan & Periode', 'value' => $dataset['angkatan_label'].' | '.$dataset['tahun_akademik_label']],
                ['label' => 'Penanggung Jawab Audit', 'value' => $user?->name ?? 'Tim GKM PSTI UPI'],
                ['label' => 'Tanggal Cetak Bukti Fisik', 'value' => $printedAt],
            ],
            'kpis' => [
                [
                    'label' => 'Total Mahasiswa Perwalian',
                    'value' => (string) $summary['total_mahasiswa'].' Mhs',
                    'sublabel' => count($summary['rekap_per_angkatan']).' Angkatan Aktif PSTI UPI',
                ],
                [
                    'label' => 'Sudah Selesai KRS/IRS',
                    'value' => (string) $summary['sudah_selesai'].' Mhs',
                    'sublabel' => 'Tingkat Kepatuhan '.$summary['kepatuhan_persen'].'%',
                ],
                [
                    'label' => 'Proses / Belum Selesai',
                    'value' => (string) $summary['belum_atau_proses'].' Mhs',
                    'sublabel' => $summary['dalam_proses'].' Proses, '.$summary['belum_selesai'].' Belum',
                ],
                [
                    'label' => 'Arsip Log Konsultasi',
                    'value' => (string) $summary['total_konsultasi'].' Sesi',
                    'sublabel' => 'Jejak Rekam Terkunci (View-Only)',
                ],
            ],
            'prodi_headers' => ['No', 'Angkatan (PSTI UPI)', 'Total Mhs', 'Sudah Selesai', 'Dalam Proses', 'Belum Selesai', '% Kepatuhan', 'Total Log'],
            'prodi_widths' => [28.0, 185.0, 85.0, 95.0, 95.0, 95.0, 95.0, 95.0],
            'prodi_rows' => $angkatanRows,
            'perwalian_headers' => ['No', 'NIM', 'Nama Mahasiswa', 'Prodi / Univ', 'Angk / Sem', 'Dosen Wali', 'Status Bimbingan KRS/IRS', 'Sesi & Tgl ACC'],
            'perwalian_widths' => [26.0, 86.0, 128.0, 96.0, 62.0, 130.0, 173.0, 72.0],
            'perwalian_rows' => $perwalianRows,
            'konsultasi_headers' => ['No', 'Tanggal', 'Mahasiswa (NIM)', 'Angkatan', 'Dosen Wali', 'Topik & Catatan Komunikasi (View-Only)', 'Status'],
            'konsultasi_widths' => [26.0, 76.0, 118.0, 86.0, 108.0, 289.0, 70.0],
            'konsultasi_rows' => $konsultasiRows,
        ]);

        $slug = ! empty($validated['angkatan']) ? 'PSTI_UPI_Angkatan_'.$validated['angkatan'] : 'PSTI_UPI';
        $filename = 'Laporan_Akreditasi_GKM_'.Str::slug($slug, '_').'_'.now()->format('Ymd_His').'.pdf';

        return response($pdfBinary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    /**
     * Unduh Laporan Akreditasi Rekap Perwalian & Riwayat Konsultasi Prodi PSTI UPI dalam format Excel (.xlsx).
     */
    public function exportExcel(Request $request): Response
    {
        $validated = $request->validate([
            'angkatan' => ['nullable', 'string', 'max:10'],
            'tahun_akademik' => ['nullable', 'string', 'max:50'],
        ]);

        $dataset = $this->accreditationService->getExportDataset($validated);
        $summary = $dataset['summary'];
        $printedAt = now()->format('d/m/Y H:i').' WIB';
        $subtitle = "Prodi: {$dataset['prodi_label']} — {$dataset['universitas_label']} | Cakupan: {$dataset['angkatan_label']} | Periode: {$dataset['tahun_akademik_label']} | Dicetak: {$printedAt}";

        // Sheet 1: Rekapitulasi Per Angkatan Prodi PSTI UPI
        $angkatanRows = [];
        foreach ($summary['rekap_per_angkatan'] as $idx => $item) {
            $kepatuhanStyle = $item['kepatuhan_persen'] >= 75 ? 6 : ($item['kepatuhan_persen'] >= 50 ? 7 : 8);
            $angkatanRows[] = [
                ['value' => $idx + 1, 'style' => 5, 'type' => 'n'],
                ['value' => $item['label'].' (PSTI - UPI)', 'style' => 4, 'type' => 's'],
                ['value' => $item['total'], 'style' => 5, 'type' => 'n'],
                ['value' => $item['selesai'], 'style' => 6, 'type' => 'n'],
                ['value' => $item['proses'], 'style' => 7, 'type' => 'n'],
                ['value' => $item['belum'], 'style' => 8, 'type' => 'n'],
                ['value' => $item['kepatuhan_persen'].'%', 'style' => $kepatuhanStyle, 'type' => 's'],
                ['value' => $item['total_konsultasi'], 'style' => 5, 'type' => 'n'],
            ];
        }

        // Baris Total Keseluruhan
        $angkatanRows[] = [
            ['value' => '', 'style' => 5, 'type' => 's'],
            ['value' => 'TOTAL KESELURUHAN PRODI PSTI UPI', 'style' => 3, 'type' => 's'],
            ['value' => $summary['total_mahasiswa'], 'style' => 5, 'type' => 'n'],
            ['value' => $summary['sudah_selesai'], 'style' => 6, 'type' => 'n'],
            ['value' => $summary['dalam_proses'], 'style' => 7, 'type' => 'n'],
            ['value' => $summary['belum_selesai'], 'style' => 8, 'type' => 'n'],
            ['value' => $summary['kepatuhan_persen'].'%', 'style' => 5, 'type' => 's'],
            ['value' => $summary['total_konsultasi'], 'style' => 5, 'type' => 'n'],
        ];

        // Sheet 2: Status Perwalian Mahasiswa
        $perwalianRows = [];
        foreach ($dataset['perwalians'] as $idx => $row) {
            $statusStyle = match ($row->status_perwalian) {
                'selesai' => 6,
                'proses' => 7,
                default => 8,
            };
            $perwalianRows[] = [
                ['value' => $idx + 1, 'style' => 5, 'type' => 'n'],
                ['value' => $row->mahasiswa?->nomor_induk ?? '-', 'style' => 5, 'type' => 's'],
                ['value' => $row->mahasiswa?->name ?? '-', 'style' => 4, 'type' => 's'],
                ['value' => $row->program_studi.' — UPI', 'style' => 4, 'type' => 's'],
                ['value' => $row->angkatan, 'style' => 5, 'type' => 's'],
                ['value' => $row->semester_berjalan, 'style' => 5, 'type' => 'n'],
                ['value' => $row->tahun_akademik, 'style' => 5, 'type' => 's'],
                ['value' => $row->dosenWali?->name ?? '-', 'style' => 4, 'type' => 's'],
                ['value' => (float) $row->ipk, 'style' => 5, 'type' => 'n'],
                ['value' => $row->sks_diambil, 'style' => 5, 'type' => 'n'],
                ['value' => $row->status_label, 'style' => $statusStyle, 'type' => 's'],
                ['value' => $row->status_krs, 'style' => 4, 'type' => 's'],
                ['value' => $row->konsultasis_count, 'style' => 5, 'type' => 'n'],
                ['value' => $row->tanggal_persetujuan_krs?->format('d/m/Y') ?? '-', 'style' => 5, 'type' => 's'],
                ['value' => $row->catatan_akhir_wali ?? '-', 'style' => 4, 'type' => 's'],
            ];
        }

        // Sheet 3: Riwayat & Log Konsultasi Akademik (View Only)
        $konsultasiRows = [];
        foreach ($dataset['konsultasis'] as $idx => $log) {
            $statusStyle = match ($log->status_sesi) {
                'ACC KRS/IRS' => 6,
                'Selesai' => 5,
                default => 7,
            };
            $konsultasiRows[] = [
                ['value' => $idx + 1, 'style' => 5, 'type' => 'n'],
                ['value' => $log->tanggal_konsultasi?->format('d/m/Y') ?? '-', 'style' => 5, 'type' => 's'],
                ['value' => $log->waktu_konsultasi ?? '-', 'style' => 5, 'type' => 's'],
                ['value' => $log->mahasiswa?->nomor_induk ?? '-', 'style' => 5, 'type' => 's'],
                ['value' => $log->mahasiswa?->name ?? '-', 'style' => 4, 'type' => 's'],
                ['value' => $log->program_studi.' — UPI', 'style' => 4, 'type' => 's'],
                ['value' => $log->tahun_akademik, 'style' => 5, 'type' => 's'],
                ['value' => $log->dosenWali?->name ?? '-', 'style' => 4, 'type' => 's'],
                ['value' => $log->metode, 'style' => 4, 'type' => 's'],
                ['value' => $log->topik, 'style' => 4, 'type' => 's'],
                ['value' => $log->catatan_mahasiswa ?? '-', 'style' => 4, 'type' => 's'],
                ['value' => $log->catatan_dosen_wali, 'style' => 4, 'type' => 's'],
                ['value' => $log->status_sesi, 'style' => $statusStyle, 'type' => 's'],
                ['value' => $log->lampiran_krs ?? '-', 'style' => 4, 'type' => 's'],
            ];
        }

        $xlsxBinary = $this->xlsxExporter->generate([
            [
                'name' => 'Rekap Per Angkatan',
                'title' => 'LAPORAN AKREDITASI GKM — REKAPITULASI PERWALIAN PRODI PSTI UPI',
                'subtitle' => $subtitle,
                'headers' => ['No', 'Angkatan (Prodi PSTI UPI)', 'Total Mahasiswa', 'Sudah Selesai KRS', 'Dalam Proses', 'Belum Selesai', 'Persentase Kepatuhan', 'Total Sesi Log'],
                'widths' => [6, 30, 18, 20, 16, 16, 22, 18],
                'rows' => $angkatanRows,
            ],
            [
                'name' => 'Rekap Perwalian Mhs',
                'title' => 'DAFTAR STATUS PENYELESAIAN BIMBINGAN AKADEMIK (KRS/IRS) MAHASISWA PSTI UPI',
                'subtitle' => $subtitle,
                'headers' => ['No', 'NIM', 'Nama Mahasiswa', 'Program Studi & Universitas', 'Angkatan', 'Semester', 'Tahun Akademik', 'Dosen Wali', 'IPK', 'SKS Diambil', 'Kategori Status', 'Detail Status KRS/IRS', 'Jumlah Sesi', 'Tgl Persetujuan KRS', 'Catatan Akhir Wali'],
                'widths' => [6, 18, 26, 38, 12, 11, 20, 28, 9, 13, 24, 28, 13, 20, 42],
                'rows' => $perwalianRows,
            ],
            [
                'name' => 'Log Konsultasi View Only',
                'title' => 'ARSIP RIWAYAT & LOG KONSULTASI BIMBINGAN AKADEMIK PSTI UPI (VIEW ONLY)',
                'subtitle' => $subtitle,
                'headers' => ['No', 'Tanggal Konsultasi', 'Waktu', 'NIM', 'Nama Mahasiswa', 'Program Studi & Universitas', 'Tahun Akademik', 'Dosen Wali', 'Metode', 'Topik Konsultasi', 'Catatan Mahasiswa', 'Arahan / Catatan Dosen Wali', 'Status Sesi', 'Lampiran KRS'],
                'widths' => [6, 18, 18, 18, 25, 38, 20, 28, 22, 34, 42, 46, 20, 26],
                'rows' => $konsultasiRows,
            ],
        ]);

        $slug = ! empty($validated['angkatan']) ? 'PSTI_UPI_Angkatan_'.$validated['angkatan'] : 'PSTI_UPI';
        $filename = 'Laporan_Akreditasi_GKM_'.Str::slug($slug, '_').'_'.now()->format('Ymd_His').'.xlsx';

        return response($xlsxBinary, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }
}
