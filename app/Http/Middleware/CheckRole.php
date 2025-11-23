<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  (Daftar role yang dibolehkan, dipisah koma)
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Jika user belum punya role terkait, tolak akses
        if (!$user->role) {
            abort(403, 'AKSES DITOLAK: Role user belum terdefinisi.');
        }

        // Gunakan helper di model User agar logika role terpusat
        if ($user->hasRole($roles)) {
            return $next($request);
        }

        abort(403, 'AKSES DITOLAK: Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}   