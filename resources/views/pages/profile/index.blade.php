@extends('layouts.app')

@section('title', 'Profil - LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/profile.css') }}">
@endpush

@section('content')
<main class="profile-main font-jakarta">

    {{-- NAVBAR dengan hamburger di kanan --}}
    <x-navbar backUrl="back" :hamburger="true"></x-navbar>

    {{-- Flash message dari edit profil --}}
    @if(session('success'))
        <div class="prof-flash prof-flash-success">
            <span class="material-icons-round">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- HERO --}}
    <section class="profile-hero">
        <div class="profile-hero-bg"></div>
        <div class="profile-hero-content">

            <div class="avatar-container">
                <a href="{{ route('profile.edit') }}" class="avatar-ring" aria-label="Edit profil">
                    <img
                        src="{{ $user->profile_photo ? Storage::url($user->profile_photo) : asset('assets/images/Image_DummyProfile.png') }}"
                        alt="Foto {{ $user->name }}"
                        class="avatar-img">
                </a>
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
                <button class="stat-bubble stat-link"
                        onclick="openFollowModal('followers')"
                        data-followers-url="{{ route('follow.followers', $user->id) }}">
                    <span class="stat-number">{{ $followerCount }}</span>
                    <span class="stat-label">Pengikut</span>
                </button>
                <div class="stat-divider-v"></div>
                <button class="stat-bubble stat-link"
                        onclick="openFollowModal('following')"
                        data-following-url="{{ route('follow.following', $user->id) }}">
                    <span class="stat-number">{{ $followingCount }}</span>
                    <span class="stat-label">Mengikuti</span>
                </button>
            </div>

        </div>
    </section>

    {{-- QUICK ACTIONS --}}
    <section class="quick-actions-section">
        <a href="{{ route('favorit.index') }}" class="quick-action-btn">
            <div class="quick-action-icon favorit-icon">
                <span class="material-icons-round">favorite</span>
            </div>
            <div class="quick-action-text">
                <span class="quick-action-title">Resep Favorit</span>
                <span class="quick-action-sub">{{ $favoritCount }} resep tersimpan</span>
            </div>
            <span class="material-icons-round quick-action-arrow">chevron_right</span>
        </a>

        {{-- Placeholder tambah resep — logic oleh teman --}}
        <button class="quick-action-btn" disabled title="Segera hadir" style="cursor:not-allowed;opacity:0.7;">
            <div class="quick-action-icon edit-icon">
                <span class="material-icons-round">add_circle</span>
            </div>
            <div class="quick-action-text">
                <span class="quick-action-title">Tambah Resep</span>
                <span class="quick-action-sub">Bagikan resep andalanmu</span>
            </div>
            <span class="material-icons-round quick-action-arrow">chevron_right</span>
        </button>
    </section>

    {{-- RESEP SAYA --}}
    <section class="my-resep-section">
        <div class="section-header">
            <h2 class="section-title font-semibold">Resep Saya</h2>
            @if(!$resepUser->isEmpty())
            <div class="prof-sort-wrap" id="profSortToggle">
                <span class="material-icons-round">sort</span>
                <span class="prof-sort-label" id="profSortLabel">Terbaru</span>
                <span class="material-icons-round prof-sort-arrow">expand_more</span>
                <div class="prof-sort-dropdown" id="profSortDropdown">
                    <button class="prof-sort-option active" data-sort="newest">Terbaru</button>
                    <button class="prof-sort-option" data-sort="rating">Rating</button>
                    <button class="prof-sort-option" data-sort="views">Terpopuler</button>
                    <button class="prof-sort-option" data-sort="name">A - Z</button>
                </div>
            </div>
            @elseif($resepCount > 12)
                <a href="#" class="see-all-link">Lihat semua</a>
            @endif
        </div>

        @if($resepUser->isEmpty())
            <div class="empty-resep">
                <span class="material-icons-round empty-icon">restaurant_menu</span>
                <p class="empty-title font-semibold">Belum ada resep</p>
                <p class="empty-sub">Mulai bagikan resep andalanmu!</p>
                {{-- Placeholder tombol tambah resep — logic oleh teman --}}
                <button class="profile-add-resep-btn font-semibold" disabled title="Segera hadir">
                    <span class="material-icons-round">add_circle</span>
                    Tambah Resep
                </button>
            </div>
        @else
            <div class="resep-grid" id="profResepGrid">
                @foreach($resepUser as $resep)
                    <a href="{{ route('detail.resep', $resep->id) }}" class="resep-card-link"
                       data-title="{{ strtolower($resep->title) }}"
                       data-rating="{{ $resep->current_star }}"
                       data-views="{{ $resep->views_count }}"
                       data-date="{{ $resep->created_at }}">
                        <div class="resep-card">
                            <div class="resep-card-thumb">
                                @if($resep->thumbnail)
                                    <img src="{{ $resep->thumbnail_url }}" alt="{{ $resep->title }}"
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

            {{-- Tombol tambah resep di bawah grid — placeholder, logic oleh teman --}}
            <button class="profile-add-resep-btn font-semibold" disabled title="Segera hadir">
                <span class="material-icons-round">add_circle</span>
                Tambah Resep Baru
            </button>
        @endif
    </section>

