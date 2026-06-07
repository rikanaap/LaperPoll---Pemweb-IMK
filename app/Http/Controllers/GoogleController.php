<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            session()->flash('toast', 'Login Google gagal. Silakan coba lagi.');
            session()->flash('toast_type', 'error');
            return redirect()->route('auth.sign-in');
        }

        try {
            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name'              => $googleUser->getName(),
                    'google_id'         => $googleUser->getId(),
                    'avatar'            => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                    'password'          => null,
                ]
            );
        } catch (\Exception $e) {
            session()->flash('toast', 'Gagal menyimpan data akun.');
            session()->flash('toast_type', 'error');
            return redirect()->route('auth.sign-in');
        }

        // Pakai Auth::login() langsung, bukan Auth::attempt()
        Auth::login($user, remember: true);
        $request->session()->regenerate();

        session()->flash('toast', 'Selamat datang, ' . $user->name . '! 👋');
        session()->flash('toast_type', 'success');

        if ($user->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('landing.index');
    }
}
