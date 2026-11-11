<?php

namespace Tests\Unit;

use App\Services\UserImportService;
use PHPUnit\Framework\TestCase;

class UserImportServiceTest extends TestCase
{
    protected UserImportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UserImportService;
    }

    public function test_normalize_role_maps_variations_correctly(): void
    {
        $this->assertEquals('admin', $this->service->normalizeRole('Admin'));
        $this->assertEquals('admin', $this->service->normalizeRole('Administrator'));
        $this->assertEquals('dosen', $this->service->normalizeRole('Dosen'));
        $this->assertEquals('dosen', $this->service->normalizeRole('Dosen Pembimbing'));
        $this->assertEquals('dewan_skripsi', $this->service->normalizeRole('Dewan Skripsi'));
        $this->assertEquals('dewan_skripsi', $this->service->normalizeRole('dewan_skripsi'));
        $this->assertEquals('gkm', $this->service->normalizeRole('GKM'));
        $this->assertEquals('gkm', $this->service->normalizeRole('Gugus Kendali Mutu'));
        $this->assertEquals('mahasiswa', $this->service->normalizeRole('Mahasiswa'));
        $this->assertEquals('mahasiswa', $this->service->normalizeRole(''));
        $this->assertEquals('mahasiswa', $this->service->normalizeRole(null));
    }

    public function test_parse_csv_file_reads_rows(): void
    {
        $csvPath = __DIR__.'/../../public/templates/template_import_user.csv';
        $this->assertFileExists($csvPath);

        $rows = $this->service->parseCsv($csvPath);

        $this->assertNotEmpty($rows);
        $this->assertEquals(['nama', 'email', 'nomor_induk', 'role', 'program_studi', 'password'], $rows[0]);
        $this->assertCount(5, $rows); // 1 header + 4 data rows
    }

    public function test_parse_xlsx_file_reads_rows(): void
    {
        $xlsxPath = __DIR__.'/../../public/templates/template_import_user.xlsx';
        $this->assertFileExists($xlsxPath);

        $rows = $this->service->parseXlsx($xlsxPath);

        $this->assertNotEmpty($rows);
        $this->assertEquals('nama', $rows[0][0]);
        $this->assertEquals('email', $rows[0][1]);
        $this->assertGreaterThanOrEqual(5, count($rows));
    }
}
