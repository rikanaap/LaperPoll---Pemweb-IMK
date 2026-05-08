{{-- Komponen Drawer Riwayat --}}
<div id="historyDrawer" class="history-drawer">
    <div class="drawer-header" id="drawerTrigger">
        <div class="drawer-handle"></div>
        <div class="drawer-info">
            <span class="material-icons-round">history</span>
            <span>Riwayat Pilihan Kamu</span>
            <span class="material-icons-round arrow-icon">expand_less</span>
        </div>
    </div>

    <div class="drawer-content">
        <div class="history-section">
            <h4 class="section-title">Disukai (Klik rasa untuk lanjut)</h4>
            <div id="likedHistoryList" class="history-flex">
                <p class="empty-history">Belum ada rasa disukai</p>
            </div>
        </div>

        <div class="history-section">
            <h4 class="section-title">Dilewati (Skip)</h4>
            <div id="dislikedHistoryList" class="history-flex">
                <p class="empty-history">Belum ada rasa dilewati</p>
            </div>
        </div>
    </div>
</div>