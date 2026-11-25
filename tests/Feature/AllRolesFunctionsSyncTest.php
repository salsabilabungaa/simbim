<?php

namespace Tests\Feature;

use App\Models\KonsultasiAkademik;
use App\Models\Skripsi;
use App\Models\SkripsiDokumen;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AllRolesFunctionsSyncTest extends TestCase
{
    /**
     * 1. Jalankan seluruh fungsi pada role Mahasiswa dan sinkronkan ke database.
     */
    public function test_mahasiswa_all_functions_and_database_sync(): void
    {
        Storage::fake('public');
        $mahasiswa = User::where('email', 'mahasiswa@simbim.ac.id')->firstOrFail();
        $dosen = User::where('role', 'dosen')->firstOrFail();

        // Fungsi 1.1: Akses Dashboard Mahasiswa
        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.beranda'));
        $response->assertStatus(200);

        // Fungsi 1.2: Bimbingan Akademik (View & Ajukan Konsultasi ke Database)
        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.bimbingan-akademik'));
        $response->assertStatus(200);

        $response = $this->actingAs($mahasiswa)->post(route('mahasiswa.bimbingan-akademik.pengajuan'), [
            'dosen_wali_id' => $dosen->id,
            'kategori_topik' => 'Rencana Studi Semester Ganjil 2026/2027',
            'detail_agenda' => 'Konsultasi pemilihan 24 SKS mata kuliah wajib dan pilihan MBKM.',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('konsultasi_akademiks', [
            'mahasiswa_id' => $mahasiswa->id,
            'topik' => 'Rencana Studi Semester Ganjil 2026/2027',
        ]);

        // Fungsi 1.3: Bimbingan Skripsi (View, Upload Dokumen, Kirim Diskusi, Tambah Komentar)
        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.bimbingan-skripsi'));
        $response->assertStatus(200);

        $dokumen = SkripsiDokumen::where('mahasiswa_id', $mahasiswa->id)->first();
        if (! $dokumen) {
            $dokumen = SkripsiDokumen::create([
                'mahasiswa_id' => $mahasiswa->id,
                'pembimbing_id' => $dosen->id,
                'judul_skripsi' => 'Pengembangan Sistem Cerdas Terdistribusi',
                'bidang_ilmu' => 'Artificial Intelligence',
                'nama_file' => 'draft_bab1_andi.docx',
                'path_dokumen' => 'dokumen/draft_bab1_andi.docx',
                'versi' => 1,
                'status_draf' => 'Diajukan',
            ]);
        }

        // Upload revisi dokumen baru
        $file = UploadedFile::fake()->create('revisi_bab2_andi.pdf', 500, 'application/pdf');
        $response = $this->actingAs($mahasiswa)->post(route('mahasiswa.bimbingan-skripsi.upload', $dokumen), [
            'file_draft' => $file,
            'catatan_mahasiswa' => 'Pembaruan metodologi penelitian pada Bab 3.',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('skripsi_dokumens', [
            'id' => $dokumen->id,
            'status_draf' => 'Menunggu Review Dosen',
        ]);

        // Kirim pesan diskusi dokumen
        $response = $this->actingAs($mahasiswa)->postJson(route('mahasiswa.bimbingan-skripsi.diskusi.store', $dokumen), [
            'pesan' => 'Selamat siang Bu, saya sudah memperbarui diagram use case sesuai arahan.',
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('skripsi_diskusi_pesans', [
            'skripsi_dokumen_id' => $dokumen->id,
            'user_id' => $mahasiswa->id,
            'pesan' => 'Selamat siang Bu, saya sudah memperbarui diagram use case sesuai arahan.',
        ]);

        // Tambah komentar dokumen
        $response = $this->actingAs($mahasiswa)->postJson(route('mahasiswa.bimbingan-skripsi.komentar.store', $dokumen), [
            'lokasi_dokumen' => 'Bab 3 - Metodologi',
            'kutipan_teks' => 'Desain arsitektur database relasional',
            'isi_komentar' => 'Apakah normalisasi 3NF di sini sudah memadai Bu?',
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('skripsi_dokumen_komentars', [
            'skripsi_dokumen_id' => $dokumen->id,
            'user_id' => $mahasiswa->id,
            'lokasi_dokumen' => 'Bab 3 - Metodologi',
        ]);

        // Fungsi 1.4: Riwayat Bimbingan Mahasiswa
        $response = $this->actingAs($mahasiswa)->get(route('mahasiswa.riwayat-bimbingan'));
        $response->assertStatus(200);

        // Fungsi 1.5: Edit & Update Profil Mahasiswa
        $response = $this->actingAs($mahasiswa)->get(route('profile.edit'));
        $response->assertStatus(200);

        $response = $this->actingAs($mahasiswa)->put(route('profile.update'), [
            'name' => 'Andi Pratama S.Kom Updated',
            'email' => 'mahasiswa@simbim.ac.id',
            'nim' => $mahasiswa->nomor_induk,
            'program_studi' => 'Pendidikan Sistem dan Teknologi Informasi (PSTI)',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $mahasiswa->id,
            'name' => 'Andi Pratama S.Kom Updated',
        ]);

        // Kembalikan ke nama kanonik seeder
        $mahasiswa->update(['name' => 'Andi Pratama']);
    }

    /**
     * 2. Jalankan seluruh fungsi pada role Dosen dan sinkronkan ke database.
     */
    public function test_dosen_all_functions_and_database_sync(): void
    {
        $dosen = User::where('email', 'dosen@simbim.ac.id')->firstOrFail();
        $dokumen = SkripsiDokumen::firstOrFail();

        // Pastikan dokumen terhubung ke dosen ini agar otorisasi show terpenuhi
        $dokumen->update(['pembimbing_id' => $dosen->id]);

        // Fungsi 2.1: Akses Dashboard Dosen
        $response = $this->actingAs($dosen)->get(route('dosen.beranda'));
        $response->assertStatus(200);

        // Fungsi 2.2: Daftar Mahasiswa Bimbingan & Pengajuan
        $response = $this->actingAs($dosen)->get(route('dosen.bimbingan.mahasiswa'));
        $response->assertStatus(200);

        $response = $this->actingAs($dosen)->get(route('dosen.bimbingan.pengajuan'));
        $response->assertStatus(200);

        // Fungsi 2.3: Buka Ruang Sesi Diskusi Revisi Skripsi
        $response = $this->actingAs($dosen)->get(route('dosen.bimbingan.skripsi.show', $dokumen));
        $response->assertStatus(200);

        // Fungsi 2.4: Kirim Komentar Langsung Dosen
        $response = $this->actingAs($dosen)->postJson(route('dosen.bimbingan.skripsi.komentar.store', $dokumen), [
            'bagian' => 'Bab 2 - Tinjauan Pustaka',
            'teks_rujukan' => 'Perbandingan algoritma machine learning',
            'isi_komentar' => 'Tambahkan paper acuan terindeks Scopus tahun 2025/2026.',
        ]);
        $response->assertStatus(201);
        $komentarId = json_decode($response->getContent(), true)['komentar']['id'];
        $this->assertDatabaseHas('skripsi_dokumen_komentars', [
            'id' => $komentarId,
            'user_id' => $dosen->id,
        ]);

        // Fungsi 2.5: Tandai Komentar Selesai (Resolve)
        $response = $this->actingAs($dosen)->patchJson(route('dosen.bimbingan.skripsi.komentar.resolve', [$dokumen, $komentarId]));
        $response->assertStatus(200);
        $this->assertDatabaseHas('skripsi_dokumen_komentars', [
            'id' => $komentarId,
            'is_resolved' => true,
        ]);

        // Fungsi 2.6: Kirim Pesan Diskusi Dosen
        $response = $this->actingAs($dosen)->postJson(route('dosen.bimbingan.skripsi.diskusi.store', $dokumen), [
            'pesan' => 'Bagus sekali revisinya, silakan lanjutkan ke Bab 4.',
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('skripsi_diskusi_pesans', [
            'skripsi_dokumen_id' => $dokumen->id,
            'user_id' => $dosen->id,
            'pesan' => 'Bagus sekali revisinya, silakan lanjutkan ke Bab 4.',
        ]);

        // Fungsi 2.7: Update Status Revisi Dokumen
        $response = $this->actingAs($dosen)->patchJson(route('dosen.bimbingan.skripsi.status.update', $dokumen), [
            'status_draf' => 'Siap Sidang Skripsi',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('skripsi_dokumens', [
            'id' => $dokumen->id,
            'status_draf' => 'Siap Sidang Skripsi',
        ]);

        // Fungsi 2.8: Riwayat Bimbingan Dosen
        $response = $this->actingAs($dosen)->get(route('dosen.riwayat-bimbingan'));
        $response->assertStatus(200);

        // Fungsi 2.9: Update Profil Dosen
        $originalDosenName = $dosen->name;
        $response = $this->actingAs($dosen)->put(route('profile.update'), [
            'name' => 'Dr. Siti Rahmawati, M.Kom. Updated',
            'email' => 'dosen@simbim.ac.id',
            'nip' => $dosen->nomor_induk,
            'program_studi' => 'Pendidikan Sistem dan Teknologi Informasi (PSTI)',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $dosen->id,
            'name' => 'Dr. Siti Rahmawati, M.Kom. Updated',
        ]);
        $dosen->update(['name' => $originalDosenName]);
    }

    /**
     * 3. Jalankan seluruh fungsi pada role Dewan Skripsi dan sinkronkan ke database.
     */
    public function test_dewan_skripsi_all_functions_and_database_sync(): void
    {
        $dewan = User::where('email', 'dewanskripsi@simbim.ac.id')->firstOrFail();
        $dosenList = User::where('role', 'dosen')->limit(4)->get();
        $skripsi = Skripsi::firstOrFail();

        // Fungsi 3.1: Dasbor Progres Skripsi
        $response = $this->actingAs($dewan)->get(route('dewan-skripsi.dashboard'));
        $response->assertStatus(200);

        // Fungsi 3.2: Plotting Dosen & Jadwal Sidang ke Database
        $response = $this->actingAs($dewan)->get(route('dewan-skripsi.plotting'));
        $response->assertStatus(200);

        $response = $this->actingAs($dewan)->post(route('dewan-skripsi.plotting.update', $skripsi), [
            'pembimbing_1_id' => $dosenList[0]->id,
            'pembimbing_2_id' => $dosenList[1]->id ?? null,
            'penguji_1_id' => $dosenList[2]->id ?? null,
            'penguji_2_id' => $dosenList[3]->id ?? null,
            'tanggal_sidang' => '2026-11-25',
            'jam_sidang' => '09:00',
            'ruangan_sidang' => 'Ruang Sidang Utama Lantai 3 PSTI',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('skripsis', [
            'id' => $skripsi->id,
            'pembimbing_1_id' => $dosenList[0]->id,
            'ruangan_sidang' => 'Ruang Sidang Utama Lantai 3 PSTI',
        ]);

        // Fungsi 3.3: Validasi Pendaftaran Sidang (ACC & Tolak)
        $response = $this->actingAs($dewan)->get(route('dewan-skripsi.validasi-sidang'));
        $response->assertStatus(200);

        // ACC / Approve Sidang
        $response = $this->actingAs($dewan)->post(route('dewan-skripsi.validasi-sidang.approve', $skripsi));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('skripsis', [
            'id' => $skripsi->id,
            'status_pendaftaran_sidang' => 'Disetujui',
            'tahap' => 'Siap Sidang',
        ]);

        // Tolak / Reject Sidang
        $response = $this->actingAs($dewan)->post(route('dewan-skripsi.validasi-sidang.reject', $skripsi), [
            'catatan_validasi_sidang' => 'Log bimbingan belum mencapai 8 kali sesi.',
        ]);
        $response->assertSessionHas('warning');
        $this->assertDatabaseHas('skripsis', [
            'id' => $skripsi->id,
            'status_pendaftaran_sidang' => 'Ditolak',
        ]);

        // Kembalikan ke tahap Revisi Final agar siap diinput nilai dan diluluskan
        $skripsi->update([
            'tahap' => 'Revisi Final',
            'status_pendaftaran_sidang' => 'Disetujui',
            'status_kelulusan' => 'Belum Lulus',
        ]);

        // Fungsi 3.4: Verifikasi Pasca-Sidang (Input Nilai & Penetapan Lulus)
        $response = $this->actingAs($dewan)->get(route('dewan-skripsi.verifikasi-pasca-sidang'));
        $response->assertStatus(200);

        // Input Rekap Nilai Sidang
        $response = $this->actingAs($dewan)->post(route('dewan-skripsi.verifikasi-pasca-sidang.nilai', $skripsi), [
            'nilai_pembimbing_1' => 88.0,
            'nilai_penguji_1' => 86.0,
            'catatan_dewan' => 'Revisi Bab 5 kesimpulan telah diverifikasi.',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('skripsis', [
            'id' => $skripsi->id,
            'nilai_huruf' => 'A',
        ]);

        // Terbitkan Kelulusan
        $response = $this->actingAs($dewan)->post(route('dewan-skripsi.verifikasi-pasca-sidang.lulus', $skripsi));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('skripsis', [
            'id' => $skripsi->id,
            'status_kelulusan' => 'Lulus/Selesai',
            'tahap' => 'Lulus/Selesai',
        ]);

        // Cetak Surat Keterangan Selesai Sidang
        $response = $this->actingAs($dewan)->get(route('dewan-skripsi.cetak-surat', $skripsi));
        $response->assertStatus(200);
        $response->assertSee('SURAT KETERANGAN LULUS SIDANG SKRIPSI');

        // Fungsi 3.5: Update Profil Dewan Skripsi
        $response = $this->actingAs($dewan)->put(route('profile.update'), [
            'name' => 'Dewan Skripsi Koordinator',
            'email' => 'dewanskripsi@simbim.ac.id',
            'nip' => $dewan->nomor_induk,
            'program_studi' => 'Pendidikan Sistem dan Teknologi Informasi (PSTI)',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $dewan->id,
            'name' => 'Dewan Skripsi Koordinator',
        ]);
        $dewan->update(['name' => 'Dewan Skripsi']);
    }

    /**
     * 4. Jalankan seluruh fungsi pada role GKM dan sinkronkan ke database.
     */
    public function test_gkm_all_functions_and_database_sync(): void
    {
        $gkm = User::where('email', 'gkm@simbim.ac.id')->firstOrFail();
        $konsultasi = KonsultasiAkademik::firstOrFail();

        // Fungsi 4.1: Dasbor Rekap Perwalian GKM
        $response = $this->actingAs($gkm)->get(route('gkm.beranda'));
        $response->assertStatus(200);

        // Fungsi 4.2: Riwayat Konsultasi & Detail Log
        $response = $this->actingAs($gkm)->get(route('gkm.riwayat-konsultasi'));
        $response->assertStatus(200);

        $response = $this->actingAs($gkm)->get(route('gkm.riwayat-konsultasi.show', $konsultasi));
        $response->assertStatus(200);

        // Fungsi 4.3: Ekspor Laporan Akreditasi PDF
        $response = $this->actingAs($gkm)->get(route('gkm.ekspor.pdf'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');

        // Fungsi 4.4: Ekspor Laporan Akreditasi Excel
        $response = $this->actingAs($gkm)->get(route('gkm.ekspor.excel'));
        $response->assertStatus(200);

        // Fungsi 4.5: Update Profil GKM
        $originalGkmName = $gkm->name;
        $response = $this->actingAs($gkm)->put(route('profile.update'), [
            'name' => 'Dr. Ratna Wijaya, M.T. GKM Updated',
            'email' => 'gkm@simbim.ac.id',
            'nip' => $gkm->nomor_induk,
            'program_studi' => 'Pendidikan Sistem dan Teknologi Informasi (PSTI)',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $gkm->id,
            'name' => 'Dr. Ratna Wijaya, M.T. GKM Updated',
        ]);
        $gkm->update(['name' => $originalGkmName]);
    }

    /**
     * 5. Jalankan seluruh fungsi pada role Administrator dan sinkronkan ke database.
     */
    public function test_admin_all_functions_and_database_sync(): void
    {
        $admin = User::where('email', 'admin@simbim.ac.id')->firstOrFail();

        // Fungsi 5.1: Dashboard Administrator
        $response = $this->actingAs($admin)->get(route('admin.beranda'));
        $response->assertStatus(200);

        // Fungsi 5.2: Manajemen User - Create, Store, Update, Destroy
        $response = $this->actingAs($admin)->get(route('admin.users.index'));
        $response->assertStatus(200);

        // Store User Baru
        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Budi Santoso Uji Simpan',
            'email' => 'budisimpan@simbim.ac.id',
            'role' => 'mahasiswa',
            'nomor_induk' => '2108107010999',
            'program_studi' => 'Pendidikan Sistem dan Teknologi Informasi (PSTI)',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'email' => 'budisimpan@simbim.ac.id',
            'nomor_induk' => '2108107010999',
        ]);

        $createdUser = User::where('email', 'budisimpan@simbim.ac.id')->firstOrFail();

        // Update User
        $response = $this->actingAs($admin)->put(route('admin.users.update', $createdUser), [
            'name' => 'Budi Santoso Updated',
            'email' => 'budisimpan@simbim.ac.id',
            'role' => 'mahasiswa',
            'nomor_induk' => '2108107010999',
            'program_studi' => 'Pendidikan Sistem dan Teknologi Informasi (PSTI)',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $createdUser->id,
            'name' => 'Budi Santoso Updated',
        ]);

        // Destroy User
        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $createdUser));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', [
            'id' => $createdUser->id,
        ]);

        // Fungsi 5.3: Monitoring Seluruh Role (Mahasiswa, Dosen, Dewan, GKM)
        $response = $this->actingAs($admin)->get(route('admin.monitoring.mahasiswa'));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get(route('admin.monitoring.dosen'));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get(route('admin.monitoring.dewan-skripsi'));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get(route('admin.monitoring.gkm'));
        $response->assertStatus(200);

        // Fungsi 5.4: Download Template Import Akun
        $response = $this->actingAs($admin)->get(route('admin.users.template', 'csv'));
        $response->assertStatus(200);

        // Fungsi 5.5: Update Profil Admin
        $originalAdminName = $admin->name;
        $response = $this->actingAs($admin)->put(route('profile.update'), [
            'name' => 'Administrator SIMBIM PSTI UPI Super',
            'email' => 'admin@simbim.ac.id',
            'nomor_induk' => $admin->nomor_induk,
            'program_studi' => 'Pendidikan Sistem dan Teknologi Informasi (PSTI)',
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Administrator SIMBIM PSTI UPI Super',
        ]);
        $admin->update(['name' => $originalAdminName]);
    }
}
