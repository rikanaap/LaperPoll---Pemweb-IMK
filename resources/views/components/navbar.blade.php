<nav class="navbar">
    <span id="searchButton" class="material-icons-round text-h4" onclick="changePage('search')">{{ $back ? "arrow_back" : "search" }}</span>
    <img src="{{ asset('assets/images/Logo_Laperpoll.png') }}" alt="Logo Laperpoll" class="logo">
    <img src="{{ asset('assets/images/Image_DummyProfile.png') }}" alt="Profil Foto" class="profile" onclick="changePage('profile')">
</nav>