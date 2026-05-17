@extends('layouts.app')

@section('title', 'Filter Resep')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/filter-pencarian-resep.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/resep-card.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/chips.css') }}">
@endpush

@section('content')
<main class="filter-page font-jakarta" 
    data-page="filter" 
    data-search-url="{{ url('/api/resep/search') }}" 
    data-bahan-url="{{ url('/api/bahan/by-ids') }}" 
    data-filter-url="{{ route('pencarian.resep') }}" {{-- 🌟 Diubah ke pencarian.resep --}}
    data-search-page-url="{{ route('pencarian.resep') }}"> {{-- 🌟 Diubah ke pencarian.resep --}}

    <x-navbar :back-url="route('pencarian.resep')" />

    <div class="main-layout">
        {{-- SIDEBAR --}}
        <aside class="sidebar-filter">
            <div class="sidebar-header">
                <h2>Riwayat Pilihan</h2>
                <p class="text-muted">Bahan yang digunakan untuk mencari resep</p>
            </div>

            {{-- CHIPS --}}
            <div id="chipsContainer" class="selected-chips-wrapper" role="list"></div>

            {{-- INFO --}}
            <div class="filter-info-box">
                <span class="material-icons-round">info</span>
                <p>Kamu bisa menghapus bahan untuk memperbarui hasil resep.</p>
            </div>
        </aside>

        {{-- CONTENT --}}
        <section class="content-section" aria-label="Daftar resep">
            {{-- HEADER --}}
            <div class="content-header">
                <p id="resultInfo" class="result-info-text">Menampilkan resep...</p>
            </div>

            {{-- LOADING --}}
            <div id="loadingState" class="loading-state" aria-live="polite">
                <div class="loading-spinner"></div>
                <p>Mencari resep terbaik...</p>
            </div>

            {{-- RESEP --}}
            <div id="resepList" class="resep-container hidden"></div>

            {{-- EMPTY --}}
            <div id="emptyState" class="result-placeholder hidden">
                <span class="material-icons-round">restaurant_menu</span>
                <h3>Belum ada hasil</h3>
                <p>Pilih bahan dulu ya</p>
            </div>

            {{-- LOAD MORE --}}
            <div id="loadMoreWrapper" class="load-more-wrapper hidden">
                <button id="loadMoreBtn" class="load-more-btn" type="button">Muat Lebih Banyak</button>
            </div>
        </section>
    </div>
</main>
@endsection

@push('scripts')
    <script src="{{ asset('js/pages/pencarian-resep.js') }}"></script>
@endpush