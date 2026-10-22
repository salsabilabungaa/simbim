<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'nomor_induk';

        $attemptCredentials = [
            $fieldType => $loginInput,
            'password' => $credentials['password'],
        ];

        $remember = $request->boolean('remember');

        if (! Auth::attempt($attemptCredentials, $remember)) {
            throw ValidationException::withMessages([
                'login' => ['Kombinasi akun dan kata sandi tidak cocok dengan data kami.'],
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Ambil dan bersihkan session intended URL jika ada
        $intendedUrl = $request->session()->pull('url.intended');

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
