@extends('layouts.app')

@section('title', 'Resep Favorit - LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/favorit.css') }}">
@endpush

@section('content')
<main class="fav-main font-jakarta">

    <x-navbar :backUrl="route('profile.index')"></x-navbar>

    {{-- Header --}}
    <div class="fav-header">
        <div class="fav-header-icon">
            <span class="material-icons-round">favorite</span>
        </div>
        <div>
            <h1 class="fav-title font-bold">Resep Favorit</h1>
            <p class="fav-sub" id="favCount">{{ $favorites->count() }} resep tersimpan</p>
        </div>
    </div>

    {{-- Search + Sort --}}
    @if(!$favorites->isEmpty())
    <div class="fav-toolbar">
        <div class="fav-search-wrap">
            <span class="material-icons-round fav-search-icon">search</span>
            <input type="text" id="favSearch" class="fav-search-input"
                   placeholder="Cari resep favorit...">
            <button class="fav-search-clear" id="favSearchClear" style="display:none">
                <span class="material-icons-round">close</span>
            </button>
        </div>
        <div class="fav-sort-wrap" id="favSortToggle">
            <span class="material-icons-round">sort</span>
            <span class="fav-sort-label" id="favSortLabel">Terbaru</span>
            <span class="material-icons-round fav-sort-arrow">expand_more</span>
            <div class="fav-sort-dropdown" id="favSortDropdown">
                <button class="fav-sort-option active" data-sort="newest">Terbaru</button>
                <button class="fav-sort-option" data-sort="oldest">Terlama</button>
                <button class="fav-sort-option" data-sort="rating">Rating Tertinggi</button>
                <button class="fav-sort-option" data-sort="name">A - Z</button>
            </div>
        </div>
    </div>

    {{-- No result state --}}
    <div class="fav-no-result" id="favNoResult" style="display:none">
        <span class="material-icons-round">search_off</span>
        <p class="fav-empty-title font-semibold">Resep tidak ditemukan</p>
        <p class="fav-empty-sub">Coba kata kunci lain.</p>
    </div>
    @endif

    {{-- Grid --}}
    @if($favorites->isEmpty())
        <div class="fav-empty">
            <span class="material-icons-round fav-empty-icon">favorite_border</span>
            <p class="fav-empty-title font-semibold">Belum ada resep favorit</p>
            <p class="fav-empty-sub">Ketuk ikon hati di detail resep untuk menyimpannya di sini.</p>
            <a href="{{ route('pencarian.resep') }}" class="fav-empty-btn font-semibold">
                <span class="material-icons-round">explore</span>
                Jelajahi Resep
            </a>
        </div>
    @else
        <div class="fav-grid" id="favGrid">
                @foreach($favorites as $resep)
                <a href="{{ route('detail.resep', $resep->id) }}" class="fav-card-link"
                   data-title="{{ strtolower($resep->title) }}"
                   data-author="{{ strtolower($resep->user->name ?? '') }}"
                   data-rating="{{ $resep->current_star }}"
                   data-date="{{ $resep->pivot->created_at ?? $resep->created_at }}" data-favorited="{{ $resep->pivot->created_at ?? $resep->created_at }}">
                    <div class="fav-card">
                        {{-- Thumbnail --}}
                        <div class="fav-card-thumb">
                            <img src="{{ $resep->thumbnail_url }}" alt="{{ $resep->title }}"
                                class="fav-thumb-img"
                                onerror="this.src='{{ asset('assets/images/Image_DummyResep.png') }}'">

                            {{-- Tombol hapus favorit --}}
                            <button class="fav-remove-btn"
                                    data-resep-id="{{ $resep->id }}"
                                    aria-label="Hapus dari favorit">
                                <span class="material-icons-round">favorite</span>
                            </button>

                            {{-- Badge rating --}}
                            @if($resep->current_star > 0)
                                <div class="fav-rating-badge">
                                    <span class="material-icons-round">star</span>
                                    {{ number_format($resep->current_star, 1) }}
                                </div>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="fav-card-info">
                            <p class="fav-card-title font-semibold">{{ $resep->title }}</p>
                            <p class="fav-card-author">
                                <span class="material-icons-round">person</span>
                                {{ $resep->user->name ?? 'Anonim' }}
                            </p>
                            <div class="fav-card-meta">
                                <span class="fav-meta-item">
                                    <span class="material-icons-round">schedule</span>
                                    {{ $resep->cook_duration_formatted }}
                                </span>
                                <span class="fav-meta-item">
                                    <span class="material-icons-round">visibility</span>
                                    {{ $resep->views_count }}
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

</main>

@push('scripts')
<script>
    const CSRF_TOKEN = "{{ csrf_token() }}";
    const TOGGLE_BASE_URL = "{{ url('/favorit/toggle') }}";
    const EXPLORE_URL     = "{{ route('pencarian.resep') }}";
</script>
<script src="{{ asset('js/favorit.js') }}"></script>
@endpush

@endsection