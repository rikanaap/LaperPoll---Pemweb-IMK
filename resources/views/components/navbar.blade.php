@props(['backUrl' => null]) {{-- Defaultnya null kalau tidak diisi --}}

<nav class="navbar">
    {{-- Jika backUrl diisi, tampilkan icon back. Jika tidak, tampilkan search --}}
    @if($backUrl)
        <a href="{{ $backUrl }}" class="back-btn">
            <span class="material-icons-round text-h4">arrow_back</span>
        </a>
    @else
        <span id="searchButton" class="material-icons-round text-h4" onclick="changePage('search')">
            search
        </span>
    @endif

    <img src="{{ asset('assets/images/Logo_Laperpoll.png') }}" alt="Logo Laperpoll" class="logo">
    
    <a href="{{ route('profile.index') }}">
        <img src="{{ asset('assets/images/Image_DummyProfile.png') }}" alt="Profil Foto" class="profile">
    </a>
</nav>