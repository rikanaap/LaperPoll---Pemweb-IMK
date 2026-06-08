// meal-planner.js — Clean Adaptive Layout (Mobile / Tablet / Desktop)
// Fixes: no DOM mutation chaos, no duplicate event listeners, clean state machine
(function () {
'use strict';

/* ── Constants ─────────────────────────────────────────────── */
const WAKTU   = ['SA', 'SI', 'MA'];
const ICON    = { SA: 'wb_sunny', SI: 'restaurant', MA: 'bedtime' };
const LABEL   = { SA: 'Sarapan', SI: 'Makan Siang', MA: 'Makan Malam' };
const LABEL_U = { SA: 'SARAPAN', SI: 'MAKAN SIANG', MA: 'MAKAN MALAM' };
const HARI    = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
const HARI_F  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
const BULAN   = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
const BULAN_F = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus',
                 'September','Oktober','November','Desember'];

/* ── State ─────────────────────────────────────────────────── */
let activeDates = [];   // ['2026-06-01', ...]
let activeIdx   = 0;    // tab/col yang aktif
let serverData  = {};   // { 'YYYY-MM-DD': { planner_id, max_calorie, total_kalori, meals:{SA,SI,MA} } }
let rangeStart  = null; // Date
let rangeEnd    = null; // Date
let calStep     = 0;    // 0=pilih start, 1=pilih end
let calMonth, calYear;

/* ── Breakpoint helpers ────────────────────────────────────── */
const BP_TABLET  = 768;
const BP_DESKTOP = 1024;
const isTablet   = () => window.innerWidth >= BP_TABLET && window.innerWidth < BP_DESKTOP;
const isDesktop  = () => window.innerWidth >= BP_DESKTOP;

/* ── Utils ─────────────────────────────────────────────────── */
const pad      = n => String(n).padStart(2, '0');
const toISO    = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
const todayISO = () => toISO(todayDate());
function todayDate() { const d = new Date(); d.setHours(0,0,0,0); return d; }
function parseISO(s) { const [y,m,d] = s.split('-').map(Number); return new Date(y, m-1, d); }
function datesInRange(s, e) {
    const out = [], cur = new Date(s);
    cur.setHours(0,0,0,0);
    const end = new Date(e); end.setHours(23,59,59);
    while (cur <= end) { out.push(toISO(new Date(cur))); cur.setDate(cur.getDate()+1); }
    return out;
}
function fmtLabel(d)      { return `${d.getDate()} ${BULAN[d.getMonth()]}`; }
function fmtDateLong(d)   { return `${HARI_F[d.getDay()]}, ${d.getDate()} ${BULAN_F[d.getMonth()]} ${d.getFullYear()}`; }
function fmtRangeLabel() {
    if (!rangeStart) return 'Pilih tanggal';
    if (!rangeEnd || toISO(rangeStart) === toISO(rangeEnd)) return fmtLabel(rangeStart);
    return `${fmtLabel(rangeStart)} – ${fmtLabel(rangeEnd)}`;
}
function formatDurasi(t) {
    if (!t) return null;
    const [j, m] = t.split(':').map(Number);
    if (j > 0 && m > 0) return `${j}j ${m}mnt`;
    if (j > 0) return `${j} jam`;
    if (m > 0) return `${m} mnt`;
    return null;
}
function countMeals(iso) {
    const d = serverData[iso];
    if (!d?.meals) return 0;
    return WAKTU.filter(w => d.meals[w]).length;
}
function el(id) { return document.getElementById(id); }

/* ── API ───────────────────────────────────────────────────── */
async function api(url, method = 'GET', body = null) {
    const opts = {
        method,
        headers: {
            'Content-Type'  : 'application/json',
            'X-CSRF-TOKEN'  : window.MP.csrf,
            'Accept'        : 'application/json',
        },
    };
    if (body) opts.body = JSON.stringify(body);
    const res = await fetch(url, opts);
    if (!res.ok) {
        const err = await res.json().catch(() => ({}));
        throw new Error(err.message || `HTTP ${res.status}`);
    }
    return res.json();
}

/* ══════════════════════════════════════════════════════════════
   LOAD DATA
══════════════════════════════════════════════════════════════ */
async function loadData(start, end) {
    setLoading(true);
    try {
        const rows = await api(`${window.MP.apiBase}?start=${start}&end=${end}`);
        serverData = {};
        rows.forEach(r => { serverData[r.tanggal] = r; });
        renderAll();
        updateKaloriUI();
        updateGenerateBtn();
    } catch (e) {
        toast('Gagal memuat data: ' + e.message, true);
    } finally {
        setLoading(false);
    }
}

/* ══════════════════════════════════════════════════════════════
   RENDER DISPATCHER — satu entry point, pilih layout
══════════════════════════════════════════════════════════════ */
function renderAll() {
    if (isDesktop()) {
        renderDesktop();
    } else if (isTablet()) {
        renderTablet();
    } else {
        renderMobile();
    }
}

/* ══════════════════════════════════════════════════════════════
   MOBILE — tabs strip + vertical card stack
══════════════════════════════════════════════════════════════ */
function renderMobile() {
    // Topbar tabs
    const tabsWrap = el('mpTabsWrap');
    if (tabsWrap) tabsWrap.style.display = activeDates.length ? '' : 'none';
    buildTabStrip();

    // Content area
    const empty   = el('mpEmpty');
    const content = el('mpContent');
    if (!activeDates.length) {
        if (empty)   empty.style.display   = '';
        if (content) content.style.display = 'none';
        return;
    }
    if (empty)   empty.style.display   = 'none';
    if (content) content.style.display = '';

    // Rebuild meal cards for active day
    if (content) {
        content.innerHTML = '';
        const iso     = activeDates[activeIdx];
        const dayData = serverData[iso] || { meals: {} };
        WAKTU.forEach(w => content.appendChild(buildMealSection(iso, dayData, w)));
    }
}

/* ══════════════════════════════════════════════════════════════
   TABLET — sidebar (calendar + day list) + right panel
══════════════════════════════════════════════════════════════ */
function renderTablet() {
    // Topbar tabs: hide (sidebar takes over)
    const tabsWrap = el('mpTabsWrap');
    if (tabsWrap) tabsWrap.style.display = 'none';

    // Ensure sidebar and content-panel exist inside mp-body
    const body = el('mpBody');
    if (!body) return;

    let sidebar = el('mpSidebar');
    if (!sidebar) {
        sidebar = document.createElement('div');
        sidebar.id = 'mpSidebar';
        sidebar.className = 'mp-sidebar';
        body.insertBefore(sidebar, body.firstChild);
    }

    let panel = el('mpContentPanel');
    if (!panel) {
        panel = document.createElement('div');
        panel.id = 'mpContentPanel';
        panel.className = 'mp-content-panel';
        // Move existing static elements into panel
        ['mpEmpty', 'mpContent', 'mpLoading', 'mpGenerateBtn'].forEach(id => {
            const node = el(id);
            if (node) panel.appendChild(node);
        });
        body.appendChild(panel);
    }

    // Render sidebar
    renderTabletSidebar(sidebar);

    // Render right panel content
    const empty   = el('mpEmpty');
    const content = el('mpContent');
    const genBtn  = el('mpGenerateBtn');

    if (!activeDates.length) {
        if (empty)   empty.style.display   = '';
        if (content) content.style.display = 'none';
        if (genBtn)  { genBtn.style.opacity = '0.45'; genBtn.style.pointerEvents = 'none'; }
        return;
    }
    if (empty)   empty.style.display   = 'none';
    if (content) content.style.display = '';

    if (content) {
        content.innerHTML = '';
        const iso     = activeDates[activeIdx];
        const dayData = serverData[iso] || { meals: {} };
        WAKTU.forEach(w => content.appendChild(buildMealSection(iso, dayData, w)));
    }
}

function renderTabletSidebar(sidebar) {
    sidebar.innerHTML = '';

    // Calendar panel
    const calPanel = document.createElement('div');
    calPanel.className = 'mp-sidebar-cal-panel';
    const calContainer = document.createElement('div');
    calContainer.id = 'mpSidebarCal';
    calPanel.appendChild(calContainer);
    sidebar.appendChild(calPanel);
    buildCalendarDOM('mpSidebarCal');

    // Day list
    const dayList = document.createElement('div');
    dayList.className = 'mp-sidebar-days';

    if (!activeDates.length) {
        dayList.innerHTML = `<p class="mp-sidebar-days-label font-jakarta">Pilih tanggal dulu</p>`;
    } else {
        const lbl = document.createElement('p');
        lbl.className = 'mp-sidebar-days-label font-jakarta';
        lbl.textContent = 'Hari Terpilih';
        dayList.appendChild(lbl);

        activeDates.forEach((iso, i) => {
            const d         = parseISO(iso);
            const isToday   = iso === todayISO();
            const mealCount = countMeals(iso);
            const mealText  = mealCount ? `${mealCount} menu dipilih` : 'Belum ada menu';

            const btn = document.createElement('button');
            btn.className = 'mp-sidebar-day-btn font-jakarta'
                + (i === activeIdx ? ' active' : '')
                + (isToday        ? ' today-day' : '');
            btn.innerHTML = `
                <div class="mp-sidebar-day-dot">
                    <span class="mp-sidebar-day-num font-jakarta font-bold">${d.getDate()}</span>
                    <span class="mp-sidebar-day-mo font-jakarta">${BULAN[d.getMonth()]}</span>
                </div>
                <div class="mp-sidebar-day-info">
                    <span class="mp-sidebar-day-name font-jakarta font-semibold">${HARI_F[d.getDay()]}</span>
                    <span class="mp-sidebar-day-meals font-jakarta">${mealText}</span>
                </div>
            `;
            btn.addEventListener('click', () => switchTab(i));
            dayList.appendChild(btn);
        });
    }
    sidebar.appendChild(dayList);
}

/* ══════════════════════════════════════════════════════════════
   DESKTOP — full weekly grid (rows=meals, cols=days)
══════════════════════════════════════════════════════════════ */
/* ══════════════════════════════════════════════════════════════
   DESKTOP — sidebar kiri (navigator hari) + panel kanan (3 kolom meal)
══════════════════════════════════════════════════════════════ */
function renderDesktop() {
    // Hide topbar tabs — navigasi pakai sidebar
    const tabsWrap = el('mpTabsWrap');
    if (tabsWrap) tabsWrap.style.display = 'none';

    const body = el('mpBody');
    if (!body) return;

    // Bersihkan tablet artifacts
    const tabletSidebar = el('mpSidebar');
    if (tabletSidebar) {
        ['mpEmpty','mpContent','mpLoading','mpGenerateBtn'].forEach(id => {
            const node = el(id); if (node && node.parentElement !== body) body.appendChild(node);
        });
        tabletSidebar.remove();
    }
    const tabletPanel = el('mpContentPanel');
    if (tabletPanel) {
        ['mpEmpty','mpContent','mpLoading','mpGenerateBtn'].forEach(id => {
            const node = el(id); if (node && node.parentElement !== body) body.appendChild(node);
        });
        tabletPanel.remove();
    }

    // Hide legacy mobile elements
    ['mpEmpty','mpContent','mpLoading','mpGenerateBtn'].forEach(id => {
        const node = el(id); if (node) node.style.display = 'none';
    });

    // No dates: show empty in panel
    if (!activeDates.length) {
        removeDesktopWidgets();
        buildDesktopShell(body, true);
        return;
    }

    buildDesktopShell(body, false);
}

function removeDesktopWidgets() {
    el('mpDgShell')?.remove();
}

function buildDesktopShell(body, isEmpty) {
    // Remove old shell, rebuild fresh
    el('mpDgShell')?.remove();

    const shell = document.createElement('div');
    shell.id = 'mpDgShell';
    shell.style.cssText = 'display:contents;';
    body.appendChild(shell);

    // ── LEFT SIDEBAR ──
    const sidebar = document.createElement('div');
    sidebar.className = 'mp-dg-sidebar';
    shell.appendChild(sidebar);
    buildDesktopSidebar(sidebar, isEmpty);

    // ── RIGHT PANEL ──
    const panel = document.createElement('div');
    panel.className = 'mp-dg-panel';
    shell.appendChild(panel);

    if (isEmpty) {
        buildDesktopPanelEmpty(panel);
    } else {
        buildDesktopPanelHead(panel);
        buildDesktopPanelBody(panel);
    }
}

function buildDesktopSidebar(sidebar, isEmpty) {
    const totalSlots  = activeDates.length * WAKTU.length;
    const filledSlots = activeDates.reduce((s, iso) => s + countMeals(iso), 0);

    // Header
    const head = document.createElement('div');
    head.className = 'mp-dg-sidebar-head';
    head.innerHTML = `
        <span class="mp-dg-sidebar-title font-jakarta">Jadwal Makan</span>
        <div class="mp-dg-stats">
            <div class="mp-dg-stat">
                <span class="mp-dg-stat-val font-jakarta font-bold">${activeDates.length}</span>
                <span class="mp-dg-stat-lbl font-jakarta">Hari</span>
            </div>
            <div class="mp-dg-stat">
                <span class="mp-dg-stat-val font-jakarta font-bold">${filledSlots}/${totalSlots}</span>
                <span class="mp-dg-stat-lbl font-jakarta">Slot terisi</span>
            </div>
        </div>
    `;
    sidebar.appendChild(head);

    // Day list
    const dayList = document.createElement('div');
    dayList.className = 'mp-dg-daylist';

    if (!isEmpty) {
        activeDates.forEach((iso, i) => {
            const d         = parseISO(iso);
            const isToday   = iso === todayISO();
            const isActive  = i === activeIdx;
            const filled    = countMeals(iso);
            const dayKal    = serverData[iso]?.total_kalori || 0;
            const dayTarget = serverData[iso]?.max_calorie  || 0;
            const kalText   = dayKal > 0
                ? (dayTarget > 0 ? `${dayKal.toLocaleString('id')} / ${dayTarget.toLocaleString('id')} kal` : `${dayKal.toLocaleString('id')} kal`)
                : (filled ? `${filled} menu dipilih` : 'Belum ada menu');

            const row = document.createElement('button');
            row.className = 'mp-dg-day-row font-jakarta'
                + (isActive  ? ' dg-active'    : '')
                + (isToday   ? ' dg-today-row' : '');

            // Build meal dots
            const dotsHTML = WAKTU.map(w =>
                `<span class="mp-dg-dot${serverData[iso]?.meals?.[w] ? ' filled' : ''}"></span>`
            ).join('');

            row.innerHTML = `
                <div class="mp-dg-date-badge">
                    <span class="mp-dg-badge-day font-jakarta">${HARI[d.getDay()]}</span>
                    <span class="mp-dg-badge-num font-jakarta font-bold">${d.getDate()}</span>
                    <span class="mp-dg-badge-mo font-jakarta">${BULAN[d.getMonth()]}</span>
                </div>
                <div class="mp-dg-day-info">
                    <span class="mp-dg-day-name-txt font-jakarta font-bold">${HARI_F[d.getDay()]}</span>
                    <span class="mp-dg-day-meta-txt font-jakarta">${kalText}</span>
                </div>
                <div class="mp-dg-meal-dots">${dotsHTML}</div>
            `;
            row.addEventListener('click', () => switchTab(i));
            dayList.appendChild(row);
        });
    }
    sidebar.appendChild(dayList);

    // Footer: generate btn
    const foot = document.createElement('div');
    foot.className = 'mp-dg-sidebar-foot';
    const hasAny = !isEmpty && activeDates.some(iso => WAKTU.some(w => serverData[iso]?.meals?.[w]));
    foot.innerHTML = `
        <button id="mpDgGenBtn"
            class="mp-generate-btn font-jakarta font-bold"
            style="opacity:${hasAny?1:0.45};pointer-events:${hasAny?'auto':'none'};">
            <span class="material-icons-round">receipt_long</span>
            Generate Nota Belanja
        </button>
    `;
    foot.querySelector('#mpDgGenBtn')?.addEventListener('click', doGenerateNota);
    sidebar.appendChild(foot);
}

function buildDesktopPanelHead(panel) {
    const iso     = activeDates[activeIdx];
    const d       = parseISO(iso);
    const dayData = serverData[iso] || {};
    const current = dayData.total_kalori || 0;
    const target  = dayData.max_calorie  || 0;
    const isOver  = target > 0 && current > target;

    const head = document.createElement('div');
    head.className = 'mp-dg-panel-head';
    head.innerHTML = `
        <div>
            <div class="mp-dg-panel-date font-jakarta font-bold">
                ${HARI_F[d.getDay()]}, ${d.getDate()} ${BULAN_F[d.getMonth()]} ${d.getFullYear()}
            </div>
            <div class="mp-dg-panel-sub font-jakarta">
                ${countMeals(iso)} dari 3 menu terpilih
            </div>
        </div>
        ${target > 0 ? `
        <div class="mp-dg-panel-kal-badge font-jakarta font-bold${isOver ? ' over' : ''}">
            <span class="material-icons-round">${isOver ? 'warning_amber' : 'local_fire_department'}</span>
            ${current.toLocaleString('id')} / ${target.toLocaleString('id')} kal
        </div>` : ''}
    `;
    panel.appendChild(head);
}

function buildDesktopPanelBody(panel) {
    const iso     = activeDates[activeIdx];
    const dayData = serverData[iso] || { meals: {} };

    const body = document.createElement('div');
    body.className = 'mp-dg-panel-body';

    WAKTU.forEach(w => {
        const meal = dayData.meals?.[w] || null;
        const col  = document.createElement('div');
        col.className = 'mp-dg-meal-col';

        // Column header
        const colHead = document.createElement('div');
        colHead.className = 'mp-dg-col-head';
        colHead.innerHTML = `
            <span class="material-icons-round">${ICON[w]}</span>
            <span class="mp-dg-col-head-label font-jakarta font-bold">${LABEL[w]}</span>
        `;
        col.appendChild(colHead);

        if (meal) {
            const dur  = formatDurasi(meal.durasi);
            const card = document.createElement('div');
            card.className = 'mp-dg-meal-card';
            card.innerHTML = `
                <div class="mp-dg-meal-img">
                    ${meal.thumbnail
                        ? `<img src="${meal.thumbnail}" alt="${escapeHtml(meal.nama)}" loading="lazy">`
                        : `<span class="material-icons-round">restaurant</span>`}
                    <div class="mp-dg-meal-overlay">
                        <button class="mp-dg-meal-del font-jakarta"
                            data-detail-id="${meal.detail_id}"
                            data-iso="${iso}" data-w="${w}"
                            data-nama="${escapeHtml(meal.nama || 'resep ini')}">
                            <span class="material-icons-round">delete</span>Hapus
                        </button>
                    </div>
                </div>
                <div class="mp-dg-meal-info">
                    <p class="mp-dg-meal-name font-jakarta font-bold">${escapeHtml(meal.nama)}</p>
                    <div class="mp-dg-meal-tags">
                        <span class="mp-dg-meal-tag font-jakarta">
                            <span class="material-icons-round">local_fire_department</span>
                            ${meal.kalori} kal
                        </span>
                        ${dur ? `<span class="mp-dg-meal-tag font-jakarta"><span class="material-icons-round">schedule</span>${dur}</span>` : ''}
                    </div>
                </div>
            `;
            card.querySelector('.mp-dg-meal-del')?.addEventListener('click', e => {
                e.stopPropagation();
                const b = e.currentTarget;
                openHapusModal(b.dataset.detailId, b.dataset.iso, b.dataset.w, b.dataset.nama, LABEL[b.dataset.w]);
            });
            col.appendChild(card);
        } else {
            const maxKal   = dayData.max_calorie  || 0;
            const totalKal = dayData.total_kalori || 0;
            const href     = `${window.MP.pilihResepUrl}?tanggal=${iso}&meal_time=${w}&max_kal=${maxKal}&used_kal=${totalKal}`;
            const slot     = document.createElement('div');
            slot.className = 'mp-dg-slot-empty';
            slot.innerHTML = `
                <div class="mp-dg-slot-plus font-jakarta">+</div>
                <span class="mp-dg-slot-label font-jakarta font-semibold">Tambah resep</span>
            `;
            slot.addEventListener('click', () => { window.location.href = href; });
            col.appendChild(slot);
        }

        body.appendChild(col);
    });

    panel.appendChild(body);
}

function buildDesktopPanelEmpty(panel) {
    const empty = document.createElement('div');
    empty.className = 'mp-dg-panel-empty';
    empty.innerHTML = `
        <div class="mp-empty-icon-wrap">
            <span class="material-icons-round">calendar_month</span>
        </div>
        <p class="mp-empty-title font-jakarta font-bold">Pilih tanggal dulu</p>
        <p class="mp-empty-sub font-jakarta font-regular">
            Pilih rentang tanggal untuk mulai merencanakan jadwal makanmu
        </p>
        <button class="mp-empty-cta font-jakarta font-semibold" id="mpDgEmptyCta">
            <span class="material-icons-round">calendar_month</span>
            Pilih Tanggal
        </button>
    `;
    empty.querySelector('#mpDgEmptyCta')?.addEventListener('click', openDropdown);
    panel.appendChild(empty);
}


/* ══════════════════════════════════════════════════════════════
   SHARED BUILDERS
══════════════════════════════════════════════════════════════ */

/* Tab strip (used by mobile + desktop topbar) */
function buildTabStrip() {
    const tabs = el('mpTabs');
    if (!tabs) return;
    tabs.innerHTML = '';
    activeDates.forEach((iso, i) => {
        const d       = parseISO(iso);
        const isToday = iso === todayISO();
        const btn     = document.createElement('button');
        btn.className = 'mp-tab'
            + (i === activeIdx ? ' active' : '')
            + (isToday         ? ' today'  : '');
        btn.innerHTML = `
            <span class="mp-tab-day">${HARI[d.getDay()]}</span>
            <span class="mp-tab-date">${d.getDate()}</span>
            <span class="mp-tab-month">${BULAN[d.getMonth()]}</span>
        `;
        btn.addEventListener('click', () => switchTab(i));
        tabs.appendChild(btn);
    });
}

/* Meal section card (mobile + tablet) */
function buildMealSection(iso, dayData, w) {
    const meal    = dayData.meals?.[w] || null;
    const section = document.createElement('div');
    section.className = 'mp-meal-section';

    const maxKal   = dayData.max_calorie  || 0;
    const totalKal = dayData.total_kalori || 0;

    let headerHTML = `
        <div class="mp-meal-header">
            <span class="material-icons-round mp-meal-icon">${ICON[w]}</span>
            <span class="mp-meal-label font-jakarta font-bold">${LABEL_U[w]}</span>
            ${meal ? `<button class="mp-meal-hapus font-jakarta font-bold"
                data-detail-id="${meal.detail_id}" data-iso="${iso}" data-w="${w}"
                data-nama="${escapeHtml(meal.nama || 'resep ini')}">HAPUS</button>` : ''}
        </div>`;

    let bodyHTML = '';
    if (meal) {
        const dur = formatDurasi(meal.durasi);
        bodyHTML = `
            <div class="mp-meal-card">
                <div class="mp-meal-thumb">
                    ${meal.thumbnail
                        ? `<img src="${meal.thumbnail}" alt="${escapeHtml(meal.nama)}">`
                        : `<span class="material-icons-round">restaurant</span>`}
                </div>
                <div class="mp-meal-info">
                    <p class="mp-meal-nama font-jakarta font-semibold">${escapeHtml(meal.nama)}</p>
                    <div class="mp-meal-meta">
                        <span class="mp-meal-meta-item font-jakarta">
                            <span class="material-icons-round">local_fire_department</span>
                            ${meal.kalori} kal
                        </span>
                        ${dur ? `<span class="mp-meal-meta-item font-jakarta"><span class="material-icons-round">schedule</span>${dur}</span>` : ''}
                    </div>
                </div>
            </div>`;
    } else {
        const href = `${window.MP.pilihResepUrl}?tanggal=${iso}&meal_time=${w}&max_kal=${maxKal}&used_kal=${totalKal}`;
        bodyHTML = `
            <a href="${href}" class="mp-slot-kosong">
                <div class="mp-slot-plus"><span class="material-icons-round">add</span></div>
                <span class="mp-slot-text font-jakarta font-medium">Tambah resep</span>
            </a>`;
    }

    section.innerHTML = headerHTML + bodyHTML;

    // Bind hapus button
    section.querySelector('.mp-meal-hapus')?.addEventListener('click', e => {
        const b = e.currentTarget;
        openHapusModal(b.dataset.detailId, b.dataset.iso, b.dataset.w, b.dataset.nama, LABEL[b.dataset.w]);
    });

    return section;
}

function escapeHtml(s) {
    return String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

/* ── Switch tab ─────────────────────────────────────────────── */
function switchTab(idx) {
    activeIdx = idx;
    renderAll();
    updateKaloriUI();
}

/* ══════════════════════════════════════════════════════════════
   HAPUS MODAL
══════════════════════════════════════════════════════════════ */
function openHapusModal(detailId, iso, w, namaResep, namaSlot) {
    // Reuse or create overlay
    let overlay = el('mpHapusOverlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'mpHapusOverlay';
        overlay.style.cssText = [
            'position:fixed', 'inset:0', 'z-index:500',
            'background:rgba(0,0,0,0.5)',
            'display:flex', 'align-items:center', 'justify-content:center',
            'padding:1rem', 'backdrop-filter:blur(2px)',
        ].join(';');
        document.body.appendChild(overlay);
    }

    overlay.innerHTML = `
        <div style="background:#fff;border-radius:1.25rem;padding:1.5rem;max-width:340px;width:100%;box-shadow:0 8px 32px rgba(0,0,0,0.2);">
            <p style="font-size:.9rem;font-weight:700;color:#2D1A11;margin-bottom:.5rem;" class="font-jakarta">Hapus dari jadwal?</p>
            <p style="font-size:.8rem;color:#6B5B54;line-height:1.6;margin-bottom:1.25rem;" class="font-jakarta">
                <strong>${escapeHtml(namaResep)}</strong> akan dihapus dari <strong>${escapeHtml(namaSlot)}</strong>.
                Tindakan ini tidak dapat dibatalkan.
            </p>
            <div style="display:flex;gap:.6rem;justify-content:flex-end;">
                <button id="mpHapusBatal"
                    style="padding:.55rem 1rem;border-radius:.75rem;border:1.5px solid #E0D3CA;background:#fff;font-size:.82rem;font-weight:600;cursor:pointer;color:#6B5B54;"
                    class="font-jakarta">Batal</button>
                <button id="mpHapusOke"
                    style="padding:.55rem 1rem;border-radius:.75rem;border:none;background:#DC2626;color:#fff;font-size:.82rem;font-weight:600;cursor:pointer;"
                    class="font-jakarta">Hapus</button>
            </div>
        </div>`;

    overlay.style.display = 'flex';

    const closeOverlay = () => { overlay.style.display = 'none'; };
    el('mpHapusBatal').onclick = closeOverlay;
    overlay.onclick = e => { if (e.target === overlay) closeOverlay(); };
    el('mpHapusOke').onclick   = () => doHapus(detailId, iso, w, overlay);
}

async function doHapus(detailId, iso, w, overlay) {
    const okeBtn = el('mpHapusOke');
    if (okeBtn) { okeBtn.disabled = true; okeBtn.textContent = '…'; }
    try {
        const res = await api(`${window.MP.apiBase}/detail/${detailId}`, 'DELETE');
        overlay.style.display = 'none';

        if (serverData[iso]?.meals) {
            serverData[iso].meals[w] = null;
            serverData[iso].total_kalori = res?.total_kalori !== undefined
                ? res.total_kalori
                : WAKTU.reduce((s, x) => s + (serverData[iso].meals[x]?.kalori || 0), 0);
        }

        renderAll();
        updateKaloriUI();
        updateGenerateBtn();
        toast('Resep dihapus dari jadwal');
    } catch (e) {
        overlay.style.display = 'none';
        toast('Gagal menghapus: ' + e.message, true);
    }
}

/* ══════════════════════════════════════════════════════════════
   KALORI UI
══════════════════════════════════════════════════════════════ */
function updateKaloriUI() {
    if (!activeDates.length) return;
    const iso     = activeDates[activeIdx];
    const dayData = serverData[iso] || {};
    const target  = dayData.max_calorie  || 0;
    const current = dayData.total_kalori || 0;

    const wrap   = el('mpKaloriWrap');
    const setBtn = el('mpSetKaloriBtn');
    if (!wrap || !setBtn) return;

    if (target > 0) {
        wrap.style.display   = '';
        setBtn.style.display = 'none';

        const curEl   = el('mpKaloriCurrent');
        const tgtEl   = el('mpKaloriTarget');
        const barFill = el('mpBarFill');
        const label   = el('mpBarLabel');
        const overEl  = el('mpKaloriOver');

        if (curEl) curEl.textContent = current.toLocaleString('id');
        if (tgtEl) tgtEl.textContent = `${target.toLocaleString('id')} kal`;

        const LAYERS = [
            { from: '#FF8A50', to: '#FF6D00' }, // 0–1× (normal)
            { from: '#FF6D00', to: '#E53935' }, // 1–2×
            { from: '#E53935', to: '#B71C1C' }, // 2–3×
            { from: '#B71C1C', to: '#7F0000' }, // 3×+
        ];

        if (current <= target) {
            const pct = Math.min((current / target) * 100, 100);
            if (barFill) {
                barFill.style.width      = pct + '%';
                barFill.style.background = `linear-gradient(90deg, ${LAYERS[0].from}, ${LAYERS[0].to})`;
            }
            if (label) { label.textContent = Math.round(pct) + '%'; label.style.color = '#E65100'; }
            if (overEl) overEl.style.display = 'none';
        } else {
            const ratio      = current / target;
            const layerIdx   = Math.min(Math.floor(ratio), LAYERS.length - 1); // 1-based layer
            const layer      = LAYERS[Math.min(layerIdx, LAYERS.length - 1)];
            const progress   = ratio - Math.floor(ratio); // 0..1 dalam layer ini
            const nextLayer  = LAYERS[Math.min(layerIdx + 1, LAYERS.length - 1)];

            if (barFill) {
                barFill.style.width = '100%';
                const sp = Math.round(progress * 100);
                barFill.style.background = sp > 0 && sp < 100
                    ? `linear-gradient(90deg, ${layer.from} 0%, ${layer.to} ${sp}%, ${nextLayer.to} 100%)`
                    : layer.from;
            }
            const lebih = (current - target).toLocaleString('id');
            if (label) { label.textContent = `+${lebih} kal melebihi target`; label.style.color = '#B71C1C'; }
            if (overEl) overEl.style.display = 'flex';
        }
    } else {
        wrap.style.display   = 'none';
        setBtn.style.display = '';
    }
}

/* ══════════════════════════════════════════════════════════════
   MODAL KALORI
══════════════════════════════════════════════════════════════ */
function openModal() {
    if (!activeDates.length) return;
    const iso  = activeDates[activeIdx];
    const d    = parseISO(iso);
    const dateEl = el('mpModalDate');
    if (dateEl) dateEl.textContent = fmtDateLong(d);

    const input = el('mpKaloriInput');
    if (input) {
        const cur = serverData[iso]?.max_calorie || null;
        input.value = cur || '';
        syncChips(cur || 0);
    }
    el('mpModalOverlay').style.display = 'flex';
}
function closeModal() { el('mpModalOverlay').style.display = 'none'; }

el('mpKaloriEdit')   ?.addEventListener('click', openModal);
el('mpSetKaloriBtn') ?.addEventListener('click', openModal);
el('mpModalCancel')  ?.addEventListener('click', closeModal);
el('mpModalClose')   ?.addEventListener('click', closeModal);
el('mpModalOverlay') ?.addEventListener('click', e => { if (e.target === el('mpModalOverlay')) closeModal(); });

el('mpStepMinus')?.addEventListener('click', () => {
    const inp = el('mpKaloriInput');
    const v   = Math.max(100, (parseInt(inp.value) || 0) - 100);
    inp.value = v; syncChips(v);
});
el('mpStepPlus')?.addEventListener('click', () => {
    const inp = el('mpKaloriInput');
    const v   = Math.min(9999, (parseInt(inp.value) || 0) + 100);
    inp.value = v; syncChips(v);
});
el('mpKaloriInput')?.addEventListener('input', e => syncChips(parseInt(e.target.value) || 0));

document.querySelectorAll('.mp-chip').forEach(c => {
    c.addEventListener('click', () => {
        const v = parseInt(c.dataset.val);
        const inp = el('mpKaloriInput');
        if (inp) inp.value = v;
        syncChips(v);
    });
});
function syncChips(val) {
    document.querySelectorAll('.mp-chip').forEach(c => {
        c.classList.toggle('active', parseInt(c.dataset.val) === val);
    });
}

el('mpModalSave')?.addEventListener('click', async () => {
    const val = parseInt(el('mpKaloriInput')?.value);
    if (!val || val < 100) { toast('Masukkan minimal 100 kal', true); return; }

    const btn = el('mpModalSave');
    btn.disabled = true;
    btn.innerHTML = '<span class="material-icons-round">hourglass_empty</span> Menyimpan…';

    try {
        const iso  = activeDates[activeIdx];
        const data = await api(`${window.MP.apiBase}/kalori`, 'POST', { tanggal: iso, max_calorie: val });
        if (!serverData[iso]) serverData[iso] = { meals: {}, total_kalori: 0 };
        serverData[iso].max_calorie = data.max_calorie;
        serverData[iso].planner_id  = data.planner_id;
        closeModal();
        updateKaloriUI();
        toast('Target kalori disimpan!');
    } catch (e) {
        toast('Gagal menyimpan: ' + e.message, true);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<span class="material-icons-round">check</span> Simpan Target';
    }
});

/* ══════════════════════════════════════════════════════════════
   GENERATE NOTA
══════════════════════════════════════════════════════════════ */
function updateGenerateBtn() {
    // Mobile/tablet FAB
    const btn = el('mpGenerateBtn');
    if (btn && !isDesktop()) {
        const hasAny = activeDates.some(iso => WAKTU.some(w => serverData[iso]?.meals?.[w]));
        btn.style.opacity       = hasAny ? '1'    : '0.45';
        btn.style.pointerEvents = hasAny ? 'auto' : 'none';
    }
    // Desktop summary bar btn — rebuilt by renderDesktopSummary
}

async function doGenerateNota() {
    if (!activeDates.length) return;
    const start = activeDates[0];
    const end   = activeDates[activeDates.length - 1];
    try {
        const data = await api(`${window.MP.apiBase}/generate-nota`, 'POST', { start, end });
        if (data.success) window.location.href = data.redirect;
    } catch (e) {
        toast('Gagal generate nota: ' + e.message, true);
    }
}

// Mobile/tablet FAB
el('mpGenerateBtn')?.addEventListener('click', doGenerateNota);

/* ══════════════════════════════════════════════════════════════
   DATE PICKER DROPDOWN
══════════════════════════════════════════════════════════════ */
const dropdown = el('mpDropdown');
const backdrop = el('mpBackdrop');

function openDropdown() {
    if (!dropdown || !backdrop) return;
    dropdown.style.display = '';
    backdrop.style.display = '';
    el('dateRangeChevron')?.classList.add('open');
    el('dateRangeBtn')?.classList.add('active');
    buildCalendarDOM('mpCalendar');
}
function closeDropdown() {
    if (!dropdown || !backdrop) return;
    dropdown.style.display = 'none';
    backdrop.style.display = 'none';
    el('dateRangeChevron')?.classList.remove('open');
    el('dateRangeBtn')?.classList.remove('active');
}

el('dateRangeBtn')?.addEventListener('click', () => {
    dropdown?.style.display === 'none' ? openDropdown() : closeDropdown();
});
backdrop?.addEventListener('click', closeDropdown);

// Preset buttons
document.querySelectorAll('.mp-preset').forEach(btn => {
    btn.addEventListener('click', async () => {
        const t = todayDate();
        let s, e;
        switch (btn.dataset.preset) {
            case 'today':
                s = e = new Date(t); break;
            case 'tomorrow':
                s = new Date(t); s.setDate(t.getDate() + 1); e = new Date(s); break;
            case 'next7':
                s = new Date(t); e = new Date(t); e.setDate(t.getDate() + 6); break;
            case 'thisweek': {
                const dow = t.getDay() === 0 ? 6 : t.getDay() - 1;
                s = new Date(t); s.setDate(t.getDate() - dow);
                if (s < t) s = new Date(t);
                e = new Date(s); e.setDate(s.getDate() + 6);
                break;
            }
            case 'thismonth':
                s = new Date(t);
                e = new Date(t.getFullYear(), t.getMonth() + 1, 0);
                break;
            default: return;
        }
        calStep = 0;
        closeDropdown();
        await applyRange(s, e);
    });
});

/* ── Calendar DOM builder (reusable for main + sidebar) ── */
function buildCalendarDOM(containerId) {
    const container = el(containerId);
    if (!container) return;

    const tISO      = todayISO();
    const firstDay  = new Date(calYear, calMonth, 1).getDay();
    const daysInMon = new Date(calYear, calMonth + 1, 0).getDate();
    const offset    = firstDay === 0 ? 6 : firstDay - 1;
    const dayNames  = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];

    let html = `
        <div class="mp-cal-nav">
            <button class="mp-cal-nav-btn mp-cal-prev-btn">
                <span class="material-icons-round">chevron_left</span>
            </button>
            <span class="mp-cal-month font-jakarta font-bold">${BULAN_F[calMonth]} ${calYear}</span>
            <button class="mp-cal-nav-btn mp-cal-next-btn">
                <span class="material-icons-round">chevron_right</span>
            </button>
        </div>
        <div class="mp-cal-grid">`;

    dayNames.forEach(d => { html += `<span class="mp-cal-day-name font-jakarta">${d}</span>`; });
    for (let i = 0; i < offset; i++) html += `<span class="mp-cal-empty"></span>`;

    for (let day = 1; day <= daysInMon; day++) {
        const d   = new Date(calYear, calMonth, day);
        const iso = toISO(d);
        const isPast = iso < tISO;
        let cls = 'mp-cal-cell font-jakarta';
        if (isPast) {
            cls += ' mp-cal-disabled';
        } else {
            if (iso === tISO) cls += ' mp-cal-today';
            if (rangeStart && rangeEnd) {
                const ds = rangeStart.getTime(), de = rangeEnd.getTime(), dt = d.getTime();
                if (ds === dt || de === dt) cls += ' mp-cal-selected';
                else if (dt > ds && dt < de) cls += ' mp-cal-in-range';
            } else if (rangeStart && toISO(rangeStart) === iso) {
                cls += ' mp-cal-selected';
            }
        }
        html += `<span class="${cls}" data-date="${iso}">${day}</span>`;
    }
    html += `</div>`;

    // Only show hint in main dropdown (not compact sidebar)
    if (containerId === 'mpCalendar') {
        html += `<p class="mp-cal-hint font-jakarta" id="mpCalHint">
            ${calStep === 0 ? 'Ketuk tanggal mulai' : 'Ketuk tanggal akhir'}
        </p>`;
    }

    container.innerHTML = html;

    // Nav buttons
    container.querySelector('.mp-cal-prev-btn')?.addEventListener('click', e => {
        e.stopPropagation();
        if (--calMonth < 0) { calMonth = 11; calYear--; }
        buildCalendarDOM(containerId);
    });
    container.querySelector('.mp-cal-next-btn')?.addEventListener('click', e => {
        e.stopPropagation();
        if (++calMonth > 11) { calMonth = 0; calYear++; }
        buildCalendarDOM(containerId);
    });

    // Cell clicks
    container.querySelectorAll('.mp-cal-cell:not(.mp-cal-disabled)').forEach(cell => {
        cell.addEventListener('click', async e => {
            e.stopPropagation();
            const clicked = parseISO(cell.dataset.date);
            if (calStep === 0 || (rangeStart && rangeEnd)) {
                // Start selection
                rangeStart = clicked; rangeEnd = null; calStep = 1;
                buildCalendarDOM(containerId);
            } else {
                // End selection
                if (clicked < rangeStart) { rangeEnd = rangeStart; rangeStart = clicked; }
                else rangeEnd = clicked;
                calStep = 0;
                closeDropdown();
                await applyRange(rangeStart, rangeEnd);
            }
        });
    });
}

/* ── Apply range ─────────────────────────────────────────────── */
async function applyRange(s, e) {
    rangeStart  = s;
    rangeEnd    = e || s;
    activeDates = datesInRange(rangeStart, rangeEnd);
    activeIdx   = 0;

    el('dateRangeLabel').textContent = fmtRangeLabel();

    // Persist range so pilih-resep redirect can restore it
    try {
        sessionStorage.setItem('mp_range', JSON.stringify({
            start: toISO(rangeStart),
            end  : toISO(rangeEnd),
        }));
    } catch (_) {}

    await loadData(toISO(rangeStart), toISO(rangeEnd));
}

/* ── Loading state ───────────────────────────────────────────── */
function setLoading(show) {
    const loading = el('mpLoading');
    const content = el('mpContent');
    if (loading) loading.style.display = show ? '' : 'none';
    if (content && show) content.style.display = 'none';
}

/* ── Toast ───────────────────────────────────────────────────── */
let toastTimer;
function toast(msg, isErr = false) {
    const toastEl = el('mpToast');
    const icon    = el('mpToastIcon');
    const txt     = el('mpToastMsg');
    if (!toastEl) return;
    toastEl.className = 'mp-toast' + (isErr ? ' error' : '');
    if (icon) icon.textContent = isErr ? 'error_outline' : 'check_circle';
    if (txt)  txt.textContent  = msg;
    toastEl.style.display = '';
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => { toastEl.style.display = 'none'; }, 3200);
}

