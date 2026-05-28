<nav class="navbar">

    @php
    $backUrl   = $backUrl   ?? false;
    $hamburger = $hamburger ?? false;
    $user      = $user      ?? Auth::user();
    @endphp

    {{-- Kiri: back / search --}}
    @if (!$backUrl)
    <a href="{{ route('pencarian.resep') }}" class="back-btn">
        <span class="material-icons-round text-h4">search</span>
    </a>
    @elseif ($backUrl == 'back')
    <a href="javascript:void(0)" onclick="window.history.length > 1 ? window.history.back() : window.location.href = '/';" class="back-btn">
        <span class="material-icons-round text-h4">arrow_back</span>
    </a>
    @else
    <a href="{{ $backUrl }}" class="back-btn">
        <span class="material-icons-round text-h4">arrow_back</span>
    </a>
    @endif

    {{-- Tengah: logo --}}
    <a href="{{ route('landing.index') }}">
        <img src="{{ asset('assets/images/Logo_Laperpoll.png') }}"
             alt="Logo Laperpoll"
             class="logo">
    </a>

    {{-- Kanan: hamburger (profile page) ATAU foto profil (halaman lain) --}}
    @if($hamburger)
    <button class="navbar-hamburger-btn" id="profileHamburger" aria-label="Buka menu">
        <div class="navbar-hamburger-inner">
            @if($user)
            <img src="{{ $user->profile_photo
                ? Storage::url($user->profile_photo)
                : asset('assets/images/Image_DummyProfile.png') }}"
                alt="Foto profil"
                class="navbar-hamburger-avatar">
            @endif
            <div class="navbar-hamburger-lines">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </button>
    @elseif($user)
    <a href="{{ route('profile.index') }}">
        <img src="{{ asset('assets/images/Image_DummyProfile.png') }}"
             alt="Profil Foto"
             class="profile">
    </a>
    @else
    <a href="{{ route('auth.sign-in') }}">
        <span class="material-icons-round text-h4 profile text-[#B62925]">person</span>
    </a>
    @endif

</nav>