<x-guest-layout>
    <div class="text-center mb-6 md:mb-8">
        <h1 class="text-3xl font-bold text-gray-700 md:text-4xl">
            Buat Pengguna Baru
        </h1>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <div>
            <x-input-label for="fullname" :value="__('Nama Lengkap:')" />
            <x-text-input id="fullname" class="block mt-1 w-full" type="text" name="fullname" :value="old('fullname')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('fullname')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="username" :value="__('Username:')" />
            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Alamat Email:')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="role_id" :value="__('Peran (Role):')" />
            <select id="role_id" name="role_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                <option value="">{{ __('-- Pilih Peran --') }}</option>
                @if(isset($roles)) {{-- Pastikan $roles ada sebelum di-loop --}}
                    @foreach ($roles as $role)
                        <option value="{{ $role->role_id }}" {{ old('role_id') == $role->role_id ? 'selected' : '' }}>
                            {{ $role->role_name }}
                        </option>
                    @endforeach
                @endif
            </select>
            <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password:')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password:')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-center pt-4">
            <button type="submit"
                    class="w-full justify-center inline-flex items-center px-4 py-3 bg-gray-200 border border-gray-400 rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                {{ __('Daftarkan Pengguna') }}
            </button>
        </div>
        
        <div class="text-center mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('admin.dashboard') }}">
                {{ __('Kembali ke Dashboard Admin') }}
            </a>
        </div>
    </form>
</x-guest-layout>