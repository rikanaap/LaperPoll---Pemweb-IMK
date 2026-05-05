@extends('layouts.app')

@section('title', 'Tambah Bahan - LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/tambah-bahan.css') }}">
@endpush

@section('content')
<main class="main-content flex flex-col">

    {{-- NAVBAR --}}
    <nav class="navbar">
        <a href="{{ route('kulkas.index') }}" class="back-btn" aria-label="Kembali">
            <span class="material-icons-round text-h4 text-accent-normal">arrow_back</span>
        </a>
        <img src="{{ asset('assets/images/Logo_Laperpoll.png') }}" alt="Logo Laperpoll" class="logo">
        <a href="{{ route('profile.index') }}" class="profile-link">
            <img src="{{ asset('assets/images/Image_DummyProfile.png') }}" alt="Profil Foto" class="profile">
        </a>
    </nav>

    <div class="form-wrapper flex flex-col gap-6">

        <div class="form-title flex flex-col gap-1">
            <h1 class="font-jakarta font-bold text-h5 kulkas-title">Tambah Bahan</h1>
            <p class="font-jakarta font-regular text-body text-primary-darker">
                Isi detail bahan yang ingin kamu tambahkan ke kulkas.
            </p>
        </div>

        {{-- ERROR VALIDATION --}}
        @if($errors->any())
            <div class="flash-error font-jakarta text-body">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('kulkas.store') }}" method="POST" id="formTambahBahan">
            @csrf

            {{-- NAMA BAHAN --}}
            <div class="form-group flex flex-col gap-2">
                <label class="font-jakarta font-semibold text-body text-secondary-normal"
                    for="searchBahan">Nama Bahan</label>
                <div class="search-wrapper">
                    <div class="input">
                        <span class="material-icons-round text-body text-primary-darker">search</span>
                        <input id="searchBahan" class="input-data font-jakarta text-body"
                            type="text" placeholder="Cari bahan..." autocomplete="off"
                            value="{{ old('bahan_nama') }}"/>
                        <span class="material-icons-round text-body text-primary-darker"
                            id="clearSearch">close</span>
                    </div>
                    {{-- Hidden input untuk kirim bahan_id --}}
                    <input type="hidden" name="bahan_id" id="bahanId" value="{{ old('bahan_id') }}">
                    <ul class="bahan-dropdown" id="bahanDropdown" role="listbox"></ul>
                </div>
                {{-- Data bahan dari server untuk JS --}}
                <script id="bahanData" type="application/json">
                    @json($bahans->map(fn($b) => [
                        'id'   => $b->id,
                        'nama' => $b->nama,
                        'has_expiry' => $b->expired_expectancy_day !== null,
                        'expired_expectancy_day' => $b->expired_expectancy_day,
                    ]))
                </script>
            </div>

            {{-- JUMLAH (angka + satuan teks bebas) --}}
            <div class="form-group flex flex-col gap-2" style="margin-top:1.25rem;">
                <label class="font-jakarta font-semibold text-body text-secondary-normal"
                    for="jumlahBahan">Jumlah</label>
                <div class="input jumlah-input">
                    <button class="jumlah-btn" id="btnMinus" type="button" aria-label="Kurang">
                        <span class="material-icons-round">remove</span>
                    </button>
                    <input id="jumlahAngka" class="input-data font-jakarta text-body jumlah-field"
                        type="number" min="1" value="{{ old('jumlah_angka', 1) }}"/>
                    <button class="jumlah-btn" id="btnPlus" type="button" aria-label="Tambah">
                        <span class="material-icons-round">add</span>
                    </button>
                </div>
                {{-- Satuan teks bebas --}}
                <div class="input" style="margin-top:0.5rem;">
                    <span class="material-icons-round text-body text-primary-darker">straighten</span>
                    <input id="satuanBahan" name="jumlah" class="input-data font-jakarta text-body"
                        type="text" placeholder="Contoh: 6 butir / 200 gram / 1 liter"
                        value="{{ old('jumlah') }}"/>
                </div>
                <p class="font-jakarta text-caption text-primary-darker">
                    Ketik jumlah + satuan lengkap, contoh: <em>6 butir</em>, <em>200 gram</em>
                </p>
            </div>

            {{-- TANGGAL BELI --}}
            <div class="form-group flex flex-col gap-2" style="margin-top:1.25rem;">
                <label class="font-jakarta font-semibold text-body text-secondary-normal"
                    for="boughtDate">Tanggal Beli</label>
                <div class="input">
                    <span class="material-icons-round text-body text-primary-darker">shopping_bag</span>
                    <input id="boughtDate" name="bought_date"
                        class="input-data font-jakarta text-body" type="date"
                        value="{{ old('bought_date', date('Y-m-d')) }}"
                        max="{{ date('Y-m-d') }}"/>
                </div>
            </div>

            {{-- TANGGAL EXPIRED (muncul hanya jika bahan has_expiry) --}}
            <div class="form-group flex flex-col gap-2" id="expiredSection"
                style="margin-top:1.25rem; display:none !important;">
                <label class="font-jakarta font-semibold text-body text-secondary-normal"
                    for="expiredDate">Tanggal Expired</label>

                {{-- Quick chips --}}
                <div class="expired-options flex flex-row gap-2" id="expiredChips"></div>

                <div class="input">
                    <span class="material-icons-round text-body text-primary-darker">calendar_today</span>
                    <input id="expiredDate" name="expired_date"
                        class="input-data font-jakarta text-body" type="date"
                        value="{{ old('expired_date') }}"/>
                </div>
                <p class="font-jakarta text-caption text-primary-darker">
                    Pilih estimasi cepat atau atur tanggal manual.
                </p>
            </div>

            {{-- SUBMIT --}}
            <button class="input-submit font-jakarta font-semibold text-title2"
                id="btnTambahBahan" type="submit" style="margin-top:1.5rem;">
                Tambah Bahan
            </button>

        </form>
    </div>
</main>
@endsection

@push('scripts')
<script src="{{ asset('js/tambah-bahan.js') }}"></script>
@endpush