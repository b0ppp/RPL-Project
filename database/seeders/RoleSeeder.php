<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents; // Bisa di-uncomment jika tidak ada event model
use Illuminate\Database\Seeder;
use App\Models\Role; // Impor model Role yang sudah kita buat/sesuaikan

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data peran yang akan dimasukkan
        $roles = [
            ['role_name' => 'Admin'],
            ['role_name' => 'Resepsionis'],
            ['role_name' => 'Staf Dapur'],
            ['role_name' => 'Staf Antar'],
        ];

        // Masukkan data ke tabel roles
        // Menggunakan firstOrCreate untuk menghindari error jika seeder dijalankan berkali-kali
        // dan role_name sudah ada (karena ada unique constraint)
        foreach ($roles as $roleData) {
            Role::firstOrCreate(['role_name' => $roleData['role_name']]);
        }

        // Atau jika Anda ingin menghapus semua data lama dulu (hati-hati jika sudah ada relasi)
        // Role::truncate(); // Hapus semua data dari tabel roles
        // foreach ($roles as $roleData) {
        //     Role::create($roleData);
        // }

        $this->command->info('Tabel roles berhasil diisi dengan data awal!'); // Pesan sukses opsional
    }
}