</main>

{{-- ── HAMBURGER SIDEBAR ── --}}
<div class="profile-sidebar-overlay" id="sidebarOverlay"></div>
<aside class="profile-sidebar" id="profileSidebar" aria-label="Menu navigasi">

    {{-- Header sidebar --}}
    <div class="sidebar-header">
        <img src="{{ asset('assets/images/Logo_Laperpoll.png') }}" alt="LaperPoll" class="sidebar-logo">
        <button class="sidebar-close" id="sidebarClose" aria-label="Tutup menu">
            <span class="material-icons-round">close</span>
        </button>
    </div>

    {{-- User info mini --}}
    <div class="sidebar-user">
        <img src="{{ $user->profile_photo ? Storage::url($user->profile_photo) : asset('assets/images/Image_DummyProfile.png') }}"
             alt="{{ $user->name }}" class="sidebar-avatar">
        <div>
            <p class="sidebar-user-name font-semibold">{{ $user->name }}</p>
            <p class="sidebar-user-email">{{ $user->email }}</p>
        </div>
    </div>

    <div class="sidebar-divider"></div>

    {{-- Menu items --}}
    <nav class="sidebar-nav">
        <a href="#sidebar-about" class="sidebar-item" onclick="showSidebarSection('about')">
            <span class="material-icons-round">info</span>
            Tentang LaperPoll
        </a>
        <a href="#sidebar-team" class="sidebar-item" onclick="showSidebarSection('team')">
            <span class="material-icons-round">group</span>
            Tim Kami
        </a>
        <a href="#sidebar-faq" class="sidebar-item" onclick="showSidebarSection('faq')">
            <span class="material-icons-round">help_outline</span>
            FAQ
        </a>
        <a href="#sidebar-contact" class="sidebar-item" onclick="showSidebarSection('contact')">
            <span class="material-icons-round">contact_support</span>
            Hubungi Kami
        </a>
    </nav>

    <div class="sidebar-divider"></div>

    {{-- Logout --}}
    <form action="{{ route('auth.logout') }}" method="POST" class="sidebar-logout-form">
        @csrf
        <button type="submit" class="sidebar-logout font-semibold">
            <span class="material-icons-round">logout</span>
            Keluar
        </button>
    </form>

</aside>

{{-- ── SIDEBAR CONTENT PANELS ── --}}

{{-- Tentang LaperPoll --}}
<div class="sidebar-panel" id="panel-about">
    <div class="sidebar-panel-box">
        <div class="sidebar-panel-header">
            <button class="sidebar-panel-back" onclick="closeSidebarSection()">
                <span class="material-icons-round">arrow_back</span>
            </button>
            <h2 class="sidebar-panel-title font-bold">Tentang LaperPoll</h2>
        </div>
        <div class="sidebar-panel-body">
            <img src="{{ asset('assets/images/Logo_Laperpoll.png') }}" alt="LaperPoll" class="sidebar-about-logo">
            <p class="sidebar-about-tagline font-semibold">"Laper Banget? Nyari Resep ya LaperPoll aja"</p>
            <p class="sidebar-about-desc">LaperPoll adalah aplikasi resep masakan yang membantu kamu menemukan inspirasi memasak berdasarkan bahan yang tersedia, selera rasa, dan kebutuhan nutrisi harianmu.</p>
            <div class="sidebar-about-features">
                <div class="sidebar-feature-item">
                    <span class="material-icons-round">swipe</span>
                    <p>Swipe Rasa untuk menemukan resep sesuai selera</p>
                </div>
                <div class="sidebar-feature-item">
                    <span class="material-icons-round">kitchen</span>
                    <p>Kulkas Digital untuk masak dari bahan yang ada</p>
                </div>
                <div class="sidebar-feature-item">
                    <span class="material-icons-round">calendar_month</span>
                    <p>Meal Planner untuk mengatur pola makan sehat</p>
                </div>
                <div class="sidebar-feature-item">
                    <span class="material-icons-round">timer</span>
                    <p>Timer Resep untuk panduan memasak step-by-step</p>
                </div>
            </div>
            <p class="sidebar-about-version">Versi 1.0.0 · © 2025 LaperPoll</p>
        </div>
    </div>
