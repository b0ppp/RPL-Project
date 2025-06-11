<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use App\Models\RoomType; // Pastikan ini ada

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Ambil semua data kamar, urutkan berdasarkan nomor kamar, dan gunakan paginasi
        $rooms = Room::orderBy('room_number', 'asc')->paginate(10); // Tampilkan 10 kamar per halaman

        // Kirim data kamar ke view
        return view('admin.rooms.index', compact('rooms'));
    }


    /**
     * Show the form for creating a new resource.
     */
     public function create(): View
    {
        // Ambil semua tipe kamar dari database untuk pilihan dropdown
        $roomTypes = RoomType::orderBy('name', 'asc')->get(); 

        // Definisikan pilihan untuk status kamar (tetap sama)
        $statuses = ['Kosong', 'Terisi', 'Dalam Perbaikan'];

        return view('admin.rooms.create', compact('roomTypes', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Hapus definisi $allowedRoomTypes karena kita validasi berdasarkan tabel room_types
        // $allowedRoomTypes = ['Standard', 'Deluxe', 'Suite', 'Family', 'Superior']; 
        $allowedStatuses = ['Kosong', 'Terisi', 'Dalam Perbaikan']; // Ini tetap

        $validatedData = $request->validate([
            'room_number' => 'required|string|max:20|unique:rooms,room_number',
            // Validasi untuk room_type_id
            'room_type_id' => ['required', 'integer', 'exists:room_types,id'], // Pastikan room_type_id ada di tabel room_types
            'status' => ['required', 'string', Rule::in($allowedStatuses)],
        ]);

        // Data yang divalidasi sudah berisi room_type_id, room_number, status
        Room::create($validatedData);

        return redirect()->route('admin.rooms.index')->with('success', 'Kamar "' . $validatedData['room_number'] . '" berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room): View
    {
        // Ambil semua tipe kamar dari database untuk pilihan dropdown
        $roomTypes = RoomType::orderBy('name', 'asc')->get();

        // Definisikan pilihan untuk status kamar (tetap sama)
        $statuses = ['Kosong', 'Terisi', 'Dalam Perbaikan'];

        // Kirim data kamar yang akan diedit, daftar tipe kamar, dan daftar status ke view
        return view('admin.rooms.edit', compact('room', 'roomTypes', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room): RedirectResponse
    {
        // Hapus definisi $allowedRoomTypes karena kita validasi berdasarkan tabel room_types
        $allowedStatuses = ['Kosong', 'Terisi', 'Dalam Perbaikan']; // Ini tetap

        $validatedData = $request->validate([
            'room_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('rooms', 'room_number')->ignore($room->room_id, 'room_id')
            ],
            // Validasi untuk room_type_id
            'room_type_id' => ['required', 'integer', 'exists:room_types,id'], // Pastikan room_type_id ada di tabel room_types
            'status' => ['required', 'string', Rule::in($allowedStatuses)],
        ]);

        // Data yang divalidasi sudah berisi room_type_id, room_number, status
        $room->update($validatedData);

        return redirect()->route('admin.rooms.index')->with('success', 'Data kamar "' . $room->room_number . '" berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room): RedirectResponse
    {
        try {
            // Simpan nomor kamar sebelum dihapus untuk pesan sukses
            $deletedRoomNumber = $room->room_number;

            // Lakukan operasi penghapusan
            $room->delete();

            return redirect()->route('admin.rooms.index')
                             ->with('success', 'Kamar "' . $deletedRoomNumber . '" berhasil dihapus.');

        } catch (QueryException $e) {
            // Tangani error jika kamar tidak bisa dihapus karena terkait dengan data lain (foreign key constraint)
            // Kode error SQLSTATE[23000] biasanya untuk integrity constraint violation,
            // dan errorInfo[1] == 1451 adalah kode spesifik MySQL untuk foreign key constraint failure.
            if ($e->errorInfo[1] == 1451) { 
                return redirect()->route('admin.rooms.index')
                                 ->with('error', 'Kamar "' . $room->room_number . '" tidak dapat dihapus karena masih terkait dengan data pesanan yang ada.');
            } else {
                // Untuk error database lainnya yang mungkin terjadi
                return redirect()->route('admin.rooms.index')
                                 ->with('error', 'Gagal menghapus kamar. Terjadi kesalahan database: ' . $e->getMessage());
            }
        }
    }
}
