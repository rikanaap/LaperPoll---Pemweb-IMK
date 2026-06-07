@extends('layouts.app')

@section('title', 'Kulkas Digital - LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/kulkas-digital.css') }}">
@endpush

@section('content')
<main class="kd-main">

    <x-navbar backUrl="back"></x-navbar>

    {{-- HEADER --}}
    <div class="kd-header">
        <h1 class="kd-title font-jakarta font-bold">Kulkas Digital</h1>
        <a href="{{ route('kulkas.tambah') }}" class="kd-add-btn" aria-label="Tambah bahan">
            <span class="material-icons-round">add</span>
        </a>
    </div>

    {{-- Toast ditangani global oleh x-popup-toast di layout --}}

    {{-- SEARCH --}}
    <div class="input">
        <span class="material-icons-round">search</span>
        <input type="text" id="kdSearch" class="input-data font-jakarta text-body"
               placeholder="Cari bahan di kulkas..." autocomplete="off">
    </div>

    {{-- FILTER CHIPS --}}
    <div class="kd-filters">
        <button class="kd-chip active" data-filter="semua">Semua</button>
        <button class="kd-chip" data-filter="tersedia">Tersedia</button>
        <button class="kd-chip" data-filter="hampir-habis">Hampir Habis</button>
        <button class="kd-chip kd-chip-expired" data-filter="expired">Expired</button>
    </div>

    {{-- BANNER PERINGATAN EXPIRED — hanya muncul jika ada item expired --}}
    @if($grouped->where('status', 'expired')->count() > 0)
    <div class="kd-expired-banner" id="kdExpiredBanner">
        <span class="material-icons-round">warning_amber</span>
        <div class="kd-expired-banner-text">
            <p class="font-jakarta font-semibold">
                {{ $grouped->where('status', 'expired')->count() }} bahan sudah kedaluwarsa
            </p>
            <p class="font-jakarta font-regular kd-expired-banner-sub">
                Bahan ini masih ditampilkan agar kamu bisa hapus secara sadar — bukan hilang begitu saja.
            </p>
        </div>
    </div>
    @endif

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
                $iconStatus = match($item['status']) {
                    'tersedia'     => 'check_circle',
                    'hampir-habis' => 'schedule',
                    default        => 'cancel',
                };
                $firstBeli = $item['pembelian'][0];
            @endphp

            <div class="kd-card"
                 data-status="{{ $item['status'] }}"
                 data-nama="{{ strtolower($item['nama']) }}">

                {{-- ── COLLAPSED ── --}}
                <div class="kd-collapsed">
                    <div class="kd-card-status-row">
                        <span class="kd-badge {{ $bc }} font-jakarta font-medium">
                            <span class="material-icons-round kd-badge-icon">{{ $iconStatus }}</span>
                            {{ $bl }}
                        </span>
                        @if(count($item['pembelian']) > 1)
                            <span class="kd-more font-jakarta">
                                +{{ count($item['pembelian']) - 1 }}
                                <span class="material-icons-round kd-arrow">expand_more</span>
                            </span>
                        @endif
                    </div>

                    <p class="kd-nama font-jakarta font-semibold">{{ $item['nama'] }}</p>

                    <div class="kd-info-pills">
                        {{-- Total semua pembelian --}}
                        <span class="kd-pill">
                            <span class="material-icons-round kd-pill-icon">scale</span>
                            {{ $item['stok_gram'] }} gram
                        </span>

                        {{-- Tanggal beli (untuk bahan tanpa expiry / mode beli) --}}
                        @if(!$item['has_expiry'] && $firstBeli['bought_date'])
                            <span class="kd-pill">
                                <span class="material-icons-round kd-pill-icon">shopping_bag</span>
                                {{ $firstBeli['bought_date'] }}
                            </span>
                        @endif

                        {{-- Sisa hari expired (untuk bahan dengan expiry) --}}
                        @if($item['has_expiry'] && $firstBeli['sisa_hari'] !== null)
                            <span class="kd-pill {{ $firstBeli['sisa_hari'] <= 3 ? 'kd-pill-warn' : '' }}">
                                <span class="material-icons-round kd-pill-icon">event_busy</span>
                                @if($firstBeli['sisa_hari'] > 0)
                                    {{ $firstBeli['sisa_hari'] }} hari lagi
                                @else
                                    Expired
                                @endif
                            </span>
                        @endif
                    </div>
                </div>

                {{-- ── EXPANDED ── --}}
                <div class="kd-expanded">
                    <div class="kd-exp-header">
                        <p class="kd-nama font-jakarta font-semibold">{{ $item['nama'] }}</p>
                        <span class="material-icons-round kd-close">expand_less</span>
                    </div>

                    <div style="margin-bottom:0.25rem;">
                        <span class="kd-badge {{ $bc }} font-jakarta font-medium">
                            <span class="material-icons-round kd-badge-icon">{{ $iconStatus }}</span>
                            {{ $bl }}
                        </span>
                    </div>

                    @foreach($item['pembelian'] as $i => $beli)
                        <div class="kd-beli-item">
                            <div class="kd-beli-row">
                                <span class="kd-beli-label font-jakarta font-bold">Pembelian {{ $i + 1 }}</span>
                                <form action="{{ route('kulkas.destroy', $beli['id']) }}"
                                      method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="button" class="kd-del-btn kd-hapus-btn">
                                        <span class="material-icons-round">delete_outline</span>
                                    </button>
                                </form>
                            </div>

                            <div class="kd-detail-grid">
                                <div class="kd-detail-cell">
                                    <span class="kd-detail-label font-jakarta">Jumlah</span>
                                    <span class="kd-detail-val font-jakarta font-semibold">
                                        <span class="material-icons-round kd-iicon">scale</span>
                                        {{ $beli['jumlah'] }} gram
                                    </span>
                                </div>

                                @if($beli['bought_date'])
                                <div class="kd-detail-cell">
                                    <span class="kd-detail-label font-jakarta">Tanggal Beli</span>
                                    <span class="kd-detail-val font-jakarta font-semibold">
                                        <span class="material-icons-round kd-iicon">shopping_bag</span>
                                        {{ $beli['bought_date'] }}
                                    </span>
                                </div>
                                @endif

                                @if($item['has_expiry'] && $beli['expired_date'])
                                <div class="kd-detail-cell {{ ($beli['sisa_hari'] !== null && $beli['sisa_hari'] <= 3) ? 'kd-detail-warn' : '' }}">
                                    <span class="kd-detail-label font-jakarta">Expired</span>
                                    <span class="kd-detail-val font-jakarta font-semibold">
                                        <span class="material-icons-round kd-iicon">event_busy</span>
                                        {{ $beli['expired_date'] }}
                                    </span>
                                </div>
                                @if($beli['sisa_hari'] !== null)
                                <div class="kd-detail-cell">
                                    <span class="kd-detail-label font-jakarta">Sisa</span>
                                    <span class="kd-detail-val font-jakarta font-semibold {{ $beli['sisa_hari'] <= 3 ? 'kd-warn-text' : '' }}">
                                        <span class="material-icons-round kd-iicon">hourglass_bottom</span>
                                        {{ $beli['sisa_hari'] > 0 ? $beli['sisa_hari'].' hari' : 'Sudah habis' }}
                                    </span>
                                </div>
                                @endif
                                @endif
                            </div>
                        </div>
                        @if(!$loop->last)<hr class="kd-divider">@endif
                    @endforeach
                </div>

            </div>
        @empty
            <div class="kd-empty" id="kdEmptyState">
                <span class="material-icons-round kd-empty-icon">kitchen</span>
                <p class="font-jakarta font-semibold kd-empty-title">Kulkas masih kosong</p>
                <p class="font-jakarta font-regular kd-empty-sub">
                    Tambahkan bahan dengan tombol <strong>+</strong> di atas
                </p>
            </div>
        @endforelse
    </section>

    {{-- REKOMENDASI RESEP — hanya muncul jika ada bahan di kulkas --}}
    @if(count($rekomendasi) > 0)
    <section class="kd-resep">
        <div class="kd-resep-header">
            <span class="kd-resep-sparkle">✨</span>
            <h2 class="kd-resep-title font-jakarta font-semibold">Resep dari bahan yang ada</h2>
        </div>

        <div class="kd-resep-list">
            @foreach($rekomendasi as $resep)
                @php
                    // Siapkan data bahan_detail sebagai JSON untuk dikirim ke JS
                    $bahanDetailJson  = json_encode($resep['bahan_detail']);
                    $bahanKurangNames = $resep['bahan_kurang']->map(fn($b) => $b['nama'])->join(', ');
                @endphp
                <div class="kd-resep-item"
                     data-resep-id="{{ $resep['id'] }}"
                     data-resep-nama="{{ $resep['title'] }}"
                     data-bahan-ids="{{ implode(',', $resep['bahan_ids']->toArray()) }}"
                     data-bahan-detail='{{ $bahanDetailJson }}'
                     data-lengkap="{{ $resep['lengkap'] ? '1' : '0' }}">

                    {{-- Thumbnail --}}
                    <div class="kd-resep-thumb">
                        @if($resep['thumbnail'])
                            <img src="{{ $resep['thumbnail'] }}" alt="{{ $resep['title'] }}" loading="lazy">
                        @else
                            <span class="material-icons-round">restaurant</span>
                        @endif
                    </div>

                    <div class="kd-resep-info">
                        <p class="kd-resep-nama font-jakarta font-medium">{{ $resep['title'] }}</p>

                        @if(!$resep['lengkap'])
                            <p class="kd-resep-kurang font-jakarta font-regular">
                                Kurang: {{ $bahanKurangNames }}
                            </p>
                        @else
                            <p class="kd-resep-lengkap-text font-jakarta font-regular">
                                Semua bahan tersedia!
                            </p>
                        @endif
                    </div>

                    <div class="kd-resep-right">
                        <span class="kd-resep-badge
                            {{ $resep['lengkap'] ? 'badge-resep-lengkap' : 'badge-resep-partial' }}
                            font-jakarta font-bold">
                            {{ $resep['bahan_ada'] }}/{{ $resep['total_bahan'] }}
                            @if($resep['lengkap']) ✓ @endif
                        </span>
                        <button class="kd-resep-detail-btn font-jakarta"
                                title="{{ $resep['lengkap'] ? 'Masak sekarang' : 'Lihat bahan kurang' }}">
                            <span class="material-icons-round">
                                {{ $resep['lengkap'] ? 'restaurant' : 'info_outline' }}
                            </span>
                        </button>
                    </div>

                </div>
            @endforeach
        </div>
    </section>
    @endif

