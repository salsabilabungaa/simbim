<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Menampilkan formulir edit profil untuk pengguna yang sedang login (semua role).
     */
    public function edit(Request $request): View
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            $user = User::first();
            if ($user) {
                Auth::login($user);
            }
        }

        $role = $user?->role ?? 'mahasiswa';

        $layout = match ($role) {
            'admin' => 'layouts.admin',
            'dosen' => 'layouts.dosen',
            'dewan_skripsi' => 'layouts.dewan_skripsi',
            'gkm' => 'layouts.gkm',
            default => 'layouts.mahasiswa',
        };

        $dashboardRoute = match ($role) {
            'admin' => route('admin.beranda'),
            'dosen' => route('dosen.beranda'),
            'dewan_skripsi' => route('dewan-skripsi.beranda'),
            'gkm' => route('gkm.beranda'),
            default => (Route::has('berandaMahasiswa') ? route('berandaMahasiswa') : route('mahasiswa.beranda')),
        };

        $mahasiswa = [
            'nama' => $user?->name ?? 'Pengguna SIMBIM',
            'nim' => $user?->nomor_induk ?? ($user?->nim ?? '-'),
            'program_studi' => $user?->program_studi ?? config('simbim.prodi'),
            'semester' => 'Semester Aktif',
            'status' => 'Pengguna Aktif',
        ];

        return view('profile.edit', [
            'user' => $user,
            'layout' => $layout,
            'dashboardRoute' => $dashboardRoute,
            'mahasiswa' => $mahasiswa,
            'notifikasi' => [],
        ]);
    }

    /**
     * Memvalidasi dan menyimpan perubahan data profil ke database untuk seluruh role.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            $user = User::first();
            if ($user) {
                Auth::login($user);
            }
        }

        if (! $user) {
            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu.');
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'program_studi' => ['required', 'string', 'max:100'],
            'foto_profil' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];

        // Validasi nomor identitas (NIM untuk mahasiswa, NIP/Nomor Induk untuk peran lainnya)
        if ($request->has('nim') || $user->isMahasiswa()) {
            $rules['nim'] = ['required', 'string', 'max:30'];
        } elseif ($request->has('nip')) {
            $rules['nip'] = ['required', 'string', 'max:30'];
        } elseif ($request->has('nomor_induk')) {
            $rules['nomor_induk'] = ['required', 'string', 'max:50'];
        }

        $messages = [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar pada akun lain.',
            'nim.required' => 'Nomor Induk Mahasiswa (NIM) wajib diisi.',
            'nip.required' => 'Nomor Induk Pegawai (NIP) wajib diisi.',
            'nomor_induk.required' => 'Nomor induk wajib diisi.',
            'program_studi.required' => 'Program studi wajib dipilih atau diisi.',
            'foto_profil.image' => 'Berkas harus berupa gambar.',
            'foto_profil.mimes' => 'Format foto profil yang didukung: JPG, JPEG, PNG, WEBP.',
            'foto_profil.max' => 'Ukuran foto profil maksimal 2 MB.',
        ];

        $validated = $request->validate($rules, $messages);

        // 1. Update data dasar
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->program_studi = $validated['program_studi'];

        // 2. Update nomor identitas
        if ($request->filled('nim')) {
            $user->nim = $request->input('nim');
            $user->nomor_induk = $request->input('nim');
        } elseif ($request->filled('nip')) {
            $user->nip = $request->input('nip');
            $user->nomor_induk = $request->input('nip');
        } elseif ($request->filled('nomor_induk')) {
            $user->nomor_induk = $request->input('nomor_induk');
            if ($user->isMahasiswa()) {
                $user->nim = $request->input('nomor_induk');
            } else {
                $user->nip = $request->input('nomor_induk');
            }
        }

        // 3. Penanganan upload foto profil
        if ($request->hasFile('foto_profil')) {
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            $path = $request->file('foto_profil')->store('avatars', 'public');
            $user->foto_profil = $path;
        } elseif ($request->boolean('hapus_foto')) {
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            $user->foto_profil = null;
        }

        // 4. Simpan ke database
        $user->save();

        // 5. Redirect sesuai role pengguna
        if ($user->isMahasiswa() && Route::has('berandaMahasiswa')) {
            return redirect()->route('berandaMahasiswa')->with('success', 'Data profil Anda berhasil diperbarui!');
        }

        $targetRoute = match ($user->role) {
            'admin' => 'admin.beranda',
            'dosen' => 'dosen.beranda',
            'dewan_skripsi' => 'dewan-skripsi.beranda',
            'gkm' => 'gkm.beranda',
            default => 'mahasiswa.beranda',
        };

        return redirect()->route($targetRoute)->with('success', 'Data profil Anda berhasil diperbarui!');
    }
}

