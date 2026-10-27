<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\Mahasiswa\BerandaController;
use App\Http\Controllers\Mahasiswa\BimbinganAkademikController;
use App\Http\Controllers\Mahasiswa\BimbinganSkripsiController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Rute Tamu (Autentikasi / Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
});

// Rute Logout (Pengguna yang sedang login)
Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// Rute Terlindungi Middleware Auth (Beranda Mahasiswa & Profil)
Route::middleware('auth')->group(function () {
    Route::get('/', [BerandaController::class, 'index'])->name('mahasiswa.beranda');
    Route::get('/mahasiswa/beranda', [BerandaController::class, 'index'])->name('berandaMahasiswa');
    Route::get('/mahasiswa/bimbingan-akademik', [BimbinganAkademikController::class, 'index'])->name('mahasiswa.bimbingan-akademik');
    Route::get('/mahasiswa/bimbingan-skripsi', [BimbinganSkripsiController::class, 'index'])->name('mahasiswa.bimbingan-skripsi');

    // Fitur Edit Profil
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});
