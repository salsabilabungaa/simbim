<?php

use App\Http\Controllers\Admin\BerandaController as AdminBerandaController;
use App\Http\Controllers\Admin\MonitoringController as AdminMonitoringController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DewanSkripsi\DewanSkripsiController;
use App\Http\Controllers\Dosen\BerandaController as DosenBerandaController;
use App\Http\Controllers\Dosen\MahasiswaBimbinganController;
use App\Http\Controllers\Dosen\PengajuanBimbinganController;
use App\Http\Controllers\Dosen\RiwayatBimbinganController as DosenRiwayatBimbinganController;
use App\Http\Controllers\Dosen\SkripsiRevisiController as DosenSkripsiRevisiController;
use App\Http\Controllers\Gkm\BerandaController as GkmBerandaController;
use App\Http\Controllers\Gkm\KonsultasiController as GkmKonsultasiController;
use App\Http\Controllers\Gkm\LaporanAkreditasiController as GkmLaporanAkreditasiController;
use App\Http\Controllers\Mahasiswa\BerandaController;
use App\Http\Controllers\Mahasiswa\BimbinganAkademikController;
use App\Http\Controllers\Mahasiswa\BimbinganSkripsiController;
use App\Http\Controllers\Mahasiswa\RiwayatBimbinganController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Root URL: Langsung diarahkan ke login jika belum login, atau ke dashboard role masing-masing jika sudah login
Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    $role = auth()->user()->role;

    return match ($role) {
        'admin' => redirect()->route('admin.beranda'),
        'dosen' => redirect()->route('dosen.beranda'),
        'dewan_skripsi' => redirect()->route('dewan-skripsi.beranda'),
        'gkm' => redirect()->route('gkm.beranda'),
        default => redirect()->route('mahasiswa.beranda'),
    };
});

// Authentication Routes (Hanya untuk tamu / yang belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Logout Route (Hanya untuk user yang login)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Admin Routes: Wajib login dengan peran admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // 1. Beranda Admin
    Route::get('/beranda', [AdminBerandaController::class, 'index'])->name('beranda');

    // 2. Manajemen Pengguna (Tambah Akun Manual & CRUD)
    Route::get('/users/import', [AdminUserController::class, 'importForm'])->name('users.import-form');
    Route::post('/users/import', [AdminUserController::class, 'import'])->name('users.import');
    Route::get('/users/template/{format}', [AdminUserController::class, 'downloadTemplate'])->name('users.template');
    Route::resource('users', AdminUserController::class)->except(['show']);

    // 3. Hak Akses Global: Pemantauan Data 4 Role
    Route::prefix('monitoring')->name('monitoring.')->group(function () {
        Route::get('/mahasiswa', [AdminMonitoringController::class, 'mahasiswa'])->name('mahasiswa');
        Route::get('/dosen', [AdminMonitoringController::class, 'dosen'])->name('dosen');
        Route::get('/dewan-skripsi', [AdminMonitoringController::class, 'dewanSkripsi'])->name('dewan-skripsi');
        Route::get('/gkm', [AdminMonitoringController::class, 'gkm'])->name('gkm');
    });
});

// Alias rute beranda mahasiswa untuk backward compatibility
Route::get('/mahasiswa/dashboard', [BerandaController::class, 'index'])->name('berandaMahasiswa')->middleware(['auth', 'role:mahasiswa']);

// Protected Mahasiswa Routes: Wajib login dengan peran mahasiswa (atau admin via hak akses global)
Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');
    Route::get('/bimbingan-akademik', [BimbinganAkademikController::class, 'index'])->name('bimbingan-akademik');
    Route::post('/bimbingan-akademik/pengajuan', [BimbinganAkademikController::class, 'storePengajuan'])->name('bimbingan-akademik.pengajuan');
    Route::get('/bimbingan-skripsi', [BimbinganSkripsiController::class, 'index'])->name('bimbingan-skripsi');
    Route::post('/bimbingan-skripsi/dokumen', [BimbinganSkripsiController::class, 'storeInitialDraft'])->name('bimbingan-skripsi.store-awal');
    Route::post('/bimbingan-skripsi/dokumen/{dokumen}/komentar', [BimbinganSkripsiController::class, 'storeKomentar'])->name('bimbingan-skripsi.komentar.store');
    Route::post('/bimbingan-skripsi/dokumen/{dokumen}/diskusi', [BimbinganSkripsiController::class, 'storeDiskusi'])->name('bimbingan-skripsi.diskusi.store');
    Route::post('/bimbingan-skripsi/dokumen/{dokumen}/upload', [BimbinganSkripsiController::class, 'uploadRevision'])->name('bimbingan-skripsi.upload');
    Route::get('/bimbingan-skripsi/dokumen/{dokumen}/download', [BimbinganSkripsiController::class, 'downloadFile'])->name('bimbingan-skripsi.download');
    Route::get('/bimbingan-skripsi/dokumen/{dokumen}/preview', [BimbinganSkripsiController::class, 'previewFile'])->name('bimbingan-skripsi.preview');
    Route::get('/riwayat-bimbingan', [RiwayatBimbinganController::class, 'index'])->name('riwayat-bimbingan');
});

