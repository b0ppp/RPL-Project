<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-4xl text-black leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold">{{ __("Selamat datang di Dashboard Admin!") }}</h3>
                    <p class="mt-2">Dari sini Anda bisa mengelola berbagai aspek sistem.</p>

                    <div class="mt-6">
                        <h4 class="text-md font-semibold mb-2">Tindakan Cepat:</h4>
                        <div class="space-y-2">
                            <a href="{{ route('register') }}" class="inline-block px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                                {{ __('+ Buat Pengguna Baru') }}
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="inline-block px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                                {{ __('Lihat Daftar Pengguna') }}  {{-- LINK BARU --}}
                            </a>
                            {{-- LINK BARU UNTUK MANAJEMEN MENU --}}
                            <a href="{{ route('admin.menuitems.index') }}" class="inline-block px-4 py-2 bg-purple-500 text-white rounded-md hover:bg-purple-600 shadow-sm text-sm font-medium">
                                {{ __('Manajemen Menu') }}
                            </a>
                            <a href="{{ route('admin.rooms.index') }}" class="inline-block px-4 py-2 bg-teal-500 text-white rounded-md hover:bg-teal-600 shadow-sm text-sm font-medium">
                                {{ __('Manajemen Kamar') }} {{-- TOMBOL BARU --}}
                            </a>
                            <a href="{{ route('admin.roomtypes.index') }}" class="inline-block px-4 py-2 bg-cyan-500 text-white rounded-md hover:bg-cyan-600 shadow-sm text-sm font-medium">
                                {{ __('Manajemen Tipe Kamar') }} {{-- TOMBOL BARU --}}
                            </a>
                        </div>
                    </div>

                    {{-- Menampilkan pesan sukses jika ada --}}
                    @if (session('success'))
                        <div class="mt-6 mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>