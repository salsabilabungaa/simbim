<?php

namespace App\Support;

use Exception;

class SimpleZipReader
{
    /**
     * Ekstrak seluruh entri XML/.rels dari berkas berbasis ZIP (XLSX/DOCX) tanpa memerlukan ext-zip.
     *
     * @return array<string, string>
     *
     * @throws Exception
     */
    public function extractXmlEntries(string $zipPath): array
    {
        $fp = fopen($zipPath, 'rb');
        if (! $fp) {
            throw new Exception("Cannot open file: {$zipPath}");
        }

        try {
            fseek($fp, 0, SEEK_END);
            $fileSize = (int) ftell($fp);
            $searchLimit = min(65557, $fileSize);
            fseek($fp, $fileSize - $searchLimit, SEEK_SET);
            $data = fread($fp, $searchLimit);
            if ($data === false) {
                throw new Exception('Unable to read ZIP header.');
            }

            $eocdPos = strrpos($data, "PK\x05\x06");
            if ($eocdPos === false) {
                throw new Exception('Invalid ZIP format (EOCD signature not found)');
            }

            $eocdOffset = $fileSize - $searchLimit + $eocdPos;
            fseek($fp, $eocdOffset, SEEK_SET);
            $eocd = unpack('Vsig/vdisk/vdiskStart/vtotalSec/vtotalRecords/VcdSize/VcdOffset/vcommentLen', fread($fp, 22));

            fseek($fp, (int) $eocd['cdOffset'], SEEK_SET);

            $files = [];
            for ($i = 0; $i < (int) $eocd['totalRecords']; $i++) {
                $header = fread($fp, 46);
                if ($header === false || strlen($header) < 46) {
                    break;
                }

                $entry = unpack(
                    'Vsig/vversion/vverNeeded/vflag/vmethod/vmodTime/vmodDate/Vcrc32/VcompSize/VuncompSize/vnameLen/vextraLen/vcommentLen/vdiskStart/vintAttr/VextAttr/Voffset',
                    $header
                );

                if ($entry['sig'] !== 0x02014B50) {
                    break;
                }

                $filename = fread($fp, (int) $entry['nameLen']);
                if ($entry['extraLen'] > 0) {
                    fread($fp, (int) $entry['extraLen']);
                }
                if ($entry['commentLen'] > 0) {
                    fread($fp, (int) $entry['commentLen']);
                }

                if (is_string($filename)) {
                    $files[$filename] = [
                        'method' => (int) $entry['method'],
                        'compSize' => (int) $entry['compSize'],
                        'offset' => (int) $entry['offset'],
                    ];
                }
            }

            $extracted = [];
            foreach ($files as $name => $meta) {
                if (! str_ends_with($name, '.xml') && ! str_ends_with($name, '.rels')) {
                    continue;
                }

                fseek($fp, $meta['offset'], SEEK_SET);
                $localHeader = fread($fp, 30);
                if ($localHeader === false || strlen($localHeader) < 30) {
                    continue;
                }

                $local = unpack('Vsig/vver/vflag/vmethod/vmodTime/vmodDate/Vcrc32/VcompSize/VuncompSize/vnameLen/vextraLen', $localHeader);
                $dataOffset = $meta['offset'] + 30 + (int) $local['nameLen'] + (int) $local['extraLen'];
                fseek($fp, $dataOffset, SEEK_SET);
                $raw = $meta['compSize'] > 0 ? fread($fp, $meta['compSize']) : '';

                if (! is_string($raw)) {
                    continue;
                }

                if ($meta['method'] === 8 && function_exists('gzinflate')) {
                    $content = @gzinflate($raw);
                    if ($content !== false) {
                        $extracted[$name] = $content;
                    }
                } elseif ($meta['method'] === 0) {
                    $extracted[$name] = $raw;
                }
            }

            return $extracted;
        } finally {
            fclose($fp);
        }
    }
}
