<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role; // Pastikan ini di-import
// use App\Providers\RouteServiceProvider; // Komentari jika tidak digunakan
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Meskipun tidak login sbg user baru, ini bisa berguna untuk cek Admin
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $roles = Role::all(); // Ambil semua peran untuk ditampilkan di dropdown
        // Jika Anda ingin Admin tidak bisa membuat Admin lain dari sini:
        // $roles = Role::where('role_name', '!=', 'Admin')->get();
        return view('auth.register', ['roles' => $roles]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'fullname' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'integer', 'exists:roles,role_id'], // Validasi role_id
        ]);

        $user = User::create([
            'fullname' => $request->fullname,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Kolom di DB adalah 'password'
            'role_id' => $request->role_id,
            'status' => 'Aktif', // Set status default
        ]);

        event(new Registered($user)); // Kirim event (misal untuk notifikasi)

        // Admin tidak login sebagai user baru yang dibuat.
        // Auth::login($user); // BARIS INI DIHAPUS/DIKOMENTARI

        // Arahkan Admin kembali ke dashboardnya atau halaman list user dengan pesan sukses.
        // Pastikan Anda sudah punya rute dengan nama 'admin.dashboard'.
        return redirect()->route('admin.dashboard')->with('success', 'Pengguna baru (' . $user->username . ') berhasil dibuat!');
    }
}