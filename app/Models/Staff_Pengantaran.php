<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff_Pengantaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama_staff_pengantaran',
        'domisili_staff_pengantaran',
        'ttl_staff_pengantaran',
        'kontak_staff_pengantaran',
    ];

    public function pemesanans(){
        return $this->hasMany(Pemesanan::class);
    }
}
