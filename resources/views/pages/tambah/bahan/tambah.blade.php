@extends('layouts.app')

@section('title', 'Tambah Bahan - LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/tambah-bahan.css') }}">
@endpush

@section('content')
<main class="main-content">

    {{-- NAVBAR --}}
    <x-navbar :back="true" />

    <div class="form-wrapper">

        <div class="form-title flex flex-col gap-1">
            <h1 class="font-jakarta font-bold text-h5 kulkas-title">Tambah Bahan</h1>
            <p class="font-jakarta font-regular text-body text-primary-darker">
                Isi detail bahan yang ingin kamu tambahkan ke kulkas.
            </p>
        </div>

        {{-- VALIDASI ERROR --}}
        @if($errors->any())
            <div class="flash-error">
                <span class="material-icons-round" style="flex-shrink:0;">error_outline</span>
                <ul style="margin:0; padding-left:1rem; list-style:disc;">
                    @foreach($errors->all() as $error)
                        <li class="font-jakarta text-body">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('kulkas.store') }}" method="POST" id="formTambahBahan">
            @csrf

            {{-- Data bahan untuk JS autocomplete --}}
            <script id="bahanData" type="application/json">
                @json($bahans->map(fn($b) => [
                    'id'                     => $b->id,
                    'nama'                   => $b->nama,
                    'has_expiry'             => $b->expired_expectancy_day !== null,
                    'expired_expectancy_day' => $b->expired_expectancy_day,
                ]))
            </script>

            {{-- NAMA BAHAN --}}
            <div class="form-group">
                <label class="font-jakarta font-semibold text-body text-secondary-normal"
                    for="searchBahan">Nama Bahan</label>
                <div class="search-wrapper">
                    <div class="input">
                        <span class="material-icons-round text-body text-primary-darker">search</span>
                        <input id="searchBahan"
                            class="input-data font-jakarta text-body"
                            type="text"
                            placeholder="Cari bahan..."
                            autocomplete="off"
                            value="{{ old('bahan_nama') }}">
                        <span class="material-icons-round text-body text-primary-darker"
                            id="clearSearch">close</span>
                    </div>
                    <input type="hidden" name="bahan_id" id="bahanId" value="{{ old('bahan_id') }}">
                    <ul class="bahan-dropdown" id="bahanDropdown" role="listbox"></ul>
                </div>
            </div>

            {{-- JUMLAH --}}
            <div class="form-group">
                <label class="font-jakarta font-semibold text-body text-secondary-normal"
                    for="satuanBahan">Jumlah</label>

                {{-- Stepper angka --}}
                <div class="input jumlah-input">
                    <button class="jumlah-btn" id="btnMinus" type="button" aria-label="Kurang">
                        <span class="material-icons-round">remove</span>
                    </button>
                    <input id="jumlahAngka"
                        class="input-data font-jakarta text-body jumlah-field"
                        type="number" min="1"
                        value="{{ old('jumlah_angka', 1) }}">
                    <button class="jumlah-btn" id="btnPlus" type="button" aria-label="Tambah">
                        <span class="material-icons-round">add</span>
                    </button>
                </div>

                {{-- Teks satuan --}}
                <div class="input" style="margin-top:0.5rem;">
                    <span class="material-icons-round text-body text-primary-darker">straighten</span>
                    <input id="satuanBahan" name="jumlah"
                        class="input-data font-jakarta text-body"
                        type="text"
                        placeholder="Contoh: 6 butir / 200 gram / 1 liter"
                        value="{{ old('jumlah') }}">
                </div>
                <p class="font-jakarta text-caption text-primary-darker">
                    Tulis jumlah dan satuan lengkap, contoh: <em>6 butir</em>, <em>200 gram</em>
                </p>
            </div>

            {{-- TANGGAL BELI --}}
            <div class="form-group">
                <label class="font-jakarta font-semibold text-body text-secondary-normal"
                    for="boughtDate">Tanggal Beli</label>
                <div class="input">
                    <span class="material-icons-round text-body text-primary-darker">shopping_bag</span>
                    <input id="boughtDate" name="bought_date"
                        class="input-data font-jakarta text-body"
                        type="date"
                        value="{{ old('bought_date', date('Y-m-d')) }}"
                        max="{{ date('Y-m-d') }}">
                </div>
            </div>

            {{-- TANGGAL EXPIRED (tampil otomatis jika bahan punya expiry) --}}
            <div class="form-group" id="expiredSection" style="display:none;">
                <label class="font-jakarta font-semibold text-body text-secondary-normal"
                    for="expiredDate">Tanggal Expired</label>
                <div class="expired-options" id="expiredChips"></div>
                <div class="input">
                    <span class="material-icons-round text-body text-primary-darker">calendar_today</span>
                    <input id="expiredDate" name="expired_date"
                        class="input-data font-jakarta text-body"
                        type="date"
                        value="{{ old('expired_date') }}">
                </div>
                <p class="font-jakarta text-caption text-primary-darker">
                    Pilih estimasi cepat atau atur tanggal manual.
                </p>
            </div>

            {{-- SUBMIT --}}
            <button class="input-submit" type="submit">
                Tambah Bahan
            </button>

        </form>
    </div>

</main>
@endsection

@push('scripts')
    <script src="{{ asset('js/tambah-bahan.js') }}"></script>
@endpush