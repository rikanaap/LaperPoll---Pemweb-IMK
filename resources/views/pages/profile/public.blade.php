@extends('layouts.app')

@section('title', $user->name . ' - LaperPoll')

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
                    <img src="{{ $user->profile_photo
                        ? Storage::url($user->profile_photo)
                        : asset('assets/images/Image_DummyProfile.png') }}"
                        alt="Foto {{ $user->name }}"
                        class="avatar-img">
                </div>
            </div>

            <div class="profile-identity">
                <h1 class="profile-name font-bold">{{ $user->name }}</h1>
                <p class="profile-email">{{ $user->email }}</p>

                {{-- Tombol Follow --}}
                @auth
                    <button class="pub-follow-btn {{ $isFollowing ? 'following' : '' }}"
                            id="pubFollowBtn"
                            data-user-id="{{ $user->id }}">
                        <span class="material-icons-round">
                            {{ $isFollowing ? 'person_remove' : 'person_add' }}
                        </span>
                        <span id="pubFollowLabel">
                            {{ $isFollowing ? 'Mengikuti' : 'Ikuti' }}
                        </span>
                    </button>
                @else
                    <a href="{{ route('auth.sign-in') }}" class="pub-follow-btn">
                        <span class="material-icons-round">person_add</span>
                        <span>Ikuti</span>
                    </a>
                @endauth
            </div>

            <div class="profile-stats-row">
                <div class="stat-bubble">
                    <span class="stat-number">{{ $resepCount }}</span>
                    <span class="stat-label">Resep</span>
                </div>
                <div class="stat-divider-v"></div>
                <div class="stat-bubble">
                    <span class="stat-number" id="pubFollowerCount">{{ $followerCount }}</span>
                    <span class="stat-label">Pengikut</span>
                </div>
                <div class="stat-divider-v"></div>
                <div class="stat-bubble">
                    <span class="stat-number">{{ $followingCount }}</span>
                    <span class="stat-label">Mengikuti</span>
                </div>
            </div>

        </div>
    </section>

    {{-- RESEP USER INI --}}
    <section class="my-resep-section">
        <div class="section-header">
            <h2 class="section-title font-semibold">Resep dari {{ $user->name }}</h2>
        </div>

        @if($resepUser->isEmpty())
            <div class="empty-resep">
                <span class="material-icons-round empty-icon">restaurant_menu</span>
                <p class="empty-title font-semibold">Belum ada resep</p>
                <p class="empty-sub">User ini belum membagikan resep apapun.</p>
            </div>
        @else
            <div class="resep-grid">
                @foreach($resepUser as $resep)
                    <a href="{{ route('detail.resep', $resep->id) }}" class="resep-card-link">
                        <div class="resep-card">
                            <div class="resep-card-thumb">
                                @if($resep->thumbnail)
                                    <img src="{{ asset($resep->thumbnail) }}"
                                         alt="{{ $resep->title }}"
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
                                        {{ $resep->cook_duration_formatted }}
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

@push('scripts')
<script>
    const PUB_CSRF    = "{{ csrf_token() }}";
    const PUB_IS_AUTH = {{ Auth::check() ? 'true' : 'false' }};
    const PUB_SIGN_IN = "{{ route('auth.sign-in') }}";
</script>
<script src="{{ asset('js/public-profile.js') }}"></script>
@endpush

{{-- Unfollow confirm modal --}}
<div class="lp-confirm-overlay" id="pubUnfollowOverlay"></div>
<div class="lp-confirm-modal" id="pubUnfollowModal">
    <div class="lp-confirm-box">
        <div class="lp-confirm-icon">👋</div>
        <h3 class="lp-confirm-title font-bold">Berhenti Mengikuti?</h3>
        <p class="lp-confirm-sub">Kamu tidak akan melihat konten dari user ini di feedmu.</p>
        <div class="lp-confirm-actions">
            <button class="lp-confirm-cancel font-semibold" onclick="closePubUnfollowConfirm()">Batal</button>
            <button class="lp-confirm-ok font-semibold" onclick="confirmPubUnfollow()"
                    style="background:var(--orange-normal);box-shadow:0 4px 12px rgba(230,81,0,0.3)">
                Ya, Berhenti
            </button>
        </div>
    </div>
</div>

@endsection