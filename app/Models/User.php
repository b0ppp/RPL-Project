<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail; // Aktifkan jika Anda akan menggunakan verifikasi email
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable // Pastikan extends Authenticatable
{
    use HasFactory, Notifiable; // Tambahkan HasApiTokens jika Anda berencana menggunakan Laravel Sanctum

    /**
     * The primary key associated with the table.
     * Hanya diperlukan jika primary key Anda BUKAN 'id'.
     *
     * @var string
     */
    protected $primaryKey = 'user_id'; // Sesuaikan jika primary key Anda 'id' (maka baris ini tidak perlu)

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fullname',
        'username',
        'email',
        'password', // Kolom password di database
        'role_id',
        'status',
        // 'email_verified_at', // Jika Anda menggunakan verifikasi email
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime', // Jika Anda menggunakan verifikasi email
        'password' => 'hashed', // Sejak Laravel 9+, ini akan otomatis hash saat diset jika belum di-hash manual
    ];

    /**
     * Mendapatkan peran (role) yang dimiliki oleh pengguna.
     */
    public function role()
    {
        // Foreign key di tabel 'users' adalah 'role_id'
        // Primary key di tabel 'roles' adalah 'role_id'
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }
}