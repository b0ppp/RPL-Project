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
        Schema::create('roles', function (Blueprint $table) {
            $table->id('role_id'); // Primary key auto-increment bernama role_id
            $table->string('role_name', 50)->unique()->comment('Nama peran, cth: Resepsionis, Staf Dapur, Staf Antar, Admin');
            // Untuk tabel roles, kita tidak memerlukan timestamps (created_at, updated_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
