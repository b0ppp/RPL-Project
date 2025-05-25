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
        Schema::create('pemesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resepsionis_id')->constrained()->onDelete('cascade');
            $table->foreignId('staff__pengantaran_id')->constrained()->onDelete('cascade');
            $table->foreignId('staff__dapur_id')->constrained()->onDelete('cascade');
            $table->foreignId('kamar_id')->constrained()->onDelete('cascade');
            $table->string('nama_pesanan');
            $table->timestamp('date/time_diantar');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemesanans');
    }
};
