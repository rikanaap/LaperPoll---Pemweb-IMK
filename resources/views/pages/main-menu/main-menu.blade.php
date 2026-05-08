@extends('layouts.app')

@section('title', 'Main Menu - LaperPoll')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/main-menu.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/resep-card.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/chips.css') }}">
@endpush

@section('content')
@include('components.navbar')
<div class="desktop-menus">
    <section class="resep-menus">
        <main class="main-content flex flex-col">
            @foreach ($reseps as $resep )
            <x-resep-card :resep="$resep" />
            @endforeach
        </main>
    </section>
    <section class="bottom-menus">
        <div class="input">
            <span class="material-icons-round">search</span>
            <input id="search-filter" class="input-data text-body font-jakarta font-semibold" type="text" placeholder="Telusuri filter yang ada">
        </div>
        <div class="bottom-filters-used" style="display: none;">
            <p class="font-jakarta text-body font-semibold text-secondary-normal">Filter saat ini:</p>
        </div>
        <div class="bottom-filters">
            <div class="bottom-filter">
                <p class="font-jakarta text-body font-regular">Makanan</p>
            </div>
            <div class="bottom-filter">
                <p class="font-jakarta text-body font-regular">Minuman</p>
            </div>
            <div class="bottom-filter">
                <p class="font-jakarta text-body font-regular">Dessert</p>
            </div>
            <div class="bottom-filter">
                <p class="font-jakarta text-body font-regular">Cemilan</p>
            </div>
            <div class="bottom-filter">
                <p class="font-jakarta text-body font-regular">Tradisional</p>
            </div>
            <div class="bottom-filter">
                <p class="font-jakarta text-body font-regular">Modern</p>
            </div>
            <div class="bottom-filter">
                <p class="font-jakarta text-body font-regular">Sarapan</p>
            </div>
            <div class="bottom-filter">
                <p class="font-jakarta text-body font-regular">Makan Malam</p>
            </div>
        </div>
        <div class="bottom-text">
            <p class="font-jakarta text-body font-semibold">Tekan untuk memfilter hasil!</p>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/global.js') }}"></script>
<script src="{{ asset('js/pages/main-menu.js') }}" type="module"></script>
@endpush