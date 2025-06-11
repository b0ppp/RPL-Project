<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-4xl text-black leading-tight">
            {{ __('Riwayat Pesanan') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Notifikasi Sukses atau Error --}}
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-xl rounded-lg border border-gray-200">
                <div class="space-y-4 p-4 sm:p-6">
                    @forelse ($orders as $order)
                        <div class="p-4 border rounded-lg flex items-center justify-between hover:bg-gray-50/50">
                            {{-- Kolom Kiri: Detail Pesanan --}}
                            <div class="flex-grow">
                                <div class="grid grid-cols-2 gap-x-4">
                                    {{-- Daftar Item --}}
                                    <div>
                                        @foreach ($order->orderItems as $item)
                                            <div class="flex justify-between">
                                                <span class="text-gray-800">{{ $item->menuItem->item_name }}</span>
                                                <span class="text-gray-500">x{{ $item->quantity }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    {{-- Info Kamar --}}
                                    <div class="text-right">
                                        <p class="font-semibold">{{ $order->room->roomType->name ?? 'Tipe Kamar Dihapus' }} - {{ $order->room->room_number ?? 'Kamar Dihapus' }}</p>
                                        <p class="text-sm text-gray-500">No. #{{ $order->order_id }}</p>
                                        <p class="text-xs text-gray-400">{{ $order->order_time->format('d M Y, H:i:s') }}</p>
                                    </div>
                                </div>
                            </div>
                            {{-- Kolom Kanan: Tombol Aksi --}}
                            <div class="flex-shrink-0 ml-6">
                                {{-- Logika Tombol Hapus 30 Detik --}}
                                @if ($order->receptionist_cancellation_allowed_until && $order->receptionist_cancellation_allowed_until->isFuture())
                                    {{-- JIKA MASIH BISA DIHAPUS --}}
                                    <form action="{{ route('receptionist.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Anda yakin ingin membatalkan pesanan No. #{{ $order->order_id }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-100 rounded-full transition-colors" title="Batalkan Pesanan">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    {{-- JIKA SUDAH TIDAK BISA DIHAPUS --}}
                                    <div class="p-2 text-gray-300" title="Waktu pembatalan sudah lewat">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0m-3.75 9.5a1.5 1.5 0 0 1-3 0V14.25a1.5 1.5 0 0 1 3 0v4.5Z" />
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 9.5a1.5 1.5 0 0 1-3 0V5.25a1.5 1.5 0 0 1 3 0v4.5Z" />
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-10">Belum ada riwayat pesanan.</p>
                    @endforelse
                </div>
                {{-- Link Paginasi --}}
                <div class="p-4">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>