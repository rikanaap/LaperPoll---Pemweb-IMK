@extends('layouts.app')
@section('title', 'Akses Ditolak - LaperPoll')

@push('links')
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Round">
<script src="https://cdn.tailwindcss.com"></script>
@endpush

@section('content')
<div class="flex flex-col items-center justify-center min-h-screen bg-primary-normal px-6">

    {{-- Card --}}
    <div class="flex flex-col items-center gap-6 bg-white rounded-2xl shadow-lg px-8 py-10 w-full max-w-sm text-center">

        {{-- Icon --}}
        <div class="flex items-center justify-center w-20 h-20 rounded-full bg-secondary-light">
            <span class="material-icons-round text-secondary-normal" style="font-size: 2.5rem;">lock</span>
        </div>

        {{-- Logo --}}
        <img src="{{ asset('assets/images/Logo_Laperpoll.png') }}" alt="LaperPoll" class="w-28">

        {{-- Text --}}
        <div class="flex flex-col gap-2">
            <h1 class="font-jakarta font-bold text-h5 text-secondary-normal">
                Akses Ditolak
            </h1>
            <p class="font-jakarta font-regular text-body text-secondary-normal" style="opacity: 0.6;">
                Halaman ini hanya bisa diakses oleh pengguna yang sudah masuk.
                Silakan login terlebih dahulu untuk melanjutkan.
            </p>
        </div>

        {{-- Button Login --}}
        <a href="{{ route('auth.sign-in') }}"
            class="w-full flex items-center justify-center gap-2 font-jakarta font-semibold text-title2 text-white rounded-xl py-3 px-6 bg-secondary-normal"
            style="background-color: var(--secondary-normal); transition: background-color 0.2s;"
            onmouseover="this.style.backgroundColor='var(--secondary-normal-hover)'"
            onmouseout="this.style.backgroundColor='var(--secondary-normal)'">
            <span class="material-icons-round text-white" style="font-size: 1.2rem; color: white;">login</span>
            Masuk Sekarang
        </a>

        {{-- Back link --}}
        <a href="{{ route('landing.index') }}"
            class="font-jakarta font-regular text-body text-secondary-normal"
            style="opacity: 0.5; text-decoration: underline;">
            Kembali ke Beranda
        </a>

    </div>

</div>
@endsection