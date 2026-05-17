@extends('layouts.app')

@section('title', 'Main Menu - LaperPoll')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/main-menu.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/resep-card-main-menu.css') }}">
@endpush

@section('content')
@php
$currentFilters = (array) request()->query('filter', []);
@endphp
<x-navbar></x-navbar>
<div class="desktop-menus">
    <section class="resep-menus">
        <main class="main-content flex flex-col">
            @foreach ($reseps as $resep )
            <x-resep-card-main-menu :resep="$resep" />
            @endforeach
        </main>
    </section>
    <section class="bottom-menus">
        <div class="input">
            <span class="material-icons-round">search</span>
            <input id="search-filter" class="input-data text-body font-jakarta font-semibold" type="text" placeholder="Telusuri filter yang ada">
        </div>
        @if (count($currentFilters) > 0)
        <div class="bottom-filters-used">
            <p class="font-jakarta text-body font-semibold text-secondary-normal">Filter saat ini:</p>
        </div>
        @endif
        <div class="bottom-filters">
            @foreach ($master_filters as $filter)
            @php
            $updatedFilters = array_merge($currentFilters, [$filter->id]);
            $toggleUrl = request()->fullUrlWithQuery(['filter' => $updatedFilters]);
            @endphp
            <a href="{{ $toggleUrl }}">
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
@endpush