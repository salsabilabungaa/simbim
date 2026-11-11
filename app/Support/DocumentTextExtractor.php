<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;

class DocumentTextExtractor
{
    public function __construct(
        protected SimpleZipReader $zipReader
    ) {}

    /**
     * Ekstrak teks naskah yang dapat dibaca dari berkas unggahan (TXT, DOCX, PDF, DOC).
     */
    public function extract(UploadedFile|string $source, ?string $extension = null): ?string
    {
        if ($source instanceof UploadedFile) {
            $path = $source->getRealPath();
            $ext = strtolower($extension ?? $source->getClientOriginalExtension());
        } else {
            $path = $source;
            $ext = strtolower($extension ?? pathinfo($source, PATHINFO_EXTENSION));
        }

        if (! $path || ! is_file($path)) {
            return null;
        }

        $text = match ($ext) {
            'txt', 'md' => $this->extractTxt($path),
            'docx' => $this->extractDocx($path),
            'pdf' => $this->extractPdf($path),
            'doc' => $this->extractLegacyDoc($path),
            default => null,
        };

        if ($text === null) {
            return null;
        }

        $normalized = $this->normalizeParagraphs($text);

        return $normalized !== '' ? $normalized : null;
    }

    protected function extractTxt(string $path): ?string
    {
        $raw = file_get_contents($path);
        if (! is_string($raw) || $raw === '') {
            return null;
        }

        if (str_starts_with($raw, "\xFF\xFE")) {
            $raw = mb_convert_encoding(substr($raw, 2), 'UTF-8', 'UTF-16LE');
        } elseif (str_starts_with($raw, "\xFE\xFF")) {
            $raw = mb_convert_encoding(substr($raw, 2), 'UTF-8', 'UTF-16BE');
        } elseif (str_starts_with($raw, "\xEF\xBB\xBF")) {
            $raw = substr($raw, 3);
        } elseif (! mb_check_encoding($raw, 'UTF-8')) {
            $raw = mb_convert_encoding($raw, 'UTF-8', 'Windows-1252');
        }

        return is_string($raw) ? $raw : null;
    }

