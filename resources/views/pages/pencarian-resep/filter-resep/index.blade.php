@extends('layouts.app')

@section('title', 'Filter Resep')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/filter-pencarian-resep.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/resep-card.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/chips.css') }}">
@endpush

@section('content')

<main class="filter-page font-jakarta">

    <x-navbar :back-url="route('pencarian.resep')" />

    <div id="chipsContainer" class="selected-chips-wrapper"></div>

    <p id="resultInfo" class="result-info-text">
        Menampilkan resep...
    </p>

    <div id="loadingState" class="loading-state">
        <div class="loading-spinner"></div>
        <p>Mencari resep terbaik...</p>
    </div>

    <section id="resepList" class="resep-container hidden">

        @foreach($reseps as $resep)
            <x-resep-card :resep="$resep" />
        @endforeach

    </section>

    <div id="emptyState" class="result-placeholder hidden">

        <span class="material-icons-round">
            restaurant_menu
        </span>

        <h3>Belum ada hasil</h3>

        <p>Pilih bahan dulu ya</p>

    </div>

</main>

@endsection

@push('scripts')
<script src="{{ asset('js/pages/filter-pencarian-resep.js') }}"></script>
@endpush