<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dosen\BerandaController as DosenBerandaController;
use App\Http\Controllers\Mahasiswa\BerandaController;
use App\Http\Controllers\Mahasiswa\BimbinganAkademikController;
use App\Http\Controllers\Mahasiswa\BimbinganSkripsiController;
use App\Http\Controllers\Mahasiswa\RiwayatBimbinganController;
use Illuminate\Support\Facades\Route;

// Root URL: Langsung diarahkan ke login jika belum login, atau ke dashboard role masing-masing jika sudah login
Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    return auth()->user()->role === 'dosen'
        ? redirect()->route('dosen.beranda')
        : redirect()->route('mahasiswa.beranda');
});

// Authentication Routes (Hanya untuk tamu / yang belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Logout Route (Hanya untuk user yang login)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Mahasiswa Routes: Wajib login dengan peran mahasiswa
Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');
    Route::get('/bimbingan-akademik', [BimbinganAkademikController::class, 'index'])->name('bimbingan-akademik');
    Route::get('/bimbingan-skripsi', [BimbinganSkripsiController::class, 'index'])->name('bimbingan-skripsi');
    Route::get('/riwayat-bimbingan', [RiwayatBimbinganController::class, 'index'])->name('riwayat-bimbingan');
});

// Protected Dosen Routes: Wajib login dengan peran dosen
Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
    Route::get('/beranda', [DosenBerandaController::class, 'index'])->name('beranda');
});
