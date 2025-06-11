<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-4xl text-black leading-tight">
            {{ __('Tambah Kamar Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 sm:p-8 bg-white border-b border-gray-200">

                    @if ($errors->any())
                        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">Oops! Ada yang salah:</strong>
                            <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.rooms.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="room_number" :value="__('Nomor Kamar:')" />
                            <x-text-input id="room_number" class="block mt-1 w-full" type="text" name="room_number" :value="old('room_number')" required autofocus placeholder="Contoh: 101, 205A, VIP-01" />
                            <x-input-error :messages="$errors->get('room_number')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="room_type_id" :value="__('Tipe Kamar:')" /> {{-- Label bisa tetap, atau ubah for dan id --}}
                            <select id="room_type_id" name="room_type_id" {{-- UBAH name menjadi room_type_id --}}
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">{{ __('-- Pilih Tipe Kamar --') }}</option>
                                {{-- Loop dari collection $roomTypes --}}
                                @foreach ($roomTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('room_type_id') == $type->id ? 'selected' : '' }}>
                                    {{--  ^^^^^^^^^^^^^^^ value adalah ID dari tipe kamar --}}
                                        {{ $type->name }} {{-- Tampilkan nama tipe kamar --}}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('room_type_id')" class="mt-2" /> {{-- Error untuk room_type_id --}}
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Status Kamar Awal:')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                @foreach ($statuses as $statusOption)
                                    <option value="{{ $statusOption }}" {{ (old('status', 'Kosong') == $statusOption) ? 'selected' : '' }}> 
                                    {{-- Default ke 'Kosong' jika tidak ada old value --}}
                                        {{ $statusOption }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end pt-4 space-x-3">
                            <a href="{{ route('admin.rooms.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Batal
                            </a>
                            <x-primary-button>
                                {{ __('Simpan Kamar') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>