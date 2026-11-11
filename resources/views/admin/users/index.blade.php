@extends('layouts.admin')

@section('title', 'Manajemen Akun Pengguna')

@section('content')
<!-- Header Card -->
<div class="card card-flush pb-0 mb-6 shadow-sm">
    <div class="card-body pt-6 pb-6">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-4">
            <div>
                <h2 class="text-dark fw-bolder mb-1">Manajemen Akun Pengguna</h2>
                <span class="text-muted fs-7">
                    Admin adalah satu-satunya otoritas yang mendata dan membuatkan akun untuk pengguna SIMBIM.
                </span>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary fw-bolder">
                    <i class="fas fa-user-plus me-1"></i> Tambah Akun Manual
                </a>
                <a href="{{ route('admin.users.import-form') }}" class="btn btn-success fw-bolder">
                    <i class="fas fa-file-excel me-1"></i> Import Excel / CSV
                </a>
            </div>
        </div>
    </div>
</div>

@if (session('import_result'))
    @php
        $res = session('import_result');
    @endphp
    <div class="alert alert-dismissible bg-light-{{ $res['imported_count'] > 0 ? 'success' : 'danger' }} border border-{{ $res['imported_count'] > 0 ? 'success' : 'danger' }} border-dashed p-6 mb-6">
        <h4 class="text-{{ $res['imported_count'] > 0 ? 'success' : 'danger' }} fw-bolder mb-2">
            <i class="fas fa-info-circle me-2"></i> Laporan Hasil Import Akun
        </h4>
        <div class="d-flex flex-wrap gap-4 mb-3">
            <div class="badge badge-light-primary fs-7 p-2 px-3">
                Total Baris: <strong>{{ $res['total_rows'] }}</strong>
            </div>
            <div class="badge badge-light-success fs-7 p-2 px-3">
                Berhasil Dibuat: <strong>{{ $res['imported_count'] }} akun</strong>
            </div>
            <div class="badge badge-light-danger fs-7 p-2 px-3">
                Dilewati / Gagal: <strong>{{ $res['skipped_count'] }} data</strong>
            </div>
        </div>

        @if (!empty($res['errors']))
            <div class="separator my-3"></div>
            <div class="text-dark fw-bold fs-7 mb-2">Catatan Data yang Dilewati:</div>
            <ul class="mb-0 fs-7 text-danger">
                @foreach (array_slice($res['errors'], 0, 10) as $err)
                    <li>Baris {{ $err['row'] }}: {{ $err['message'] }}</li>
                @endforeach
                @if (count($res['errors']) > 10)
                    <li>...dan {{ count($res['errors']) - 10 }} kesalahan lainnya.</li>
                @endif
            </ul>
        @endif
        <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
            <i class="fas fa-times fs-4 text-gray-700"></i>
        </button>
    </div>
@endif

