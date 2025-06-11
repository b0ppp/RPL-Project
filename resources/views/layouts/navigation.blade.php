@props(['pageTitleSlot' => null])

<nav class="sticky top-0 z-40 w-full
        @auth
            @if(Auth::user()->role && Auth::user()->role->role_name === 'Admin')
                border-b border-black/20 backdrop-blur-sm mb-6
            @else
                py-2 border-b border-black/10 backdrop-blur-sm mb-4
            @endif
        @endauth
    ">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            
            @auth
                {{-- Tampilan untuk Admin --}}
                @if(Auth::user()->role && Auth::user()->role->role_name === 'Admin')
                    <div class="flex items-center space-x-4 sm:space-x-8">
                        <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium text-black hover:bg-gray-200/75 transition-colors">Dashboard Admin</a>
                        <a href="{{ route('admin.users.index') }}" class="px-3 py-2 rounded-md text-sm font-medium text-black hover:bg-gray-200/75 transition-colors">Manajemen Pengguna</a>
                        <a href="{{ route('admin.menuitems.index') }}" class="px-3 py-2 rounded-md text-sm font-medium text-black hover:bg-gray-200/75 transition-colors">Manajemen Menu</a>
                        <a href="{{ route('admin.rooms.index') }}" class="px-3 py-2 rounded-md text-sm font-medium text-black hover:bg-gray-200/75 transition-colors">Manajemen Kamar</a>
                        <a href="{{ route('admin.roomtypes.index') }}" class="px-3 py-2 rounded-md text-sm font-medium text-black hover:bg-gray-200/75 transition-colors">Manajemen Tipe Kamar</a>
                    </div>
                    <div class="flex items-center">
                         <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" title="Log Out" class="inline-flex items-center justify-center w-10 h-10 text-black rounded-full hover:bg-gray-200/75 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H5.25" />
                                </svg>
                            </button>
                        </form>
                    </div>

                {{-- Tampilan untuk Resepsionis --}}
                @elseif(Auth::user()->role && Auth::user()->role->role_name === 'Resepsionis')
                    <div class="flex-shrink-0">
                        @if (Route::currentRouteName() == 'receptionist.orders.history')
                            <a href="{{ route('receptionist.order.create') }}" title="Kembali ke Buat Pesanan" class="inline-flex items-center justify-center w-10 h-10 text-black rounded-full hover:bg-gray-200/75 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-8 h-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('receptionist.orders.history') }}" title="Riwayat Pesanan" class="inline-flex items-center justify-center w-10 h-10 text-black rounded-full hover:bg-gray-200/75 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </a>
                        @endif
                    </div>
                    <div class="flex-grow text-center px-2">
                        @if ($pageTitleSlot && $pageTitleSlot->isNotEmpty())
                            {{ $pageTitleSlot }}
                        @else
                            <h2 class="font-semibold text-4xl text-black leading-tight">{{ config('app.name', 'Laravel') }}</h2>
                        @endif
                    </div>
                    <div class="flex-shrink-0">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" title="Log Out" class="inline-flex items-center justify-center w-10 h-10 text-black rounded-full hover:bg-gray-200/75 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H5.25" />
                                </svg>
                            </button>
                        </form>
                    </div>

                {{-- Tampilan untuk peran lain (Staf Dapur, Staf Antar, dll) --}}
                @else
                    <div class="w-10 h-10"></div> {{-- Placeholder kosong agar judul tetap di tengah --}}
                    <div class="flex-grow text-center px-2">
                        @if ($pageTitleSlot && $pageTitleSlot->isNotEmpty())
                            {{ $pageTitleSlot }}
                        @else
                            <h2 class="font-semibold text-4xl text-black leading-tight">{{ config('app.name', 'Laravel') }}</h2>
                        @endif
                    </div>
                    <div class="flex-shrink-0 w-10 h-10">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" title="Log Out" class="inline-flex items-center justify-center w-10 h-10 text-black rounded-full hover:bg-gray-200/75 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H5.25" />
                                </svg>
                            </button>
                        </form>
                    </div>
                @endif
            @else 
                <div class="flex-grow">&nbsp;</div>
            @endauth
        </div>
    </div>
</nav>