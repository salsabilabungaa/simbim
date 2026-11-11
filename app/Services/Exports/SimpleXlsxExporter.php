<?php

namespace App\Services\Exports;

class SimpleXlsxExporter
{
    /**
     * Hasilkan file binary .xlsx multi-sheet dari daftar definisi sheet.
     *
     * @param  array<int, array{
     *     name: string,
     *     title: string,
     *     subtitle: string,
     *     headers: array<int, string>,
     *     widths?: array<int, int|float>,
     *     rows: array<int, array<int, mixed>>
     * }>  $sheets
     */
    public function generate(array $sheets): string
    {
        if (empty($sheets)) {
            $sheets = [[
                'name' => 'Data',
                'title' => 'Laporan',
                'subtitle' => '',
                'headers' => ['Informasi'],
                'rows' => [['Tidak ada data tersedia.']],
            ]];
        }

        $entries = [
            '[Content_Types].xml' => $this->buildContentTypesXml(count($sheets)),
            '_rels/.rels' => $this->buildRootRelsXml(),
            'xl/workbook.xml' => $this->buildWorkbookXml($sheets),
            'xl/_rels/workbook.xml.rels' => $this->buildWorkbookRelsXml(count($sheets)),
            'xl/styles.xml' => $this->buildStylesXml(),
        ];

        foreach (array_values($sheets) as $index => $sheet) {
            $sheetNumber = $index + 1;
            $entries["xl/worksheets/sheet{$sheetNumber}.xml"] = $this->buildWorksheetXml($sheet);
        }

        return $this->buildZipArchive($entries);
    }

    /**
     * Konversi indeks kolom 0-based ke huruf kolom Excel (0 => A, 26 => AA).
     */
    protected function columnLetter(int $index): string
    {
        $letter = '';
        $num = $index + 1;

        while ($num > 0) {
            $mod = ($num - 1) % 26;
            $letter = chr(65 + $mod).$letter;
            $num = intdiv($num - 1, 26);
        }

        return $letter;
    }

    protected function sanitizeSheetName(string $name, int $fallbackIndex): string
    {
        $clean = preg_replace('/[:\\\\\\/\\?\\*\\[\\]]/', ' ', $name) ?? '';
        $clean = trim(preg_replace('/\s+/', ' ', $clean) ?? '');

        if ($clean === '') {
            $clean = 'Sheet'.$fallbackIndex;
        }

        return mb_substr($clean, 0, 31);
    }

