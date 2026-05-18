@extends('layouts.app')

@section('title', 'Pencarian Resep')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/pencarian-resep.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/resep-card.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/bahan-item.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/chips.css') }}">
@endpush

@section('content')
<x-navbar :back-url="route('landing.index')" />
<main class="search-page font-jakarta" 
    data-page="search" 
    data-search-url="{{ url('/api/resep/search') }}" 
    data-bahan-url="{{ url('/api/bahan/by-ids') }}" 
    data-filter-url="{{ route('pencarian.resep') }}" {{-- 🌟 Diubah ke pencarian.resep --}}
    data-search-page-url="{{ route('pencarian.resep') }}">


    <section class="search-layout">
        {{-- SIDEBAR --}}
        <aside class="search-sidebar">
            {{-- GLOBAL SEARCH TEXT --}}
            <div class="input" id="searchWrapper">
                <span class="material-icons-round">search</span>
                {{-- 🌟 Tambahkan value dari controller jika ada input pencarian sebelumnya --}}
                <input type="text" id="searchInput" class="input-data" placeholder="Cari nama resep atau bahan..." autocomplete="off" value="{{ $keyword ?? '' }}">
            </div>

            <p class="text-body font-medium section-title">Bahan Populer Minggu Ini</p>

            {{-- LIST BAHAN --}}
            <section class="bahan-wrapper" aria-label="Daftar bahan">
                <div class="bahan-list">
                    @forelse($bahans as $huruf => $kelompokBahan)
                        <div class="bahan-group">
                            <span class="group-letter">{{ $huruf }}</span>
                            @foreach($kelompokBahan as $bahan)
                                {{-- 🌟 Pastikan komponen bahan-item lo tahu kalau bahannya sedang ter-check (opsional tergantung logic komponen) --}}
                                <x-bahan-item :bahan="$bahan" />
                            @endforeach
                        </div>
                    @empty
                        <p class="text-caption text-center">Tidak ada bahan ditemukan.</p>
                    @endforelse
                </div>
            </section>

            <div id="selectedInfo" class="selected-info hidden">0 bahan dipilih</div>

            {{-- ACTION BUTTON --}}
            <div class="action-buttons-wrapper">
                <button id="hapusSemuaBtn" class="action-btn hapus-btn" type="button" disabled>Hapus Semua</button>
                <button id="terapkanBtn" class="action-btn terapkan-btn disabled" type="button" disabled>Terapkan</button>
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

        {{-- RESULT --}}
        <section class="search-result" aria-label="Hasil pencarian resep">
            <div class="result-header-wrapper">
                <p id="resultInfoText" class="result-info-text">Pilih bahan atau ketik nama resep untuk memulai</p>
                <div id="selectedChips" class="selected-chips-wrapper" role="list"></div>
            </div>

            {{-- RESEP LIST --}}
            <div id="resepContainer" class="resep-container hidden"></div>

            {{-- LOADING --}}
            <div id="loadingState" class="loading-state hidden" aria-live="polite">
                <div class="loading-spinner"></div>
                <p>Sedang mencari resep...</p>
            </div>

            {{-- EMPTY STATE --}}
            <div id="resultPlaceholder" class="result-placeholder">
                <span class="material-icons-round">restaurant_menu</span>
                <h3>Rekomendasi Resep</h3>
                <p>Masukkan kata kunci pencarian atau pilih kombinasi bahan dapurmu.</p>
            </div>

            {{-- LOAD MORE --}}
            <div id="loadMoreWrapper" class="load-more-wrapper hidden">
                <button id="loadMoreBtn" class="load-more-btn" type="button">Muat Lebih Banyak</button>
            </div>
        </section>
    </section>
</main>
@endsection

@push('scripts')
    <script src="{{ asset('js/pages/pencarian-resep.js') }}"></script>
@endpush