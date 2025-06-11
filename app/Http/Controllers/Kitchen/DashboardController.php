<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama untuk staf dapur.
     */
    public function index(): View
    {
        // PERBAIKAN 2: Tambahkan status 'Diterima' agar pesanan tidak hilang
        $relevantStatuses = ['Diproses', 'Siap Dihantar', 'Dihantarkan', 'Diterima'];

        $orders = Order::whereIn('order_status', $relevantStatuses)
                        ->whereDate('created_at', today())
                        ->with('orderItems.menuItem')
                        // Urutkan status secara logis
                        ->orderByRaw("FIELD(order_status, 'Diproses', 'Siap Dihantar', 'Dihantarkan', 'Diterima')")
                        ->latest('order_time')
                        ->get();

        return view('kitchen.dashboard', compact('orders'));
    }

    /**
     * Menandai sebuah pesanan sebagai 'Siap Dihantar'.
     */
    public function markAsReady(Request $request, Order $order): JsonResponse
    {
        if ($order->order_status !== 'Diproses') {
            return response()->json(['success' => false, 'message' => 'Pesanan sudah tidak bisa diubah.'], 409);
        }

        $order->order_status = 'Siap Dihantar';
        $order->kitchen_marked_ready_time = now();
        $order->kitchen_uncheck_allowed_until = now()->addSeconds(30);
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Pesanan No. ' . $order->order_id . ' ditandai Siap Dihantar.'
        ]);
    }
    
    /**
     * Membatalkan status "Siap Dihantar" kembali menjadi "Diproses".
     */
    public function uncheck(Request $request, Order $order): JsonResponse
    {
        if (!$order->kitchen_uncheck_allowed_until || now()->gt($order->kitchen_uncheck_allowed_until)) {
            return response()->json(['success' => false, 'message' => 'Waktu untuk uncheck sudah lewat.'], 403);
        }

        $order->order_status = 'Diproses'; // Kembalikan status
        $order->kitchen_marked_ready_time = null;
        $order->kitchen_uncheck_allowed_until = null;
        
        // TAMBAHAN: Kosongkan juga penugasan staf antar
        $order->delivery_staff_user_id = null;
        $order->delivery_assignment_time = null;
        
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Checklist untuk Pesanan No. ' . $order->order_id . ' dibatalkan.'
        ]);
    }
    }