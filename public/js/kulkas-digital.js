// kulkas-digital.js — v2.3 (Multi-layout: Mobile + Tablet + Desktop)
// Fixes: sidebar filter data-filter vs data-kategori, resep terkait tablet,
//        expired banner tablet/desktop, expired indicator per-pembelian,
//        matchKategori removed, updateSidebarCounts no-op.
'use strict';

document.addEventListener('DOMContentLoaded', () => {

    // ── BREAKPOINT DETECTION ──────────────────────────────────────────
    const isTablet  = () => window.innerWidth >= 768 && window.innerWidth < 1024;
    const isDesktop = () => window.innerWidth >= 1024;
    const isMobile  = () => window.innerWidth < 768;

    // ── GLOBAL CONSTANTS GUARD ────────────────────────────────────────
    const CSRF      = (typeof CSRF_TOKEN      !== 'undefined') ? CSRF_TOKEN      : '';
    const PAKAI_URL = (typeof PAKAI_RESEP_URL !== 'undefined') ? PAKAI_RESEP_URL : '';

    // ── TOAST ─────────────────────────────────────────────────────────
    function showToast(msg, type = 'success') {
        let toast = document.getElementById('kdToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'kdToast';
            document.body.appendChild(toast);
        }
        const icon = type === 'success' ? 'check_circle' : 'error_outline';
        toast.className = `kd-toast kd-toast-${type}`;
        toast.innerHTML = `<span class="material-icons-round">${icon}</span><span>${msg}</span>`;
        toast.classList.add('show');
        clearTimeout(toast._timer);
        toast._timer = setTimeout(() => toast.classList.remove('show'), 3000);
    }

    // ── LAYOUT-AWARE CARD ACCESSOR ────────────────────────────────────
    function getAllDataCards() {
        if (isMobile()) {
            return document.querySelectorAll('#kdList .kd-card');
        }
        return document.querySelectorAll('#kdGridView .kd-grid-card');
    }

    // ── CHIP COUNTER ──────────────────────────────────────────────────
    function updateChipCounts(query = '') {
        const allCards = getAllDataCards();
        const counts   = { semua: 0, tersedia: 0, 'hampir-habis': 0, expired: 0 };
        allCards.forEach(card => {
            const status = card.dataset.status;
            const nama   = (card.dataset.nama || '').toLowerCase();
            if (query && !nama.includes(query)) return;
            counts['semua']++;
            if (counts[status] !== undefined) counts[status]++;
        });
        document.querySelectorAll('.kd-chip').forEach(chip => {
            const filter = chip.dataset.filter;
            let countEl  = chip.querySelector('.kd-chip-count');
            if (!countEl) {
                countEl = document.createElement('span');
                countEl.className = 'kd-chip-count';
                chip.appendChild(countEl);
            }
            countEl.textContent = counts[filter] ?? 0;
        });
    }

    // ── FILTER STATE ──────────────────────────────────────────────────
    let activeFilter = 'semua';
    let activeSearch = '';

    // Mobile chips
    document.querySelectorAll('.kd-chip').forEach(chip => {
        chip.addEventListener('click', () => {
            const newFilter = chip.dataset.filter;
            document.querySelectorAll('.kd-chip').forEach(c => c.classList.remove('active'));
            document.querySelectorAll(`.kd-chip[data-filter="${newFilter}"]`).forEach(c => c.classList.add('active'));
            // Sync sidebar
            document.querySelectorAll('.kd-sidebar-item').forEach(i => i.classList.remove('active'));
            const matchSidebar = document.querySelector(`.kd-sidebar-item[data-filter="${newFilter}"]`);
            if (matchSidebar) matchSidebar.classList.add('active');
            activeFilter = newFilter;
            applyFilter();
        });
    });

    // FIX: Sidebar menggunakan data-filter (bukan data-kategori)
    document.querySelectorAll('.kd-sidebar-item[data-filter]').forEach(item => {
        item.addEventListener('click', () => {
            document.querySelectorAll('.kd-sidebar-item').forEach(i => i.classList.remove('active'));
            item.classList.add('active');
            activeFilter = item.dataset.filter;
            // Sync chip mobile
            document.querySelectorAll('.kd-chip').forEach(c => c.classList.remove('active'));
            document.querySelectorAll(`.kd-chip[data-filter="${item.dataset.filter}"]`).forEach(c => c.classList.add('active'));
            // Update topbar title
            const titleEl = document.querySelector('.kd-topbar-title');
            if (titleEl) {
                const labelMap = {
                    'semua':       'Semua Bahan',
                    'tersedia':    'Tersedia',
                    'hampir-habis':'Hampir Habis',
                    'expired':     'Expired',
                };
                titleEl.textContent = labelMap[item.dataset.filter] || 'Semua Bahan';
            }
            applyFilter();
        });
    });

    // Search sync
    let _syncingSearch = false;
    document.querySelectorAll('#kdSearch, #kdSearchTop, #kdSearchDesktop').forEach(inp => {
        inp.addEventListener('input', e => {
            if (_syncingSearch) return;
            _syncingSearch = true;
            activeSearch = e.target.value.trim().toLowerCase();
            document.querySelectorAll('#kdSearch, #kdSearchTop, #kdSearchDesktop').forEach(other => {
                if (other !== e.target) other.value = e.target.value;
            });
            _syncingSearch = false;
            applyFilter();
        });
    });

    function applyFilter() {
        const q = activeSearch;

        // ── MOBILE: list items
        document.querySelectorAll('#kdList .kd-swipe-wrapper').forEach(wrapper => {
            const card = wrapper.querySelector('.kd-card');
            if (!card) return;
            const matchFilter = activeFilter === 'semua' || card.dataset.status === activeFilter;
            const matchSearch = !q || (card.dataset.nama || '').toLowerCase().includes(q);
            wrapper.style.display = (matchFilter && matchSearch) ? '' : 'none';
        });

        // ── TABLET/DESKTOP: grid cards
        // FIX: hapus matchKategori — filter status sudah ditangani activeFilter
        document.querySelectorAll('#kdGridView .kd-grid-card').forEach(card => {
            const matchFilter = activeFilter === 'semua' || card.dataset.status === activeFilter;
            const matchSearch = !q || (card.dataset.nama || '').toLowerCase().includes(q);
            card.style.display = (matchFilter && matchSearch) ? '' : 'none';
        });

        updateChipCounts(q);

        // Expired banner mobile — hide when filter = 'expired'
        const banner = document.getElementById('kdExpiredBanner');
        if (banner) banner.style.display = activeFilter === 'expired' ? 'none' : '';
        // Expired banner desktop
        const bannerDesktop = document.getElementById('kdExpiredBannerDesktop');
        if (bannerDesktop) bannerDesktop.style.display = activeFilter === 'expired' ? 'none' : '';

        handleNoResult();
    }

    function handleNoResult() {
        const q = activeSearch;
        const hasActiveFilter = activeFilter !== 'semua' || q !== '';

        // ── MOBILE empty state
        const mobileList = document.getElementById('kdList');
        if (mobileList && isMobile()) {
            const visible = [...mobileList.querySelectorAll('.kd-swipe-wrapper')]
                .filter(w => w.style.display !== 'none').length;
            let noResultMobile = document.getElementById('kdNoResultMobile');
            const shouldShow = visible === 0 && hasActiveFilter;
            if (shouldShow) {
                if (!noResultMobile) {
                    noResultMobile = document.createElement('div');
                    noResultMobile.id = 'kdNoResultMobile';
                    noResultMobile.className = 'kd-empty';
                    noResultMobile.innerHTML = `
                        <span class="material-icons-round kd-empty-icon">search_off</span>
                        <p class="font-jakarta font-semibold kd-empty-title">Tidak ditemukan</p>
                        <p class="font-jakarta font-regular kd-empty-sub">Coba kata kunci atau filter lain</p>
                    `;
                    mobileList.appendChild(noResultMobile);
                }
                noResultMobile.style.display = '';
            } else {
                if (noResultMobile) noResultMobile.style.display = 'none';
            }
        }

        // ── TABLET/DESKTOP empty state
        const gridView = document.getElementById('kdGridView');
        if (gridView) {
            const visible = [...gridView.querySelectorAll('.kd-grid-card')]
                .filter(c => c.style.display !== 'none').length;
            let noResult  = document.getElementById('kdNoResult');
            const shouldShow = visible === 0 && hasActiveFilter;
            if (shouldShow) {
                if (!noResult) {
                    noResult = document.createElement('div');
                    noResult.id = 'kdNoResult';
                    noResult.className = 'kd-empty';
                    noResult.innerHTML = `
                        <span class="material-icons-round kd-empty-icon">search_off</span>
                        <p class="font-jakarta font-semibold kd-empty-title">Tidak ditemukan</p>
                        <p class="font-jakarta font-regular kd-empty-sub">Coba kata kunci atau filter lain</p>
                    `;
                    gridView.appendChild(noResult);
                }
                noResult.style.display = '';
            } else {
                if (noResult) noResult.style.display = 'none';
            }
        }
    }

    // ── SIDEBAR COUNTS — server-side rendered, no-op ──────────────────
    // FIX: counts sudah di-render Blade (server-side), tidak perlu update JS
    function updateSidebarCounts() {}

    // ── RESIZE: reset filter when switching layouts ───────────────────
    let _lastBreakpoint = isMobile() ? 'mobile' : (isTablet() ? 'tablet' : 'desktop');
    window.addEventListener('resize', () => {
        const current = isMobile() ? 'mobile' : (isTablet() ? 'tablet' : 'desktop');
        if (current !== _lastBreakpoint) {
            _lastBreakpoint = current;
            applyFilter();
            updateChipCounts(activeSearch);
        }
    });

    // ═══════════════════════════════════════════════════════════════════
    //  MOBILE — SWIPE GESTURES
    // ═══════════════════════════════════════════════════════════════════
    const SWIPE_THRESHOLD = 72;
    const SWIPE_MAX_X     = 100;

    function initSwipeCard(wrapper) {
        const card = wrapper.querySelector('.kd-card');
        if (!card) return;

        let startX = 0, startY = 0, currentX = 0;
        let isSwiping = false, direction = null;

        card.addEventListener('touchstart', e => {
            startX    = e.touches[0].clientX;
            startY    = e.touches[0].clientY;
            currentX  = 0;
            isSwiping = true;
            direction = null;
            card.style.transition = 'none';
        }, { passive: true });

        card.addEventListener('touchmove', e => {
            if (!isSwiping) return;
            const dx = e.touches[0].clientX - startX;
            const dy = e.touches[0].clientY - startY;

            if (!direction) {
                if (Math.abs(dx) > Math.abs(dy) + 5) direction = 'h';
                else if (Math.abs(dy) > Math.abs(dx) + 5) { direction = 'v'; return; }
                else return;
            }
            if (direction === 'v') return;

            e.preventDefault();
            currentX = Math.max(-SWIPE_MAX_X, Math.min(SWIPE_MAX_X, dx));
            card.style.transform = `translateX(${currentX}px)`;

            wrapper.classList.remove('swiping-left', 'swiping-right');
            if (currentX < -20) wrapper.classList.add('swiping-left');
            else if (currentX > 20) wrapper.classList.add('swiping-right');
        }, { passive: false });

        // FIX: track apakah touchend sudah handle aksi, supaya click tidak dobel
        let _touchHandled = false;

        card.addEventListener('touchend', () => {
            if (!isSwiping) return;

            // direction null = tap cepat tanpa gerak horizontal — biarkan click handler yang handle
            if (direction === null) {
                isSwiping = false;
                card.style.transform = '';
                wrapper.classList.remove('swiping-left', 'swiping-right');
                // Tidak panggil openMobileDetail di sini — click event akan fire
                return;
            }

            if (direction !== 'h') {
                // Vertikal scroll — reset saja, jangan buka detail
                isSwiping = false;
                direction = null;
                card.style.transform = '';
                wrapper.classList.remove('swiping-left', 'swiping-right');
                return;
            }

            isSwiping = false;
            direction = null;
            card.style.transition = 'transform 0.3s cubic-bezier(0.32, 0.72, 0, 1)';
            wrapper.classList.remove('swiping-left', 'swiping-right');

            if (currentX < -SWIPE_THRESHOLD) {
                _touchHandled = true;
                card.style.transform = `translateX(-100px)`;
                setTimeout(() => {
                    card.style.transform = '';
                    _touchHandled = false;
                    const bahanId   = card.dataset.bahanId;
                    const bahanNama = card.dataset.nama || 'bahan ini';
                    openHapusModal(bahanId, bahanNama);
                }, 200);
            } else if (currentX > SWIPE_THRESHOLD) {
                _touchHandled = true;
                card.style.transform = '';
                openMobileDetail(card);
                setTimeout(() => { _touchHandled = false; }, 300);
            } else {
                card.style.transform = '';
            }
            currentX = 0;
        });

        card.addEventListener('click', e => {
            if (e.target.closest('.kd-card-actions-inline')) return;
            if (_touchHandled) return; // FIX: skip jika sudah ditangani swipe
            openMobileDetail(card);
        });
    }

    document.querySelectorAll('#kdList .kd-swipe-wrapper').forEach(initSwipeCard);

    // ═══════════════════════════════════════════════════════════════════
    //  MOBILE — BOTTOM SHEET
    // ═══════════════════════════════════════════════════════════════════
    const sheet        = document.getElementById('kdSheet');
    const sheetOverlay = document.getElementById('kdSheetOverlay');

    function openMobileDetail(card) {
        if (!sheet) return;
        const bahanId = card.dataset.bahanId;
        const data    = getCardData(bahanId);
        if (!data) return;

        renderSheetContent(data);
        sheet.classList.add('open');
        if (sheetOverlay) sheetOverlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeSheet() {
        if (sheet) {
            sheet.classList.remove('open');
            sheet.style.transform = '';
        }
        if (sheetOverlay) sheetOverlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    sheetOverlay?.addEventListener('click', closeSheet);
    document.getElementById('kdSheetClose')?.addEventListener('click', closeSheet);

    if (sheet) {
        let sheetStart = 0, sheetDy = 0;

        sheet.addEventListener('touchstart', e => {
            if (!e.target.closest('.kd-sheet-handle') && !e.target.closest('.kd-sheet-header')) {
                sheetStart = 0;
                return;
            }
            sheetStart = e.touches[0].clientY;
            sheetDy    = 0;
            sheet.style.transition = 'none';
        }, { passive: true });

        sheet.addEventListener('touchmove', e => {
            if (sheetStart === 0) return;
            sheetDy = Math.max(0, e.touches[0].clientY - sheetStart);
            sheet.style.transform = `translateY(${sheetDy}px)`;
        }, { passive: true });

        sheet.addEventListener('touchend', () => {
            if (sheetStart === 0) return;
            sheet.style.transition = '';
            if (sheetDy > 120) {
                closeSheet();
            } else {
                sheet.style.transform = '';
            }
            sheetStart = 0;
            sheetDy    = 0;
        });
    }

    function renderSheetContent(data) {
        const sheetNama  = document.getElementById('kdSheetNama');
        const sheetBadge = document.getElementById('kdSheetBadge');
        const sheetStok  = document.getElementById('kdSheetStok');
        const sheetList  = document.getElementById('kdSheetBeliList');
        if (!sheetNama) return;

        sheetNama.textContent = data.nama;
        if (sheetStok) {
            sheetStok.querySelector('.kd-sheet-stok-val').textContent = data.stok_gram + ' gram';
        }

        if (sheetBadge) {
            const { cls, label } = badgeInfo(data.status);
            sheetBadge.className = `kd-badge ${cls} font-jakarta font-medium`;
            sheetBadge.textContent = label;
        }

        if (sheetList) {
            sheetList.innerHTML = '';
            data.pembelian.forEach((beli, i) => {
                // FIX: tandai pembelian yang sudah expired
                const isExpired = beli.sisa_hari !== null && beli.sisa_hari <= 0;
                const item = document.createElement('div');
                item.className = `kd-sheet-beli-item${isExpired ? ' beli-item-expired' : ''}`;
                item.innerHTML = `
                    ${isExpired ? `<div class="kd-beli-expired-badge"><span class="material-icons-round">warning_amber</span> Pembelian ini sudah expired</div>` : ''}
                    <div class="kd-sheet-beli-info">
                        <div class="kd-sheet-beli-label">Pembelian ${i + 1}</div>
                        <div class="kd-sheet-beli-detail">
                            <span class="kd-pill">
                                <span class="material-icons-round kd-pill-icon">scale</span>
                                ${beli.jumlah} gram
                            </span>
                            ${beli.bought_date ? `<span class="kd-pill"><span class="material-icons-round kd-pill-icon">shopping_bag</span>${beli.bought_date}</span>` : ''}
                            ${beli.expired_date ? `<span class="kd-pill ${beli.sisa_hari !== null && beli.sisa_hari <= 3 ? 'kd-pill-warn' : ''}"><span class="material-icons-round kd-pill-icon">event_busy</span>${beli.expired_date}</span>` : ''}
                            ${beli.sisa_hari !== null ? `<span class="kd-pill ${beli.sisa_hari <= 0 ? 'kd-pill-exp' : beli.sisa_hari <= 3 ? 'kd-pill-warn' : ''}"><span class="material-icons-round kd-pill-icon">hourglass_bottom</span>${beli.sisa_hari > 0 ? beli.sisa_hari + ' hari' : 'Expired'}</span>` : ''}
                        </div>
                    </div>
                    <button class="kd-sheet-delete-btn" data-pembelian-id="${beli.id}">
                        <span class="material-icons-round">delete_outline</span>
                    </button>
                `;
                sheetList.appendChild(item);
            });

            sheetList.querySelectorAll('.kd-sheet-delete-btn').forEach(btn => {
                btn.addEventListener('click', e => {
                    e.stopPropagation();
                    confirmDeletePembelian(btn.dataset.pembelianId);
                });
            });
        }
    }

    // ═══════════════════════════════════════════════════════════════════
    //  TABLET / DESKTOP — GRID CARD CLICK → DETAIL PANEL
    // ═══════════════════════════════════════════════════════════════════
    const detailPanel = document.getElementById('kdDetailPanel');

    document.getElementById('kdGridView')?.addEventListener('click', e => {
        const card = e.target.closest('.kd-grid-card');
        if (!card) return;
        if (e.target.closest('.kd-detail-delete-btn') || e.target.closest('.kd-grid-card-actions')) return;

        const bahanId = card.dataset.bahanId;
        const data    = getCardData(bahanId);
        if (!data) return;

        document.querySelectorAll('.kd-grid-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');

        openDetailPanel(data);
    });

    function openDetailPanel(data) {
        if (!detailPanel) return;
        renderDetailPanel(data);
        if (isTablet()) detailPanel.classList.add('open');
    }

    function closeDetailPanel() {
        if (!detailPanel) return;
        if (isTablet()) detailPanel.classList.remove('open');
        if (isDesktop()) {
            const content     = detailPanel.querySelector('.kd-detail-content');
            const placeholder = detailPanel.querySelector('.kd-detail-placeholder');
            if (content)     content.style.display     = 'none';
            if (placeholder) placeholder.style.display = '';
        }
        document.querySelectorAll('.kd-grid-card').forEach(c => c.classList.remove('selected'));
    }

    // ── TABLET SCRIM OVERLAY ──────────────────────────────────────────
    (function initTabletScrim() {
        const scrim = document.createElement('div');
        scrim.id    = 'kdTabletScrim';
        Object.assign(scrim.style, {
            display:              'none',
            position:             'fixed',
            inset:                '0',
            zIndex:               '199',
            background:           'rgba(0,0,0,0.35)',
            backdropFilter:       'blur(1px)',
            WebkitBackdropFilter: 'blur(1px)',
            opacity:              '0',
            transition:           'opacity 0.3s ease',
            cursor:               'pointer',
        });
        scrim.addEventListener('click', () => closeDetailPanel());
        document.body.appendChild(scrim);

        if (!detailPanel) return;

        const observer = new MutationObserver(() => {
            const isTabletNow = window.innerWidth >= 768 && window.innerWidth < 1024;
            if (isTabletNow && detailPanel.classList.contains('open')) {
                scrim.style.display = 'block';
                scrim.getBoundingClientRect();
                scrim.style.opacity = '1';
            } else {
                scrim.style.opacity = '0';
                setTimeout(() => {
                    if (!detailPanel.classList.contains('open')) {
                        scrim.style.display = 'none';
                    }
                }, 300);
            }
        });

        observer.observe(detailPanel, { attributes: true, attributeFilter: ['class'] });
    })();

    function renderDetailPanel(data) {
        const panel = detailPanel;
        if (!panel) return;

        const placeholder = panel.querySelector('.kd-detail-placeholder');
        let content       = panel.querySelector('.kd-detail-content');

        if (placeholder) placeholder.style.display = 'none';

        if (!content) {
            content = document.createElement('div');
            content.className = 'kd-detail-content';
            panel.appendChild(content);
        }
        content.style.display = '';

        const { cls, label, icon } = badgeInfo(data.status);

        const belanHtml = data.pembelian.map((beli, i) => {
            // FIX: tandai pembelian yang sudah expired di detail panel
            const isExpired = beli.sisa_hari !== null && beli.sisa_hari <= 0;
            return `
            <div class="kd-detail-beli-item${isExpired ? ' beli-item-expired' : ''}">
                <div>
                    ${isExpired ? `<div class="kd-beli-expired-badge"><span class="material-icons-round">warning_amber</span> Pembelian ini sudah expired</div>` : ''}
                    <div class="kd-detail-beli-label">Pembelian ${i + 1}</div>
                    <div class="kd-detail-beli-pills">
                        <span class="kd-pill"><span class="material-icons-round kd-pill-icon">scale</span>${beli.jumlah} gram</span>
                        ${beli.bought_date ? `<span class="kd-pill"><span class="material-icons-round kd-pill-icon">shopping_bag</span>${beli.bought_date}</span>` : ''}
                        ${beli.expired_date ? `<span class="kd-pill ${beli.sisa_hari !== null && beli.sisa_hari <= 3 ? 'kd-pill-warn' : ''}"><span class="material-icons-round kd-pill-icon">event_busy</span>${beli.expired_date}</span>` : ''}
                        ${beli.sisa_hari !== null ? `<span class="kd-pill ${beli.sisa_hari <= 0 ? 'kd-pill-exp' : beli.sisa_hari <= 3 ? 'kd-pill-warn' : ''}"><span class="material-icons-round kd-pill-icon">hourglass_bottom</span>${beli.sisa_hari > 0 ? beli.sisa_hari + ' hari' : 'Expired'}</span>` : ''}
                    </div>
                </div>
                <button class="kd-detail-delete-btn" data-pembelian-id="${beli.id}">
                    <span class="material-icons-round">delete_outline</span>
                </button>
            </div>
        `}).join('<hr class="kd-detail-divider">');

        // FIX: resep terkait sekarang muncul di tablet DAN desktop
        const resepSection = (isDesktop() || isTablet()) ? buildResepSection(data) : '';

        content.innerHTML = `
            <div class="kd-detail-top">
                <div class="kd-detail-top-row">
                    <div>
                        <div class="kd-detail-nama">${data.nama}</div>
                        <span class="kd-badge ${cls} font-jakarta font-medium" style="margin-top:0.4rem; display:inline-flex;">
                            <span class="material-icons-round kd-badge-icon">${icon}</span>
                            ${label}
                        </span>
                    </div>
                    <button class="kd-detail-panel-close" id="kdDetailClose">
                        <span class="material-icons-round">close</span>
                    </button>
                </div>
                <div class="kd-detail-stok-block">
                    <span class="material-icons-round kd-detail-stok-icon">kitchen</span>
                    <div>
                        <div class="kd-detail-stok-val">${data.stok_gram}</div>
                        <div class="kd-detail-stok-label">gram tersedia</div>
                    </div>
                </div>
            </div>
            <div class="kd-detail-body">
                <div>
                    <div class="kd-detail-section-title">Riwayat Pembelian</div>
                    <div class="kd-detail-beli-list">${belanHtml}</div>
                </div>
                ${resepSection}
            </div>
        `;

        content.querySelector('#kdDetailClose')?.addEventListener('click', closeDetailPanel);

        content.querySelectorAll('.kd-detail-delete-btn').forEach(btn => {
            btn.addEventListener('click', e => {
                e.stopPropagation();
                confirmDeletePembelian(btn.dataset.pembelianId);
            });
        });

        content.querySelectorAll('.kd-detail-resep-item').forEach(item => {
            item.addEventListener('click', () => {
                const lengkap   = item.dataset.lengkap === '1';
                const nama      = item.dataset.resepNama;
                let bahanDetail = [];
                try { bahanDetail = JSON.parse(item.dataset.bahanDetail || '[]'); } catch(e) {}
                if (lengkap) openModalMasak(item, bahanDetail);
                else openModalKurang(nama, bahanDetail);
            });
        });
    }

    function buildResepSection(data) {
        const rekomendasi = window.__kdRekomendasi || [];
        const related     = rekomendasi.filter(r =>
            r.bahan_ids && r.bahan_ids.includes(parseInt(data.bahan_id))
        ).slice(0, 4);

        if (!related.length) return '';

        const itemsHtml = related.map(r => `
            <div class="kd-detail-resep-item kd-resep-item"
                 data-resep-id="${r.id}"
                 data-resep-nama="${r.title}"
                 data-bahan-ids="${r.bahan_ids.join(',')}"
                 data-bahan-detail='${JSON.stringify(r.bahan_detail)}'
                 data-lengkap="${r.lengkap ? '1' : '0'}">
                <div class="kd-resep-thumb">
                    ${r.thumbnail
                        ? `<img src="${r.thumbnail}" alt="${r.title}" loading="lazy">`
                        : `<span class="material-icons-round">restaurant</span>`}
                </div>
                <div class="kd-resep-info" style="flex:1; min-width:0;">
                    <div class="kd-resep-nama">${r.title}</div>
                </div>
                <span class="kd-resep-badge ${r.lengkap ? 'badge-resep-lengkap' : 'badge-resep-partial'} font-jakarta font-bold">
                    ${r.bahan_ada}/${r.total_bahan}${r.lengkap ? ' ✓' : ''}
                </span>
            </div>
        `).join('');

        return `
            <div class="kd-detail-resep-section">
                <div class="kd-detail-section-title">✨ Resep terkait</div>
                <div class="kd-detail-resep-list">${itemsHtml}</div>
            </div>
        `;
    }

    // ═══════════════════════════════════════════════════════════════════
    //  DATA ACCESS
    // ═══════════════════════════════════════════════════════════════════
    function getCardData(bahanId) {
        let card = document.querySelector(`#kdList .kd-card[data-bahan-id="${bahanId}"]`);
        if (!card) card = document.querySelector(`#kdGridView .kd-grid-card[data-bahan-id="${bahanId}"]`);
        if (!card) return null;

        let pembelian = [];
        try { pembelian = JSON.parse(card.dataset.pembelian || '[]'); } catch(e) {}

        return {
            bahan_id:  bahanId,
            nama:      card.dataset.nama      || '',
            status:    card.dataset.status    || 'tersedia',
            stok_gram: card.dataset.stokGram  || '0',
            pembelian: pembelian,
        };
    }

    function badgeInfo(status) {
        const map = {
            'tersedia':     { cls: 'badge-ok',   label: 'Tersedia',     icon: 'check_circle' },
            'hampir-habis': { cls: 'badge-warn',  label: 'Hampir Habis', icon: 'schedule'     },
            'expired':      { cls: 'badge-exp',   label: 'Expired',      icon: 'cancel'       },
        };
        return map[status] || map['tersedia'];
    }

    // ═══════════════════════════════════════════════════════════════════
    //  HAPUS / DELETE
    // ═══════════════════════════════════════════════════════════════════
    let hapusPembelianId = null;

    const modalHapus        = document.getElementById('modalHapus');
    const modalHapusOverlay = document.getElementById('modalHapusOverlay');
    const modalHapusCancel  = document.getElementById('modalHapusCancel');
    const modalHapusConfirm = document.getElementById('modalHapusConfirm');

    function confirmDeletePembelian(pembelianId) {
        hapusPembelianId = pembelianId;
        if (modalHapus) modalHapus.style.display = 'flex';
    }

    function openHapusModal(bahanId, bahanNama) {
        const data = getCardData(bahanId);
        if (!data || data.pembelian.length === 0) return;

        if (data.pembelian.length === 1) {
            confirmDeletePembelian(data.pembelian[0].id);
        } else {
            openMobileDetail(
                document.querySelector(`#kdList .kd-card[data-bahan-id="${bahanId}"]`)
                || document.querySelector(`#kdGridView .kd-grid-card[data-bahan-id="${bahanId}"]`)
            );
        }
    }

    function closeHapusModal() {
        if (modalHapus) modalHapus.style.display = 'none';
        hapusPembelianId = null;
    }

    modalHapusCancel?.addEventListener('click', closeHapusModal);
    modalHapusOverlay?.addEventListener('click', closeHapusModal);

    modalHapusConfirm?.addEventListener('click', () => {
        if (!hapusPembelianId) return;
        submitDelete(hapusPembelianId);
        closeHapusModal();
    });

    function submitDelete(pembelianId) {
        const preForm = document.getElementById(`form-del-${pembelianId}`)
                     || document.querySelector(`form[data-pembelian-id="${pembelianId}"]`);
        if (preForm) { preForm.submit(); return; }

        if (!CSRF) {
            showToast('CSRF token tidak ditemukan.', 'error');
            return;
        }
        const f = document.createElement('form');
        f.method = 'POST';
        f.action = `/kulkas/${pembelianId}`;
        f.innerHTML = `
            <input type="hidden" name="_token"  value="${CSRF}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        document.body.appendChild(f);
        f.submit();
    }

    // ═══════════════════════════════════════════════════════════════════
    //  MODAL BAHAN KURANG
    // ═══════════════════════════════════════════════════════════════════
    const modalKurang        = document.getElementById('modalBahanKurang');
    const modalKurangTitle   = document.getElementById('modalKurangTitle');
    const modalKurangList    = document.getElementById('modalKurangList');
    const modalKurangClose   = document.getElementById('modalKurangClose');
    const modalKurangOverlay = document.getElementById('modalKurangOverlay');

    function openModalKurang(nama, bahanDetail) {
        if (!modalKurang) return;
        if (modalKurangTitle) modalKurangTitle.textContent = nama;
        if (modalKurangList)  renderBahanDetailList(bahanDetail, modalKurangList);
        modalKurang.style.display = 'flex';
    }
    function closeModalKurang() { if (modalKurang) modalKurang.style.display = 'none'; }
    modalKurangClose?.addEventListener('click', closeModalKurang);
    modalKurangOverlay?.addEventListener('click', closeModalKurang);

    // ═══════════════════════════════════════════════════════════════════
    //  MODAL MASAK
    // ═══════════════════════════════════════════════════════════════════
    const modalMasak        = document.getElementById('modalMasak');
    const modalMasakTitle   = document.getElementById('modalMasakTitle');
    const modalMasakList    = document.getElementById('modalMasakBahanList');
    const modalMasakCancel  = document.getElementById('modalMasakCancel');
    const modalMasakConfirm = document.getElementById('modalMasakConfirm');
    const modalMasakOverlay = document.getElementById('modalMasakOverlay');
    const modalMasakLoading = document.getElementById('modalMasakLoading');

    let currentBahanIds = [];
    let currentGramMap  = {};
    let currentResepId  = null;

    function openModalMasak(resepItem, bahanDetail) {
        if (!modalMasak) return;
        currentResepId  = resepItem.dataset.resepId;
        currentBahanIds = (resepItem.dataset.bahanIds || '')
            .split(',').map(s => parseInt(s.trim())).filter(Boolean);
        currentGramMap  = {};
        bahanDetail.forEach(b => { currentGramMap[b.id] = b.butuh; });

        if (modalMasakTitle) modalMasakTitle.textContent = resepItem.dataset.resepNama;
        if (modalMasakList)  renderBahanDetailList(bahanDetail, modalMasakList);

        const detailLink = document.getElementById('modalMasakDetailLink');
        if (detailLink && currentResepId) detailLink.href = `/detail-resep/${currentResepId}`;

        if (modalMasakLoading) modalMasakLoading.style.display = 'none';
        if (modalMasakConfirm) modalMasakConfirm.disabled = false;
        modalMasak.style.display = 'flex';
    }
    function closeModalMasak() { if (modalMasak) modalMasak.style.display = 'none'; }
    modalMasakCancel?.addEventListener('click', closeModalMasak);
    modalMasakOverlay?.addEventListener('click', closeModalMasak);

    modalMasakConfirm?.addEventListener('click', async () => {
        if (!currentResepId || !currentBahanIds.length) return;

        if (!PAKAI_URL) {
            showToast('URL tidak dikonfigurasi.', 'error');
            return;
        }
        if (!CSRF) {
            showToast('CSRF token tidak ditemukan.', 'error');
            return;
        }

        if (modalMasakConfirm) modalMasakConfirm.disabled = true;
        if (modalMasakLoading) modalMasakLoading.style.display = 'block';

        try {
            const res = await fetch(PAKAI_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    resep_id:     parseInt(currentResepId),
                    bahan_ids:    currentBahanIds,
                    gram_dipakai: currentGramMap,
                }),
            });
            const data = await res.json();
            if (data.success && data.redirect) {
                window.location.href = data.redirect;
            } else {
                showToast(data.message || 'Terjadi kesalahan, coba lagi.', 'error');
                if (modalMasakConfirm) modalMasakConfirm.disabled = false;
                if (modalMasakLoading) modalMasakLoading.style.display = 'none';
            }
        } catch (err) {
            console.error(err);
            showToast('Gagal menghubungi server.', 'error');
            if (modalMasakConfirm) modalMasakConfirm.disabled = false;
            if (modalMasakLoading) modalMasakLoading.style.display = 'none';
        }
    });

    // ═══════════════════════════════════════════════════════════════════
    //  RESEP ITEM CLICK (mobile rekomendasi list)
    // ═══════════════════════════════════════════════════════════════════
    document.querySelectorAll('#kdResepList .kd-resep-item').forEach(item => {
        item.addEventListener('click', () => {
            const lengkap   = item.dataset.lengkap === '1';
            const nama      = item.dataset.resepNama;
            let bahanDetail = [];
            try { bahanDetail = JSON.parse(item.dataset.bahanDetail || '[]'); } catch(e) {}
            if (lengkap) openModalMasak(item, bahanDetail);
            else openModalKurang(nama, bahanDetail);
        });
    });

    // ═══════════════════════════════════════════════════════════════════
    //  HELPERS
    // ═══════════════════════════════════════════════════════════════════
    function renderBahanDetailList(bahanDetail, targetEl) {
        targetEl.innerHTML = '';
        bahanDetail.forEach(b => {
            const li = document.createElement('li');
            li.className = b.cukup ? 'bahan-cukup' : 'bahan-kurang';
            let gramInfo = '';
            if (b.butuh > 0) {
                gramInfo = b.punya > 0 ? `${b.punya}g / ${b.butuh}g` : `Butuh ${b.butuh}g`;
            }
            li.innerHTML = `
                <span class="modal-bahan-nama">${b.nama}</span>
                ${gramInfo ? `<span class="modal-bahan-gram">${gramInfo}</span>` : ''}
            `;
            targetEl.appendChild(li);
        });
    }

    // ── INIT ──────────────────────────────────────────────────────────
    updateChipCounts();
    updateSidebarCounts();

    if (isDesktop() && detailPanel) {
        const content = detailPanel.querySelector('.kd-detail-content');
        if (content) content.style.display = 'none';
    }
});