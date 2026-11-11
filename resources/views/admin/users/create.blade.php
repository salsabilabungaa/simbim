@extends('layouts.admin')

@section('title', 'Tambah Akun Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <!-- Breadcrumb / Header -->
        <div class="d-flex align-items-center justify-content-between mb-6">
            <div>
                <a href="{{ route('admin.users.index') }}" class="text-muted text-hover-primary fs-7 mb-1 d-inline-block">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Pengguna
                </a>
                <h2 class="text-dark fw-bolder mb-0">Tambah Akun Pengguna Baru (Manual)</h2>
            </div>
            <a href="{{ route('admin.users.import-form') }}" class="btn btn-sm btn-light-success fw-bolder">
                <i class="fas fa-file-excel me-1"></i> Ingin Import Banyak Akun?
            </a>
        </div>

        <div class="card card-flush shadow-sm">
            <div class="card-header pt-6">
                <h3 class="card-title fw-bolder text-gray-800">Formulir Pendaftaran Akun</h3>
            </div>
            <div class="card-body pt-0">
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf

                    <!-- 1. Nama Lengkap -->
                    <div class="mb-6">
                        <label class="form-label fw-bolder text-dark required fs-7">Nama Lengkap Pengguna</label>
                        <input type="text" name="name" class="form-control form-control-solid @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" placeholder="Contoh: Muhammad Ihsan / Dr. Rahmawati, M.Kom." required />
                        @error('name')
                            <div class="invalid-feedback fs-7">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 2. Email & Nomor Induk (Grid) -->
                    <div class="row g-4 mb-6">
                        <div class="col-md-6">
                            <label class="form-label fw-bolder text-dark required fs-7">Alamat Email</label>
                            <input type="email" name="email" class="form-control form-control-solid @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" placeholder="pengguna@simbim.ac.id" required />
                            @error('email')
                                <div class="invalid-feedback fs-7">{{ $message }}</div>
                            @enderror
                            <div class="form-text fs-8 text-muted">Email digunakan sebagai salah satu opsi login.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bolder text-dark fs-7">Nomor Induk (NIM / NIP / ID)</label>
                            <input type="text" name="nomor_induk" class="form-control form-control-solid @error('nomor_induk') is-invalid @enderror" 
                                   value="{{ old('nomor_induk') }}" placeholder="Contoh: 2108107010001 (NIM) atau 1985... (NIP)" />
                            @error('nomor_induk')
                                <div class="invalid-feedback fs-7">{{ $message }}</div>
                            @enderror
                            <div class="form-text fs-8 text-muted">Bisa digunakan untuk login mahasiswa & dosen.</div>
                        </div>
                    </div>

                    <!-- 3. Pilihan Role Akun (Selector Cards) -->
                    <div class="mb-6">
                        <label class="form-label fw-bolder text-dark required fs-7 mb-3">Pilih Peran / Role Akun</label>
                        @error('role')
                            <div class="text-danger fs-7 mb-2 fw-bold">{{ $message }}</div>
                        @enderror
                        <div class="row g-3">
                            <!-- Mahasiswa -->
                            <div class="col-md-6">
                                <label class="border border-gray-300 rounded p-4 d-flex align-items-center cursor-pointer h-100 hover-border-primary role-card" for="role_mahasiswa">
                                    <input type="radio" class="form-check-input me-3" name="role" id="role_mahasiswa" value="mahasiswa" {{ old('role', 'mahasiswa') === 'mahasiswa' ? 'checked' : '' }} required />
                                    <div>
                                        <div class="fw-bolder text-dark fs-6">Mahasiswa</div>
                                        <div class="text-muted fs-8">Bimbingan skripsi &amp; akademik</div>
                                    </div>
                                </label>
                            </div>

                            <!-- Dosen -->
                            <div class="col-md-6">
                                <label class="border border-gray-300 rounded p-4 d-flex align-items-center cursor-pointer h-100 hover-border-success role-card" for="role_dosen">
                                    <input type="radio" class="form-check-input me-3" name="role" id="role_dosen" value="dosen" {{ old('role') === 'dosen' ? 'checked' : '' }} />
                                    <div>
                                        <div class="fw-bolder text-dark fs-6">Dosen</div>
                                        <div class="text-muted fs-8">Pembimbing &amp; wali akademik</div>
                                    </div>
                                </label>
                            </div>

                            <!-- Dewan Skripsi -->
                            <div class="col-md-6">
                                <label class="border border-gray-300 rounded p-4 d-flex align-items-center cursor-pointer h-100 hover-border-warning role-card" for="role_dewan">
                                    <input type="radio" class="form-check-input me-3" name="role" id="role_dewan" value="dewan_skripsi" {{ old('role') === 'dewan_skripsi' ? 'checked' : '' }} />
                                    <div>
                                        <div class="fw-bolder text-dark fs-6">Dewan Skripsi</div>
                                        <div class="text-muted fs-8">Penetapan sidang &amp; penguji</div>
                                    </div>
                                </label>
                            </div>

                            <!-- GKM -->
                            <div class="col-md-6">
                                <label class="border border-gray-300 rounded p-4 d-flex align-items-center cursor-pointer h-100 hover-border-info role-card" for="role_gkm">
                                    <input type="radio" class="form-check-input me-3" name="role" id="role_gkm" value="gkm" {{ old('role') === 'gkm' ? 'checked' : '' }} />
                                    <div>
                                        <div class="fw-bolder text-dark fs-6">GKM (Gugus Kendali Mutu)</div>
                                        <div class="text-muted fs-8">Penjaminan mutu &amp; kepatuhan sesi bimbingan</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Penetapan Dosen Pembimbing Akademik (Khusus Mahasiswa) -->
                    <div class="mb-6" id="dosen_wali_section" style="{{ old('role', 'mahasiswa') === 'mahasiswa' ? '' : 'display: none;' }}">
                        <label class="form-label fw-bolder text-dark fs-7 @if ($dosenList->isNotEmpty()) required @endif">Dosen Pembimbing Akademik (Dosen Wali)</label>
                        <select name="dosen_wali_id" id="dosen_wali_select" class="form-select form-select-solid @error('dosen_wali_id') is-invalid @enderror" data-control="select2" data-placeholder="-- Pilih Dosen Pembimbing Akademik PSTI UPI --" data-allow-clear="true">
                            <option value="">-- Pilih Dosen Pembimbing Akademik PSTI UPI --</option>
                            @foreach ($dosenList as $dsn)
                                <option value="{{ $dsn->id }}" {{ (string) old('dosen_wali_id') === (string) $dsn->id ? 'selected' : '' }}>
                                    {{ $dsn->name }} ({{ $dsn->nomor_induk ?: $dsn->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('dosen_wali_id')
                            <div class="invalid-feedback fs-7">{{ $message }}</div>
                        @enderror
                        <div class="form-text fs-8 text-muted">
                            Penetapan Dosen Pembimbing Akademik langsung disinkronkan ke portal Mahasiswa, Dosen Wali, dan GKM.
                        </div>
                    </div>

                    <!-- 5. Kata Sandi -->
                    <div class="mb-8">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bolder text-dark required fs-7 mb-0">Kata Sandi (Password)</label>
                            <button type="button" class="btn btn-link btn-color-primary btn-active-color-primary p-0 fs-8 fw-bold" onclick="document.getElementById('pwd_input').value='password123';">
                                Gunakan Password Bawaan (password123)
                            </button>
                        </div>
                        <div class="position-relative">
                            <input type="text" name="password" id="pwd_input" class="form-control form-control-solid pe-10 @error('password') is-invalid @enderror" 
                                   value="{{ old('password', 'password123') }}" placeholder="Minimal 6 karakter" required />
                            <span class="position-absolute top-50 end-0 translate-middle-y me-3 text-muted">
                                <i class="fas fa-key fs-6"></i>
                            </span>
                        </div>
                        @error('password')
                            <div class="invalid-feedback fs-7 d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text fs-8 text-muted">
                            Pengguna dapat mengganti kata sandi ini setelah login ke portal mereka.
                        </div>
                    </div>

                    <!-- Submit & Actions -->
                    <div class="d-flex justify-content-end gap-3 pt-4 border-top">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light fw-bolder">Batal</a>
                        <button type="submit" class="btn btn-primary fw-bolder px-6">
                            <i class="fas fa-check-circle me-1"></i> Simpan & Buat Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const roleInputs = document.querySelectorAll('input[name="role"]');
        const section = document.getElementById('dosen_wali_section');
        const select = document.getElementById('dosen_wali_select');
        const hasDosenOptions = @json($dosenList->isNotEmpty());

        function syncDosenWaliVisibility() {
            const checked = document.querySelector('input[name="role"]:checked');
            const isMahasiswa = checked && checked.value === 'mahasiswa';
            if (section) {
                section.style.display = isMahasiswa ? '' : 'none';
            }
            if (select) {
                select.required = Boolean(isMahasiswa && hasDosenOptions);
            }
        }

        roleInputs.forEach((input) => input.addEventListener('change', syncDosenWaliVisibility));
        syncDosenWaliVisibility();
    })();
</script>
@endpush

