<?php

namespace Tests\Unit;

use App\Services\Exports\SimplePdfExporter;
use App\Services\Exports\SimpleXlsxExporter;
use App\Services\UserImportService;
use PHPUnit\Framework\TestCase;

class GkmExportTest extends TestCase
{
    public function test_simple_xlsx_exporter_menghasilkan_file_xlsx_multi_sheet_yang_valid(): void
    {
        $exporter = new SimpleXlsxExporter;

        $binary = $exporter->generate([
            [
                'name' => 'Rekap Per Angkatan',
                'title' => 'LAPORAN AKREDITASI GKM',
                'subtitle' => 'Prodi PSTI — Universitas Pendidikan Indonesia (UPI)',
                'headers' => ['No', 'Angkatan', 'Total', 'Selesai'],
                'widths' => [8, 28, 12, 12],
                'rows' => [
                    [1, 'Angkatan 2021 (PSTI - UPI)', 4, 3],
                    [2, 'Angkatan 2022 (PSTI - UPI)', 4, 2],
                ],
            ],
            [
                'name' => 'Log Konsultasi',
                'title' => 'ARSIP KONSULTASI',
                'subtitle' => 'View Only',
                'headers' => ['No', 'Topik', 'Catatan'],
                'rows' => [
                    [1, 'Pengisian KRS Semester 7', 'Disetujui 21 SKS tanpa bentrok jadwal.'],
                ],
            ],
        ]);

        $this->assertStringStartsWith("PK\x03\x04", $binary);
        $this->assertStringContainsString('[Content_Types].xml', $binary);
        $this->assertStringContainsString('xl/worksheets/sheet1.xml', $binary);
        $this->assertStringContainsString('xl/worksheets/sheet2.xml', $binary);

        $tempPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'gkm_test_'.uniqid('', true).'.xlsx';
        file_put_contents($tempPath, $binary);

        try {
            $parser = new UserImportService;
            $parsedRows = $parser->parseXlsx($tempPath);

            $this->assertNotEmpty($parsedRows);
            $this->assertSame('LAPORAN AKREDITASI GKM', $parsedRows[0][0]);
            $this->assertSame('Angkatan 2021 (PSTI - UPI)', $parsedRows[3][1]);
            $this->assertSame('Angkatan 2022 (PSTI - UPI)', $parsedRows[4][1]);
        } finally {
            @unlink($tempPath);
        }
    }

    public function test_simple_pdf_exporter_menghasilkan_pdf_valid_dengan_halaman_otomatis(): void
    {
        $exporter = new SimplePdfExporter;

        $manyPerwalianRows = [];
        for ($i = 1; $i <= 40; $i++) {
            $manyPerwalianRows[] = [
                (string) $i,
                '2108107010'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'Mahasiswa Uji Akreditasi Ke-'.$i,
                'PSTI - UPI',
                $i % 2 === 0 ? 'Angk. 2021 / Sem 7' : 'Angk. 2022 / Sem 5',
                'Dr. Siti Rahmawati, M.Kom.',
                'Sudah Selesai KRS/IRS - Disetujui (21 SKS)',
                '2 Sesi | 05/09/2026',
            ];
        }

        $pdfBinary = $exporter->generateAccreditationReport([
            'title' => 'LAPORAN AKREDITASI BIMBINGAN AKADEMIK & PERWALIAN (GKM)',
            'subtitle' => 'Prodi Pendidikan Sistem dan Teknologi Informasi (PSTI) — Universitas Pendidikan Indonesia (UPI)',
            'printed_at' => '10/11/2026 16:50 WIB',
            'petugas_nama' => 'Dr. Ratna Wijaya, S.T., M.T.',
            'petugas_nip' => '198204122008122003',
            'metadata' => [
                ['label' => 'Program Studi & Institusi', 'value' => 'Pendidikan Sistem dan Teknologi Informasi (PSTI) — UPI'],
                ['label' => 'Periode', 'value' => 'Ganjil 2026/2027'],
                ['label' => 'Petugas', 'value' => 'Dr. Ratna Wijaya, S.T., M.T.'],
                ['label' => 'Tanggal Cetak', 'value' => '10/11/2026 16:50 WIB'],
            ],
            'kpis' => [
                ['label' => 'Total Mahasiswa', 'value' => '40 Mhs', 'sublabel' => '2 Angkatan PSTI UPI'],
                ['label' => 'Sudah Selesai', 'value' => '40 Mhs', 'sublabel' => '100%'],
                ['label' => 'Belum Selesai', 'value' => '0 Mhs', 'sublabel' => '0 Proses'],
                ['label' => 'Log Konsultasi', 'value' => '40 Sesi', 'sublabel' => 'View Only'],
            ],
            'prodi_headers' => ['No', 'Angkatan (PSTI UPI)', 'Total Mhs', 'Sudah Selesai', 'Dalam Proses', 'Belum Selesai', '% Kepatuhan', 'Total Log'],
            'prodi_widths' => [28.0, 185.0, 85.0, 95.0, 95.0, 95.0, 95.0, 95.0],
            'prodi_rows' => [
                ['1', 'Angkatan 2021', '20', '20', '0', '0', '100%', '20 Sesi'],
                ['2', 'Angkatan 2022', '20', '20', '0', '0', '100%', '20 Sesi'],
            ],
            'perwalian_headers' => ['No', 'NIM', 'Nama Mahasiswa', 'Prodi / Univ', 'Angk / Sem', 'Dosen Wali', 'Status Bimbingan KRS/IRS', 'Sesi & Tgl ACC'],
            'perwalian_widths' => [26.0, 86.0, 128.0, 96.0, 62.0, 130.0, 173.0, 72.0],
            'perwalian_rows' => $manyPerwalianRows,
            'konsultasi_headers' => ['No', 'Tanggal', 'Mahasiswa (NIM)', 'Angkatan', 'Dosen Wali', 'Topik & Catatan Komunikasi', 'Status'],
            'konsultasi_widths' => [26.0, 76.0, 118.0, 86.0, 108.0, 289.0, 70.0],
            'konsultasi_rows' => [
                ['1', '05/09/2026', 'Mahasiswa Uji (2108107010001)', 'Angk. 2021', 'Dr. Siti Rahmawati', 'Topik: Validasi KRS | Disetujui.', 'ACC KRS/IRS'],
            ],
        ]);

        $this->assertStringStartsWith('%PDF-1.4', $pdfBinary);
        $this->assertStringEndsWith('%%EOF', $pdfBinary);
        $this->assertStringContainsString('xref', $pdfBinary);
        $this->assertStringContainsString('Halaman 1 dari ', $pdfBinary);
        $this->assertStringContainsString('Halaman 2 dari ', $pdfBinary);
    }
}
