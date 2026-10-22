@extends('layouts.auth')

@section('title', 'Daftar Akun Baru')

@section('content')
<div class="w-100">
    <!-- Header Heading -->
    <div class="text-center mb-8">
        <h2 class="text-dark fw-bolder fs-2 mb-2">Pendaftaran Akun</h2>
        <div class="text-gray-500 fw-bold fs-6">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" class="link-primary fw-bolder text-decoration-none ms-1">
                Masuk di Sini
            </a>
        </div>
    </div>

    <!-- Registration Form -->
    <form class="form w-100" method="POST" action="{{ route('register') }}" id="kt_sign_up_form">
        @csrf

        <!-- Role Selection -->
        <div class="fv-row mb-6">
            <label class="form-label fs-7 fw-bolder text-dark mb-2 required">Pilih Peran Akun</label>
            <div class="row g-3">
                <div class="col-6">
                    <label class="d-block">
                        <input type="radio" class="d-none role-check-input" name="role" value="mahasiswa" id="role_mahasiswa"
                            {{ old('role', 'mahasiswa') === 'mahasiswa' ? 'checked' : '' }} />
                        <div class="role-selector-card rounded-3 p-4 text-center">
                            <i class="fas fa-user-graduate fs-2x text-primary mb-2 d-block"></i>
                            <span class="role-title fw-bolder text-dark fs-6 d-block">Mahasiswa</span>
                            <span class="text-gray-500 fs-8 d-block">Bimbingan Skripsi & Akademik</span>
                        </div>
                    </label>
                </div>
                <div class="col-6">
                    <label class="d-block">
                        <input type="radio" class="d-none role-check-input" name="role" value="dosen" id="role_dosen"
                            {{ old('role') === 'dosen' ? 'checked' : '' }} />
                        <div class="role-selector-card rounded-3 p-4 text-center">
                            <i class="fas fa-chalkboard-teacher fs-2x text-success mb-2 d-block"></i>
                            <span class="role-title fw-bolder text-dark fs-6 d-block">Dosen</span>
                            <span class="text-gray-500 fs-8 d-block">Dosen Pembimbing & DPA</span>
                        </div>
                    </label>
                </div>
            </div>
            @error('role')
                <div class="invalid-feedback d-block mt-1 fs-7 fw-semibold">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Hidden default Program Studi: PSTI -->
        <input type="hidden" name="program_studi" value="Pendidikan Sistem dan Teknologi Informasi (PSTI)" />

        <!-- Nama Lengkap -->
        <div class="fv-row mb-5">
            <label class="form-label fs-7 fw-bolder text-dark mb-1 required" id="name_label">
                {{ old('role', 'mahasiswa') === 'dosen' ? 'Nama Lengkap beserta Gelar' : 'Nama Lengkap' }}
            </label>
            <div class="position-relative">
                <span class="position-absolute top-50 translate-middle-y ms-4 text-gray-500">
                    <i class="fas fa-user fs-5"></i>
                </span>
                <input class="form-control form-control-solid ps-12 py-3 @error('name') is-invalid @enderror"
                    type="text"
                    name="name"
                    id="name_input"
                    value="{{ old('name') }}"
                    placeholder="{{ old('role', 'mahasiswa') === 'dosen' ? 'Contoh: Dr. Ir. Budi, M.T.' : 'Contoh: Andi Pratama' }}"
                    autocomplete="name"
                    required />
            </div>
            @error('name')
                <div class="invalid-feedback d-block mt-1 fs-7 fw-semibold">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- NIM / NIP -->
        <div class="fv-row mb-5">
            <label class="form-label fs-7 fw-bolder text-dark mb-1 required" id="nomor_induk_label">
                {{ old('role', 'mahasiswa') === 'dosen' ? 'NIP (Nomor Induk Pegawai)' : 'NIM (Nomor Induk Mahasiswa)' }}
            </label>
            <div class="position-relative">
                <span class="position-absolute top-50 translate-middle-y ms-4 text-gray-500">
                    <i class="fas fa-id-card fs-5"></i>
                </span>
                <input class="form-control form-control-solid ps-12 py-3 @error('nomor_induk') is-invalid @enderror"
                    type="text"
                    name="nomor_induk"
                    id="nomor_induk_input"
                    value="{{ old('nomor_induk') }}"
                    placeholder="{{ old('role', 'mahasiswa') === 'dosen' ? 'Contoh: 198503152010122001' : 'Contoh: 2108107010001' }}"
                    required />
            </div>
            @error('nomor_induk')
                <div class="invalid-feedback d-block mt-1 fs-7 fw-semibold">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Alamat Email -->
        <div class="fv-row mb-5">
            <label class="form-label fs-7 fw-bolder text-dark mb-1 required">Alamat Email</label>
            <div class="position-relative">
                <span class="position-absolute top-50 translate-middle-y ms-4 text-gray-500">
                    <i class="fas fa-envelope fs-5"></i>
                </span>
                <input class="form-control form-control-solid ps-12 py-3 @error('email') is-invalid @enderror"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="nama@usk.ac.id atau nama@mhs.usk.ac.id"
                    autocomplete="email"
                    required />
            </div>
            @error('email')
                <div class="invalid-feedback d-block mt-1 fs-7 fw-semibold">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="row g-4 mb-5">
            <!-- Password -->
            <div class="col-md-6 fv-row">
                <label class="form-label fs-7 fw-bolder text-dark mb-1 required">Kata Sandi</label>
                <div class="position-relative">
                    <span class="position-absolute top-50 translate-middle-y ms-4 text-gray-500">
                        <i class="fas fa-lock fs-5"></i>
                    </span>
                    <input class="form-control form-control-solid ps-12 pe-12 py-3 @error('password') is-invalid @enderror"
                        type="password"
                        name="password"
                        id="reg_password_input"
                        placeholder="Minimal 8 karakter"
                        autocomplete="new-password"
                        required />
                    <button type="button" class="btn btn-sm btn-icon position-absolute top-50 end-0 translate-middle-y me-2 text-gray-500 text-hover-primary password-toggle-btn"
                        data-toggle="password" data-target="#reg_password_input">
                        <i class="fas fa-eye fs-6"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback d-block mt-1 fs-7 fw-semibold">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Konfirmasi Password -->
            <div class="col-md-6 fv-row">
                <label class="form-label fs-7 fw-bolder text-dark mb-1 required">Ulangi Kata Sandi</label>
                <div class="position-relative">
                    <span class="position-absolute top-50 translate-middle-y ms-4 text-gray-500">
                        <i class="fas fa-check-double fs-5"></i>
                    </span>
                    <input class="form-control form-control-solid ps-12 pe-12 py-3 @error('password_confirmation') is-invalid @enderror"
                        type="password"
                        name="password_confirmation"
                        id="reg_password_confirm_input"
                        placeholder="Konfirmasi sandi"
                        autocomplete="new-password"
                        required />
                    <button type="button" class="btn btn-sm btn-icon position-absolute top-50 end-0 translate-middle-y me-2 text-gray-500 text-hover-primary password-toggle-btn"
                        data-toggle="password" data-target="#reg_password_confirm_input">
                        <i class="fas fa-eye fs-6"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Password Strength Meter -->
        <div class="mb-5">
            <div class="d-flex align-items-center mb-1">
                <div class="progress flex-grow-1 bg-secondary h-5px me-2 rounded">
                    <div class="progress-bar bg-danger" id="pass_meter_bar" role="progressbar" style="width: 0%"></div>
                </div>
                <span class="fs-8 fw-bold text-gray-500" id="pass_meter_text">Kekuatan Sandi</span>
            </div>
            <div class="text-muted fs-8">Gunakan minimal 8 karakter kombinasi huruf dan angka untuk keamanan optimal.</div>
        </div>

        <!-- Terms & Conditions Checkbox -->
        <div class="fv-row mb-8">
            <label class="form-check form-check-custom form-check-solid form-check-sm align-items-start">
                <input class="form-check-input mt-1" type="checkbox" name="terms" value="1" {{ old('terms') ? 'checked' : '' }} required />
                <span class="form-check-label fw-bold text-gray-700 fs-7 ms-2">
                    Saya menyetujui <a href="javascript:void(0)" class="link-primary fw-bolder">Syarat & Ketentuan Layanan</a> serta kebijakan privasi data akademik SIMBIM.
                </span>
            </label>
            @error('terms')
                <div class="invalid-feedback d-block mt-1 fs-7 fw-semibold">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="text-center">
            <button type="submit" id="kt_sign_up_submit" class="btn btn-primary w-100 py-3 fw-bolder fs-6 shadow-sm">
                <span class="indicator-label">
                    Daftar Akun Sekarang <i class="fas fa-user-plus ms-2 fs-6"></i>
                </span>
                <span class="indicator-progress d-none">
                    Mendaftarkan akun...
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                </span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Dynamic Role Switcher (Mahasiswa vs Dosen)
    const roleMahasiswa = document.getElementById('role_mahasiswa');
    const roleDosen = document.getElementById('role_dosen');
    const nomorIndukLabel = document.getElementById('nomor_induk_label');
    const nomorIndukInput = document.getElementById('nomor_induk_input');
    const nameLabel = document.getElementById('name_label');
    const nameInput = document.getElementById('name_input');

    function updateRoleLabels() {
        if (roleDosen.checked) {
            nomorIndukLabel.textContent = 'NIP (Nomor Induk Pegawai)';
            nomorIndukInput.placeholder = 'Contoh: 198503152010122001';
            if (nameLabel) nameLabel.textContent = 'Nama Lengkap beserta Gelar';
            if (nameInput) nameInput.placeholder = 'Contoh: Dr. Ir. Budi, M.T.';
        } else {
            nomorIndukLabel.textContent = 'NIM (Nomor Induk Mahasiswa)';
            nomorIndukInput.placeholder = 'Contoh: 2108107010001';
            if (nameLabel) nameLabel.textContent = 'Nama Lengkap';
            if (nameInput) nameInput.placeholder = 'Contoh: Andi Pratama';
        }
    }

    if (roleMahasiswa && roleDosen) {
        roleMahasiswa.addEventListener('change', updateRoleLabels);
        roleDosen.addEventListener('change', updateRoleLabels);
    }

    // Password strength indicator
    const passInput = document.getElementById('reg_password_input');
    const meterBar = document.getElementById('pass_meter_bar');
    const meterText = document.getElementById('pass_meter_text');

    if (passInput) {
        passInput.addEventListener('input', function () {
            const val = this.value;
            let score = 0;
            if (val.length >= 8) score += 35;
            if (/[0-9]/.test(val)) score += 25;
            if (/[A-Z]/.test(val)) score += 20;
            if (/[^A-Za-z0-9]/.test(val)) score += 20;

            meterBar.style.width = score + '%';

            if (score <= 35) {
                meterBar.className = 'progress-bar bg-danger';
                meterText.textContent = 'Lemah';
                meterText.className = 'fs-8 fw-bold text-danger';
            } else if (score <= 70) {
                meterBar.className = 'progress-bar bg-warning';
                meterText.textContent = 'Cukup';
                meterText.className = 'fs-8 fw-bold text-warning';
            } else {
                meterBar.className = 'progress-bar bg-success';
                meterText.textContent = 'Kuat';
                meterText.className = 'fs-8 fw-bold text-success';
            }
        });
    }

    // Form submission animation
    const regForm = document.getElementById('kt_sign_up_form');
    if (regForm) {
        regForm.addEventListener('submit', function () {
            const submitBtn = document.getElementById('kt_sign_up_submit');
            if (submitBtn) {
                const label = submitBtn.querySelector('.indicator-label');
                const progress = submitBtn.querySelector('.indicator-progress');
                if (label && progress) {
                    label.classList.add('d-none');
                    progress.classList.remove('d-none');
                }
            }
        });
    }
</script>
@endpush
