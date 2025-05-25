<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resepsionis extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama_resepsionis',
        'domisili_resepsionis',
        'ttl_resepsionis',
        'kontak_resepsionis',
    ];

    public function pemesanans(){
        return $this->hasMany(Pemesanan::class);
    }
}
