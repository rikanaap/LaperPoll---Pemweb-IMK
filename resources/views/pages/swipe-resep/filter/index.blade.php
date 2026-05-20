@extends('layouts.app')

@section('title', 'Rekomendasi Resep - LaperPoll')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/filter-resep-swipe.css') }}">
@endpush

@section('content')
<main class="filter-page font-jakarta">
    <x-navbar :backUrl="route('swipe.rasa')" />

    <div class="filter-layout">

        {{-- LEFT PANEL: Rasa yang dipilih --}}
        <aside class="filter-sidebar">
            <div class="sidebar-card">
                <span class="badge-pill">LaperPoll</span>
                <h2 class="sidebar-card__title">Rasa Pilihanmu</h2>
                <p class="sidebar-card__desc">
                    Sistem menampilkan resep terbaik berdasarkan rasa favoritmu.
                </p>
                <div id="selectedRasaContainer" class="selected-chips" aria-label="Rasa yang dipilih">
                    {{-- Diisi dinamis via JS --}}
                </div>
            </div>
        </aside>

        {{-- RIGHT PANEL: Grid rekomendasi resep --}}
        <section class="filter-content">
            <div class="content-header">
                <h1 class="content-header__title">Rekomendasi Resep</h1>
                <p id="resultInfoText" class="result-info" aria-live="polite">
                    Sedang memuat rekomendasi...
                </p>
            </div>

            <div id="resepContainer" class="resep-grid" role="list">
                {{-- Loading state default --}}
                <div class="state-box" role="status" aria-label="Memuat data">
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
        apiUrl:   "{{ route('api.swipe.filter.resep.swipe') }}",
        swipeUrl: "{{ route('swipe.rasa') }}"
    };
</script>
<script src="{{ asset('js/pages/filter-swipe-resep.js') }}"></script>
@endpush