<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resep;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    // Toggle favorit
    public function toggle($id)
    {
        $user = Auth::user();
        Resep::where('is_published', true)->findOrFail($id);

        // toggle() return array ['attached'=>[], 'detached'=>[]]
        $result     = $user->favorites()->toggle($id);
        $isFavorite = count($result['attached']) > 0;

        return response()->json([
            'status'     => 'success',
            'isFavorite' => $isFavorite,
        ]);
    }

    // Halaman favorit — urut berdasarkan pivot created_at (kapan difavoritkan)
    public function index()
    {
        $favorites = Auth::user()
            ->favorites()
            ->with('user') // eager load author
            ->withPivot('created_at')
            ->where('is_published', true)
            ->orderBy('favorites.created_at', 'desc')
            ->get();

        return view('pages.favorit.index', compact('favorites'));
    }
}