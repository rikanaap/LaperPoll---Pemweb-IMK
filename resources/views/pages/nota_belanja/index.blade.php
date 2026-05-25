@extends('layouts.app')

@section('title', 'Nota Belanja - LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/nota-belanja.css') }}">
@endpush

@section('content')
<main class="nb-main">

    <x-navbar :backUrl="route('meal-planner.index')"></x-navbar>

    {{-- ── HEADER ── --}}
    <div class="nb-header">
        <div class="nb-header-left">
            <h1 class="nb-title font-jakarta font-bold">Nota Belanja</h1>
            @if($start && $end)
                <div class="nb-range-badge">
                    <span class="material-icons-round">date_range</span>
                    <span class="font-jakarta font-medium">
                        {{ $start->format('d M') }}
                        @if($start->toDateString() !== $end->toDateString())
                            – {{ $end->format('d M Y') }}
                        @else
                            {{ $start->format('Y') }}
                        @endif
                    </span>
                </div>
            @endif
        </div>
        <button class="nb-filter-btn" id="filterBtn" title="Filter tanggal">
            <span class="material-icons-round">tune</span>
        </button>
    </div>

    {{-- ── FILTER DROPDOWN ── --}}
    <div class="nb-filter-dropdown hidden" id="filterDropdown">
        <p class="font-jakarta font-semibold nb-filter-label">Filter Rentang Tanggal</p>
        <div class="nb-filter-presets">
            <button class="nb-preset-btn font-jakarta font-medium" data-preset="today">Hari ini</button>
            <button class="nb-preset-btn font-jakarta font-medium" data-preset="tomorrow">Besok</button>
            <button class="nb-preset-btn font-jakarta font-medium" data-preset="thisweek">Minggu ini</button>
            <button class="nb-preset-btn font-jakarta font-medium" data-preset="next7">7 Hari ke depan</button>
        </div>
        <div class="nb-filter-custom">
            <div class="nb-filter-row">
                <label class="font-jakarta font-medium nb-filter-field-label">Dari</label>
                <div class="nb-cal-wrap">
                    <input type="hidden" id="filterStart"
                           value="{{ $start ? $start->format('Y-m-d') : date('Y-m-d') }}">
                    <button type="button" class="nb-cal-trigger" id="filterStartTrigger">
                        <span class="material-icons-round">calendar_today</span>
                        <span class="nb-cal-trigger-text font-jakarta" id="filterStartText">Pilih tanggal</span>
                        <span class="material-icons-round nb-cal-chevron">expand_more</span>
                    </button>
                    <div class="nb-cal-popup" id="filterStartPopup" style="display:none;"></div>
                </div>
            </div>
            <div class="nb-filter-row">
                <label class="font-jakarta font-medium nb-filter-field-label">Sampai</label>
                <div class="nb-cal-wrap">
                    <input type="hidden" id="filterEnd"
                           value="{{ $end ? $end->format('Y-m-d') : date('Y-m-d') }}">
                    <button type="button" class="nb-cal-trigger" id="filterEndTrigger">
                        <span class="material-icons-round">calendar_today</span>
                        <span class="nb-cal-trigger-text font-jakarta" id="filterEndText">Pilih tanggal</span>
                        <span class="material-icons-round nb-cal-chevron">expand_more</span>
                    </button>
                    <div class="nb-cal-popup" id="filterEndPopup" style="display:none;"></div>
                </div>
            </div>
        </div>
        <button class="nb-filter-apply font-jakarta font-bold" id="filterApply">
            <span class="material-icons-round">search</span>
            Tampilkan
        </button>
    </div>

    {{-- ── RESEP DALAM RANGE ── --}}
    @if($resepDalamRange->isNotEmpty())
    <div class="nb-resep-list">
        <p class="nb-section-label font-jakarta font-bold">
            <span class="material-icons-round">restaurant_menu</span>
            Resep di periode ini
        </p>
        <div class="nb-resep-chips">
            @foreach($resepDalamRange as $resep)
                <span class="nb-resep-chip font-jakarta font-medium">{{ $resep->title }}</span>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── PROGRESS + HAPUS SELESAI ── --}}
    @if($totalItem > 0)
    <div class="nb-progress-card" id="nbProgressCard">
        <div class="nb-progress-top">
            <div class="nb-progress-icon-wrap">
                <span class="material-icons-round">shopping_cart</span>
            </div>
            <div class="nb-progress-info">
                <p class="font-jakarta font-bold nb-progress-title">Daftar Belanja</p>
                <p class="font-jakarta font-regular nb-progress-sub" id="progressSub">
                    {{ $doneItem }} dari {{ $totalItem }} item sudah dibeli
                </p>
            </div>
            <span class="nb-progress-pct font-jakarta font-bold" id="progressPct">
                {{ $totalItem > 0 ? round(($doneItem / $totalItem) * 100) : 0 }}%
            </span>
        </div>
        <div class="nb-progress-track">
            <div class="nb-progress-fill" id="progressFill"
                 style="width: {{ $totalItem > 0 ? round(($doneItem / $totalItem) * 100) : 0 }}%"></div>
        </div>
    </div>

    {{-- Tombol hapus muncul hanya jika ada yang sudah dibeli --}}
    <div class="nb-hapus-wrap {{ $doneItem > 0 ? '' : 'hidden' }}" id="hapusSelesaiWrap">
        <button class="nb-hapus-btn font-jakarta font-semibold" id="hapusSelesaiBtn">
            <span class="material-icons-round">delete_sweep</span>
            Hapus {{ $doneItem }} bahan yang sudah dibeli
        </button>
    </div>
    @endif

    {{-- ── DAFTAR BAHAN PER KATEGORI ── --}}
    <div id="bahanList">
        @forelse($groupedOrdered as $kategori => $items)
            @php
                $iconKat = match($kategori) {
                    'KARBOHIDRAT' => 'grain',
                    'PROTEIN'     => 'egg_alt',
                    'SAYURAN'     => 'eco',
                    'BUMBU'       => 'spa',
                    default       => 'category',
                };
                $colorKat = match($kategori) {
                    'KARBOHIDRAT' => 'kat-amber',
                    'PROTEIN'     => 'kat-red',
                    'SAYURAN'     => 'kat-green',
                    'BUMBU'       => 'kat-orange',
                    default       => 'kat-gray',
                };
            @endphp
            <div class="nb-kategori-card" data-kategori="{{ $kategori }}">
                <div class="nb-kat-header">
                    <div class="nb-kat-icon {{ $colorKat }}">
                        <span class="material-icons-round">{{ $iconKat }}</span>
                    </div>
                    <span class="font-jakarta font-bold nb-kat-label">{{ $kategori }}</span>
                    <span class="nb-kat-count font-jakarta font-medium" data-kat-count="{{ $kategori }}">
                        {{ $items->count() }} item
                    </span>
                </div>

                <div class="nb-kat-items">
                    @foreach($items as $item)
                        <label class="nb-item {{ $item->is_done ? 'nb-item-done' : '' }}"
                               data-id="{{ $item->id }}">
                            <input type="checkbox" class="nb-check"
                                   {{ $item->is_done ? 'checked' : '' }}>
                            <span class="nb-checkmark">
                                <span class="material-icons-round nb-check-icon">check</span>
                            </span>
                            <div class="nb-item-info">
                                <span class="nb-item-nama font-jakarta font-medium">
                                    {{ $item->bahan->nama }}
                                </span>
                                <span class="nb-item-qty font-jakarta font-regular">
                                    {{ $item->gram_total > 0 ? $item->gram_total . ' gram' : '—' }}
                                </span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="nb-empty" id="notaEmpty">
                <div class="nb-empty-icon-wrap">
                    <span class="material-icons-round">receipt_long</span>
                </div>
                <p class="font-jakarta font-semibold nb-empty-title">Nota belanja kosong</p>
                <p class="font-jakarta font-regular nb-empty-sub">
                    Generate dari meal planner atau pilih rentang tanggal yang berbeda
                </p>
                <a href="{{ route('meal-planner.index') }}" class="nb-empty-cta font-jakarta font-semibold">
                    <span class="material-icons-round">arrow_back</span>
                    Ke Meal Planner
                </a>
            </div>
        @endforelse
    </div>

</main>

{{-- ── TOAST ── --}}
<div class="nb-toast hidden" id="nbToast">
    <span class="material-icons-round" id="nbToastIcon">check_circle</span>
    <span id="nbToastMsg"></span>
</div>
@endsection

@push('scripts')
<script>
    window.csrfToken     = "{{ csrf_token() }}";
    window.nbApiToggle   = "{{ url('/api/nota-belanja/toggle') }}";
    window.nbApiHapus    = "{{ url('/api/nota-belanja/hapus-selesai') }}";
    window.notaUrl       = "{{ route('nota.index') }}";
    window.nbGenerateUrl = "{{ route('api.meal-planner.generate-nota') }}";
</script>
<script src="{{ asset('js/nota-belanja.js') }}"></script>
@endpush