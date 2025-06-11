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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id('menu_item_id');
            $table->string('item_name', 100)->unique()->comment('Nama item menu, harus unik');
            $table->enum('availability_status', ['Tersedia', 'Tidak Tersedia'])->default('Tersedia')->comment('Status ketersediaan menu');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users', 'user_id')->onDelete('set null')->comment('Admin yang membuat/mengupdate menu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
