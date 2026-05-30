<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">
    @stack('styles')
    <title>@yield('title', 'Admin') — LaperPoll</title>
</head>
<body>
<div class="admin-layout">

    <aside class="sidebar">
        <div class="sidebar__brand">
            <div class="sidebar__brand-icon">
                <span class="material-icons-round">restaurant</span>
            </div>
            <div class="sidebar__brand-text">
                <span class="sidebar__brand-name">LaperPoll</span>
                <span class="sidebar__brand-sub">Admin Panel</span>
            </div>
        </div>

        <nav class="sidebar__nav">
            <span class="sidebar__section-label">Overview</span>

            <a href="{{ route('admin.dashboard') }}" class="sidebar__link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                <span class="material-icons-round">dashboard</span>
                Dashboard
            </a>

            <span class="sidebar__section-label">Konten</span>

            <a href="{{ route('admin.resep.index') }}" class="sidebar__link {{ request()->routeIs('admin.resep.*') ? 'is-active' : '' }}">
                <span class="material-icons-round">menu_book</span>
                Resep
            </a>

            <span class="sidebar__section-label">Pengguna</span>

            <a href="{{ route('admin.user.index') }}" class="sidebar__link {{ request()->routeIs('admin.user.*') ? 'is-active' : '' }}">
                <span class="material-icons-round">group</span>
                User
            </a>

            <span class="sidebar__section-label">Bahan</span>

            <a href="{{ route('admin.bahan.index') }}" class="sidebar__link {{ request()->routeIs('admin.bahan.*') ? 'is-active' : '' }}">
                <span class="material-icons-round">local_grocery_store</span>
                Bahan
            </a>
        </nav>


        <div class="sidebar__footer">
            <div class="sidebar__user">
                <div class="sidebar__user-avatar">
                    <span class="material-icons-round">person</span>
                </div>
                <div class="sidebar__user-info">
                    <div class="sidebar__user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <div class="sidebar__user-role">Super Admin</div>
                </div>
                <span class="material-icons-round">more_vert</span>
            </div>
        </div>
    </aside>

    <div class="admin-main">
        <header class="topbar">
            <div class="topbar__title">
                <div class="topbar__page-title">@yield('page-title', 'Dashboard')</div>
                <div class="topbar__breadcrumb">@yield('breadcrumb', 'Admin / Dashboard')</div>
            </div>
            <div class="topbar__actions">
                <div class="topbar__search">
                    <span class="material-icons-round">search</span>
                    <input type="text" placeholder="Cari sesuatu...">
                </div>
                <button class="topbar__btn" type="button">
                    <span class="material-icons-round">notifications</span>
                    <span class="topbar__btn-dot"></span>
                </button>
            </div>
        </header>

        <main class="page-content">
            @yield('content')
        </main>
    </div>

</div>

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