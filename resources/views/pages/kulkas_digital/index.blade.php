@extends('layouts.app')

@section('title', 'Kulkas Digital - LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/kulkas-digital.css') }}">
@endpush

@section('content')
<main class="kd-main">

    {{-- ── NAVBAR (shared) ─────────────────────────────────────────── --}}
    <x-navbar backUrl="back"></x-navbar>

    {{-- ══════════════════════════════════════════════════════════════
         MOBILE-ONLY SECTION (hidden on tablet/desktop via CSS)
         ═══════════════════════════════════════════════════════════════ --}}

    {{-- MOBILE HEADER --}}
    <div class="kd-header">
        <div class="kd-header-left">
            <h1 class="kd-title font-jakarta font-bold">Kulkas Digital</h1>
            <span class="kd-subtitle font-jakarta font-regular">
                {{ $grouped->count() }} bahan tersimpan
            </span>
        </div>
    </div>

    {{-- MOBILE SEARCH --}}
    <div class="kd-search-wrap">
        <div class="input">
            <span class="material-icons-round">search</span>
            <input type="text" id="kdSearch" class="input-data font-jakarta text-body"
                   placeholder="Cari bahan di kulkas..." autocomplete="off">
        </div>
    </div>

    {{-- MOBILE FILTER CHIPS --}}
    <div class="kd-filters">
        <button class="kd-chip active" data-filter="semua">Semua</button>
        <button class="kd-chip" data-filter="tersedia">Tersedia</button>
        <button class="kd-chip" data-filter="hampir-habis">Hampir Habis</button>
        <button class="kd-chip kd-chip-expired" data-filter="expired">Expired</button>
    </div>

    {{-- MOBILE EXPIRED BANNER --}}
    @php
        $expiredCount      = $grouped->where('status', 'expired')->count();
        $hasExpiredItems   = $grouped->where('has_expired_item', true)->count();
        $totalExpiredAlert = max($expiredCount, $hasExpiredItems);
    @endphp
    @if($totalExpiredAlert > 0)
    <div class="kd-expired-banner" id="kdExpiredBanner">
        <span class="material-icons-round">warning_amber</span>
        <div class="kd-expired-banner-text">
            <p class="font-jakarta font-semibold">
                {{ $totalExpiredAlert }} bahan memiliki pembelian kedaluwarsa
            </p>
            <p class="font-jakarta font-regular kd-expired-banner-sub">
                Bahan ini masih ditampilkan agar kamu bisa hapus secara sadar.
            </p>
        </div>
    </div>
    @endif

    {{-- MOBILE LIST — swipeable cards --}}
    <section class="kd-list" id="kdList">
        @forelse($grouped as $item)
            @php
                $statusIcon = match($item['status']) {
                    'tersedia'     => 'check_circle',
                    'hampir-habis' => 'schedule',
                    default        => 'cancel',
                };
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
                $firstBeli      = $item['pembelian'][0];
                $pembelianJson  = json_encode($item['pembelian']);
            @endphp

            <div class="kd-swipe-wrapper">
                {{-- Swipe background hints --}}
                <div class="kd-swipe-actions">
                    <div class="kd-action-delete">
                        <span class="material-icons-round">delete_outline</span>
                        Hapus
                    </div>
                    <div class="kd-action-info">
                        <span class="material-icons-round">info</span>
                        Detail
                    </div>
                </div>

                <div class="kd-card"
                     data-bahan-id="{{ $item['bahan_id'] }}"
                     data-status="{{ $item['status'] }}"
                     data-nama="{{ strtolower($item['nama']) }}"
                     data-stok-gram="{{ $item['stok_gram'] }}"
                     data-pembelian="{{ $pembelianJson }}">

                    <div class="kd-card-icon">
                        <span class="material-icons-round">{{ $statusIcon }}</span>
                    </div>

                    <div class="kd-card-body">
                        <div class="kd-card-nama font-jakarta font-semibold">{{ $item['nama'] }}</div>
                        <div class="kd-card-meta">
                            <span class="kd-badge {{ $bc }} font-jakarta font-medium">
                                <span class="material-icons-round kd-badge-icon">{{ $statusIcon }}</span>
                                {{ $bl }}
                            </span>
                            <span class="kd-card-gram font-jakarta">{{ $item['stok_gram'] }} gram</span>
                            {{-- FIX: tanda ada pembelian expired meski status bahan overall tersedia --}}
                            @if($item['has_expired_item'] && $item['status'] !== 'expired')
                                <span class="kd-pill kd-pill-exp kd-card-expired-hint font-jakarta">
                                    <span class="material-icons-round kd-pill-icon">warning_amber</span>
                                    Ada yg exp
                                </span>
                            @endif
                        </div>
                    </div>

                    <span class="material-icons-round kd-card-arrow">chevron_right</span>
                </div>

                {{-- Hidden delete forms for JS to submit --}}
                @foreach($item['pembelian'] as $beli)
                    <form id="form-del-{{ $beli['id'] }}"
                          action="{{ route('kulkas.destroy', $beli['id']) }}"
                          method="POST" style="display:none;">
                        @csrf @method('DELETE')
                    </form>
                @endforeach
            </div>
        @empty
            <div class="kd-empty">
                <span class="material-icons-round kd-empty-icon">kitchen</span>
                <p class="font-jakarta font-semibold kd-empty-title">Kulkas masih kosong</p>
                <p class="font-jakarta font-regular kd-empty-sub">
                    Tap tombol <strong>+</strong> di bawah untuk tambah bahan pertamamu
                </p>
            </div>
        @endforelse
    </section>

    {{-- MOBILE REKOMENDASI RESEP --}}
    @if(count($rekomendasi) > 0)
    <section class="kd-resep">
        <div class="kd-resep-header">
            <span class="kd-resep-sparkle">✨</span>
            <h2 class="kd-resep-title font-jakarta font-semibold">Resep dari bahan yang ada</h2>
        </div>
        <div class="kd-resep-list" id="kdResepList">
            @foreach($rekomendasi as $resep)
                @php
                    $bahanDetailJson  = json_encode($resep['bahan_detail']);
                    $bahanKurangNames = $resep['bahan_kurang']->map(fn($b) => $b['nama'])->join(', ');
                @endphp
                <div class="kd-resep-item"
                     data-resep-id="{{ $resep['id'] }}"
                     data-resep-nama="{{ $resep['title'] }}"
                     data-bahan-ids="{{ implode(',', $resep['bahan_ids']->toArray()) }}"
                     data-bahan-detail='{{ $bahanDetailJson }}'
                     data-lengkap="{{ $resep['lengkap'] ? '1' : '0' }}">

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
                            <p class="kd-resep-kurang font-jakarta font-regular">Kurang: {{ $bahanKurangNames }}</p>
                        @else
                            <p class="kd-resep-lengkap-text font-jakarta font-regular">Semua bahan tersedia!</p>
                        @endif
                    </div>
                    <div class="kd-resep-right">
                        <span class="kd-resep-badge {{ $resep['lengkap'] ? 'badge-resep-lengkap' : 'badge-resep-partial' }} font-jakarta font-bold">
                            {{ $resep['bahan_ada'] }}/{{ $resep['total_bahan'] }}
                            @if($resep['lengkap']) ✓ @endif
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- MOBILE FAB --}}
    <a href="{{ route('kulkas.tambah') }}" class="kd-fab" aria-label="Tambah bahan">
        <span class="material-icons-round">add</span>
        <span class="kd-fab-text font-jakarta font-bold">Tambah Bahan</span>
    </a>

    {{-- ══════════════════════════════════════════════════════════════
         TABLET + DESKTOP LAYOUT (hidden on mobile via CSS)
         ═══════════════════════════════════════════════════════════════ --}}
    <div class="kd-split-layout">

        {{-- ── SIDEBAR KATEGORI — FIX: pakai data-filter bukan data-kategori ── --}}
        <aside class="kd-kategori-sidebar">
            <div class="kd-sidebar-header">
                <div class="kd-sidebar-brand">
                    <span class="material-icons-round">kitchen</span>
                    Kulkas Digital
                </div>
                <div class="kd-sidebar-count font-jakarta">{{ $grouped->count() }} bahan tersimpan</div>
            </div>

            <nav class="kd-sidebar-nav">
                <div class="kd-sidebar-section-title">Filter Status</div>

                <div class="kd-sidebar-item active" data-filter="semua">
                    <div class="kd-sidebar-item-left">
                        <span class="material-icons-round kd-sidebar-icon">all_inbox</span>
                        <span class="kd-sidebar-item-label font-jakarta">Semua</span>
                    </div>
                    <span class="kd-sidebar-item-count font-jakarta">{{ $grouped->count() }}</span>
                </div>

                <div class="kd-sidebar-item" data-filter="tersedia">
                    <div class="kd-sidebar-item-left">
                        <span class="material-icons-round kd-sidebar-icon">check_circle</span>
                        <span class="kd-sidebar-item-label font-jakarta">Tersedia</span>
                    </div>
                    <span class="kd-sidebar-item-count font-jakarta">{{ $grouped->where('status', 'tersedia')->count() }}</span>
                </div>

                <div class="kd-sidebar-item" data-filter="hampir-habis">
                    <div class="kd-sidebar-item-left">
                        <span class="material-icons-round kd-sidebar-icon">schedule</span>
                        <span class="kd-sidebar-item-label font-jakarta">Hampir Habis</span>
                    </div>
                    <span class="kd-sidebar-item-count font-jakarta">{{ $grouped->where('status', 'hampir-habis')->count() }}</span>
                </div>

                @if($totalExpiredAlert > 0)
                <div class="kd-sidebar-item" data-filter="expired" style="color: #991B1B;">
                    <div class="kd-sidebar-item-left">
                        <span class="material-icons-round kd-sidebar-icon" style="color: #DC2626;">cancel</span>
                        <span class="kd-sidebar-item-label font-jakarta">Expired</span>
                    </div>
                    <span class="kd-sidebar-item-count font-jakarta" style="background:#FEE2E2; color:#991B1B;">
                        {{ $expiredCount }}
                    </span>
                </div>
                @endif
            </nav>

            <a href="{{ route('kulkas.tambah') }}" class="kd-sidebar-add-btn font-jakarta">
                <span class="material-icons-round">add</span>
                Tambah Bahan
            </a>
        </aside>

        {{-- ── CONTENT AREA ── --}}
        <div class="kd-content-area">

            {{-- Topbar --}}
            <div class="kd-content-topbar">
                <div>
                    <div class="kd-topbar-title font-jakarta font-bold">Semua Bahan</div>
                    <div class="kd-topbar-subtitle font-jakarta">
                        Klik bahan untuk melihat detail & riwayat pembelian
                    </div>
                </div>
                <div class="kd-topbar-search">
                    <div class="input">
                        <span class="material-icons-round">search</span>
                        <input type="text" id="kdSearchDesktop" class="input-data font-jakarta"
                               placeholder="Cari bahan..." autocomplete="off">
                    </div>
                </div>
            </div>

            {{-- FIX: Expired banner untuk tablet & desktop --}}
            @if($totalExpiredAlert > 0)
            <div class="kd-expired-banner kd-expired-banner-desktop" id="kdExpiredBannerDesktop">
                <span class="material-icons-round">warning_amber</span>
                <div class="kd-expired-banner-text">
                    <p class="font-jakarta font-semibold">
                        {{ $totalExpiredAlert }} bahan memiliki pembelian kedaluwarsa
                    </p>
                    <p class="font-jakarta font-regular kd-expired-banner-sub">
                        Bahan ini masih ditampilkan agar kamu bisa hapus secara sadar.
                    </p>
                </div>
            </div>
            @endif

            {{-- GRID CARD --}}
            <div class="kd-grid-view" id="kdGridView">
                @forelse($grouped as $item)
                    @php
                        $statusIcon    = match($item['status']) {
                            'tersedia'     => 'check_circle',
                            'hampir-habis' => 'schedule',
                            default        => 'cancel',
                        };
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
                        $pembelianJson = json_encode($item['pembelian']);
                    @endphp

                    <div class="kd-grid-card"
                         data-bahan-id="{{ $item['bahan_id'] }}"
                         data-status="{{ $item['status'] }}"
                         data-nama="{{ strtolower($item['nama']) }}"
                         data-stok-gram="{{ $item['stok_gram'] }}"
                         data-pembelian="{{ $pembelianJson }}">

                        <div class="kd-grid-card-top">
                            <div class="kd-grid-card-icon">
                                <span class="material-icons-round">{{ $statusIcon }}</span>
                            </div>
                            {{-- FIX: badge peringatan expired di pojok kanan atas grid card --}}
                            @if($item['has_expired_item'] && $item['status'] !== 'expired')
                                <span class="kd-grid-card-expired-hint" title="Ada pembelian yang sudah expired">
                                    <span class="material-icons-round">warning_amber</span>
                                </span>
                            @endif
                        </div>

                        <div class="kd-grid-card-nama font-jakarta font-semibold">{{ $item['nama'] }}</div>

                        <div class="kd-grid-card-footer">
                            <span class="kd-grid-card-gram font-jakarta">
                                <span class="material-icons-round">scale</span>
                                {{ $item['stok_gram'] }} gram
                            </span>
                            <span class="kd-badge {{ $bc }} font-jakarta font-medium" style="flex-shrink:0;">
                                {{ $bl }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="kd-empty">
                        <span class="material-icons-round kd-empty-icon">kitchen</span>
                        <p class="font-jakarta font-semibold kd-empty-title">Kulkas masih kosong</p>
                        <p class="font-jakarta font-regular kd-empty-sub">
                            Gunakan tombol "Tambah Bahan" di sidebar untuk memulai
                        </p>
                    </div>
                @endforelse
            </div>

        </div>

        {{-- ── DETAIL PANEL ── --}}
        <aside class="kd-detail-panel" id="kdDetailPanel">
            <div class="kd-detail-placeholder">
                <span class="material-icons-round">touch_app</span>
                <div class="kd-detail-placeholder-title font-jakarta font-semibold">
                    Pilih bahan
                </div>
                <div class="kd-detail-placeholder-sub font-jakarta font-regular">
                    Klik salah satu bahan di sebelah kiri untuk melihat detail, riwayat pembelian, dan resep yang bisa dibuat.
                </div>
            </div>

            <div class="kd-detail-content" style="display:none;"></div>
        </aside>

    </div>

    {{-- ══════════════════════════════════════════════════════════════
         MOBILE BOTTOM SHEET — detail bahan
         ═══════════════════════════════════════════════════════════════ --}}
    <div class="kd-sheet-overlay" id="kdSheetOverlay"></div>
    <div class="kd-sheet" id="kdSheet">
        <div class="kd-sheet-handle"></div>
        <div class="kd-sheet-header">
            <div class="kd-sheet-title-wrap">
                <div class="kd-sheet-nama font-jakarta font-bold" id="kdSheetNama">—</div>
                <span class="kd-badge font-jakarta font-medium" id="kdSheetBadge"></span>
            </div>
            <button class="kd-sheet-close-btn" id="kdSheetClose" aria-label="Tutup">
                <span class="material-icons-round">close</span>
            </button>
        </div>

        <div class="kd-sheet-stok" id="kdSheetStok">
            <span class="material-icons-round kd-sheet-stok-icon">kitchen</span>
            <div>
                <div class="kd-sheet-stok-val font-jakarta font-bold">0 gram</div>
                <div class="kd-sheet-stok-label font-jakarta">total stok tersedia</div>
            </div>
        </div>

        <div class="kd-sheet-section-title font-jakarta">Riwayat Pembelian</div>
        <div class="kd-sheet-beli-list" id="kdSheetBeliList"></div>
    </div>

    {{-- ── MODAL BAHAN KURANG ──────────────────────────────────────────── --}}
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

    {{-- ── MODAL KONFIRMASI MASAK ──────────────────────────────────────── --}}
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
                <button class="modal-btn-cancel font-jakarta font-medium" id="modalMasakCancel">Nanti dulu</button>
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
            <p class="font-jakarta font-regular" id="modalMasakLoading"
               style="display:none; font-size:0.8rem; color:#6B5B54; margin-top:0.5rem;">
                Memproses...
            </p>
        </div>
    </div>

    {{-- ── MODAL KONFIRMASI HAPUS ──────────────────────────────────────── --}}
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

</main>

@php
    $kdRekomendasiJs = $rekomendasi->map(fn($r) => [
        'id'           => $r['id'],
        'title'        => $r['title'],
        'thumbnail'    => $r['thumbnail'],
        'lengkap'      => $r['lengkap'],
        'bahan_ada'    => $r['bahan_ada'],
        'total_bahan'  => $r['total_bahan'],
        'bahan_ids'    => $r['bahan_ids']->toArray(),
        'bahan_detail' => $r['bahan_detail'],
    ])->values();
@endphp

<script>
    const PAKAI_RESEP_URL = "{{ route('kulkas.pakai-resep') }}";
    const CSRF_TOKEN      = "{{ csrf_token() }}";
    window.__kdRekomendasi = @json($kdRekomendasiJs);
</script>
@endsection

@push('scripts')
    <script src="{{ asset('js/kulkas-digital.js') }}"></script>
@endpush