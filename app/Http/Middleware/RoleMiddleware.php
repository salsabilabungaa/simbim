<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Admin memiliki hak akses global ke semua halaman role lainnya
        if ($user->role === 'admin') {
            return $next($request);
        }

        $allowedRoles = [];
        foreach ($roles as $roleItem) {
            foreach (explode(',', $roleItem) as $subRole) {
                $trimmed = trim($subRole);
                if ($trimmed !== '') {
                    $allowedRoles[] = $trimmed;
                }
            }
        }

        if (! in_array($user->role, $allowedRoles, true)) {
            return match ($user->role) {
                'admin' => redirect()->route('admin.beranda'),
                'dosen' => redirect()->route('dosen.beranda'),
                'dewan_skripsi' => redirect()->route('dewan-skripsi.beranda'),
                'gkm' => redirect()->route('gkm.beranda'),
                default => redirect()->route('mahasiswa.beranda'),
            };
        }

        return $next($request);
    }
}
