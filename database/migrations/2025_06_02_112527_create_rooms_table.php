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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id('room_id');
            $table->string('room_number', 20)->unique()->comment('Nomor kamar, harus unik');

            // $table->string('room_type', 50)->comment('Tipe kamar, cth: Standard, Deluxe, Suite'); // HAPUS BARIS INI

            // TAMBAHKAN BARIS INI untuk foreign key ke tabel room_types
            $table->foreignId('room_type_id')
                ->comment('Foreign key ke tabel room_types')
                ->constrained('room_types', 'id') // Merujuk ke kolom 'id' di tabel 'room_types'
                ->onDelete('restrict'); // Mencegah penghapusan tipe kamar jika masih digunakan oleh kamar

            $table->enum('status', ['Terisi', 'Kosong', 'Dalam Perbaikan'])->default('Kosong')->comment('Status kamar saat ini');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};