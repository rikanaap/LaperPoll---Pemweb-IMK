@extends('layouts.app')

@section('title', 'Kulkas Digital - LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/kulkas-digital.css') }}">
@endpush

@section('content')
<main class="kd-main">

    <x-navbar :back="true"></x-navbar>

    {{-- HEADER --}}
    <div class="kd-header">
        <h1 class="kd-title font-jakarta font-bold">Kulkas Digital</h1>
        <a href="{{ route('kulkas.tambah') }}" class="kd-add-btn" aria-label="Tambah bahan">
            <span class="material-icons-round">add</span>
        </a>
    </div>

    {{-- FLASH --}}
    @if(session('success'))
        <div class="kd-flash">
            <span class="material-icons-round">check_circle</span>
            <span class="font-jakarta text-body">{{ session('success') }}</span>
        </div>
    @endif

    {{-- SEARCH --}}
    <div class="input">
        <span class="material-icons-round">search</span>
        <input type="text" id="kdSearch" class="input-data font-jakarta text-body"
               placeholder="Cari bahan di kulkas..." autocomplete="off">
    </div>

    {{-- FILTER --}}
    <div class="kd-filters">
        <button class="kd-chip active" data-filter="semua">Semua</button>
        <button class="kd-chip" data-filter="tersedia">Tersedia</button>
        <button class="kd-chip" data-filter="hampir-habis">Hampir Habis</button>
    </div>

    {{-- GRID BAHAN --}}
    <section class="kd-grid" id="kdGrid">
        @forelse($grouped as $item)
            @php
                $bc = match($item['status']) {
                    'tersedia'     => 'badge-ok',
                    'hampir-habis' => 'badge-warn',
                    default        => 'badge-exp',
                };
                $bl = match($item['status']) {
                    'tersedia'     => 'Tersedia',
                    'hampir-habis' => 'Hampir Habis',
                    default        => 'Expired',
                };
            @endphp
            <div class="kd-card"
                 data-status="{{ $item['status'] }}"
                 data-nama="{{ strtolower($item['nama']) }}">

                {{-- Collapsed --}}
                <div class="kd-collapsed">
                    <p class="kd-nama font-jakarta font-semibold">{{ $item['nama'] }}</p>
                    <p class="kd-sub font-jakarta font-regular">
                        {{ $item['pembelian'][0]['jumlah'] }}
                        @if($item['pembelian'][0]['sisa_hari'] !== null)
                            &nbsp;·&nbsp;
                            {{ $item['pembelian'][0]['sisa_hari'] > 0
                                ? $item['pembelian'][0]['sisa_hari'].' hari'
                                : 'Habis' }}
                        @endif
                    </p>
                    <div class="kd-card-footer">
                        <span class="kd-badge {{ $bc }} font-jakarta font-medium">{{ $bl }}</span>
                        @if(count($item['pembelian']) > 1)
                            <span class="kd-more font-jakarta">
                                +{{ count($item['pembelian']) - 1 }}
                                <span class="material-icons-round kd-arrow">expand_more</span>
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Expanded --}}
                <div class="kd-expanded">
                    <div class="kd-exp-header">
                        <p class="kd-nama font-jakarta font-semibold">{{ $item['nama'] }}</p>
                        <span class="material-icons-round kd-close">expand_less</span>
                    </div>

                    @foreach($item['pembelian'] as $i => $beli)
                        <div class="kd-beli-item">
                            <div class="kd-beli-row">
                                <span class="kd-beli-label font-jakarta font-bold">Pembelian {{ $i + 1 }}</span>
                                <form action="{{ route('kulkas.destroy', $beli['id']) }}"
                                      method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="kd-del-btn"
                                        onclick="return confirm('Hapus pembelian ini?')">
                                        <span class="material-icons-round">delete_outline</span>
                                    </button>
                                </form>
                            </div>
                            <p class="kd-beli-info font-jakarta font-regular">
                                <span class="material-icons-round kd-iicon">scale</span>
                                {{ $beli['jumlah'] }}
                            </p>
                            <p class="kd-beli-info font-jakarta font-regular">
                                <span class="material-icons-round kd-iicon">shopping_bag</span>
                                Dibeli: {{ $beli['bought_date'] ?? '-' }}
                            </p>
                            @if($item['has_expiry'] && $beli['expired_date'])
                                <p class="kd-beli-info font-jakarta font-regular
                                    {{ ($beli['sisa_hari'] !== null && $beli['sisa_hari'] <= 3) ? 'kd-warn-text' : '' }}">
                                    <span class="material-icons-round kd-iicon">event_busy</span>
                                    Expired: {{ $beli['expired_date'] }}
                                    @if($beli['sisa_hari'] !== null)
                                        ({{ $beli['sisa_hari'] > 0 ? $beli['sisa_hari'].' hari lagi' : 'sudah habis' }})
                                    @endif
                                </p>
                            @endif
                        </div>
                        @if(!$loop->last)<hr class="kd-divider">@endif
                    @endforeach
                </div>

            </div>
        @empty
            <div class="kd-empty">
                <span class="material-icons-round kd-empty-icon">kitchen</span>
                <p class="font-jakarta font-semibold kd-empty-title">Kulkas masih kosong</p>
                <p class="font-jakarta font-regular kd-empty-sub">
                    Tambahkan bahan dengan tombol <strong>+</strong> di atas
                </p>
            </div>
        @endforelse
    </section>

    {{-- REKOMENDASI RESEP --}}
    @if(count($rekomendasi) > 0)
    <section class="kd-resep">
        <div class="kd-resep-header">
            <span class="kd-resep-sparkle">✨</span>
            <h2 class="kd-resep-title font-jakarta font-semibold">Resep dari bahan yang ada</h2>
        </div>

        <div class="kd-resep-list">
            @foreach($rekomendasi as $resep)
                <div class="kd-resep-item {{ $resep['lengkap'] ? 'resep-lengkap' : '' }}"
                     data-bahan-kurang="{{ implode(', ', $resep['bahan_kurang']->toArray()) }}">

                    <div class="kd-resep-info">
                        <p class="kd-resep-nama font-jakarta font-medium">{{ $resep['title'] }}</p>
                        @if(!$resep['lengkap'])
                            <p class="kd-resep-kurang font-jakarta font-regular">
                                Kurang: {{ implode(', ', $resep['bahan_kurang']->toArray()) }}
                            </p>
                        @endif
                    </div>

                    <div class="kd-resep-right">
                        <span class="kd-resep-badge {{ $resep['lengkap'] ? 'badge-resep-lengkap' : 'badge-resep-partial' }} font-jakarta font-bold">
                            {{ $resep['bahan_ada'] }}/{{ $resep['total_bahan'] }}
                            @if($resep['lengkap']) ✓ @endif
                        </span>
                        @if(!$resep['lengkap'])
                            <button class="kd-resep-detail-btn font-jakarta"
                                    data-kurang="{{ implode(', ', $resep['bahan_kurang']->toArray()) }}"
                                    data-nama="{{ $resep['title'] }}">
                                <span class="material-icons-round">info_outline</span>
                            </button>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>
    </section>
    @endif

</main>

{{-- MODAL BAHAN KURANG --}}
<div id="modalBahanKurang" style="display:none;">
    <div class="modal-overlay" id="modalKurangOverlay"></div>
    <div class="modal-box">
        <div class="modal-resep-icon">
            <span class="material-icons-round">shopping_cart</span>
        </div>
        <h3 class="modal-title font-jakarta font-bold" id="modalKurangTitle"></h3>
        <p class="modal-desc font-jakarta font-regular">Bahan yang masih kurang:</p>
        <ul class="modal-kurang-list" id="modalKurangList"></ul>
        <button class="modal-btn-confirm font-jakarta font-bold" id="modalKurangClose"
                style="width:100%; margin-top:0.5rem;">
            Tutup
        </button>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/kulkas-digital.js') }}"></script>
@endpush