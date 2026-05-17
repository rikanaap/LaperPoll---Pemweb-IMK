@extends('layouts.app')

@section('title', 'Rekomendasi Resep')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/filter-resep-swipe.css') }}">
@endpush

@section('content')
<main class="filter-page font-jakarta">
    <x-navbar :backUrl="route('swipe.rasa')" />

    <div class="filter-layout">
        {{-- LEFT PANEL --}}
        <aside class="filter-sidebar">
            <div class="sidebar-card full-height">
                <span class="sidebar-badge">LaperPoll</span>
                <h2>Rasa Pilihanmu</h2>
                <p>Sistem menampilkan resep terbaik berdasarkan rasa favoritmu.</p>
                <div id="selectedRasaContainer" class="selected-chips">
                    {{-- Diisi dinamis via JS Engine --}}
                </div>
            </div>
        </aside>

        {{-- RIGHT PANEL --}}
        <section class="filter-content">
            <div class="content-header">
                <h1>Rekomendasi Resep</h1>
                <p id="resultInfoText" class="result-info">Sedang memuat rekomendasi...</p>
            </div>

            {{-- GRID CONTAINER --}}
            <div id="resepContainer" class="resep-grid">
                {{-- LOADING STATE DEFAULT --}}
                <div class="loading-state">
                    <div class="loading-spinner"></div>
                    <h3>Sedang mencari resep...</h3>
                    <p>Mohon tunggu sebentar</p>
                </div>
            </div>
        </section>
    </div>
</main>
@endsection

@push('scripts')
<script>
    window.filterSwipeConfig = {
        apiUrl: "{{ route('api.swipe.filter.resep.swipe') }}",
        swipeUrl: "{{ route('swipe.rasa') }}"
    };
</script>
<script src="{{ asset('js/pages/filter-swipe-resep.js') }}"></script>
@endpush