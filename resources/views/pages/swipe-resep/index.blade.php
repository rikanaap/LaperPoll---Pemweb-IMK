@extends('layouts.app')

@section('title', 'Swipe Rasa - LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/swipe-resep.css') }}">
@endpush

@section('content')
<main class="swipe-page font-jakarta">

    @php
        $allowedBackRoutes = [
            route('pencarian.resep'),
            route('landing.index'),
        ];

        $previousUrl = url()->previous();

        $backUrl = in_array($previousUrl, $allowedBackRoutes)
            ? $previousUrl
            : route('landing.index');
    @endphp

    <x-navbar :backUrl="$backUrl" />

    <div class="swipe-wrapper">
        <div class="swipe-split-layout">

            {{-- Panel kiri: info & progress --}}
            <x-swipe-resep.info-panel />

            {{-- Panel tengah: swipe cards --}}
            <section class="swipe-interaction-panel" aria-label="Area swipe rasa">

                {{-- Header info (mobile only) --}}
                <div class="mobile-info-header" aria-hidden="true">
                    <span class="badge-pill">Pilih 3 Rasa</span>
                    <h1 class="mobile-info-header__title">Swipe Rasa Favorit</h1>
                    <div class="progress-box__track progress-box__track--mobile" role="progressbar" aria-valuemin="0" aria-valuemax="3">
                        <div id="mobileProgressBar" class="progress-box__fill"></div>
                    </div>
                </div>

                {{-- Card area --}}
                <div class="swipe-container">
                    <div
                        id="swipeCards"
                        class="swipe-cards"
                        role="region"
                        aria-label="Kartu pilihan rasa"
                        aria-live="polite"
                    >
                        <div
                            id="emptyState"
                            class="swipe-empty-state"
                            style="display: none;"
                            role="status"
                        >
                            Tidak ada rasa tersedia
                        </div>
                    </div>

                    <x-swipe-resep.action-buttons />
                </div>

            </section>

            {{-- Panel kanan: history (desktop only) --}}
            <x-swipe-resep.history-panel />

        </div>
    </div>

    {{-- Drawer riwayat (mobile only) --}}
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
<script src="{{ asset('js/pages/swipe-resep.js') }}" defer></script>
@endpush