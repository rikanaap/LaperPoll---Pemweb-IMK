@extends('layouts.app')

@section('title', 'Kulkas Digital - LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/kulkas-digital.css') }}">
@endpush

@section('content')
<main class="main-content flex flex-col">

    {{-- NAVBAR — pakai komponen yang sama dengan temenmu --}}
    <x-navbar :back="true"></x-navbar>

    {{-- HEADER --}}
    <section class="kulkas-header flex flex-row">
        <h1 class="font-jakarta font-bold text-h5 kulkas-title">Kulkas Digital</h1>
        <a href="{{ route('kulkas.tambah') }}" class="kulkas-add-btn" aria-label="Tambah bahan">
            <span class="material-icons-round">add</span>
        </a>
    </section>

    {{-- SEARCH BAR (revisi dosen) --}}
    <div class="kulkas-search">
        <div class="input">
            <span class="material-icons-round">search</span>
            <input type="text" id="searchKulkas"
                class="input-data font-jakarta text-body"
                placeholder="Cari bahan di kulkas..."
                autocomplete="off">
        </div>
    </div>

    {{-- FILTER TABS — 3 tab (Expired dihapus per revisi dosen) --}}
    <div class="filter-tabs flex flex-row" role="tablist">
        <button class="filter-tab active font-jakarta font-semibold text-body"
            data-filter="semua" role="tab" aria-selected="true">Semua</button>
        <button class="filter-tab font-jakarta font-medium text-body"
            data-filter="tersedia" role="tab" aria-selected="false">Tersedia</button>
        <button class="filter-tab font-jakarta font-medium text-body"
            data-filter="hampir-habis" role="tab" aria-selected="false">Hampir Habis</button>
    </div>

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div class="flash-success font-jakarta text-body" id="flashMsg">
            <span class="material-icons-round">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- BAHAN GRID --}}
    <section class="bahan-grid" id="bahanGrid">
        @forelse($grouped as $item)
            @php
                $badgeClass = match($item['status']) {
                    'tersedia'     => 'badge-tersedia',
                    'hampir-habis' => 'badge-hampir',
                    default        => 'badge-expired',
                };
                $badgeLabel = match($item['status']) {
                    'tersedia'     => 'Tersedia',
                    'hampir-habis' => 'Hampir Habis',
                    default        => 'Habis',
                };
            @endphp

            <div class="bahan-card"
                 data-status="{{ $item['status'] }}"
                 data-nama="{{ strtolower($item['nama']) }}">

                {{-- COLLAPSED VIEW --}}
                <div class="card-collapsed">
                    <h2 class="font-jakarta font-semibold text-title2 text-secondary-normal">
                        {{ $item['nama'] }}
                    </h2>
                    <p class="font-jakarta font-regular text-caption text-primary-darker">
                        {{ $item['pembelian'][0]['jumlah'] }}
                        @if($item['pembelian'][0]['sisa_hari'] !== null)
                            · {{ $item['pembelian'][0]['sisa_hari'] > 0
                                ? $item['pembelian'][0]['sisa_hari'].' hari'
                                : 'Habis' }}
                        @endif
                    </p>
                    <div class="card-footer flex flex-row">
                        <span class="status-badge {{ $badgeClass }} font-jakarta font-medium text-caption">
                            {{ $badgeLabel }}
                        </span>
                        @if(count($item['pembelian']) > 1)
                            <span class="expand-indicator font-jakarta text-caption text-primary-darker">
                                {{ count($item['pembelian']) }}x
                                <span class="material-icons-round expand-arrow">expand_more</span>
                            </span>
                        @endif
                    </div>
                </div>

                {{-- EXPANDED VIEW --}}
                <div class="card-expanded">
                    <div class="expanded-header flex flex-row">
                        <h2 class="font-jakarta font-semibold text-title2 text-secondary-normal">
                            {{ $item['nama'] }}
                        </h2>
                        <span class="material-icons-round expand-close">expand_less</span>
                    </div>

                    @foreach($item['pembelian'] as $i => $beli)
                        <div class="pembelian-item flex flex-col gap-1">
                            <div class="pembelian-row flex flex-row">
                                <span class="font-jakarta font-semibold text-caption pembelian-label">
                                    Pembelian {{ $i + 1 }}
                                </span>
                                <form action="{{ route('kulkas.destroy', $beli['id']) }}"
                                      method="POST" class="hapus-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="hapus-btn"
                                        onclick="return confirm('Hapus pembelian ini?')"
                                        aria-label="Hapus pembelian">
                                        <span class="material-icons-round">delete_outline</span>
                                    </button>
                                </form>
                            </div>
                            <p class="font-jakarta font-regular text-caption text-secondary-normal">
                                <span class="material-icons-round icon-sm">scale</span>
                                {{ $beli['jumlah'] }}
                            </p>
                            <p class="font-jakarta font-regular text-caption text-primary-darker">
                                <span class="material-icons-round icon-sm">shopping_bag</span>
                                Dibeli: {{ $beli['bought_date'] ?? '-' }}
                            </p>
                            @if($item['has_expiry'] && $beli['expired_date'])
                                <p class="font-jakarta font-regular text-caption
                                    {{ ($beli['sisa_hari'] !== null && $beli['sisa_hari'] <= 3)
                                        ? 'text-accent-normal' : 'text-primary-darker' }}">
                                    <span class="material-icons-round icon-sm">event_busy</span>
                                    Expired: {{ $beli['expired_date'] }}
                                    @if($beli['sisa_hari'] !== null)
                                        <em>({{ $beli['sisa_hari'] > 0
                                            ? $beli['sisa_hari'].' hari lagi'
                                            : 'Sudah habis' }})</em>
                                    @endif
                                </p>
                            @endif
                        </div>
                        @if(!$loop->last)
                            <hr class="pembelian-divider">
                        @endif
                    @endforeach
                </div>

            </div>
        @empty
            <div class="empty-state flex flex-col gap-2">
                <span class="material-icons-round empty-icon">kitchen</span>
                <p class="font-jakarta font-medium text-body text-secondary-normal">
                    Kulkas masih kosong
                </p>
                <p class="font-jakarta font-regular text-caption text-primary-darker">
                    Tambahkan bahan dengan tombol + di atas
                </p>
            </div>
        @endforelse
    </section>

    {{-- RESEP SUGGESTION --}}
    @if($grouped->count() > 0)
    <section class="resep-suggestion flex flex-col gap-3">
        <div class="resep-suggestion-header flex flex-row gap-2">
            <span class="sparkle-icon">✨</span>
            <h3 class="font-jakarta font-semibold text-title2 text-secondary-normal">
                Resep dari bahan yang ada
            </h3>
        </div>
        <div class="resep-list flex flex-col gap-2">
            <div class="resep-suggestion-item flex flex-row">
                <p class="font-jakarta font-medium text-body text-secondary-normal resep-nama">Scrambled Egg</p>
                <span class="resep-badge badge-complete font-jakarta font-bold text-caption">3/3 bahan ✓</span>
            </div>
            <div class="resep-suggestion-item flex flex-row">
                <p class="font-jakarta font-medium text-body text-secondary-normal resep-nama">Nasi Goreng</p>
                <span class="resep-badge badge-partial font-jakarta font-bold text-caption">4/6 bahan</span>
            </div>
            <div class="resep-suggestion-item flex flex-row">
                <p class="font-jakarta font-medium text-body text-secondary-normal resep-nama">Mie Goreng Spesial</p>
                <span class="resep-badge badge-partial font-jakarta font-bold text-caption">3/4 bahan</span>
            </div>
        </div>
    </section>
    @endif

</main>
@endsection

@push('scripts')
    <script src="{{ asset('js/kulkas-digital.js') }}"></script>
@endpush