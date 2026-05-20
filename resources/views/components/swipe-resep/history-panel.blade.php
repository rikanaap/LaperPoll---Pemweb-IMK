{{--
    Komponen: swipe-resep/history-panel
    Panel riwayat pilihan rasa untuk tampilan desktop.
--}}
<aside class="desktop-history-panel">
    <div class="history-panel-card">
        <div class="history-panel-card__header">
            <span class="material-icons-round">history</span>
            <h3>Riwayat Pilihan</h3>
        </div>

        <div class="history-section">
            <h4 class="history-section__label">DISUKAI</h4>
            <div id="likedContainer" class="history-section__list">
                <p class="history-section__empty">Belum ada rasa favorit</p>
            </div>
        </div>

        <div class="history-section">
            <h4 class="history-section__label">DILEWATI</h4>
            <div id="dislikedContainer" class="history-section__list">
                <p class="history-section__empty">Belum ada rasa dilewati</p>
            </div>
        </div>
    </div>
</aside>