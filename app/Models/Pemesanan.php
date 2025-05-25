<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pemesanan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'resepsionis_id',
        'staff__pengantaran_id',
        'staff__dapur_id',
        'kamar_id',
        'nama_pesanan',
        'date/time_diantar',
    ];

    // ORM
    public function resepsionis(){
        return $this->belongsTo(Resepsionis::class);
    }

    public function staff_dapur(){
        return $this->belongsTo(Staff_Dapur::class);
    }

    public function staff_pengantaran(){
        return $this->belongsTo(Staff_Pengantaran::class);
    }

    public function kamars(){
        return $this->hasMany(Kamar::class);
    }    

}