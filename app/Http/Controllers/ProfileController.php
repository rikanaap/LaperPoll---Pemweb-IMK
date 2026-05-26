<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil user yang sedang login.
     */
    public function index()
    {
        $user = Auth::user();

        $resepCount     = $user->reseps()->count();
        $followerCount  = $user->followers()->count();
        $followingCount = $user->following()->count();
        $favoritCount   = $user->favorites()->count();

        // Ambil resep milik user (maksimal 12 untuk grid)
        $resepUser = $user->reseps()
            ->with(['feedbacks', 'langkahs'])
            ->latest()
            ->take(12)
            ->get();

        return view('pages.profile.index', compact(
            'user',
            'resepCount',
            'followerCount',
            'followingCount',
            'favoritCount',
            'resepUser'
        ));
    }

    /**
     * Tampilkan form edit profil.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('pages.profile.edit', compact('user'));
    }

    /**
     * Simpan perubahan profil.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email,' . $user->id,
            'password'          => 'nullable|min:6|confirmed',
            'profile_photo'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'name.required'         => 'Nama wajib diisi.',
            'email.required'        => 'Email wajib diisi.',
            'email.unique'          => 'Email sudah dipakai akun lain.',
            'password.min'          => 'Password minimal 6 karakter.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok.',
            'profile_photo.image'   => 'File harus berupa gambar.',
            'profile_photo.max'     => 'Ukuran foto maksimal 2 MB.',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile_photo')) {
            // Hapus foto lama jika ada
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $data['profile_photo'] = $path;
        }

        User::where('id', $user->id)->update($data);

        return redirect()->route('profile.index')
            ->with('success', 'Profil berhasil diperbarui!');
    }
}