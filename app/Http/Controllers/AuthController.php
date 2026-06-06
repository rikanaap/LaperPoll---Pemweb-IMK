<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\User;
use App\Models\UserToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Str;

class AuthController extends Controller
{
    public function signIn()
    {
        return view('pages.auth.signin');
    }

    public function signUp()
    {
        return view('pages.auth.signup');
    }

    public function forgotPass()
    {
        return view('pages.auth.forgotpass');
    }
    public function showOtp()
    {
        if (!session('fp_email')) {
            return redirect()->route('auth.forgot-pass');
        }
        return view('pages.auth.forgototp');
    }

    public function showReset()
    {
        if (!session('fp_email') || !session('fp_verified')) {
            return redirect()->route('auth.forgot-otp');
        }

        return view('pages.auth.resetpass');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ], [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.unique'       => 'Email sudah terdaftar.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->route('landing.index');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            session()->flash('toast', 'Selamat datang, ' . Auth::user()->name . '! 👋');
            session()->flash('toast_type', 'success');

            if (Auth::user()->is_admin) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('landing.index');
        }

        session()->flash('toast', 'Email atau password salah.');
        session()->flash('toast_type', 'error');

        return back()->withErrors([
            'login' => 'Email atau password salah.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        // ✅ Simpan dulu sebelum session di-invalidate
        $isAdmin = Auth::user()?->is_admin;

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($isAdmin) {
            return redirect()->route('auth.sign-in');
        }

        return redirect()->route('landing.index');
    }

    public function sendOtp(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ], [
                'email.required' => 'Email wajib diisi.',
                'email.email'    => 'Format email tidak valid.',
                'email.exists'   => 'Email tidak terdaftar.',
            ]);

            $user = User::where('email', $request->email)->first();

            // Hapus OTP lama milik user ini (jika ada)
            UserToken::where('user_id', $user->id)
                ->where('token_code', 'FP')
                ->delete();

            // Generate OTP 6 digit
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Simpan ke user_tokens (fresh create setelah delete)
            UserToken::create([
                'identifier_id' => \Illuminate\Support\Str::uuid(),
                'token_code'    => 'FP',
                'payload'       => $otp,
                'user_id'       => $user->id,
                'expired_date'  => now()->addMinutes(10),
            ]);

            // Kirim email
            Mail::to($user->email)->send(new OtpMail($user, $otp));

            // Simpan email di session untuk step berikutnya
            session(['fp_email' => $user->email]);

            session()->flash('toast', 'Kode OTP telah dikirim ke ' . $user->email);
            session()->flash('toast_type', 'success');

            return redirect()->route('auth.forgot-otp');
        } catch (\Throwable $th) {
            session()->flash('toast', $th->getMessage());
            session()->flash('toast_type', 'error');
            throw $th;
        }
    }

    // ─── STEP 2: Verifikasi OTP ──────────────────────────────────────────────
    public function verifyOtp(Request $request)
    {
        if (!session('fp_email')) {
            return redirect()->route('auth.forgot-pass');
        }

        try {
            $request->validate([
                'token' => 'required|array|size:6',
                'token.*' => 'required|string|size:1',
            ], [
                'token.required' => 'Kode OTP wajib diisi.',
                'token.size'     => 'Kode OTP harus 6 digit.',
            ]);

            // Gabungkan array token[] jadi string "123456"
            $otp  = implode('', $request->token);
            $user = User::where('email', session('fp_email'))->first();

            $token = UserToken::where('user_id', $user->id)
                ->where('token_code', 'FP')
                ->where('payload', $otp)
                ->where('expired_date', '>=', now())
                ->first();


            if (!$token) {
                session()->flash('toast', 'Kode OTP salah atau sudah kadaluarsa.');
                session()->flash('toast_type', 'error');
                return redirect()->route('auth.forgot-otp');
            }

            session(['fp_verified' => true]);

            session()->flash('toast', 'OTP valid! Buat password baru.');
            session()->flash('toast_type', 'success');

            return redirect()->route('auth.reset-pass');
        } catch (\Throwable $th) {
            session()->flash('toast', $th->getMessage());
            session()->flash('toast_type', 'error');
            throw $th;
        }
    }

    // ─── STEP 3: Simpan password baru ────────────────────────────────────────
    public function resetPassword(Request $request)
    {
        if (!session('fp_email') || !session('fp_verified')) {
            return redirect()->route('forgot.email');
        }

        try {
            $request->validate([
                'password' => 'required|min:6|confirmed',
            ], [
                'password.required'  => 'Password baru wajib diisi.',
                'password.min'       => 'Password minimal 6 karakter.',
                'password.confirmed' => 'Konfirmasi password tidak cocok.',
            ]);

            $user = User::where('email', session('fp_email'))->first();

            $user->update(['password' => Hash::make($request->password)]);

            // Hapus OTP & bersihkan session
            UserToken::where('user_id', $user->id)->where('token_code', 'FP')->delete();
            session()->forget(['fp_email', 'fp_verified']);

            session()->flash('toast', 'Password berhasil diubah! Silakan login.');
            session()->flash('toast_type', 'success');

            return redirect()->route('auth.sign-in');
        } catch (\Throwable $th) {
            session()->flash('toast', $th->getMessage());
            session()->flash('toast_type', 'error');
            throw $th;
        }
    }
}