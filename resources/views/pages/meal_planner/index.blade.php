@extends('layouts.app')

@section('title', 'Meal Planner - LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/meal-planner.css') }}">
@endpush

@section('content')
<main class="main-content flex flex-col">

    {{-- NAVBAR --}}
    <nav class="navbar">
        <a href="{{ route('profile.index') }}" class="back-btn">
            <span class="material-icons-round text-h4 text-accent-normal">arrow_back</span>
        </a>
        <img src="{{ asset('assets/Logo_Laperpoll.png') }}" alt="Logo Laperpoll" class="logo">
        <a href="{{ route('profile.index') }}" class="profile-link">
            <img src="{{ asset('assets/Image_DummyProfile.png') }}" alt="Profil Foto" class="profile">
        </a>
    </nav>

    {{-- PAGE HEADER --}}
    <div class="page-header flex flex-col gap-1">
        <h1 class="font-jakarta font-bold text-h5 kulkas-title">Meal Planner</h1>
    </div>

    {{-- TAB HARI --}}
    <div class="hari-tabs">
        <input type="radio" name="hari" id="tab-sen" checked>
        <input type="radio" name="hari" id="tab-sel">
        <input type="radio" name="hari" id="tab-rab">
        <input type="radio" name="hari" id="tab-kam">
        <input type="radio" name="hari" id="tab-jum">
        <input type="radio" name="hari" id="tab-sab">
        <input type="radio" name="hari" id="tab-min">

        <div class="hari-labels flex flex-row">
            <label for="tab-sen" class="hari-label font-jakarta font-semibold text-body">Sen</label>
            <label for="tab-sel" class="hari-label font-jakarta font-semibold text-body">Sel</label>
            <label for="tab-rab" class="hari-label font-jakarta font-semibold text-body">Rab</label>
            <label for="tab-kam" class="hari-label font-jakarta font-semibold text-body">Kam</label>
            <label for="tab-jum" class="hari-label font-jakarta font-semibold text-body">Jum</label>
            <label for="tab-sab" class="hari-label font-jakarta font-semibold text-body">Sab</label>
            <label for="tab-min" class="hari-label font-jakarta font-semibold text-body">Min</label>
        </div>

        {{-- Konten diisi oleh meal-planner.js --}}
        <div class="hari-content" id="content-sen"></div>
        <div class="hari-content" id="content-sel"></div>
        <div class="hari-content" id="content-rab"></div>
        <div class="hari-content" id="content-kam"></div>
        <div class="hari-content" id="content-jum"></div>
        <div class="hari-content" id="content-sab"></div>
        <div class="hari-content" id="content-min"></div>
    </div>

    {{-- GENERATE NOTA --}}
    <a href="{{ route('nota.index') }}" class="generate-btn flex flex-row gap-2">
        <span class="material-icons-round">bolt</span>
        <span class="font-jakarta font-semibold text-title2">Generate Nota Belanja Otomatis</span>
    </a>

</main>
@endsection

@push('scripts')
    <script>
        // Pass route ke JS supaya link pilih resep bisa pakai Laravel route
        window.pilihResepUrl = "{{ route('pilih-resep.index') }}";
    </script>
    <script src="{{ asset('js/pages/meal-planner.js') }}"></script>
@endpush