    protected function escapeXml(string $value): string
    {
        $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? $value;

        return htmlspecialchars($clean, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    protected function buildContentTypesXml(int $sheetCount): string
    {
        $overrides = '';
        for ($i = 1; $i <= $sheetCount; $i++) {
            $overrides .= '<Override PartName="/xl/worksheets/sheet'.$i.'.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .$overrides
            .'</Types>';
    }

    protected function buildRootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>';
    }

    /**
     * @param  array<int, array<string, mixed>>  $sheets
     */
    protected function buildWorkbookXml(array $sheets): string
    {
        $sheetTags = '';
        foreach (array_values($sheets) as $index => $sheet) {
            $sheetNumber = $index + 1;
            $sheetName = $this->escapeXml($this->sanitizeSheetName((string) ($sheet['name'] ?? ''), $sheetNumber));
            $sheetTags .= '<sheet name="'.$sheetName.'" sheetId="'.$sheetNumber.'" r:id="rId'.$sheetNumber.'"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets>'.$sheetTags.'</sheets>'
            .'</workbook>';
    }

    protected function buildWorkbookRelsXml(int $sheetCount): string
    {
        $rels = '';
        for ($i = 1; $i <= $sheetCount; $i++) {
            $rels .= '<Relationship Id="rId'.$i.'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet'.$i.'.xml"/>';
        }
        $stylesId = $sheetCount + 1;
        $rels .= '<Relationship Id="rId'.$stylesId.'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .$rels
            .'</Relationships>';
    }

    protected function buildStylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<fonts count="7">'
            .'<font><sz val="10"/><color rgb="FF1E293B"/><name val="Calibri"/></font>'
            .'<font><b/><sz val="13"/><color rgb="FF0F172A"/><name val="Calibri"/></font>'
            .'<font><i/><sz val="9.5"/><color rgb="FF475569"/><name val="Calibri"/></font>'
            .'<font><b/><sz val="10"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>'
            .'<font><b/><sz val="10"/><color rgb="FF166534"/><name val="Calibri"/></font>'
            .'<font><b/><sz val="10"/><color rgb="FF92400E"/><name val="Calibri"/></font>'
            .'<font><b/><sz val="10"/><color rgb="FF991B1B"/><name val="Calibri"/></font>'
            .'</fonts>'
            .'<fills count="7">'
            .'<fill><patternFill patternType="none"/></fill>'
            .'<fill><patternFill patternType="gray125"/></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FF1E3A8A"/></patternFill></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FFF8FAFC"/></patternFill></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FFDCFCE7"/></patternFill></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FFFEF3C7"/></patternFill></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FFFEE2E2"/></patternFill></fill>'
            .'</fills>'
            .'<borders count="2">'
            .'<border><left/><right/><top/><bottom/><diagonal/></border>'
            .'<border>'
            .'<left style="thin"><color rgb="FFCBD5E1"/></left>'
            .'<right style="thin"><color rgb="FFCBD5E1"/></right>'
            .'<top style="thin"><color rgb="FFCBD5E1"/></top>'
            .'<bottom style="thin"><color rgb="FFCBD5E1"/></bottom>'
            .'<diagonal/>'
            .'</border>'
            .'</borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="9">'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            .'<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment vertical="center"/></xf>'
            .'<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment vertical="center"/></xf>'
            .'<xf numFmtId="0" fontId="3" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="top" wrapText="1"/></xf>'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="top" wrapText="1"/></xf>'
            .'<xf numFmtId="0" fontId="4" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="top" wrapText="1"/></xf>'
            .'<xf numFmtId="0" fontId="5" fillId="5" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="top" wrapText="1"/></xf>'
            .'<xf numFmtId="0" fontId="6" fillId="6" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="top" wrapText="1"/></xf>'
            .'</cellXfs>'
            .'</styleSheet>';
    }

    /**
     * @param  array{
     *     name: string,
     *     title?: string,
     *     subtitle?: string,
     *     headers: array<int, string>,
     *     widths?: array<int, int|float>,
     *     rows: array<int, array<int, mixed>>
     * }  $sheet
     */
    protected function buildWorksheetXml(array $sheet): string
    {
        $headers = $sheet['headers'] ?? [];
        $widths = $sheet['widths'] ?? [];
        $rows = $sheet['rows'] ?? [];
        $title = (string) ($sheet['title'] ?? '');
        $subtitle = (string) ($sheet['subtitle'] ?? '');

        $colsXml = '';
        if (! empty($headers)) {
            $colsXml .= '<cols>';
            foreach (array_values($headers) as $colIndex => $header) {
                $colNum = $colIndex + 1;
                $width = $widths[$colIndex] ?? max(14, min(42, mb_strlen((string) $header) + 6));
                $colsXml .= '<col min="'.$colNum.'" max="'.$colNum.'" width="'.$width.'" customWidth="1"/>';
            }
            $colsXml .= '</cols>';
        }

        $sheetRowsXml = '';
        $rowIndex = 1;

        if ($title !== '') {
            $sheetRowsXml .= '<row r="'.$rowIndex.'" ht="24" customHeight="1">'
                .$this->renderCellXml('A'.$rowIndex, $title, 1, 's')
                .'</row>';
            $rowIndex++;
        }

        if ($subtitle !== '') {
            $sheetRowsXml .= '<row r="'.$rowIndex.'" ht="18" customHeight="1">'
                .$this->renderCellXml('A'.$rowIndex, $subtitle, 2, 's')
                .'</row>';
            $rowIndex++;
        }

        if ($title !== '' || $subtitle !== '') {
            $rowIndex++;
        }

        if (! empty($headers)) {
            $headerCells = '';
            foreach (array_values($headers) as $colIndex => $headerText) {
                $coord = $this->columnLetter($colIndex).$rowIndex;
                $headerCells .= $this->renderCellXml($coord, (string) $headerText, 3, 's');
            }
            $sheetRowsXml .= '<row r="'.$rowIndex.'" ht="26" customHeight="1">'.$headerCells.'</row>';
            $rowIndex++;
        }

        foreach ($rows as $row) {
            $rowCells = '';
            foreach (array_values($row) as $colIndex => $cell) {
                $coord = $this->columnLetter($colIndex).$rowIndex;
                if (is_array($cell) && array_key_exists('value', $cell)) {
                    $value = $cell['value'];
                    $style = (int) ($cell['style'] ?? 4);
                    $type = (string) ($cell['type'] ?? (is_int($value) || is_float($value) ? 'n' : 's'));
                } else {
                    $value = $cell;
                    $type = (is_int($value) || is_float($value)) ? 'n' : 's';
                    $style = $type === 'n' ? 5 : 4;
                }
                $rowCells .= $this->renderCellXml($coord, $value, $style, $type);
            }
            $sheetRowsXml .= '<row r="'.$rowIndex.'">'.$rowCells.'</row>';
            $rowIndex++;
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<sheetViews><sheetView workbookViewId="0"/></sheetViews>'
            .$colsXml
            .'<sheetData>'.$sheetRowsXml.'</sheetData>'
            .'</worksheet>';
    }

    protected function renderCellXml(string $coord, mixed $value, int $styleIndex, string $type): string
    {
        if ($type === 'n' && is_numeric($value)) {
            return '<c r="'.$coord.'" s="'.$styleIndex.'"><v>'.$value.'</v></c>';
        }

        $text = (string) ($value ?? '');
        $escaped = $this->escapeXml($text);
        $preserve = (str_starts_with($text, ' ') || str_ends_with($text, ' ') || str_contains($text, "\n"))
            ? ' xml:space="preserve"'
            : '';

        return '<c r="'.$coord.'" t="inlineStr" s="'.$styleIndex.'"><is><t'.$preserve.'>'.$escaped.'</t></is></c>';
    }

    /**
     * Kemas berkas XML OpenXML ke dalam arsip PKZIP standar tanpa membutuhkan ext-zip.
     *
     * @param  array<string, string>  $files
     */
    protected function buildZipArchive(array $files): string
    {
        $localData = '';
        $centralDirectory = '';
        $offset = 0;
        $entryCount = 0;

        $dosTime = $this->currentDosTime();
        $dosDate = $this->currentDosDate();

        foreach ($files as $filename => $content) {
            $uncompSize = strlen($content);
            $crc = crc32($content) & 0xFFFFFFFF;
            $compressed = gzdeflate($content, 6);
            if ($compressed === false) {
                $compressed = $content;
                $method = 0;
            } else {
                $method = 8;
            }
            $compSize = strlen($compressed);
            $nameLen = strlen($filename);

            // Local file header (PK\x03\x04)
            $localHeader = pack(
                'VvvvvvVVVvv',
                0x04034B50,
                20,
                0,
                $method,
                $dosTime,
                $dosDate,
                $crc,
                $compSize,
                $uncompSize,
                $nameLen,
                0
            ).$filename;

            $localData .= $localHeader.$compressed;

            // Central directory header (PK\x01\x02)
            $centralDirectory .= pack(
                'VvvvvvvVVVvvvvvVV',
                0x02014B50,
                20,
                20,
                0,
                $method,
                $dosTime,
                $dosDate,
                $crc,
                $compSize,
                $uncompSize,
                $nameLen,
                0,
                0,
                0,
                0,
                0,
                $offset
            ).$filename;

            $offset += strlen($localHeader) + $compSize;
            $entryCount++;
        }

        // End of central directory record (PK\x05\x06)
        $eocd = pack(
            'VvvvvVVv',
            0x06054B50,
            0,
            0,
            $entryCount,
            $entryCount,
            strlen($centralDirectory),
            $offset,
            0
        );

        return $localData.$centralDirectory.$eocd;
    }

    protected function currentDosTime(): int
    {
        $now = getdate();

        return (($now['hours'] & 0x1F) << 11)
            | (($now['minutes'] & 0x3F) << 5)
            | (($now['seconds'] >> 1) & 0x1F);
    }

    protected function currentDosDate(): int
    {
        $now = getdate();
        $year = max(1980, $now['year']) - 1980;

        return (($year & 0x7F) << 9)
            | (($now['mon'] & 0x0F) << 5)
            | ($now['mday'] & 0x1F);
    }
}