</main>

{{-- ── MODAL BAHAN KURANG (resep belum lengkap) ── --}}
<div id="modalBahanKurang" style="display:none; position:fixed; inset:0; z-index:999; align-items:center; justify-content:center; padding:1rem;">
    <div class="modal-overlay" id="modalKurangOverlay"></div>
    <div class="modal-box">
        <div class="modal-resep-icon">
            <span class="material-icons-round">shopping_cart</span>
        </div>
        <h3 class="modal-title font-jakarta font-bold" id="modalKurangTitle"></h3>
        <p class="modal-desc font-jakarta font-regular">Cek takaran bahan yang dibutuhkan:</p>
        <ul class="modal-bahan-detail-list" id="modalKurangList"></ul>
        <button class="modal-btn-confirm font-jakarta font-bold" id="modalKurangClose"
                style="width:100%; margin-top:0.5rem;">
            Tutup
        </button>
    </div>
</div>

{{-- ── MODAL KONFIRMASI MASAK (resep sudah lengkap) ── --}}
<div id="modalMasak" style="display:none; position:fixed; inset:0; z-index:999; align-items:center; justify-content:center; padding:1rem;">
    <div class="modal-overlay" id="modalMasakOverlay"></div>
    <div class="modal-box">
        <div class="modal-resep-icon" style="background:#F0FDF4; border-radius:50%; padding:0.75rem;">
            <span class="material-icons-round" style="color:#16A34A; font-size:2rem;">restaurant</span>
        </div>
        <h3 class="modal-title font-jakarta font-bold" id="modalMasakTitle"></h3>
        <p class="modal-desc font-jakarta font-regular">
            Semua bahan tersedia! Mau dimasak sekarang?<br>
            <small style="color:#B87C5A;">Bahan yang dipakai otomatis dikurangi dari kulkas.</small>
        </p>

        <ul class="modal-bahan-detail-list" id="modalMasakBahanList"></ul>

        <div class="modal-actions" style="width:100%; margin-top:0.5rem;">
            <button class="modal-btn-cancel font-jakarta font-medium" id="modalMasakCancel">
                Nanti dulu
            </button>
            <button class="modal-btn-confirm font-jakarta font-bold" id="modalMasakConfirm">
                <span class="material-icons-round" style="font-size:1rem; vertical-align:middle;">check</span>
                Yuk, masak!
            </button>
        </div>

        <a href="#" id="modalMasakDetailLink"
           style="display:block; text-align:center; margin-top:0.75rem; font-size:0.75rem;
                  color:var(--orange-normal); font-family:var(--font-jakarta); text-decoration:none;">
            <span class="material-icons-round" style="font-size:0.85rem; vertical-align:middle;">open_in_new</span>
            Lihat detail resep lengkap
        </a>

        <p class="modal-loading font-jakarta font-regular" id="modalMasakLoading"
           style="display:none; font-size:0.8rem; color:#6B5B54; margin-top:0.5rem;">
            Memproses...
        </p>
    </div>
