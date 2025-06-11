<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Di sini Anda bisa mendaftarkan semua channel event broadcasting yang
| didukung oleh aplikasi Anda. Laravel akan secara otomatis memuat
| file ini untuk mendaftarkan channel Anda.
|
*/

// Otorisasi untuk channel pesanan dapur.
// Hanya user yang terautentikasi DAN memiliki peran 'Staf Dapur' yang bisa mendengarkan.
Broadcast::channel('kitchen-orders', function ($user) {
    return $user && $user->role && $user->role->role_name === 'Staf Dapur';
});