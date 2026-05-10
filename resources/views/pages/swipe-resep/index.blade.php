@extends('layouts.app')

@section('title', 'Swipe Rasa - LaperPoll')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/swipe-resep.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/history-drawer.css') }}">
@endpush

@section('content')
<main class="swipe-page font-jakarta">

    {{-- =========================================
         NAVBAR
    ========================================== --}}
    <x-navbar :backUrl="route('pencarian.resep')" />

    <div class="swipe-wrapper">

        <div class="swipe-split-layout">

            {{-- =========================================
                 PANEL KIRI - INFO
            ========================================== --}}
            <div class="swipe-info-panel">

                <div class="info-card">

                    <div class="info-header">

                        <span class="material-icons-round info-icon">
                            tune
                        </span>

                        <span class="badge-tag">
                            LaperPoll
                        </span>

                    </div>

                    <h2 class="info-title">
                        Temukan Resep Berdasarkan Selera Rasa
                    </h2>

                    <p class="info-desc">
                        Pilih 3 rasa favoritmu hari ini agar sistem kami dapat
                        merekomendasikan resep terbaik untukmu.
                    </p>

                    <div class="divider-line"></div>

                    {{-- Progress --}}
                    <div class="progress-box">

                        <div class="progress-label">
                            <span>Batas Pilihan</span>
                            <span id="counterText">0 / 3</span>
                        </div>

                        <div class="progress-bar-container">
                            <div class="progress-bar" id="progressBar"></div>
                        </div>

                    </div>

                    {{-- Tips --}}
                    <div class="tips-box">

                        <h4 class="tips-title">

                            <span class="material-icons-round">
                                lightbulb
                            </span>

                            Tips

                        </h4>

                        <ul class="tips-list">
                            <li>➡ Swipe kanan untuk suka</li>
                            <li>⬅ Swipe kiri untuk skip</li>
                        </ul>

                    </div>

                </div>

            </div>

            {{-- =========================================
                 PANEL TENGAH - SWIPE
            ========================================== --}}
            <div class="swipe-interaction-panel">

                {{-- Mobile Header --}}
                <div class="mobile-info-header">

                    <span class="mobile-badge">
                        Pilih 3 Rasa
                    </span>

                    <h1 class="mobile-title">
                        Swipe Rasa Favorit
                    </h1>

                    <div class="mobile-progress-wrapper">
                        <div class="mobile-progress-bar" id="mobileProgressBar"></div>
                    </div>

                </div>

                <section class="swipe-container">

                    {{-- Swipe Cards --}}
                    <div class="swipe-cards" id="swipeCards">

                        @foreach ($rasaDummy as $index => $item)

                            <x-swipe-card
                                :title="strtoupper($item['title'])"
                                :desc="$item['description']"
                                :icon="$item['icon']"
                                :color_start="$item['gradient'][0]"
                                :color_end="$item['gradient'][1]"
                            />

                        @endforeach

                    </div>

                    {{-- Empty State --}}
                    <div
                        id="emptyState"
                        class="empty-text"
                        style="display:none;"
                    >

                        <div class="empty-icon">
                            🎉
                        </div>

                        <p>
                            Udah habis bro, lanjut ke rekomendasi!
                        </p>

                        <button class="btn-primary mt-3">
                            Lihat Hasil
                        </button>

                    </div>

                    {{-- Action Buttons --}}
                    <div class="swipe-buttons">

                        <button
                            id="dislike"
                            class="swipe-btn dislike-btn"
                            type="button"
                        >
                            <span class="material-icons-round">
                                close
                            </span>
                        </button>

                        <button
                            id="like"
                            class="swipe-btn like-btn"
                            type="button"
                        >
                            <span class="material-icons-round">
                                favorite
                            </span>
                        </button>

                    </div>

                </section>

            </div>

            {{-- =========================================
                 PANEL KANAN - HISTORY DESKTOP
            ========================================== --}}
            <div class="desktop-history-panel">

                <div class="desktop-history-card">

                    {{-- Header --}}
                    <div class="desktop-history-header">

                        <span class="material-icons-round">
                            history
                        </span>

                        <h3>
                            Riwayat Pilihan
                        </h3>

                    </div>

                    {{-- LIKE --}}
                    <div class="history-section">

                        <h4 class="section-title">
                            Disukai
                        </h4>

                        <div
                            id="desktopLikedHistory"
                            class="history-flex"
                        >
                            <p class="empty-history">
                                Belum ada rasa disukai
                            </p>
                        </div>

                    </div>

                    {{-- DISLIKE --}}
                    <div class="history-section">

                        <h4 class="section-title">
                            Dilewati
                        </h4>

                        <div
                            id="desktopDislikedHistory"
                            class="history-flex"
                        >
                            <p class="empty-history">
                                Belum ada rasa dilewati
                            </p>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- =========================================
         DRAWER ONLY MOBILE & TABLET
    ========================================== --}}
    <div class="mobile-history-wrapper">

        <x-history-drawer title="Riwayat Pilihan Kamu" />

    </div>

</main>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/swipe-resep.js') }}"></script>
@endpush