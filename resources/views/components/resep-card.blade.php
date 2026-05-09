<div class="resep">
    <!-- Banner Image Tetap di Atas -->
    <div class="resep-banner">
        @if(isset($resep->thumbnail) && $resep->thumbnail)
            <img src="{{ asset($resep->thumbnail) }}" alt="{{ $resep->title }}">
        @else
            <div class="resep-banner-placeholder">
                <span class="material-icons-round">restaurant</span>
            </div>
        @endif
    </div>

    <!-- Konten Bawah dengan Warna Lebih Hidup -->
    <div class="resep-container-bottom">
        <div class="resep-content">
            <div class="resep-detail">
                <h1 class="font-jakarta text-black font-bold resep-title">{{ $resep->title }}</h1>
                
                <div class="resep-content-detail">
                    <div class="info-item">
                        <span class="material-icons-round icon-time">schedule</span>
                        <p class="font-jakarta">{{ $resep->cook_duration }}</p>
                    </div>
                    <div class="info-item">
                        <span class="material-icons-round icon-star">star</span>
                        <p class="font-jakarta text-bold">{{ number_format($resep->current_star ?? 0, 1) }}</p>
                    </div>
                </div>

                <div class="resep-verified">
                    <p class="font-jakarta user-name">{{ $resep->user->name ?? 'User' }}</p>
                    @if(isset($resep->user_id) && in_array($resep->user_id, [1, 3]))
                    <div class="verified-badge">
                        <span class="material-icons-round">verified</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="resep-stats-right">
            <span class="material-icons-round arrow-icon">chevron_right</span>
            <div class="views-wrapper">
                <span class="material-icons-round">visibility</span>
                <span>{{ $resep->views_count ?? 0 }}</span>
            </div>
        </div>
    </div>
</div>