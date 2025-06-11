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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id')->comment('No.Check, ID unik pesanan');

            // HAPUS KOLOM LAMA INI:
            // $table->string('room_type', 50)->nullable()->comment('Tipe kamar pemesan');
            // $table->string('room_number', 20)->comment('Nomor kamar pemesan');

            // TAMBAHKAN KOLOM BARU INI:
            $table->foreignId('room_id')
                  ->comment('Foreign key ke tabel rooms')
                  ->constrained('rooms', 'room_id') // Merujuk ke room_id di tabel rooms
                  ->onDelete('restrict'); // Jika kamar dihapus, pesanan yang terkait tidak bisa (mencegah error)
                                          // atau onDelete('set null') jika room_id dibuat nullable dan Anda ingin order tetap ada
                                          // Untuk pesanan hotel, 'restrict' atau tidak memperbolehkan penghapusan kamar yang ada order adalah pilihan aman.

            $table->dateTime('order_time')->comment('Waktu pesanan dibuat oleh resepsionis');
            $table->foreignId('receptionist_user_id')
                  ->comment('ID Resepsionis yang membuat pesanan')
                  ->constrained('users', 'user_id');
            $table->foreignId('kitchen_staff_user_id')
                  ->nullable()
                  ->comment('ID Staf Dapur (individu) yang memasak (By Cook)')
                  ->constrained('users', 'user_id')
                  ->onDelete('set null');
            $table->foreignId('delivery_staff_user_id')
                  ->nullable()
                  ->comment('ID Staf Antar (individu) yang mengantar (By Delivery)')
                  ->constrained('users', 'user_id')
                  ->onDelete('set null');
            $table->enum('order_status', ['Diproses', 'Siap Dihantar', 'Dihantarkan', 'Diterima', 'Dibatalkan'])
                  ->default('Diproses');
            $table->dateTime('kitchen_timer_start_time')->nullable()->comment('Waktu notifikasi pesanan muncul di dapur & timer mulai');
            $table->dateTime('kitchen_marked_ready_time')->nullable()->comment('Waktu Staf Dapur checklist pesanan siap');
            $table->dateTime('kitchen_uncheck_allowed_until')->nullable()->comment('Batas waktu Staf Dapur boleh uncheck');
            $table->dateTime('delivery_assignment_time')->nullable()->comment('Waktu Staf Antar dipilih dan status diubah ke Dihantarkan');
            $table->dateTime('delivery_actual_time')->nullable()->comment('Waktu makanan diterima tamu (Waktu Delivery)');
            $table->dateTime('delivery_correction_allowed_until')->nullable()->comment('Batas waktu Staf Antar boleh koreksi status Diterima');
            $table->boolean('sop_violation_flag')->default(false)->comment('True jika ada pelanggaran SOP');
            $table->string('sop_violation_notes')->nullable()->comment('Catatan singkat mengenai pelanggaran SOP');
            $table->boolean('is_cancelled_by_receptionist')->default(false)->comment('True jika pesanan dibatalkan');
            $table->dateTime('cancellation_time')->nullable()->comment('Waktu pembatalan oleh resepsionis');
            $table->dateTime('receptionist_cancellation_allowed_until')->nullable()->comment('Batas waktu Resepsionis boleh membatalkan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
