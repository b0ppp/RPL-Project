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
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id'); // Kita ganti nama primary key menjadi user_id
            $table->string('fullname', 100)->comment('Nama lengkap pengguna');
            $table->string('username', 50)->unique()->comment('Username untuk login');
            $table->string('email')->unique()->nullable()->comment('Email pengguna, unik dan opsional');
            $table->timestamp('email_verified_at')->nullable(); // Bawaan Laravel, biarkan jika ingin fitur verifikasi email
            $table->string('password')->comment('Password yang sudah di-hash'); // Nama kolom standar Laravel untuk password

            // Definisikan kolom role_id di sini, TIPE DATA SAJA, TANPA FOREIGN KEY CONSTRAINT
            $table->unsignedBigInteger('role_id')->nullable()->comment('ID untuk peran pengguna (akan dihubungkan nanti)');
            // Kita buat nullable() sementara jika ada kasus user dibuat sebelum role_id di-set,
            // atau Anda bisa menghapus nullable() jika role_id wajib saat user dibuat dan diisi dari seeder/controller

            $table->enum('status', ['Aktif', 'Tidak Aktif'])->default('Aktif')->comment('Status akun pengguna');
            $table->rememberToken();
            $table->timestamps(); // Kolom created_at dan updated_at
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
