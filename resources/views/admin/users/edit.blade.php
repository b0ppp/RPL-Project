<x-app-layout>
    <x-slot name="header">
        {{-- Judul halaman dengan ukuran font text-4xl --}}
        <h2 class="font-semibold text-4xl text-black leading-tight">
        {{--                     ^^^^^^^^^ Diubah dari text-xl menjadi text-4xl --}}
            {{ __('Edit Pengguna: ') }} {{ $user->username }}
        </h2>
    </x-slot>

    <div class="py-12">
        {{-- Mengubah max-w-3xl menjadi max-w-xl agar kotak form lebih kecil --}}
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
        {{--         ^^^^^^^^^ Diubah dari max-w-3xl menjadi max-w-xl (Anda bisa coba juga max-w-lg) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 sm:p-8 bg-white border-b border-gray-200"> {{-- Tambahkan sm:p-8 untuk padding lebih di layar >sm --}}
                    
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

                    <form method="POST" action="{{ route('admin.users.update', $user->user_id) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="fullname" :value="__('Nama Lengkap:')" />
                            <x-text-input id="fullname" class="block mt-1 w-full" type="text" name="fullname" :value="old('fullname', $user->fullname)" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('fullname')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="username" :value="__('Username:')" />
                            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username', $user->username)" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('username')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Alamat Email:')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required autocomplete="email" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="role_id" :value="__('Peran (Role):')" />
                            <select id="role_id" name="role_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">{{ __('-- Pilih Peran --') }}</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->role_id }}" {{ old('role_id', $user->role_id) == $role->role_id ? 'selected' : '' }}>
                                        {{ $role->role_name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Status Akun:')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="Aktif" {{ old('status', $user->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Tidak Aktif" {{ old('status', $user->status) == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="border-t border-gray-200 pt-6 mt-6">
                            <p class="text-sm text-gray-600 mb-4">Biarkan kosong jika tidak ingin mengubah password.</p>
                            <div>
                                <x-input-label for="password" :value="__('Password Baru (Opsional):')" />
                                <x-text-input id="password" class="block mt-1 w-full"
                                                type="password"
                                                name="password"
                                                autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="password_confirmation" :value="__('Konfirmasi Password Baru:')" />
                                <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                                type="password"
                                                name="password_confirmation" autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-4 space-x-3"> {{-- pt-4 untuk sedikit ruang di atas tombol --}}
                            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Batal
                            </a>
                            <x-primary-button>
                                {{ __('Simpan Perubahan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>