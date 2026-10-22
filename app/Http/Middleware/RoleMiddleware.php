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
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->role !== $role) {
            if ($user->role === 'dosen') {
                return redirect()->route('dosen.beranda');
            }

            if ($user->role === 'mahasiswa') {
                return redirect()->route('mahasiswa.beranda');
            }
        }

        return $next($request);
    }
}
