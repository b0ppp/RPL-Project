<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $primaryKey = 'room_id';

    protected $fillable = [
        'room_number',
        // 'room_type', // Hapus ini
        'room_type_id', // Tambahkan ini
        'status',
    ];

    // Relasi: Satu Kamar memiliki satu Tipe Kamar
    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id', 'id');
    }
}