@extends('layouts.app')

@section('title', 'Main Menu - LaperPoll')

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="{{ asset('css/pages/main-menu.css') }}">
<link rel="stylesheet" href="{{ asset('css/pages/main-menu-animations.css') }}">
<link rel="stylesheet" href="{{ asset('css/medias/main-menu.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/resep-card-main-menu.css') }}">
@endpush

@section('content')
@php
$currentFilters = (array) request()->query('filter', []);
@endphp
<x-navbar backUrl="{{ route('landing.index') }}"></x-navbar>
<div class="desktop-menus anim-fade-in">
    <section class="resep-menus">
        <main class="main-content">
            @foreach ($reseps as $resep )
            <div class="anim-card" style="--i: {{ $loop->index }}">
                <x-resep-card-main-menu :resep="$resep" />
            </div>
            @endforeach
        </main>
    </section>
    <section class="bottom-menus anim-slide-up">
        <div class="input">
            <span class="material-icons-round">search</span>
            <input id="search-filter" class="input-data text-body font-jakarta font-semibold" type="text" placeholder="Telusuri filter yang ada">
        </div>
        @if (count($currentFilters) > 0)
        <div class="bottom-filters-used">
            <p class="font-jakarta text-body font-semibold text-secondary-normal">Filter saat ini:</p>
            @foreach ($usedFilters as $filter)
            @php
            $isLast = $loop->last;
            $removedFilters = array_values(array_diff($currentFilters, [$filter->id]));
            $removeUrl = request()->fullUrlWithQuery(['filter' => $removedFilters]);
            @endphp
            @if ($isLast)
            <a href="{{ $removeUrl }}">
                @endif
                <div class="bottom-filter-used">
                    <p class="font-jakarta text-body font-semibold text-primary-normal">{{ $filter->title }}</p>
                    @if ($isLast)
                    <span class="material-icons-round text-primary-normal" style="font-size: 0.85rem;">close</span>
                    @endif
                </div>
                @if ($isLast)
            </a>
            @endif
            @endforeach
        </div>
        @endif
        <div class="bottom-filters">
            @foreach ($master_filters as $filter)
            @php
            $updatedFilters = array_merge($currentFilters, [$filter->id]);
            $toggleUrl = request()->fullUrlWithQuery(['filter' => $updatedFilters]);
            @endphp
            <a href="{{ $toggleUrl }}" class="anim-chip" style="--i: {{ $loop->index }}">
                <div class="bottom-filter">
                    <p class="font-jakarta text-body font-regular">{{ $filter->title }}</p>
                </div>
            </a>
            @endforeach
        </div>
        @if (count($currentFilters) < 1)
            <div class="bottom-text">
            <p class="font-jakarta text-body font-semibold">Tekan untuk memfilter hasil!</p>
</div>
@endif
</section>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/main-menu.js') }}"></script>
<script>
    document.querySelectorAll('a').forEach(link => {
        if (link.hostname === location.hostname) {
            link.addEventListener('click', function(e) {
                const href = this.href;
                e.preventDefault();
                document.body.classList.add('anim-page-out');
                setTimeout(() => {
                    window.location = href;
                }, 250);
            });
        }
    });
</script>
@endpush