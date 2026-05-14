@extends('layouts.app')

@section('title', 'Swipe Rasa - LaperPoll')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/swipe-resep.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/history-drawer.css') }}">
@endpush

@section('content')

<main class="swipe-page font-jakarta">

    <x-navbar :backUrl="route('pencarian.resep')" />

    <div class="swipe-wrapper">

        <div class="swipe-split-layout">

            {{-- PANEL INFO --}}
            <aside class="swipe-info-panel">

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

                    {{-- PROGRESS --}}
                    <div class="progress-box">

                        <div class="progress-label">

                            <span>
                                Batas Pilihan
                            </span>

                            <span id="counterText">
                                0 / 3
                            </span>

                        </div>

                        <div class="progress-bar-container">

                            <div
                                id="progressBar"
                                class="progress-bar"
                            ></div>

                        </div>

                    </div>

                    {{-- TIPS --}}
                    <div class="tips-box">

                        <h4 class="tips-title">

                            <span class="material-icons-round">
                                lightbulb
                            </span>

                            Tips

                        </h4>

                        <ul class="tips-list">

                            <li>
                                ➡ Swipe kanan untuk suka
                            </li>

                            <li>
                                ⬅ Swipe kiri untuk skip
                            </li>

                        </ul>

                    </div>

                </div>

            </aside>

            {{-- PANEL SWIPE --}}
            <section class="swipe-interaction-panel">

                {{-- MOBILE HEADER --}}
                <div class="mobile-info-header">

                    <span class="mobile-badge">
                        Pilih 3 Rasa
                    </span>

                    <h1 class="mobile-title">
                        Swipe Rasa Favorit
                    </h1>

                    <div class="mobile-progress-wrapper">

                        <div
                            id="mobileProgressBar"
                            class="mobile-progress-bar"
                        ></div>

                    </div>

                </div>

                {{-- SWIPE AREA --}}
                <div class="swipe-container">

                    {{-- SWIPE CARDS --}}
                    <div
                        id="swipeCards"
                        class="swipe-cards"
                    >

                        @foreach ($rasaDummy as $item)

                            <x-swipe-card
                                :title="strtoupper($item['title'])"
                                :desc="$item['description']"
                                :icon="$item['icon']"
                                :color_start="$item['gradient'][0]"
                                :color_end="$item['gradient'][1]"
                            />

                        @endforeach

                    </div>

                    {{-- EMPTY STATE --}}
                    <div
                        id="emptyState"
                        class="empty-text"
                        style="display: none;"
                    >

                        <div class="empty-icon">
                            🎉
                        </div>

                        <p>
                            Udah habis bro, lanjut ke rekomendasi!
                        </p>

                        <button
                            id="lihatHasilBtn"
                            class="btn-primary mt-3"
                            type="button"
                        >
                            Lihat Hasil
                        </button>

                    </div>

                    {{-- ACTION BUTTONS --}}
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

                </div>

            </section>

            {{-- PANEL HISTORY DESKTOP --}}
            <aside class="desktop-history-panel">

                <div class="desktop-history-card">

                    {{-- HEADER --}}
                    <div class="desktop-history-header">

                        <span class="material-icons-round">
                            history
                        </span>

                        <h3>
                            Riwayat Pilihan
                        </h3>

                    </div>

                    {{-- DISUKAI --}}
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

                    {{-- DILEWATI --}}
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

            </aside>

        </div>

    </div>

    {{-- MOBILE DRAWER --}}
    <div class="mobile-history-wrapper">

        <x-history-drawer title="Riwayat Pilihan Kamu" />

    </div>

</main>

@endsection

@push('scripts')
<script src="{{ asset('js/pages/swipe-resep.js') }}"></script>
@endpush