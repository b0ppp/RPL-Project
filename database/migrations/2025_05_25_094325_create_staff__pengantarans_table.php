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
        Schema::create('staff__pengantarans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_staff_pengantaran');
            $table->string('domisili_staff_pengantaran');
            $table->string('ttl_staff_pengantaran');
            $table->string('kontak_staff_pengantaran');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff__pengantarans');
    }
};
