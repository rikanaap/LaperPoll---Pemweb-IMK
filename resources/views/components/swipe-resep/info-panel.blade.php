{{--
    Komponen: swipe-resep/info-panel
    Panel kiri berisi deskripsi fitur, progress, dan tips swipe.
--}}
<aside class="swipe-info-panel">
    <div class="info-card">
        <div class="info-card__header">
            <span class="material-icons-round info-card__icon">tune</span>
            <span class="badge-pill">LaperPoll</span>
        </div>

        <h2 class="info-card__title">Temukan Resep Berdasarkan Selera Rasa</h2>
        <p class="info-card__desc">
            Swipe kanan untuk menyukai rasa, swipe kiri untuk melewati.
            Pilih 3 rasa favoritmu untuk mendapatkan rekomendasi resep terbaik.
        </p>

        <div class="info-card__divider"></div>

        <x-swipe-resep.progress />

        <div class="tips-box">
            <h4 class="tips-box__title">
                <span class="material-icons-round">lightbulb</span>
                Tips
            </h4>
            <ul class="tips-box__list">
                <li>➡ Swipe kanan untuk suka</li>
                <li>⬅ Swipe kiri untuk skip</li>
            </ul>
        </div>
    </div>
</aside>