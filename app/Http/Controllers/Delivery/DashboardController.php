<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;


class DashboardController extends Controller
{
    public function index(): View
    {
        $relevantStatuses = ['Diproses', 'Siap Dihantar', 'Dihantarkan', 'Diterima'];

        $orders = Order::whereIn('order_status', $relevantStatuses)
                        ->whereDate('created_at', today())
                        ->with(['room.roomType', 'orderItems.menuItem', 'kitchenStaff', 'deliveryStaff'])
                        ->orderByRaw("FIELD(order_status, 'Siap Dihantar', 'Dihantarkan', 'Diproses', 'Diterima')")
                        ->latest('order_time')
                        ->get();

        $cooks = User::whereHas('role', function ($query) {
            $query->where('role_name', 'Staf Dapur');
        })->where('status', 'Aktif')->select('user_id', 'fullname')->get();

        $deliveryStaffs = User::whereHas('role', function ($query) {
            $query->where('role_name', 'Staf Antar');
        })->where('status', 'Aktif')->select('user_id', 'fullname')->get();

        return view('delivery.dashboard', compact('orders', 'cooks', 'deliveryStaffs'));
    }

    public function assignStaff(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,user_id',
            'type'    => 'required|string|in:cook,delivery',
        ]);

        if ($validated['type'] === 'cook') {
            $order->kitchen_staff_user_id = $validated['user_id'];
        } 
        elseif ($validated['type'] === 'delivery') {
            // BLOK IF YANG MEMBATASI TELAH DIHAPUS DARI SINI

            $order->delivery_staff_user_id = $validated['user_id'];
            
            // Jika ini adalah penetapan pertama kali, ubah statusnya
            if($order->order_status === 'Siap Dihantar') {
                $order->order_status = 'Dihantarkan';
                $order->delivery_assignment_time = now();
            }
        }

        $order->save();

        return response()->json($order->load(['room.roomType', 'orderItems.menuItem', 'kitchenStaff', 'deliveryStaff']));
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Diterima',
        ]);

        if ($order->order_status !== 'Dihantarkan') {
             return response()->json(['message' => 'Hanya pesanan yang sedang DIHANTARKAN yang bisa diselesaikan.'], 422);
        }

        $order->order_status = $validated['status'];
        if ($validated['status'] === 'Diterima') {
            $order->delivery_actual_time = now();
        }

        $order->save();

        // Kembalikan data order yang sudah ter-update LENGKAP dengan semua relasinya
        return response()->json($order->load(['room.roomType', 'orderItems.menuItem', 'kitchenStaff', 'deliveryStaff']));
    }
}