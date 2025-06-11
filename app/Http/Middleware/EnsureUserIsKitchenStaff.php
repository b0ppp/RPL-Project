<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsKitchenStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role && Auth::user()->role->role_name === 'Staf Dapur') {
            return $next($request);
        }

        abort(403, 'AKSES DITOLAK. Halaman ini hanya untuk Staf Dapur.');
    }
}