<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PerwalianMahasiswa;
use App\Models\User;
use App\Services\UserImportService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct(
        protected UserImportService $importService
    ) {}

    /**
     * Display a listing of all users with search and role filtering.
     */
    public function index(Request $request): View
    {
        $query = User::query()->with([
            'dosenWali:id,name',
            'perwalianMahasiswas' => fn ($q) => $q->with('dosenWali:id,name')->latest('updated_at'),
        ]);

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $keyword = trim($request->search);
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('nomor_induk', 'like', "%{$keyword}%")
                    ->orWhere('program_studi', 'like', "%{$keyword}%");
            });
        }

        $users = $query->latest()->paginate(6)->withQueryString();

        $counts = [
            'all' => User::count(),
            'mahasiswa' => User::where('role', 'mahasiswa')->count(),
            'dosen' => User::where('role', 'dosen')->count(),
            'dewan_skripsi' => User::where('role', 'dewan_skripsi')->count(),
            'gkm' => User::where('role', 'gkm')->count(),
            'admin' => User::where('role', 'admin')->count(),
        ];

        return view('admin.users.index', compact('users', 'counts'));
    }

    /**
     * Show the form for creating a new user manually.
     */
    public function create(): View
    {
        return view('admin.users.create', $this->userFormOptions());
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'nomor_induk' => ['nullable', 'string', 'max:50', 'unique:users,nomor_induk'],
            'role' => ['required', 'string', Rule::in(['mahasiswa', 'dosen', 'dewan_skripsi', 'gkm'])],
            'program_studi' => ['nullable', 'string', 'max:255'],
            'dosen_wali_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('role', 'dosen')),
            ],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'name.required' => 'Nama lengkap pengguna wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar di SIMBIM.',
            'nomor_induk.unique' => 'Nomor induk (NIM/NIP) ini sudah terdaftar di SIMBIM.',
            'role.required' => 'Peran / Role akun wajib dipilih.',
            'role.in' => 'Peran akun yang dipilih tidak valid.',
            'dosen_wali_id.exists' => 'Dosen Pembimbing Akademik (Dosen Wali) yang dipilih tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal terdiri dari :min karakter.',
        ]);

        $validated['program_studi'] = ! empty($validated['program_studi'])
            ? $validated['program_studi']
            : config('simbim.prodi', 'Pendidikan Sistem dan Teknologi Informasi (PSTI)');
        $validated['dosen_wali_id'] = $validated['role'] === 'mahasiswa'
            ? (! empty($validated['dosen_wali_id']) ? (int) $validated['dosen_wali_id'] : null)
            : null;
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        $this->syncMahasiswaDosenWali($user, $validated['dosen_wali_id']);

        return redirect()->route('admin.users.index')->with(
            'success',
            "Akun pengguna {$user->name} ({$user->role_label}) berhasil dibuat!"
        );
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $currentDosenWaliId = $user->dosen_wali_id;
        if ($currentDosenWaliId === null && $user->isMahasiswa()) {
            try {
                $currentDosenWaliId = $user->perwalianMahasiswas()->latest('updated_at')->value('dosen_wali_id');
            } catch (\Throwable) {
                $currentDosenWaliId = null;
            }
        }

        return view('admin.users.edit', array_merge([
            'user' => $user,
            'currentDosenWaliId' => $currentDosenWaliId,
        ], $this->userFormOptions()));
    }

    /**
     * Opsi peran pengguna dan daftar dosen wali resmi PSTI UPI untuk form manajemen akun.
     *
     * @return array{roles: array<string, string>, dosenList: Collection<int, User>}
     */
    private function userFormOptions(): array
    {
        try {
            $dosenList = User::query()
                ->select(['id', 'name', 'nomor_induk', 'email'])
                ->where('role', 'dosen')
                ->orderBy('name')
                ->get();
        } catch (\Throwable) {
            $dosenList = collect();
        }

        return [
            'roles' => [
                'mahasiswa' => 'Mahasiswa',
                'dosen' => 'Dosen Pembimbing / Wali',
                'dewan_skripsi' => 'Dewan Skripsi',
                'gkm' => 'Gugus Kendali Mutu (GKM)',
            ],
            'dosenList' => $dosenList,
        ];
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $allowedRoles = $user->isAdmin()
            ? ['admin']
            : ['mahasiswa', 'dosen', 'dewan_skripsi', 'gkm'];

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'nomor_induk' => ['nullable', 'string', 'max:50', Rule::unique('users', 'nomor_induk')->ignore($user->id)],
            'role' => ['required', 'string', Rule::in($allowedRoles)],
            'program_studi' => ['nullable', 'string', 'max:255'],
            'dosen_wali_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('role', 'dosen')),
            ],
            'password' => ['nullable', 'string', 'min:6'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar untuk pengguna lain.',
            'nomor_induk.unique' => 'Nomor induk ini sudah terdaftar untuk pengguna lain.',
            'dosen_wali_id.exists' => 'Dosen Pembimbing Akademik (Dosen Wali) yang dipilih tidak valid.',
            'password.min' => 'Kata sandi baru minimal :min karakter.',
        ]);

        $validated['program_studi'] = ! empty($validated['program_studi'])
            ? $validated['program_studi']
            : config('simbim.prodi', 'Pendidikan Sistem dan Teknologi Informasi (PSTI)');
        $validated['dosen_wali_id'] = $validated['role'] === 'mahasiswa'
            ? (! empty($validated['dosen_wali_id']) ? (int) $validated['dosen_wali_id'] : null)
            : null;

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        $this->syncMahasiswaDosenWali($user, $validated['dosen_wali_id']);

        return redirect()->route('admin.users.index')->with(
            'success',
            "Data akun {$user->name} berhasil diperbarui!"
        );
    }

    /**
     * Sinkronkan penugasan Dosen Pembimbing Akademik (Dosen Wali) pada akun mahasiswa dan tabel perwalian.
     */
    private function syncMahasiswaDosenWali(User $user, ?int $dosenWaliId): void
    {
        if (! $user->isMahasiswa() || $dosenWaliId === null) {
            return;
        }

        $perwalian = PerwalianMahasiswa::where('mahasiswa_id', $user->id)
            ->latest('updated_at')
            ->first();

        if ($perwalian !== null) {
            $perwalian->update(['dosen_wali_id' => $dosenWaliId]);

            return;
        }

        PerwalianMahasiswa::create([
            'mahasiswa_id' => $user->id,
            'dosen_wali_id' => $dosenWaliId,
            'program_studi' => $user->program_studi ?: config('simbim.prodi', 'Pendidikan Sistem dan Teknologi Informasi (PSTI)'),
            'angkatan' => (string) now()->year,
            'semester_berjalan' => 1,
            'tahun_akademik' => 'Ganjil 2026/2027',
            'ipk' => 0.00,
            'sks_diambil' => 0,
            'status_perwalian' => 'belum',
            'status_krs' => 'Belum Mengisi KRS/IRS',
        ]);
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with(
                'error',
                'Anda tidak dapat menghapus akun admin yang sedang Anda gunakan saat ini.'
            );
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with(
            'success',
            "Akun {$name} berhasil dihapus dari sistem SIMBIM."
        );
    }

    /**
     * Show the user import form.
     */
    public function importForm(): View
    {
        return view('admin.users.import');
    }

    /**
     * Handle Excel / CSV bulk account import.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,csv,txt', 'max:10240'],
            'default_password' => ['nullable', 'string', 'min:6'],
        ], [
            'file.required' => 'Silakan pilih dokumen Excel (.xlsx) atau CSV (.csv) untuk diimpor.',
            'file.mimes' => 'Format file harus berupa dokumen Excel (.xlsx) atau CSV (.csv).',
            'file.max' => 'Ukuran file maksimal adalah 10MB.',
            'default_password.min' => 'Password bawaan minimal terdiri dari :min karakter.',
        ]);

        $file = $request->file('file');
        $defaultPassword = $request->input('default_password');

        $result = $this->importService->import($file, $defaultPassword);

        if ($result['imported_count'] > 0) {
            $message = "Berhasil mengimpor {$result['imported_count']} akun baru.";
            if ($result['skipped_count'] > 0) {
                $message .= " Sebanyak {$result['skipped_count']} data dilewati (duplikat atau data tidak lengkap).";
            }

            return redirect()->route('admin.users.index')
                ->with('success', $message)
                ->with('import_result', $result);
        }

        return redirect()->back()
            ->with('error', 'Gagal mengimpor data akun. Tidak ada akun yang berhasil ditambahkan. Periksa format kolom dan data pada file.')
            ->with('import_result', $result);
    }

    /**
     * Download template import Excel (.xlsx) or CSV (.csv).
     */
    public function downloadTemplate(string $format): Response
    {
        $format = strtolower($format);

        if ($format === 'xlsx') {
            $path = public_path('templates/template_import_user.xlsx');
            $filename = 'template_import_pengguna_simbim.xlsx';
            $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        } else {
            $path = public_path('templates/template_import_user.csv');
            $filename = 'template_import_pengguna_simbim.csv';
            $contentType = 'text/csv';
        }

        if (! file_exists($path)) {
            abort(404, 'File template tidak ditemukan.');
        }

        $content = file_get_contents($path);

        return response($content, 200, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
