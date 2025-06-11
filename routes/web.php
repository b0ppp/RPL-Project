<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController; // Untuk halaman profil pengguna (bawaan Breeze)
use App\Http\Controllers\Admin\UserController; // Controller untuk manajemen pengguna oleh Admin
use App\Http\Controllers\Admin\MenuItemController; // Controller untuk manajemen menu (akan kita gunakan nanti)
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\Receptionist\OrderController as ReceptionistOrderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sinilah Anda bisa mendaftarkan rute web untuk aplikasi Anda. Rute-rute
| ini dimuat oleh RouteServiceProvider dan semuanya akan
| ditugaskan ke grup middleware "web". Buat sesuatu yang hebat!
|
*/

// Halaman selamat datang (publik)
Route::get('/', function () {
    return view('welcome');
});

// Dashboard umum untuk pengguna yang sudah login (jika tidak ada pengalihan peran spesifik)
// Atau sebagai fallback jika peran tidak terdefinisi untuk pengalihan khusus.
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard'); // 'verified' jika Anda menggunakan verifikasi email

// ==================================================
// --- GRUP RUTE KHUSUS UNTUK ADMIN ---
// ==================================================
Route::middleware(['auth', 'admin']) // Hanya bisa diakses oleh user yang login DAN adalah Admin
    ->prefix('admin')               // Semua URL akan diawali dengan /admin (cth: /admin/dashboard)
    ->name('admin.')                // Semua nama rute akan diawali dengan admin. (cth: admin.dashboard)
    ->group(function () {

    // Dashboard Admin
    Route::get('/dashboard', function () {
        return view('admin.dashboard'); // Menggunakan view resources/views/admin/dashboard.blade.php
    })->name('dashboard'); // Nama rute lengkap: admin.dashboard

    // Manajemen Pengguna oleh Admin
    // Kita menggunakan Route::resource tetapi mengecualikan 'create' dan 'store'
    // karena fungsi tersebut sudah ditangani oleh halaman /register (RegisteredUserController)
    // Kita juga mengecualikan 'show' untuk saat ini karena belum kita implementasikan.
    Route::resource('users', UserController::class)
        ->except(['create', 'store', 'show']) // Metode yang tidak akan dibuatkan rutenya untuk UserController
        ->names([ // Memberi nama eksplisit untuk konsistensi
            'index'   => 'users.index',   // Menjadi admin.users.index (GET /admin/users)
            'edit'    => 'users.edit',    // Menjadi admin.users.edit (GET /admin/users/{user}/edit)
            'update'  => 'users.update',  // Menjadi admin.users.update (PUT/PATCH /admin/users/{user})
            'destroy' => 'users.destroy', // Menjadi admin.users.destroy (DELETE /admin/users/{user})
        ]);

    // Manajemen Menu oleh Admin (akan kita implementasikan nanti)
    // Ini juga menggunakan resource controller, dan semua rutenya akan memiliki prefix /admin dan nama admin.
    Route::resource('menu-items', MenuItemController::class)->names('menuitems');

    // Rute untuk Manajemen Kamar oleh Admin
    Route::resource('rooms', RoomController::class)->names('rooms');
    // Ini akan membuat rute seperti: admin.rooms.index, admin.rooms.create, dll.

    // Rute untuk Manajemen Tipe Kamar
    Route::resource('room-types', RoomTypeController::class)->names('roomtypes');
});
// --- AKHIR GRUP RUTE ADMIN ---

// --- GRUP RUTE UNTUK RESEPSIONIS ---
Route::middleware(['auth', 'receptionist'])
    ->prefix('receptionist')
    ->name('receptionist.')
    ->group(function () {

        // Rute-rute yang sudah ada
        Route::get('/order/create', [ReceptionistOrderController::class, 'create'])->name('order.create');
        Route::post('/orders', [ReceptionistOrderController::class, 'store'])->name('orders.store');

        // --- TAMBAHKAN DUA RUTE DI BAWAH INI ---

        // 1. Rute untuk menampilkan halaman riwayat (GET)
        Route::get('/orders/history', [ReceptionistOrderController::class, 'history'])->name('orders.history');

        // 2. Rute untuk menghapus pesanan (DELETE)
        Route::delete('/orders/{order}', [ReceptionistOrderController::class, 'destroy'])->name('orders.destroy');
});

// Staf Dapur
Route::middleware(['auth', 'kitchen'])
    ->prefix('kitchen')
    ->name('kitchen.')
    ->group(function () {
        // Rute untuk menampilkan dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Kitchen\DashboardController::class, 'index'])->name('dashboard');

        // Rute untuk menandai pesanan sebagai siap (aksi dari checklist)
        Route::patch('/orders/{order}/mark-as-ready', [\App\Http\Controllers\Kitchen\DashboardController::class, 'markAsReady'])->name('orders.mark-as-ready');

        // Di dalam grup rute kitchen
        Route::patch('/orders/{order}/uncheck', [\App\Http\Controllers\Kitchen\DashboardController::class, 'uncheck'])->name('orders.uncheck');
});

// Staf Antar
Route::middleware(['auth', 'delivery'])
    ->prefix('delivery')
    ->name('delivery.')
    ->group(function () {
        // Rute untuk menampilkan dashboard utama
        Route::get('/dashboard', [\App\Http\Controllers\Delivery\DashboardController::class, 'index'])->name('dashboard');

        // Rute untuk aksi memilih staf (Cook atau Delivery) dari dropdown
        Route::patch('/orders/{order}/assign-staff', [\App\Http\Controllers\Delivery\DashboardController::class, 'assignStaff'])->name('orders.assign-staff');

        // Rute untuk aksi mengubah status pesanan
        Route::patch('/orders/{order}/update-status', [\App\Http\Controllers\Delivery\DashboardController::class, 'updateStatus'])->name('orders.update-status');
});




// Rute untuk Manajemen Profil Pengguna (bawaan Breeze, untuk semua pengguna yang sudah login)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Memuat rute-rute autentikasi (login, register khusus admin, logout, dll.)
require __DIR__.'/auth.php';