@extends('layouts.app')

@section('title', 'Pilih Resep - LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/pilih-resep.css') }}">
@endpush

@section('content')
<main class="main-content flex flex-col">

    {{-- NAVBAR --}}
    <x-navbar :back="true"></x-navbar>

    {{-- HEADER INFO SLOT --}}
    <div class="slot-info flex flex-col gap-1">
        <h1 class="font-jakarta font-bold text-h5 kulkas-title">Pilih Resep</h1>
        <p class="font-jakarta font-regular text-body text-primary-darker" id="slotLabel">Memilih untuk...</p>
    </div>

    {{-- WARNING: tidak ada slot --}}
    <div class="slot-warning font-jakarta text-body" id="slotWarning" style="display:none;">
        <span class="material-icons-round">warning_amber</span>
        Buka halaman ini dari Meal Planner dengan menekan tombol tambah resep.
    </div>

    {{-- SEARCH --}}
    <div class="input">
        <span class="material-icons-round">search</span>
        <input type="text" class="input-data font-jakarta text-body"
               placeholder="Cari nama resep..." id="searchResep"
               autocomplete="off">
    </div>

    {{-- LIST RESEP --}}
    <section class="resep-menus" id="resepList"></section>

</main>
@endsection

@push('scripts')
    <script>
        window.mealPlannerUrl = "{{ route('meal-planner.index') }}";
    </script>
    <script src="{{ asset('js/pilih-resep.js') }}"></script>
@endpush