    protected function extractDocx(string $path): ?string
    {
        try {
            $entries = $this->zipReader->extractXmlEntries($path);
            $docXml = $entries['word/document.xml'] ?? null;
            if (! $docXml) {
                return null;
            }

            $dom = new \DOMDocument;
            $previous = libxml_use_internal_errors(true);
            $loaded = $dom->loadXML($docXml, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
            libxml_clear_errors();
            libxml_use_internal_errors($previous);

            if (! $loaded) {
                return null;
            }

            $nsW = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
            $bodies = $dom->getElementsByTagNameNS($nsW, 'body');
            $bodyNode = $bodies->length > 0 ? $bodies->item(0) : $dom->documentElement;
            if (! $bodyNode) {
                return null;
            }

            $blocks = [];
            foreach ($bodyNode->childNodes as $node) {
                if (! $node instanceof \DOMElement) {
                    continue;
                }

                if ($node->localName === 'p') {
                    $formatted = $this->formatDocxDomParagraph($node, $nsW);
                    if ($formatted !== null) {
                        $blocks[] = $formatted;
                    }
                } elseif ($node->localName === 'tbl') {
                    $rows = $node->getElementsByTagNameNS($nsW, 'tr');
                    foreach ($rows as $row) {
                        if (! $row instanceof \DOMElement) {
                            continue;
                        }
                        $cellTexts = [];
                        foreach ($row->childNodes as $cell) {
                            if (! $cell instanceof \DOMElement || $cell->localName !== 'tc') {
                                continue;
                            }
                            $pLines = [];
                            foreach ($cell->getElementsByTagNameNS($nsW, 'p') as $p) {
                                if ($p instanceof \DOMElement) {
                                    $pText = $this->formatDocxDomParagraph($p, $nsW, true);
                                    if ($pText !== null) {
                                        $pLines[] = $pText;
                                    }
                                }
                            }
                            if (! empty($pLines)) {
                                $cellTexts[] = implode(' ', $pLines);
                            }
                        }
                        if (! empty($cellTexts)) {
                            $blocks[] = implode(' | ', $cellTexts);
                        }
                    }
                }
            }

            return ! empty($blocks) ? implode("\n\n", $blocks) : null;
        } catch (\Throwable) {
            return null;
        }
    }

    protected function formatDocxDomParagraph(\DOMElement $paragraph, string $nsW, bool $plainOnly = false): ?string
    {
        $parts = [];
        foreach ($paragraph->getElementsByTagNameNS($nsW, '*') as $el) {
            if ($el->localName === 'tab') {
                $parts[] = ' ';
            } elseif ($el->localName === 'br') {
                $parts[] = "\n";
            } elseif ($el->localName === 't') {
                $parts[] = $el->textContent;
            }
        }

        $line = trim(implode('', $parts));
        if ($line === '') {
            return null;
        }

        if ($plainOnly) {
            return $line;
        }

        $styleVal = '';
        $pStyles = $paragraph->getElementsByTagNameNS($nsW, 'pStyle');
        if ($pStyles->length > 0 && $pStyles->item(0) instanceof \DOMElement) {
            $styleEl = $pStyles->item(0);
            $styleVal = strtolower($styleEl->getAttributeNS($nsW, 'val') ?: $styleEl->getAttribute('w:val'));
        }

        if (str_contains($styleVal, 'heading1') || $styleVal === 'title') {
            return '## '.$line;
        }
        if (str_contains($styleVal, 'heading2') || str_contains($styleVal, 'heading3') || $styleVal === 'subtitle') {
            return '### '.$line;
        }

        return $line;
    }

    protected function extractPdf(string $path): ?string
    {
        $raw = file_get_contents($path);
        if (! is_string($raw) || ! str_starts_with($raw, '%PDF-')) {
            return null;
        }

        $streams = $this->extractPdfStreams($raw);
        if (empty($streams)) {
            return null;
        }

        // Bangun tabel mapping Unicode dari semua stream /ToUnicode CMap
        $cmapTables = [];
        foreach ($streams as $streamContent) {
            if (str_contains($streamContent, 'beginbfchar') || str_contains($streamContent, 'beginbfrange')) {
                $cmapTables = array_replace($cmapTables, $this->parseToUnicodeCMap($streamContent));
            }
        }

        $extractedLines = [];
        foreach ($streams as $streamContent) {
            if (! preg_match('/\bBT\b.*?\bET\b/s', $streamContent)) {
                continue;
            }

            if (preg_match_all('/\bBT\b(.*?)\bET\b/s', $streamContent, $btBlocks)) {
                foreach ($btBlocks[1] as $block) {
                    $blockText = $this->parsePdfTextBlock($block, $cmapTables);
                    if ($blockText !== '') {
                        $extractedLines[] = $blockText;
                    }
                }
            }
        }

        if (empty($extractedLines)) {
            return null;
        }

        $joined = trim(implode("\n\n", $extractedLines));
        if (! $this->isReadableText($joined)) {
            return null;
        }

        return $joined;
    }

    /**
     * @return array<int, string>
     */
    protected function extractPdfStreams(string $pdfContent): array
    {
        $streams = [];
        $offset = 0;
        $length = strlen($pdfContent);

        while ($offset < $length) {
            $streamPos = strpos($pdfContent, 'stream', $offset);
            if ($streamPos === false) {
                break;
            }

            // Pastikan kata 'stream' bukan bagian dari 'endstream' atau nama lain
            $prevChar = $streamPos > 0 ? $pdfContent[$streamPos - 1] : "\n";
            if (! in_array($prevChar, ["\n", "\r", ' ', "\t", '>'], true)) {
                $offset = $streamPos + 6;

                continue;
            }

            $afterStream = $streamPos + 6;
            if ($afterStream < $length && $pdfContent[$afterStream] === "\r") {
                $afterStream++;
            }
            if ($afterStream < $length && $pdfContent[$afterStream] === "\n") {
                $afterStream++;
            } else {
                $offset = $streamPos + 6;

                continue;
            }

            $endStreamPos = strpos($pdfContent, 'endstream', $afterStream);
            if ($endStreamPos === false) {
                break;
            }

            $dictWindowStart = max(0, $streamPos - 512);
            $dictChunk = substr($pdfContent, $dictWindowStart, $streamPos - $dictWindowStart);
            $isFlate = stripos($dictChunk, '/FlateDecode') !== false;

            $streamBody = substr($pdfContent, $afterStream, $endStreamPos - $afterStream);
            $streamBody = rtrim($streamBody, "\r\n");

            if ($isFlate && $streamBody !== '') {
                $decoded = @gzuncompress($streamBody);
                if ($decoded === false) {
                    $decoded = @gzinflate($streamBody);
                }
                if ($decoded === false && strlen($streamBody) > 2) {
                    $decoded = @gzinflate(substr($streamBody, 2));
                }
                if (is_string($decoded) && $decoded !== '') {
                    $streams[] = $decoded;
                }
            } elseif ($streamBody !== '') {
                $streams[] = $streamBody;
            }

            $offset = $endStreamPos + 9;
        }

        return $streams;
    }

    /**
     * @return array<string, string>
     */
    protected function parseToUnicodeCMap(string $cmapContent): array
    {
        $map = [];

        if (preg_match_all('/beginbfchar\s*(.*?)\s*endbfchar/s', $cmapContent, $bfChars)) {
            foreach ($bfChars[1] as $section) {
                if (preg_match_all('/<([0-9A-Fa-f]+)>\s*<([0-9A-Fa-f]+)>/', $section, $pairs, PREG_SET_ORDER)) {
                    foreach ($pairs as $pair) {
                        $src = strtoupper($pair[1]);
                        $dst = $this->decodeHexUtf16($pair[2]);
                        if ($dst !== '') {
                            $map[$src] = $dst;
                        }
                    }
                }
            }
        }

        if (preg_match_all('/beginbfrange\s*(.*?)\s*endbfrange/s', $cmapContent, $bfRanges)) {
            foreach ($bfRanges[1] as $section) {
                // Format: <start> <end> <dstStart>
                if (preg_match_all('/<([0-9A-Fa-f]+)>\s*<([0-9A-Fa-f]+)>\s*<([0-9A-Fa-f]+)>/', $section, $ranges, PREG_SET_ORDER)) {
                    foreach ($ranges as $range) {
                        $startHex = $range[1];
                        $endHex = $range[2];
                        $dstHex = $range[3];
                        $padLen = strlen($startHex);
                        $start = hexdec($startHex);
                        $end = hexdec($endHex);
                        $dstStart = hexdec($dstHex);

                        if (($end - $start) > 1024) {
                            continue;
                        }

                        for ($code = $start; $code <= $end; $code++) {
                            $srcKey = strtoupper(str_pad(dechex((int) $code), $padLen, '0', STR_PAD_LEFT));
                            $targetHex = str_pad(dechex((int) ($dstStart + ($code - $start))), max(4, strlen($dstHex)), '0', STR_PAD_LEFT);
                            $decoded = $this->decodeHexUtf16($targetHex);
                            if ($decoded !== '') {
                                $map[$srcKey] = $decoded;
                            }
                        }
                    }
                }

                // Format: <start> <end> [<d1> <d2> ...]
                if (preg_match_all('/<([0-9A-Fa-f]+)>\s*<([0-9A-Fa-f]+)>\s*\[(.*?)\]/s', $section, $arrayRanges, PREG_SET_ORDER)) {
                    foreach ($arrayRanges as $range) {
                        $padLen = strlen($range[1]);
                        $start = hexdec($range[1]);
                        $end = hexdec($range[2]);
                        preg_match_all('/<([0-9A-Fa-f]+)>/', $range[3], $targets);
                        $targetList = $targets[1] ?? [];
                        $offset = 0;

                        for ($code = $start; $code <= $end && isset($targetList[$offset]); $code++, $offset++) {
                            $srcKey = strtoupper(str_pad(dechex((int) $code), $padLen, '0', STR_PAD_LEFT));
                            $decoded = $this->decodeHexUtf16($targetList[$offset]);
                            if ($decoded !== '') {
                                $map[$srcKey] = $decoded;
                            }
                        }
                    }
                }
            }
        }

        return $map;
    }

    /**
     * @param  array<string, string>  $cmap
     */
    protected function parsePdfTextBlock(string $block, array $cmap): string
    {
        $output = [];
        $currentLine = '';
        $len = strlen($block);
        $i = 0;
        $pendingTokens = [];
        $lastY = null;

        while ($i < $len) {
            $ch = $block[$i];

            if (ctype_space($ch)) {
                $i++;

                continue;
            }

            if ($ch === '(') {
                [$literal, $nextIdx] = $this->readPdfLiteralString($block, $i);
                $pendingTokens[] = ['type' => 'str', 'value' => $literal];
                $i = $nextIdx;

                continue;
            }

            if ($ch === '<' && ($i + 1 >= $len || $block[$i + 1] !== '<')) {
                $closePos = strpos($block, '>', $i + 1);
                if ($closePos !== false) {
                    $hex = preg_replace('/\s+/', '', substr($block, $i + 1, $closePos - $i - 1)) ?? '';
                    $decodedHex = $this->decodePdfHexString($hex, $cmap);
                    $pendingTokens[] = ['type' => 'str', 'value' => $decodedHex];
                    $i = $closePos + 1;

                    continue;
                }
            }

            if ($ch === '[') {
                [$arrayContent, $nextIdx] = $this->readPdfArray($block, $i, $cmap);
                $pendingTokens[] = ['type' => 'arr', 'value' => $arrayContent];
                $i = $nextIdx;

                continue;
            }

            // Baca angka atau operator
            $start = $i;
            while ($i < $len && ! ctype_space($block[$i]) && ! in_array($block[$i], ['(', '<', '[', ']', ')'], true)) {
                $i++;
            }
            $token = substr($block, $start, $i - $start);

            if ($token === 'Tj' || $token === "'" || $token === '"') {
                if (($token === "'" || $token === '"') && trim($currentLine) !== '') {
                    $output[] = trim($currentLine);
                    $currentLine = '';
                }
                $lastTok = array_pop($pendingTokens);
                if ($lastTok && is_string($lastTok['value'])) {
                    $currentLine .= $lastTok['value'];
                }
                $pendingTokens = [];
            } elseif ($token === 'TJ') {
                $lastTok = array_pop($pendingTokens);
                if ($lastTok && is_string($lastTok['value'])) {
                    $currentLine .= $lastTok['value'];
                }
                $pendingTokens = [];
            } elseif ($token === 'T*') {
                if (trim($currentLine) !== '') {
                    $output[] = trim($currentLine);
                    $currentLine = '';
                }
                $pendingTokens = [];
            } elseif ($token === 'Td' || $token === 'TD') {
                $ty = isset($pendingTokens[count($pendingTokens) - 1]['value']) ? (float) $pendingTokens[count($pendingTokens) - 1]['value'] : 0.0;
                $tx = isset($pendingTokens[count($pendingTokens) - 2]['value']) ? (float) $pendingTokens[count($pendingTokens) - 2]['value'] : 0.0;
                if (abs($ty) > 1.0 && trim($currentLine) !== '') {
                    $output[] = trim($currentLine);
                    if (abs($ty) >= 20.0) {
                        $output[] = '';
                    }
                    $currentLine = '';
                } elseif ($tx > 3.0 && trim($currentLine) !== '' && ! str_ends_with($currentLine, ' ')) {
                    $currentLine .= ' ';
                }
                $pendingTokens = [];
            } elseif ($token === 'Tm') {
                $ty = isset($pendingTokens[count($pendingTokens) - 1]['value']) ? (float) $pendingTokens[count($pendingTokens) - 1]['value'] : null;
                if ($lastY !== null && $ty !== null && abs($ty - $lastY) > 2.0 && trim($currentLine) !== '') {
                    $output[] = trim($currentLine);
                    if (abs($ty - $lastY) >= 20.0) {
                        $output[] = '';
                    }
                    $currentLine = '';
                }
                if ($ty !== null) {
                    $lastY = $ty;
                }
                $pendingTokens = [];
            } elseif (is_numeric($token)) {
                $pendingTokens[] = ['type' => 'num', 'value' => $token];
            } else {
                // Operator lain (Tf, Tc, Tw, Tz, cm, dll.) bersihkan operand sebelumnya
                $pendingTokens = [];
            }
        }

        if (trim($currentLine) !== '') {
            $output[] = trim($currentLine);
        }

        return implode("\n", $output);
    }

    /**
     * @return array{0: string, 1: int}
     */
    protected function readPdfLiteralString(string $block, int $startIdx): array
    {
        $len = strlen($block);
        $i = $startIdx + 1;
        $depth = 1;
        $result = '';

        while ($i < $len && $depth > 0) {
            $ch = $block[$i];
            if ($ch === '\\' && $i + 1 < $len) {
                $next = $block[$i + 1];
                if ($next === 'n') {
                    $result .= "\n";
                    $i += 2;
                } elseif ($next === 'r') {
                    $result .= "\r";
                    $i += 2;
                } elseif ($next === 't') {
                    $result .= "\t";
                    $i += 2;
                } elseif (in_array($next, ['(', ')', '\\'], true)) {
                    $result .= $next;
                    $i += 2;
                } elseif (ctype_digit($next) && $next <= '7') {
                    $oct = substr($block, $i + 1, 3);
                    if (preg_match('/^([0-7]{1,3})/', $oct, $m)) {
                        $result .= chr((int) octdec($m[1]));
                        $i += 1 + strlen($m[1]);
                    } else {
                        $i += 2;
                    }
                } else {
                    $i += 2;
                }

                continue;
            }

            if ($ch === '(') {
                $depth++;
                $result .= $ch;
            } elseif ($ch === ')') {
                $depth--;
                if ($depth > 0) {
                    $result .= $ch;
                }
            } else {
                $result .= $ch;
            }
            $i++;
        }

        if ($result !== '' && ! mb_check_encoding($result, 'UTF-8')) {
            $result = (string) mb_convert_encoding($result, 'UTF-8', 'Windows-1252');
        }

        return [$result, $i];
    }

    /**
     * @param  array<string, string>  $cmap
     * @return array{0: string, 1: int}
     */
    protected function readPdfArray(string $block, int $startIdx, array $cmap): array
    {
        $len = strlen($block);
        $i = $startIdx + 1;
        $out = '';

        while ($i < $len) {
            $ch = $block[$i];
            if ($ch === ']') {
                $i++;
                break;
            }
            if (ctype_space($ch)) {
                $i++;

                continue;
            }
            if ($ch === '(') {
                [$str, $nextIdx] = $this->readPdfLiteralString($block, $i);
                $out .= $str;
                $i = $nextIdx;

                continue;
            }
            if ($ch === '<') {
                $close = strpos($block, '>', $i + 1);
                if ($close !== false) {
                    $hex = preg_replace('/\s+/', '', substr($block, $i + 1, $close - $i - 1)) ?? '';
                    $out .= $this->decodePdfHexString($hex, $cmap);
                    $i = $close + 1;

                    continue;
                }
            }

            $numStart = $i;
            while ($i < $len && ! ctype_space($block[$i]) && ! in_array($block[$i], ['(', '<', ']'], true)) {
                $i++;
            }
            $numToken = substr($block, $numStart, $i - $numStart);
            if (is_numeric($numToken) && (float) $numToken <= -120.0 && $out !== '' && ! str_ends_with($out, ' ')) {
                $out .= ' ';
            }
        }

        return [$out, $i];
    }

    /**
     * @param  array<string, string>  $cmap
     */
    protected function decodePdfHexString(string $hex, array $cmap): string
    {
        if ($hex === '') {
            return '';
        }

        $hexUpper = strtoupper($hex);
        if (str_starts_with($hexUpper, 'FEFF')) {
            return $this->decodeHexUtf16(substr($hexUpper, 4));
        }

        if (! empty($cmap)) {
            // Tentukan panjang kode glyph berdasarkan key di CMap (umumnya 4 hex digit / 2 byte atau 2 hex digit / 1 byte)
            $sampleKey = (string) array_key_first($cmap);
            $step = strlen($sampleKey) >= 4 ? 4 : 2;
            $mapped = '';
            $matchedAny = false;

            for ($pos = 0; $pos + $step <= strlen($hexUpper); $pos += $step) {
                $chunk = substr($hexUpper, $pos, $step);
                if (isset($cmap[$chunk])) {
                    $mapped .= $cmap[$chunk];
                    $matchedAny = true;
                } elseif ($step === 4 && isset($cmap[substr($chunk, 0, 2)])) {
                    $mapped .= $cmap[substr($chunk, 0, 2)];
                    $matchedAny = true;
                } else {
                    $codeVal = hexdec($chunk);
                    if ($codeVal >= 32 && $codeVal <= 126) {
                        $mapped .= chr((int) $codeVal);
                    }
                }
            }

            if ($matchedAny) {
                return $mapped;
            }
        }

        if (strlen($hexUpper) % 2 !== 0) {
            $hexUpper .= '0';
        }

        $bin = @hex2bin($hexUpper);
        if (! is_string($bin)) {
            return '';
        }

        if (! mb_check_encoding($bin, 'UTF-8')) {
            $bin = (string) mb_convert_encoding($bin, 'UTF-8', 'Windows-1252');
        }

        return $bin;
    }

    protected function decodeHexUtf16(string $hex): string
    {
        $clean = preg_replace('/[^0-9A-Fa-f]/', '', $hex) ?? '';
        if ($clean === '') {
            return '';
        }
        if (strlen($clean) % 2 !== 0) {
            $clean = '0'.$clean;
        }
        if (strlen($clean) === 2) {
            $clean = '00'.$clean;
        }

        $bin = @hex2bin($clean);
        if (! is_string($bin)) {
            return '';
        }

        $decoded = @mb_convert_encoding($bin, 'UTF-8', 'UTF-16BE');

        return is_string($decoded) ? str_replace("\0", '', $decoded) : '';
    }

    protected function extractLegacyDoc(string $path): ?string
    {
        $raw = file_get_contents($path);
        if (! is_string($raw) || $raw === '') {
            return null;
        }

        // Ambil segmen teks ASCII yang cukup panjang dan dapat dibaca dari berkas biner .doc
        if (preg_match_all('/[A-Za-z0-9][A-Za-z0-9\s.,;:!?()\-\/]{24,}/', $raw, $matches)) {
            $lines = array_values(array_filter(
                array_map('trim', $matches[0]),
                fn (string $s): bool => $this->isReadableText($s) && ! str_contains($s, 'Microsoft Word')
            ));

            if (! empty($lines)) {
                return implode("\n\n", array_slice($lines, 0, 80));
            }
        }

        return null;
    }

    protected function isReadableText(string $text): bool
    {
        $trimmed = trim($text);
        if (mb_strlen($trimmed) < 5) {
            return false;
        }

        $total = mb_strlen($trimmed);
        $printableMatches = [];
        preg_match_all('/[\p{L}\p{N}\s.,;:!?()\[\]\'"\-\/%&+=]/u', $trimmed, $printableMatches);
        $printableCount = count($printableMatches[0] ?? []);

        return ($printableCount / max(1, $total)) >= 0.78;
    }

    protected function normalizeParagraphs(string $text): string
    {
        $clean = str_replace(["\r\n", "\r"], "\n", $text);
        $clean = preg_replace("/[ \t]+\n/", "\n", $clean) ?? $clean;
        $clean = preg_replace("/\n{3,}/", "\n\n", $clean) ?? $clean;

        return trim($clean);
    }
}
