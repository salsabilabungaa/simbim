<?php

namespace Tests\Feature;

use App\Models\SkripsiDokumen;
use App\Models\SkripsiDokumenKomentar;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SkripsiRevisiDiskusiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_dosen_melihat_aksi_diskusi_revisi_dan_membuka_dokumen_serta_kolom_diskusi_mahasiswa(): void
    {
        $dosen = User::factory()->dosen()->create([
            'name' => 'Dr. Siti Rahmawati Uji',
        ]);
        $mahasiswa = User::factory()->mahasiswa()->create([
            'name' => 'Mahasiswa Uji Skripsi',
            'nomor_induk' => '2108107099001',
        ]);

        $dokumen = SkripsiDokumen::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_pembimbing_id' => $dosen->id,
            'judul_skripsi' => 'Aplikasi Audit Kepatuhan Akademik PSTI UPI',
            'konten_naskah' => "## BAB III — METODOLOGI PENELITIAN\n\n### 3.1 Metode Pengumpulan Data\nKalimat uji dokumen yang dapat disorot dan diberi komentar secara langsung oleh dosen pembimbing.",
        ]);

        $resDaftar = $this->actingAs($dosen)->get(route('dosen.bimbingan.mahasiswa'));
        $resDaftar->assertStatus(200);
        $resDaftar->assertSee('Mahasiswa Uji Skripsi');
        $resDaftar->assertSee(route('dosen.bimbingan.skripsi.show', $dokumen->id));

        $resBeranda = $this->actingAs($dosen)->get(route('dosen.beranda'));
        $resBeranda->assertStatus(200);
        $resBeranda->assertSee(route('dosen.bimbingan.skripsi.show', $dokumen->id));

        $resWorkspace = $this->actingAs($dosen)->get(route('dosen.bimbingan.skripsi.show', $dokumen->id));
        $resWorkspace->assertStatus(200);
        $resWorkspace->assertSee('Aplikasi Audit Kepatuhan Akademik PSTI UPI');
        $resWorkspace->assertSee('Kalimat uji dokumen yang dapat disorot');
        $resWorkspace->assertSee('Komentar Langsung Dokumen');
        $resWorkspace->assertSee('Kolom Diskusi Dua Arah');
    }

    public function test_komentar_langsung_dokumen_dan_diskusi_dua_arah_tersinkronisasi_antara_dosen_dan_mahasiswa(): void
    {
        $dosen = User::factory()->dosen()->create(['name' => 'Dr. Siti Pembimbing']);
        $mahasiswa = User::factory()->mahasiswa()->create(['name' => 'Andi Mahasiswa Uji']);

        $dokumen = SkripsiDokumen::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_pembimbing_id' => $dosen->id,
            'judul_skripsi' => 'Sistem Bimbingan Sinkronisasi Dua Arah',
            'konten_naskah' => "## BAB III\n\n### 3.2 Desain Basis Data\nRelasi entitas komentar dokumen disinkronkan secara langsung antara dosen dan mahasiswa.",
        ]);

        // 1. Dosen memberi komentar langsung pada kutipan teks terpilih
        $resKomentar = $this->actingAs($dosen)->postJson(
            route('dosen.bimbingan.skripsi.komentar.store', $dokumen),
            [
                'lokasi_dokumen' => '3.2 Desain Basis Data',
                'kutipan_teks' => 'Relasi entitas komentar dokumen disinkronkan secara langsung',
                'isi_komentar' => 'Tambahkan kardinalitas one-to-many dari tabel dokumen ke komentar.',
                'status' => 'Perlu Revisi',
            ]
        );
        $resKomentar->assertStatus(201)
            ->assertJsonPath('komentar.isi_komentar', 'Tambahkan kardinalitas one-to-many dari tabel dokumen ke komentar.')
            ->assertJsonPath('komentar.status', 'Perlu Revisi');

        $komentarId = $resKomentar->json('komentar.id');

        // 2. Dosen mengirim pesan di kolom diskusi dua arah & mengubah status draf
        $this->actingAs($dosen)->postJson(
            route('dosen.bimbingan.skripsi.diskusi.store', $dokumen),
            ['pesan' => 'Halo Andi, cek komentar kuning di Sub-bab 3.2 ya.']
        )->assertStatus(201);

        $this->actingAs($dosen)->patchJson(
            route('dosen.bimbingan.skripsi.status.update', $dokumen),
            [
                'status_draf' => 'Revisi Diperlukan',
                'progres_persen' => 70,
                'catatan_akhir_dosen' => 'Perbaiki relasi ERD pada Sub-bab 3.2.',
            ]
        )->assertOk()->assertJsonPath('status_draf', 'Revisi Diperlukan');

        // 3. Verifikasi halaman Mahasiswa langsung tersinkronisasi melihat komentar, kutipan, status & chat dari Dosen
        $resMahasiswa = $this->actingAs($mahasiswa)->get(route('mahasiswa.bimbingan-skripsi'));
        $resMahasiswa->assertStatus(200);
        $resMahasiswa->assertSee('Dr. Siti Pembimbing');
        $resMahasiswa->assertSee('Tambahkan kardinalitas one-to-many dari tabel dokumen ke komentar.');
        $resMahasiswa->assertSee('Relasi entitas komentar dokumen disinkronkan secara langsung');
        $resMahasiswa->assertSee('Halo Andi, cek komentar kuning di Sub-bab 3.2 ya.');
        $resMahasiswa->assertSee('Revisi Diperlukan');

        // 4. Mahasiswa membalas komentar inline & membalas di kolom diskusi dua arah
        $this->actingAs($mahasiswa)->postJson(
            route('mahasiswa.bimbingan-skripsi.komentar.store', $dokumen),
            [
                'parent_id' => $komentarId,
                'lokasi_dokumen' => '3.2 Desain Basis Data',
                'isi_komentar' => 'Sudah saya tambahkan relasi one-to-many pada diagram revisi Bu.',
            ]
        )->assertStatus(201);

        $this->actingAs($mahasiswa)->postJson(
            route('mahasiswa.bimbingan-skripsi.diskusi.store', $dokumen),
            ['pesan' => 'Siap Bu, draf revisi sudah saya perbarui dan siap diperiksa ulang.']
        )->assertStatus(201);

        // 5. Dosen menandai komentar selesai & melihat semua balasan mahasiswa di sesi diskusi revisi
        $this->actingAs($dosen)->patchJson(
            route('dosen.bimbingan.skripsi.komentar.resolve', ['dokumen' => $dokumen, 'komentar' => $komentarId])
        )->assertOk()->assertJsonPath('komentar.is_resolved', true);

        $resDosenCheck = $this->actingAs($dosen)->get(route('dosen.bimbingan.skripsi.show', $dokumen));
        $resDosenCheck->assertStatus(200);
        $resDosenCheck->assertSee('Sudah saya tambahkan relasi one-to-many pada diagram revisi Bu.');
        $resDosenCheck->assertSee('Siap Bu, draf revisi sudah saya perbarui dan siap diperiksa ulang.');
        $resDosenCheck->assertSee('Buka Lagi');
    }

    public function test_mahasiswa_dapat_mengunggah_revisi_berkas_yang_langsung_muncul_di_portal_dosen(): void
    {
        Storage::fake('local');

        $dosen = User::factory()->dosen()->create();
        $mahasiswa = User::factory()->mahasiswa()->create();
        $dokumen = SkripsiDokumen::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_pembimbing_id' => $dosen->id,
            'nama_file' => 'Draft_BAB3_v1.0.txt',
            'versi' => 'v1.0',
        ]);

        $uploadedFile = UploadedFile::fake()->createWithContent(
            'Revisi_Bab3_Terbaru.txt',
            "## BAB III REVISI\n\nIsi draf revisi terbaru yang diunggah oleh mahasiswa PSTI UPI."
        );

        $resUpload = $this->actingAs($mahasiswa)->post(
            route('mahasiswa.bimbingan-skripsi.upload', $dokumen),
            [
                'bab_aktif' => 'Bab III (Metodologi Revisi v1.1)',
                'file_draft' => $uploadedFile,
            ]
        );
        $resUpload->assertRedirect();

        $dokumen->refresh();
        $this->assertSame('v1.1', $dokumen->versi);
        $this->assertSame('Revisi_Bab3_Terbaru.txt', $dokumen->nama_file);
        $this->assertStringContainsString('Isi draf revisi terbaru', (string) $dokumen->konten_naskah);

        $resDosen = $this->actingAs($dosen)->get(route('dosen.bimbingan.skripsi.show', $dokumen));
        $resDosen->assertStatus(200);
        $resDosen->assertSee('Revisi_Bab3_Terbaru.txt');
        $resDosen->assertSee('v1.1');
        $resDosen->assertSee('Isi draf revisi terbaru yang diunggah oleh mahasiswa PSTI UPI.');

        $this->actingAs($dosen)->get(route('dosen.bimbingan.skripsi.download', $dokumen))
            ->assertStatus(200)
            ->assertHeader('content-disposition');

        $this->actingAs($mahasiswa)->get(route('mahasiswa.bimbingan-skripsi.download', $dokumen))
            ->assertStatus(200)
            ->assertHeader('content-disposition');
    }

    public function test_otorisasi_ketat_mencegah_dosen_dan_mahasiswa_lain_mengakses_dokumen_bukan_miliknya(): void
    {
        $dosenPembimbing = User::factory()->dosen()->create();
        $dosenLain = User::factory()->dosen()->create();
        $mahasiswaPemilik = User::factory()->mahasiswa()->create();
        $mahasiswaLain = User::factory()->mahasiswa()->create();
        $admin = User::factory()->admin()->create();

        $dokumen = SkripsiDokumen::factory()->create([
            'mahasiswa_id' => $mahasiswaPemilik->id,
            'dosen_pembimbing_id' => $dosenPembimbing->id,
        ]);

        $komentar = SkripsiDokumenKomentar::factory()->create([
            'skripsi_dokumen_id' => $dokumen->id,
            'user_id' => $dosenPembimbing->id,
        ]);

        // Dosen lain tidak boleh buka/komen/chat di mahasiswa bukan bimbingannya
        $this->actingAs($dosenLain)->get(route('dosen.bimbingan.skripsi.show', $dokumen))->assertStatus(403);
        $this->actingAs($dosenLain)->postJson(route('dosen.bimbingan.skripsi.komentar.store', $dokumen), [
            'isi_komentar' => 'Tidak sah',
        ])->assertStatus(403);
        $this->actingAs($dosenLain)->patchJson(
            route('dosen.bimbingan.skripsi.komentar.resolve', ['dokumen' => $dokumen, 'komentar' => $komentar])
        )->assertStatus(403);

        // Mahasiswa lain tidak boleh balas/chat/upload/download dokumen mahasiswa pemilik
        $this->actingAs($mahasiswaLain)->postJson(route('mahasiswa.bimbingan-skripsi.diskusi.store', $dokumen), [
            'pesan' => 'Pesan tidak sah',
        ])->assertStatus(403);
        $this->actingAs($mahasiswaLain)->get(route('mahasiswa.bimbingan-skripsi.download', $dokumen))->assertStatus(403);

        // Mahasiswa pemilik tidak boleh mengakses rute dosen
        $this->actingAs($mahasiswaPemilik)->get(route('dosen.bimbingan.skripsi.show', $dokumen))->assertRedirect(route('mahasiswa.beranda'));

        // Admin tetap memiliki hak akses global
        $this->actingAs($admin)->get(route('dosen.bimbingan.skripsi.show', $dokumen))->assertStatus(200);
    }

    public function test_naskah_komentar_langsung_otomatis_tersinkronisasi_saat_mahasiswa_mengunggah_docx_dan_pdf(): void
    {
        Storage::fake('local');

        $dosen = User::factory()->dosen()->create();
        $mahasiswa = User::factory()->mahasiswa()->create();
        $dokumen = SkripsiDokumen::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_pembimbing_id' => $dosen->id,
            'konten_naskah' => 'Naskah lama yang harus terganti oleh isi dokumen unggahan baru.',
            'versi' => 'v1.0',
        ]);

        // 1. Uji unggah DOCX berisi Heading & Paragraf
        $docxBytes = $this->buildMinimalDocxBinary(
            'BAB IV IMPLEMENTASI SISTEM',
            'Kalimat unik dari file Word DOCX mahasiswa PSTI UPI untuk dikomentari langsung oleh dosen.'
        );
        $docxFile = UploadedFile::fake()->createWithContent('Revisi_Bab4.docx', $docxBytes);

        $this->actingAs($mahasiswa)->post(
            route('mahasiswa.bimbingan-skripsi.upload', $dokumen),
            [
                'bab_aktif' => 'Bab IV (Implementasi)',
                'file_draft' => $docxFile,
                'konten_naskah' => '',
            ]
        )->assertRedirect();

        $dokumen->refresh();
        $this->assertSame('v1.1', $dokumen->versi);
        $this->assertStringContainsString('BAB IV IMPLEMENTASI SISTEM', (string) $dokumen->konten_naskah);
        $this->assertStringContainsString('Kalimat unik dari file Word DOCX mahasiswa PSTI UPI', (string) $dokumen->konten_naskah);
        $this->assertStringNotContainsString('Naskah lama yang harus terganti', (string) $dokumen->konten_naskah);

        $this->actingAs($dosen)->get(route('dosen.bimbingan.skripsi.show', $dokumen))
            ->assertStatus(200)
            ->assertSee('Kalimat unik dari file Word DOCX mahasiswa PSTI UPI')
            ->assertDontSee('Naskah lama yang harus terganti');

        // 2. Uji unggah PDF (terkompresi FlateDecode + ToUnicode CMap) -> mengganti naskah otomatis
        $pdfBytes = $this->buildMinimalPdfWithToUnicode(
            'Analisis Performa Microservices PDF Revisi Final Mahasiswa UPI'
        );
        $pdfFile = UploadedFile::fake()->createWithContent('Revisi_Bab4_Final.pdf', $pdfBytes);

        $this->actingAs($mahasiswa)->post(
            route('mahasiswa.bimbingan-skripsi.upload', $dokumen),
            [
                'bab_aktif' => 'Bab IV (Final PDF)',
                'file_draft' => $pdfFile,
            ]
        )->assertRedirect();

        $dokumen->refresh();
        $this->assertSame('v1.2', $dokumen->versi);
        $this->assertStringContainsString('Analisis Performa Microservices PDF Revisi Final Mahasiswa UPI', (string) $dokumen->konten_naskah);
        $this->assertStringNotContainsString('Kalimat unik dari file Word DOCX', (string) $dokumen->konten_naskah);

        // Dosen melihat teks PDF langsung di halaman komentar dan memberi komentar langsung pada kutipan PDF
        $this->actingAs($dosen)->get(route('dosen.bimbingan.skripsi.show', $dokumen))
            ->assertStatus(200)
            ->assertSee('Analisis Performa Microservices PDF Revisi Final Mahasiswa UPI');

        $this->actingAs($dosen)->postJson(
            route('dosen.bimbingan.skripsi.komentar.store', $dokumen),
            [
                'lokasi_dokumen' => 'Bab IV (Final PDF)',
                'kutipan_teks' => 'Analisis Performa Microservices PDF',
                'isi_komentar' => 'Tambahkan grafik latensi p95 pada bagian ini.',
                'status' => 'Perlu Revisi',
            ]
        )->assertStatus(201);

        $this->actingAs($mahasiswa)->get(route('mahasiswa.bimbingan-skripsi'))
            ->assertStatus(200)
            ->assertSee('Analisis Performa Microservices PDF Revisi Final Mahasiswa UPI')
            ->assertSee('Tambahkan grafik latensi p95 pada bagian ini.');

        // 3. Uji unggah PDF tanpa teks (misalnya PDF pindaian gambar) -> tidak mempertahankan naskah lama
        $scannedPdf = UploadedFile::fake()->createWithContent('Scan_Lampiran.pdf', "%PDF-1.4\n1 0 obj <<>> endobj\n%%EOF");
        $this->actingAs($mahasiswa)->post(
            route('mahasiswa.bimbingan-skripsi.upload', $dokumen),
            ['file_draft' => $scannedPdf]
        )->assertRedirect();

        $dokumen->refresh();
        $this->assertNull($dokumen->konten_naskah);
        $this->actingAs($dosen)->get(route('dosen.bimbingan.skripsi.show', $dokumen))
            ->assertStatus(200)
            ->assertSee('Naskah teks tidak dapat diekstrak otomatis dari berkas')
            ->assertDontSee('Analisis Performa Microservices PDF Revisi Final Mahasiswa UPI');
    }

    protected function buildMinimalDocxBinary(string $heading, string $paragraph): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:body>'
            .'<w:p><w:pPr><w:pStyle w:val="Heading1"/></w:pPr><w:r><w:t>'.htmlspecialchars($heading, ENT_XML1).'</w:t></w:r></w:p>'
            .'<w:p><w:r><w:t>'.htmlspecialchars($paragraph, ENT_XML1).'</w:t></w:r></w:p>'
            .'</w:body></w:document>';

        return $this->packStoreZip(['word/document.xml' => $xml]);
    }

    protected function buildMinimalPdfWithToUnicode(string $text): string
    {
        $cmapStream = "/CIDInit /ProcSet findresource begin\n"
            ."12 dict begin\n"
            ."begincmap\n"
            ."1 begincodespacerange <0001> <00FF> endcodespacerange\n"
            ."2 beginbfchar\n"
            ."<0001> <0041>\n" // A
            ."<0002> <006E>\n" // n
            ."endbfchar\n"
            ."endcmap\n";

        $compressedCmap = gzcompress($cmapStream);
        // Gunakan operator campuran: CID hex untuk "An" + literal untuk sisa teks
        $rest = substr($text, 2);
        $escapedRest = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $rest);
        $contentStream = "BT\n/F1 12 Tf\n72 720 Td\n<00010002> Tj\n({$escapedRest}) Tj\nET\n";
        $compressedContent = gzcompress($contentStream);

        return "%PDF-1.4\n"
            ."1 0 obj\n<< /Length ".strlen($compressedCmap)." /Filter /FlateDecode >>\nstream\n{$compressedCmap}\nendstream\nendobj\n"
            ."2 0 obj\n<< /Length ".strlen($compressedContent)." /Filter /FlateDecode >>\nstream\n{$compressedContent}\nendstream\nendobj\n"
            .'%%EOF';
    }

    /**
     * @param  array<string, string>  $entries
     */
    protected function packStoreZip(array $entries): string
    {
        $localData = '';
        $centralDir = '';
        $offset = 0;

        foreach ($entries as $name => $content) {
            $crc = crc32($content);
            $len = strlen($content);
            $nameLen = strlen($name);

            $localHeader = pack('VvvvvvVVVvv', 0x04034B50, 20, 0, 0, 0, 0, $crc, $len, $len, $nameLen, 0).$name;
            $centralHeader = pack('VvvvvvvVVVvvvvvVV', 0x02014B50, 20, 20, 0, 0, 0, 0, $crc, $len, $len, $nameLen, 0, 0, 0, 0, 0, $offset).$name;

            $localData .= $localHeader.$content;
            $centralDir .= $centralHeader;
            $offset += strlen($localHeader) + $len;
        }

        $eocd = pack('VvvvvVVv', 0x06054B50, 0, 0, count($entries), count($entries), strlen($centralDir), $offset, 0);

        return $localData.$centralDir.$eocd;
    }
}
