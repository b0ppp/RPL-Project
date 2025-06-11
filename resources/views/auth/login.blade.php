<x-guest-layout>
    <div class="text-center mb-6 md:mb-8">
        <h1 class="text-3xl font-bold text-gray-700 md:text-4xl">
            Hotel Appala
        </h1>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6"> {{-- space-y-6 untuk jarak vertikal --}}
        @csrf

        <div>
            <x-input-label for="username" :value="__('Username:')" />
            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <div> {{-- Hapus mt-4 jika space-y pada form sudah cukup --}}
            <x-input-label for="password" :value="__('Password:')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- "Remember Me" dan "Forgot password?" sudah dihapus sesuai permintaan --}}

        <div class="flex items-center justify-center pt-4">
            {{-- Tombol Login dengan styling agar terlihat seperti kotak --}}
            <button type="submit"
                    class="w-full justify-center inline-flex items-center px-4 py-3 bg-gray-200 border border-gray-400 rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                {{ __('Login') }}
            </button>
            {{-- Jika ingin tetap menggunakan komponen x-primary-button tapi mengubah stylenya, Anda harus modifikasi file komponennya --}}
        </div>
    </form>
</x-guest-layout>