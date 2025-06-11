<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Pastikan kolom role_id sudah ada (dibuat di migrasi create_users_table)
            // Sekarang kita tambahkan constraint foreign key-nya.
            $table->foreign('role_id') // Nama kolom di tabel 'users'
                  ->references('role_id') // Merujuk ke kolom 'role_id'
                  ->on('roles') // Di tabel 'roles'
                  ->onDelete('restrict') // Aksi saat role dihapus: restrict (tolak) jika masih ada user yang menggunakan role tsb.
                                        // Anda bisa ganti ke 'set null' jika kolom role_id di users dibuat nullable dan Anda ingin mengizinkannya.
                                        // Namun, karena role_id di users kita buat nullable() sementara, 'restrict' atau 'set null' bisa jadi pilihan.
                                        // Jika role_id wajib, maka 'restrict' atau 'cascade' (jika menghapus role juga menghapus user, tidak disarankan di sini).
                                        // Untuk sekarang, 'restrict' adalah pilihan aman jika role_id dianggap wajib setelah di-set.
                  ->comment('Menambahkan foreign key constraint untuk role_id ke tabel roles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus foreign key. Nama constraint default biasanya: namaTabel_namaKolom_foreign
            $table->dropForeign(['role_id']);
            // Jika Anda ingin kolom role_id juga ikut terhapus saat rollback (tidak disarankan jika kolom dibuat di migrasi lain):
            // $table->dropColumn('role_id');
        });
    }
};
