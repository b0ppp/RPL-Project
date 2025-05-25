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
        Schema::create('staff__dapurs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_staff_dapur');
            $table->string('domisili_staff_dapur');
            $table->string('ttl_staff_dapur');
            $table->string('kontak_staff_dapur');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff__dapurs');
    }
};
