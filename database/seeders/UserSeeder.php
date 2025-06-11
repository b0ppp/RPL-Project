<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // Impor model User
use App\Models\Role;  // Impor model Role
use Illuminate\Support\Facades\Hash; // Impor Hash untuk password

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari role 'Admin' untuk mendapatkan ID-nya
        $adminRole = Role::where('role_name', 'Admin')->first();

        // Jika role 'Admin' tidak ditemukan, tampilkan pesan dan hentikan.
        // Ini penting karena RoleSeeder harus dijalankan terlebih dahulu.
        if (!$adminRole) {
            $this->command->error('Peran "Admin" tidak ditemukan. Jalankan RoleSeeder terlebih dahulu.');
            return;
        }

        // Buat pengguna Admin default
        // Gunakan firstOrCreate untuk menghindari duplikasi jika seeder dijalankan berkali-kali
        User::firstOrCreate(
            ['username' => 'admin'], // Kriteria untuk mencari (username harus unik)
            [
                'fullname' => 'Administrator Utama',
                'email' => 'admin@hotelemail.com', // Ganti dengan email yang valid jika perlu
                'password' => Hash::make('password'), // Ganti 'password' dengan password yang aman!
                'role_id' => $adminRole->role_id,
                'status' => 'Aktif',
                'email_verified_at' => now(), // Anggap email sudah terverifikasi untuk admin default
            ]
        );

        $this->command->info('User Admin default berhasil dibuat (username: admin, password: password). Harap segera ganti password!');

        // Anda bisa menambahkan user lain di sini jika perlu, misalnya Resepsionis default
        // $receptionistRole = Role::where('role_name', 'Resepsionis')->first();
        // if ($receptionistRole) {
        //     User::firstOrCreate(
        //         ['username' => 'resepsionis01'],
        //         [
        //             'fullname' => 'Resepsionis Default',
        //             'email'    => 'resepsionis@hotelemail.com',
        //             'password' => Hash::make('password123'),
        //             'role_id'  => $receptionistRole->role_id,
        //             'status'   => 'Aktif',
        //             'email_verified_at' => now(),
        //         ]
        //     );
        //     $this->command->info('User Resepsionis default berhasil dibuat (username: resepsionis01).');
        // }
    }
}