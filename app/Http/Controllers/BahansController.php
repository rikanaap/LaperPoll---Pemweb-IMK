<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use Illuminate\Http\Request;

/**
 * BahansController — mengelola data master bahan (daftar bahan yang tersedia di sistem).
 *
 * INI BUKAN controller untuk "tambah bahan ke kulkas" (itu ada di KulkasDigitalController).
 * Controller ini digunakan untuk CRUD data Bahan di tabel `bahans` (master data).
 *
 * Contoh penggunaan:
 *   - Admin menambah bahan baru ke daftar master (misal: "Tempe", "Tahu", dll.)
 *   - Admin mengedit atau menghapus bahan dari master list
 *   - API endpoint untuk autocomplete pencarian bahan
 */
class BahansController extends Controller
{
    /**
     * Tampilkan daftar semua bahan master.
     */
    public function index()
    {
        $bahans = Bahan::orderBy('nama')->get();
        return view('pages.admin.bahans.index', compact('bahans'));
    }

    /**
     * Form tambah bahan master baru.
     */
    public function create()
    {
        return view('pages.admin.bahans.create');
    }

    /**
     * Simpan bahan master baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'                   => 'required|string|max:100|unique:bahans,nama',
            'expired_expectancy_day' => 'nullable|integer|min:1',
        ]);

        Bahan::create([
            'nama'                   => $request->nama,
            'expired_expectancy_day' => $request->expired_expectancy_day,
        ]);

        return redirect()->route('admin.bahans.index')
            ->with('success', 'Bahan berhasil ditambahkan ke master data.');
    }

    /**
     * Form edit bahan master.
     */
    public function edit(Bahan $bahan)
    {
        return view('pages.admin.bahans.edit', compact('bahan'));
    }

    /**
     * Update data bahan master.
     */
    public function update(Request $request, Bahan $bahan)
    {
        $request->validate([
            'nama'                   => 'required|string|max:100|unique:bahans,nama,' . $bahan->id,
            'expired_expectancy_day' => 'nullable|integer|min:1',
        ]);

        $bahan->update([
            'nama'                   => $request->nama,
            'expired_expectancy_day' => $request->expired_expectancy_day,
        ]);

        return redirect()->route('admin.bahans.index')
            ->with('success', 'Bahan berhasil diperbarui.');
    }

    /**
     * Hapus bahan master.
     */
    public function destroy(Bahan $bahan)
    {
        $bahan->delete();

        return redirect()->route('admin.bahans.index')
            ->with('success', 'Bahan berhasil dihapus dari master data.');
    }

    /**
     * API endpoint — kembalikan daftar bahan dalam format JSON untuk autocomplete.
     * Digunakan oleh JS di halaman tambah bahan kulkas.
     */
    public function apiList()
    {
        $bahans = Bahan::orderBy('nama')
            ->get(['id', 'nama', 'expired_expectancy_day'])
            ->map(fn($b) => [
                'id'                     => $b->id,
                'nama'                   => $b->nama,
                'has_expiry'             => $b->expired_expectancy_day !== null,
                'expired_expectancy_day' => $b->expired_expectancy_day,
            ]);

        return response()->json($bahans);
    }
}