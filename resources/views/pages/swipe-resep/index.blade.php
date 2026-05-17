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
            {{-- LEFT PANEL --}}
            <aside class="swipe-info-panel">
                <div class="info-card">
                    <div class="info-header">
                        <span class="material-icons-round info-icon">tune</span>
                        <span class="badge-tag">LaperPoll</span>
                    </div>

                    <h2 class="info-title">Temukan Resep Berdasarkan Selera Rasa</h2>
                    <p class="info-desc">
                        Swipe kanan untuk menyukai rasa, swipe kiri untuk melewati. 
                        Pilih 3 rasa favoritmu untuk mendapatkan rekomendasi resep terbaik.
                    </p>

                    <div class="divider-line"></div>

                    {{-- PROGRESS --}}
                    <div class="progress-box">
                        <div class="progress-label">
                            <span>Batas Pilihan</span>
                            <span id="counterText">0 / 3</span>
                        </div>
                        <div class="progress-bar-container">
                            <div id="progressBar" class="progress-bar"></div>
                        </div>
                    </div>

                    {{-- TIPS --}}
                    <div class="tips-box">
                        <h4 class="tips-title">
                            <span class="material-icons-round">lightbulb</span> Tips
                        </h4>
                        <ul class="tips-list">
                            <li>➡ Swipe kanan untuk suka</li>
                            <li>⬅ Swipe kiri untuk skip</li>
                        </ul>
                    </div>
                </div>
            </aside>

            {{-- CENTER PANEL --}}
            <section class="swipe-interaction-panel">
                <div class="mobile-info-header">
                    <span class="mobile-badge">Pilih 3 Rasa</span>
                    <h1 class="mobile-title">Swipe Rasa Favorit</h1>
                    <div class="mobile-progress-wrapper">
                        <div id="mobileProgressBar" class="mobile-progress-bar"></div>
                    </div>
                </div>

                <div class="swipe-container">
                    <div id="swipeCards" class="swipe-cards">
                        <div id="emptyState" class="empty-state" style="display:none;">
                            Tidak ada rasa tersedia
                        </div>
                    </div>

                    <div class="swipe-buttons">
                        <button id="dislikeBtn" class="swipe-btn dislike-btn" type="button">
                            <span class="material-icons-round">close</span>
                        </button>
                        <button id="likeBtn" class="swipe-btn like-btn" type="button">
                            <span class="material-icons-round">favorite</span>
                        </button>
                    </div>
                </div>
            </section>

            {{-- RIGHT PANEL (DESKTOP HISTORY) --}}
            <aside class="desktop-history-panel">
                <div class="desktop-history-card">
                    <div class="desktop-history-header">
                        <span class="material-icons-round">history</span>
                        <h3>Riwayat Pilihan</h3>
                    </div>

                    <div class="history-section">
                        <h4 class="section-title">DISUKAI</h4>
                        <div id="likedContainer" class="history-flex">
                            <p class="empty-history">Belum ada rasa favorit</p>
                        </div>
                    </div>

                    <div class="history-section">
                        <h4 class="section-title">DILEWATI</h4>
                        <div id="dislikedContainer" class="history-flex">
                            <p class="empty-history">Belum ada rasa dilewati</p>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    {{-- MOBILE DRAWER OVERLAY --}}
    <div id="drawerOverlay" class="drawer-overlay"></div>

    {{-- MOBILE HISTORY DRAWER --}}
    <div class="mobile-history-wrapper">
        <div id="historyDrawer" class="history-drawer">
            <div id="drawerHeader" class="drawer-header">
                <div class="drawer-handle"></div>
                <div class="drawer-info">
                    <span class="material-icons-round">history</span>
                    <span>Riwayat Pilihan Kamu</span>
                    <span id="drawerArrow" class="material-icons-round arrow-icon">expand_less</span>
                </div>
            </div>

            <div class="drawer-content">
                <div class="history-section">
                    <h4 class="section-title">DISUKAI</h4>
                    <div id="mobileLikedContainer" class="history-flex">
                        <p class="empty-history">Belum ada rasa favorit</p>
                    </div>
                </div>

                <div class="history-section">
                    <h4 class="section-title">DILEWATI</h4>
                    <div id="mobileDislikedContainer" class="history-flex">
                        <p class="empty-history">Belum ada rasa dilewati</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    window.swipeConfig = {
        apiUrl: "{{ route('api.swipe.rasa') }}",
        redirectUrl: "{{ route('swipe.filter') }}"
    };
</script>
<script src="{{ asset('js/pages/swipe-resep.js') }}"></script>
@endpush