<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-4xl text-black leading-tight">
            Dashboard Pengantaran
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">{{ session('error') }}</div>
            @endif
            
            <div class="overflow-x-auto bg-white p-4 rounded-lg shadow-xl border">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        {{-- SUSUNAN KOLOM SESUAI FLOW 9 --}}
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kamar</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No.Check</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pesanan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Order</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">By (Cook)</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Timer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Delivery</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">By (Delivery)</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Pesanan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($orders as $order)
                            <tr class="align-top" x-data="deliveryOrderRow({{ Js::from($order) }}, {{ Js::from($cooks) }}, {{ Js::from($deliveryStaffs) }})">
                                {{-- PERBAIKAN: Kolom Kamar dan No.Check dipisah --}}
                                <td class="px-4 py-4 whitespace-nowrap font-medium">{{ $order->room->roomType->name }} - {{ $order->room->room_number }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-gray-600">{{ $order->order_id }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                    <template x-for="item in order.order_items" :key="item.order_item_id">
                                        <div x-text="`${item.menu_item.item_name} x${item.quantity}`"></div>
                                    </template>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600" x-text="new Date(order.order_time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })"></td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                    <div x-show="!isEditingCook && order.kitchen_staff" @dblclick="isEditingCook = true" class="cursor-pointer p-1" x-text="order.kitchen_staff.fullname"></div>
                                    <div x-show="isEditingCook || !order.kitchen_staff">
                                        <select x-model="selectedCook" @change="assignStaff('cook')" @click.outside="isEditingCook = false" class="text-sm rounded-md border-gray-300">
                                            <option value="">Pilih Koki</option>
                                            <template x-for="cook in cooks">
                                                <option :value="cook.user_id" x-text="cook.fullname"></option>
                                            </template>
                                        </select>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap font-mono font-bold" :class="timer.isLate ? 'text-gray-400' : 'text-red-600'" x-text="timer.display"></td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600" x-text="order.delivery_actual_time ? new Date(order.delivery_actual_time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-'"></td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-bold" x-text="durationText"></td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                    <div x-show="!isEditingDelivery && order.delivery_staff" @dblclick="isEditingDelivery = true" class="cursor-pointer p-1" x-text="order.delivery_staff.fullname"></div>
                                    <div x-show="isEditingDelivery || !order.delivery_staff">
                                        <select x-model="selectedDelivery" @change="assignStaff('delivery')" @click.outside="isEditingDelivery = false" class="text-sm rounded-md border-gray-300" :disabled="order.order_status === 'Diproses'">
                                            <option value="">Pilih Staf</option>
                                            <template x-for="staff in deliveryStaffs">
                                                <option :value="staff.user_id" x-text="staff.fullname"></option>
                                            </template>
                                        </select>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                    <div @dblclick="if(order.order_status === 'Siap Dihantar' || order.order_status === 'Dihantarkan') isEditingStatus = true" :class="{'cursor-pointer': (order.order_status === 'Siap Dihantar' || order.order_status === 'Dihantarkan')}">
                                        <div x-show="!isEditingStatus">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="statusClass()" x-text="order.order_status"></span>
                                        </div>
                                        <div x-show="isEditingStatus">
                                            <select @change="updateStatus" @click.outside="isEditingStatus = false" class="text-sm rounded-md border-gray-300">
                                                <option x-show="order.order_status === 'Siap Dihantar'" value="Dihantarkan">Dihantarkan</option>
                                                <option x-show="order.order_status === 'Dihantarkan'" value="Diterima">Diterima</option>
                                            </select>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="px-4 py-10 text-center text-gray-500">Tidak ada pesanan untuk hari ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        function deliveryOrderRow(order, cooks, deliveryStaffs) {
            return {
                order: order, cooks: cooks, deliveryStaffs: deliveryStaffs,
                isEditingCook: false, isEditingDelivery: false, isEditingStatus: false,
                selectedCook: order.kitchen_staff_user_id || '',
                selectedDelivery: order.delivery_staff_user_id || '',
                timer: { display: '30:00', isLate: false },
                durationText: '-',
                interval: null,
                init() {
                    this.updateTimer(); this.interval = setInterval(() => this.updateTimer(), 1000);
                    this.calculateDuration();
                },
                updateTimer() {
                    if (this.order.order_status === 'Diterima') {
                        this.timer.display = '✓'; this.timer.isLate = true; clearInterval(this.interval); return;
                    }
                    let timeRemaining = (30 * 60 * 1000) - (new Date() - new Date(this.order.order_time));
                    if (timeRemaining <= 0) { timeRemaining = 0; this.timer.isLate = true; clearInterval(this.interval); }
                    let minutes = Math.floor(timeRemaining / 60000); let seconds = Math.floor((timeRemaining % 60000) / 1000);
                    this.timer.display = `${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`;
                },
                calculateDuration() {
                    if (this.order.delivery_actual_time) {
                        let durationInSeconds = (new Date(this.order.delivery_actual_time) - new Date(this.order.order_time)) / 1000;
                        let minutes = Math.floor(durationInSeconds / 60); let seconds = Math.round(durationInSeconds % 60);
                        this.durationText = `${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`;
                    } else { this.durationText = '-'; }
                },
                statusClass() {
                    return {
                        'bg-gray-100 text-gray-800': this.order.order_status === 'Diproses',
                        'bg-yellow-100 text-yellow-800': this.order.order_status === 'Siap Dihantar',
                        'bg-blue-100 text-blue-800': this.order.order_status === 'Dihantarkan',
                        'bg-green-100 text-green-800': this.order.order_status === 'Diterima',
                    }
                },
                assignStaff(type) {
                    let userId = (type === 'cook') ? this.selectedCook : this.selectedDelivery;
                    if (!userId) return;
                    axios.patch(`/delivery/orders/${this.order.order_id}/assign-staff`, { user_id: userId, type: type })
                        .then(res => { this.order = res.data; this.isEditingCook = false; this.isEditingDelivery = false; })
                        .catch(err => { console.error(err.response.data.message); alert('Gagal: ' + (err.response.data.message || 'Terjadi kesalahan')); });
                },
                updateStatus(event) {
                    axios.patch(`/delivery/orders/${this.order.order_id}/update-status`, { status: event.target.value })
                        .then(res => { 
                            this.order = res.data; 
                            this.isEditingStatus = false;
                            this.calculateDuration();
                        })
                        .catch(err => { console.error(err.response.data.message); alert('Gagal: ' + (err.response.data.message || 'Terjadi kesalahan')); });
                }
            }
        }
    </script>
</x-app-layout>