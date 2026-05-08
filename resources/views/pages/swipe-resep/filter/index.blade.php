@extends('layouts.app')

@section('title', 'Hasil Rekomendasi Resep')

@push('styles')
    {{-- Memanggil CSS yang kita buat di bawah --}}
    <link rel="stylesheet" href="{{ asset('css/pages/filter-resep-swipe.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/resep-card.css') }}">
@endpush

@section('content')
<main class="filter-page font-jakarta">

    {{-- NAVBAR: Menempel penuh di atas --}}
    <x-navbar :backUrl="route('swipe.rasa')" />

    {{-- CONTAINER KONTEN --}}
    <div class="main-layout">
        
        {{-- SIDEBAR: Riwayat Pilihan --}}
        <aside class="sidebar-filter">
            <div class="sidebar-header">
                <h2>Riwayat Pilihan</h2>
                <p class="text-muted mt-1">Bahan atau rasa yang sedang Anda saring</p>
            </div>
            
            <div class="selected-chips-wrapper">
                {{-- Chip Dinamis --}}
                <div class="chip">
                    <span>Ayam</span>
                    <span class="material-icons-round chip-close">close</span>
                </div>
                <div class="chip">
                    <span>Apel</span>
                    <span class="material-icons-round chip-close">close</span>
                </div>
            </div>

            <div class="filter-info-box">
                <span class="material-icons-round">info</span>
                <p>Ubah pilihan pada menu sebelumnya untuk mengubah hasil saringan.</p>
            </div>
        </aside>

        {{-- CONTENT: Daftar Resep --}}
        <section class="content-section">
            <div class="content-header">
                <p class="result-info-text">
                    @if(count($resepList) > 0)
                        Terdapat {{ count($resepList) }} resep pilihan untuk bahan yang tersedia:
                    @endif
                </p>
            </div>

            <div class="resep-container">
                @forelse ($resepList as $resep)
                    <x-resep-card :resep="$resep" />
                @empty
                    <div class="result-placeholder" style="display: block;">
                        <span class="material-icons-round">restaurant_menu</span>
                        <h3>Belum ada hasil</h3>
                        <p>Silakan lakukan swipe pada resep terlebih dahulu</p>
                    </div>
                @endforelse
            </div>
        </section>

    </div>
</main>
@endsection