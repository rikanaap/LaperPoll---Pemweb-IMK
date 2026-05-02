@extends('layouts.app')

@section('title', 'Swipe Rasa')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/swipe-resep.css') }}">
@endpush

@section('content')

<main class="main-content flex flex-col gap-4 font-jakarta">

    {{-- Navbar --}}
    <x-navbar />

    {{-- Swipe Container --}}
    <section class="swipe-container">

        {{-- Cards --}}
        <div class="swipe-cards" id="swipeCards">

            <div class="swipe-card">
                <span class="material-icons-round swipe-icon">ramen_dining</span>
                <h3>ASIN GURIH</h3>
                <p>Lagi pengen yang asin gurih dan nagih?</p>
            </div>

            <div class="swipe-card">
                <span class="material-icons-round swipe-icon">local_fire_department</span>
                <h3>PEDAS</h3>
                <p>Suka sensasi pedas yang nampol?</p>
            </div>

            <div class="swipe-card">
                <span class="material-icons-round swipe-icon">emoji_food_beverage</span>
                <h3>ASAM SEGAR</h3>
                <p>Lagi pengen yang asem seger?</p>
            </div>

            <div class="swipe-card">
                <span class="material-icons-round swipe-icon">apple</span>
                <h3>MANIS BUAH</h3>
                <p>Suka manis alami dari buah-buahan?</p>
            </div>

            <div class="swipe-card">
                <span class="material-icons-round swipe-icon">restaurant</span>
                <h3>UMAMI</h3>
                <p>Suka rasa gurih khas yang dalam?</p>
            </div>

            <div class="swipe-card">
                <span class="material-icons-round swipe-icon">coffee</span>
                <h3>PAHIT</h3>
                <p>Berani sama rasa pahit yang khas?</p>
            </div>

            <div class="swipe-card">
                <span class="material-icons-round swipe-icon">icecream</span>
                <h3>ASAM MANIS</h3>
                <p>Pengen rasa asam tapi manis?</p>
            </div>

            <div class="swipe-card">
                <span class="material-icons-round swipe-icon">lunch_dining</span>
                <h3>GURIH</h3>
                <p>Lagi pengen yang enak dimakan kapan aja?</p>
            </div>

        </div>

        {{-- Buttons --}}
        <div class="swipe-buttons">
            <button id="dislike" type="button">
                <span class="material-icons-round">close</span>
            </button>

            <button id="like" type="button">
                <span class="material-icons-round">favorite</span>
            </button>
        </div>

    </section>

</main>

@endsection


@push('scripts')
<script src="{{ asset('js/pages/swipe-resep.js') }}"></script>
@endpush