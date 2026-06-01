@extends('layouts.app')

@section('title', '{{ $resep->title }} - LaperPoll')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/detail-resep.css') }}">
@endpush

@section('content')
<main class="dr-main font-jakarta" data-recipe-id="{{ $resep->id }}">

    <x-navbar backUrl="back"></x-navbar>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="dr-flash dr-flash-success">
            <span class="material-icons-round">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="dr-flash dr-flash-error">
            <span class="material-icons-round">error</span>
            {{ session('error') }}
        </div>
    @endif

    {{-- ── HERO IMAGE ── --}}
    <section class="dr-hero">
        @if($resep->thumbnail)
             <img src="{{ $resep->thumbnail_url }}" alt="{{ $resep->title }}" class="dr-hero-img"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            <div class="dr-hero-placeholder" style="display:none">
                <span class="material-icons-round">restaurant</span>
            </div>
        @else
            <div class="dr-hero-placeholder">
                <span class="material-icons-round">restaurant</span>
            </div>
        @endif

        {{-- Favorite btn --}}
        <button class="dr-fav-btn {{ $isFavorited ? 'active' : '' }}"
                id="drFavBtn"
                data-resep-id="{{ $resep->id }}"
                aria-label="Favoritkan resep">
            <span class="material-icons-round">{{ $isFavorited ? 'favorite' : 'favorite_border' }}</span>
        </button>
    </section>

    {{-- ── INFO RESEP ── --}}
    <section class="dr-info-section">
        <div class="dr-info-left">
            <h1 class="dr-title font-bold">{{ $resep->title }}</h1>
            <div class="dr-meta-row">
                <span class="dr-meta-chip">
                    <span class="material-icons-round">schedule</span>
                    {{ $resep->cook_duration_formatted }}
                </span>
                @if($resep->calorie)
                    <span class="dr-meta-chip">
                        <span class="material-icons-round">local_fire_department</span>
                        {{ $resep->calorie }} kkal
                    </span>
                @endif
                @foreach($resep->filters->take(2) as $filter)
                    <span class="dr-meta-chip dr-chip-filter">{{ $filter->name }}</span>
                @endforeach
            </div>
        </div>

        <div class="dr-info-right">
            {{-- Author --}}
            <div class="dr-author">
                <a href="{{ route('profile.public', $resep->user_id) }}" class="dr-author-link">
                    <img src="{{ $resep->user->profile_photo
                        ? Storage::url($resep->user->profile_photo)
                        : asset('assets/images/Image_DummyProfile.png') }}"
                         alt="{{ $resep->user->name ?? 'Anonim' }}"
                         class="dr-author-avatar">
                    <div class="dr-author-text">
                        <span class="dr-author-label">Dibuat oleh</span>
                        <span class="dr-author-name font-semibold">{{ $resep->user->name ?? 'Anonim' }}</span>
                    </div>
                </a>

                {{-- Tombol follow — sembunyikan kalau resep milik sendiri atau guest --}}
                @auth
                    @if(Auth::id() !== $resep->user_id)
                        <button class="dr-follow-btn {{ $isFollowing ? 'following' : '' }}"
                                id="drFollowBtn"
                                data-user-id="{{ $resep->user_id }}">
                            <span class="material-icons-round">
                                {{ $isFollowing ? 'person_remove' : 'person_add' }}
                            </span>
                            <span class="dr-follow-label">
                                {{ $isFollowing ? 'Mengikuti' : 'Ikuti' }}
                            </span>
                        </button>
                    @endif
                @endauth
            </div>

            {{-- Rating --}}
            <div class="dr-rating">
                @php
                    $star  = $ratingAvg;
                    $full  = floor($star);
                    $half  = ($star - $full) >= 0.3 ? 1 : 0;
                    $empty = 5 - $full - $half;
                @endphp
                <div class="dr-stars">
                    @for($i = 0; $i < $full; $i++)
                        <span class="material-icons-round">star</span>
                    @endfor
                    @if($half)
                        <span class="material-icons-round">star_half</span>
                    @endif
                    @for($i = 0; $i < $empty; $i++)
                        <span class="material-icons-round">star_border</span>
                    @endfor
                </div>
                <span class="dr-rating-num">{{ $ratingAvg > 0 ? $ratingAvg : '-' }}</span>
                <span class="dr-rating-count">({{ $totalUlasan }})</span>
            </div>
        </div>
    </section>

    {{-- ── BAHAN-BAHAN ── --}}
    <section class="dr-card">
        <div class="dr-card-header">
            <h2 class="dr-card-title font-semibold">Bahan-bahan</h2>
            <div class="dr-unit-toggle" id="unitToggle">
                <span class="material-icons-round dr-unit-icon">scale</span>
                <span class="dr-unit-label" id="unitLabel">Gram</span>
                <span class="material-icons-round dr-unit-arrow" id="unitArrow">expand_more</span>
                <div class="dr-unit-dropdown" id="unitDropdown">
                    <button class="dr-unit-option active" data-value="gram">Gram</button>
                    <button class="dr-unit-option" data-value="miligram">Miligram</button>
                    <button class="dr-unit-option" data-value="kilogram">Kilogram</button>
                    <button class="dr-unit-option" data-value="sdm">Sdm</button>
                </div>
            </div>
        </div>

        <div class="dr-chips-grid">
            @forelse($resep->bahans as $bahan)
                <div class="dr-chip" data-gram="{{ $bahan->pivot->gram_total }}">
                    <span class="dr-chip-amt">{{ $bahan->pivot->gram_total }}g</span>
                    {{ $bahan->nama }}
                </div>
            @empty
                <p class="dr-empty-text">Bahan belum tersedia.</p>
            @endforelse
        </div>
    </section>

    {{-- ── LANGKAH MEMASAK ── --}}
    <section class="dr-card">
        <h2 class="dr-card-title font-semibold" style="margin-bottom:1rem">Cara Membuat</h2>

        @forelse($resep->langkahs->sortBy('step_order') as $langkah)
            <div class="dr-step-item {{ !$loop->last ? 'dr-step-line' : '' }}">
                <div class="dr-step-num font-bold">{{ $langkah->step_order }}</div>
                <div class="dr-step-body">
                    @if($langkah->step_duration && $langkah->step_duration !== '00:00:00')
                        <span class="dr-step-duration">
                            <span class="material-icons-round">timer</span>
                            {{ \Carbon\Carbon::createFromFormat('H:i:s', $langkah->step_duration)->format('i') }} menit
                        </span>
                    @endif
                    <p class="dr-step-text">{{ $langkah->description }}</p>
                </div>
            </div>
        @empty
            <div class="dr-step-empty">
                <span class="material-icons-round">info_outline</span>
                <p>Langkah memasak belum tersedia untuk resep ini.</p>
            </div>
        @endforelse
    </section>

    {{-- ── SECTION ULASAN ── --}}
    <section class="dr-ulasan-section">
        <div class="dr-ulasan-header">
            <h2 class="dr-card-title font-semibold">Ulasan</h2>
            <span class="dr-ulasan-count">{{ $totalUlasan }} ulasan</span>
        </div>

        {{-- Rating breakdown --}}
        @if($totalUlasan > 0)
        <div class="dr-rating-breakdown">
            <div class="dr-rating-big">
                <span class="dr-rating-big-num font-bold">{{ $ratingAvg }}</span>
                <div class="dr-rating-big-stars">
                    @php $full=$ratingAvg|0; $half=($ratingAvg-$full)>=0.3?1:0; $empty=5-$full-$half; @endphp
                    @for($i=0;$i<$full;$i++) <span class="material-icons-round">star</span> @endfor
                    @if($half) <span class="material-icons-round">star_half</span> @endif
                    @for($i=0;$i<$empty;$i++) <span class="material-icons-round">star_border</span> @endfor
                </div>
                <span class="dr-rating-big-total">dari {{ $totalUlasan }} ulasan</span>
            </div>
            <div class="dr-rating-bars">
                @foreach($ratingBreakdown as $star => $data)
                <div class="dr-rating-bar-row">
                    <span class="dr-rating-bar-label">{{ $star }}</span>
                    <span class="material-icons-round dr-rating-bar-star">star</span>
                    <div class="dr-rating-bar-track">
                        <div class="dr-rating-bar-fill" style="width:{{ $data['percent'] }}%"></div>
                    </div>
                    <span class="dr-rating-bar-count">{{ $data['count'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Form / sudah ulasan --}}
        @auth
            @if(!$sudahUlasan)
                <div class="dr-ulasan-form-card">
                    <p class="dr-ulasan-form-title font-semibold">Tulis Ulasanmu</p>
                    <form action="{{ route('ulasan.store', $resep->id) }}" method="POST"
                          enctype="multipart/form-data" class="dr-ulasan-form" id="drUlasanForm">
                        @csrf
                        <div class="dr-star-input" id="starInput">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="material-icons-round dr-star-pick" data-val="{{ $i }}">star_border</span>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" value="0">
                        <textarea name="description" class="dr-ulasan-textarea"
                                  placeholder="Bagaimana pengalamanmu memasak resep ini?" rows="3"></textarea>
                        <div class="dr-photo-upload" id="photoUploadArea">
                            <span class="material-icons-round">add_photo_alternate</span>
                            <span class="dr-photo-upload-text">Tambah foto (opsional, maks. 3)</span>
                            <input type="file" name="photos[]" id="photoInput"
                                   accept="image/*" multiple class="hidden-input">
                        </div>
                        <div class="dr-photo-previews" id="photoPreviews"></div>
                        <button type="submit" class="dr-ulasan-submit font-semibold">
                            <span class="material-icons-round">send</span>
                            Kirim Ulasan
                        </button>
                    </form>
                </div>
            @else
                {{-- Tampilkan ulasan sendiri + tombol edit/hapus --}}
                <div class="dr-my-ulasan-card">
                    <div class="dr-my-ulasan-top">
                        <span class="dr-my-ulasan-badge font-semibold">
                            <span class="material-icons-round">verified</span>
                            Ulasanmu
                        </span>
                        <div class="dr-my-ulasan-actions">
                            <a href="{{ route('ulasan.edit', [$resep->id, $myFeedback->id]) }}"
                               class="dr-ulasan-action-btn dr-ulasan-edit">
                                <span class="material-icons-round">edit</span>
                                Edit
                            </a>
                            <form action="{{ route('ulasan.destroy', [$resep->id, $myFeedback->id]) }}"
                                  method="POST" id="drDeleteForm">
                                @csrf @method('DELETE')
                                <button type="button" class="dr-ulasan-action-btn dr-ulasan-delete"
                                        onclick="drConfirmDelete()">
                                    <span class="material-icons-round">delete</span>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="dr-my-stars">
                        @for($i=1;$i<=5;$i++)
                            <span class="material-icons-round {{ $i<=$myFeedback->rating ? 'dr-star-on' : 'dr-star-off' }}">
                                {{ $i<=$myFeedback->rating ? 'star' : 'star_border' }}
                            </span>
                        @endfor
                        <span class="dr-my-date">{{ $myFeedback->created_at->diffForHumans() }}</span>
                    </div>
                    @if($myFeedback->description)
                        <p class="dr-my-desc">{{ $myFeedback->description }}</p>
                    @endif
                    @if($myFeedback->photos && $myFeedback->photos->isNotEmpty())
                        <div class="dr-ulasan-photos">
                            @foreach($myFeedback->photos as $photo)
                                <img src="{{ asset($photo->path) }}" alt="Foto ulasan"
                                     class="dr-ulasan-photo" onclick="openPhotoModal(this.src)">
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        @else
            <a href="{{ route('auth.sign-in') }}" class="dr-login-prompt">
                <span class="material-icons-round">login</span>
                Login untuk menulis ulasan
            </a>
        @endauth

        {{-- Daftar ulasan --}}
        @if($resep->feedbacks->isEmpty())
            <div class="dr-ulasan-empty">
                <span class="material-icons-round">chat_bubble_outline</span>
                <p>Belum ada ulasan. Jadilah yang pertama!</p>
            </div>
        @else
            <div class="dr-ulasan-list">
                @foreach($resep->feedbacks->sortByDesc('created_at') as $fb)
                    @if(Auth::check() && $fb->user_id === Auth::id()) @continue @endif
                    <div class="dr-ulasan-item">
                        <div class="dr-ulasan-user">
                            <img src="{{ $fb->user->profile_photo
                                ? Storage::url($fb->user->profile_photo)
                                : asset('assets/images/Image_DummyProfile.png') }}"
                                 alt="{{ $fb->user->name ?? 'User' }}"
                                 class="dr-ulasan-avatar">
                            <div class="dr-ulasan-user-info">
                                <span class="dr-ulasan-name font-semibold">{{ $fb->user->name ?? 'User' }}</span>
                                <span class="dr-ulasan-date">{{ $fb->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="dr-ulasan-stars">
                                @for($i=1;$i<=5;$i++)
                                    <span class="material-icons-round {{ $i<=$fb->rating ? 'star-filled' : 'star-empty' }}">
                                        {{ $i<=$fb->rating ? 'star' : 'star_border' }}
                                    </span>
                                @endfor
                            </div>
                        </div>
                        @if($fb->description)
                            <p class="dr-ulasan-desc">{{ $fb->description }}</p>
                        @endif
                        @if($fb->photos->isNotEmpty())
                            <div class="dr-ulasan-photos">
                                @foreach($fb->photos as $photo)
                                    <img src="{{ asset($photo->path) }}" alt="Foto ulasan"
                                         class="dr-ulasan-photo" onclick="openPhotoModal(this.src)">
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <div style="height:5rem"></div>

</main>

{{-- ── TOMBOL BUAT SEKARANG (fixed bottom) ── --}}
<div class="dr-bottom-bar">
    <a href="{{ route('timer.resep', $resep->id) }}" class="dr-cook-btn font-semibold">
        Buat Sekarang
        <span class="material-icons-round">arrow_forward</span>
    </a>
</div>

{{-- Unfollow confirm modal --}}
<div class="lp-confirm-overlay" id="drUnfollowOverlay" onclick="closeDrUnfollowConfirm()"></div>
<div class="lp-confirm-modal" id="drUnfollowModal">
    <div class="lp-confirm-box">
        <div class="lp-confirm-icon">👋</div>
        <p class="lp-confirm-title font-bold">Berhenti Mengikuti?</p>
        <p class="lp-confirm-sub">Kamu tidak akan melihat konten dari user ini.</p>
        <div class="lp-confirm-actions">
            <button class="lp-confirm-cancel font-semibold" onclick="closeDrUnfollowConfirm()">Batal</button>
            <button class="lp-confirm-ok font-semibold"
                    style="background:var(--orange-normal);box-shadow:0 4px 12px rgba(230,81,0,0.3)"
                    onclick="confirmDrUnfollow()">Ya, Berhenti</button>
        </div>
    </div>
</div>

{{-- Delete confirm modal --}}
<div class="dr-confirm-overlay" id="drConfirmOverlay" onclick="closeDrConfirm()"></div>
<div class="dr-confirm-modal" id="drConfirmModal">
    <div class="dr-confirm-box">
        <div class="dr-confirm-icon">🗑️</div>
        <p class="dr-confirm-title font-bold">Hapus Ulasan?</p>
        <p class="dr-confirm-sub">Ulasan yang dihapus tidak bisa dikembalikan.</p>
        <div class="dr-confirm-actions">
            <button class="dr-confirm-cancel font-semibold" onclick="closeDrConfirm()">Batal</button>
            <button class="dr-confirm-ok font-semibold"
                    onclick="document.getElementById('drDeleteForm').submit()">Ya, Hapus</button>
        </div>
    </div>
</div>
<div class="dr-photo-modal" id="photoModal" onclick="closePhotoModal()">
    <button class="dr-photo-modal-close" onclick="closePhotoModal()">
        <span class="material-icons-round">close</span>
    </button>
    <img src="" alt="Foto" class="dr-photo-modal-img" id="photoModalImg">
</div>

@push('scripts')
<script>
    const CSRF_TOKEN     = "{{ csrf_token() }}";
    const FAVORIT_URL    = "{{ route('favorit.toggle', $resep->id) }}";
    const IS_AUTH        = {{ Auth::check() ? 'true' : 'false' }};
    const SIGN_IN_URL    = "{{ route('auth.sign-in') }}";
</script>
<script src="{{ asset('js/detail-resep.js') }}"></script>
@endpush

@endsection