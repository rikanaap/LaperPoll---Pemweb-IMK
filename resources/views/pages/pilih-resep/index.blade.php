@extends('layouts.app')

@section('title', 'Pilih Resep - LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/pilih-resep.css') }}">
@endpush

@section('content')
<main class="main-content flex flex-col gap-4">

    {{-- NAVBAR --}}
    <nav class="navbar">
        <a href="{{ route('meal-planner.index') }}" class="back-btn" aria-label="Kembali">
            <span class="material-icons-round text-h4 text-accent-normal">arrow_back</span>
        </a>
        <img src="{{ asset('assets/Logo_Laperpoll.png') }}" alt="Logo Laperpoll" class="logo">
        <a href="{{ route('profile.index') }}" class="profile-link">
            <img src="{{ asset('assets/Image_DummyProfile.png') }}" alt="Profil Foto" class="profile">
        </a>
    </nav>

    {{-- HEADER INFO SLOT --}}
    <div class="slot-info flex flex-col gap-1">
        <h1 class="font-jakarta font-bold text-h5 kulkas-title">Pilih Resep</h1>
        <p class="font-jakarta font-regular text-body text-primary-darker" id="slotLabel">Memilih untuk...</p>
    </div>

    {{-- SEARCH --}}
    <div class="input">
        <span class="material-icons-round">search</span>
        <input type="text" class="input-data" placeholder="Cari nama resep..." id="searchResep">
    </div>

    {{-- LIST RESEP --}}
    <section class="resep-menus" id="resepList"></section>

</main>
@endsection

@push('scripts')
    <script>
        // Pass route balik ke meal planner
        window.mealPlannerUrl = "{{ route('meal-planner.index') }}";
    </script>
    <script src="{{ asset('js/pages/pilih-resep.js') }}"></script>
@endpush