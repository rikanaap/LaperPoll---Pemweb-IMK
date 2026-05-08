@extends('layouts.app')

@section('title', 'Swipe Rasa - LaperPoll')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/swipe-resep.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/history-drawer.css') }}">
@endpush

@section('content')
<main class="swipe-page font-jakarta">

    {{-- Navbar --}}
    <x-navbar :backUrl="route('pencarian.resep')" />

    <div class="swipe-wrapper">
        <div class="swipe-split-layout">
            
            {{-- Panel Kiri: Informasi Status --}}
            <div class="swipe-info-panel">
                <div class="info-card">
                    <div class="info-header">
                        <span class="material-icons-round info-icon">tune</span>
                        <span class="badge-tag">LaperPoll</span>
                    </div>
                    
                    <h2 class="info-title">
                        Temukan Resep Berdasarkan Selera Rasa
                    </h2>

                    <p class="info-desc">
                        Pilih 3 rasa favoritmu hari ini agar sistem kami dapat 
                        merekomendasikan resep terbaik untukmu.
                    </p>

                    <div class="divider-line"></div>

                    <div class="progress-box">
                        <div class="progress-label">
                            <span>Batas Pilihan</span>
                            <span id="counterText">0 / 3</span>
                        </div>

                        <div class="progress-bar-container">
                            <div class="progress-bar" id="progressBar"></div>
                        </div>
                    </div>

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
            </div>

            {{-- Panel Kanan: Area Interaction Swipe --}}
            <div class="swipe-interaction-panel">
                
                {{-- Progress Mobile Header --}}
                <div class="mobile-info-header">
                    <span class="mobile-badge">Pilih 3 Rasa</span>
                    <h1 class="mobile-title">Swipe Rasa Favorit</h1>

                    <div class="mobile-progress-wrapper">
                        <div class="mobile-progress-bar" id="mobileProgressBar"></div>
                    </div>
                </div>

                <section class="swipe-container">
                    <div class="swipe-cards" id="swipeCards">
                        @php
                            // Palet warna estetik untuk kartu
                            $colors = [
                                ['#b6ff2e', '#ff7a00'],
                                ['#ef4444', '#b91c1c'],
                                ['#10b981', '#047857'],
                                ['#6366f1', '#4338ca'],
                                ['#f59e0b', '#b45309'],
                            ];
                        @endphp

                        {{-- Loop data asli dari Controller --}}
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

                    {{-- Tampilan saat kartu habis --}}
                    <div id="emptyState" class="empty-text" style="display:none;">
                        <div class="empty-icon">🎉</div>
                        <p>Udah habis bro, lanjut ke rekomendasi!</p>
                        <button class="btn-primary mt-3">Lihat Hasil</button>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="swipe-buttons">
                        <button id="dislike" class="swipe-btn dislike-btn" type="button">
                            <span class="material-icons-round">close</span>
                        </button>

                        <button id="like" class="swipe-btn like-btn" type="button">
                            <span class="material-icons-round">favorite</span>
                        </button>
                    </div>
                </section>
            </div>
        </div>
    </div>

    {{-- Komponen Drawer Riwayat --}}
    <x-history-drawer title="Riwayat Pilihan Kamu" />

</main>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/swipe-resep.js') }}"></script>
@endpush