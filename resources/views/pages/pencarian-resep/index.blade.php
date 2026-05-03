@extends('layouts.app')

@section('title', 'Pencarian Resep')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/pencarian-resep.css') }}">
@endpush

@section('content')

<main class="search-page font-jakarta">

    <x-navbar />

    <section class="search-layout">

        {{-- LEFT SIDE --}}
        <aside class="search-sidebar">

            {{-- Search Box --}}
            <div class="input" id="searchWrapper">
                <span class="material-icons-round">search</span>
                <input type="text" id="searchInput" class="input-data" placeholder="Cari Bahan / Nama Resep">
            </div>

            {{-- Title --}}
            <p class="text-body font-medium section-title">Bahan Populer Minggu Ini</p>

            {{-- Bahan List --}}
            <section class="bahan-wrapper">
                <div class="bahan-list flex flex-col gap-5">
                    
                    {{-- Pengelompokan Berdasarkan Abjad (Grup A, B, C, dst) --}}
                    @forelse($bahans as $huruf => $kelompokBahan)
                        <div class="bahan-group flex flex-col gap-3">
                            <span class="group-letter">{{ $huruf }}</span>
                            
                            @foreach($kelompokBahan as $bahan)
                                <div class="bahan-item flex justify-between items-center">
                                    <div class="bahan-left flex items-center gap-3">
                                        <div class="bahan-icon flex items-center justify-center">
                                            <span class="material-icons-round">restaurant</span>
                                        </div>
                                        <span class="bahan-nama font-medium text-sm text-neutral-900">{{ $bahan->nama }}</span>
                                    </div>
                                    <input type="checkbox" name="bahan[]" value="{{ $bahan->id }}">
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <p class="text-caption text-center">Tidak ada bahan yang ditemukan.</p>
                    @endforelse

                </div>
            </section>

            {{-- Pesan Info Bahan Terpilih --}}
            <div class="selected-info" id="selectedInfo" style="display: none;">
                0 bahan terpilih
            </div>

            {{-- Tombol Aksi (Hapus & Terapkan) --}}
            <div class="action-buttons-wrapper">
                <button id="hapusSemuaBtn" class="action-btn hapus-btn" type="button" disabled>Hapus Semua</button>
                <button id="terapkanBtn" class="action-btn terapkan-btn disabled" type="button" disabled>Terapkan</button>
            </div>

            {{-- Divider --}}
            <div class="divider-wrap">
                <div class="horizontal-line flex-1"></div>
                <span class="text-caption atau-text">Atau</span>
                <div class="horizontal-line flex-1"></div>
            </div>

            {{-- Swipe Button --}}
            <a href="{{ route('swipe.rasa') }}" class="swipe-btn">
                <span class="material-icons-round">swap_horiz</span>
                <span>Swipe Untuk Mencari</span>
            </a>

        </aside>

        {{-- RIGHT SIDE --}}
        <section class="search-result">
            <div class="result-placeholder">
                <span class="material-icons-round">restaurant_menu</span>
                <h3>Rekomendasi Resep</h3>
                <p>Pilih bahan terlebih dahulu untuk melihat hasil resep.</p>
            </div>
        </section>

    </section>

</main>

@endsection

@push('scripts')
<script src="{{ asset('js/pages/pencarian-resep.js') }}"></script>
@endpush