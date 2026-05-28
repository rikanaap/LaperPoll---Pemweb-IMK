<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use Illuminate\Http\Request;

class BahansController extends Controller
{
    public function index()
    {
        $bahans = Bahan::orderBy('nama')->get();
        return view('admin.bahans.index', compact('bahans'));
    }

    public function create()
    {
        return view('admin.bahans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'                   => 'required|string|max:100|unique:bahans,nama',
            'expired_expectancy_day' => 'nullable|integer|min:1',
        ]);

        Bahan::create($request->only('nama', 'expired_expectancy_day'));

        return redirect()->route('admin.bahans.index')
            ->with('success', 'Bahan berhasil ditambahkan.');
    }

    public function edit(Bahan $bahan)
    {
        return view('admin.bahans.edit', compact('bahan'));
    }

    public function update(Request $request, Bahan $bahan)
    {
        $request->validate([
            'nama'                   => 'required|string|max:100|unique:bahans,nama,' . $bahan->id,
            'expired_expectancy_day' => 'nullable|integer|min:1',
        ]);

        $bahan->update($request->only('nama', 'expired_expectancy_day'));

        return redirect()->route('admin.bahans.index')
            ->with('success', 'Bahan berhasil diupdate.');
    }

    public function destroy(Bahan $bahan)
    {
        $bahan->delete();
        return redirect()->route('admin.bahans.index')
            ->with('success', 'Bahan berhasil dihapus.');
    }

    // API — dipakai tambah-bahan.js untuk autocomplete search bahan
    public function apiList(Request $request)
    {
        $q = $request->query('q', '');

        $bahans = Bahan::when($q, fn($query) =>
                $query->where('nama', 'like', "%$q%")
            )
            ->orderBy('nama')
            ->get(['id', 'nama', 'expired_expectancy_day']);

        return response()->json($bahans);
    }
}