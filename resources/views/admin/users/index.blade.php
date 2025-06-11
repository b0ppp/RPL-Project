<x-app-layout>
    <x-slot name="header">
        {{-- Judul ini akan muncul di navbar --}}
        <h2 class="font-semibold text-4xl text-black leading-tight">
            {{ __('Manajemen Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">{{ __('Daftar Pengguna Terdaftar') }}</h3>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                            {{ __('+ Tambah Pengguna Baru') }}
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- TAMBAHKAN BLOK INI UNTUK PESAN ERROR --}}
                    @if (session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama Lengkap
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Username
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Peran (Role)
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Terdaftar Sejak
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Aksi</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($users as $user)
                                    <tr>
                                        {{-- Kolom ID Pengguna --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $user->user_id }}
                                        </td>
                                        {{-- Kolom Nama Lengkap --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $user->fullname }}
                                        </td>
                                        {{-- Kolom Username --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $user->username }}
                                        </td>
                                        {{-- Kolom Email --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $user->email }}
                                        </td>
                                        {{-- Kolom Peran (Role) --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{-- Menampilkan nama peran jika relasi 'role' ada dan role_name tidak null --}}
                                            {{ $user->role ? $user->role->role_name : 'Tidak ada peran' }}
                                        </td>
                                        {{-- Kolom Status --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if ($user->status == 'Aktif')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Tidak Aktif
                                                </span>
                                            @endif
                                        </td>
                                        {{-- Kolom Terdaftar Sejak (created_at) --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{-- Menampilkan tanggal jika created_at tidak null --}}
                                            {{ $user->created_at ? $user->created_at->format('d M Y, H:i') : '-' }}
                                        </td>
                                        {{-- Kolom Aksi (Edit dan Delete) --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <a href="{{ route('admin.users.edit', $user->user_id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                            
                                            {{-- Hanya tampilkan tombol delete jika bukan user Admin yang sedang login (mencegah admin hapus diri sendiri) --}}
                                            {{-- Dan mungkin juga, jika user yang akan dihapus bukan satu-satunya Admin --}}
                                            @if(Auth::check() && Auth::id() !== $user->user_id)
                                                <form action="{{ route('admin.users.destroy', $user->user_id) }}" method="POST" class="inline-block" onsubmit="return confirm('Anda yakin ingin menghapus pengguna \'{{ $user->username }}\'? Tindakan ini tidak bisa dibatalkan!');">
                                                    @csrf
                                                    @method('DELETE') {{-- Penting untuk method spoofing --}}
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Tidak ada data pengguna.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- Link Paginasi --}}
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>