// Protected Dosen Routes: Wajib login dengan peran dosen (atau admin via hak akses global)
Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
    Route::get('/beranda', [DosenBerandaController::class, 'index'])->name('beranda');

    // Bagian Bimbingan
    Route::prefix('bimbingan')->name('bimbingan.')->group(function () {
        Route::get('/mahasiswa', [MahasiswaBimbinganController::class, 'index'])->name('mahasiswa');
        Route::get('/pengajuan', [PengajuanBimbinganController::class, 'index'])->name('pengajuan');

        // Sesi Diskusi Revisi Skripsi & Komentar Dokumen Langsung
        Route::get('/skripsi/{dokumen}', [DosenSkripsiRevisiController::class, 'show'])->name('skripsi.show');
        Route::post('/skripsi/{dokumen}/komentar', [DosenSkripsiRevisiController::class, 'storeKomentar'])->name('skripsi.komentar.store');
        Route::patch('/skripsi/{dokumen}/komentar/{komentar}/resolve', [DosenSkripsiRevisiController::class, 'toggleResolveKomentar'])->name('skripsi.komentar.resolve');
        Route::post('/skripsi/{dokumen}/diskusi', [DosenSkripsiRevisiController::class, 'storeDiskusi'])->name('skripsi.diskusi.store');
        Route::patch('/skripsi/{dokumen}/status', [DosenSkripsiRevisiController::class, 'updateStatus'])->name('skripsi.status.update');
        Route::get('/skripsi/{dokumen}/download', [DosenSkripsiRevisiController::class, 'downloadFile'])->name('skripsi.download');
        Route::get('/skripsi/{dokumen}/preview', [DosenSkripsiRevisiController::class, 'previewFile'])->name('skripsi.preview');
    });

    // Riwayat Bimbingan
    Route::get('/riwayat-bimbingan', [DosenRiwayatBimbinganController::class, 'index'])->name('riwayat-bimbingan');
});

// Protected Dewan Skripsi Routes: Wajib login dengan peran dewan_skripsi (atau admin via hak akses global)
Route::middleware(['auth', 'role:dewan_skripsi'])->prefix('dewan-skripsi')->name('dewan-skripsi.')->group(function () {
    // 1. Dasbor Progres Skripsi
    Route::get('/', fn () => redirect()->route('dewan-skripsi.beranda'));
    Route::get('/beranda', [DewanSkripsiController::class, 'dashboard'])->name('beranda');
    Route::get('/dashboard', [DewanSkripsiController::class, 'dashboard'])->name('dashboard');

    // 2. Plotting Dosen & Jadwal
    Route::get('/plotting', [DewanSkripsiController::class, 'plotting'])->name('plotting');
    Route::post('/plotting/{skripsi}', [DewanSkripsiController::class, 'updatePlotting'])->name('plotting.update');

    // 3. Validasi Pendaftaran Sidang (Approval)
    Route::get('/validasi-sidang', [DewanSkripsiController::class, 'validasiSidang'])->name('validasi-sidang');
    Route::post('/validasi-sidang/{skripsi}/approve', [DewanSkripsiController::class, 'approveSidang'])->name('validasi-sidang.approve');
    Route::post('/validasi-sidang/{skripsi}/reject', [DewanSkripsiController::class, 'rejectSidang'])->name('validasi-sidang.reject');

    // 4. Verifikasi Berkas Pasca-Sidang
    Route::get('/verifikasi-pasca-sidang', [DewanSkripsiController::class, 'verifikasiPascaSidang'])->name('verifikasi-pasca-sidang');
    Route::post('/verifikasi-pasca-sidang/{skripsi}/nilai', [DewanSkripsiController::class, 'simpanNilai'])->name('verifikasi-pasca-sidang.nilai');
    Route::post('/verifikasi-pasca-sidang/{skripsi}/lulus', [DewanSkripsiController::class, 'keluarkanStatusLulus'])->name('verifikasi-pasca-sidang.lulus');
    Route::get('/verifikasi-pasca-sidang/{skripsi}/surat-keterangan', [DewanSkripsiController::class, 'cetakSuratKeterangan'])->name('cetak-surat');
});

// Protected GKM Routes: Wajib login dengan peran gkm (atau admin via hak akses global)
Route::middleware(['auth', 'role:gkm'])->prefix('gkm')->name('gkm.')->group(function () {
    Route::get('/beranda', [GkmBerandaController::class, 'index'])->name('beranda');
    Route::get('/riwayat-konsultasi', [GkmKonsultasiController::class, 'index'])->name('riwayat-konsultasi');
    Route::get('/riwayat-konsultasi/{konsultasi}', [GkmKonsultasiController::class, 'show'])->name('riwayat-konsultasi.show');
    Route::get('/ekspor/pdf', [GkmLaporanAkreditasiController::class, 'exportPdf'])->name('ekspor.pdf');
    Route::get('/ekspor/excel', [GkmLaporanAkreditasiController::class, 'exportExcel'])->name('ekspor.excel');
});

// Protected Profile Routes: Dapat diakses oleh seluruh peran yang telah login (Admin, Dosen, Dewan Skripsi, GKM, Mahasiswa)
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/update', [ProfileController::class, 'update'])->name('update');
});
