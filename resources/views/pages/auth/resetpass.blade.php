@extends('layouts.app')
@section('title', 'Password Baru - LaperPoll')

@push('links')
<link rel="stylesheet" href="{{ asset('css/pages/auth.css') }}">
<link rel="stylesheet" href="{{ asset('css/medias/auth.css') }}">
@endpush

@php
$remaining = max(0, session('otp_expired_at', 0) - now()->timestamp);
@endphp

@section('content')
<div class="auth-section flex flex-col">
    <a href="{{ route('landing.index') }}">
        <img src="{{ asset('assets/images/Logo_Laperpoll.png') }}" alt="Logo Laperpoll" class="logo">
    </a>

    <form method="POST" action="{{ route('auth.reset-pass.post') }}">
        @csrf
        <div class="form flex flex-col">
            <div class="auth-text flex flex-col">
                <h1 class="font-jakarta text-h4 font-bold">Password Baru</h1>
                <div class="auth-link gap-1 flex flex-row">
                    <p class="font-jakarta text-title2 font-regular">Buat password yang kuat</p>
                </div>
            </div>

            @if ($errors->any())
            <p class="font-jakarta text-body font-semibold" style="color:red; margin-bottom:4px;">
                {{ $errors->first() }}
            </p>
            @endif

            <div class="form-inputs flex flex-col">
                <div class="input">
                    <span class="material-icons-round">lock</span>
                    <div class="vertical-line"></div>
                    <input class="input-data text-body font-jakarta font-semibold"
                        type="password" name="password"
                        placeholder="Password baru" required>
                    <span class="material-icons-round" onclick="togglePassword(this)">remove_red_eye</span>
                </div>
                <div class="input">
                    <span class="material-icons-round">lock</span>
                    <div class="vertical-line"></div>
                    <input class="input-data text-body font-jakarta font-semibold"
                        type="password" name="password_confirmation"
                        placeholder="Konfirmasi password baru" required>
                    <span class="material-icons-round" onclick="togglePassword(this)">remove_red_eye</span>
                </div>
            </div>

            <button type="submit" class="input-submit">
                <h1 class="font-jakarta">Simpan Password</h1>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/global.js') }}"></script>
<script src="{{ asset('js/pages/auth.js') }}"></script>
@endpush