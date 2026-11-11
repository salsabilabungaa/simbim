@extends('layouts.admin')

@section('title', 'Edit Akun Pengguna')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <!-- Breadcrumb / Header -->
        <div class="d-flex align-items-center justify-content-between mb-6">
            <div>
                <a href="{{ route('admin.users.index') }}" class="text-muted text-hover-primary fs-7 mb-1 d-inline-block">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Pengguna
                </a>
                <h2 class="text-dark fw-bolder mb-0">Edit Data Akun: {{ $user->name }}</h2>
            </div>
            <span class="badge badge-light-{{ $user->role_badge }} fs-7 fw-bolder px-3 py-2">
                {{ $user->role_label }}
            </span>
        </div>

        <div class="card card-flush shadow-sm">
            <div class="card-header pt-6">
                <h3 class="card-title fw-bolder text-gray-800">Perbarui Informasi Pengguna</h3>
            </div>
            <div class="card-body pt-0">
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <!-- 1. Nama Lengkap -->
                    <div class="mb-6">
                        <label class="form-label fw-bolder text-dark required fs-7">Nama Lengkap Pengguna</label>
                        <input type="text" name="name" class="form-control form-control-solid @error('name') is-invalid @enderror" 
                               value="{{ old('name', $user->name) }}" required />
                        @error('name')
                            <div class="invalid-feedback fs-7">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 2. Email & Nomor Induk (Grid) -->
                    <div class="row g-4 mb-6">
                        <div class="col-md-6">
                            <label class="form-label fw-bolder text-dark required fs-7">Alamat Email</label>
                            <input type="email" name="email" class="form-control form-control-solid @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $user->email) }}" required />
                            @error('email')
                                <div class="invalid-feedback fs-7">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bolder text-dark fs-7">Nomor Induk (NIM / NIP / ID)</label>
                            <input type="text" name="nomor_induk" class="form-control form-control-solid @error('nomor_induk') is-invalid @enderror" 
                                   value="{{ old('nomor_induk', $user->nomor_induk) }}" />
                            @error('nomor_induk')
                                <div class="invalid-feedback fs-7">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- 3. Pilihan Role Akun -->
                    <div class="mb-6">
                        <label class="form-label fw-bolder text-dark required fs-7 mb-2">Peran / Role Akun</label>
                        @if ($user->isAdmin())
                            <input type="hidden" name="role" value="admin" />
                            <div class="form-control form-control-solid text-muted fw-bold">Administrator (Tetap)</div>
                        @else
                            <select name="role" id="edit_role_select" class="form-select form-select-solid @error('role') is-invalid @enderror" data-control="select2" data-placeholder="-- Pilih Peran Akun --">
                                @foreach ($roles as $key => $lbl)
                                    <option value="{{ $key }}" {{ old('role', $user->role) === $key ? 'selected' : '' }}>
                                        {{ $lbl }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        @error('role')
                            <div class="invalid-feedback fs-7">{{ $message }}</div>
                        @enderror
                    </div>

                    @if (! $user->isAdmin())
                        <!-- 4. Penetapan Dosen Pembimbing Akademik (Khusus Mahasiswa) -->
                        <div class="mb-6" id="edit_dosen_wali_section" style="{{ old('role', $user->role) === 'mahasiswa' ? '' : 'display: none;' }}">
                            <label class="form-label fw-bolder text-dark fs-7">Dosen Pembimbing Akademik (Dosen Wali)</label>
                            <select name="dosen_wali_id" id="edit_dosen_wali_select" class="form-select form-select-solid @error('dosen_wali_id') is-invalid @enderror" data-control="select2" data-placeholder="-- Pilih Dosen Pembimbing Akademik PSTI UPI --" data-allow-clear="true">
                                <option value="">-- Pilih Dosen Pembimbing Akademik PSTI UPI --</option>
                                @foreach ($dosenList as $dsn)
                                    <option value="{{ $dsn->id }}" {{ (string) old('dosen_wali_id', $currentDosenWaliId ?? '') === (string) $dsn->id ? 'selected' : '' }}>
                                        {{ $dsn->name }} ({{ $dsn->nomor_induk ?: $dsn->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('dosen_wali_id')
                                <div class="invalid-feedback fs-7">{{ $message }}</div>
                            @enderror
                            <div class="form-text fs-8 text-muted">
                                Perubahan Dosen Wali akan langsung diperbarui pada portal Mahasiswa, Dosen Wali, dan GKM.
                            </div>
                        </div>
                    @endif

                    <!-- 5. Reset Kata Sandi (Opsional) -->
                    <div class="mb-8">
                        <label class="form-label fw-bolder text-dark fs-7 mb-1">Ganti Kata Sandi (Kosongkan jika tidak diubah)</label>
                        <input type="text" name="password" class="form-control form-control-solid @error('password') is-invalid @enderror" 
                               placeholder="Masukkan password baru jika ingin mereset sandi akun ini" />
                        @error('password')
                            <div class="invalid-feedback fs-7 d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text fs-8 text-muted">
                            Hanya isi kolom ini jika pengguna meminta reset kata sandi atau ingin mengganti password akun.
                        </div>
                    </div>

                    <!-- Submit & Actions -->
                    <div class="d-flex justify-content-end gap-3 pt-4 border-top">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light fw-bolder">Batal</a>
                        <button type="submit" class="btn btn-primary fw-bolder px-6">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
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
        const roleSelect = document.getElementById('edit_role_select');
        const section = document.getElementById('edit_dosen_wali_section');
        if (!roleSelect || !section) return;

        function syncEditDosenWaliVisibility() {
            section.style.display = roleSelect.value === 'mahasiswa' ? '' : 'none';
        }

        roleSelect.addEventListener('change', syncEditDosenWaliVisibility);
        syncEditDosenWaliVisibility();
    })();
</script>
@endpush

