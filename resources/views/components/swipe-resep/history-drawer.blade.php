{{--
    Komponen: swipe-resep/history-drawer
    Drawer riwayat pilihan rasa untuk tampilan mobile.
--}}
<div id="drawerOverlay" class="drawer-overlay" aria-hidden="true"></div>

<div class="mobile-history-wrapper">
    <div id="historyDrawer" class="history-drawer" role="dialog" aria-label="Riwayat pilihan rasa">
        <div id="drawerHeader" class="history-drawer__header">
            <div class="history-drawer__handle"></div>
            <div class="history-drawer__info">
                <span class="material-icons-round">history</span>
                <span>Riwayat Pilihan Kamu</span>
                <span id="drawerArrow" class="material-icons-round history-drawer__arrow">expand_less</span>
            </div>
        </div>

        <div class="history-drawer__content">
            <div class="history-section">
                <h4 class="history-section__label">DISUKAI</h4>
                <div id="mobileLikedContainer" class="history-section__list">
                    <p class="history-section__empty">Belum ada rasa favorit</p>
                </div>
            </div>

            <div class="history-section">
                <h4 class="history-section__label">DILEWATI</h4>
                <div id="mobileDislikedContainer" class="history-section__list">
                    <p class="history-section__empty">Belum ada rasa dilewati</p>
                </div>
            </div>
        </div>
    </div>
</div>