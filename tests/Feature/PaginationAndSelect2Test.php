<?php

namespace Tests\Feature;

use App\Models\KonsultasiAkademik;
use App\Models\PerwalianMahasiswa;
use App\Models\SkripsiDokumen;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class PaginationAndSelect2Test extends TestCase
{
    use DatabaseTransactions;

    protected function createMockUser(string $role, int $id = 999): User
    {
        $user = new User;
        $user->id = $id;
        $user->name = 'Mock '.ucfirst($role);
        $user->email = "mock_{$role}@simbim.ac.id";
        $user->role = $role;
        $user->nomor_induk = 'MOCK_'.strtoupper($role);
        $user->program_studi = config('simbim.prodi', 'Pendidikan Sistem dan Teknologi Informasi (PSTI)');

        return $user;
    }

    public function test_semua_tabel_admin_dan_dewan_skripsi_menggunakan_paginasi_6_dan_select2(): void
    {
        $admin = User::factory()->admin()->create();
        $dosen = User::factory()->dosen()->create([
            'name' => 'Dr. Uji Paginasi, M.T.',
        ]);

        // Pastikan total pengguna dan mahasiswa melampaui 6 baris agar halaman 2 terbentuk
        $createdStudents = [];
        for ($i = 1; $i <= 8; $i++) {
            $createdStudents[] = User::factory()->mahasiswa()->create([
                'name' => 'Mahasiswa Paginasi '.$i,
                'nomor_induk' => '24081070900'.$i,
                'dosen_wali_id' => $dosen->id,
            ]);
        }

        // 1. Kelola Akun Pengguna (server-side paginate(6))
        $resUsersPage1 = $this->actingAs($admin)->get(route('admin.users.index'));
        $resUsersPage1->assertStatus(200);
        $resUsersPage1->assertSee('assets/js/simbim-ui.js');
        $usersPaginator = $resUsersPage1->viewData('users');
        $this->assertInstanceOf(LengthAwarePaginator::class, $usersPaginator);
        $this->assertSame(6, $usersPaginator->perPage());
        $this->assertCount(6, $usersPaginator->items());

        $resUsersPage2 = $this->actingAs($admin)->get(route('admin.users.index', ['page' => 2]));
        $resUsersPage2->assertStatus(200);
        $usersPaginator2 = $resUsersPage2->viewData('users');
        $this->assertSame(2, $usersPaginator2->currentPage());

        // 2. Monitoring Global Mahasiswa: perPage=6 & akun mahasiswa baru tampil pada halaman 1
        $resMonMhs1 = $this->actingAs($admin)->get(route('admin.monitoring.mahasiswa'));
        $resMonMhs1->assertStatus(200);
        $monMhsPaginator = $resMonMhs1->viewData('daftarMahasiswa');
        $this->assertInstanceOf(LengthAwarePaginator::class, $monMhsPaginator);
        $this->assertSame(6, $monMhsPaginator->perPage());
        $this->assertGreaterThan(6, $monMhsPaginator->total());
        $this->assertCount(6, $monMhsPaginator->items());

        $resMonMhs2 = $this->actingAs($admin)->get(route('admin.monitoring.mahasiswa', ['page' => 2]));
        $resMonMhs2->assertStatus(200);
        $this->assertSame(2, $resMonMhs2->viewData('daftarMahasiswa')->currentPage());

        // 3. Monitoring Global Dosen, Dewan Skripsi, dan GKM: perPage=6
        foreach ([
            ['route' => 'admin.monitoring.dosen', 'key' => 'daftarDosen'],
            ['route' => 'admin.monitoring.dewan-skripsi', 'key' => 'agendaSidang'],
            ['route' => 'admin.monitoring.gkm', 'key' => 'indikatorMutu'],
        ] as $target) {
            $res = $this->actingAs($admin)->get(route($target['route']));
            $res->assertStatus(200);
            $paginator = $res->viewData($target['key']);
            $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
            $this->assertSame(6, $paginator->perPage());
        }

        // 4. Form Create & Edit User menggunakan Select2
        $resCreate = $this->actingAs($admin)->get(route('admin.users.create'));
        $resCreate->assertStatus(200);
        $resCreate->assertSee('data-control="select2"', false);
        $resCreate->assertSee('assets/js/simbim-ui.js');

        $targetStudent = $createdStudents[0];
        $resEdit = $this->actingAs($admin)->get(route('admin.users.edit', $targetStudent));
        $resEdit->assertStatus(200);
        $resEdit->assertSee('data-control="select2"', false);

        // 5. Portal Dewan Skripsi menggunakan paginasi 6
        $dewan = $this->createMockUser('dewan_skripsi', 998);
        $resDewan = $this->actingAs($dewan)->get(route('dewan-skripsi.beranda'));
        $resDewan->assertStatus(200);
        $resDewan->assertSee('assets/js/simbim-ui.js');
        $jadwalPaginator = $resDewan->viewData('jadwal_sidang');
        $this->assertInstanceOf(LengthAwarePaginator::class, $jadwalPaginator);
        $this->assertSame(6, $jadwalPaginator->perPage());
    }

    public function test_halaman_gkm_menggunakan_paginasi_6_dan_select2(): void
    {
        $gkm = User::factory()->gkm()->create();
        $dosen = User::factory()->dosen()->create();

        for ($i = 1; $i <= 8; $i++) {
            $mhs = User::factory()->mahasiswa()->create([
                'name' => 'GKM Paginasi Mhs '.$i,
                'nomor_induk' => '23081070800'.$i,
                'dosen_wali_id' => $dosen->id,
            ]);

            $perwalian = PerwalianMahasiswa::factory()->create([
                'mahasiswa_id' => $mhs->id,
                'dosen_wali_id' => $dosen->id,
                'angkatan' => '2023',
            ]);

            KonsultasiAkademik::factory()->create([
                'perwalian_mahasiswa_id' => $perwalian->id,
                'mahasiswa_id' => $mhs->id,
                'dosen_wali_id' => $dosen->id,
                'topik' => 'Konsultasi Paginasi GKM Ke-'.$i,
            ]);
        }

        // Beranda GKM: 6 baris per halaman + Select2 di filter & modal ekspor
        $resBeranda1 = $this->actingAs($gkm)->get(route('gkm.beranda'));
        $resBeranda1->assertStatus(200);
        $resBeranda1->assertSee('assets/js/simbim-ui.js');
        $resBeranda1->assertSee('data-control="select2"', false);
        $resBeranda1->assertSee('data-dropdown-parent="#modal_ekspor_akreditasi"', false);

        $perwalianPaginator = $resBeranda1->viewData('daftarPerwalian');
        $this->assertInstanceOf(LengthAwarePaginator::class, $perwalianPaginator);
        $this->assertSame(6, $perwalianPaginator->perPage());
        $this->assertCount(6, $perwalianPaginator->items());

        $resBeranda2 = $this->actingAs($gkm)->get(route('gkm.beranda', ['page' => 2]));
        $resBeranda2->assertStatus(200);
        $this->assertSame(2, $resBeranda2->viewData('daftarPerwalian')->currentPage());

        // Riwayat Konsultasi GKM: 6 baris per halaman + 4 filter Select2
        $resKonsultasi1 = $this->actingAs($gkm)->get(route('gkm.riwayat-konsultasi'));
        $resKonsultasi1->assertStatus(200);
        $resKonsultasi1->assertSee('data-control="select2"', false);

        $konsultasiPaginator = $resKonsultasi1->viewData('daftarKonsultasi');
        $this->assertInstanceOf(LengthAwarePaginator::class, $konsultasiPaginator);
        $this->assertSame(6, $konsultasiPaginator->perPage());
        $this->assertCount(6, $konsultasiPaginator->items());

        $resKonsultasi2 = $this->actingAs($gkm)->get(route('gkm.riwayat-konsultasi', ['page' => 2]));
        $resKonsultasi2->assertStatus(200);
        $this->assertSame(2, $resKonsultasi2->viewData('daftarKonsultasi')->currentPage());
    }

    public function test_halaman_mahasiswa_dan_dosen_menggunakan_paginasi_6_dan_select2(): void
    {
        $dosen = User::factory()->dosen()->create([
            'name' => 'Dr. Pembimbing Paginasi, M.Kom.',
        ]);
        $mahasiswa = User::factory()->mahasiswa()->create([
            'name' => 'Mahasiswa Uji Paginasi',
            'nomor_induk' => '2408107077777',
            'dosen_wali_id' => $dosen->id,
        ]);

        $perwalian = PerwalianMahasiswa::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_wali_id' => $dosen->id,
            'status_perwalian' => 'proses',
        ]);

        // Buat 8 sesi konsultasi akademik agar tabel bimbingan akademik membentuk halaman 2
        for ($i = 1; $i <= 8; $i++) {
            KonsultasiAkademik::factory()->create([
                'perwalian_mahasiswa_id' => $perwalian->id,
                'mahasiswa_id' => $mahasiswa->id,
                'dosen_wali_id' => $dosen->id,
                'topik' => 'Sesi Bimbingan Akademik #'.$i,
                'tanggal_konsultasi' => now()->subDays($i),
            ]);
        }

        $dokumen = SkripsiDokumen::factory()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_pembimbing_id' => $dosen->id,
            'judul_skripsi' => 'Sistem Uji Paginasi dan Select2 PSTI UPI',
        ]);

        // 1. Mahasiswa Bimbingan Akademik: paginator 6 & Select2 di modal pengajuan
        $resMhsAkad1 = $this->actingAs($mahasiswa)->get(route('mahasiswa.bimbingan-akademik'));
        $resMhsAkad1->assertStatus(200);
        $resMhsAkad1->assertSee('assets/js/simbim-ui.js');
        $resMhsAkad1->assertSee('data-control="select2"', false);
        $resMhsAkad1->assertSee('data-dropdown-parent="#modal_ajukan_akademik"', false);
        $resMhsAkad1->assertSee('Total 8 Sesi');

        $akadPaginator = $resMhsAkad1->viewData('riwayat_bimbingan');
        $this->assertInstanceOf(LengthAwarePaginator::class, $akadPaginator);
        $this->assertSame(6, $akadPaginator->perPage());
        $this->assertSame(8, $akadPaginator->total());
        $this->assertCount(6, $akadPaginator->items());

        $resMhsAkad2 = $this->actingAs($mahasiswa)->get(route('mahasiswa.bimbingan-akademik', ['page' => 2]));
        $resMhsAkad2->assertStatus(200);
        $this->assertCount(2, $resMhsAkad2->viewData('riwayat_bimbingan')->items());

        // 2. Mahasiswa Bimbingan Skripsi: paginator 6 & Select2 di modal pengajuan
        $resMhsSkripsi = $this->actingAs($mahasiswa)->get(route('mahasiswa.bimbingan-skripsi'));
        $resMhsSkripsi->assertStatus(200);
        $resMhsSkripsi->assertSee('data-control="select2"', false);
        $resMhsSkripsi->assertSee('data-dropdown-parent="#modal_ajukan_skripsi"', false);
        $skripsiPaginator = $resMhsSkripsi->viewData('riwayat_bimbingan');
        $this->assertInstanceOf(LengthAwarePaginator::class, $skripsiPaginator);
        $this->assertSame(6, $skripsiPaginator->perPage());

        // 3. Mahasiswa Riwayat Bimbingan: paginasi 6 client-side & filter Select2
        $resMhsRiwayat = $this->actingAs($mahasiswa)->get(route('mahasiswa.riwayat-bimbingan'));
        $resMhsRiwayat->assertStatus(200);
        $resMhsRiwayat->assertSee('data-simbim-paginate="6"', false);
        $resMhsRiwayat->assertSee('id="pager_mhs_riwayat"', false);
        $resMhsRiwayat->assertSee('data-control="select2"', false);

        // 4. Seluruh halaman tabel & dropdown di portal Dosen
        $resDosenBeranda = $this->actingAs($dosen)->get(route('dosen.beranda'));
        $resDosenBeranda->assertStatus(200);
        $resDosenBeranda->assertSee('assets/js/simbim-ui.js');
        $resDosenBeranda->assertSee('id="tabel_dosen_skripsi"', false);
        $resDosenBeranda->assertSee('id="tabel_dosen_perwalian"', false);
        $resDosenBeranda->assertSee('data-simbim-paginate="6"', false);
        $resDosenBeranda->assertSee('data-dropdown-parent="#modal_edit_profil_dosen"', false);

        $resDosenMhs = $this->actingAs($dosen)->get(route('dosen.bimbingan.mahasiswa'));
        $resDosenMhs->assertStatus(200);
        $resDosenMhs->assertSee('id="table_mahasiswa"', false);
        $resDosenMhs->assertSee('data-simbim-paginate="6"', false);
        $resDosenMhs->assertSee('id="pager_dosen_mahasiswa"', false);
        $resDosenMhs->assertSee('data-dropdown-parent="#modal_review_draf"', false);

        $resDosenPengajuan = $this->actingAs($dosen)->get(route('dosen.bimbingan.pengajuan'));
        $resDosenPengajuan->assertStatus(200);
        $resDosenPengajuan->assertSee('id="table_pengajuan"', false);
        $resDosenPengajuan->assertSee('data-simbim-paginate="6"', false);
        $resDosenPengajuan->assertSee('id="pager_dosen_pengajuan"', false);

        $resDosenRiwayat = $this->actingAs($dosen)->get(route('dosen.riwayat-bimbingan'));
        $resDosenRiwayat->assertStatus(200);
        $resDosenRiwayat->assertSee('id="tabel_riwayat_dosen"', false);
        $resDosenRiwayat->assertSee('data-simbim-paginate="6"', false);
        $resDosenRiwayat->assertSee('id="pager_riwayat_dosen"', false);
        $resDosenRiwayat->assertSee('data-dropdown-parent="#modal_cetak_logbook"', false);

        $resDiskusi = $this->actingAs($dosen)->get(route('dosen.bimbingan.skripsi.show', $dokumen));
        $resDiskusi->assertStatus(200);
        $resDiskusi->assertSee('data-control="select2"', false);
        $resDiskusi->assertSee('data-dropdown-parent="#floating_doc_comment_box"', false);
        $resDiskusi->assertSee('data-dropdown-parent="#collapse_manual_comment_form"', false);
    }
}
