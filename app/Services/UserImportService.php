<?php

namespace App\Services;

use App\Models\User;
use App\Support\SimpleZipReader;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use ZipArchive;

class UserImportService
{
    /**
     * Import users from an uploaded CSV or XLSX file.
     *
     * @return array{
     *     total_rows: int,
     *     imported_count: int,
     *     skipped_count: int,
     *     errors: array<int, array{row: int, message: string}>,
     *     created_users: array<int, array{name: string, email: string, role: string, nomor_induk: ?string}>
     * }
     */
    public function import(UploadedFile $file, ?string $defaultPassword = null): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();

        if ($extension === 'csv' || $file->getClientMimeType() === 'text/csv' || $file->getClientMimeType() === 'text/plain') {
            $rows = $this->parseCsv($path);
        } elseif (in_array($extension, ['xlsx', 'xls'], true) || str_contains((string) $file->getClientMimeType(), 'spreadsheet')) {
            $rows = $this->parseXlsx($path);
        } else {
            // Coba parse sebagai CSV jika ekstensi tidak pasti
            $rows = $this->parseCsv($path);
        }

        return $this->processRows($rows, $defaultPassword);
    }

    /**
     * Parse CSV file into an array of rows.
     *
     * @return array<int, array<int, string>>
     */
    public function parseCsv(string $path): array
    {
        $handle = fopen($path, 'r');
        if (! $handle) {
            return [];
        }

        // Baca sampel untuk deteksi delimiter dan hapus UTF-8 BOM
        $sample = fread($handle, 4096);
        rewind($handle);

        $delimiter = ',';
        $commaCount = substr_count($sample, ',');
        $semicolonCount = substr_count($sample, ';');
        $tabCount = substr_count($sample, "\t");

        if ($semicolonCount > $commaCount && $semicolonCount > $tabCount) {
            $delimiter = ';';
        } elseif ($tabCount > $commaCount && $tabCount > $semicolonCount) {
            $delimiter = "\t";
        }

        $rows = [];
        $isFirst = true;

        while (($data = fgetcsv($handle, 0, $delimiter, '"', '\\')) !== false) {
            if ($isFirst && isset($data[0])) {
                // Hapus BOM UTF-8 jika ada pada sel pertama
                $data[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $data[0]);
                $isFirst = false;
            }

            // Bersihkan setiap sel dari whitespace
            $cleanRow = array_map(fn ($cell) => trim((string) $cell), $data);

            // Lewati baris yang seluruh kolomnya kosong
            if (array_filter($cleanRow, fn ($v) => $v !== '') === []) {
                continue;
            }

            $rows[] = $cleanRow;
        }

        fclose($handle);

        return $rows;
    }

    /**
     * Parse XLSX file into an array of rows using ZipArchive or pure PHP fallback.
     *
     * @return array<int, array<int, string>>
     */
    public function parseXlsx(string $path): array
    {
        $extracted = [];

        if (class_exists(ZipArchive::class)) {
            $zip = new ZipArchive;
            if ($zip->open($path) === true) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $entryName = $zip->getNameIndex($i);
                    if ($entryName !== false && (
                        str_ends_with($entryName, 'sharedStrings.xml') ||
                        str_ends_with($entryName, 'sheet1.xml') ||
                        str_contains($entryName, 'worksheets/')
                    )) {
                        $extracted[$entryName] = $zip->getFromIndex($i);
                    }
                }
                $zip->close();
            }
        }

        // Jika ZipArchive tidak ada atau gagal mengekstrak, gunakan pure PHP zip reader
        if (empty($extracted)) {
            try {
                $extracted = (new SimpleZipReader)->extractXmlEntries($path);
            } catch (Exception) {
                return [];
            }
        }

        // Temukan file sharedStrings.xml
        $sharedStringsXml = null;
        foreach ($extracted as $name => $content) {
            if (str_ends_with($name, 'sharedStrings.xml')) {
                $sharedStringsXml = $content;
                break;
            }
        }

        $strings = [];
        if ($sharedStringsXml) {
            $xml = @simplexml_load_string($sharedStringsXml);
            if ($xml && isset($xml->si)) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $strings[] = (string) $si->t;
                    } elseif (isset($si->r)) {
                        $richText = '';
                        foreach ($si->r as $r) {
                            $richText .= (string) ($r->t ?? '');
                        }
                        $strings[] = $richText;
                    } else {
                        $strings[] = '';
                    }
                }
            }
        }

        // Temukan file worksheet utama (sheet1.xml)
        $sheetXml = null;
        foreach ($extracted as $name => $content) {
            if (str_ends_with($name, 'sheet1.xml')) {
                $sheetXml = $content;
                break;
            }
        }

        if (! $sheetXml) {
            // Ambil worksheet pertama yang ada
            foreach ($extracted as $name => $content) {
                if (str_contains($name, 'worksheets/')) {
                    $sheetXml = $content;
                    break;
                }
            }
        }

        if (! $sheetXml) {
            return [];
        }

        $xml = @simplexml_load_string($sheetXml);
        if (! $xml || ! isset($xml->sheetData)) {
            return [];
        }

        $rows = [];
        foreach ($xml->sheetData->row as $row) {
            $rowCells = [];
            foreach ($row->c as $c) {
                $coord = (string) $c['r'];
                $colIndex = $this->coordinateToColumnIndex($coord);

                $val = '';
                $type = (string) $c['t'];

                if ($type === 's') {
                    $stringIdx = (int) $c->v;
                    $val = $strings[$stringIdx] ?? '';
                } elseif ($type === 'inlineStr' && isset($c->is->t)) {
                    $val = (string) $c->is->t;
                } elseif (isset($c->v)) {
                    $val = (string) $c->v;
                }

                // Tempatkan nilai pada posisi kolom yang benar
                $rowCells[$colIndex] = trim($val);
            }

            if (! empty($rowCells)) {
                $maxKey = max(array_keys($rowCells));
                $normalizedRow = [];
                for ($k = 0; $k <= $maxKey; $k++) {
                    $normalizedRow[$k] = $rowCells[$k] ?? '';
                }

                if (array_filter($normalizedRow, fn ($v) => $v !== '') !== []) {
                    $rows[] = $normalizedRow;
                }
            }
        }

        return $rows;
    }

    /**
     * Convert cell coordinate (e.g. 'B3') to 0-based column index (1).
     */
    protected function coordinateToColumnIndex(string $coordinate): int
    {
        preg_match('/^([A-Z]+)/', strtoupper($coordinate), $matches);
        if (! isset($matches[1])) {
            return 0;
        }

        $letters = $matches[1];
        $index = 0;
        for ($i = 0; $i < strlen($letters); $i++) {
            $index = $index * 26 + (ord($letters[$i]) - 64);
        }

        return $index - 1;
    }

    /**
     * Process parsed rows into User records.
     *
     * @param  array<int, array<int, string>>  $rows
     * @return array{
     *     total_rows: int,
     *     imported_count: int,
     *     skipped_count: int,
     *     errors: array<int, array{row: int, message: string}>,
     *     created_users: array<int, array{name: string, email: string, role: string, nomor_induk: ?string}>
     * }
     */
    public function processRows(array $rows, ?string $defaultPassword = null): array
    {
        if (count($rows) < 2) {
            return [
                'total_rows' => 0,
                'imported_count' => 0,
                'skipped_count' => 0,
                'errors' => [['row' => 1, 'message' => 'File kosong atau tidak memiliki baris data.']],
                'created_users' => [],
            ];
        }

        $headerRow = array_shift($rows);
        $columnMap = $this->mapHeaders($headerRow);

        $importedCount = 0;
        $skippedCount = 0;
        $errors = [];
        $createdUsers = [];

        $fallbackPassword = $defaultPassword ?: 'simbim123';

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +1 untuk 1-based, +1 karena header sudah di-shift

            $name = $this->extractValue($row, $columnMap, 'name');
            $email = $this->extractValue($row, $columnMap, 'email');
            $nomorInduk = $this->extractValue($row, $columnMap, 'nomor_induk');
            $rawRole = $this->extractValue($row, $columnMap, 'role');
            $programStudi = $this->extractValue($row, $columnMap, 'program_studi');
            $password = $this->extractValue($row, $columnMap, 'password') ?: $fallbackPassword;

            if (empty($name)) {
                $skippedCount++;
                $errors[] = ['row' => $rowNumber, 'message' => 'Nama lengkap wajib diisi.'];

                continue;
            }

            if (empty($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skippedCount++;
                $errors[] = ['row' => $rowNumber, 'message' => "Format email tidak valid atau kosong: '{$email}'."];

                continue;
            }

            if (User::where('email', $email)->exists()) {
                $skippedCount++;
                $errors[] = ['row' => $rowNumber, 'message' => "Email '{$email}' sudah terdaftar di sistem."];

                continue;
            }

            if (! empty($nomorInduk) && User::where('nomor_induk', $nomorInduk)->exists()) {
                $skippedCount++;
                $errors[] = ['row' => $rowNumber, 'message' => "Nomor Induk / NIM / NIP '{$nomorInduk}' sudah terdaftar di sistem."];

                continue;
            }

            $role = $this->normalizeRole($rawRole);

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'nomor_induk' => ! empty($nomorInduk) ? $nomorInduk : null,
                'role' => $role,
                'program_studi' => ! empty($programStudi)
                    ? $programStudi
                    : config('simbim.prodi', 'Pendidikan Sistem dan Teknologi Informasi (PSTI)'),
                'password' => Hash::make($password),
            ]);

            $importedCount++;
            $createdUsers[] = [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'nomor_induk' => $user->nomor_induk,
            ];
        }

        return [
            'total_rows' => count($rows),
            'imported_count' => $importedCount,
            'skipped_count' => $skippedCount,
            'errors' => $errors,
            'created_users' => $createdUsers,
        ];
    }

    /**
     * Map header labels to field names.
     *
     * @param  array<int, string>  $headerRow
     * @return array<string, int>
     */
    protected function mapHeaders(array $headerRow): array
    {
        $map = [];

        foreach ($headerRow as $idx => $header) {
            $clean = strtolower(trim($header));
            $clean = str_replace([' ', '-', '_', '.', '/', '\\'], '', $clean);

            if (in_array($clean, ['nama', 'namalengkap', 'name', 'fullname'], true) && ! isset($map['name'])) {
                $map['name'] = $idx;
            } elseif (in_array($clean, ['email', 'surel', 'mail', 'alamatemail'], true) && ! isset($map['email'])) {
                $map['email'] = $idx;
            } elseif (in_array($clean, ['nomorinduk', 'nim', 'nip', 'nimnip', 'noinduk', 'identitynumber'], true) && ! isset($map['nomor_induk'])) {
                $map['nomor_induk'] = $idx;
            } elseif (in_array($clean, ['role', 'peran', 'jabatan', 'tipeakun'], true) && ! isset($map['role'])) {
                $map['role'] = $idx;
            } elseif (in_array($clean, ['programstudi', 'prodi', 'jurusan', 'studyprogram'], true) && ! isset($map['program_studi'])) {
                $map['program_studi'] = $idx;
            } elseif (in_array($clean, ['password', 'katasandi', 'pass'], true) && ! isset($map['password'])) {
                $map['password'] = $idx;
            }
        }

        return $map;
    }

    /**
     * Extract a value from a row using the column map.
     *
     * @param  array<int, string>  $row
     * @param  array<string, int>  $map
     */
    protected function extractValue(array $row, array $map, string $field): ?string
    {
        if (isset($map[$field]) && isset($row[$map[$field]])) {
            $val = trim((string) $row[$map[$field]]);

            return $val !== '' ? $val : null;
        }

        return null;
    }

    /**
     * Normalize role string to valid role key.
     */
    public function normalizeRole(?string $role): string
    {
        if (empty($role)) {
            return 'mahasiswa';
        }

        $clean = strtolower(trim($role));
        $clean = str_replace([' ', '-', '_'], '', $clean);

        if (str_contains($clean, 'admin')) {
            return 'admin';
        }
        if (str_contains($clean, 'dosen') || str_contains($clean, 'pengajar')) {
            return 'dosen';
        }
        if (str_contains($clean, 'dewan') || str_contains($clean, 'skripsi')) {
            return 'dewan_skripsi';
        }
        if (str_contains($clean, 'gkm') || str_contains($clean, 'mutu')) {
            return 'gkm';
        }

        return 'mahasiswa';
    }
}
