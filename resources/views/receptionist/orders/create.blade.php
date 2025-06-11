@php
use Illuminate\Support\Js;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-4xl text-black leading-tight">
            Pesan Antar Makanan
        </h2>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8" x-data="orderManager(
        {{ Js::from($menuItems ?? []) }},
        {{ Js::from($occupiedRooms ?? []) }}
    )">
    
        {{-- Search Bar --}}
        <div class="mb-8 max-w-xl mx-auto">
            <input type="text" x-model.debounce.300ms="searchQuery" placeholder="Cari Menu..."
                   class="w-full px-6 py-3 border border-gray-300 rounded-full shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-8">
            {{-- Kolom Kiri: Daftar Menu --}}
            <div class="md:col-span-2 bg-white p-6 shadow-xl rounded-lg border border-gray-200">
                <h3 class="text-2xl font-semibold mb-6 text-gray-800 border-b pb-3">Daftar Menu Tersedia</h3>
                <div class="space-y-4 max-h-[65vh] overflow-y-auto pr-2 custom-scrollbar">
                    <template x-for="menuItem in filteredMenuItems" :key="menuItem.menu_item_id">
                        <div class="border rounded-lg p-4 flex justify-between items-center hover:shadow-md transition-shadow">
                            <div>
                                <h4 class="text-lg font-medium" x-text="menuItem.item_name"></h4>
                                <input type="text" x-model="notes[menuItem.menu_item_id]" @input.debounce.500ms="updateNoteInCart(menuItem)"
                                       placeholder="Beri catatan..." class="mt-2 text-sm w-60 border-gray-300 rounded-md shadow-sm">
                            </div>
                            <button type="button" @click="addToCart(menuItem)" title="Tambah ke Pesanan"
                                    class="w-10 h-10 flex items-center justify-center bg-green-500 text-white rounded-full hover:bg-green-600">
                                <span>+</span>
                            </button>
                        </div>
                    </template>
                    <template x-if="!filteredMenuItems.length && searchQuery">
                        <p class="text-gray-500 py-4 text-center">Tidak ada item menu yang cocok dengan pencarian Anda.</p>
                    </template>
                </div>
            </div>

            {{-- Kolom Kanan: Ringkasan Pesanan --}}
            <div class="md:col-span-1 bg-white p-6 shadow-xl rounded-lg border h-fit sticky top-20"> 
                <h3 class="text-2xl font-semibold mb-6 text-gray-800 border-b pb-3">Pesanan Saat Ini</h3>
                <div id="current-order-items" class="space-y-3 mb-6 min-h-[calc(65vh-200px)] border-b pb-4 custom-scrollbar overflow-y-auto">
                    <template x-if="isCartEmpty"><p class="text-gray-400 italic text-center py-10">Belum ada item.</p></template>
                    <template x-for="cartItem in cart" :key="cartItem.id">
                        <div class="py-2 border-b">
                            <div class="flex justify-between items-center">
                                <span class="font-medium" x-text="cartItem.name"></span>
                                <button @click="removeFromCart(cartItem.id)" class="text-red-500 hover:text-red-700 text-xs font-semibold">Hapus</button>
                            </div>
                            <p class="text-xs text-gray-500 italic" x-show="cartItem.note" x-text="'Catatan: ' + cartItem.note"></p>
                            <div class="flex items-center justify-end mt-1 space-x-1">
                                <button @click="decreaseQuantity(cartItem)" class="px-2 py-0.5 bg-gray-200 rounded-md hover:bg-gray-300">-</button>
                                <span class="px-3 py-0.5 border-y" x-text="cartItem.quantity"></span>
                                <button @click="increaseQuantity(cartItem)" class="px-2 py-0.5 bg-gray-200 rounded-md hover:bg-gray-300">+</button>
                            </div>
                        </div>
                    </template>
                </div>
                
                <div class="mt-6">
                    <button type="button" @click="openSubmitModal()" x-show="!isCartEmpty"
                            class="w-full justify-center inline-flex items-center px-4 py-3 bg-gray-800 text-white rounded-md font-semibold text-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                            :disabled="isCartEmpty">
                        Kirim Pesanan
                    </button>
                </div>
            </div>
        </div>

        {{-- Modal --}}
        <div x-show="isModalOpen" style="display: none;"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
             @keydown.escape.window="isModalOpen = false">
            
            <div class="bg-white rounded-lg shadow-2xl p-6 sm:p-8 w-full max-w-lg mx-4" @click.outside="isModalOpen = false">
                <h3 class="text-2xl font-semibold mb-6">Konfirmasi Pesanan</h3>
                
                <div class="space-y-6">
                    <div>
                        <label for="room_id_modal" class="block font-medium text-sm text-gray-700">Pilih Kamar:</label>
                        <select id="room_id_modal" x-model="selectedRoomId"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="" disabled>-- Pilih Nomor Kamar --</option>
                            <template x-for="room in occupiedRoomsFromPHP" :key="room.room_id">
                                <option :value="room.room_id" x-text="`Kamar ${room.room_number} (${room.room_type ? room.room_type.name : ''})`"></option>
                            </template>
                        </select>
                    </div>

                    <div x-show="successMessage" class="p-4 text-sm text-green-700 bg-green-100 rounded-lg" x-text="successMessage" style="display: none;"></div>
                    <div x-show="errorMessage" class="p-4 text-sm text-red-700 bg-red-100 rounded-lg" x-text="errorMessage" style="display: none;"></div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <button type="button" @click="isModalOpen = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                        Batal
                    </button>
                    {{-- PERBAIKAN FINAL BERDASARKAN PENEMUAN ANDA --}}
                    <button type="button" @click="submitOrder()"
                            class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 disabled:opacity-50 flex items-center"
                            :disabled="isLoading || !selectedRoomId || successMessage.length > 0">
                        <svg x-show="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isLoading ? 'Mengirim...' : 'Konfirmasi & Kirim Pesanan'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function orderManager(menuItems, occupiedRooms) {
            return {
                searchQuery: '',
                displayMenuItems: menuItems || [],
                cart: [],
                notes: {},
                occupiedRoomsFromPHP: occupiedRooms || [],
                isModalOpen: false,
                selectedRoomId: '',
                isLoading: false,
                successMessage: '',
                errorMessage: '',
                get filteredMenuItems() {
                    if (!this.displayMenuItems) return [];
                    if (this.searchQuery.trim() === '') return this.displayMenuItems;
                    return this.displayMenuItems.filter(item =>
                        item && item.item_name && String(item.item_name).toLowerCase().includes(this.searchQuery.toLowerCase())
                    );
                },
                get isCartEmpty() { return this.cart.length === 0; },
                addToCart(menuItem) {
                    let existingItem = this.cart.find(item => item.id === menuItem.menu_item_id);
                    if (existingItem) {
                        existingItem.quantity++;
                    } else {
                        this.cart.push({
                            id: menuItem.menu_item_id, name: menuItem.item_name, quantity: 1,
                            note: this.notes[menuItem.menu_item_id] || ''
                        });
                    }
                },
                updateNoteInCart(menuItem) {
                   let itemInCart = this.cart.find(item => item.id === menuItem.menu_item_id);
                   if (itemInCart) { itemInCart.note = this.notes[menuItem.menu_item_id] || ''; }
                },
                increaseQuantity(cartItem) { cartItem.quantity++; },
                decreaseQuantity(cartItem) {
                    if (cartItem.quantity > 1) { cartItem.quantity--; } 
                    else { this.removeFromCart(cartItem.id); }
                },
                removeFromCart(itemId) {
                    this.cart = this.cart.filter(item => item.id !== itemId);
                },
                openSubmitModal() {
                    this.errorMessage = '';
                    this.successMessage = '';
                    this.isModalOpen = true;
                },
                submitOrder() {
                    if (!this.selectedRoomId) {
                        this.errorMessage = 'Anda harus memilih kamar terlebih dahulu.';
                        return;
                    }
                    this.isLoading = true;
                    this.errorMessage = '';
                    this.successMessage = '';
                    axios.post('{{ route('receptionist.orders.store') }}', {
                        room_id: this.selectedRoomId,
                        cart_items: this.cart
                    })
                    .then(response => {
                        if (response.data.success) {
                            this.successMessage = response.data.message;
                            this.cart = [];
                            this.notes = {};
                            this.selectedRoomId = '';
                            setTimeout(() => { this.isModalOpen = false; this.successMessage = ''; }, 1000);
                        } else {
                            this.errorMessage = response.data.message || 'Server mengindikasikan kegagalan.';
                        }
                    })
                    .catch(error => {
                        if (error.response && error.response.data && error.response.data.message) {
                            this.errorMessage = error.response.data.message;
                        } else {
                            this.errorMessage = 'Gagal terhubung ke server. Periksa koneksi Anda.';
                        }
                    })
                    .finally(() => {
                        this.isLoading = false;
                    });
                }
            };
        }
    </script>
</x-app-layout>