<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
// TAMBAHKAN IMPORT YANG MUNGKIN HILANG:
use Illuminate\Http\RedirectResponse; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate(); // Melakukan percobaan login

        $request->session()->regenerate(); // Membuat ulang ID sesi

        $user = Auth::user(); // Mendapatkan pengguna yang baru saja login

        // Logika pengalihan berdasarkan peran pengguna
        if ($user && $user->role) { // Pastikan pengguna memiliki relasi 'role'
            switch ($user->role->role_name) {
                case 'Admin':
                    return redirect()->route('admin.dashboard');
                case 'Resepsionis':
                    // Arahkan Resepsionis ke halaman pembuatan pesanan
                    return redirect()->route('receptionist.order.create'); 
                // Anda bisa menambahkan case lain di sini untuk peran berbeda nanti:
                case 'Staf Dapur':
                    return redirect()->route('kitchen.dashboard'); // (jika rute sudah ada)
                case 'Staf Antar':
                    return redirect()->route('delivery.dashboard'); // (jika rute sudah ada)
                default:
                    // Jika peran tidak dikenali atau tidak ada pengalihan khusus,
                    // arahkan ke dashboard umum.
                    return redirect()->intended(route('dashboard'));
            }
        }

        // Fallback jika pengguna tidak memiliki peran (seharusnya tidak terjadi jika role_id wajib)
        // atau jika kondisi di atas tidak terpenuhi.
        return redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}