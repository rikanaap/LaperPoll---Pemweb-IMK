@extends('layouts.app')

@section('title', 'Swipe Rasa - LaperPoll')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/swipe-resep.css') }}">
@endpush

@section('content')
<main class="swipe-page font-jakarta">
    <x-navbar :backUrl="route('pencarian.resep')" />

    <div class="swipe-wrapper">
        <div class="swipe-split-layout">

            {{-- LEFT PANEL: Info & Progress --}}
            <x-swipe-resep.info-panel />

            {{-- CENTER PANEL: Kartu Swipe --}}
            <section class="swipe-interaction-panel">
                {{-- Header mobile --}}
                <div class="mobile-info-header">
                    <span class="badge-pill">Pilih 3 Rasa</span>
                    <h1 class="mobile-info-header__title">Swipe Rasa Favorit</h1>
                    <div class="progress-box__track progress-box__track--mobile">
                        <div id="mobileProgressBar" class="progress-box__fill"></div>
                    </div>
                </div>

                {{-- Area kartu swipe --}}
                <div class="swipe-container">
                    <div id="swipeCards" class="swipe-cards" role="region" aria-label="Kartu rasa">
                        <div id="emptyState" class="swipe-empty-state" style="display:none;" aria-live="polite">
                            Tidak ada rasa tersedia
                        </div>
                    </div>

                    {{-- Tombol like & dislike --}}
                    <x-swipe-resep.action-buttons />
                </div>
            </section>

            {{-- RIGHT PANEL: Riwayat (Desktop) --}}
            <x-swipe-resep.history-panel />

        </div>
    </div>

    {{-- MOBILE: Drawer riwayat --}}
    <x-swipe-resep.history-drawer />
</main>
@endsection

@push('scripts')
<script>
    window.swipeConfig = {
        apiUrl:      "{{ route('api.swipe.rasa') }}",
        redirectUrl: "{{ route('swipe.filter') }}"
    };
</script>
<script src="{{ asset('js/pages/swipe-resep.js') }}"></script>
@endpush