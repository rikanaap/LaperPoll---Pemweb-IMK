@extends('layouts.app')

@section('title', 'Swipe Rasa')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/swipe-resep.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/navbar.css') }}">
@endpush

@section('content')
<main class="swipe-page font-jakarta">
     <nav class="navbar">

        <a href="{{ route('pencarian.resep') }}" class="back-btn">
            <span class="material-icons-round text-h4 text-accent-normal">
                arrow_back
            </span>
        </a>

        <img
            src="{{ asset('assets/images/Logo_Laperpoll.png') }}"
            class="logo"
            alt="Logo"
        >

        <a href="#">
            <img
                src="{{ asset('assets/images/Image_DummyProfile.png') }}"
                class="profile"
                alt="Profile"
            >
        </a>

    </nav>

    <div class="swipe-wrapper">
        <div class="swipe-split-layout">
            
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

            <div class="swipe-interaction-panel">
                <div class="mobile-info-header">
                    <span class="mobile-badge">Pilih 3 Rasa</span>
                    <h1 class="mobile-title">Swipe Rasa Favorit</h1>

                    <div class="mobile-progress-wrapper">
                        <div class="mobile-progress-bar" id="mobileProgressBar"></div>
                    </div>
                </div>

                @php
                $rasaDummy = [
                    [
                        'title' => 'Pedas',
                        'desc' => 'Cocok buat kamu yang suka tantangan rasa membara 🔥',
                        'icon' => 'local_fire_department'
                    ],
                    [
                        'title' => 'Manis',
                        'desc' => 'Rasa lembut yang bikin mood jadi lebih baik 🍰',
                        'icon' => 'cake'
                    ],
                    [
                        'title' => 'Gurih',
                        'desc' => 'Favorit semua orang, penuh cita rasa 🤤',
                        'icon' => 'ramen_dining'
                    ],
                    [
                        'title' => 'Asin',
                        'desc' => 'Rasa klasik yang nggak pernah gagal 🧂',
                        'icon' => 'restaurant'
                    ],
                    [
                        'title' => 'Sehat',
                        'desc' => 'Pilihan ringan dan bergizi untuk tubuh 🥗',
                        'icon' => 'eco'
                    ],
                ];
                @endphp

                <section class="swipe-container">
                    <div class="swipe-cards" id="swipeCards">
                        @foreach ($rasaDummy as $index => $item)
                            @php
                                $colors = [
                                    ['#b6ff2e', '#ff7a00'],
                                    ['#ef4444', '#b91c1c'],
                                    ['#10b981', '#047857'],
                                    ['#6366f1', '#4338ca'],
                                    ['#f59e0b', '#b45309'],
                                ];
                                $colorPair = $colors[$index % count($colors)];
                            @endphp

                            <x-swipe-card 
                                :title="strtoupper($item['title'])"
                                :desc="$item['desc']"
                                :icon="$item['icon']"
                                :color_start="$colorPair[0]"
                                :color_end="$colorPair[1]"
                            />
                        @endforeach
                    </div>

                    <p id="emptyState" class="empty-text" style="display:none;">
                        🎉 Udah habis bro, lanjut ke rekomendasi!
                    </p>

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
</main>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/swipe-resep.js') }}"></script>
@endpush