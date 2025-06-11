<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class RoomTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $roomTypes = RoomType::orderBy('name', 'asc')->paginate(10);
        return view('admin.room-types.index', compact('roomTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Karena form kita sederhana (hanya nama tipe kamar),
        // kita tidak perlu mengirim data tambahan ke view ini.
        return view('admin.room-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi data yang masuk dari formulir
        $validatedData = $request->validate([
            'name' => 'required|string|max:100|unique:room_types,name', // 'name' harus unik di tabel 'room_types'
        ]);

        // 2. Buat dan simpan tipe kamar baru ke database
        // Karena kita sudah menyederhanakan model dan tabel hanya untuk 'name',
        // $validatedData sudah cukup.
        RoomType::create($validatedData);

        // 3. Redirect kembali ke halaman daftar tipe kamar dengan pesan sukses
        return redirect()->route('admin.roomtypes.index')->with('success', 'Tipe kamar "' . $validatedData['name'] . '" berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(RoomType $roomType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RoomType $roomType): View // Parameter $roomType akan otomatis di-inject
    {
        // Kirim data tipe kamar yang akan diedit ke view
        return view('admin.room-types.edit', compact('roomType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RoomType $roomType): RedirectResponse
    {
        // 1. Validasi data yang masuk dari formulir edit
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('room_types', 'name')->ignore($roomType->id)
                // 'name' harus unik di tabel 'room_types', kecuali untuk record ini sendiri.
                // Karena primary key di tabel room_types adalah 'id', kita gunakan $roomType->id
            ],
        ]);

        // 2. Update atribut tipe kamar dengan data yang sudah divalidasi
        $roomType->update($validatedData);

        // 3. Redirect kembali ke halaman daftar tipe kamar dengan pesan sukses
        return redirect()->route('admin.roomtypes.index')->with('success', 'Tipe kamar "' . $roomType->name . '" berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RoomType $roomType): RedirectResponse
    {
        try {
            // Simpan nama tipe kamar sebelum dihapus untuk pesan sukses
            $deletedTypeName = $roomType->name;

            // Lakukan operasi penghapusan
            $roomType->delete();

            return redirect()->route('admin.roomtypes.index')
                             ->with('success', 'Tipe kamar "' . $deletedTypeName . '" berhasil dihapus.');

        } catch (QueryException $e) {
            // Tangani error jika tipe kamar tidak bisa dihapus karena terkait dengan data lain (foreign key constraint)
            // Kode error SQLSTATE[23000] biasanya untuk integrity constraint violation,
            // dan errorInfo[1] == 1451 adalah kode spesifik MySQL untuk foreign key constraint failure.
            if ($e->errorInfo[1] == 1451) { 
                return redirect()->route('admin.roomtypes.index')
                                 ->with('error', 'Tipe kamar "' . $roomType->name . '" tidak dapat dihapus karena masih digunakan oleh data kamar.');
            } else {
                // Untuk error database lainnya yang mungkin terjadi
                return redirect()->route('admin.roomtypes.index')
                                 ->with('error', 'Gagal menghapus tipe kamar. Terjadi kesalahan database: ' . $e->getMessage());
            }
        }
    }
}
