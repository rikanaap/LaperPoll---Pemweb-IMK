@extends('layouts.app')

@section('title', 'Hasil Rekomendasi Resep')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/filter-resep-swipe.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/resep-card.css') }}">
@endpush

@section('content')

<main class="filter-page font-jakarta">

    {{-- =========================================
         NAVBAR
    ========================================== --}}
    <x-navbar :back-url="route('swipe.rasa')" />

    {{-- =========================================
         MAIN LAYOUT
    ========================================== --}}
    <div class="main-layout">

        {{-- =========================================
             SIDEBAR FILTER
        ========================================== --}}
        <aside class="sidebar-filter">

            {{-- HEADER --}}
            <div class="sidebar-header">

                <h2>
                    Riwayat Pilihan
                </h2>

                <p class="text-muted mt-1">
                    Bahan atau rasa yang sedang Anda saring
                </p>

            </div>

            {{-- =========================================
                 CHIPS DINAMIS
            ========================================== --}}
            <div
                class="selected-chips-wrapper"
                id="selectedRasaContainer"
            >
            </div>

            {{-- =========================================
                 INFO BOX
            ========================================== --}}
            <div class="filter-info-box">

                <span class="material-icons-round">
                    info
                </span>

                <p>
                    Ubah pilihan pada menu sebelumnya
                    untuk mengubah hasil saringan.
                </p>

            </div>

        </aside>

        {{-- =========================================
             CONTENT SECTION
        ========================================== --}}
        <section class="content-section">

            {{-- HEADER --}}
            <div class="content-header">

                <p class="result-info-text">

                    @if(count($resepList) > 0)

                        Terdapat
                        {{ count($resepList) }}
                        resep pilihan untuk bahan yang tersedia:

                    @endif

                </p>

            </div>

            {{-- =========================================
                 RESEP LIST
            ========================================== --}}
            <div class="resep-container">

                @forelse ($resepList as $resep)

                    <x-resep-card :resep="$resep" />

                @empty

                    {{-- EMPTY STATE --}}
                    <div
                        class="result-placeholder"
                        style="display: block;"
                    >

                        <span class="material-icons-round">
                            restaurant_menu
                        </span>

                        <h3>
                            Belum ada hasil
                        </h3>

                        <p>
                            Silakan lakukan swipe
                            pada resep terlebih dahulu
                        </p>

                    </div>

                @endforelse

            </div>

        </section>

    </div>

</main>

@endsection

@push('scripts')
<script src="{{ asset('js/pages/filter-resep-swipe.js') }}"></script>
@endpush