/* ── Return from pilih-resep ─────────────────────────────────── */
async function handleReturnFromPilihResep() {
    const p     = new URLSearchParams(window.location.search);
    const date  = p.get('date');
    const waktu = p.get('meal_time');
    window.history.replaceState({}, '', window.location.pathname);

    // Restore range from sessionStorage
    try {
        const saved = sessionStorage.getItem('mp_range');
        if (saved) {
            const { start, end } = JSON.parse(saved);
            rangeStart = parseISO(start);
            rangeEnd   = parseISO(end);
        }
    } catch (_) {}

    if (!rangeStart && date) { rangeStart = parseISO(date); rangeEnd = parseISO(date); }
    if (!rangeStart) return;

    await applyRange(rangeStart, rangeEnd);

    // Jump to the day that was just added
    if (date) {
        const idx = activeDates.indexOf(date);
        if (idx >= 0) { activeIdx = idx; renderAll(); updateKaloriUI(); }
    }
    if (waktu) toast(`Resep ditambahkan ke ${LABEL[waktu] || waktu}!`);
}

/* ── Resize: re-render on breakpoint change ──────────────────── */
let lastBP = '';
function currentBP() { return isDesktop() ? 'desktop' : isTablet() ? 'tablet' : 'mobile'; }

let resizeTimer;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
        const bp = currentBP();
        if (bp !== lastBP) {
            lastBP = bp;
            if (activeDates.length) { renderAll(); updateKaloriUI(); }
        }
    }, 200);
});

/* ── Empty CTA ───────────────────────────────────────────────── */
el('mpEmptyCta')?.addEventListener('click', openDropdown);

/* ══════════════════════════════════════════════════════════════
   INIT
══════════════════════════════════════════════════════════════ */
(async function init() {
    const now = new Date();
    calMonth  = now.getMonth();
    calYear   = now.getFullYear();
    lastBP    = currentBP();

    const p = new URLSearchParams(window.location.search);

    if (p.get('added')) {
        await handleReturnFromPilihResep();
        return;
    }

    // Try restore from sessionStorage, fallback to today
    let defaultStart = todayDate();
    let defaultEnd   = todayDate();
    try {
        const saved = sessionStorage.getItem('mp_range');
        if (saved) {
            const { start, end } = JSON.parse(saved);
            const s = parseISO(start);
            const e = parseISO(end);
            const t = todayDate();
            if (e >= t) {
                defaultStart = s < t ? t : s;
                defaultEnd   = e < defaultStart ? new Date(defaultStart) : e;
            }
        }
    } catch (_) {}

    await applyRange(defaultStart, defaultEnd);
})();

})();