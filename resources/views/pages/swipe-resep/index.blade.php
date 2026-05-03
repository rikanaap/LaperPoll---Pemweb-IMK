@extends('layouts.app')

@section('title', 'Swipe Rasa')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/swipe-resep.css') }}">
@endpush

@section('content')

<main class="swipe-page font-jakarta">
    <x-navbar />

    <div class="swipe-wrapper">
        <div class="swipe-split-layout">
            
            {{-- Panel Kiri (Desktop/Tablet) --}}
            <div class="swipe-info-panel">
                <div class="info-card">
                    <div class="info-header">
                        <span class="material-icons-round info-icon">tune</span>
                        <span class="badge-tag">LaperPoll</span>
                    </div>
                    
                    <h2 class="info-title">Temukan Resep Berdasarkan Selera Rasa</h2>
                    <p class="info-desc">
                        Pilih 3 rasa favoritmu hari ini agar sistem kami dapat merekomendasikan 
                        resep yang paling pas dan menggugah selera!
                    </p>

                    <div class="divider-line"></div>

                    <div class="progress-box">
                        <div class="progress-label">
                            <span class="text-slate-500 font-medium">Batas Pilihan</span>
                            <span class="text-orange-500 font-bold" id="counterText">0 / 3</span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar" id="progressBar"></div>
                        </div>
                    </div>

                    <div class="tips-box">
                        <h4 class="tips-title">
                            <span class="material-icons-round">lightbulb</span> Panduan Singkat
                        </h4>
                        <ul class="tips-list">
                            <li><span>&#9656;</span> <strong>Swipe Kanan</strong> jika kamu menyukai rasa tersebut.</li>
                            <li><span>&#9656;</span> <strong>Swipe Kiri</strong> untuk melewati pilihan.</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Panel Kanan (Interaction) --}}
            <div class="swipe-interaction-panel">
                <div class="mobile-info-header">
                    <span class="mobile-badge">Pilih 3 Rasa</span>
                    <h1 class="mobile-title">Pilih Berdasarkan Rasa</h1>
                    <div class="mobile-progress-wrapper">
                        <div class="mobile-progress-bar" id="mobileProgressBar"></div>
                    </div>
                </div>

                <section class="swipe-container">
                    <div class="swipe-cards" id="swipeCards">
                        @php
                            $colors = [
                                ['#475569', '#1e293b'], // Asin
                                ['#ef4444', '#b91c1c'], // Pedas
                                ['#f97316', '#c2410c'], // Gurih
                                ['#10b981', '#047857'], // Sehat / Umum
                            ];
                        @endphp

                        @forelse ($listRasa as $index => $item)
                            @php
                                $colorPair = $colors[$index % count($colors)];
                            @endphp
                            
                            <div class="swipe-card" style="background: linear-gradient(135deg, {{ $colorPair[0] }} 0%, {{ $colorPair[1] }} 100%);">
                                <div class="swipe-icon-wrapper">
                                    <span class="material-icons-round">
                                        @if(strtolower($item->title) == 'pedas') local_fire_department
                                        @elseif(strtolower($item->title) == 'manis') cake
                                        @else restaurant
                                        @endif
                                    </span>
                                </div>
                                <div class="card-body">
                                    <h3 class="swipe-title">{{ strtoupper($item->title) }}</h3>
                                    <p class="swipe-desc">{{ $item->description }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-slate-500">Data filter rasa belum tersedia.</p>
                        @endforelse
                    </div>

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