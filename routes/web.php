<?php

use App\Http\Controllers\Mahasiswa\BerandaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BerandaController::class, 'index'])->name('mahasiswa.beranda');
Route::get('/mahasiswa/beranda', [BerandaController::class, 'index'])->name('mahasiswa.beranda');
