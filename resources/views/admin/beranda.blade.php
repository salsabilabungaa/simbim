@extends('layouts.admin')

@section('title', 'Dashboard Administrator')

@section('content')
<!-- Header Banner -->
<div class="card card-flush pb-0 bgi-position-y-center bgi-no-repeat mb-8" style="background-color: #1e1e2d; background-position: right top; background-size: 30%;">
    <div class="card-body pt-9 pb-8">
        <div class="d-flex flex-wrap flex-sm-nowrap align-items-center justify-content-between">
            <div class="me-7 mb-4">
                <div class="d-flex align-items-center mb-2">
                    <span class="badge badge-light-danger fw-bolder me-3 px-3 py-1 fs-8">ROLE ADMINISTRATOR</span>
                    <span class="text-gray-400 fs-7">{{ config('simbim.prodi') }} &mdash; {{ config('simbim.universitas') }}</span>
                </div>
                <h1 class="text-white fw-bolder fs-2x mb-3">Selamat Datang, {{ auth()->user()->name }}!</h1>
                <p class="text-gray-300 fs-6 mb-0 mw-650px">
                    Sebagai Administrator, Anda memiliki kewenangan tunggal untuk mendata dan membuat akun pengguna sistem, serta memiliki <strong>hak akses global</strong> untuk meninjau seluruh data pada 4 role: Mahasiswa, Dosen, Dewan Skripsi, dan GKM.
                </p>
            </div>
            <div class="d-flex gap-3 my-2">
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary fw-bolder py-3 px-5">
                    <i class="fas fa-user-plus me-2"></i> Tambah Akun Manual
                </a>
                <a href="{{ route('admin.users.import-form') }}" class="btn btn-success fw-bolder py-3 px-5">
                    <i class="fas fa-file-excel me-2"></i> Import Excel / CSV
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Notice Bar: Penjelasan Fitur 1 -->
<div class="notice d-flex bg-light-primary rounded border-primary border border-dashed mb-8 p-6">
    <span class="svg-icon svg-icon-2tx svg-icon-primary me-4">
        <i class="fas fa-info-circle fs-1 text-primary"></i>
    </span>
    <div class="d-flex flex-stack flex-grow-1">
        <div class="fw-semibold">
            <h4 class="text-gray-900 fw-bolder mb-1">Pemberitahuan Sistem Otentikasi & Registrasi</h4>
            <div class="fs-6 text-gray-700">
                Sistem SIMBIM tidak menyediakan pendaftaran akun (register) secara publik. Administrator adalah satu-satunya otoritas yang bertugas membuat akun pengguna baik secara manual (formulir) maupun massal (import spreadsheet .xlsx/.csv).
            </div>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-5 g-xl-8 mb-8">
    <div class="col-xl-3 col-md-6">
        <div class="card card-xl-stretch mb-xl-8 bg-body shadow-sm border-start border-4 border-primary">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <span class="text-gray-500 fw-bold fs-7 text-uppercase">Total Pengguna</span>
                    <span class="badge badge-light-primary p-2">
                        <i class="fas fa-users text-primary fs-4"></i>
                    </span>
                </div>
                <div class="text-dark fw-bolder fs-2x mb-1">{{ $totalPengguna }}</div>
                <div class="text-muted fs-7">Akun terdaftar di SIMBIM</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-xl-stretch mb-xl-8 bg-body shadow-sm border-start border-4 border-success">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <span class="text-gray-500 fw-bold fs-7 text-uppercase">Mahasiswa</span>
                    <span class="badge badge-light-success p-2">
                        <i class="fas fa-user-graduate text-success fs-4"></i>
                    </span>
                </div>
                <div class="text-dark fw-bolder fs-2x mb-1">{{ $totalMahasiswa }}</div>
                <div class="text-muted fs-7">Akun mahasiswa aktif</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-xl-stretch mb-xl-8 bg-body shadow-sm border-start border-4 border-warning">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <span class="text-gray-500 fw-bold fs-7 text-uppercase">Dosen</span>
                    <span class="badge badge-light-warning p-2">
                        <i class="fas fa-chalkboard-teacher text-warning fs-4"></i>
                    </span>
                </div>
                <div class="text-dark fw-bolder fs-2x mb-1">{{ $totalDosen }}</div>
                <div class="text-muted fs-7">Pembimbing & Dosen Wali</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-xl-stretch mb-xl-8 bg-body shadow-sm border-start border-4 border-info">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <span class="text-gray-500 fw-bold fs-7 text-uppercase">Dewan & GKM</span>
                    <span class="badge badge-light-info p-2">
                        <i class="fas fa-shield-alt text-info fs-4"></i>
                    </span>
                </div>
                <div class="text-dark fw-bolder fs-2x mb-1">{{ $totalDewanSkripsi + $totalGkm }}</div>
                <div class="text-muted fs-7">{{ $totalDewanSkripsi }} Dewan Skripsi, {{ $totalGkm }} GKM</div>
            </div>
        </div>
    </div>
</div>
@endsection

