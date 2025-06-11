<?php

namespace App\Http\Controllers\Admin; // Pastikan namespace sudah benar

use App\Http\Controllers\Controller; // Controller dasar Laravel
use App\Models\Role;
use App\Models\User; // Impor model User
use Illuminate\Http\Request;
use Illuminate\View\View; // Untuk tipe return View
use Illuminate\Support\Facades\Hash; // Untuk hashing password baru
use Illuminate\Validation\Rule; // Untuk aturan validasi unique yang lebih kompleks
use Illuminate\Validation\Rules\Password; // Untuk aturan validasi password standar Laravel
use Illuminate\Http\RedirectResponse; // Untuk tipe return redirect
use Illuminate\Support\Facades\Auth; // Pastikan ini ada

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View // Tipe return adalah View
    {
        // Ambil semua pengguna beserta data perannya (menggunakan eager loading)
        // Urutkan berdasarkan user_id atau created_at bisa juga
        $users = User::with('role')->orderBy('user_id', 'asc')->paginate(10); // Contoh dengan paginasi 10 user per halaman

        // Kirim data pengguna ke view
        return view('admin.users.index', ['users' => $users]);
        // atau: return view('admin.users.index', compact('users'));
    }

    // Method lain (create, store, show, edit, update, destroy) akan kita isi nanti
    public function edit(User $user): View // Laravel akan otomatis inject User berdasarkan ID di route
    {
        // Ambil semua peran untuk ditampilkan di dropdown pilihan peran
        $roles = Role::all();

        // Kirim data pengguna yang akan diedit dan daftar peran ke view
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        // Validasi data yang masuk
        $validatedData = $request->validate([
            'fullname' => ['required', 'string', 'max:100'],
            'username' => [
                'required', 
                'string', 
                'max:50', 
                Rule::unique('users', 'username')->ignore($user->user_id, 'user_id') // Unik, kecuali untuk user ini sendiri
            ],
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255', 
                Rule::unique('users', 'email')->ignore($user->user_id, 'user_id') // Unik, kecuali untuk user ini sendiri
            ],
            'role_id' => ['required', 'integer', 'exists:roles,role_id'],
            'status' => ['required', 'string', Rule::in(['Aktif', 'Tidak Aktif'])],
            'password' => ['nullable', 'confirmed', Password::defaults()], // Password opsional, jika diisi harus dikonfirmasi
        ]);

        // Update atribut pengguna
        $user->fullname = $validatedData['fullname'];
        $user->username = $validatedData['username'];
        $user->email = $validatedData['email'];
        $user->role_id = $validatedData['role_id'];
        $user->status = $validatedData['status'];

        // Jika password baru diisi, update passwordnya
        if (!empty($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }

        $user->save(); // Simpan perubahan ke database

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna ' . $user->username . ' berhasil diperbarui!');
    }

    public function destroy(User $user): RedirectResponse
    {
        // 1. Pemeriksaan Keamanan: Admin tidak boleh menghapus akunnya sendiri.
        if (Auth::id() === $user->user_id) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
        }

        // 2. Pemeriksaan Tambahan (Opsional tapi direkomendasikan):
        //    Mencegah penghapusan Admin terakhir atau Admin dengan peran tertentu jika hanya ada satu.
        //    Contoh: Jika ini adalah satu-satunya user dengan peran 'Admin'.
        if ($user->role && $user->role->role_name === 'Admin') {
            // Hitung jumlah admin lain
            $otherAdminsCount = User::where('role_id', $user->role_id)
                                    ->where('user_id', '!=', $user->user_id)
                                    ->count();
            if ($otherAdminsCount === 0) {
                return redirect()->route('admin.users.index')->with('error', 'Tidak dapat menghapus Admin terakhir. Harus ada minimal satu Admin.');
            }
        }

        // Simpan username sebelum dihapus untuk pesan sukses
        $deletedUsername = $user->username;

        // 3. Hapus pengguna
        $user->delete();

        // 4. Redirect kembali ke daftar pengguna dengan pesan sukses
        return redirect()->route('admin.users.index')->with('success', 'Pengguna \'' . $deletedUsername . '\' berhasil dihapus.');
    }
}