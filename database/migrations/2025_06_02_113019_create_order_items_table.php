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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id('order_item_id');

            // Foreign key ke tabel orders
            $table->foreignId('order_id')
                  ->comment('Foreign key ke tabel orders')
                  ->constrained('orders', 'order_id') // Merujuk ke order_id di tabel orders
                  ->onDelete('cascade'); // Jika order dihapus, semua itemnya juga ikut terhapus

            // Foreign key ke tabel menu_items
            $table->foreignId('menu_item_id')
                  ->comment('Foreign key ke tabel menu_items')
                  ->constrained('menu_items', 'menu_item_id'); // Merujuk ke menu_item_id di tabel menu_items
                  // onDelete('restrict') adalah default, yang berarti menu item tidak bisa dihapus jika masih ada di order_items.
                  // Anda bisa mempertimbangkan onDelete('set null') jika menu_item_id dibuat nullable dan Anda ingin menyimpan record order_item meskipun menu aslinya dihapus.
                  // Atau onDelete('restrict') untuk mencegah penghapusan menu jika masih terpakai.

            $table->integer('quantity');
            $table->string('item_notes')->nullable()->comment('Catatan spesifik per item, cth: Pedas');
            // Kita tidak menggunakan timestamps di sini karena item pesanan biasanya tidak diubah setelah pesanan dibuat.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