</div>

{{-- Tim Kami --}}
<div class="sidebar-panel" id="panel-team">
    <div class="sidebar-panel-box">
        <div class="sidebar-panel-header">
            <button class="sidebar-panel-back" onclick="closeSidebarSection()">
                <span class="material-icons-round">arrow_back</span>
            </button>
            <h2 class="sidebar-panel-title font-bold">Tim Kami</h2>
        </div>
        <div class="sidebar-panel-body">
            <p class="sidebar-team-sub">Kelompok 12 — Pemrograman Web & IMK</p>
            <div class="sidebar-team-grid">
                <div class="sidebar-member-card">
                    <div class="sidebar-member-avatar">
                        <img src="{{ asset('assets/images/team/harmoni.jpg') }}"
                             alt="Harmoni"
                             class="sidebar-member-photo"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <span class="material-icons-round" style="display:none">person</span>
                    </div>
                    <p class="sidebar-member-name font-semibold">Harmoni Natanael S.</p>
                    <p class="sidebar-member-role">Project Lead, Full Stack Dev & UI/UX Designer</p>
                </div>
                <div class="sidebar-member-card">
                    <div class="sidebar-member-avatar">
                        <img src="{{ asset('assets/images/team/ikbal.jpg') }}"
                             alt="Ikbal"
                             class="sidebar-member-photo"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <span class="material-icons-round" style="display:none">person</span>
                    </div>
                    <p class="sidebar-member-name font-semibold">Ikbal Miftahudin</p>
                    <p class="sidebar-member-role">Full Stack Dev, UI/UX Designer & System Architect</p>
                </div>
                <div class="sidebar-member-card">
                    <div class="sidebar-member-avatar">
                        <img src="{{ asset('assets/images/team/ihsan.jpg') }}"
                             alt="Ihsan"
                             class="sidebar-member-photo"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <span class="material-icons-round" style="display:none">person</span>
                    </div>
                    <p class="sidebar-member-name font-semibold">M. Ihsan Ansori</p>
                    <p class="sidebar-member-role">Full Stack Dev & UI/UX Designer</p>
                </div>
                <div class="sidebar-member-card">
                    <div class="sidebar-member-avatar">
                        <img src="{{ asset('assets/images/team/iqbal.jpg') }}"
                             alt="Iqbal"
                             class="sidebar-member-photo"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <span class="material-icons-round" style="display:none">person</span>
                    </div>
                    <p class="sidebar-member-name font-semibold">M. Iqbal Ramadhan</p>
                    <p class="sidebar-member-role">Full Stack Dev & UI/UX Designer</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- FAQ --}}
