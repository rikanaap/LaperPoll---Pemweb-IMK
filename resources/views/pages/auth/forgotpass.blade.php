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

    <!-- FORGOT PASS START -->
    <form onsubmit="kirimData('forgot')">
        <div id="forgotForm" style="display: none;" class="form flex flex-col">
            <div class="auth-text flex flex-col">
                <h1 class="font-jakarta text-h4 font-bold">Lupa Password</h1>
                <div class="auth-link gap-1 flex flex-row">
                    <p class="font-jakarta text-title2 font-regular">Sudah ingat!</p>
                    <a href="#" class="font-jakarta text-title2 font-bold" onclick="changeMode('signin')">Masuk
                        disini</a>
                </div>
            </div>
            <div class="form-inputs flex flex-col">
                <div class="input">
                    <span class="material-icons-round">mail</span>
                    <div class="vertical-line"></div>
                    <input class="input-data text-body font-jakarta font-semibold" type="email"
                        placeholder="mail@gmail.com">
                </div>
                <div class="input">
                    <span class="material-icons-round">lock</span>
                    <div class="vertical-line">&nbsp;</div>
                    <div class="otp-container">
                        <input type="text" name="token[]" class="otp-input" maxlength="1" placeholder="_">
                        <input type="text" name="token[]" class="otp-input" maxlength="1" placeholder="_">
                        <input type="text" name="token[]" class="otp-input" maxlength="1" placeholder="_">
                        <input type="text" name="token[]" class="otp-input" maxlength="1" placeholder="_">
                        <input type="text" name="token[]" class="otp-input" maxlength="1" placeholder="_">
                        <input type="text" name="token[]" class="otp-input" maxlength="1" placeholder="_">
                    </div>
                </div>
            </div>
            <button type="submit" class="input-submit">
                <h1 class="font-jakarta">Login</h1>
            </button>
        </div>
    </form>
</div>
@endsection

@push('script')
<script src="{{ asset('js/global.js') }}"></script>
<script src="{{ asset('js/pages/auth.js') }}"></script>
@endpush