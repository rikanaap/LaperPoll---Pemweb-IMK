@extends('layouts.app')
@section('title', 'LaperPoll')

@push('links')
<link rel="stylesheet" href="{{ asset('css/pages/auth.css') }}">
<link rel="stylesheet" href="{{ asset('css/medias/auth.css') }}">
@endpush

@section('content')
<div class="auth-section flex flex-col">
    <img src="{{ asset('assets/images/Logo_Laperpoll.png') }}" alt="Logo Laperpoll" class="logo">

    <form method="POST" action="{{ route('auth.forgot.send-otp') }}">
        @csrf
        <div class="form flex flex-col">
            <div class="auth-text flex flex-col">
                <h1 class="font-jakarta text-h4 font-bold">Lupa Password</h1>
                <div class="auth-link gap-1 flex flex-row">
                    <p class="font-jakarta text-title2 font-regular">Sudah ingat!</p>
                    <a href="{{ route('auth.sign-in') }}" class="font-jakarta text-title2 font-bold">
                        Masuk disini
                    </a>
                </div>
            </div>

            @if ($errors->any())
            <p class="font-jakarta text-body font-semibold" style="color:red; margin-bottom:4px;">
                {{ $errors->first() }}
            </p>
            @endif

            <div class="form-inputs flex flex-col">
                <div class="input">
                    <span class="material-icons-round">mail</span>
                    <div class="vertical-line"></div>
                    <input class="input-data text-body font-jakarta font-semibold"
                        type="email" name="email"
                        value="{{ old('email') }}"
                        placeholder="mail@gmail.com" required>
                </div>
            </div>

            <button type="submit" class="input-submit">
                <h1 class="font-jakarta">Kirim Kode OTP</h1>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/global.js') }}"></script>
<script src="{{ asset('js/pages/auth.js') }}"></script>
@endpush