<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_id';

    protected $fillable = [
        'room_id',
        'order_time',
        'receptionist_user_id',
        'kitchen_staff_user_id',
        'delivery_staff_user_id',
        'order_status',
        'kitchen_timer_start_time',
        'kitchen_marked_ready_time',
        'kitchen_uncheck_allowed_until',
        'delivery_assignment_time',
        'delivery_actual_time',
        'delivery_correction_allowed_until',
        'sop_violation_flag',
        'sop_violation_notes',
        'is_cancelled_by_receptionist',
        'cancellation_time',
        'receptionist_cancellation_allowed_until',
    ];

    protected $casts = [
        'order_time' => 'datetime',
        'kitchen_timer_start_time' => 'datetime',
        'kitchen_marked_ready_time' => 'datetime',
        'kitchen_uncheck_allowed_until' => 'datetime',
        'delivery_assignment_time' => 'datetime',
        'delivery_actual_time' => 'datetime',
        'delivery_correction_allowed_until' => 'datetime',
        'cancellation_time' => 'datetime',
        'receptionist_cancellation_allowed_until' => 'datetime',
        'sop_violation_flag' => 'boolean',
        'is_cancelled_by_receptionist' => 'boolean',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id', 'room_id');
    }

    public function receptionist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receptionist_user_id', 'user_id');
    }
    
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    // --- TAMBAHKAN DUA FUNGSI DI BAWAH INI ---

    /**
     * Relasi ke model User (Staf Dapur).
     */
    public function kitchenStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kitchen_staff_user_id', 'user_id');
    }

    /**
     * Relasi ke model User (Staf Antar).
     */
    public function deliveryStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delivery_staff_user_id', 'user_id');
    }
}