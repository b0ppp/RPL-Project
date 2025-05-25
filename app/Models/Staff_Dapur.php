<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Staff_Dapur extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama_staff_dapur',
        'domisili_staff_dapur',
        'ttl_staff_dapur',
        'kontak_staff_dapur',
    ];

    public function pemesanans(){
        return $this->hasMany(Pemesanan::class);
    }
}
