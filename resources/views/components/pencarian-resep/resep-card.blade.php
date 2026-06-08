{{--
    Component: x-pencarian-resep.resep-card
    Digunakan oleh:
      - API endpoint POST /api/resep/render-cards (SSR untuk JS fetch)

    Props:
      $resep → array data dari ResepResource
--}}

@props(['resep'])

@php
    $thumbnail     = $resep['thumbnail'] ?? null;
    $title         = $resep['title'] ?? '';
    $description   = $resep['description'] ?? 'Yuk, intip resep lengkap dan cara mudah membuatnya di dapur lo!';
    $cookDuration  = $resep['cook_duration'] ?? null;
    $rating        = number_format((float) ($resep['rating'] ?? 5), 1);
    $views         = $resep['views'] ?? 0;
    $authorName    = $resep['author']['name'] ?? 'User LaperPoll';
    $searchByBahan = $resep['search_by_bahan'] ?? false;
    $totalBahan    = (int) ($resep['total_bahan_count'] ?? 0);
    $matchedBahan  = (int) ($resep['matched_bahan_count'] ?? 0);
    $matchPercent  = (int) ($resep['match_percentage'] ?? 0);
    $missingBahans = $resep['missing_bahans'] ?? [];

    // Format durasi masak dari "HH:MM:SS" → "X Jam Y Menit"
    $durationLabel = '-';
    if ($cookDuration) {
        $parts        = explode(':', str_replace('.', ':', $cookDuration));
        $totalMinutes = ((int) ($parts[0] ?? 0) * 60) + (int) ($parts[1] ?? 0);
        if ($totalMinutes > 0) {
            $h = intdiv($totalMinutes, 60);
            $m = $totalMinutes % 60;
            $durationLabel = match(true) {
                $h > 0 && $m > 0 => "{$h} Jam {$m} Menit",
                $h > 0           => "{$h} Jam",
                default          => "{$m} Menit",
            };
        }
    }

    // Match status label & class
    $matchStatus = match(true) {
        $matchPercent >= 80 => ['class' => 'excellent', 'label' => 'Sangat Cocok'],
        $matchPercent >= 50 => ['class' => 'good',      'label' => 'Cocok'],
        default             => ['class' => 'low',       'label' => 'Kurang Cocok'],
    };
@endphp

<div class="resep">
    {{-- THUMBNAIL --}}
    <div class="resep-banner">
        @if ($thumbnail)
           <img src="{{ $resep->thumbnail
            ? asset($resep->thumbnail)
            : asset('assets/images/Image_DummyResep.png'); }}" alt="{{ $title }}">
        @else
            <div class="resep-banner-placeholder">
                <span class="material-icons-round">restaurant</span>
            </div>
        @endif
    </div>

    {{-- CARD BODY --}}
    <div class="resep-container-bottom">
        <div class="resep-content">
            <div class="resep-detail">

                {{-- JUDUL --}}
                <div class="resep-top-header">
                    <h1 class="resep-title">{{ $title }}</h1>
                    <span class="material-icons-round resep-arrow">chevron_right</span>
                </div>

                {{-- META INFO --}}
                <div class="resep-meta-list">
                    <div class="meta-item">
                        <span class="material-icons-round">schedule</span>
                        <p>{{ $durationLabel }}</p>
                    </div>
                    <div class="meta-item">
                        <span class="material-icons-round">star</span>
                        <p>{{ $rating }}</p>
                    </div>
                    <div class="meta-item views">
                        <span class="material-icons-round">visibility</span>
                        <p>{{ $views }}</p>
                    </div>
                </div>

                {{-- MATCH INFO atau DESKRIPSI --}}
                @if ($searchByBahan && $totalBahan > 0)
                    <x-pencarian-resep.match-info
                        :match-percent="$matchPercent"
                        :matched-bahan="$matchedBahan"
                        :total-bahan="$totalBahan"
                        :match-status="$matchStatus"
                        :missing-bahans="$missingBahans"
                    />
                @else
                    <x-pencarian-resep.description :text="$description" />
                @endif

                {{-- AUTHOR --}}
                <div class="resep-verified">
                    <span class="material-icons-round">account_circle</span>
                    <p class="user-name">{{ $authorName }}</p>
                </div>

            </div>
        </div>
    </div>
</div>