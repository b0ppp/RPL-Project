<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', // Hanya 'name' yang bisa diisi massal sekarang
        // 'description', // HAPUS BARIS INI
    ];

    // Relasi: Satu Tipe Kamar bisa dimiliki oleh banyak Kamar
    public function rooms()
    {
        return $this->hasMany(Room::class, 'room_type_id', 'id');
    }
}