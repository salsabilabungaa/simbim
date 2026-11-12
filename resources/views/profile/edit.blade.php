@extends($layout ?? 'layouts.mahasiswa')

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
        <p class="text-gray-500 fs-7 mb-0">Perbarui data diri, informasi akun, dan foto profil SIMBIM Anda.</p>
    </div>
    <a href="{{ $dashboardRoute ?? route('mahasiswa.beranda') }}" class="btn btn-sm btn-light-primary fw-bold d-inline-flex align-items-center">
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
                <span class="badge badge-light-{{ $user->role_badge ?? 'primary' }} fw-bold fs-8 px-3 py-1.5 rounded-pill mb-4 text-uppercase">
                    {{ $user->role_label ?? 'Pengguna' }}
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
                        <i class="fas fa-graduation-cap me-1"></i> {{ $user->program_studi ?? config('simbim.prodi') }}
                    </span>
                    <span class="badge badge-light fw-bold fs-8 px-3 py-1.5 rounded-pill text-muted" id="preview_nim">
                        {{ $user->isMahasiswa() ? 'NIM' : 'NIP' }}: {{ $user->isMahasiswa() ? ($user->nim ?? $user->nomor_induk) : ($user->nip ?? $user->nomor_induk) }}
                    </span>
                </div>
            </div>

            <!-- Kartu Status Akun -->
            <div class="border-top border-gray-100 pt-4 mt-2">
                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 mb-2">
                    <span class="fs-8 fw-bold text-gray-600">Status Akun</span>
                    <span class="badge badge-soft-success fw-bold fs-8 px-2.5 py-1 rounded-pill d-inline-flex align-items-center">
                        <span class="pulse-dot me-1.5"></span> Aktif
                    </span>
                </div>
                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3">
                    <span class="fs-8 fw-bold text-gray-600">Peran Sistem</span>
                    <span class="badge badge-light-{{ $user->role_badge ?? 'primary' }} fw-bolder fs-8 px-2.5 py-1">
                        {{ $user->role_label ?? 'Pengguna' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Form Pengaturan & Edit Data Profil -->
    <div class="col-12 col-lg-8 col-xl-8">
        <div class="simbim-card p-6 p-lg-8">
            <div class="d-flex align-items-center justify-content-between pb-5 mb-6 border-bottom border-gray-100">
                <div>
                    <h3 class="fs-4 fw-bolder text-gray-900 mb-1">Informasi Dasar &amp; Akademik</h3>
                    <p class="text-gray-500 fs-7 mb-0">Pastikan informasi di bawah ini selalu akurat dan mutakhir.</p>
                </div>
                <span class="badge badge-light-primary fs-8 fw-bold px-3 py-1.5 rounded">SIMBIM</span>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profile_form">
                @csrf
                @method('PUT')

                <!-- Field 1: Nama Lengkap -->
                <div class="mb-5">
                    <label for="name" class="form-label fs-7 fw-bold text-gray-800 required">
                        Nama Lengkap
                    </label>
                    <div class="position-relative">
                        <input type="text" name="name" id="name" class="form-control form-control-solid @error('name') is-invalid @enderror" placeholder="Masukkan nama lengkap Anda" value="{{ old('name', $user->name) }}" required />
                    </div>
                    @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Field 2: Alamat Email -->
                <div class="mb-5">
                    <label for="email" class="form-label fs-7 fw-bold text-gray-800 required">
                        Alamat Email
                    </label>
                    <div class="position-relative">
                        <input type="email" name="email" id="email" class="form-control form-control-solid @error('email') is-invalid @enderror" placeholder="nama@simbim.ac.id" value="{{ old('email', $user->email) }}" required />
                    </div>
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Field 3: Nomor Induk (NIM / NIP) -->
                <div class="mb-5">
                    @php
                        $nomorIndukLabel = $user->isMahasiswa()
                            ? 'Nomor Induk Mahasiswa (NIM)'
                            : ($user->isAdmin() ? 'Nomor Induk Pegawai / Admin' : 'Nomor Induk Pegawai (NIP)');
                        $nomorIndukName = $user->isMahasiswa() ? 'nim' : ($user->isAdmin() ? 'nomor_induk' : 'nip');
                        $nomorIndukVal = old($nomorIndukName, $user->isMahasiswa() ? ($user->nim ?? $user->nomor_induk) : ($user->nip ?? $user->nomor_induk));
                    @endphp

                    <label for="{{ $nomorIndukName }}" class="form-label fs-7 fw-bold text-gray-800 required">
                        {{ $nomorIndukLabel }}
                    </label>
                    <div class="position-relative">
                        <input type="text" name="{{ $nomorIndukName }}" id="nim" class="form-control form-control-solid @error($nomorIndukName) is-invalid @enderror" placeholder="Contoh: 2108107010001" value="{{ $nomorIndukVal }}" required />
                    </div>
                    @error($nomorIndukName)
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Field 4: Program Studi -->
                <div class="mb-5">
                    <label for="program_studi" class="form-label fs-7 fw-bold text-gray-800 required">
                        Program Studi / Homebase
                    </label>
                    <input type="text" name="program_studi" id="program_studi" class="form-control form-control-solid @error('program_studi') is-invalid @enderror" value="{{ old('program_studi', $user->program_studi ?? config('simbim.prodi')) }}" required />
                    @error('program_studi')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Field 5: Upload Foto Profil Kustom -->
                <div class="mb-7">
                    <label class="form-label fs-7 fw-bold text-gray-800 d-flex align-items-center justify-content-between">
                        <span>Unggah Foto Profil Baru</span>
                        <span class="badge badge-light-secondary fs-9 text-muted">Opsional</span>
                    </label>

                    <input type="file" name="foto_profil" id="foto_profil" class="form-control form-control-solid @error('foto_profil') is-invalid @enderror" accept="image/png, image/jpeg, image/jpg, image/webp" />

                    @error('foto_profil')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @else
                        <div class="form-text fs-8 text-muted mt-1.5" id="avatar_file_name">
                            Pilih file gambar dari komputer atau HP Anda (Maks. 2MB, JPG/PNG/WEBP).
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
                    <a href="{{ $dashboardRoute ?? route('mahasiswa.beranda') }}" class="btn btn-light fw-bold px-6 py-3">
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
                previewName.textContent = this.value.trim() || 'Nama Pengguna';
            });
        }

        if (emailInput && previewEmail) {
            emailInput.addEventListener('input', function() {
                previewEmail.textContent = this.value.trim() || 'email@simbim.ac.id';
            });
        }

        if (nimInput && previewNim) {
            nimInput.addEventListener('input', function() {
                previewNim.textContent = (this.value.trim() || '-');
            });
        }

        if (prodiInput && previewProdi) {
            prodiInput.addEventListener('input', function() {
                previewProdi.innerHTML = '<i class="fas fa-graduation-cap me-1"></i> ' + this.value;
            });
        }
    });
</script>
@endpush
