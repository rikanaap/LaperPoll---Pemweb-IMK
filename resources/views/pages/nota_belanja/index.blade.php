@extends('layouts.app')

@section('title', 'Nota Belanja - LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/nota-belanja.css') }}">
@endpush

@section('content')
<main class="main-content flex flex-col">

    {{-- NAVBAR --}}
    <nav class="navbar">
        <a href="{{ route('meal-planner.index') }}" class="back-btn">
            <span class="material-icons-round text-h4 text-accent-normal">arrow_back</span>
        </a>
        <img src="{{ asset('assets/Logo_Laperpoll.png') }}" alt="Logo Laperpoll" class="logo">
        <a href="{{ route('profile.index') }}" class="profile-link">
            <img src="{{ asset('assets/Image_DummyProfile.png') }}" alt="Profil Foto" class="profile">
        </a>
    </nav>

    {{-- KONTEN UTAMA --}}
    <div class="nota-konten flex flex-col gap-3">

        {{-- HEADER CARD --}}
        <div class="header-card flex flex-row">
            <h1 class="font-jakarta font-bold text-h5 kulkas-title">Nota Belanja</h1>
            <div class="nota-date flex flex-row gap-1">
                <span class="material-icons-round nota-date-icon">calendar_today</span>
                <p class="font-jakarta font-medium text-caption nota-date-text">Senin</p>
            </div>
        </div>

        {{-- PROGRESS CARD --}}
        <div class="progress-card flex flex-col gap-2">
            <div class="progress-header flex flex-row gap-2">
                <span class="material-icons-round progress-cart-icon">shopping_cart</span>
                <p class="font-jakarta font-bold text-title2 text-secondary-normal" style="flex:1;">Daftar Belanja</p>
                <p class="font-jakarta font-bold text-title2 kulkas-title">3/8 ✓</p>
            </div>
            <div class="progress-bar-track">
                <div class="progress-bar-fill" style="width: 37.5%;"></div>
            </div>
            <p class="font-jakarta font-regular text-caption text-primary-darker" style="text-align:right;">37.5% selesai</p>
        </div>

        {{-- KATEGORI: KARBOHIDRAT --}}
        <div class="belanja-kategori flex flex-col gap-2">
            <p class="font-jakarta font-bold text-caption kategori-label">KARBOHIDRAT</p>
            <label class="belanja-item flex flex-row gap-3">
                <input type="checkbox" class="belanja-check" checked>
                <span class="checkmark"></span>
                <span class="font-jakarta font-medium text-body text-secondary-normal item-nama">Beras</span>
                <span class="font-jakarta font-regular text-body text-primary-darker item-qty">200 gram</span>
            </label>
            <label class="belanja-item flex flex-row gap-3">
                <input type="checkbox" class="belanja-check" checked>
                <span class="checkmark"></span>
                <span class="font-jakarta font-medium text-body text-secondary-normal item-nama">Tepung Ketan</span>
                <span class="font-jakarta font-regular text-body text-primary-darker item-qty">150 gram</span>
            </label>
            <label class="belanja-item flex flex-row gap-3">
                <input type="checkbox" class="belanja-check">
                <span class="checkmark"></span>
                <span class="font-jakarta font-medium text-body text-secondary-normal item-nama">Roti Tawar</span>
                <span class="font-jakarta font-regular text-body text-primary-darker item-qty">4 lembar</span>
            </label>
        </div>

        {{-- KATEGORI: PROTEIN --}}
        <div class="belanja-kategori flex flex-col gap-2">
            <p class="font-jakarta font-bold text-caption kategori-label">PROTEIN</p>
            <label class="belanja-item flex flex-row gap-3">
                <input type="checkbox" class="belanja-check" checked>
                <span class="checkmark"></span>
                <span class="font-jakarta font-medium text-body text-secondary-normal item-nama">Telur</span>
                <span class="font-jakarta font-regular text-body text-primary-darker item-qty">3 butir</span>
            </label>
            <label class="belanja-item flex flex-row gap-3">
                <input type="checkbox" class="belanja-check">
                <span class="checkmark"></span>
                <span class="font-jakarta font-medium text-body text-secondary-normal item-nama">Ayam</span>
                <span class="font-jakarta font-regular text-body text-primary-darker item-qty">500 gram</span>
            </label>
            <label class="belanja-item flex flex-row gap-3">
                <input type="checkbox" class="belanja-check">
                <span class="checkmark"></span>
                <span class="font-jakarta font-medium text-body text-secondary-normal item-nama">Tempe</span>
                <span class="font-jakarta font-regular text-body text-primary-darker item-qty">1 papan</span>
            </label>
        </div>

        {{-- KATEGORI: SAYURAN --}}
        <div class="belanja-kategori flex flex-col gap-2">
            <p class="font-jakarta font-bold text-caption kategori-label">SAYURAN</p>
            <label class="belanja-item flex flex-row gap-3">
                <input type="checkbox" class="belanja-check" checked>
                <span class="checkmark"></span>
                <span class="font-jakarta font-medium text-body text-secondary-normal item-nama">Daun Bawang</span>
                <span class="font-jakarta font-regular text-body text-primary-darker item-qty">2 batang</span>
            </label>
            <label class="belanja-item flex flex-row gap-3">
                <input type="checkbox" class="belanja-check">
                <span class="checkmark"></span>
                <span class="font-jakarta font-medium text-body text-secondary-normal item-nama">Bayam</span>
                <span class="font-jakarta font-regular text-body text-primary-darker item-qty">1 ikat</span>
            </label>
        </div>

        {{-- KATEGORI: BUMBU --}}
        <div class="belanja-kategori flex flex-col gap-2">
            <p class="font-jakarta font-bold text-caption kategori-label">BUMBU</p>
            <label class="belanja-item flex flex-row gap-3">
                <input type="checkbox" class="belanja-check">
                <span class="checkmark"></span>
                <span class="font-jakarta font-medium text-body text-secondary-normal item-nama">Bawang Merah</span>
                <span class="font-jakarta font-regular text-body text-primary-darker item-qty">50 gram</span>
            </label>
            <label class="belanja-item flex flex-row gap-3">
                <input type="checkbox" class="belanja-check">
                <span class="checkmark"></span>
                <span class="font-jakarta font-medium text-body text-secondary-normal item-nama">Bawang Putih</span>
                <span class="font-jakarta font-regular text-body text-primary-darker item-qty">30 gram</span>
            </label>
            <label class="belanja-item flex flex-row gap-3">
                <input type="checkbox" class="belanja-check">
                <span class="checkmark"></span>
                <span class="font-jakarta font-medium text-body text-secondary-normal item-nama">Cabai Merah</span>
                <span class="font-jakarta font-regular text-body text-primary-darker item-qty">5 buah</span>
            </label>
        </div>

        {{-- BELUM DIBELI --}}
        <div class="belum-dibeli flex flex-row">
            <p class="font-jakarta font-medium text-body text-primary-darker">Item belum dibeli</p>
            <p class="font-jakarta font-bold text-body kulkas-title">5 item</p>
        </div>

    </div>

</main>
@endsection