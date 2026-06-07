@extends('layouts.app')
@section('title', 'LaperPoll')
@push('styles')

@endpush

@push('links')
<link rel="stylesheet" href="{{ asset('css/pages/auth.css') }}">
<link rel="stylesheet" href="{{ asset('css/medias/auth.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush

@section('content')
<div class="auth-section flex flex-col">
    <img src="{{ asset('assets/images/Logo_Laperpoll.png') }}" alt="Logo Laperpoll" class="logo">

    <form method="post" action="{{ route('auth.login') }}">
        @csrf
        <div class="form flex flex-col">
            @if ($errors->any())
                <div class="auth-error-box">
                    @foreach ($errors->all() as $error)
                        <p class="auth-error-msg font-jakarta text-body">{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            <div class="auth-text flex flex-col">
                <h1 class="font-jakarta text-h4 font-bold">Sign In</h1>
                <div class="auth-link gap-1 flex flex-row">
                    <p class="font-jakarta text-title2 font-regular">Belum punya akun?</p>
                    <a href="{{ route('auth.sign-up') }}" class="font-jakarta text-title2 font-bold">Daftar
                        disini</a>
                </div>
            </div>
            <div class="form-inputs flex flex-col">
                <div class="input">
                    <span class="material-icons-round">mail</span>
                    <div class="vertical-line"></div>
                    <input class="input-data text-body font-jakarta font-semibold" type="email" name="email"
                        placeholder="mail@gmail.com" value="{{ old('email') }}" autocomplete="email">
                </div>
                <div class="wrap-forgot-pass flex flex-col">
                    <div class="input">
                        <span class="material-icons-round">lock</span>
                        <div class="vertical-line"></div>
                        <input class="input-data text-body font-jakarta font-semibold" type="password" name="password"
                            placeholder="Password">
                        <span class="material-icons-round" onclick="togglePassword(this)">remove_red_eye</span>
                    </div>
                    <a class="font-jakarta text-body font-regular text-secondary-normal" style="text-decoration: none;" href="{{ route('auth.forgot-pass') }}">Lupa password?</a>
                </div>
            </div>
            <button type="submit" class="input-submit">
                <h1 class="font-jakarta">Login</h1>
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
                    <path fill="none" d="M0 0h48v48H0z" />
                </svg>
                <span class="google-login-text font-jakarta">Continue with Google</span>
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/global.js') }}"></script>
<script src="{{ asset('js/pages/auth.js') }}"></script>
@endpush