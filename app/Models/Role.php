<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'role_id'; // Primary key kita adalah 'role_id'

    /**
     * Indicates if the model should be timestamped.
     * Kita tidak menggunakan created_at/updated_at di tabel roles.
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     * Kolom yang boleh diisi saat membuat record baru secara massal.
     * @var array<int, string>
     */
    protected $fillable = [
        'role_name',
    ];
}