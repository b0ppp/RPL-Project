<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- <<< PASTIKAN BARIS INI ADA --}}

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen">
            
            {{-- Meneruskan slot $header ke navigasi agar bisa digunakan oleh non-Admin --}}
            @include('layouts.navigation', ['pageTitleSlot' => $header ?? null])

            @auth
                @if (isset($header) && Auth::user()->role && Auth::user()->role->role_name === 'Admin')
                    <header class="py-4"> {{-- Tanpa background, hanya padding vertikal --}}
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                            {{ $header }} {{-- Slot berisi h2 dari view Admin --}}
                        </div>
                    </header>
                @endif
            @endauth

            <main>
                {{-- Jika bukan Admin dan ada judul (yang akan tampil di navbar), beri sedikit padding atas tambahan --}}
                <div class="px-4 sm:px-6 lg:px-8 
                            @auth @if(Auth::user()->role && Auth::user()->role->role_name !== 'Admin' && isset($header)) pt-6 pb-8 @else py-8 @endif @else py-8 @endauth">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>