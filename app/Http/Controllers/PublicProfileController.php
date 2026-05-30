<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserFollow;
use Illuminate\Support\Facades\Auth;

class PublicProfileController extends Controller
{
    public function show($userId)
    {
        // Kalau lihat profil sendiri → redirect ke profile sendiri
        if (Auth::check() && Auth::id() == $userId) {
            return redirect()->route('profile.index');
        }

        $user = User::findOrFail($userId);

        $resepCount     = $user->reseps()->count();
        $followerCount  = $user->followers()->count();
        $followingCount = $user->following()->count();

        $resepUser = $user->reseps()
            ->with(['feedbacks'])
            ->where('is_published', true)
            ->latest()
            ->take(12)
            ->get();

        $isFollowing = Auth::check()
            ? UserFollow::where('user_id', Auth::id())
                ->where('to_user_id', $userId)
                ->exists()
            : false;

        return view('pages.profile.public', compact(
            'user', 'resepCount', 'followerCount',
            'followingCount', 'resepUser', 'isFollowing'
        ));
    }
}