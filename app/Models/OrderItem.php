<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * Nama primary key tabel.
     *
     * @var string
     */
    protected $primaryKey = 'order_item_id';

    /**
     * Menonaktifkan timestamps (created_at, updated_at) untuk model ini.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Atribut yang bisa diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'menu_item_id',
        'quantity',
        'item_notes',
    ];

    /**
     * Relasi ke model Order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    /**
     * Relasi ke model MenuItem.
     */
    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id', 'menu_item_id');
    }
}