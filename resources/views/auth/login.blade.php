@extends('layouts.auth')

@section('title', 'Masuk Akun')

@section('content')
<div class="w-100">
    <!-- Header Heading -->
    <div class="text-center mb-9">
        <h2 class="text-dark fw-bolder fs-2 mb-2">Selamat Datang</h2>
        <div class="text-gray-500 fw-semibold fs-6">
            Silakan masuk dengan akun SIMBIM Anda
        </div>
    </div>

    <!-- Login Form -->
    <form class="form w-100" method="POST" action="{{ route('login') }}" id="kt_sign_in_form">
        @csrf

        <!-- Input: Email atau NIM/NIP -->
        <div class="fv-row mb-6">
            <label class="form-label fs-7 fw-bolder text-dark mb-1 required">Email</label>
            <div class="position-relative">
                <span class="position-absolute top-50 translate-middle-y ms-4 text-gray-500">
                    <i class="fas fa-id-badge fs-5"></i>
                </span>
                <input class="form-control form-control-solid ps-12 py-3 @error('login') is-invalid @enderror"
                    type="text"
                    name="login"
                    id="login_input"
                    value="{{ old('login') }}"
                    placeholder="Masukkan Email atau NIM / NIP Anda"
                    autocomplete="username"
                    required
                    autofocus />
            </div>
            @error('login')
                <div class="invalid-feedback d-block mt-1 fs-7 fw-semibold">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Input: Password -->
        <div class="fv-row mb-6">
            <div class="d-flex flex-stack mb-1">
                <label class="form-label fs-7 fw-bolder text-dark mb-0 required">Kata Sandi</label>
                <a href="javascript:void(0)" class="link-primary fs-8 fw-bolder text-hover-underline" onclick="Swal.fire({icon: 'info', title: 'Reset Kata Sandi', text: 'Silakan hubungi administrator program studi atau bagian IT untuk reset password akun Anda.', confirmButtonText: 'Mengerti', customClass: {confirmButton: 'btn btn-primary'}})">
                    Lupa Kata Sandi?
                </a>
            </div>
            <div class="position-relative">
                <span class="position-absolute top-50 translate-middle-y ms-4 text-gray-500">
                    <i class="fas fa-lock fs-5"></i>
                </span>
                <input class="form-control form-control-solid ps-12 pe-12 py-3 @error('password') is-invalid @enderror"
                    type="password"
                    name="password"
                    id="password_input"
                    placeholder="Masukkan kata sandi"
                    autocomplete="current-password"
                    required />
                <button type="button" class="btn btn-sm btn-icon position-absolute top-50 end-0 translate-middle-y me-2 text-gray-500 text-hover-primary password-toggle-btn"
                    data-toggle="password" data-target="#password_input" title="Lihat/Sembunyikan Kata Sandi">
                    <i class="fas fa-eye fs-6"></i>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback d-block mt-1 fs-7 fw-semibold">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Remember Me Checkbox -->
        <div class="d-flex flex-stack mb-8">
            <label class="form-check form-check-custom form-check-solid form-check-sm">
                <input class="form-check-input" type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} />
                <span class="form-check-label fw-bold text-gray-700 fs-7 ms-2">
                    Ingat sesi saya di perangkat ini
                </span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="text-center">
            <button type="submit" id="kt_sign_in_submit" class="btn btn-primary w-100 py-3 fw-bolder fs-6 shadow-sm">
                <span class="indicator-label">
                    Masuk ke SIMBIM <i class="fas fa-arrow-right ms-2 fs-6"></i>
                </span>
                <span class="indicator-progress d-none">
                    Memverifikasi kredensial...
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                </span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Form submission animation
    const loginForm = document.getElementById('kt_sign_in_form');
    if (loginForm) {
        loginForm.addEventListener('submit', function () {
            const submitBtn = document.getElementById('kt_sign_in_submit');
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
