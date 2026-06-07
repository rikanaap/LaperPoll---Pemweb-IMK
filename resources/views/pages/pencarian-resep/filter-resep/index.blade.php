@extends('layouts.app')

@section('title', 'Filter Resep')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/filter-pencarian-resep.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/resep-card.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/chips.css') }}">
@endpush

@section('content')
<main
    class="filter-page font-jakarta"
    data-page="filter"
    data-search-url="{{ route('api.resep.search') }}"
    data-bahan-url="{{ route('api.bahan.by-ids') }}"
    data-filter-url="{{ route('pencarian.resep') }}"
    data-search-page-url="{{ route('pencarian.resep') }}"
    data-render-url="{{ route('api.resep.render-cards') }}"
>
    <x-navbar :back-url="route('pencarian.resep')" />

    <div class="main-layout">

        {{-- SIDEBAR --}}
        <aside class="sidebar-filter">
    <div class="sidebar-header">
        <span class="badge-pill">LaperPoll</span>
        <h2>Riwayat Pilihan</h2>
        <p class="text-muted">Bahan yang digunakan untuk mencari resep</p>
    </div>

    <div id="chipsContainer" class="selected-chips-wrapper" role="list"></div>

    <div class="filter-info-box">
        <span class="material-icons-round">info</span>
        <p>Kamu bisa menghapus bahan untuk memperbarui hasil resep.</p>
    </div>
</aside>

        {{-- CONTENT --}}
        <section class="content-section" aria-label="Daftar resep">

            <div class="content-header">
                <p id="resultInfo" class="result-info-text">Menampilkan resep...</p>
            </div>

            {{-- LOADING --}}
            <x-pencarian-resep.loading-state message="Mencari resep terbaik..." :hidden="false" />

            {{-- RESEP LIST --}}
            <div id="resepList" class="resep-container hidden"></div>

            {{-- EMPTY STATE --}}
            <x-pencarian-resep.empty-state
                id="emptyState"
                title="Belum ada hasil"
                message="Pilih bahan dulu ya"
                :hidden="true"
            />

            {{-- LOAD MORE --}}
            <x-pencarian-resep.load-more />

        </section>

    </div>
</main>
@endsection

@push('scripts')
    <script src="{{ asset('js/pages/pencarian-resep.js') }}"></script>
@endpush