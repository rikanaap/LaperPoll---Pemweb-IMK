@extends('layouts.app')

@section('title', 'Daftar - LaperPoll')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/auth.css') }}">
<link rel="stylesheet" href="{{ asset('css/medias/auth.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush

@section('content')
<div class="auth-section flex flex-col">
    <img src="{{ asset('assets/images/Logo_Laperpoll.png') }}" alt="Logo Laperpoll" class="logo">

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

                {{-- Nama --}}
                <div class="input-wrapper-field">
                    <div class="input @error('name') input-error @enderror" id="wrapName">
                        <span class="material-icons-round">person</span>
                        <div class="vertical-line"></div>
                        <input class="input-data text-body font-jakarta font-semibold"
                               type="text" name="name" id="inputName"
                               value="{{ old('name') }}"
                               autocomplete="name"
                               required placeholder="Nama Lengkap">
                    </div>
                    <p class="field-hint" id="hintName"></p>
                </div>

                {{-- Email --}}
                <div class="input-wrapper-field">
                    <div class="input @error('email') input-error @enderror" id="wrapEmail">
                        <span class="material-icons-round">mail</span>
                        <div class="vertical-line"></div>
                        <input class="input-data text-body font-jakarta font-semibold"
                               type="email" name="email" id="inputEmail"
                               value="{{ old('email') }}"
                               autocomplete="email"
                               required placeholder="mail@gmail.com">
                    </div>
                    <p class="field-hint" id="hintEmail"></p>
                </div>

                {{-- Password --}}
                <div class="input-wrapper-field">
                    <div class="input @error('password') input-error @enderror" id="wrapPassword">
                        <span class="material-icons-round">lock</span>
                        <div class="vertical-line"></div>
                        <input class="input-data text-body font-jakarta font-semibold"
                               type="password" name="password" id="inputPassword"
                               autocomplete="new-password"
                               required placeholder="Password (min. 6 karakter)">
                        <span class="material-icons-round eye-toggle" onclick="togglePassword(this)">remove_red_eye</span>
                    </div>
                    <p class="field-hint" id="hintPassword"></p>
                </div>

                {{-- Konfirmasi Password --}}
                <div class="input-wrapper-field">
                    <div class="input" id="wrapConfirm">
                        <span class="material-icons-round">lock</span>
                        <div class="vertical-line"></div>
                        <input class="input-data text-body font-jakarta font-semibold"
                               type="password" name="password_confirmation" id="inputConfirm"
                               autocomplete="new-password"
                               required placeholder="Konfirmasi Password">
                        <span class="material-icons-round eye-toggle" onclick="togglePassword(this)">remove_red_eye</span>
                    </div>
                    <p class="field-hint" id="hintConfirm"></p>
                </div>

            </div>

            <button class="input-submit" type="submit" id="btnSubmit">
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
    <!-- SIGNUP FORM END -->
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/global.js') }}"></script>
<script src="{{ asset('js/pages/auth.js') }}"></script>
@endpush