<div class="sidebar-panel" id="panel-faq">
    <div class="sidebar-panel-box">
        <div class="sidebar-panel-header">
            <button class="sidebar-panel-back" onclick="closeSidebarSection()">
                <span class="material-icons-round">arrow_back</span>
            </button>
            <h2 class="sidebar-panel-title font-bold">FAQ</h2>
        </div>
        <div class="sidebar-panel-body">
            <div class="sidebar-faq-list">
                @php
                $faqs = [
                    ['q' => 'Apa itu LaperPoll?', 'a' => 'LaperPoll adalah aplikasi resep masakan yang membantu kamu menemukan resep berdasarkan bahan, selera, dan kebutuhan nutrisi.'],
                    ['q' => 'Bagaimana cara menggunakan Swipe Rasa?', 'a' => 'Geser kartu ke kanan untuk menyukai rasa, ke kiri untuk melewati. Setelah memilih beberapa rasa, LaperPoll akan menampilkan resep yang cocok.'],
                    ['q' => 'Apakah LaperPoll gratis?', 'a' => 'Ya, LaperPoll sepenuhnya gratis untuk digunakan.'],
                    ['q' => 'Bagaimana cara menambah bahan ke Kulkas Digital?', 'a' => 'Buka menu Kulkas Digital, ketuk tombol tambah, lalu pilih bahan yang kamu miliki beserta jumlahnya.'],
                    ['q' => 'Apakah saya bisa mengubah ulasan yang sudah dikirim?', 'a' => 'Ya, kamu bisa mengedit atau menghapus ulasanmu dari halaman detail resep atau halaman ulasan.'],
                ];
                @endphp
                @foreach($faqs as $faq)
                <div class="sidebar-faq-item" onclick="toggleFaq(this)">
                    <div class="sidebar-faq-q">
                        <p class="font-semibold">{{ $faq['q'] }}</p>
                        <span class="material-icons-round sidebar-faq-arrow">expand_more</span>
                    </div>
                    <p class="sidebar-faq-a">{{ $faq['a'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Hubungi Kami --}}
<div class="sidebar-panel" id="panel-contact">
    <div class="sidebar-panel-box">
        <div class="sidebar-panel-header">
            <button class="sidebar-panel-back" onclick="closeSidebarSection()">
                <span class="material-icons-round">arrow_back</span>
            </button>
            <h2 class="sidebar-panel-title font-bold">Hubungi Kami</h2>
        </div>
        <div class="sidebar-panel-body">
            <p class="sidebar-contact-sub">Ada pertanyaan atau masukan? Hubungi kami melalui:</p>
            <div class="sidebar-contact-list">
                <a href="tel:628990042" class="sidebar-contact-item">
                    <div class="sidebar-contact-icon">
                        <span class="material-icons-round">phone</span>
                    </div>
                    <div>
                        <p class="font-semibold sidebar-contact-label">Telepon</p>
                        <p class="sidebar-contact-value">62-899-0042</p>
                    </div>
                </a>
                <a href="https://instagram.com" target="_blank" class="sidebar-contact-item">
                    <div class="sidebar-contact-icon sidebar-contact-ig">
                        <span class="material-icons-round">photo_camera</span>
                    </div>
                    <div>
                        <p class="font-semibold sidebar-contact-label">Instagram</p>
                        <p class="sidebar-contact-value">@laperpoll</p>
                    </div>
                </a>
            </div>
            <p class="sidebar-contact-hours">Kami melayani pertanyaan pada hari kerja, 09.00–17.00 WIB.</p>
        </div>
    </div>
</div>

{{-- ── UNFOLLOW CONFIRM MODAL ── --}}
<div class="lp-confirm-overlay" id="unfollowOverlay"></div>
<div class="lp-confirm-modal" id="unfollowModal">
    <div class="lp-confirm-box">
        <div class="lp-confirm-icon">👋</div>
        <h3 class="lp-confirm-title font-bold">Berhenti Mengikuti?</h3>
        <p class="lp-confirm-sub">Kamu tidak akan melihat konten dari user ini di feedmu.</p>
        <div class="lp-confirm-actions">
            <button class="lp-confirm-cancel font-semibold" onclick="closeUnfollowConfirm()">Batal</button>
            <button class="lp-confirm-ok font-semibold" onclick="confirmUnfollow()"
                    style="background:var(--orange-normal);box-shadow:0 4px 12px rgba(230,81,0,0.3)">
                Ya, Berhenti
            </button>
        </div>
    </div>
</div>

{{-- ── FOLLOW MODAL ── --}}
<div class="follow-overlay" id="followOverlay" onclick="closeFollowModal()"></div>
<div class="follow-modal" id="followModal">
    <div class="follow-modal-handle"></div>
    <div class="follow-modal-header">
        <h3 class="follow-modal-title font-bold" id="followModalTitle">Pengikut</h3>
        <button class="follow-modal-close" onclick="closeFollowModal()">
            <span class="material-icons-round">close</span>
        </button>
    </div>
    <div class="follow-modal-body" id="followModalBody">
        <div class="follow-loading" id="followLoading">
            <span class="material-icons-round follow-spin">autorenew</span>
            <p>Memuat...</p>
        </div>
        <div class="follow-list" id="followList"></div>
        <div class="follow-empty" id="followEmpty" style="display:none">
            <span class="material-icons-round">person_off</span>
            <p id="followEmptyText">Belum ada pengikut</p>
        </div>
    </div>
</div>

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
<script>
    const PROFILE_USER_ID = {{ Auth::id() ?? 'null' }};
</script>
<script src="{{ asset('js/profile.js') }}"></script>
@endpush

@endsection