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
            'bahan',
            'langkah',
            'fIlter',
            'feedbacks.users',
            'feedbacks.photos',
        ])->findOrFail($id);

        $resep->increment('views_count');

        $totalUlasan = $resep->feedbacks->count();
        $ratingAvg   = $totalUlasan > 0 ? round($resep->feedbacks->avg('rating'), 1) : 0;

        // Breakdown rating per bintang (1–5)
        $ratingBreakdown = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = $resep->feedbacks->where('rating', $i)->count();
            $ratingBreakdown[$i] = [
                'count'   => $count,
                'percent' => $totalUlasan > 0 ? round(($count / $totalUlasan) * 100) : 0,
            ];
        }

        $isFavorited = Auth::check()
            ? $resep->favoritedBy()->where('user_id', Auth::id())->exists()
            : false;

        $sudahUlasan = false;
        $myFeedback  = null;
        $isFollowing = false;

        if (Auth::check()) {
            $myFeedback  = $resep->feedbacks->where('user_id', Auth::id())->first();
            $sudahUlasan = $myFeedback !== null;
            // Cek apakah sudah follow author resep ini
            $isFollowing = \App\Models\UserFollow::where('user_id', Auth::id())
                ->where('to_user_id', $resep->user_id)
                ->exists();
        }

        return view('pages.detail_resep.detail_resep', compact(
            'resep', 'totaLULasan', 'ratingavg',
            'ratingBreakdown', 'isFavorited', 'sudahUlasan', 'myFeedback', 'isFollowing'
        ));
    }
}