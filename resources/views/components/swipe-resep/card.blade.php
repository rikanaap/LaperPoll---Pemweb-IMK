{{--
    Komponen: swipe-resep/resep-card
    Card hasil rekomendasi resep setelah swipe filter.
    Dirender via JavaScript (template string), komponen ini hanya referensi markup.
    Anda bisa gunakan komponen ini jika merender server-side (SSR).

    Props (jika SSR):
        $resep          - object : Data resep
        $totalSelection - int    : Total filter yang dipilih user
--}}
@props(['resep', 'totalSelection' => 3])

@php
    $imageUrl = $resep->thumbnail
        ? Storage::url($resep->thumbnail)
        : asset('images/default-food.jpg');

    $duration = $resep->cook_duration;
    if ($duration && str_contains($duration, ':')) {
        [$h, $m] = explode(':', $duration);
        $duration = (int)$h > 0 ? "{$h} jam {$m} menit" : "{$m} menit";
    } elseif ($duration) {
        $duration = "{$duration} menit";
    } else {
        $duration = '-';
    }
@endphp

<div class="resep-card">
    <div class="resep-card__thumbnail">
        <img
            src="{{ $imageUrl }}"
            alt="{{ $resep->title }}"
            loading="lazy"
        >
        <div class="resep-card__badge">
            🔥 Cocok {{ $resep->match_count ?? 0 }}/{{ $totalSelection }} Rasa
        </div>
    </div>

    <div class="resep-card__body">
        <h3 class="resep-card__title">{{ $resep->title ?? 'Tanpa Judul' }}</h3>

        <div class="resep-card__meta">
            <div class="meta-pill">
                <span class="material-icons-round">schedule</span>
                <span>{{ $duration }}</span>
            </div>
            <div class="meta-pill meta-pill--star">
                <span class="material-icons-round">star</span>
                <span>{{ $resep->current_star ?? 0 }}</span>
            </div>
            <div class="meta-pill meta-pill--orange">
                <span class="material-icons-round">visibility</span>
                <span>{{ $resep->views_count ?? 0 }}</span>
            </div>
        </div>

        @if($resep->filters && $resep->filters->count())
            <div class="resep-card__rasa">
                <span class="resep-card__rasa-label">Rasa pada resep ini</span>
                <div class="resep-card__rasa-list">
                    @foreach($resep->filters as $filter)
                        <span class="resep-rasa-chip">❤️ {{ $filter->title }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="resep-card__author">
            <span class="material-icons-round">person</span>
            <span>{{ $resep->user?->name ?? 'Unknown' }}</span>
        </div>
    </div>
</div>