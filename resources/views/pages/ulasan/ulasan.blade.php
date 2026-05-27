@extends('layouts.app')

@section('title', 'Ulasan - {{ $resep->title }}')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/ulasan.css') }}">
@endpush

@section('content')
<main class="ul-main font-jakarta">

    <x-navbar :backUrl="route('detail.resep', $resep->id)"></x-navbar>

    {{-- ── HEADER RESEP ── --}}
    <div class="ul-resep-header">
        <div class="ul-resep-thumb-wrap">
            @if($resep->thumbnail)
                <img src="{{ asset($resep->thumbnail) }}" alt="{{ $resep->title }}"
                     class="ul-resep-thumb"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <div class="ul-resep-thumb-placeholder" style="display:none">
                    <span class="material-icons-round">restaurant</span>
                </div>
            @else
                <div class="ul-resep-thumb-placeholder">
                    <span class="material-icons-round">restaurant</span>
                </div>
            @endif
        </div>
        <div class="ul-resep-info">
            <h1 class="ul-resep-title font-bold">{{ $resep->title }}</h1>
            <p class="ul-resep-author">
                <span class="material-icons-round">person</span>
                {{ $resep->user->name ?? 'Anonim' }}
            </p>
            <div class="ul-resep-rating">
                @php
                    $avg  = $resep->feedbacks->avg('rating') ?? 0;
                    $full = floor($avg); $half = ($avg - $full) >= 0.3 ? 1 : 0;
                    $empty = 5 - $full - $half;
                @endphp
                @for($i = 0; $i < $full;  $i++) <span class="material-icons-round ul-star-filled">star</span> @endfor
                @if($half)                        <span class="material-icons-round ul-star-filled">star_half</span> @endif
                @for($i = 0; $i < $empty; $i++) <span class="material-icons-round ul-star-empty">star_border</span> @endfor
                <span class="ul-rating-text">{{ $avg > 0 ? number_format($avg,1) : '-' }} ({{ $resep->feedbacks->count() }})</span>
            </div>
        </div>
    </div>

    {{-- ── ALERT ── --}}
    @if(session('success'))
        <div class="ul-alert ul-alert-success">
            <span class="material-icons-round">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="ul-alert ul-alert-error">
            <span class="material-icons-round">error</span>
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="ul-alert ul-alert-error">
            <span class="material-icons-round">error</span>
            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- ── FORM ULASAN ── --}}
    @auth
        @if(!$sudahUlasan)
            <div class="ul-form-card">
                <h2 class="ul-form-title font-semibold">
                    {{ $dariTimer ? 'Bagikan Hasil Masakanmu' : 'Tulis Ulasanmu' }}
                </h2>
                <p class="ul-form-sub">
                    {{ $dariTimer
                        ? 'Ceritakan pengalamanmu masak resep ini — tips, hasil, dan foto masakanmu!'
                        : 'Bagaimana pengalaman memasak resep ini?' }}
                </p>

                <form action="{{ route('ulasan.store', $resep->id) }}" method="POST"
                      enctype="multipart/form-data" class="ul-form" id="ulasanForm">
                    @csrf

                    {{-- Rating bintang --}}
                    <div class="ul-rating-section">
                        <p class="ul-field-label">
                            <span class="material-icons-round">star</span>
                            Rating
                        </p>
                        <div class="ul-stars" id="ulStars">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="material-icons-round ul-star-pick" data-val="{{ $i }}">star_border</span>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" id="ulRatingInput" value="0">
                        <p class="ul-rating-hint" id="ulRatingHint">Pilih bintang</p>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="ul-field-group">
                        <label class="ul-field-label" for="ulDesc">
                            <span class="material-icons-round">rate_review</span>
                            Ceritakan pengalamanmu
                        </label>
                        <textarea id="ulDesc" name="description" class="ul-textarea"
                                  placeholder="Bagaimana rasanya? Tips memasak? Apa yang kamu suka?" rows="4">{{ old('description') }}</textarea>
                    </div>

                    {{-- Upload foto --}}
                    <div class="ul-field-group">
                        <p class="ul-field-label">
                            <span class="material-icons-round">photo_camera</span>
                            Foto hasil masakan <span class="ul-optional">(opsional, maks. 3)</span>
                        </p>
                        <div class="ul-photo-upload" id="ulPhotoUpload">
                            <span class="material-icons-round ul-photo-icon">add_photo_alternate</span>
                            <span class="ul-photo-text">Ketuk untuk tambah foto</span>
                            <input type="file" name="photos[]" id="ulPhotoInput"
                                   accept="image/*" multiple class="ul-hidden-input">
                        </div>
                        <div class="ul-photo-grid" id="ulPhotoGrid"></div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="ul-btn-submit font-semibold" id="ulBtnSubmit">
                        <span class="material-icons-round">send</span>
                        Kirim Ulasan
                    </button>

                    <a href="{{ route('detail.resep', $resep->id) }}" class="ul-btn-skip font-semibold">
                        Lewati
                    </a>

                </form>
            </div>
        @else
            <div class="ul-sudah-card">
                <span class="material-icons-round ul-sudah-icon">check_circle</span>
                <p class="ul-sudah-title font-bold">Ulasan sudah dikirim!</p>
                <p class="ul-sudah-sub">Kamu sudah memberikan ulasan untuk resep ini.</p>
                <a href="{{ route('detail.resep', $resep->id) }}" class="ul-btn-back font-semibold">
                    <span class="material-icons-round">arrow_back</span>
                    Kembali ke Resep
                </a>
            </div>
        @endif
    @else
        <div class="ul-login-card">
            <span class="material-icons-round ul-login-icon">lock</span>
            <p class="ul-login-title font-bold">Login dulu yuk!</p>
            <p class="ul-login-sub">Kamu perlu login untuk memberikan ulasan.</p>
            <a href="{{ route('auth.sign-in') }}" class="ul-btn-submit font-semibold">
                <span class="material-icons-round">login</span>
                Login Sekarang
            </a>
        </div>
    @endauth

    {{-- ── DAFTAR ULASAN ── --}}
    <section class="ul-list-section">
        <div class="ul-list-header">
            <h2 class="ul-list-title font-semibold">Semua Ulasan</h2>
            <span class="ul-list-count">{{ $resep->feedbacks->count() }} ulasan</span>
        </div>

        @if($resep->feedbacks->isEmpty())
            <div class="ul-empty">
                <span class="material-icons-round">chat_bubble_outline</span>
                <p class="ul-empty-title font-semibold">Belum ada ulasan</p>
                <p class="ul-empty-sub">Jadilah yang pertama memberikan ulasan!</p>
            </div>
        @else
            <div class="ul-list">
                @foreach($resep->feedbacks->sortByDesc('created_at') as $fb)
                    <div class="ul-item">
                        <div class="ul-item-header">
                            <img src="{{ $fb->user->profile_photo
                                ? Storage::url($fb->user->profile_photo)
                                : asset('assets/images/Image_DummyProfile.png') }}"
                                 alt="{{ $fb->user->name ?? 'User' }}"
                                 class="ul-item-avatar">
                            <div class="ul-item-user">
                                <span class="ul-item-name font-semibold">{{ $fb->user->name ?? 'User' }}</span>
                                <span class="ul-item-date">{{ $fb->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="ul-item-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="material-icons-round {{ $i <= $fb->rating ? 'ul-star-filled' : 'ul-star-empty' }}">
                                        {{ $i <= $fb->rating ? 'star' : 'star_border' }}
                                    </span>
                                @endfor
                            </div>
                        </div>
                        @if($fb->description)
                            <p class="ul-item-desc">{{ $fb->description }}</p>
                        @endif
                        @if($fb->photos->isNotEmpty())
                            <div class="ul-item-photos">
                                @foreach($fb->photos as $photo)
                                    <img src="{{ asset($photo->path) }}" alt="Foto ulasan"
                                         class="ul-item-photo" onclick="openModal(this.src)">
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </section>

</main>

{{-- Photo modal --}}
<div class="ul-modal" id="ulModal" onclick="closeModal()">
    <button class="ul-modal-close"><span class="material-icons-round">close</span></button>
    <img src="" id="ulModalImg" class="ul-modal-img" alt="Foto ulasan">
</div>

@push('scripts')
<script src="{{ asset('js/ulasan.js') }}"></script>
@endpush

@endsection