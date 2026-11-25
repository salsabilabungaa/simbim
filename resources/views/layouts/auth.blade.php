<!DOCTYPE html>
<html lang="id">
<head>
    <base href="{{ url('/') }}/">
    <title>@yield('title', 'Autentikasi') — BIMASTI UPI</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />
    
    <!-- Google Fonts: Poppins -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    
    <!-- Global Stylesheets -->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/simbim-theme.css') }}" rel="stylesheet" type="text/css" />

    <style>
        body {
            background-color: #f4f7fb;
        }
        .role-selector-card {
            border: 2px solid #eff2f5;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }
        .role-selector-card:hover {
            border-color: #00A3E0;
            background-color: #EBF8FD;
        }
        .role-check-input:checked + .role-selector-card {
            border-color: #00A3E0;
            background-color: #EBF8FD;
            box-shadow: 0 4px 12px rgba(0, 163, 224, 0.15);
        }
        .role-check-input:checked + .role-selector-card .role-title {
            color: #00A3E0 !important;
        }
        .password-toggle-btn {
            z-index: 10;
        }
        .auth-card-wrapper {
            background-color: #ffffff;
            box-shadow: 0 10px 40px rgba(14, 32, 70, 0.06);
            border: 1px solid #e2e8f0;
        }
    </style>
    @stack('styles')
</head>
<body id="kt_body" class="bg-light">
    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-column flex-column-fluid py-10 px-4 justify-content-center align-items-center min-vh-100">
            <!-- Center Auth Container -->
            <div class="w-100 mw-550px">
                <!-- Top Brand Logo -->
                <div class="text-center mb-8">
                    <a href="{{ url('/') }}" class="d-inline-flex flex-column align-items-center text-decoration-none mb-3">
                        <img alt="BIMASTI - Universitas Pendidikan Indonesia" src="{{ asset('assets/media/logos/psti-logo.png') }}" class="h-60px mb-1" style="max-width: 270px; object-fit: contain;" />
                    </a>
                    <div class="d-flex justify-content-center align-items-center gap-2 mb-1">
                        <span class="badge badge-light-primary fw-bolder fs-7 px-3 py-1.5 text-uppercase ls-1">
                            <i class="fas fa-graduation-cap me-1.5 text-primary"></i> BIMASTI UPI
                        </span>
                    </div>
                    <div class="text-gray-700 fs-7 fw-bolder mt-1">
                        {{ config('simbim.tagline') }}
                    </div>
                    <div class="text-muted fs-8 fw-semibold">
                        {{ config('simbim.prodi') }} &bull; {{ config('simbim.universitas') }}
                    </div>
                </div>

                <!-- Session Status Alerts -->
                @if (session('status'))
                    <div class="alert alert-primary d-flex align-items-center p-4 mb-6 rounded-3">
                        <i class="fas fa-info-circle fs-3 text-primary me-3"></i>
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-gray-800">{{ session('status') }}</span>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success d-flex align-items-center p-4 mb-6 rounded-3">
                        <i class="fas fa-check-circle fs-3 text-success me-3"></i>
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-gray-800">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if ($errors->has('login'))
                    <div class="alert alert-danger d-flex align-items-center p-4 mb-6 rounded-3">
                        <i class="fas fa-exclamation-triangle fs-3 text-danger me-3"></i>
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-gray-800">{{ $errors->first('login') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Auth Content Card -->
                <div class="auth-card-wrapper rounded-3 p-8 p-md-12">
                    @yield('content')
                </div>

                <!-- Bottom Footer -->
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center text-gray-500 fs-8 mt-8 px-2 gap-3">
                    <span>&copy; {{ date('Y') }} BIMASTI &bull; {{ config('simbim.prodi') }} &mdash; {{ config('simbim.universitas') }}</span>
                    <!-- <div class="d-flex align-items-center gap-4">
                        <a href="javascript:void(0)" class="text-gray-500 text-hover-primary">Bantuan & FAQ</a>
                        <span class="bullet bullet-dot bg-gray-400"></span>
                        <a href="javascript:void(0)" class="text-gray-500 text-hover-primary">Panduan</a>
                        <span class="bullet bullet-dot bg-gray-400"></span>
                        <a href="javascript:void(0)" class="text-gray-500 text-hover-primary">Kontak Admin</a>
                    </div> -->
                </div>
            </div>
        </div>
    </div>

    <!-- Metronic Global Scripts -->
    <script>var hostUrl = "{{ asset('assets') }}/";</script>
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>

    <script>
        // Password Visibility Toggle Utility
        document.querySelectorAll('[data-toggle="password"]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const targetInput = document.querySelector(this.getAttribute('data-target'));
                const icon = this.querySelector('i');
                if (targetInput) {
                    if (targetInput.type === 'password') {
                        targetInput.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        targetInput.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
