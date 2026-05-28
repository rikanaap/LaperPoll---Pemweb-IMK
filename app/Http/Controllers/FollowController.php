<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserFollow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    // ── Daftar followers ─────────────────────────────────────────────────────
    public function followers($userId)
    {
        $user = User::findOrFail($userId);

        $followers = UserFollow::where('to_user_id', $userId)
            ->with('user')
            ->latest()
            ->get()
            ->map(fn($f) => [
                'id'            => $f->user->id,
                'name'          => $f->user->name,
                'profile_photo' => $f->user->profile_photo
                    ? \Storage::url($f->user->profile_photo)
                    : null,
                'is_following'  => Auth::check()
                    ? UserFollow::where('user_id', Auth::id())
                        ->where('to_user_id', $f->user->id)
                        ->exists()
                    : false,
            ]);

        return response()->json([
            'success' => true,
            'type'    => 'followers',
            'title'   => 'Pengikut',
            'count'   => $followers->count(),
            'users'   => $followers,
        ]);
    }

    // ── Daftar following ─────────────────────────────────────────────────────
    public function following($userId)
    {
        $user = User::findOrFail($userId);

        $following = UserFollow::where('user_id', $userId)
            ->with('target')
            ->latest()
            ->get()
            ->map(fn($f) => [
                'id'            => $f->target->id,
                'name'          => $f->target->name,
                'profile_photo' => $f->target->profile_photo
                    ? \Storage::url($f->target->profile_photo)
                    : null,
                'is_following'  => Auth::check()
                    ? UserFollow::where('user_id', Auth::id())
                        ->where('to_user_id', $f->target->id)
                        ->exists()
                    : false,
            ]);

        return response()->json([
            'success' => true,
            'type'    => 'following',
            'title'   => 'Mengikuti',
            'count'   => $following->count(),
            'users'   => $following,
        ]);
    }

    // ── Toggle follow/unfollow ────────────────────────────────────────────────
    public function toggle($userId)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        if (Auth::id() == $userId) {
            return response()->json(['success' => false, 'message' => 'Tidak bisa follow diri sendiri'], 400);
        }

        $existing = UserFollow::where('user_id', Auth::id())
            ->where('to_user_id', $userId)
            ->first();

        if ($existing) {
            $existing->delete();
            $isFollowing = false;
        } else {
            UserFollow::create([
                'user_id'    => Auth::id(),
                'to_user_id' => $userId,
            ]);
            $isFollowing = true;
        }

        $followerCount = UserFollow::where('to_user_id', $userId)->count();

        return response()->json([
            'success'        => true,
            'is_following'   => $isFollowing,
            'follower_count' => $followerCount,
        ]);
    }
}