@extends('layouts.app')

@section('title', 'Daftar - LaperPoll')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/auth.css') }}">
<link rel="stylesheet" href="{{ asset('css/medias/auth.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush

@section('content')
<div class="auth-section flex flex-col">
    <a href="{{ route('landing.index') }}">
        <img src="{{ asset('assets/images/Logo_Laperpoll.png') }}" alt="Logo Laperpoll" class="logo">
    </a>

    <!-- SIGNUP FORM -->
    <form method="POST" action="{{ route('auth.register') }}" id="signupFormEl" novalidate>
        @csrf
        <div id="signupForm" class="form flex flex-col">

            <div class="auth-text flex flex-col">
                <h1 class="font-jakarta text-h4 font-bold">Sign Up</h1>
                <div class="auth-link gap-1 flex flex-row">
                    <p class="font-jakarta text-title2 font-regular">Sudah punya akun?</p>
                    <a href="{{ route('auth.sign-in') }}" class="font-jakarta text-title2 font-bold">Masuk disini</a>
                </div>
            </div>

            {{-- Server-side error messages --}}
            @if($errors->any())
            <div class="auth-errors" id="serverErrors">
                @foreach($errors->all() as $error)
                <p class="auth-error-item">
                    <span class="material-icons-round">error_outline</span>
                    {{ $error }}
                </p>
                @endforeach
            </div>
            @endif

            {{-- Client-side realtime error container --}}
            <div class="auth-errors auth-errors-client" id="clientErrors" style="display:none;"></div>

            <div class="form-inputs flex flex-col">

                <div class="input @error('name') input-error @enderror">
                    <span class="material-icons-round">person</span>
                    <div class="vertical-line"></div>
                    <input class="input-data text-body font-jakarta font-semibold"
                        type="text" name="name"
                        value="{{ old('name') }}"
                        oninvalid="this.setCustomValidity('Maaf, mohon isi dengan nama anda')"
                        oninput="this.setCustomValidity('')"
                        required placeholder="Nama Lengkap">
                </div>
                <div class="input @error('email') input-error @enderror">
                    <span class="material-icons-round">mail</span>
                    <div class="vertical-line"></div>
                    <input class="input-data text-body font-jakarta font-semibold"
                        type="email" name="email"
                        value="{{ old('email') }}"
                        oninvalid="this.setCustomValidity('Maaf, mohon isi dengan email anda')"
                        oninput="this.setCustomValidity('')"
                        required placeholder="mail@gmail.com">
                </div>
                <div class="input @error('password') input-error @enderror">
                    <span class="material-icons-round">lock</span>
                    <div class="vertical-line"></div>
                    <input class="input-data text-body font-jakarta font-semibold"
                        type="password" required name="password"
                        placeholder="Password">
                    <span class="material-icons-round" onclick="togglePassword(this)">remove_red_eye</span>
                </div>
                <div class="input">
                    <span class="material-icons-round">lock</span>
                    <div class="vertical-line"></div>
                    <input class="input-data text-body font-jakarta font-semibold"
                        type="password" required name="password_confirmation"
                        placeholder="Konfirmasi Password">
                    <span class="material-icons-round" onclick="togglePassword(this)">remove_red_eye</span>
                </div>
            </div>

            <button class="input-submit" type="submit">
                <h1 class="font-jakarta">Daftar</h1>
            </button>

            <div class="divider flex flex-row">
                <div class="horizontal-line"></div>
                <h2 class="font-jakarta text-body font-semibold text-black">Atau</h2>
                <div class="horizontal-line"></div>
            </div>

            <a href="{{ route('auth.google.redirect') }}" class="google-login">
                <svg class="google-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z" />
                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z" />
                    <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z" />
                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z" />
                </svg>
                <span class="google-login-text font-jakarta">Continue with Google</span>
            </a>
        </div>
    </form>
    <!-- SIGNUP FORM END -->
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/global.js') }}"></script>
<script src="{{ asset('js/pages/auth.js') }}"></script>
@endpush