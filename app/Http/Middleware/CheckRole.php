<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Periksa apakah role user yang sedang login ada di dalam daftar role yang diizinkan.
        if (! in_array($request->user()->role, $roles)) {
            // Jika role tidak sesuai, arahkan ke dashboard umum.
            // Anda bisa juga mengarahkan ke halaman lain atau menampilkan error 403 (forbidden).
            return redirect('/dashboard');
        }

        return $next($request);
    }
}