@extends('layouts.mahasiswa')

@section('title', 'Edit Profil — SIMBIM')
@section('page-title', 'Edit Profil')

@push('styles')
<style>
    .simbim-card {
        border-radius: 1rem;
        border: 1px solid rgba(226, 232, 240, 0.8);
        background: #ffffff;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.04);
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .pulse-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #10b981;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulseRing 1.8s infinite;
    }
    @keyframes pulseRing {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { box-shadow: 0 0 0 7px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    .avatar-wrapper {
        position: relative;
        width: 140px;
        height: 140px;
        margin: 0 auto;
    }
    .avatar-image {
        width: 140px;
        height: 140px;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid #ffffff;
        box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.12);
    }
    .badge-soft-primary { background-color: #eef2ff; color: #4338ca; }
    .badge-soft-success { background-color: #ecfdf5; color: #047857; }
</style>
@endpush

@section('content')
<!-- Header Page / Breadcrumb -->
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-6">
    <div>
        <h1 class="fs-2 fw-bolder text-gray-900 mb-1">
            <i class="fas fa-user-edit text-primary me-2"></i> Pengaturan Profil Pengguna
        </h1>
        <p class="text-gray-500 fs-7 mb-0">Perbarui data diri, informasi akademik, dan foto profil akun SIMBIM Anda.</p>
    </div>
    <a href="{{ Route::has('berandaMahasiswa') ? route('berandaMahasiswa') : route('mahasiswa.beranda') }}" class="btn btn-sm btn-light-primary fw-bold d-inline-flex align-items-center">
        <i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard
    </a>
</div>

<!-- Flash Message: Success -->
@if (session('success'))
<div class="alert alert-dismissible bg-light-success border border-success border-opacity-25 d-flex align-items-center p-5 mb-6 rounded-3">
    <div class="symbol symbol-40px me-3">
        <span class="symbol-label bg-success text-white rounded-circle">
            <i class="fas fa-check fs-4"></i>
        </span>
    </div>
    <div class="d-flex flex-column flex-grow-1">
        <h5 class="fw-bolder text-success mb-0">Berhasil!</h5>
        <span class="text-gray-700 fs-7">{{ session('success') }}</span>
    </div>
    <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Validation Errors Alert -->
@if ($errors->any())
<div class="alert alert-dismissible bg-light-danger border border-danger border-opacity-25 d-flex align-items-start p-5 mb-6 rounded-3">
    <div class="symbol symbol-40px me-3">
        <span class="symbol-label bg-danger text-white rounded-circle">
            <i class="fas fa-exclamation-triangle fs-4"></i>
        </span>
    </div>
    <div class="d-flex flex-column flex-grow-1">
        <h5 class="fw-bolder text-danger mb-1">Terjadi kesalahan pada isian form:</h5>
        <ul class="mb-0 ps-3 text-danger fs-7">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- 2-Column Responsive Layout -->
<div class="row g-6 g-xl-8">
    <!-- Kolom Kiri: Kartu Identitas & Preview Foto Profil -->
    <div class="col-12 col-lg-4 col-xl-4">
        <div class="simbim-card p-6 text-center h-100 d-flex flex-column justify-content-between">
            <div>
                <span class="badge badge-light-primary fw-bold fs-8 px-3 py-1.5 rounded-pill mb-4">
                    Pratinjau Foto Profil
                </span>

                <!-- Avatar Preview -->
                <div class="avatar-wrapper mb-4">
                    <img id="avatar_preview" src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="avatar-image" />
                </div>

                <!-- Info Identitas Singkat -->
                <h3 class="fs-4 fw-bolder text-gray-900 mb-1" id="preview_name">{{ $user->name }}</h3>
                <div class="text-gray-500 fs-7 fw-semibold mb-3" id="preview_email">{{ $user->email }}</div>

                <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
                    <span class="badge badge-soft-primary fw-bold fs-8 px-3 py-1.5 rounded-pill" id="preview_prodi">
                        <i class="fas fa-graduation-cap me-1"></i> {{ $user->program_studi ?? 'Informatika' }}
                    </span>
                    <span class="badge badge-light-info fw-bold fs-8 px-3 py-1.5 rounded-pill" id="preview_nim">
                        NIM: {{ $user->nim ?? '2108107010001' }}
                    </span>
                    <span class="badge badge-soft-success fw-bold fs-8 px-3 py-1.5 rounded-pill">
                        <span class="pulse-dot me-1"></span> Mahasiswa Aktif
                    </span>
                </div>

                <div class="border-top border-gray-100 pt-4 text-start">
                    <span class="text-gray-500 fw-bold fs-8 text-uppercase tracking-wider d-block mb-2">Petunjuk Foto</span>
                    <ul class="text-gray-600 fs-8 ps-3 mb-0">
                        <li>Format file: <strong>JPG, JPEG, PNG, WEBP</strong></li>
                        <li>Ukuran maksimum: <strong>2 MB</strong></li>
                        <li>Disarankan rasio <strong>1:1 (persegi)</strong> dengan wajah terlihat jelas.</li>
                    </ul>
                </div>
            </div>

            <!-- Footer Card -->
            <div class="pt-4 mt-4 border-top border-gray-100 text-muted fs-8">
                Terakhir diperbarui: <br />
                <strong class="text-gray-700">{{ $user->updated_at ? $user->updated_at->isoFormat('D MMMM Y, HH:mm') : 'Hari ini' }} WIB</strong>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Form Edit Data Profil -->
    <div class="col-12 col-lg-8 col-xl-8">
        <div class="simbim-card p-6 p-md-8">
            <div class="d-flex align-items-center justify-content-between pb-4 mb-6 border-bottom border-gray-100">
                <div>
                    <h2 class="fs-4 fw-bolder text-gray-900 mb-1">Formulir Data Pribadi & Akademik</h2>
                    <span class="text-gray-500 fs-7">Pastikan seluruh data yang dimasukkan akurat dan valid</span>
                </div>
                <span class="badge bg-light text-muted fw-bold fs-9 px-2.5 py-1 rounded">
                    PUT /profile/update
                </span>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="form_edit_profile">
                @csrf
                @method('PUT')

                <div class="row g-4 mb-5">
                    <!-- Input Nama Lengkap -->
                    <div class="col-12 col-md-6">
                        <label for="name" class="form-label fw-bold text-gray-800 fs-7 mb-2 required">
                            Nama Lengkap
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-gray-300 text-gray-500">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" name="name" id="name" class="form-control form-control-solid @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" placeholder="Contoh: Andi Pratama" required autocomplete="name" />
                        </div>
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @else
                            <div class="form-text fs-8 text-muted">Nama lengkap sesuai identitas resmi mahasiswa.</div>
                        @enderror
                    </div>

                    <!-- Input Email -->
                    <div class="col-12 col-md-6">
                        <label for="email" class="form-label fw-bold text-gray-800 fs-7 mb-2 required">
                            Alamat Email
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-gray-300 text-gray-500">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" name="email" id="email" class="form-control form-control-solid @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" placeholder="Contoh: andi@mhs.usk.ac.id" required autocomplete="email" />
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @else
                            <div class="form-text fs-8 text-muted">Digunakan untuk menerima notifikasi bimbingan.</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-4 mb-5">
                    <!-- Input NIM -->
                    <div class="col-12 col-md-6">
                        <label for="nim" class="form-label fw-bold text-gray-800 fs-7 mb-2 required">
                            Nomor Induk Mahasiswa (NIM)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-gray-300 text-gray-500">
                                <i class="fas fa-id-card"></i>
                            </span>
                            <input type="text" name="nim" id="nim" class="form-control form-control-solid @error('nim') is-invalid @enderror" value="{{ old('nim', $user->nim) }}" placeholder="Contoh: 2108107010001" required />
                        </div>
                        @error('nim')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @else
                            <div class="form-text fs-8 text-muted">NIM yang terdaftar pada pangkalan data kampus.</div>
                        @enderror
                    </div>

                    <!-- Input Program Studi -->
                    <div class="col-12 col-md-6">
                        <label for="program_studi" class="form-label fw-bold text-gray-800 fs-7 mb-2 required">
                            Program Studi
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-gray-300 text-gray-500">
                                <i class="fas fa-graduation-cap"></i>
                            </span>
                            <select name="program_studi" id="program_studi" class="form-select form-select-solid @error('program_studi') is-invalid @enderror" required>
                                @php
                                    $currentProdi = old('program_studi', $user->program_studi ?? 'Informatika');
                                    $prodiList = [
                                        'Informatika',
                                        'Sistem Informasi',
                                        'Teknik Komputer',
                                        'Teknik Elektro',
                                        'Matematika',
                                        'Statistika',
                                        'Manajemen Informatika',
                                    ];
                                @endphp
                                @foreach ($prodiList as $p)
                                    <option value="{{ $p }}" {{ $currentProdi === $p ? 'selected' : '' }}>
                                        Program Studi {{ $p }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('program_studi')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @else
                            <div class="form-text fs-8 text-muted">Pilih jurusan/program studi aktif Anda.</div>
                        @enderror
                    </div>
                </div>

                <!-- Input Foto Profil -->
                <div class="mb-6 p-4 rounded-3 bg-light border border-gray-200">
                    <label for="foto_profil" class="form-label fw-bold text-gray-800 fs-7 mb-2 d-flex align-items-center justify-content-between">
                        <span>
                            <i class="fas fa-camera text-primary me-1.5"></i> Ganti Foto Profil
                        </span>
                        <span class="badge badge-light-secondary fs-9 text-muted">Opsional</span>
                    </label>

                    <input type="file" name="foto_profil" id="foto_profil" class="form-control form-control-solid @error('foto_profil') is-invalid @enderror" accept="image/png, image/jpeg, image/jpg, image/webp" />

                    @error('foto_profil')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @else
                        <div class="form-text fs-8 text-muted mt-1.5" id="avatar_file_name">
                            Pilih file gambar dari komputer atau HP Anda (Maks. 2MB).
                        </div>
                    @enderror

                    @if ($user->foto_profil)
                    <div class="form-check form-check-custom form-check-solid mt-3">
                        <input class="form-check-input" type="checkbox" name="hapus_foto" value="1" id="hapus_foto" />
                        <label class="form-check-label text-danger fs-8 fw-semibold" for="hapus_foto">
                            <i class="fas fa-trash-alt me-1"></i> Hapus foto profil kustom dan kembalikan ke avatar standar
                        </label>
                    </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-end gap-3 pt-4 border-top border-gray-100">
                    <a href="{{ Route::has('berandaMahasiswa') ? route('berandaMahasiswa') : route('mahasiswa.beranda') }}" class="btn btn-light fw-bold px-6 py-3">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary fw-bolder px-7 py-3 shadow-sm d-inline-flex align-items-center justify-content-center">
                        <i class="fas fa-save me-2 fs-5"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('foto_profil');
        const avatarPreview = document.getElementById('avatar_preview');
        const avatarFileName = document.getElementById('avatar_file_name');
        const nameInput = document.getElementById('name');
        const previewName = document.getElementById('preview_name');
        const emailInput = document.getElementById('email');
        const previewEmail = document.getElementById('preview_email');
        const nimInput = document.getElementById('nim');
        const previewNim = document.getElementById('preview_nim');
        const prodiInput = document.getElementById('program_studi');
        const previewProdi = document.getElementById('preview_prodi');

        // Real-time avatar image preview
        if (fileInput && avatarPreview) {
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Check file size (2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Ukuran file terlalu besar! Maksimal ukuran file foto adalah 2 MB.');
                        fileInput.value = '';
                        return;
                    }

                    if (avatarFileName) {
                        avatarFileName.innerHTML = '<span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> File dipilih: ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)</span>';
                    }

                    const reader = new FileReader();
                    reader.onload = function(event) {
                        avatarPreview.src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Live text update on preview card
        if (nameInput && previewName) {
            nameInput.addEventListener('input', function() {
                previewName.textContent = this.value.trim() || 'Nama Mahasiswa';
            });
        }

        if (emailInput && previewEmail) {
            emailInput.addEventListener('input', function() {
                previewEmail.textContent = this.value.trim() || 'email@mhs.usk.ac.id';
            });
        }

        if (nimInput && previewNim) {
            nimInput.addEventListener('input', function() {
                previewNim.textContent = 'NIM: ' + (this.value.trim() || '-');
            });
        }

        if (prodiInput && previewProdi) {
            prodiInput.addEventListener('change', function() {
                previewProdi.innerHTML = '<i class="fas fa-graduation-cap me-1"></i> ' + this.value;
            });
        }
    });
</script>
@endpush
