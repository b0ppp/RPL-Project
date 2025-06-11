<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\MenuItem; // Untuk mengambil data menu
use App\Models\Room;    // Untuk mengambil data kamar
use App\Models\Order;   // Untuk membuat record order baru
use App\Models\OrderItem; // Untuk membuat record item-item dalam order
use App\Events\NewOrderCreated;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;     // Untuk tipe return dari method store()
use Illuminate\Support\Facades\Auth;  // Untuk mendapatkan ID resepsionis yang login
use Illuminate\Support\Facades\DB;    // Untuk database transaction
use Carbon\Carbon;                    // Untuk manipulasi waktu (order_time, dll.)
use Illuminate\Validation\Rule;       // Untuk aturan validasi yang lebih kompleks (exists with where)

class OrderController extends Controller
{
     public function create(): View
    {
        // 1. Ambil semua item menu yang status ketersediaannya 'Tersedia'
        //    dan urutkan berdasarkan nama item.
        $menuItems = MenuItem::where('availability_status', 'Tersedia')
                                    ->orderBy('item_name', 'asc')
                                    ->get();
        
        // 2. Ambil semua kamar yang statusnya 'Terisi'
        //    Sertakan juga informasi tipe kamarnya melalui relasi 'roomType' (eager loading).
        //    Urutkan berdasarkan nomor kamar.
        $occupiedRooms = Room::with('roomType') 
                             ->where('status', 'Terisi')
                             ->orderBy('room_number', 'asc')
                             ->get(); 

        // Debugging (opsional, hapus atau komentari setelah selesai memeriksa):
        // dd($menuItems, $occupiedRooms); 

        // 3. Kirim kedua data tersebut ke view 'receptionist.orders.create'
        return view('receptionist.orders.create', [
            'menuItems' => $menuItems,
            'occupiedRooms' => $occupiedRooms, // Pastikan variabel ini juga dikirim
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        // 1. Validasi data yang dikirim dari frontend (Alpine.js)
        $validatedData = $request->validate([
            'room_id' => [ // Validasi untuk room_id
                'required', 
                'integer', 
                // Pastikan room_id yang dikirim ada di tabel 'rooms' DAN statusnya 'Terisi'
                Rule::exists('rooms', 'room_id')->where(function ($query) {
                    return $query->where('status', 'Terisi');
                })
            ],
            'cart_items' => 'required|array|min:1', // Keranjang tidak boleh kosong
            'cart_items.*.id' => 'required|integer|exists:menu_items,menu_item_id', // Setiap item ID harus ada di tabel menu_items
            'cart_items.*.quantity' => 'required|integer|min:1', // Kuantitas minimal 1
            'cart_items.*.note' => 'nullable|string|max:255', // Catatan opsional, maksimal 255 karakter
        ], [
            // Pesan error kustom untuk validasi room_id jika diperlukan
            'room_id.exists' => 'Kamar yang dipilih tidak valid atau tidak sedang berstatus "Terisi".' 
        ]);

        // 2. Mulai Database Transaction
        DB::beginTransaction();

        try {
            $now = Carbon::now(); // Waktu saat ini

            // 3. Buat record baru di tabel 'orders'
            $order = Order::create([
                'room_id' => $validatedData['room_id'], // ID kamar yang dipilih
                'order_time' => $now,                    // Waktu pesanan dibuat
                'receptionist_user_id' => Auth::id(),    // ID Resepsionis yang sedang login
                'order_status' => 'Diproses',            // Status awal pesanan
                'kitchen_timer_start_time' => $now,      // Asumsi timer dapur mulai saat pesanan dibuat
                'receptionist_cancellation_allowed_until' => $now->copy()->addSeconds(30), // Batas waktu pembatalan
            ]);

            // 4. Loop melalui item di keranjang dan simpan ke tabel 'order_items'
            foreach ($validatedData['cart_items'] as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->order_id,
                    'menu_item_id' => $cartItem['id'],
                    'quantity' => $cartItem['quantity'],
                    'item_notes' => $cartItem['note'] ?? null,
                ]);
            }

            DB::commit(); // Jika semua proses di atas berhasil, simpan permanen perubahan ke database

            // Di sini Anda bisa menambahkan event untuk notifikasi ke dapur nanti
            NewOrderCreated::dispatch($order);

            // Kirim respons JSON sukses ke frontend
            return response()->json([
                'success' => true, 
                'message' => 'Pesanan berhasil dikirim ke dapur! No. Pesanan: ' . $order->order_id,
                'order_id' => $order->order_id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            // Kita tidak hanya melaporkan, tapi juga mengirim pesan errornya ke browser untuk debugging
            return response()->json([
                'success' => false,
                // Tampilkan pesan error yang sebenarnya
                'message' => 'Terjadi error di server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan halaman riwayat pesanan untuk resepsionis yang sedang login.
     */
    public function history(): View
    {
        $orders = Order::where('receptionist_user_id', Auth::id())
                        ->whereDate('created_at', today()) // <-- TAMBAHKAN BARIS INI
                        ->with(['room', 'orderItems.menuItem'])
                        ->latest('order_time')
                        ->paginate(10);

        return view('receptionist.orders.history', compact('orders'));
    }

    /**
     * Menghapus pesanan jika memenuhi syarat.
     */
    public function destroy(Order $order): RedirectResponse
    {
        // Keamanan: Pastikan resepsionis hanya bisa menghapus pesanannya sendiri
        if ($order->receptionist_user_id !== Auth::id()) {
            abort(403, 'AKSES DITOLAK.');
        }

        // Logika 30 detik: Cek apakah waktu pembatalan sudah lewat
        // Kita menggunakan kolom receptionist_cancellation_allowed_until yang sudah kita siapkan
        if (now()->gt($order->receptionist_cancellation_allowed_until)) {
            return redirect()->route('receptionist.orders.history')
                             ->with('error', 'Gagal membatalkan. Waktu untuk membatalkan pesanan No. ' . $order->order_id . ' sudah lewat.');
        }
        
        // Simpan nomor pesanan untuk pesan sukses
        $orderId = $order->order_id;

        // Hapus pesanan (karena ada onDelete('cascade'), order_items juga akan terhapus)
        $order->delete();

        return redirect()->route('receptionist.orders.history')
                         ->with('success', 'Pesanan No. ' . $orderId . ' berhasil dibatalkan dan dihapus.');
    }
}


