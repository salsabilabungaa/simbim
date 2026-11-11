<?php

namespace App\Services\Exports;

class SimplePdfExporter
{
    protected float $pageWidth = 841.89;

    protected float $pageHeight = 595.28;

    protected float $marginLeft = 34.0;

    protected float $marginRight = 34.0;

    protected float $contentTop = 74.0;

    protected float $contentBottomLimit = 552.0;

    /** @var array<int, string> */
    protected array $pages = [];

    protected int $currentPage = -1;

    protected float $cursorY = 74.0;

    protected string $reportTitle = '';

    protected string $reportSubtitle = '';

    protected string $printedAt = '';

    /**
     * Hasilkan dokumen PDF lengkap laporan akreditasi GKM (A4 Landscape).
     *
     * @param  array{
     *     title: string,
     *     subtitle: string,
     *     printed_at: string,
     *     petugas_nama: string,
     *     petugas_nip: string,
     *     metadata: array<int, array{label: string, value: string}>,
     *     kpis: array<int, array{label: string, value: string, sublabel: string}>,
     *     prodi_headers: array<int, string>,
     *     prodi_widths: array<int, float>,
     *     prodi_rows: array<int, array<int, string>>,
     *     perwalian_headers: array<int, string>,
     *     perwalian_widths: array<int, float>,
     *     perwalian_rows: array<int, array<int, string>>,
     *     konsultasi_headers: array<int, string>,
     *     konsultasi_widths: array<int, float>,
     *     konsultasi_rows: array<int, array<int, string>>
     * }  $payload
     */
    public function generateAccreditationReport(array $payload): string
    {
        $this->pages = [];
        $this->currentPage = -1;
        $this->reportTitle = $payload['title'];
        $this->reportSubtitle = $payload['subtitle'];
        $this->printedAt = $payload['printed_at'];

        $this->startNewPage();

        // 1. Metadata Akreditasi
        $this->drawMetadataGrid($payload['metadata']);

        // 2. 4 Kartu Ringkasan Statistik
        $this->drawKpiCards($payload['kpis']);

        // 3. Tabel 1: Rekap Per Angkatan / Unit
        $this->drawSectionTitle(
            $payload['prodi_section_title'] ?? 'I. Rekapitulasi Status Bimbingan Akademik (KRS/IRS) per Angkatan — Prodi PSTI UPI',
            $payload['prodi_section_subtitle'] ?? 'Ringkasan kepatuhan penyelesaian bimbingan perwalian akademik Prodi Pendidikan Sistem dan Teknologi Informasi (PSTI) UPI.'
        );
        $this->drawTable(
            $payload['prodi_headers'],
            $payload['prodi_widths'],
            $payload['prodi_rows'],
            ['center', 'left', 'center', 'center', 'center', 'center', 'center', 'center']
        );

        // 4. Tabel 2: Daftar Status Penyelesaian Mahasiswa
        $this->ensureSpace(95.0);
        $this->cursorY += 8.0;
        $this->drawSectionTitle(
            'II. Daftar Mahasiswa Status Penyelesaian Bimbingan Akademik (Pengisian KRS/IRS)',
            'Pemetaan mahasiswa yang sudah maupun belum menyelesaikan bimbingan perwalian bersama dosen wali.'
        );
        $this->drawTable(
            $payload['perwalian_headers'],
            $payload['perwalian_widths'],
            $payload['perwalian_rows'],
            ['center', 'center', 'left', 'left', 'center', 'left', 'center', 'center']
        );

        // 5. Tabel 3: Riwayat & Log Konsultasi (View Only)
        $this->ensureSpace(95.0);
        $this->cursorY += 8.0;
        $this->drawSectionTitle(
            'III. Arsip Riwayat & Log Konsultasi Bimbingan Akademik (View Only)',
            'Jejak rekam komunikasi (tanggal, topik, catatan mahasiswa & arahan dosen wali) terkunci untuk audit akreditasi.'
        );
        $this->drawTable(
            $payload['konsultasi_headers'],
            $payload['konsultasi_widths'],
            $payload['konsultasi_rows'],
            ['center', 'center', 'left', 'left', 'left', 'left', 'center']
        );

        // 6. Blok Tanda Tangan Pengesahan GKM
        $this->drawSignatureBlock(
            $payload['petugas_nama'],
            $payload['petugas_nip'],
            $payload['printed_at']
        );

        return $this->compilePdf();
    }

