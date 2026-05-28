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

    <!-- SIGNUP FORM DESIGN START -->
    <form method="post" action="{{ route('auth.register') }}">
        <div id="signupForm" class="form flex flex-col">
            <div class="auth-text flex flex-col">
                <h1 class="font-jakarta text-h4 font-bold">Sign Up</h1>
                <div class="auth-link gap-1 flex flex-row">
                    <p class="font-jakarta text-title2 font-regular">Sudah punya akun?</p>
                    <a href="/auth/signin" class="font-jakarta text-title2 font-bold">Masuk
                        disini</a>
                </div>
            </div>
            <div class="form-inputs flex flex-col">
                <div class="input">
                    <span class="material-icons-round">person</span>
                    <div class="vertical-line"></div>
                    <input class="input-data text-body font-jakarta font-semibold" type="text" name="name"
                        oninvalid="this.setCustomValidity('Maaf, mohon isi dengan nama anda')"
                        oninput="this.setCustomValidity('')" required placeholder="Nama Lengkap">
                </div>
                <div class="input">
                    <span class="material-icons-round">mail</span>
                    <div class="vertical-line"></div>
                    <input class="input-data text-body font-jakarta font-semibold" type="email" name="email"
                        oninvalid="this.setCustomValidity('Maaf, mohon isi dengan email anda')"
                        oninput="this.setCustomValidity('')" required placeholder="mail@gmail.com">
                </div>
                <div class="input">
                    <span class="material-icons-round">lock</span>
                    <div class="vertical-line"></div>
                    <input class="input-data text-body font-jakarta font-semibold" type="password" required name="password"
                        placeholder="Password">
                    <span class="material-icons-round" onclick="togglePassword(this)">remove_red_eye</span>
                </div>
                <div class="input">
                    <span class="material-icons-round">lock</span>
                    <div class="vertical-line"></div>
                    <input class="input-data text-body font-jakarta font-semibold" type="password" required name="password_confirmation"
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
            <div class="input-submit google-login">
                <i class="bi bi-google"></i>
                <h1 class="font-jakarta text-title2 font-regular text-secondary-normal">Continue with Google</h1>
            </div>
        </div>
    </form>
    <!-- SIGNUP FORM DESIGN END -->
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/global.js') }}"></script>
<script src="{{ asset('js/pages/auth.js') }}"></script>
@endpush