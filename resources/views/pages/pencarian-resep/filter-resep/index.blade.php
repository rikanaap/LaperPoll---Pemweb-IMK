@extends('layouts.app')

@section('title', 'Filter Resep')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/filter-pencarian-resep.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/resep-card.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/chips.css') }}">
@endpush

@section('content')

<main class="filter-page font-jakarta">

    <x-navbar :back-url="route('pencarian.resep')" />

    <div class="main-layout">

        <aside class="sidebar-filter">

            <div class="sidebar-header">

                <h2>
                    Riwayat Pilihan
                </h2>

                <p class="text-muted">
                    Bahan yang sedang digunakan untuk mencari resep
                </p>

            </div>

            <div
                id="chipsContainer"
                class="selected-chips-wrapper"
            ></div>

            <div class="filter-info-box">

                <span class="material-icons-round">
                    info
                </span>

                <p>
                    Kamu bisa menghapus bahan untuk memperbarui hasil resep.
                </p>

            </div>

        </aside>

        <section class="content-section">

            <div class="content-header">

                <p
                    id="resultInfo"
                    class="result-info-text"
                >
                    Menampilkan resep...
                </p>

            </div>

            <div
                id="loadingState"
                class="loading-state"
            >

                <div class="loading-spinner"></div>

                <p>
                    Mencari resep terbaik...
                </p>

            </div>

            <div
                id="resepList"
                class="resep-container hidden"
            >

                @foreach($reseps as $resep)

                    <x-resep-card :resep="$resep" />

                @endforeach

            </div>

            <div
                id="emptyState"
                class="result-placeholder hidden"
            >

                <span class="material-icons-round">
                    restaurant_menu
                </span>

                <h3>
                    Belum ada hasil
                </h3>

                <p>
                    Pilih bahan dulu ya
                </p>

            </div>

        </section>

    </div>

</main>

@endsection

@push('scripts')
<script src="{{ asset('js/pages/filter-pencarian-resep.js') }}"></script>
@endpush