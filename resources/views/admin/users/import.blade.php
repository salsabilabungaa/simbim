@extends('layouts.admin')

@section('title', 'Import Akun Pengguna')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-9">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-6">
            <div>
                <a href="{{ route('admin.users.index') }}" class="text-muted text-hover-primary fs-7 mb-1 d-inline-block">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Pengguna
                </a>
                <h2 class="text-dark fw-bolder mb-0">Import Akun Pengguna Massal (.xlsx / .csv)</h2>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-light-primary fw-bolder">
                <i class="fas fa-user-plus me-1"></i> Tambah 1 Akun Manual
            </a>
        </div>

        @if (session('import_result') && !empty(session('import_result')['errors']))
            <div class="alert alert-danger d-flex flex-column p-5 mb-7 border border-danger border-dashed">
                <h5 class="text-danger fw-bolder mb-2">
                    <i class="fas fa-exclamation-triangle me-2"></i> Terdapat {{ count(session('import_result')['errors']) }} Catatan Data yang Dilewati:
                </h5>
                <ul class="mb-0 fs-7">
                    @foreach (session('import_result')['errors'] as $err)
                        <li>Baris {{ $err['row'] }}: {{ $err['message'] }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Card Form Upload -->
        <div class="card card-flush shadow-sm mb-8">
            <div class="card-header pt-6">
                <h3 class="card-title fw-bolder text-gray-800">Unggah Berkas Spreadsheet</h3>
                <div class="card-toolbar gap-2">
                    <a href="{{ route('admin.users.template', 'xlsx') }}" class="btn btn-sm btn-light-success fw-bolder">
                        <i class="fas fa-file-excel me-1"></i> Unduh Template Excel (.xlsx)
                    </a>
                    <a href="{{ route('admin.users.template', 'csv') }}" class="btn btn-sm btn-light-primary fw-bolder">
                        <i class="fas fa-file-csv me-1"></i> Unduh Template CSV (.csv)
                    </a>
                </div>
            </div>

            <div class="card-body pt-2 pb-8">
                <form method="POST" action="{{ route('admin.users.import') }}" enctype="multipart/form-data" id="import_form">
                    @csrf

                    <!-- File Input -->
                    <div class="mb-6">
                        <label class="form-label fw-bolder text-dark required fs-7">Pilih File Dokumen (.xlsx atau .csv)</label>
                        <div class="input-group">
                            <input type="file" name="file" class="form-control form-control-solid @error('file') is-invalid @enderror" 
                                   accept=".xlsx, .xls, .csv, text/csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required />
                        </div>
                        @error('file')
                            <div class="invalid-feedback fs-7 d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text fs-8 text-muted mt-2">
                            Mendukung file Excel (.xlsx) dan CSV (.csv) dengan ukuran maksimal 10 MB.
                        </div>
                    </div>

                    <!-- Password Bawaan -->
                    <div class="mb-8">
                        <label class="form-label fw-bolder text-dark fs-7">Password Default (Jika Kolom Password pada File Kosong)</label>
                        <input type="text" name="default_password" class="form-control form-control-solid @error('default_password') is-invalid @enderror" 
                               value="{{ old('default_password', 'simbim123') }}" placeholder="simbim123" />
                        @error('default_password')
                            <div class="invalid-feedback fs-7 d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text fs-8 text-muted">
                            Setiap baris data yang kolom password-nya dikosongkan akan otomatis memakai password default ini.
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 pt-4 border-top">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light fw-bolder">Batal</a>
                        <button type="submit" id="btn_submit_import" class="btn btn-success fw-bolder px-8">
                            <span class="indicator-label">
                                <i class="fas fa-upload me-2"></i> Mulai Import Data Akun
                            </span>
                            <span class="indicator-progress d-none">
                                Memproses Dokumen...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Panduan Format Kolom -->
        <div class="card card-flush shadow-sm">
            <div class="card-header pt-6">
                <h3 class="card-title fw-bolder text-gray-800">Panduan Susunan Kolom Spreadsheet</h3>
            </div>
            <div class="card-body pt-0 pb-6">
                <p class="text-gray-600 fs-7 mb-4">
                    Pastikan baris pertama spreadsheet merupakan nama header kolom berikut (urutan kolom fleksibel):
                </p>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle gs-4 gy-3">
                        <thead class="bg-light">
                            <tr class="fw-bolder text-dark fs-7">
                                <th class="min-w-120px">Nama Kolom</th>
                                <th class="min-w-100px">Sifat</th>
                                <th class="min-w-180px">Pilihan / Format</th>
                                <th class="min-w-250px">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="fs-7">
                            <tr>
                                <td class="fw-bolder font-monospace text-primary">nama</td>
                                <td><span class="badge badge-light-danger fw-bold">Wajib</span></td>
                                <td>Teks bebas</td>
                                <td>Nama lengkap pengguna / gelar</td>
                            </tr>
                            <tr>
                                <td class="fw-bolder font-monospace text-primary">email</td>
                                <td><span class="badge badge-light-danger fw-bold">Wajib</span></td>
                                <td>Format email valid, unik</td>
                                <td>Digunakan sebagai kredensial login utama</td>
                            </tr>
                            <tr>
                                <td class="fw-bolder font-monospace text-primary">nomor_induk</td>
                                <td><span class="badge badge-light-warning fw-bold">Opsional</span></td>
                                <td>NIM / NIP unik</td>
                                <td>NIM untuk mahasiswa, NIP untuk dosen</td>
                            </tr>
                            <tr>
                                <td class="fw-bolder font-monospace text-primary">role</td>
                                <td><span class="badge badge-light-danger fw-bold">Wajib</span></td>
                                <td><code>mahasiswa</code>, <code>dosen</code>, <code>dewan_skripsi</code>, <code>gkm</code>, <code>admin</code></td>
                                <td>Menentukan hak akses menu pengguna dalam sistem</td>
                            </tr>
                            <tr>
                                <td class="fw-bolder font-monospace text-primary">program_studi</td>
                                <td><span class="badge badge-light-secondary fw-bold">Opsional</span></td>
                                <td>Contoh: <code>Pendidikan Sistem dan Teknologi Informasi (PSTI)</code></td>
                                <td>Default diisi Pendidikan Sistem dan Teknologi Informasi (PSTI) — UPI jika dikosongkan</td>
                            </tr>
                            <tr>
                                <td class="fw-bolder font-monospace text-primary">password</td>
                                <td><span class="badge badge-light-secondary fw-bold">Opsional</span></td>
                                <td>Minimal 6 karakter</td>
                                <td>Jika kosong, otomatis diisi dengan password default di atas</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 p-4 rounded bg-light-primary border border-primary border-dashed">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-lightbulb text-primary fs-3 me-3"></i>
                        <div class="fs-7 text-gray-700">
                            <strong>Tips Praktis:</strong> Unduh template Excel atau CSV yang telah kami sediakan di atas, isi baris data pengguna Anda, lalu unggah kembali melalui formulir ini.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const importForm = document.getElementById('import_form');
    if (importForm) {
        importForm.addEventListener('submit', function () {
            const btn = document.getElementById('btn_submit_import');
            if (btn) {
                btn.querySelector('.indicator-label').classList.add('d-none');
                btn.querySelector('.indicator-progress').classList.remove('d-none');
                btn.setAttribute('disabled', 'disabled');
            }
        });
    }
</script>
@endpush

