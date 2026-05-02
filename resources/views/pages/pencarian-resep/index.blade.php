 @extends('layouts.app')

@section('title', 'Pencarian Resep')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/pencarian-resep.css') }}">
@endpush

@section('content')

<main class="main-content flex flex-col gap-4 font-jakarta">

    {{-- Navbar --}}
    <x-navbar />

    {{-- Search --}}
    <section>
        <div class="input" id="searchWrapper">

            <span class="material-icons-round">
                search
            </span>

            <input
                type="text"
                id="searchInput"
                class="input-data"
                placeholder="Cari Bahan / Nama Resep"
            >

        </div>
    </section>

    {{-- Title --}}
    <p class="text-body font-medium">
        Bahan Populer Minggu Ini
    </p>

    {{-- List Bahan --}}
    <section class="bahan-wrapper">

        <div class="bahan-list flex flex-col gap-3">

            <x-bahan-item nama="Mie" />
            <x-bahan-item nama="Ayam" />
            <x-bahan-item nama="Coklat" />
            <x-bahan-item nama="Telur" />
            <x-bahan-item nama="Sapi" />

        </div>

    </section>

    {{-- Button --}}
    <button
        id="terapkanBtn"
        class="terapkan-btn"
        type="button">

        Terapkan

    </button>

    {{-- Divider --}}
    <div class="flex flex-row gap-2 items-center">

        <div class="horizontal-line flex-1"></div>

        <span class="text-caption atau-text">
            Atau
        </span>

        <div class="horizontal-line flex-1"></div>

    </div>

    {{-- Swipe --}}
    <a href="{{ route('swipe.rasa') }}" class="swipe-btn">

        <span class="material-icons-round">
            swap_horiz
        </span>

        <span>
            Swipe Untuk Mencari
        </span>

    </a>

</main>

@endsection

@push('scripts')
<script src="{{ asset('js/pages/pencarian-resep.js') }}"></script>
@endpush