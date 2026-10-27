<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman formulir login.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('berandaMahasiswa');
        }

        return view('login');
    }

    /**
     * Memproses autentikasi pengguna menggunakan Email atau NIM dan Password.
     */
    public function login(Request $request): RedirectResponse
    {
        // 1. Ambil input login (bisa dari input 'email', 'login', atau 'nim')
        $loginInput = trim((string) ($request->input('email') ?? $request->input('login') ?? $request->input('nim')));
        $password = (string) $request->password;

        // Pastikan input 'email' & 'login' terisi untuk validasi dan penanganan error Blade
        $request->merge([
            'email' => $loginInput,
            'login' => $loginInput,
        ]);

        $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email atau NIM wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $throttleKey = Str::transliterate(Str::lower($loginInput).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()
                ->withInput($request->only('email', 'login', 'remember'))
                ->withErrors([
                    'email' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
                ]);
        }

        // 2. Cek apakah input berisi Email atau NIM
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'nim';

        // Toleransi jika password di database belum ter-hash Bcrypt (misal plain-text hasil input manual)
        $user = User::where($fieldType, $loginInput)->first();
        if (! $user && $fieldType === 'nim') {
            $user = User::where('email', $loginInput)->first();
        }

        if ($user) {
            $rawPassword = $user->getRawOriginal('password');
            if (! str_starts_with($rawPassword, '$2y$') && ! str_starts_with($rawPassword, '$2a$') && ! str_starts_with($rawPassword, '$argon2')) {
                if ($rawPassword === $password) {
                    $user->password = Hash::make($password);
                    $user->save();
                }
            }
        }

        // Jalankan Auth::attempt([ $fieldType => $loginInput, 'password' => $request->password ])
        $authenticated = Auth::attempt([
            $fieldType => $loginInput,
            'password' => $request->password,
        ], $request->boolean('remember'));

        // Fallback: jika login via NIM gagal dan tidak berformat email standar, coba juga via kolom email
        if (! $authenticated && $fieldType === 'nim') {
            $authenticated = Auth::attempt([
                'email' => $loginInput,
                'password' => $request->password,
            ], $request->boolean('remember'));
        }

        // 3. Session & Redirect: regenerate session saat Auth::attempt bernilai true
        if ($authenticated) {
            RateLimiter::clear($throttleKey);
            request()->session()->regenerate();

            return redirect()->route('berandaMahasiswa')
                ->with('success', 'Selamat datang kembali, '.Auth::user()->name.'!');
        }

        RateLimiter::hit($throttleKey);

        // Jika autentikasi gagal
        return back()
            ->withInput($request->only('email', 'login', 'remember'))
            ->withErrors([
                'email' => 'Email atau password salah!',
                'login' => 'Email atau password salah!',
            ]);
    }

    /**
     * Mengeluarkan pengguna dari sesi autentikasi (Logout).
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sesi.');
    }
}
