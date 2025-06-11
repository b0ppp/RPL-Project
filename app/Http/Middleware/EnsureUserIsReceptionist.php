<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsReceptionist
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Middleware 'auth' dalam grup rute ['auth', 'receptionist'] sudah memastikan Auth::user() ada.
        if (Auth::user()->role && Auth::user()->role->role_name === 'Resepsionis') {
            return $next($request); // Izinkan akses jika Resepsionis
        }

        // Jika bukan Resepsionis, tampilkan error 403
        abort(403, 'AKSES DITOLAK. Halaman ini hanya untuk pengguna dengan peran Resepsionis.');
    }
}