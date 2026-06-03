@extends('layouts.app')
@section('title', 'LaperPoll')

@push('links')
<link rel="stylesheet" href="{{ asset('css/pages/auth.css') }}">
<link rel="stylesheet" href="{{ asset('css/medias/auth.css') }}">
@endpush

@php
$remaining = max(0, session('otp_expired_at', 0) - now()->timestamp);
@endphp

@section('content')
<div class="auth-section flex flex-col">
    <img src="{{ asset('assets/images/Logo_Laperpoll.png') }}" alt="Logo Laperpoll" class="logo">
    <form method="POST" action="{{ route('auth.forgot.verify-otp') }}">
        @csrf
        <div class="form flex flex-col">
            <div class="auth-text flex flex-col">
                <h1 class="font-jakarta text-h4 font-bold">Masukkan OTP</h1>
                <div class="auth-link gap-1 flex flex-row">
                    <p class="font-jakarta text-title2 font-regular">
                        Kode dikirim ke
                    </p>
                    <p class="font-jakarta text-title2 font-bold">
                        {{ session('fp_email') }}
                    </p>
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
                    <div class="vertical-line">&nbsp;</div>
                    <div class="otp-container">
                        <input type="text" name="token[]" class="otp-input" maxlength="1" placeholder="_" autocomplete="off">
                        <input type="text" name="token[]" class="otp-input" maxlength="1" placeholder="_" autocomplete="off">
                        <input type="text" name="token[]" class="otp-input" maxlength="1" placeholder="_" autocomplete="off">
                        <input type="text" name="token[]" class="otp-input" maxlength="1" placeholder="_" autocomplete="off">
                        <input type="text" name="token[]" class="otp-input" maxlength="1" placeholder="_" autocomplete="off">
                        <input type="text" name="token[]" class="otp-input" maxlength="1" placeholder="_" autocomplete="off">
                    </div>
                </div>
            </div>

            <button type="submit" class="input-submit">
                <h1 class="font-jakarta">Verifikasi OTP</h1>
            </button>

            {{-- Tombol kirim ulang dengan timer --}}
            <form method="POST" action="{{ route('auth.forgot.send-otp') }}" style="width:100%;">
                @csrf
                <input type="hidden" name="email" value="{{ session('fp_email') }}">
                <button type="submit" id="resendBtn"
                    data-remaining="{{ $remaining }}"
                    {{ $remaining > 0 ? 'disabled' : '' }}
                    class="input-submit"
                    style="display:flex; gap:2rem; justify-content:space-between;
                                   padding:1rem 2rem; background-color:gray;">
                    <h1 class="font-jakarta" id="timer">{{ gmdate('i:s', $remaining) }}</h1>
                    <h1 class="font-jakarta" id="status">
                        {{ $remaining > 0 ? 'Tunggu' : 'Minta Ulang Kode' }}
                    </h1>
                </button>
            </form>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Auto-focus antar kotak OTP
    const otpInputs = document.querySelectorAll('.otp-input');
    otpInputs.forEach((input, index) => {
        input.addEventListener('input', () => {
            input.value = input.value.replace(/\D/g, '');
            if (input.value && index < otpInputs.length - 1) otpInputs[index + 1].focus();
        });
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !input.value && index > 0) otpInputs[index - 1].focus();
        });
    });
    if (otpInputs.length) otpInputs[0].focus();

    // Countdown timer tombol resend
    const btn = document.getElementById('resendBtn');
    if (btn) {
        let remaining = parseInt(btn.dataset.remaining);
        const timerEl = document.getElementById('timer');
        const statusEl = document.getElementById('status');

        if (remaining > 0) {
            const interval = setInterval(() => {
                remaining--;
                const m = String(Math.floor(remaining / 60)).padStart(2, '0');
                const s = String(remaining % 60).padStart(2, '0');
                timerEl.textContent = `${m}:${s}`;

                if (remaining <= 0) {
                    clearInterval(interval);
                    timerEl.textContent = '';
                    statusEl.textContent = 'Minta Ulang Kode';
                    btn.disabled = false;
                    btn.style.backgroundColor = '';
                }
            }, 1000);
        }
    }
</script>
<script src="{{ asset('js/global.js') }}"></script>
<script src="{{ asset('js/pages/auth.js') }}"></script>
@endpush