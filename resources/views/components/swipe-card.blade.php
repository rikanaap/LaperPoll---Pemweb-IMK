<div class="swipe-card" style="background: linear-gradient(135deg, {{ $attributes['color_start'] ?? '#475569' }} 0%, {{ $attributes['color_end'] ?? '#1e293b' }} 100%);">
    <div class="swipe-icon-wrapper">
        <span class="material-icons-round">{{ $icon }}</span>
    </div>
    <div class="card-body">
        <h3 class="swipe-title">{{ $title }}</h3>
        <p class="swipe-desc">{{ $desc }}</p>
    </div>
</div>