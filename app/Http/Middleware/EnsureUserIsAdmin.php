<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Penting untuk di-import
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role && Auth::user()->role->role_name === 'Admin') {
            return $next($request);
        }
        abort(403, 'AKSES DITOLAK. HANYA ADMIN YANG DAPAT MENGAKSES HALAMAN INI.');
    }
}