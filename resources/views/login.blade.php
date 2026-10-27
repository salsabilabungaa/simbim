<!DOCTYPE html>
<html lang="id">
<head>
    <base href="{{ url('/') }}/">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Masuk — SIMBIM Universitas Syiah Kuala</title>
    <meta name="description" content="Sistem Informasi Bimbingan Tugas Akhir dan Bimbingan Akademik Mahasiswa" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />
    
    <!-- Google Fonts Poppins -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    
    <!-- Metronic Global Stylesheets Bundle -->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />

    <style>
        :root {
            --simbim-primary: #1e40af;
            --simbim-primary-hover: #1d4ed8;
            --simbim-accent: #3b82f6;
            --simbim-bg: #0f172a;
        }

        html, body {
            min-height: 100%;
            height: auto;
            overflow-x: hidden;
            overflow-y: auto;
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #0b1329;
        }

        .auth-page-container {
            min-height: 100vh;
            height: auto;
            display: flex;
            background: linear-gradient(135deg, #0b1329 0%, #111e38 50%, #0f172a 100%);
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .auth-decoration-circle-1 {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, rgba(30, 64, 175, 0.02) 70%, transparent 100%);
            top: -150px;
            left: -100px;
            pointer-events: none;
        }

        .auth-decoration-circle-2 {
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.12) 0%, rgba(30, 58, 138, 0.03) 70%, transparent 100%);
            bottom: -200px;
            right: 25%;
            pointer-events: none;
        }

        .brand-panel {
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(12px);
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            position: relative;
            z-index: 2;
            overflow-y: auto;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 1rem;
            padding: 1.25rem 1.5rem;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(59, 130, 246, 0.4);
            transform: translateY(-2px);
        }

        .login-card {
            background: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.8);
            width: 100%;
            max-width: 480px;
            position: relative;
            z-index: 5;
        }

        .form-control-simbim {
            border: 1.5px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0.875rem 1.15rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            background-color: #f8fafc;
        }

        .form-control-simbim:focus {
            background-color: #ffffff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        }

        .btn-simbim-primary {
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
            border: none;
            border-radius: 0.75rem;
            padding: 0.95rem 1.5rem;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.3px;
            color: #ffffff;
            box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);
            transition: all 0.25s ease;
        }

        .btn-simbim-primary:hover {
            background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%);
            box-shadow: 0 14px 25px -5px rgba(37, 99, 235, 0.5);
            transform: translateY(-1px);
            color: #ffffff;
        }

        .btn-simbim-primary:active {
            transform: translateY(1px);
        }

        .demo-box {
            background: #f1f5f9;
            border: 1px dashed #cbd5e1;
            border-radius: 0.85rem;
            padding: 0.9rem 1.15rem;
        }

        .demo-chip {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 0.45rem 0.8rem;
            font-size: 0.825rem;
            cursor: pointer;
            transition: all 0.2s;
            user-select: none;
        }

        .demo-chip:hover {
            border-color: #3b82f6;
            background-color: #eff6ff;
            color: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.15);
        }

        .demo-chip:active {
            transform: translateY(0);
        }

        .badge-live-pulse {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #10b981;
            margin-right: 6px;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulse-green 2s infinite;
        }

        @keyframes pulse-green {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
    </style>
</head>
<body id="kt_body" class="bg-body">
    <!-- Wrapper Utama: min-h-screen dan overflow-y-auto aktif tanpa overflow-hidden yang memotong layar -->
    <div class="auth-page-container d-flex flex-column flex-lg-row">
        <!-- Ambient Decorative Circles -->
        <div class="auth-decoration-circle-1"></div>
        <div class="auth-decoration-circle-2"></div>

        <!-- Left Column: Branding & Feature Highlights -->
        <div class="brand-panel d-none d-lg-flex flex-column justify-content-between p-12 p-xl-16 w-100 w-lg-50 text-white">
            <div>
                <!-- Brand Logo & Header -->
                <div class="d-flex align-items-center mb-10">
                    <div class="d-flex align-items-center justify-content-center bg-primary rounded-3 w-50px h-50px me-4 shadow">
                        <i class="fas fa-graduation-cap text-white fs-2"></i>
                    </div>
                    <div>
                        <h2 class="text-white fw-bolder fs-2x mb-0 ls-1">SIMBIM</h2>
                        <span class="text-white-50 fs-7 fw-semibold">Sistem Informasi Bimbingan Skripsi & Akademik</span>
                    </div>
                </div>

                <!-- Badge Status -->
                <div class="d-inline-flex align-items-center bg-white bg-opacity-10 rounded-pill px-4 py-2 mb-8 border border-white border-opacity-10">
                    <span class="badge-live-pulse"></span>
                    <span class="fs-7 fw-semibold text-white">Portal Akademik USK &bull; Semester Ganjil 2026/2027</span>
                </div>

                <!-- Main Tagline -->
                <h1 class="text-white fw-bolder fs-1 mb-5 lh-base">
                    Kelola Bimbingan Skripsi & Akademik dengan Lebih Mudah, Cepat, dan Transparan.
                </h1>
                <p class="text-white-50 fs-6 fw-normal mb-10 max-w-500px">
                    Aplikasi terintegrasi untuk mahasiswa dan dosen pembimbing dalam memantau progress penulisan tugas akhir, konsultasi berkala, serta evaluasi kemajuan studi.
                </p>

                <!-- Feature Highlights -->
                <div class="d-flex flex-column gap-4 mb-10">
                    <div class="feature-card d-flex align-items-center">
                        <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-20 rounded-circle w-45px h-45px me-4 flex-shrink-0">
                            <i class="fas fa-clipboard-check text-info fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-6 text-white">Monitoring Progres Tugas Akhir</div>
                            <div class="text-white-50 fs-8">Catat progress revisi per bab, upload berkas draft, dan pantau status persetujuan pembimbing.</div>
                        </div>
                    </div>

                    <div class="feature-card d-flex align-items-center">
                        <div class="d-flex align-items-center justify-content-center bg-success bg-opacity-20 rounded-circle w-45px h-45px me-4 flex-shrink-0">
                            <i class="fas fa-calendar-alt text-success fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-6 text-white">Jadwal Konsultasi Terstruktur</div>
                            <div class="text-white-50 fs-8">Atur waktu konsultasi tatap muka maupun daring langsung bersama dosen wali & pembimbing.</div>
                        </div>
                    </div>

                    <div class="feature-card d-flex align-items-center">
                        <div class="d-flex align-items-center justify-content-center bg-warning bg-opacity-20 rounded-circle w-45px h-45px me-4 flex-shrink-0">
                            <i class="fas fa-history text-warning fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-6 text-white">Logbook & Catatan Digital</div>
                            <div class="text-white-50 fs-8">Riwayat saran dan masukan dosen tersimpan otomatis, siap digunakan untuk syarat sidang skripsi.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Left Footer -->
            <div class="pt-6 border-top border-white border-opacity-10 d-flex align-items-center justify-content-between text-white-50 fs-8">
                <div>&copy; {{ date('Y') }} SIMBIM — Universitas Syiah Kuala</div>
                <div>Fakultas MIPA / Teknik Informatika</div>
            </div>
        </div>

        <!-- Right Column: Login Form Container (Scrollable, Fleksibel, Bebas Terpotong) -->
        <div class="d-flex flex-column flex-center flex-grow-1 p-6 p-md-10 py-12 justify-content-center align-items-center w-100 min-vh-100 overflow-y-auto">
            <!-- Mobile Brand Header (Visible on small screens) -->
            <div class="d-flex d-lg-none align-items-center mb-8 text-center flex-column">
                <div class="d-flex align-items-center justify-content-center bg-primary rounded-3 w-50px h-50px mb-3 shadow">
                    <i class="fas fa-graduation-cap text-white fs-2"></i>
                </div>
                <h2 class="text-white fw-bolder fs-2 mb-0">SIMBIM</h2>
                <span class="text-white-50 fs-7">Sistem Informasi Bimbingan USK</span>
            </div>

            <!-- Login Card -->
            <div class="login-card p-8 p-md-12 my-auto">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h2 class="text-gray-900 fw-bolder fs-2x mb-2">Selamat Datang 👋</h2>
                    <p class="text-muted fs-6 mb-0">Silakan masukkan Email atau NIM dan Password Anda</p>
                </div>

                <!-- Flash Success Message (e.g. from Logout) -->
                @if (session('success'))
                    <div class="alert alert-success d-flex align-items-center p-4 mb-6 rounded-3 border-0 bg-light-success">
                        <span class="svg-icon svg-icon-2hx svg-icon-success me-3">
                            <i class="fas fa-check-circle fs-3 text-success"></i>
                        </span>
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-success fs-7">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Flash Error / Validation Alert -->
                @if ($errors->any())
                    <div class="alert alert-danger d-flex align-items-center p-4 mb-6 rounded-3 border-0 bg-light-danger" role="alert">
                        <span class="svg-icon svg-icon-2hx svg-icon-danger me-3">
                            <i class="fas fa-exclamation-triangle fs-3 text-danger"></i>
                        </span>
                        <div class="d-flex flex-column">
                            <h5 class="mb-1 text-danger fw-bolder fs-7">Login Gagal</h5>
                            <span class="text-danger fs-8 fw-semibold">
                                {{ $errors->first('email') ?: $errors->first('login') ?: $errors->first() }}
                            </span>
                        </div>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login.submit') }}" id="login_form" novalidate="novalidate">
                    @csrf

                    <!-- Identifier Input (Email or NIM) -->
                    <div class="fv-row mb-6">
                        <label for="email" class="form-label fs-7 fw-bolder text-gray-800 text-uppercase ls-1">
                            Email atau NIM Mahasiswa <span class="text-danger">*</span>
                        </label>
                        <div class="position-relative">
                            <span class="position-absolute top-50 translate-middle-y ms-4 text-gray-400">
                                <i class="fas fa-id-card fs-5"></i>
                            </span>
                            <input 
                                type="text" 
                                id="email" 
                                name="email" 
                                class="form-control form-control-simbim ps-12 @error('email') is-invalid @enderror @error('login') is-invalid @enderror" 
                                placeholder="NIM: 2108107010001 atau Email" 
                                value="{{ old('email', old('login')) }}" 
                                required 
                                autofocus 
                                autocomplete="username"
                            />
                        </div>
                        @error('email')
                            <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                        @else
                            @error('login')
                                <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                            @enderror
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div class="fv-row mb-6">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label fs-7 fw-bolder text-gray-800 text-uppercase ls-1 mb-0">
                                Password <span class="text-danger">*</span>
                            </label>
                        </div>
                        <div class="position-relative">
                            <span class="position-absolute top-50 translate-middle-y ms-4 text-gray-400">
                                <i class="fas fa-lock fs-5"></i>
                            </span>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-control form-control-simbim ps-12 pe-12 @error('password') is-invalid @enderror" 
                                placeholder="Masukkan password Anda" 
                                required 
                                autocomplete="current-password"
                            />
                            <button 
                                type="button" 
                                id="btn_toggle_password" 
                                class="btn btn-sm btn-icon position-absolute top-50 end-0 translate-middle-y me-2 text-gray-500 text-hover-primary" 
                                title="Lihat/Sembunyikan Password" 
                                tabindex="-1"
                            >
                                <i class="far fa-eye fs-5" id="toggle_password_icon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="d-flex flex-stack justify-content-between align-items-center mb-8">
                        <label class="form-check form-check-custom form-check-solid cursor-pointer">
                            <input class="form-check-input h-20px w-20px" type="checkbox" name="remember" id="remember_me" {{ old('remember') ? 'checked' : '' }} />
                            <span class="form-check-label fw-semibold text-gray-700 fs-7 ms-2">
                                Ingat sesi saya
                            </span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid mb-6">
                        <button type="submit" id="btn_submit_login" class="btn btn-simbim-primary">
                            <span class="indicator-label d-flex align-items-center justify-content-center">
                                <i class="fas fa-sign-in-alt me-2 fs-5"></i> Masuk ke SIMBIM
                            </span>
                            <span class="indicator-progress d-none">
                                Memverifikasi... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>

                    <!-- Quick Demo Credentials Box -->
                    <div class="demo-box mt-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold fs-8 text-gray-700">
                                <i class="fas fa-key text-primary me-1"></i> Akun Demo Mahasiswa:
                            </span>
                            <span class="badge badge-light-primary fs-9 fw-bolder">Tersedia</span>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <!-- Chip NIM -->
                            <button 
                                type="button" 
                                class="demo-chip text-gray-700 fw-semibold" 
                                data-login="2108107010001"
                                onclick="fillLogin('2108107010001')"
                            >
                                <i class="fas fa-mouse-pointer text-primary me-1 fs-9"></i> NIM: <strong>2108107010001</strong>
                            </button>
                            <!-- Chip Email -->
                            <button 
                                type="button" 
                                class="demo-chip text-gray-700 fw-semibold" 
                                data-login="andi.pratama@mhs.usk.ac.id"
                                onclick="fillLogin('andi.pratama@mhs.usk.ac.id')"
                            >
                                <i class="fas fa-envelope text-primary me-1 fs-9"></i> Email: <strong>andi.pratama@mhs.usk.ac.id</strong>
                            </button>
                        </div>
                        <div class="fs-9 text-muted mt-2">
                            <i class="fas fa-info-circle me-1"></i> Klik chip di atas untuk mengisi Email/NIM dan Password (<code>password</code>) secara otomatis.
                        </div>
                    </div>
                </form>
            </div>

            <!-- Card Bottom Subtitle -->
            <div class="text-center text-white-50 fs-8 mt-6">
                Butuh bantuan login? Hubungi UPT TIK atau Bagian Akademik Fakultas MIPA USK.
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Toggle Password Visibility
        const btnTogglePassword = document.getElementById('btn_toggle_password');
        const passwordInput = document.getElementById('password') || document.getElementById('password_input') || document.querySelector('input[name="password"]');
        const toggleIcon = document.getElementById('toggle_password_icon');

        if (btnTogglePassword && passwordInput && toggleIcon) {
            btnTogglePassword.addEventListener('click', function () {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                toggleIcon.classList.toggle('fa-eye', !isPassword);
                toggleIcon.classList.toggle('fa-eye-slash', isPassword);
            });
        }

        // JavaScript Autofill Sederhana untuk Chip Akun Demo
        function fillLogin(loginValue) {
            const loginField = document.querySelector('input[name="login"]') 
                || document.querySelector('input[name="email"]')
                || document.getElementById('email');

            const passwordField = document.querySelector('input[name="password"]')
                || document.getElementById('password');

            if (loginField) {
                loginField.value = loginValue;
                loginField.dispatchEvent(new Event('input', { bubbles: true }));
                loginField.dispatchEvent(new Event('change', { bubbles: true }));
                loginField.focus();
            }

            if (passwordField) {
                passwordField.value = 'password';
                passwordField.dispatchEvent(new Event('input', { bubbles: true }));
                passwordField.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }

        // Alias fungsi agar kompatibel
        function fillDemoCredentials(identifier, password) {
            fillLogin(identifier);
        }

        // Event listener tambahan agar chip tetap responsif diklik
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.demo-chip').forEach(function (chip) {
                chip.addEventListener('click', function (e) {
                    e.preventDefault();
                    const loginVal = this.getAttribute('data-login');
                    if (loginVal) {
                        fillLogin(loginVal);
                    }
                });
            });
        });

        // Form submit progress indicator
        const loginForm = document.getElementById('login_form');
        const btnSubmit = document.getElementById('btn_submit_login');

        if (loginForm && btnSubmit) {
            loginForm.addEventListener('submit', function () {
                const label = btnSubmit.querySelector('.indicator-label');
                const progress = btnSubmit.querySelector('.indicator-progress');
                if (label && progress) {
                    label.classList.add('d-none');
                    progress.classList.remove('d-none');
                    btnSubmit.setAttribute('disabled', 'disabled');
                }
            });
        }
    </script>
</body>
</html>
