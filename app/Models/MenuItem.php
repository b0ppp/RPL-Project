<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'menu_item_id';

    protected $fillable = [
        'item_name',
        'availability_status',
        'created_by_user_id', // Diisi saat create, tidak diubah saat update standar
    ];

    // Relasi ke user yang membuat (opsional)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id', 'user_id');
    }
}