<!-- Filter & Search Toolbar -->
<div class="card card-flush shadow-sm mb-6">
    <div class="card-body pt-6 pb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex flex-column flex-md-row gap-4 align-items-md-center justify-content-between">
            <!-- Role Filter Pills -->
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.users.index', array_filter(['search' => request('search')])) }}" 
                   class="btn btn-sm {{ !request('role') ? 'btn-dark' : 'btn-light' }} fw-bolder">
                    Semua ({{ $counts['all'] }})
                </a>
                <a href="{{ route('admin.users.index', array_filter(['role' => 'mahasiswa', 'search' => request('search')])) }}" 
                   class="btn btn-sm {{ request('role') === 'mahasiswa' ? 'btn-primary' : 'btn-light-primary' }} fw-bolder">
                    Mahasiswa ({{ $counts['mahasiswa'] }})
                </a>
                <a href="{{ route('admin.users.index', array_filter(['role' => 'dosen', 'search' => request('search')])) }}" 
                   class="btn btn-sm {{ request('role') === 'dosen' ? 'btn-success' : 'btn-light-success' }} fw-bolder">
                    Dosen ({{ $counts['dosen'] }})
                </a>
                <a href="{{ route('admin.users.index', array_filter(['role' => 'dewan_skripsi', 'search' => request('search')])) }}" 
                   class="btn btn-sm {{ request('role') === 'dewan_skripsi' ? 'btn-warning' : 'btn-light-warning' }} fw-bolder">
                    Dewan Skripsi ({{ $counts['dewan_skripsi'] }})
                </a>
                <a href="{{ route('admin.users.index', array_filter(['role' => 'gkm', 'search' => request('search')])) }}" 
                   class="btn btn-sm {{ request('role') === 'gkm' ? 'btn-info' : 'btn-light-info' }} fw-bolder">
                    GKM ({{ $counts['gkm'] }})
                </a>
                <a href="{{ route('admin.users.index', array_filter(['role' => 'admin', 'search' => request('search')])) }}" 
                   class="btn btn-sm {{ request('role') === 'admin' ? 'btn-danger' : 'btn-light-danger' }} fw-bolder">
                    Admin ({{ $counts['admin'] }})
                </a>
            </div>

            <!-- Search Input -->
            <div class="d-flex align-items-center gap-2">
                @if (request('role'))
                    <input type="hidden" name="role" value="{{ request('role') }}">
                @endif
                <div class="position-relative">
                    <span class="position-absolute top-50 translate-middle-y ms-3 text-gray-500">
                        <i class="fas fa-search fs-6"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="form-control form-control-solid form-control-sm ps-10 w-250px" 
                           placeholder="Cari nama, email, NIM / NIP..." />
                </div>
                <button type="submit" class="btn btn-sm btn-primary fw-bolder">Cari</button>
                @if (request('search') || request('role'))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-light" title="Reset filter">
                        <i class="fas fa-undo"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card card-flush shadow-sm">
    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table table-row-bordered table-row-gray-200 align-middle gs-0 gy-4">
                <thead>
                    <tr class="fw-bolder text-muted fs-7 text-uppercase border-bottom">
                        <th class="w-50px">No</th>
                        <th class="min-w-200px">Nama & Email</th>
                        <th class="min-w-140px">Nomor Induk (NIM / NIP)</th>
                        <th class="min-w-130px">Role Akun</th>
                        <th class="min-w-160px">Program Studi</th>
                        <th class="min-w-120px">Tgl Registrasi</th>
                        <th class="min-w-100px text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $index => $u)
                        <tr>
                            <td class="text-gray-600 fs-7 fw-bold">
                                {{ $users->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40px symbol-circle me-3 bg-light-{{ $u->role_badge }} text-{{ $u->role_badge }} fw-bolder d-flex align-items-center justify-content-center">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark fw-bolder fs-6">{{ $u->name }}</span>
                                        <span class="text-muted fs-7">{{ $u->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if ($u->nomor_induk)
                                    <span class="badge badge-light-dark fw-bold font-monospace fs-7">{{ $u->nomor_induk }}</span>
                                @else
                                    <span class="text-muted fs-8 fst-italic">Belum diisi</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-light-{{ $u->role_badge }} fw-bolder fs-8">
                                    {{ $u->role_label }}
                                </span>
                            </td>
                            <td>
                                <span class="text-gray-700 fs-7">{{ $u->program_studi ?? '-' }}</span>
                                @if ($u->isMahasiswa())
                                    @php
                                        $dpaName = $u->dosenWali?->name ?? $u->perwalianMahasiswas->first()?->dosenWali?->name;
                                    @endphp
                                    <div class="text-muted fs-8 mt-1">
                                        DPA: <strong class="text-dark">{{ $dpaName ?: 'Belum Ditetapkan' }}</strong>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="text-gray-600 fs-8">{{ $u->created_at ? $u->created_at->format('d M Y') : '-' }}</span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-icon btn-light-primary btn-sm" title="Edit Akun">
                                        <i class="fas fa-edit fs-7"></i>
                                    </a>
                                    @if ($u->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $u->name }}? Tindakan ini tidak dapat dibatalkan.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-light-danger btn-sm" title="Hapus Akun">
                                                <i class="fas fa-trash-alt fs-7"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-icon btn-light btn-sm" disabled title="Akun Anda Sendiri">
                                            <i class="fas fa-lock fs-8 text-muted"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-user-slash fs-3x text-muted mb-3"></i>
                                    <div class="text-gray-600 fw-bold fs-6">Tidak ditemukan pengguna</div>
                                    <div class="text-muted fs-7 mb-4">Coba ubah kata kunci pencarian atau filter peran akun.</div>
                                    <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus me-1"></i> Tambah Akun Baru
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap pt-4">
            <span class="text-muted fs-7">
                Menampilkan {{ $users->firstItem() ?? 0 }} sampai {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} akun pengguna
            </span>
            <div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

