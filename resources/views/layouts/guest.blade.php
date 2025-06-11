<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        {{-- 
            Div ini akan menjadi flex container utama yang memenuhi layar
            dan menengahkan kontennya (kartu login/registrasi).
        --}}
        <div class="min-h-screen flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8 py-12">
        {{-- 
            - min-h-screen: Minimal tinggi div ini adalah setinggi layar viewport.
            - flex flex-col: Mengaktifkan flexbox dengan arah kolom (meskipun di sini kita hanya punya satu anak utama yaitu kartu).
            - justify-center: Menengahkan item secara vertikal di dalam container flex.
            - items-center: Menengahkan item secara horizontal di dalam container flex.
            - px-4 sm:px-6 lg:px-8: Memberi padding horizontal di sisi layar agar kartu tidak menempel di tepi layar kecil.
            - py-12: Memberi padding vertikal atas dan bawah untuk keseluruhan area, 
                       ini akan memberi ruang jika kontennya pendek, dan justify-center akan tetap bekerja.
                       Anda bisa sesuaikan nilai padding ini.
        --}}
            
            {{-- Ini adalah Kartu yang akan berisi slot (form login/registrasi) --}}
            {{-- Kita gunakan max-w-lg agar tidak terlalu lebar di desktop, sesuai diskusi sebelumnya --}}
            {{-- Styling untuk kartu putih dengan border dan shadow --}}
            <div class="w-full max-w-lg bg-white shadow-xl rounded-lg p-6 sm:p-8 border border-gray-200">
            {{-- 
                - w-full: Lebar penuh hingga batas max-w-lg.
                - max-w-lg: Batas lebar maksimum kartu. Anda bisa sesuaikan (misal, max-w-md atau max-w-xl).
                - bg-white: Latar belakang putih (tidak ada dark: variant di sini agar selalu putih).
                - shadow-xl: Memberi efek bayangan yang sedikit lebih jelas.
                - rounded-lg: Sudut yang membulat.
                - p-6 sm:p-8: Padding di dalam kartu.
                - border border-gray-200: Border abu-abu terang.
            --}}
                {{-- Logo Laravel sudah kita hapus dari sini sebelumnya --}}
                
                {{ $slot }} {{-- Di sinilah konten dari login.blade.php atau register.blade.php akan dimasukkan --}}
            </div>
        </div>
    </body>
</html>