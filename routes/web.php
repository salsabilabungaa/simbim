<?php

use App\Http\Controllers\Mahasiswa\BerandaController;
use App\Http\Controllers\Mahasiswa\BimbinganAkademikController;
use App\Http\Controllers\Mahasiswa\BimbinganSkripsiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BerandaController::class, 'index'])->name('mahasiswa.beranda');
Route::get('/mahasiswa/beranda', [BerandaController::class, 'index'])->name('mahasiswa.beranda');
Route::get('/mahasiswa/bimbingan-akademik', [BimbinganAkademikController::class, 'index'])->name('mahasiswa.bimbingan-akademik');
Route::get('/mahasiswa/bimbingan-skripsi', [BimbinganSkripsiController::class, 'index'])->name('mahasiswa.bimbingan-skripsi');
