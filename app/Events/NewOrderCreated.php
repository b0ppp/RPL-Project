<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel; // Pastikan ini PrivateChannel
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class NewOrderCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order->load('orderItems.menuItem');
    }

    public function broadcastOn(): array
    {
        // Channel harus sama dengan yang di routes/channels.php
        return [
            new PrivateChannel('kitchen-orders'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'new-order'; // Nama event untuk didengarkan oleh JavaScript
    }
}