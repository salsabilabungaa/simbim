<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'in:mahasiswa,dosen'],
            'nomor_induk' => ['required', 'string', 'max:30', 'unique:users,nomor_induk'],
            'program_studi' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
            'terms' => ['accepted'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'role.required' => 'Pilih peran akun (Mahasiswa atau Dosen).',
            'role.in' => 'Peran akun tidak valid.',
            'nomor_induk.required' => 'NIM / NIP wajib diisi.',
            'nomor_induk.unique' => 'NIM / NIP ini sudah terdaftar pada sistem.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal terdiri dari 8 karakter.',
            'terms.accepted' => 'Anda harus menyetujui syarat & ketentuan layanan.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'role' => $validated['role'],
            'nomor_induk' => trim($validated['nomor_induk']),
            'program_studi' => $validated['program_studi'] ?? 'PSTI (Pendidikan Sistem dan Teknologi Informasi)',
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        $redirectRoute = route('mahasiswa.beranda');
        if ($user->role === 'dosen' && Route::has('dosen.beranda')) {
            $redirectRoute = route('dosen.beranda');
        }

        return redirect()->to($redirectRoute)->with(
            'success',
            'Pendaftaran berhasil! Selamat datang di SIMBIM, '.$user->name.'.'
        );
    }
}
