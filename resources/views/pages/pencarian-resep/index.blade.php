@extends('layouts.app')

@section('title', 'Pencarian Resep')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/pencarian-resep.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/resep-card.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/bahan-item.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/chips.css') }}">
@endpush

@section('content')

<main class="search-page font-jakarta">

    <x-navbar :back-url="route('pencarian.resep')" />

    <section class="search-layout">

        <aside class="search-sidebar">

            <div class="input" id="searchWrapper">
                <span class="material-icons-round">search</span>
                <input
                    type="text"
                    id="searchInput"
                    class="input-data"
                    placeholder="Cari Bahan / Nama Resep">
            </div>

            <p class="text-body font-medium section-title">
                Bahan Populer Minggu Ini
            </p>

            <section class="bahan-wrapper">
                <div class="bahan-list">

                    @forelse($bahans as $huruf => $kelompokBahan)

                    <div class="bahan-group">

                        <span class="group-letter">
                            {{ $huruf }}
                        </span>

                        @foreach($kelompokBahan as $bahan)
                        <x-bahan-item :bahan="$bahan" />
                        @endforeach

                    </div>

                    @empty
                    <p class="text-caption text-center">
                        Tidak ada bahan yang ditemukan.
                    </p>
                    @endforelse

                </div>
            </section>

            <div class="selected-info" id="selectedInfo" style="display: none;">
                0 bahan terpilih
            </div>

            <div class="action-buttons-wrapper">

                <button
                    id="hapusSemuaBtn"
                    class="action-btn hapus-btn"
                    type="button"
                    disabled>
                    Hapus Semua
                </button>

                <button
                    id="terapkanBtn"
                    class="action-btn terapkan-btn disabled"
                    type="button"
                    disabled>
                    Terapkan
                </button>

            </div>

            <div class="divider-wrap">
                <div class="horizontal-line"></div>
                <span class="text-caption atau-text">Atau</span>
                <div class="horizontal-line"></div>
            </div>

            <a href="{{ route('swipe.rasa') }}" class="swipe-btn">
                <span class="material-icons-round">swap_horiz</span>
                <span>Swipe Untuk Mencari</span>
            </a>

        </aside>

        <section class="search-result">

            <div class="result-header-wrapper">

                <p class="result-info-text" id="resultInfoText">
                    Pilih bahan untuk melihat resep
                </p>

                <div class="selected-chips-wrapper" id="selectedChips"></div>

            </div>

            <div id="resepContainer" class="resep-container hidden">

                @foreach($reseps as $resep)
                <x-resep-card :resep="$resep" />
                @endforeach

            </div>

            <div id="loadingState" class="loading-state hidden">
                <div class="loading-spinner"></div>
                <p>Sedang mencari resep...</p>
            </div>

            <div class="result-placeholder" id="resultPlaceholder">
                <span class="material-icons-round">restaurant_menu</span>
                <h3>Rekomendasi Resep</h3>
                <p>Pilih bahan terlebih dahulu untuk melihat hasil resep.</p>
            </div>

        </section>

    </section>

</main>

@endsection

@push('scripts')
<script src="{{ asset('js/pages/pencarian-resep.js') }}"></script>
@endpush