    protected function usableWidth(): float
    {
        return $this->pageWidth - $this->marginLeft - $this->marginRight;
    }

    protected function toPdfY(float $topY): float
    {
        return $this->pageHeight - $topY;
    }

    protected function sanitizeText(string $text): string
    {
        $replacements = [
            '—' => '-',
            '–' => '-',
            '•' => '*',
            '’' => "'",
            '‘' => "'",
            '“' => '"',
            '”' => '"',
            '…' => '...',
            "\r\n" => ' ',
            "\n" => ' ',
            "\r" => ' ',
            "\t" => ' ',
        ];

        $normalized = strtr($text, $replacements);
        $converted = @iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $normalized);
        if ($converted === false) {
            $converted = preg_replace('/[^\x20-\x7E]/', '', $normalized) ?? '';
        }

        return trim(preg_replace('/\s+/', ' ', $converted) ?? '');
    }

    protected function escapePdfString(string $text): string
    {
        $sanitized = $this->sanitizeText($text);

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $sanitized);
    }

    protected function estimateTextWidth(string $text, float $fontSize, bool $bold = false): float
    {
        $clean = $this->sanitizeText($text);
        $len = strlen($clean);
        if ($len === 0) {
            return 0.0;
        }

        $sum = 0.0;
        for ($i = 0; $i < $len; $i++) {
            $ch = $clean[$i];
            if (strpos('ijlI.,:;\'!| ', $ch) !== false) {
                $sum += 0.28;
            } elseif (strpos('frt()-[]/\\"', $ch) !== false) {
                $sum += 0.36;
            } elseif (strpos('MWmw@%&', $ch) !== false) {
                $sum += 0.86;
            } elseif ($ch >= 'A' && $ch <= 'Z') {
                $sum += 0.67;
            } else {
                $sum += 0.54;
            }
        }

        return $sum * $fontSize * ($bold ? 1.07 : 1.0);
    }

    /**
     * @return array<int, string>
     */
    protected function wrapText(string $text, float $maxWidth, float $fontSize, bool $bold = false): array
    {
        $sanitized = $this->sanitizeText($text);
        if ($sanitized === '') {
            return ['-'];
        }

        $words = explode(' ', $sanitized);
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            $candidate = $currentLine === '' ? $word : $currentLine.' '.$word;
            if ($this->estimateTextWidth($candidate, $fontSize, $bold) <= $maxWidth) {
                $currentLine = $candidate;

                continue;
            }

            if ($currentLine !== '') {
                $lines[] = $currentLine;
                $currentLine = '';
            }

            // Potong kata yang lebih panjang dari lebar kolom
            if ($this->estimateTextWidth($word, $fontSize, $bold) > $maxWidth) {
                $chunk = '';
                $chars = str_split($word);
                foreach ($chars as $char) {
                    if ($this->estimateTextWidth($chunk.$char, $fontSize, $bold) > $maxWidth && $chunk !== '') {
                        $lines[] = $chunk;
                        $chunk = $char;
                    } else {
                        $chunk .= $char;
                    }
                }
                $currentLine = $chunk;
            } else {
                $currentLine = $word;
            }
        }

        if ($currentLine !== '') {
            $lines[] = $currentLine;
        }

        return empty($lines) ? ['-'] : $lines;
    }

    protected function appendStream(string $commands): void
    {
        $this->pages[$this->currentPage] .= $commands."\n";
    }

    protected function setFillRgb(float $r, float $g, float $b): void
    {
        $this->appendStream(sprintf('%.3f %.3f %.3f rg', $r / 255, $g / 255, $b / 255));
    }

    protected function setStrokeRgb(float $r, float $g, float $b): void
    {
        $this->appendStream(sprintf('%.3f %.3f %.3f RG', $r / 255, $g / 255, $b / 255));
    }

    protected function drawRect(float $x, float $topY, float $width, float $height, bool $fill = false, bool $stroke = false, float $lineWidth = 0.6): void
    {
        $pdfY = $this->toPdfY($topY + $height);
        $op = match (true) {
            $fill && $stroke => 'B',
            $fill => 'f',
            default => 'S',
        };
        $this->appendStream(sprintf('%.2f w %.2f %.2f %.2f %.2f re %s', $lineWidth, $x, $pdfY, $width, $height, $op));
    }

    protected function drawLine(float $x1, float $topY1, float $x2, float $topY2, float $lineWidth = 0.8): void
    {
        $this->appendStream(sprintf(
            '%.2f w %.2f %.2f m %.2f %.2f l S',
            $lineWidth,
            $x1,
            $this->toPdfY($topY1),
            $x2,
            $this->toPdfY($topY2)
        ));
    }

    protected function drawText(float $x, float $topBaselineY, string $text, float $fontSize = 9.0, string $fontKey = 'F1'): void
    {
        $escaped = $this->escapePdfString($text);
        $this->appendStream(sprintf(
            'BT /%s %.2f Tf %.2f %.2f Td (%s) Tj ET',
            $fontKey,
            $fontSize,
            $x,
            $this->toPdfY($topBaselineY),
            $escaped
        ));
    }

    protected function startNewPage(): void
    {
        $this->pages[] = '';
        $this->currentPage = count($this->pages) - 1;

        // Top Institutional Accent Banner
        $this->setFillRgb(30, 58, 138);
        $this->drawRect($this->marginLeft, 22.0, $this->usableWidth(), 3.0, true, false);

        $this->setFillRgb(30, 58, 138);
        $this->drawText($this->marginLeft, 39.0, $this->reportTitle, 11.5, 'F2');

        $this->setFillRgb(71, 85, 105);
        $this->drawText($this->marginLeft, 52.0, $this->reportSubtitle, 8.2, 'F1');

        $tag = 'ARSIP AKREDITASI GKM (VIEW-ONLY)';
        $tagW = $this->estimateTextWidth($tag, 7.5, true) + 12.0;
        $tagX = $this->marginLeft + $this->usableWidth() - $tagW;
        $this->setFillRgb(224, 242, 254);
        $this->setStrokeRgb(14, 116, 144);
        $this->drawRect($tagX, 29.0, $tagW, 15.0, true, true, 0.6);
        $this->setFillRgb(14, 116, 144);
        $this->drawText($tagX + 6.0, 39.2, $tag, 7.5, 'F2');

        $this->setStrokeRgb(203, 213, 225);
        $this->drawLine($this->marginLeft, 60.0, $this->marginLeft + $this->usableWidth(), 60.0, 0.7);

        $this->cursorY = $this->contentTop;
    }

    protected function ensureSpace(float $requiredHeight): void
    {
        if ($this->cursorY + $requiredHeight > $this->contentBottomLimit) {
            $this->startNewPage();
        }
    }

    /**
     * @param  array<int, array{label: string, value: string}>  $items
     */
    protected function drawMetadataGrid(array $items): void
    {
        $boxHeight = 42.0;
        $this->ensureSpace($boxHeight + 8.0);

        $this->setFillRgb(248, 250, 252);
        $this->setStrokeRgb(203, 213, 225);
        $this->drawRect($this->marginLeft, $this->cursorY, $this->usableWidth(), $boxHeight, true, true, 0.6);

        $cols = min(4, max(1, count($items)));
        $colW = $this->usableWidth() / $cols;

        foreach (array_values($items) as $idx => $item) {
            $col = $idx % $cols;
            $x = $this->marginLeft + ($col * $colW) + 10.0;
            $this->setFillRgb(100, 116, 139);
            $this->drawText($x, $this->cursorY + 14.0, strtoupper($item['label']), 7.0, 'F2');

            $this->setFillRgb(15, 23, 42);
            $lines = $this->wrapText($item['value'], $colW - 18.0, 8.8, true);
            $this->drawText($x, $this->cursorY + 28.0, $lines[0], 8.8, 'F2');
        }

        $this->cursorY += $boxHeight + 10.0;
    }

    /**
     * @param  array<int, array{label: string, value: string, sublabel: string}>  $cards
     */
    protected function drawKpiCards(array $cards): void
    {
        $cardHeight = 46.0;
        $this->ensureSpace($cardHeight + 12.0);

        $count = max(1, count($cards));
        $gap = 10.0;
        $cardW = ($this->usableWidth() - ($gap * ($count - 1))) / $count;

        foreach (array_values($cards) as $idx => $card) {
            $x = $this->marginLeft + ($idx * ($cardW + $gap));

            $this->setFillRgb(255, 255, 255);
            $this->setStrokeRgb(203, 213, 225);
            $this->drawRect($x, $this->cursorY, $cardW, $cardHeight, true, true, 0.7);

            // Strip warna di sisi kiri kartu
            $accentColors = [
                [30, 58, 138],
                [22, 163, 74],
                [217, 119, 6],
                [14, 116, 144],
            ];
            $rgb = $accentColors[$idx % count($accentColors)];
            $this->setFillRgb($rgb[0], $rgb[1], $rgb[2]);
            $this->drawRect($x, $this->cursorY, 3.5, $cardHeight, true, false);

            $this->setFillRgb(100, 116, 139);
            $this->drawText($x + 10.0, $this->cursorY + 13.0, strtoupper($card['label']), 6.8, 'F2');

            $this->setFillRgb(15, 23, 42);
            $this->drawText($x + 10.0, $this->cursorY + 28.0, $card['value'], 12.5, 'F2');

            $this->setFillRgb(71, 85, 105);
            $subLines = $this->wrapText($card['sublabel'], $cardW - 16.0, 7.2, false);
            $this->drawText($x + 10.0, $this->cursorY + 39.5, $subLines[0], 7.2, 'F1');
        }

        $this->cursorY += $cardHeight + 14.0;
    }

    protected function drawSectionTitle(string $title, string $subtitle = ''): void
    {
        $needed = $subtitle !== '' ? 32.0 : 20.0;
        $this->ensureSpace($needed + 28.0);

        $this->setFillRgb(15, 23, 42);
        $this->drawText($this->marginLeft, $this->cursorY + 10.0, $title, 10.0, 'F2');

        if ($subtitle !== '') {
            $this->setFillRgb(100, 116, 139);
            $this->drawText($this->marginLeft, $this->cursorY + 21.0, $subtitle, 7.8, 'F3');
            $this->cursorY += 27.0;
        } else {
            $this->cursorY += 16.0;
        }
    }

    /**
     * @param  array<int, string>  $headers
     * @param  array<int, float>  $widths
     * @param  array<int, array<int, string>>  $rows
     * @param  array<int, string>  $alignments
     */
    protected function drawTable(array $headers, array $widths, array $rows, array $alignments = []): void
    {
        $this->drawTableHeaderRow($headers, $widths);

        if (empty($rows)) {
            $emptyHeight = 24.0;
            if ($this->cursorY + $emptyHeight > $this->contentBottomLimit) {
                $this->startNewPage();
                $this->drawTableHeaderRow($headers, $widths);
            }
            $this->setFillRgb(248, 250, 252);
            $this->setStrokeRgb(203, 213, 225);
            $this->drawRect($this->marginLeft, $this->cursorY, array_sum($widths), $emptyHeight, true, true, 0.5);
            $this->setFillRgb(100, 116, 139);
            $this->drawText($this->marginLeft + 10.0, $this->cursorY + 15.0, 'Tidak ada data yang tercatat pada filter periode/program studi ini.', 8.2, 'F3');
            $this->cursorY += $emptyHeight + 6.0;

            return;
        }

        $fontSize = 7.6;
        $lineHeight = 9.8;
        $paddingX = 5.0;
        $paddingY = 4.5;

        foreach (array_values($rows) as $rowIdx => $row) {
            $wrappedCells = [];
            $maxLines = 1;

            foreach (array_values($widths) as $colIdx => $colWidth) {
                $cellText = (string) ($row[$colIdx] ?? '-');
                $lines = $this->wrapText($cellText, max(16.0, $colWidth - ($paddingX * 2)), $fontSize, false);
                $wrappedCells[$colIdx] = $lines;
                $maxLines = max($maxLines, count($lines));
            }

            $rowHeight = ($maxLines * $lineHeight) + ($paddingY * 2);

            if ($this->cursorY + $rowHeight > $this->contentBottomLimit) {
                $this->startNewPage();
                $this->drawTableHeaderRow($headers, $widths);
            }

            $isOdd = ($rowIdx % 2) === 1;
            $x = $this->marginLeft;

            foreach (array_values($widths) as $colIdx => $colWidth) {
                if ($isOdd) {
                    $this->setFillRgb(248, 250, 252);
                } else {
                    $this->setFillRgb(255, 255, 255);
                }
                $this->setStrokeRgb(203, 213, 225);
                $this->drawRect($x, $this->cursorY, $colWidth, $rowHeight, true, true, 0.5);

                $this->setFillRgb(30, 41, 59);
                $align = $alignments[$colIdx] ?? 'left';
                $cellLines = $wrappedCells[$colIdx];

                foreach ($cellLines as $lineIdx => $lineText) {
                    $baselineY = $this->cursorY + $paddingY + 7.2 + ($lineIdx * $lineHeight);
                    $textX = $x + $paddingX;
                    if ($align === 'center') {
                        $tw = $this->estimateTextWidth($lineText, $fontSize, false);
                        $textX = $x + max($paddingX, ($colWidth - $tw) / 2);
                    }
                    $this->drawText($textX, $baselineY, $lineText, $fontSize, 'F1');
                }

                $x += $colWidth;
            }

            $this->cursorY += $rowHeight;
        }

        $this->cursorY += 8.0;
    }

    /**
     * @param  array<int, string>  $headers
     * @param  array<int, float>  $widths
     */
    protected function drawTableHeaderRow(array $headers, array $widths): void
    {
        $fontSize = 7.6;
        $lineHeight = 9.5;
        $paddingX = 5.0;
        $paddingY = 5.0;

        $wrappedHeaders = [];
        $maxLines = 1;

        foreach (array_values($headers) as $colIdx => $headerText) {
            $colWidth = $widths[$colIdx] ?? 60.0;
            $lines = $this->wrapText($headerText, max(16.0, $colWidth - ($paddingX * 2)), $fontSize, true);
            $wrappedHeaders[$colIdx] = $lines;
            $maxLines = max($maxLines, count($lines));
        }

        $headerHeight = ($maxLines * $lineHeight) + ($paddingY * 2);
        $this->ensureSpace($headerHeight + 22.0);

        $x = $this->marginLeft;
        foreach (array_values($headers) as $colIdx => $headerText) {
            $colWidth = $widths[$colIdx] ?? 60.0;
            $this->setFillRgb(30, 58, 138);
            $this->setStrokeRgb(30, 58, 138);
            $this->drawRect($x, $this->cursorY, $colWidth, $headerHeight, true, true, 0.5);

            $this->setFillRgb(255, 255, 255);
            foreach ($wrappedHeaders[$colIdx] as $lineIdx => $lineText) {
                $baselineY = $this->cursorY + $paddingY + 7.2 + ($lineIdx * $lineHeight);
                $tw = $this->estimateTextWidth($lineText, $fontSize, true);
                $textX = $x + max($paddingX, ($colWidth - $tw) / 2);
                $this->drawText($textX, $baselineY, $lineText, $fontSize, 'F2');
            }

            $x += $colWidth;
        }

        $this->cursorY += $headerHeight;
    }

    protected function drawSignatureBlock(string $namaGkm, string $nipGkm, string $tanggalCetak): void
    {
        $this->ensureSpace(95.0);
        $this->cursorY += 12.0;

        $sigX = $this->marginLeft + $this->usableWidth() - 230.0;
        $this->setFillRgb(51, 65, 85);
        $this->drawText($sigX, $this->cursorY + 10.0, 'Dicetak & Disahkan Secara Digital: '.$tanggalCetak, 8.2, 'F1');
        $this->drawText($sigX, $this->cursorY + 22.0, 'Ketua / Perwakilan Gugus Kendali Mutu (GKM)', 8.2, 'F2');

        $this->setStrokeRgb(148, 163, 184);
        $this->drawLine($sigX, $this->cursorY + 62.0, $sigX + 215.0, $this->cursorY + 62.0, 0.7);

        $this->setFillRgb(15, 23, 42);
        $this->drawText($sigX, $this->cursorY + 74.0, $namaGkm, 8.8, 'F2');
        $this->setFillRgb(71, 85, 105);
        $this->drawText($sigX, $this->cursorY + 85.0, 'NIP/ID: '.$nipGkm, 7.8, 'F1');
    }

    /**
     * Susun seluruh objek PDF 1.4, xref table, dan footer nomor halaman.
     */
    protected function compilePdf(): string
    {
        $totalPages = count($this->pages);

        foreach ($this->pages as $idx => $stream) {
            $this->currentPage = $idx;
            $pageNum = $idx + 1;

            $this->setStrokeRgb(203, 213, 225);
            $this->drawLine($this->marginLeft, 566.0, $this->marginLeft + $this->usableWidth(), 566.0, 0.6);

            $this->setFillRgb(100, 116, 139);
            $this->drawText(
                $this->marginLeft,
                578.0,
                'SIMBIM — Bukti Fisik Penilaian Akreditasi | Data Konsultasi Terkunci (View-Only) | Dicetak: '.$this->printedAt,
                7.4,
                'F1'
            );

            $pageLabel = "Halaman {$pageNum} dari {$totalPages}";
            $pw = $this->estimateTextWidth($pageLabel, 7.4, true);
            $this->drawText(
                $this->marginLeft + $this->usableWidth() - $pw,
                578.0,
                $pageLabel,
                7.4,
                'F2'
            );
        }

        // Bangun daftar objek PDF:
        // 1: Catalog, 2: Pages, 3: F1 Helvetica, 4: F2 Helvetica-Bold, 5: F3 Helvetica-Oblique,
        // lalu untuk tiap halaman: Page Obj + Content Stream Obj.
        $objects = [];
        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';

        $pageObjectIds = [];
        $nextObjId = 6;
        for ($i = 0; $i < $totalPages; $i++) {
            $pageObjectIds[] = $nextObjId;
            $nextObjId += 2;
        }

        $kidsRef = implode(' ', array_map(fn (int $id) => "{$id} 0 R", $pageObjectIds));
        $objects[2] = "<< /Type /Pages /Kids [ {$kidsRef} ] /Count {$totalPages} >>";
        $objects[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $objects[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';
        $objects[5] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Oblique /Encoding /WinAnsiEncoding >>';

        foreach ($this->pages as $idx => $stream) {
            $pageObjId = $pageObjectIds[$idx];
            $contentObjId = $pageObjId + 1;

            $objects[$pageObjId] = sprintf(
                '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %.2f %.2f] /Resources << /Font << /F1 3 0 R /F2 4 0 R /F3 5 0 R >> >> /Contents %d 0 R >>',
                $this->pageWidth,
                $this->pageHeight,
                $contentObjId
            );

            $objects[$contentObjId] = '<< /Length '.strlen($stream)." >>\nstream\n".$stream.'endstream';
        }

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [0 => 0];

        ksort($objects);
        foreach ($objects as $id => $body) {
            $offsets[$id] = strlen($pdf);
            $pdf .= "{$id} 0 obj\n{$body}\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $maxId = max(array_keys($objects));
        $pdf .= "xref\n0 ".($maxId + 1)."\n";
        $pdf .= "0000000000 65535 f \n";

        for ($id = 1; $id <= $maxId; $id++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$id] ?? 0);
        }

        $pdf .= "trailer\n<< /Size ".($maxId + 1)." /Root 1 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF";

        return $pdf;
    }
}
