<nav class="navbar">

    @php
    $backUrl = $backUrl ?? false;
    @endphp
    {{-- Jika kosong -> search --}}
    @if (!$backUrl)

    <a href="{{ route('pencarian.resep') }}" class="back-btn">
        <span class="material-icons-round text-h4">search</span>
    </a>

    {{-- Jika "back" -> history back --}}
    @elseif ($backUrl == 'back')
    <a href="javascript:void(0)" onclick="window.history.length > 1 ? window.history.back() : window.location.href = '/';" class="back-btn">
        <span class="material-icons-round text-h4">arrow_back</span>
    </a>

    {{-- Jika URL biasa --}}
    @else
    <a href="{{ $backUrl }}" class="back-btn">
        <span class="material-icons-round text-h4">arrow_back</span>
    </a>

    @endif

    <a href="{{ route('landing.index') }}">
        <img src="{{ asset('assets/images/Logo_Laperpoll.png') }}"
            alt="Logo Laperpoll"
            class="logo">
    </a>

    <a href="{{ route('profile.index') }}">
        <img src="{{ asset('assets/images/Image_DummyProfile.png') }}"
            alt="Profil Foto"
            class="profile">
    </a>

</nav>