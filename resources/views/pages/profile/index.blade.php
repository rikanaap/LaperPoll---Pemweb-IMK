@extends('layouts.app')

@section('title', 'Profil - LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/profile.css') }}">
@endpush

@section('content')
<main class="profile-main font-jakarta">

    <x-navbar backUrl="back"></x-navbar>

    {{-- HERO --}}
    <section class="profile-hero">
        <div class="profile-hero-bg"></div>
        <div class="profile-hero-content">

            <div class="avatar-container">
                <div class="avatar-ring">
                    <img
                        src="{{ $user->profile_photo ? Storage::url($user->profile_photo) : asset('assets/images/Image_DummyProfile.png') }}"
                        alt="Foto {{ $user->name }}"
                        class="avatar-img">
                </div>
                <a href="{{ route('profile.edit') }}" class="avatar-edit-btn" aria-label="Edit profil">
                    <span class="material-icons-round">edit</span>
                </a>
            </div>

            <div class="profile-identity">
                <h1 class="profile-name font-bold">{{ $user->name }}</h1>
                <p class="profile-email">{{ $user->email }}</p>
            </div>

            <div class="profile-stats-row">
                <div class="stat-bubble">
                    <span class="stat-number">{{ $resepCount }}</span>
                    <span class="stat-label">Resep</span>
                </div>
                <div class="stat-divider-v"></div>
                <a href="#" class="stat-bubble stat-link">
                    <span class="stat-number">{{ $followerCount }}</span>
                    <span class="stat-label">Pengikut</span>
                </a>
                <div class="stat-divider-v"></div>
                <a href="#" class="stat-bubble stat-link">
                    <span class="stat-number">{{ $followingCount }}</span>
                    <span class="stat-label">Mengikuti</span>
                </a>
            </div>

        </div>
    </section>

    {{-- QUICK ACTIONS --}}
    <section class="quick-actions-section">
        <a href="{{ url('/favorit') }}" class="quick-action-btn">
            <div class="quick-action-icon favorit-icon">
                <span class="material-icons-round">favorite</span>
            </div>
            <div class="quick-action-text">
                <span class="quick-action-title">Resep Favorit</span>
                <span class="quick-action-sub">{{ $favoritCount }} resep tersimpan</span>
            </div>
            <span class="material-icons-round quick-action-arrow">chevron_right</span>
        </a>

        <a href="{{ route('profile.edit') }}" class="quick-action-btn">
            <div class="quick-action-icon edit-icon">
                <span class="material-icons-round">manage_accounts</span>
            </div>
            <div class="quick-action-text">
                <span class="quick-action-title">Edit Profil</span>
                <span class="quick-action-sub">Ubah nama, email, foto</span>
            </div>
            <span class="material-icons-round quick-action-arrow">chevron_right</span>
        </a>
    </section>

    {{-- RESEP SAYA --}}
    <section class="my-resep-section">
        <div class="section-header">
            <h2 class="section-title font-semibold">Resep Saya</h2>
            @if($resepCount > 12)
                <a href="#" class="see-all-link">Lihat semua</a>
            @endif
        </div>

        @if($resepUser->isEmpty())
            <div class="empty-resep">
                <span class="material-icons-round empty-icon">restaurant_menu</span>
                <p class="empty-title font-semibold">Belum ada resep</p>
                <p class="empty-sub">Mulai bagikan resep andalanmu!</p>
            </div>
        @else
            <div class="resep-grid">
                @foreach($resepUser as $resep)
                    <a href="{{ route('detail.resep', $resep->id) }}" class="resep-card-link">
                        <div class="resep-card">
                            <div class="resep-card-thumb">
                                @if($resep->thumbnail)
                                    <img src="{{ asset($resep->thumbnail) }}" alt="{{ $resep->title }}"
                                         class="resep-thumb-img"
                                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                                    <div class="resep-thumb-placeholder" style="display:none">
                                        <span class="material-icons-round">restaurant</span>
                                    </div>
                                @else
                                    <div class="resep-thumb-placeholder">
                                        <span class="material-icons-round">restaurant</span>
                                    </div>
                                @endif
                                @if($resep->current_star > 0)
                                    <div class="resep-rating-badge">
                                        <span class="material-icons-round">star</span>
                                        {{ number_format($resep->current_star, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div class="resep-card-info">
                                <p class="resep-card-title font-semibold">{{ $resep->title }}</p>
                                <div class="resep-card-meta">
                                    <span class="resep-meta-item">
                                        <span class="material-icons-round">schedule</span>
                                        {{ $resep->cook_duration }}
                                    </span>
                                    <span class="resep-meta-item">
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
    </section>

</main>

{{-- FAB --}}
<div class="fab-overlay" id="fabOverlay"></div>
<div class="fab-container" id="fabContainer">
    <div class="fab-menu" id="fabMenu">
        <a href="{{ route('kulkas.index') }}" class="fab-item">
            <span class="fab-item-label font-semibold">Kulkas Digital</span>
            <div class="fab-item-icon"><span class="material-icons-round">kitchen</span></div>
        </a>
        <a href="{{ route('nota.index') }}" class="fab-item">
            <span class="fab-item-label font-semibold">Nota Belanja</span>
            <div class="fab-item-icon"><span class="material-icons-round">receipt_long</span></div>
        </a>
        <a href="{{ route('meal-planner.index') }}" class="fab-item">
            <span class="fab-item-label font-semibold">Meal Planner</span>
            <div class="fab-item-icon"><span class="material-icons-round">calendar_month</span></div>
        </a>
    </div>
    <button class="fab-btn" id="fabBtn" aria-label="Menu">
        <span class="material-icons-round fab-icon" id="fabIcon">add</span>
    </button>
</div>

@push('scripts')
<script src="{{ asset('js/profile.js') }}"></script>
@endpush

@endsection