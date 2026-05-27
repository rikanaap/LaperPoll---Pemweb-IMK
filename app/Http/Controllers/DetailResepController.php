<?php

namespace App\Http\Controllers;

use App\Models\Resep;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DetailResepController extends Controller
{
    public function showDetail($id)
    {
        $resep = Resep::with([
            'user',
            'bahans',
            'langkahs',
            'filters',
            'feedbacks.user',
            'feedbacks.photos',
        ])->findOrFail($id);

        // Increment views
        $resep->increment('views_count');

        // Hitung rating breakdown
        $totalUlasan  = $resep->feedbacks->count();
        $ratingAvg    = $totalUlasan > 0 ? round($resep->feedbacks->avg('rating'), 1) : 0;

        // Cek apakah user sudah favoritkan
        $isFavorited = Auth::check()
            ? $resep->favoritedBy()->where('user_id', Auth::id())->exists()
            : false;

        // Cek apakah user sudah pernah kasih ulasan
        $sudahUlasan = Auth::check()
            ? $resep->feedbacks()->where('user_id', Auth::id())->exists()
            : false;

        return view('pages.detail_resep.detail_resep', compact(
            'resep',
            'totalUlasan',
            'ratingAvg',
            'isFavorited',
            'sudahUlasan',
        ));
    }
}