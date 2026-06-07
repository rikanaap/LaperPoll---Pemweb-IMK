<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $resepCount     = $user->reseps()->count();
        $followerCount  = $user->followers()->count();
        $followingCount = $user->following()->count();
        $favoritCount   = $user->favorites()->count();

        $resepUser = $user->reseps()
            ->with(['feedbacks', 'langkahs'])
            ->latest()
            ->take(12)
            ->get();

        return view('pages.profile.index', compact(
            'user', 'resepCount', 'followerCount',
            'followingCount', 'favoritCount', 'resepUser'
        ));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('pages.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email,' . $user->id,
            'profile_photo'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'password'         => 'nullable|min:6|confirmed',
            'bio'              => 'nullable|string|max:500',
        ];

        $messages = [
            'name.required'          => 'Nama wajib diisi.',
            'email.required'         => 'Email wajib diisi.',
            'email.unique'           => 'Email sudah dipakai akun lain.',
            'password.min'           => 'Password minimal 6 karakter.',
            'password.confirmed'     => 'Konfirmasi password tidak cocok.',
            'profile_photo.image'    => 'File harus berupa gambar.',
            'profile_photo.max'      => 'Ukuran foto maksimal 2 MB.',
        ];

        // Jika user ingin ganti password, wajib isi current_password
        if ($request->filled('password')) {
            $rules['current_password'] = 'required';
            $messages['current_password.required'] = 'Password lama wajib diisi untuk mengganti password.';
        }

        $request->validate($rules, $messages);

        // Verifikasi password lama
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()
                    ->withErrors(['current_password' => 'Password lama tidak sesuai.'])
                    ->withInput($request->except(['password', 'password_confirmation', 'current_password']));
            }
        }

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile_photo')) {
            // Hapus foto lama
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $data['profile_photo'] = $request->file('profile_photo')
                ->store('profile_photos', 'public');
        }

        // Fix: pakai $user->update() supaya Auth instance ter-refresh
        $user->update($data);

        // Refresh Auth session supaya navbar langsung update tanpa login ulang
        Auth::setUser($user->fresh());

        return redirect()->route('profile.index')
            ->with('success', 'Profil berhasil diperbarui!');
    }
}