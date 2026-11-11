<?php

namespace Tests\Feature;

use App\Models\KonsultasiAkademik;
use App\Models\PerwalianMahasiswa;
use App\Models\SkripsiDokumen;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MahasiswaNewAccountSyncTest extends TestCase
{
    use DatabaseTransactions;

    public function test_akun_baru_mahasiswa_tidak_menampilkan_data_demo_statis_di_seluruh_halaman_portal(): void
    {
        $admin = User::factory()->admin()->create();

        // Admin membuat akun mahasiswa baru melalui form manajemen akun manual
        $resCreate = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Najwa Rizki',
            'email' => 'kiki_baru@simbim.ac.id',
            'nomor_induk' => '2408107099881',
            'role' => 'mahasiswa',
            'password' => 'password123',
        ]);
        $resCreate->assertRedirect(route('admin.users.index'));

        $newStudent = User::where('email', 'kiki_baru@simbim.ac.id')->firstOrFail();

        // 1. Dashboard Mahasiswa menampilkan identitas akun baru & status kosong yang sinkron
        $resBeranda = $this->actingAs($newStudent)->get(route('mahasiswa.beranda'));
        $resBeranda->assertStatus(200);
        $resBeranda->assertSee('Najwa Rizki');
        $resBeranda->assertSee('2408107099881');
        $resBeranda->assertSee('Belum Perwalian');
        $resBeranda->assertSee('Belum Ada Draf Skripsi');
        $resBeranda->assertSee('0 dari 100%');
        $resBeranda->assertSee('Belum Ada Sesi');
        $resBeranda->assertSee('Belum Ditetapkan');
        $resBeranda->assertDontSee('Dr. Siti Rahmawati, M.Kom.');
        $resBeranda->assertDontSee('Prof. Dr. Ir. Ahmad Rizal, M.T.');
        $resBeranda->assertDontSee('Bab III — Metodologi Penelitian');
        $resBeranda->assertDontSee('65 dari 100%');

        // 2. Halaman Bimbingan Skripsi menampilkan kondisi awal kosong (tanpa draf/chat palsu)
        $resSkripsi = $this->actingAs($newStudent)->get(route('mahasiswa.bimbingan-skripsi'));
        $resSkripsi->assertStatus(200);
        $resSkripsi->assertSee('Belum Ada Draf Skripsi Diunggah');
        $resSkripsi->assertSee('Kolom Diskusi Belum Aktif');
        $resSkripsi->assertSee('Belum Memulai Bimbingan');
        $resSkripsi->assertSee('Belum Ditetapkan');
        $resSkripsi->assertDontSee('Draft_BAB3_Diagram_v1.2.pdf');
        $resSkripsi->assertDontSee('Halaman 12 (Paragraf 2)');
        $resSkripsi->assertDontSee('relasi token JWT');
        $resSkripsi->assertDontSee('Andi Pratama');

        // 3. Halaman Bimbingan Akademik menampilkan IPK 0.00 / 0 SKS / tabel kosong
        $resAkademik = $this->actingAs($newStudent)->get(route('mahasiswa.bimbingan-akademik'));
        $resAkademik->assertStatus(200);
        $resAkademik->assertSee('0.00');
        $resAkademik->assertSee('0 SKS Lulus');
        $resAkademik->assertSee('0% Selesai');
        $resAkademik->assertSee('Belum Mengisi KRS');
        $resAkademik->assertSee('Belum Ada Sesi');
        $resAkademik->assertDontSee('3.82');
        $resAkademik->assertDontSee('118 SKS');
        $resAkademik->assertDontSee('STATUS KRS SEMESTER 7');
        $resAkademik->assertDontSee('Pengajuan Beasiswa & Surat Rekomendasi');

        // 4. Halaman Riwayat Bimbingan menampilkan logbook kosong (0 sesi)
        $resRiwayat = $this->actingAs($newStudent)->get(route('mahasiswa.riwayat-bimbingan'));
        $resRiwayat->assertStatus(200);
        $resRiwayat->assertSee('Menampilkan 0 Sesi');
        $resRiwayat->assertSee('Belum ada data riwayat bimbingan.');
        $resRiwayat->assertDontSee('Draft_BAB3_Diagram_v1.2.pdf');
        $resRiwayat->assertDontSee('Prof. Dr. Eng. Ir. Herman, M.T.');
        $resRiwayat->assertDontSee('Dr. Ir. Teuku Muhammad, M.T.');

        // 5. Pemantauan Data Mahasiswa di panel Admin juga ikut menampilkan akun baru ini
        $resMonitoring = $this->actingAs($admin)->get(route('admin.monitoring.mahasiswa'));
        $resMonitoring->assertStatus(200);
        $resMonitoring->assertSee('Najwa Rizki');
        $resMonitoring->assertSee('2408107099881');
        $resMonitoring->assertSee('Akun Baru / Belum Perwalian');
    }

    public function test_akun_baru_mahasiswa_dapat_mengajukan_skripsi_dan_perwalian_awal_hingga_tersinkron_lintas_role(): void
    {
        Storage::fake('local');

        $admin = User::factory()->admin()->create();
        $dosenPembimbing = User::factory()->dosen()->create([
            'name' => 'Dr. Pembimbing Baru PSTI',
            'nomor_induk' => '198701012015042001',
        ]);
        $dosenWali = User::factory()->dosen()->create([
            'name' => 'Dr. Wali Baru PSTI',
            'nomor_induk' => '198602022014041002',
        ]);
        $gkm = User::factory()->gkm()->create();

        $mahasiswa = User::factory()->mahasiswa()->create([
            'name' => 'Kiki Mahasiswa Baru',
            'email' => 'kiki_sync_baru@simbim.ac.id',
            'nomor_induk' => '2408107099882',
        ]);

        // 1. Mahasiswa baru mengajukan bimbingan akademik awal ke Dosen Wali
        $this->actingAs($mahasiswa)->post(route('mahasiswa.bimbingan-akademik.pengajuan'), [
            'dosen_wali_id' => $dosenWali->id,
            'kategori_topik' => 'Konsultasi KRS Semester Awal PSTI',
            'detail_agenda' => 'Mohon arahan pemilihan mata kuliah paket semester ganjil Prodi PSTI.',
        ])->assertRedirect();

        $this->assertDatabaseHas('perwalian_mahasiswas', [
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_wali_id' => $dosenWali->id,
            'status_perwalian' => 'proses',
        ]);
        $this->assertDatabaseHas('konsultasi_akademiks', [
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_wali_id' => $dosenWali->id,
            'topik' => 'Konsultasi KRS Semester Awal PSTI',
        ]);

        // 2. Mahasiswa baru mengunggah & mengajukan draf skripsi awal ke Dosen Pembimbing
        $draftFile = UploadedFile::fake()->createWithContent(
            'Proposal_Kiki_Bab1.txt',
            "## BAB I — PENDAHULUAN\n\n### 1.1 Latar Belakang\nPenelitian sistem adaptif untuk mahasiswa PSTI UPI."
        );

        $this->actingAs($mahasiswa)->post(route('mahasiswa.bimbingan-skripsi.store-awal'), [
            'dosen_pembimbing_id' => $dosenPembimbing->id,
            'judul_skripsi' => 'Sistem Rekomendasi Belajar Adaptif Mahasiswa PSTI UPI',
            'bab_aktif' => 'Bab I (Pendahuluan)',
            'detail_agenda' => 'Mohon review draf Bab I proposal tugas akhir.',
            'file_draft' => $draftFile,
        ])->assertRedirect();

        $dokumen = SkripsiDokumen::where('mahasiswa_id', $mahasiswa->id)->firstOrFail();
        $this->assertSame($dosenPembimbing->id, $dokumen->dosen_pembimbing_id);
        $this->assertStringContainsString('Penelitian sistem adaptif untuk mahasiswa PSTI UPI', (string) $dokumen->konten_naskah);

        // 3. Verifikasi halaman Mahasiswa menampilkan seluruh data yang baru disinkronkan
        $this->actingAs($mahasiswa)->get(route('mahasiswa.beranda'))
            ->assertStatus(200)
            ->assertSee('Dr. Pembimbing Baru PSTI')
            ->assertSee('Dr. Wali Baru PSTI')
            ->assertSee('Bab I (Pendahuluan)')
            ->assertSee('1 Sesi Terlaksana')
            ->assertSee('Dalam Progres');

        $this->actingAs($mahasiswa)->get(route('mahasiswa.bimbingan-skripsi'))
            ->assertStatus(200)
            ->assertSee('Sistem Rekomendasi Belajar Adaptif Mahasiswa PSTI UPI')
            ->assertSee('Proposal_Kiki_Bab1.txt')
            ->assertSee('Penelitian sistem adaptif untuk mahasiswa PSTI UPI');

        $this->actingAs($mahasiswa)->get(route('mahasiswa.bimbingan-akademik'))
            ->assertStatus(200)
            ->assertSee('Dr. Wali Baru PSTI')
            ->assertSee('Konsultasi KRS Semester Awal PSTI');

        $this->actingAs($mahasiswa)->get(route('mahasiswa.riwayat-bimbingan'))
            ->assertStatus(200)
            ->assertSee('Menampilkan 2 Sesi')
            ->assertSee('Sistem Rekomendasi Belajar Adaptif Mahasiswa PSTI UPI')
            ->assertSee('Konsultasi KRS Semester Awal PSTI');

        // 4. Verifikasi portal Dosen, GKM, dan Admin langsung menerima sinkronisasi
        $this->actingAs($dosenPembimbing)->get(route('dosen.bimbingan.mahasiswa'))
            ->assertStatus(200)
            ->assertSee('Kiki Mahasiswa Baru')
            ->assertSee(route('dosen.bimbingan.skripsi.show', $dokumen->id));

        $this->actingAs($dosenWali)->get(route('dosen.bimbingan.pengajuan'))
            ->assertStatus(200)
            ->assertSee('Kiki Mahasiswa Baru')
            ->assertSee('Konsultasi KRS Semester Awal PSTI');

        $this->actingAs($gkm)->get(route('gkm.riwayat-konsultasi'))
            ->assertStatus(200)
            ->assertSee('Kiki Mahasiswa Baru')
            ->assertSee('Konsultasi KRS Semester Awal PSTI');

        $this->actingAs($admin)->get(route('admin.monitoring.mahasiswa'))
            ->assertStatus(200)
            ->assertSee('Kiki Mahasiswa Baru')
            ->assertSee('Dr. Pembimbing Baru PSTI')
            ->assertSee('Dr. Wali Baru PSTI');
    }

    public function test_validasi_pengajuan_awal_mahasiswa_menolak_dosen_tidak_valid_dan_guest(): void
    {
        $mahasiswa = User::factory()->mahasiswa()->create();
        $bukanDosen = User::factory()->mahasiswa()->create();

        $this->post(route('mahasiswa.bimbingan-akademik.pengajuan'), [
            'kategori_topik' => 'Test',
        ])->assertRedirect(route('login'));

        $this->actingAs($mahasiswa)
            ->post(route('mahasiswa.bimbingan-akademik.pengajuan'), [
                'dosen_wali_id' => $bukanDosen->id,
                'kategori_topik' => 'Konsultasi KRS',
                'detail_agenda' => 'Agenda uji.',
            ])
            ->assertSessionHasErrors('dosen_wali_id');

        $this->actingAs($mahasiswa)
            ->post(route('mahasiswa.bimbingan-skripsi.store-awal'), [
                'dosen_pembimbing_id' => $bukanDosen->id,
                'judul_skripsi' => 'Judul Uji',
                'bab_aktif' => 'Bab I (Pendahuluan)',
                'detail_agenda' => 'Agenda uji.',
            ])
            ->assertSessionHasErrors('dosen_pembimbing_id');

        $this->assertSame(0, PerwalianMahasiswa::where('mahasiswa_id', $mahasiswa->id)->count());
        $this->assertSame(0, KonsultasiAkademik::where('mahasiswa_id', $mahasiswa->id)->count());
        $this->assertSame(0, SkripsiDokumen::where('mahasiswa_id', $mahasiswa->id)->count());
    }

    public function test_dosen_pembimbing_akademik_ditetapkan_saat_admin_buat_dan_edit_akun_mahasiswa(): void
    {
        $admin = User::factory()->admin()->create();
        $dosenWaliA = User::factory()->dosen()->create([
            'name' => 'Dr. Wali Penetapan Admin PSTI',
            'nomor_induk' => '198801012015041001',
            'email' => 'wali_admin_a@upi.edu',
        ]);
        $dosenWaliB = User::factory()->dosen()->create([
            'name' => 'Dr. Wali Pengganti PSTI',
            'nomor_induk' => '198902022016042002',
            'email' => 'wali_admin_b@upi.edu',
        ]);
        $nonDosen = User::factory()->mahasiswa()->create();

        // Validasi menolak jika dosen_wali_id bukan akun dosen
        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Gagal Wali',
            'email' => 'gagal_wali@simbim.ac.id',
            'role' => 'mahasiswa',
            'dosen_wali_id' => $nonDosen->id,
            'password' => 'password123',
        ])->assertSessionHasErrors('dosen_wali_id');

        // 1. Admin membuat akun mahasiswa sambil langsung menetapkan Dosen Pembimbing Akademik
        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Salsa Mahasiswa Baru',
            'email' => 'salsa_dpa@simbim.ac.id',
            'nomor_induk' => '2508107000123',
            'role' => 'mahasiswa',
            'dosen_wali_id' => $dosenWaliA->id,
            'password' => 'password123',
        ])->assertRedirect(route('admin.users.index'));

        $student = User::where('email', 'salsa_dpa@simbim.ac.id')->firstOrFail();
        $this->assertSame($dosenWaliA->id, $student->dosen_wali_id);
        $this->assertDatabaseHas('perwalian_mahasiswas', [
            'mahasiswa_id' => $student->id,
            'dosen_wali_id' => $dosenWaliA->id,
            'status_perwalian' => 'belum',
            'sks_diambil' => 0,
        ]);

        // 2. Mahasiswa langsung melihat Dosen Wali yang ditetapkan Admin di Beranda & Bimbingan Akademik
        $this->actingAs($student)->get(route('mahasiswa.beranda'))
            ->assertStatus(200)
            ->assertSee('Dr. Wali Penetapan Admin PSTI')
            ->assertSee('Belum Ada Draf Skripsi')
            ->assertSee('0 dari 100%')
            ->assertDontSee('Prof. Dr. Ir. Ahmad Rizal, M.T.');

        $this->actingAs($student)->get(route('mahasiswa.bimbingan-akademik'))
            ->assertStatus(200)
            ->assertSee('Dr. Wali Penetapan Admin PSTI')
            ->assertSee('Dosen Pembimbing Akademik (DPA):')
            ->assertSee('0.00')
            ->assertSee('0 SKS Lulus')
            ->assertDontSee('Pilih Dosen Wali Akademik');

        // 3. Dosen Wali & Admin daftar akun langsung melihat relasi DPA ini
        $this->actingAs($dosenWaliA)->get(route('dosen.bimbingan.mahasiswa'))
            ->assertStatus(200)
            ->assertSee('Salsa Mahasiswa Baru');

        $this->actingAs($admin)->get(route('admin.users.index', ['search' => 'salsa_dpa']))
            ->assertStatus(200)
            ->assertSee('DPA:')
            ->assertSee('Dr. Wali Penetapan Admin PSTI');

        // 4. Mahasiswa mengajukan bimbingan akademik tanpa harus memilih Dosen Wali lagi
        $this->actingAs($student)->post(route('mahasiswa.bimbingan-akademik.pengajuan'), [
            'kategori_topik' => 'Konsultasi KRS Semester 1 PSTI',
            'detail_agenda' => 'Meminta arahan pengambilan KRS awal.',
        ])->assertRedirect();

        $this->assertDatabaseHas('konsultasi_akademiks', [
            'mahasiswa_id' => $student->id,
            'dosen_wali_id' => $dosenWaliA->id,
            'topik' => 'Konsultasi KRS Semester 1 PSTI',
        ]);

        // 5. Admin mengubah Dosen Wali lewat Edit Akun -> langsung tersinkron
        $this->actingAs($admin)->get(route('admin.users.edit', $student))
            ->assertStatus(200)
            ->assertSee('Dosen Pembimbing Akademik (Dosen Wali)');

        $this->actingAs($admin)->put(route('admin.users.update', $student), [
            'name' => 'Salsa Mahasiswa Baru',
            'email' => 'salsa_dpa@simbim.ac.id',
            'nomor_induk' => '2508107000123',
            'role' => 'mahasiswa',
            'dosen_wali_id' => $dosenWaliB->id,
        ])->assertRedirect(route('admin.users.index'));

        $student->refresh();
        $this->assertSame($dosenWaliB->id, $student->dosen_wali_id);
        $this->assertDatabaseHas('perwalian_mahasiswas', [
            'mahasiswa_id' => $student->id,
            'dosen_wali_id' => $dosenWaliB->id,
        ]);

        $this->actingAs($student)->get(route('mahasiswa.beranda'))
            ->assertStatus(200)
            ->assertSee('Dr. Wali Pengganti PSTI')
            ->assertDontSee('Dr. Wali Penetapan Admin PSTI');
    }
}
