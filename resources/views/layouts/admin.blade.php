<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/Logo_Laperpoll.png') }}">
<link rel="apple-touch-icon" href="{{ asset('assets/images/Logo_Laperpoll.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">
    @stack('styles')
    <title>@yield('title', 'Admin') — LaperPoll</title>
</head>
<body>
<div class="admin-layout">

    {{-- Overlay mobile --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- Sidebar --}}
    <aside class="sidebar" id="sidebar">
            <div class="sidebar__brand">
                <div class="sidebar__brand-icon">
                    <img src="{{ asset('assets/images/Logo_Laperpoll.png') }}" alt="LaperPoll" style="width:22px;height:22px;object-fit:contain;">
                </div>
                <div class="sidebar__brand-text">
                    <span class="sidebar__brand-name">LaperPoll</span>
                    <span class="sidebar__brand-sub">Admin Panel</span>
                </div>
            </div>

        <nav class="sidebar__nav">

            <span class="sidebar__section-label">Overview</span>

            <a href="{{ route('admin.dashboard') }}"
               class="sidebar__link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                <span class="material-icons-round">dashboard</span>
                Dashboard
            </a>

            <span class="sidebar__section-label">Konten</span>

            <a href="{{ route('admin.resep.index') }}"
               class="sidebar__link {{ request()->routeIs('admin.resep.*') ? 'is-active' : '' }}">
                <span class="material-icons-round">menu_book</span>
                Resep
            </a>

            <a href="{{ route('admin.bahan.index') }}"
               class="sidebar__link {{ request()->routeIs('admin.bahan.*') ? 'is-active' : '' }}">
                <span class="material-icons-round">kitchen</span>
                Bahan
            </a>

            <a href="{{ route('admin.filter.index') }}"
               class="sidebar__link {{ request()->routeIs('admin.filter.*') ? 'is-active' : '' }}">
                <span class="material-icons-round">filter_list</span>
                Filter
            </a>

            <span class="sidebar__section-label">Pengguna</span>

            <a href="{{ route('admin.user.index') }}"
               class="sidebar__link {{ request()->routeIs('admin.user.*') ? 'is-active' : '' }}">
                <span class="material-icons-round">group</span>
                User
            </a>

        </nav>

        <div class="sidebar__footer">

            {{-- Info user --}}
            <!-- <div class="sidebar__user">
                <div class="sidebar__user-avatar">
                    @if(auth()->user()?->profile_photo)
                        <img src="{{ Storage::url(auth()->user()->profile_photo) }}" alt="">
                    @else
                        <span class="material-icons-round">person</span>
                    @endif
                </div>
                <div class="sidebar__user-info">
                    <div class="sidebar__user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <div class="sidebar__user-role">{{ auth()->user()?->is_admin ? 'Super Admin' : 'Admin' }}</div>
                </div>
            </div> -->

            {{-- ✅ Tombol logout --}}
            <form method="POST" action="{{ route('auth.logout') }}">
                @csrf
                <button type="submit" class="sidebar__logout">
                    <span class="material-icons-round">logout</span>
                    Keluar
                </button>
            </form>

        </div>

    </aside>

    {{-- Main --}}
    <div class="admin-main">

        <header class="topbar">

            <button class="topbar__hamburger" id="sidebarToggle" type="button" aria-label="Buka menu">
                <span class="material-icons-round">menu</span>
            </button>

            <div class="topbar__title">
                <div class="topbar__page-title">@yield('page-title', 'Dashboard')</div>
                <div class="topbar__breadcrumb">@yield('breadcrumb', 'Admin / Dashboard')</div>
            </div>

            <!-- <div class="topbar__actions">
                <button class="topbar__btn" type="button" title="Notifikasi">
                    <span class="material-icons-round">notifications</span>
                    <span class="topbar__btn-dot"></span>
                </button>
            </div> -->

        </header>

        <main class="page-content">
            @yield('content')
        </main>

    </div>

</div>

{{-- Global Modal --}}
<div id="modalOverlay" class="modal-overlay">
    <div class="modal" id="modal">
        <div class="modal__header">
            <span class="modal__title" id="modalTitle">Modal</span>
            <button class="modal__close" id="modalClose" type="button">
                <span class="material-icons-round">close</span>
            </button>
        </div>
        <div class="modal__body" id="modalBody"></div>
        <div class="modal__footer" id="modalFooter"></div>
    </div>
</div>

<script src="{{ asset('js/admin/admin.js') }}"></script>
@stack('scripts')

</body>
</html>