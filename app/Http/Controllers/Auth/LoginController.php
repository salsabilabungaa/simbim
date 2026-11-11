<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Email atau NIM / NIP wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $loginInput = trim($credentials['login']);
        $password = $credentials['password'];
        $remember = $request->boolean('remember');

        // Mendukung login fleksibel dengan:
        // 1. Email lengkap (admin@simbim.ac.id, dll.)
        // 2. NIM / NIP / Nomor Induk
        // 3. Username tanpa domain (admin, dewan, gkm, dll.)
        // 4. Role alias ("dewan skripsi", "dewan_skripsi", "admin", "gkm")
        $normalizedLower = strtolower($loginInput);

        $user = User::where('email', $loginInput)
            ->orWhere('nomor_induk', $loginInput)
            ->orWhere('email', $loginInput.'@simbim.ac.id')
            ->when(in_array($normalizedLower, ['admin', 'amin', 'administrator', 'gkm', 'dewan', 'dewan skripsi', 'dewan_skripsi', 'dosen', 'mahasiswa'], true), function ($query) use ($normalizedLower) {
                $role = match ($normalizedLower) {
                    'admin', 'amin', 'administrator' => 'admin',
                    'gkm' => 'gkm',
                    'dewan', 'dewan skripsi', 'dewan_skripsi' => 'dewan_skripsi',
                    'dosen' => 'dosen',
                    'mahasiswa' => 'mahasiswa',
                };
                $query->orWhere('role', $role);
            })
            ->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['Kombinasi akun dan kata sandi tidak cocok dengan data kami.'],
            ]);
        }

        Auth::login($user, $remember);

        $request->session()->regenerate();

        $user = Auth::user();

        // Ambil dan bersihkan session intended URL jika ada
        $intendedUrl = $request->session()->pull('url.intended');

        if ($user && $user->role === 'admin') {
            if ($intendedUrl && str_contains($intendedUrl, '/admin')) {
                return redirect()->to($intendedUrl)->with(
                    'success',
                    'Selamat datang kembali, '.$user->name.'!'
                );
            }

            return redirect()->route('admin.beranda')->with(
                'success',
                'Selamat datang di Panel Administrator SIMBIM, '.$user->name.'!'
            );
        }

        if ($user && $user->role === 'dosen') {
            if ($intendedUrl && str_contains($intendedUrl, '/dosen')) {
                return redirect()->to($intendedUrl)->with(
                    'success',
                    'Selamat datang kembali, '.$user->name.'!'
                );
            }

            return redirect()->route('dosen.beranda')->with(
                'success',
                'Selamat datang kembali, '.$user->name.'!'
            );
        }

        if ($user && $user->role === 'dewan_skripsi') {
            if ($intendedUrl && str_contains($intendedUrl, '/dewan-skripsi')) {
                return redirect()->to($intendedUrl)->with(
                    'success',
                    'Selamat datang kembali, '.$user->name.'!'
                );
            }

            return redirect()->route('dewan-skripsi.beranda')->with(
                'success',
                'Selamat datang kembali, '.$user->name.'!'
            );
        }

        if ($user && $user->role === 'gkm') {
            if ($intendedUrl && str_contains($intendedUrl, '/gkm')) {
                return redirect()->to($intendedUrl)->with(
                    'success',
                    'Selamat datang kembali, '.$user->name.'!'
                );
            }

            return redirect()->route('gkm.beranda')->with(
                'success',
                'Selamat datang kembali, '.$user->name.'!'
            );
        }

        if ($intendedUrl && str_contains($intendedUrl, '/mahasiswa')) {
            return redirect()->to($intendedUrl)->with(
                'success',
                'Selamat datang kembali, '.($user?->name ?? 'Mahasiswa').'!'
            );
        }

        return redirect()->route('mahasiswa.beranda')->with(
            'success',
            'Selamat datang kembali, '.($user?->name ?? 'Mahasiswa').'!'
        );
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with(
            'status',
            'Anda telah berhasil keluar dari akun.'
        );
    }
}
