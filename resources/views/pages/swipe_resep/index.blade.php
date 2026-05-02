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

            <x-swipe-card
                icon="ramen_dining"
                title="ASIN GURIH"
                desc="Lagi pengen yang asin gurih dan nagih?"
            />

            <x-swipe-card
                icon="local_fire_department"
                title="PEDAS"
                desc="Suka sensasi pedas yang nampol?"
            />

            <x-swipe-card
                icon="emoji_food_beverage"
                title="ASAM SEGAR"
                desc="Lagi pengen yang asem seger?"
            />

            <x-swipe-card
                icon="apple"
                title="MANIS BUAH"
                desc="Suka manis alami dari buah-buahan?"
            />

            <x-swipe-card
                icon="restaurant"
                title="UMAMI"
                desc="Suka rasa gurih khas yang dalam?"
            />

            <x-swipe-card
                icon="coffee"
                title="PAHIT"
                desc="Berani sama rasa pahit yang khas?"
            />

            <x-swipe-card
                icon="icecream"
                title="ASAM MANIS"
                desc="Pengen rasa asam tapi manis?"
            />

            <x-swipe-card
                icon="lunch_dining"
                title="GURIH"
                desc="Lagi pengen yang enak dimakan kapan aja?"
            />

        </div>

        {{-- Buttons tetap di halaman utama --}}
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