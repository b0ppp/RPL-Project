<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment standar bernama 'id'
            $table->string('name', 100)->unique()->comment('Nama tipe kamar, cth: Standard, Deluxe');
            // $table->text('description')->nullable()->comment('Deskripsi singkat tipe kamar (opsional)'); // BARIS INI DIHAPUS
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};