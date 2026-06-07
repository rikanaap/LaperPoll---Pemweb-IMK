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
                    <a class="font-jakarta text-body font-regular text-secondary-normal" href="{{ route('auth.forgot-pass') }}">Lupa password?</a>
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
            <div class="input-submit google-login">
                <i class="bi bi-google"></i>
                <h1 class="font-jakarta text-title2 font-regular text-secondary-normal">Continue with Google</h1>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/global.js') }}"></script>
<script src="{{ asset('js/pages/auth.js') }}"></script>
@endpush