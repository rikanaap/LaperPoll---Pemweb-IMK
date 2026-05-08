<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resep;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    // Menambah atau menghapus favorit (Toggle)
    public function toggle($id)
    {
        $user = Auth::user();
        
        // Cek apakah resep ada
        $resep = Resep::findOrFail($id);
        
        // Logika toggle otomatis (jika ada dihapus, jika tidak ada ditambah)
        $user->favorites()->toggle($id);

        $isFavorite = $user->favorites()->where('resep_id', $id)->exists();

        return response()->json([
            'status' => 'success',
            'isFavorite' => $isFavorite
        ]);
    }

    // Menampilkan halaman favorit
    public function index()
    {
        $favorites = Auth::user()->favorites()->latest()->get();
        return view('pages.favorit.index', compact('favorites'));
    }
}