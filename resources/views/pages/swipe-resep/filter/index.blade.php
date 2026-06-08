@extends('layouts.app')

@section('title', 'Rekomendasi Resep - LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/filter-resep-swipe.css') }}">
@endpush

@section('content')
<main class="filter-page font-jakarta">

    <x-navbar :backUrl="route('swipe.rasa')" />

    <div class="filter-layout">

        {{-- Sidebar: rasa yang dipilih --}}
        <aside class="filter-sidebar" aria-label="Rasa yang dipilih">
            <div class="sidebar-card">
    <span class="badge-pill">LaperPoll</span>
    <h2 class="sidebar-card__title">Rasa Pilihanmu</h2>
    <p class="sidebar-card__desc">
        Resep terbaik berdasarkan rasa favoritmu ditampilkan di bawah ini.
    </p>

    <div id="selectedRasaContainer" class="selected-chips"></div>

</div>
        </aside>

        {{-- Konten utama: grid rekomendasi --}}
        <section class="filter-content" aria-label="Rekomendasi resep">
            <div class="content-header">
                <h1 class="content-header__title">Rekomendasi Resep</h1>
                <p id="resultInfoText" class="result-info" aria-live="polite">
                    Sedang memuat rekomendasi...
                </p>
            </div>

            <div id="resepContainer" class="resep-grid" role="list">
                {{-- Loading state default --}}
                <div class="state-box" role="status" aria-label="Memuat data">
                    <div class="loading-spinner" aria-hidden="true"></div>
                    <h3>Mencari resep terbaik…</h3>
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
<script src="{{ asset('js/pages/filter-swipe-resep.js') }}" defer></script>
@endpush