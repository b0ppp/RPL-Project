<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kamar extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tipe_kamar',
        'no_kamar',
    ];

    public function pemesanan(){
        return $this->belongsTo(Pemesanan::class);
    }
}
