@extends('layouts.app')

@section('title', 'Edit Profil - LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/edit-profile.css') }}">
@endpush

@section('content')
<main class="ep-main font-jakarta">

    <x-navbar :backUrl="route('profile.index')"></x-navbar>

    <div class="ep-container">

        {{-- Page Header --}}
        <div class="ep-page-header">
            <h1 class="ep-page-title font-bold">Edit Profil</h1>
            <p class="ep-page-sub">Perbarui informasi akun kamu</p>
        </div>

        {{-- Alert sukses --}}
        @if(session('success'))
            <div class="ep-alert ep-alert-success">
                <span class="material-icons-round">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        {{-- Alert error --}}
        @if($errors->any())
            <div class="ep-alert ep-alert-error">
                <span class="material-icons-round">error</span>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="ep-form">
            @csrf
            @method('PATCH')

            {{-- Avatar Upload --}}
            <div class="ep-avatar-section">
                <div class="ep-avatar-wrapper" id="avatarWrapper">
                    <img
                        id="avatarPreview"
                        src="{{ $user->profile_photo
                            ? Storage::url($user->profile_photo)
                            : asset('assets/images/Image_DummyProfile.png') }}"
                        alt="Foto Profil"
                        class="ep-avatar-img">
                    <div class="ep-avatar-overlay">
                        <span class="material-icons-round">photo_camera</span>
                        <span class="ep-avatar-overlay-text">Ganti Foto</span>
                    </div>
                </div>
                <input
                    type="file"
                    id="profilePhotoInput"
                    name="profile_photo"
                    accept="image/jpg,image/jpeg,image/png,image/webp"
                    class="hidden-input">
                <p class="ep-avatar-hint">JPG, PNG, WEBP · Maks. 2 MB</p>
            </div>

            {{-- Form Fields --}}
            <div class="ep-fields">

                <div class="ep-field-group">
                    <label for="name" class="ep-label">
                        <span class="material-icons-round">person</span>
                        Nama Lengkap
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        placeholder="Masukkan nama lengkap"
                        class="ep-input @error('name') ep-input-error @enderror"
                        required>
                    @error('name')
                        <span class="ep-field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="ep-field-group">
                    <label for="email" class="ep-label">
                        <span class="material-icons-round">email</span>
                        Email
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        placeholder="contoh@email.com"
                        class="ep-input @error('email') ep-input-error @enderror"
                        required>
                    @error('email')
                        <span class="ep-field-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Password (opsional) --}}
                <div class="ep-field-divider">
                    <span>Ganti Password (opsional)</span>
                </div>

                <div class="ep-field-group">
                    <label for="password" class="ep-label">
                        <span class="material-icons-round">lock</span>
                        Password Baru
                    </label>
                    <div class="ep-input-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Minimal 6 karakter"
                            class="ep-input @error('password') ep-input-error @enderror"
                            autocomplete="new-password">
                        <button type="button" class="ep-toggle-pass" id="togglePassword" aria-label="Tampilkan password">
                            <span class="material-icons-round" id="togglePassIcon">visibility_off</span>
                        </button>
                    </div>
                    @error('password')
                        <span class="ep-field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="ep-field-group">
                    <label for="password_confirmation" class="ep-label">
                        <span class="material-icons-round">lock_reset</span>
                        Konfirmasi Password
                    </label>
                    <div class="ep-input-wrapper">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Ulangi password baru"
                            class="ep-input"
                            autocomplete="new-password">
                        <button type="button" class="ep-toggle-pass" id="toggleConfirm" aria-label="Tampilkan password">
                            <span class="material-icons-round" id="toggleConfirmIcon">visibility_off</span>
                        </button>
                    </div>
                </div>

            </div>

            {{-- Submit --}}
            <div class="ep-actions">
                <button type="submit" class="ep-btn-save font-semibold">
                    <span class="material-icons-round">save</span>
                    Simpan Perubahan
                </button>
                <a href="{{ route('profile.index') }}" class="ep-btn-cancel font-semibold">
                    Batal
                </a>
            </div>

        </form>
    </div>
</main>

@push('scripts')
<script src="{{ asset('js/edit-profile.js') }}"></script>
@endpush

@endsection