</div>

{{-- ── MODAL KONFIRMASI HAPUS (custom) ── --}}
<div id="modalHapus" style="display:none; position:fixed; inset:0; z-index:999; align-items:center; justify-content:center; padding:1rem;">
    <div class="modal-overlay" id="modalHapusOverlay"></div>
    <div class="modal-box">
        <div class="modal-resep-icon" style="background:#FEF2F2; border-radius:50%; padding:0.75rem;">
            <span class="material-icons-round" style="color:#DC2626; font-size:2rem;">delete_outline</span>
        </div>
        <h3 class="modal-title font-jakarta font-bold">Hapus Pembelian?</h3>
        <p class="modal-desc font-jakarta font-regular">
            Stok bahan ini akan dihapus dari kulkas digitalmu.
        </p>
        <div class="modal-actions" style="width:100%;">
            <button class="modal-btn-cancel font-jakarta font-medium" id="modalHapusCancel">Batal</button>
            <button class="modal-btn-confirm font-jakarta font-bold" id="modalHapusConfirm"
                    style="background:#DC2626;">
                <span class="material-icons-round" style="font-size:1rem; vertical-align:middle;">delete</span>
                Hapus
            </button>
        </div>
    </div>
</div>

<script>
    const PAKAI_RESEP_URL = "{{ route('kulkas.pakai-resep') }}";
    const CSRF_TOKEN      = "{{ csrf_token() }}";
</script>
@endsection

@push('scripts')
    <script src="{{ asset('js/kulkas-digital.js') }}"></script>
@endpush