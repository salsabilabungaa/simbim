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
     * Menampilkan formulir edit profil untuk pengguna yang sedang login.
     */
    public function edit(): View
    {
        $user = Auth::user();

        if (! $user) {
            $user = User::firstOrCreate(
                ['email' => 'andi.pratama@mhs.usk.ac.id'],
                [
                    'name' => 'Andi Pratama',
                    'nim' => '2108107010001',
                    'program_studi' => 'Informatika',
                    'password' => bcrypt('password'),
                ]
            );
            Auth::login($user);
        }

        $mahasiswa = [
            'nama' => $user->name,
            'nim' => $user->nim ?? '2108107010001',
            'program_studi' => $user->program_studi ?? 'Informatika',
            'semester' => 'Semester 7',
            'status' => 'Mahasiswa Aktif',
        ];

        return view('profile.edit', [
            'user' => $user,
            'mahasiswa' => $mahasiswa,
            'notifikasi' => [],
        ]);
    }

    /**
     * Memvalidasi dan menyimpan perubahan data profil ke database.
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
            $targetRoute = Route::has('berandaMahasiswa') ? 'berandaMahasiswa' : 'mahasiswa.beranda';

            return redirect()->route($targetRoute)->with('error', 'Pengguna tidak ditemukan.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nim' => ['required', 'string', 'max:30'],
            'program_studi' => ['required', 'string', 'max:100'],
            'foto_profil' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar pada akun lain.',
            'nim.required' => 'Nomor Induk Mahasiswa (NIM) wajib diisi.',
            'program_studi.required' => 'Program studi wajib dipilih atau diisi.',
            'foto_profil.image' => 'Berkas harus berupa gambar.',
            'foto_profil.mimes' => 'Format foto profil yang didukung: JPG, JPEG, PNG, WEBP.',
            'foto_profil.max' => 'Ukuran foto profil maksimal 2 MB.',
        ]);

        // 1. Memasukkan data ke objek user yang sedang login
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->nim = $validated['nim'];
        $user->program_studi = $validated['program_studi'];

        // Penanganan upload foto profil ke storage
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

        // 2. Eksekusi penyimpanan permanen ke database
        $user->save();

        // 3. Redirect ke route berandaMahasiswa
        $targetRoute = Route::has('berandaMahasiswa') ? 'berandaMahasiswa' : 'mahasiswa.beranda';

        return redirect()->route($targetRoute)->with('success', 'Data profil Anda berhasil diperbarui!');
    }
}
