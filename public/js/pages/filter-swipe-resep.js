document.addEventListener('DOMContentLoaded', async () => {
    const el = {
        selectedRasa: document.getElementById('selectedRasaContainer'),
        resepGrid:    document.getElementById('resepContainer'),
        resultInfo:   document.getElementById('resultInfoText'),
    };

    const filterIds = new URLSearchParams(window.location.search)
        .get('filters')
        ?.split(',')
        .map(Number)
        .filter(id => Number.isFinite(id) && id > 0)
        ?? [];

    if (!filterIds.length) {
        setInfo('Belum ada rekomendasi');
        setGrid(stateBox('Belum ada rasa dipilih', 'Silakan tentukan pilihan rasa terlebih dahulu', true));
        return;
    }

    await load(filterIds);

    async function load(ids) {
    const base = window.filterSwipeConfig?.apiUrl;
    if (!base) { renderError('Konfigurasi API tidak ditemukan'); return; }

    try {
        const res = await fetch(`${base}?filters=${ids.join(',')}`);
        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        const json = await res.json();
        if (!json.success) { renderError(json.message); return; }

        const selectedFilters = json.selected_filters ?? [];

        renderChips(selectedFilters);
        renderGrid(json.data ?? [], selectedFilters.length, selectedFilters);
    } catch (err) {
        renderError(err.message ?? 'Terjadi kesalahan jaringan');
    }
}

    function renderChips(filters) {
        if (!el.selectedRasa || !filters.length) return;
        el.selectedRasa.innerHTML = filters
            .map(f => `<div class="selected-chip"> ${esc(f.title)}</div>`)
            .join('');
    }

   function renderGrid(data, totalSelected, selectedFilters) {
    if (!data.length) {
        setInfo('0 resep ditemukan');
        setGrid(stateBox('Tidak ada resep cocok', 'Coba kombinasi rasa lain', true));
        return;
    }

    setInfo(`${data.length} resep ditemukan`);
    setGrid(data.map(r => cardHtml(r, totalSelected, selectedFilters)).join(''));
}

    function renderError(msg) {
        setInfo('Gagal memuat');
        setGrid(stateBox('Terjadi kesalahan', msg ?? 'Coba lagi beberapa saat', true));
    }

 function cardHtml(r, total, selectedFilters) {
    const img       = r.thumbnail ? esc(r.thumbnail) : '/images/default-food.jpg';
    const match     = Number(r.match_count ?? 0);
    const percent   = total > 0 ? Math.round((match / total) * 100) : 0;
    const detailUrl = r.detail_url || `/detail-resep/${r.id}`;

    const matchStatus = percent >= 80
        ? { cls: 'excellent', label: 'Sangat Cocok' }
        : percent >= 50
            ? { cls: 'good', label: 'Cocok' }
            : { cls: 'low', label: 'Kurang Cocok' };

    // Hitung missing rasa dari selected vs yang ada di resep
    const resepFilterIds = (r.filters ?? []).map(f => f.id);
    const missingRasa    = selectedFilters.filter(f => !resepFilterIds.includes(f.id));

    const missingHtml = missingRasa.length === 0
        ? `<div class="perfect-match">
               <span class="material-icons-round">verified</span>
               <span>Semua rasa terpenuhi! 🎉</span>
           </div>`
        : `<div class="missing-section">
               <div class="missing-label">
                   <span class="material-icons-round">favorite_border</span>
                   <span>Rasa belum terpenuhi</span>
               </div>
               <div class="missing-chips">
                   ${missingRasa.map(f => `<div class="missing-chip">${esc(f.title)}</div>`).join('')}
               </div>
           </div>`;

    // Rasa yang ada di resep ini
    const allRasaChips = (r.filters ?? [])
        .map(f => `<span class="resep-rasa-chip"> ${esc(f.title)}</span>`)
        .join('');

    return `
        <article class="resep-card" role="listitem"
                 data-detail-url="${detailUrl}"
                 style="cursor:pointer;">
            <div class="resep-card__thumbnail">
                <img src="${img}" alt="${esc(r.title)}" loading="lazy"
                     onerror="this.src='/images/default-food.jpg'">
            </div>
            <div class="resep-card__body">
                <h3 class="resep-card__title">${esc(r.title || 'Tanpa Judul')}</h3>

                <div class="resep-card__meta">
                    <div class="meta-pill">
                        <span class="material-icons-round">schedule</span>
                        <span>${duration(r.cook_duration)}</span>
                    </div>
                    <div class="meta-pill meta-pill--star">
                        <span class="material-icons-round">star</span>
                        <span>${Number(r.current_star ?? 0).toFixed(1)}</span>
                    </div>
                    <div class="meta-pill meta-pill--orange">
                        <span class="material-icons-round">visibility</span>
                        <span>${Number(r.views_count ?? 0).toLocaleString('id-ID')}</span>
                    </div>
                </div>

                <div class="match-wrapper">
                    <div class="match-header">
                        <div class="match-percent">
                            <div class="match-percent-circle">${percent}%</div>
                            <div class="match-percent-info">
                                <h4>Kecocokan Rasa</h4>
                                <p>${match} / ${total} rasa terpenuhi</p>
                            </div>
                        </div>
                        <div class="match-status ${matchStatus.cls}">${matchStatus.label}</div>
                    </div>
                    ${missingHtml}
                </div>

                ${allRasaChips ? `
                    <div class="resep-card__rasa">
                        <span class="resep-card__rasa-label">Rasa pada resep ini</span>
                        <div class="resep-card__rasa-list">${allRasaChips}</div>
                    </div>` : ''}

                <div class="resep-card__author">
                    <span class="material-icons-round">person</span>
                    <span>${esc(r.user?.name || 'Unknown')}</span>
                </div>
            </div>
        </article>
    `;
}

    function stateBox(title, sub, showBack = false) {
        const back = showBack
            ? `<div class="state-box__action"><a href="${window.filterSwipeConfig?.swipeUrl ?? '/swipe'}" class="btn btn--primary-outline">Kembali Memilih Rasa</a></div>`
            : '';
        return `<div class="state-box" role="status" aria-live="polite"><h2>${esc(title)}</h2><p>${esc(sub)}</p>${back}</div>`;
    }

    function setInfo(text) { if (el.resultInfo) el.resultInfo.textContent = text; }
    function setGrid(html) { if (el.resepGrid)  el.resepGrid.innerHTML    = html; }

    function duration(val) {
        if (!val) return '-';
        const s = String(val);
        if (s.includes(':')) {
            const [h, m] = s.split(':').map(Number);
            if (h > 0 && m > 0) return `${h} jam ${m} menit`;
            return h > 0 ? `${h} jam` : `${m} menit`;
        }
        const n = parseInt(s, 10);
        return isNaN(n) ? s : `${n} menit`;
    }

    function esc(str) {
        if (str == null) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
});

// ── Navigasi ke detail resep saat card diklik ──────────────────────────────
document.addEventListener('click', function (e) {
    const card = e.target.closest('[data-detail-url]');
    if (card) {
        window.location.href = card.dataset.detailUrl;
    }
});

function renderChips(filters) {
    if (!el.selectedRasa || !filters.length) return;
    el.selectedRasa.innerHTML = filters
        .map(f => `<div class="selected-chip">❤️ ${esc(f.title)}</div>`)
        .join('');

    // Update total count
    const countEl = document.getElementById('totalRasaCount');
    if (countEl) countEl.textContent = filters.length;
}