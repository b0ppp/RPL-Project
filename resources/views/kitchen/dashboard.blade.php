<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-4xl text-black leading-tight">
            Pesanan Dapur
        </h2>
    </x-slot>

    {{-- Komponen Alpine utama untuk mengelola seluruh dashboard --}}
    <div class="py-8" x-data="kitchenDashboard({{ Js::from($orders) }})">
        
        {{-- Banner Notifikasi Real-time --}}
        <div x-show="showNotification" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90"
             class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-6" style="display: none;">
            <div class="p-4 bg-blue-600 text-white rounded-lg shadow-lg flex justify-between items-center">
                <p class="font-bold">🔔 Pesanan Baru Diterima! Daftar diperbarui.</p>
                <button @click="showNotification = false" class="text-2xl font-bold leading-none">&times;</button>
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-4 sm:p-6 shadow-xl rounded-lg border border-gray-200">
                <div class="space-y-4">
                    <template x-if="orders.length === 0">
                        <p class="text-center text-gray-500 py-16 text-2xl">Tidak ada pesanan aktif.</p>
                    </template>
                    
                    {{-- Daftar pesanan sekarang di-render oleh Alpine.js --}}
                    <template x-for="order in orders" :key="order.order_id">
                         <div x-data="kitchenOrderItem(order)"
                             class="p-4 border rounded-lg flex items-center justify-between transition-opacity duration-500"
                             :class="{ 'opacity-50': order.order_status === 'Dihantarkan', 'opacity-30': order.order_status === 'Diterima' }">
                            
                            <div class="flex-grow">
                                <div class="grid grid-cols-2 gap-x-4">
                                    <div>
                                        <template x-for="item in order.order_items" :key="item.order_item_id">
                                            <p>
                                                <span class="font-medium" x-text="item.menu_item.item_name"></span>
                                                <span class="text-gray-600" x-text="'x' + item.quantity"></span>
                                                <template x-if="item.item_notes">
                                                    <p class="text-xs text-red-600 italic pl-2" x-text="'- ' + item.item_notes"></p>
                                                </template>
                                            </p>
                                        </template>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-lg" :class="timer.isLate ? 'text-gray-400' : 'text-red-600'" x-text="timer.display"></p>
                                        <p class="text-sm text-gray-500" x-text="'No. #' + order.order_id"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex-shrink-0 ml-6">
                                <div @click="toggleCheck()"
                                     class="w-12 h-12 border-2 rounded-md flex items-center justify-center"
                                     :class="{
                                         'bg-green-500 border-green-600': order.order_status === 'Siap Dihantar',
                                         'border-gray-400 hover:bg-gray-100': order.order_status === 'Diproses',
                                         'bg-blue-100': order.order_status === 'Dihantarkan',
                                         'bg-gray-200': order.order_status === 'Diterima',
                                         'cursor-pointer': canInteract,
                                         'cursor-not-allowed': !canInteract || isLoading,
                                         'animate-pulse': canUncheck
                                     }">
                                    <svg x-show="order.order_status !== 'Diproses'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" class="w-8 h-8" 
                                        :class="order.order_status === 'Siap Dihantar' ? 'stroke-white' : 'stroke-gray-500'" style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                </div>
                                <p x-show="canUncheck" class="text-xs text-center text-blue-600" x-text="uncheckTimer.display"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <script>
        function kitchenDashboard(initialOrders) {
            return {
                orders: initialOrders,
                showNotification: false,
                notificationSound: new Audio('/sounds/notification.mp3'),
                init() {
                    if (typeof window.Echo === 'undefined') {
                        console.error('Error: Laravel Echo tidak terkonfigurasi.');
                        return;
                    }
                    window.Echo.private('kitchen-orders')
                        .listen('.new-order', (event) => {
                            this.orders.unshift(event.order);
                            this.showNotification = true;
                            this.notificationSound.play().catch(e => console.error("Gagal memainkan suara.", e));
                        });
                }
            }
        }

        function kitchenOrderItem(order) {
            return {
                order: order,
                canUncheck: false,
                uncheckDeadline: null,
                timer: { display: '30:00', isLate: false },
                uncheckTimer: { display: '0:30' },
                isLoading: false,
                mainInterval: null,
                uncheckInterval: null,

                get canInteract() {
                    return this.order.order_status === 'Diproses' || (this.order.order_status === 'Siap Dihantar' && this.canUncheck);
                },

                init() {
                    this.updateMainTimer();
                    this.mainInterval = setInterval(() => this.updateMainTimer(), 1000);
                    if (this.order.order_status === 'Siap Dihantar') {
                        this.uncheckDeadline = new Date(this.order.kitchen_uncheck_allowed_until);
                        this.startUncheckTimer();
                    }
                },
                updateMainTimer() {
                    if (['Dihantarkan', 'Diterima'].includes(this.order.order_status)) {
                        this.timer.display = '✓'; this.timer.isLate = true; clearInterval(this.mainInterval); return;
                    }
                    let timeRemaining = (30 * 60 * 1000) - (new Date() - new Date(this.order.order_time));
                    if (timeRemaining <= 0) { timeRemaining = 0; this.timer.isLate = true; clearInterval(this.mainInterval); }
                    let minutes = Math.floor(timeRemaining / 60000);
                    let seconds = Math.floor((timeRemaining % 60000) / 1000);
                    this.timer.display = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                },
                startUncheckTimer() {
                    if (!this.uncheckDeadline || new Date() > this.uncheckDeadline) { this.canUncheck = false; return; }
                    this.canUncheck = true;
                    if(this.uncheckInterval) clearInterval(this.uncheckInterval);
                    this.uncheckInterval = setInterval(() => {
                        let timeRemaining = this.uncheckDeadline - new Date();
                        if (timeRemaining < 0) {
                            this.canUncheck = false; this.uncheckTimer.display = ''; clearInterval(this.uncheckInterval);
                        } else {
                            this.uncheckTimer.display = `0:${String(Math.ceil(timeRemaining/1000)).padStart(2, '0')}`;
                        }
                    }, 1000);
                },
                toggleCheck() {
                    if (!this.canInteract || this.isLoading) return;
                    if (this.order.order_status === 'Siap Dihantar') {
                        this.uncheckOrder();
                    } else {
                        this.checkOrder();
                    }
                },
                checkOrder() {
                    this.isLoading = true;
                    axios.patch(`/kitchen/orders/${this.order.order_id}/mark-as-ready`)
                        .then(res => {
                            this.order.order_status = 'Siap Dihantar';
                            this.uncheckDeadline = new Date(new Date().getTime() + 30000);
                            this.startUncheckTimer();
                        }).catch(err => alert('Gagal: ' + (err.response?.data?.message || 'Terjadi kesalahan')))
                        .finally(() => this.isLoading = false);
                },
                uncheckOrder() {
                    this.isLoading = true;
                    axios.patch(`/kitchen/orders/${this.order.order_id}/uncheck`)
                        .then(res => {
                            this.order.order_status = 'Diproses';
                            this.canUncheck = false;
                            clearInterval(this.uncheckInterval);
                            this.uncheckTimer.display = '';
                        }).catch(err => alert('Gagal: ' + (err.response?.data?.message || 'Terjadi kesalahan')))
                        .finally(() => this.isLoading = false);
                }
            }
        }
    </script>
</x-app-layout>