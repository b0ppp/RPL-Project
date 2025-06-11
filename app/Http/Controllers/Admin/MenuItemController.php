<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse; // Untuk tipe return redirect
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException; // Untuk menangani error database

class MenuItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $menuItems = MenuItem::orderBy('item_name', 'asc')->paginate(10); // Ambil semua item menu, urutkan, paginasi
        return view('admin.menu-items.index', compact('menuItems'));
    }
    /**
     * Show the form for creating a new resource.
     */
   public function create(): View // Method untuk menampilkan form tambah
    {
        // Tidak ada data spesifik yang perlu dikirim ke view untuk form tambah menu item sederhana ini
        // kecuali jika Anda memiliki kategori atau hal lain yang perlu dipilih.
        return view('admin.menu-items.create');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi data yang masuk dari formulir
        $validatedData = $request->validate([
            'item_name' => 'required|string|max:100|unique:menu_items,item_name', // item_name harus unik di tabel menu_items
            'availability_status' => 'required|string|in:Tersedia,Tidak Tersedia',
        ]);

        // 2. Tambahkan created_by_user_id dengan ID pengguna yang sedang login (Admin)
        $dataToStore = $validatedData;
        $dataToStore['created_by_user_id'] = Auth::id();

        // 3. Buat dan simpan item menu baru ke database
        MenuItem::create($dataToStore);

        // 4. Redirect kembali ke halaman daftar item menu dengan pesan sukses
        return redirect()->route('admin.menuitems.index')->with('success', 'Item menu "' . $validatedData['item_name'] . '" berhasil ditambahkan!');
    }


    /**
     * Display the specified resource.
     */
    public function show(MenuItem $menuItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MenuItem $menuItem): View // Parameter $menuItem akan otomatis di-inject
    {
        // Kirim data item menu yang akan diedit ke view
        return view('admin.menu-items.edit', compact('menuItem'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MenuItem $menuItem): RedirectResponse
    {
        // 1. Validasi data yang masuk dari formulir edit
        $validatedData = $request->validate([
            'item_name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('menu_items', 'item_name')->ignore($menuItem->menu_item_id, 'menu_item_id')
                // item_name harus unik, kecuali untuk record item menu ini sendiri (saat tidak diubah namanya)
                // 'menu_item_id' kedua adalah nama primary key di tabel menu_items
            ],
            'availability_status' => ['required', 'string', Rule::in(['Tersedia', 'Tidak Tersedia'])],
        ]);

        // 2. Update atribut item menu dengan data yang sudah divalidasi
        // Jika Anda ingin mencatat siapa yang terakhir mengupdate, Anda bisa menambahkan logika untuk
        // kolom 'updated_by_user_id' jika ada, misalnya:
        // $validatedData['updated_by_user_id'] = Auth::id();
        // Untuk sekarang, kita hanya update nama dan status.

        $menuItem->update($validatedData);

        // 3. Redirect kembali ke halaman daftar item menu dengan pesan sukses
        return redirect()->route('admin.menuitems.index')->with('success', 'Item menu "' . $menuItem->item_name . '" berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
     public function destroy(MenuItem $menuItem): RedirectResponse
    {
        try {
            // Simpan nama item sebelum dihapus untuk pesan sukses
            $deletedItemName = $menuItem->item_name;

            $menuItem->delete();

            return redirect()->route('admin.menuitems.index')
                             ->with('success', 'Item menu "' . $deletedItemName . '" berhasil dihapus.');

        } catch (QueryException $e) {
            // Tangani error jika item menu tidak bisa dihapus karena terkait dengan data lain (foreign key constraint)
            // Kode error SQLSTATE[23000] biasanya untuk integrity constraint violation
            if ($e->errorInfo[1] == 1451) { // Kode error MySQL spesifik untuk foreign key constraint failure
                return redirect()->route('admin.menuitems.index')
                                 ->with('error', 'Item menu "' . $menuItem->item_name . '" tidak dapat dihapus karena sudah digunakan dalam data pesanan.');
            } else {
                // Untuk error database lainnya
                return redirect()->route('admin.menuitems.index')
                                 ->with('error', 'Gagal menghapus item menu. Terjadi kesalahan database.');
            }
        }
    }
}
