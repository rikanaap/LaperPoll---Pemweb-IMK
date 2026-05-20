document.addEventListener('DOMContentLoaded', async () => {
    const el = {
        selectedRasaContainer: document.getElementById('selectedRasaContainer'),
        resepContainer:        document.getElementById('resepContainer'),
        resultInfoText:        document.getElementById('resultInfoText'),
    };

    const params    = new URLSearchParams(window.location.search);
    const filterIds = (params.get('filters') ?? '')
        .split(',')
        .map(Number)
        .filter(id => Number.isFinite(id) && id > 0);

    if (!filterIds.length) {
        renderEmptySelection();
        return;
    }

    await fetchResep(filterIds);

    async function fetchResep(ids) {
        try {
            const baseUrl = window.filterSwipeConfig?.apiUrl ?? '';
            if (!baseUrl) {
                throw new Error('API URL tidak terdefinisi di window.filterSwipeConfig');
            }

            const url  = `${baseUrl}?filters=${ids.join(',')}`;
            const res  = await fetch(url);

            if (!res.ok) throw new Error(`HTTP Error! Status: ${res.status}`);

            const result = await res.json();

            if (!result.success) {
                renderApiError();
                return;
            }

            if (Array.isArray(result.selected_filters)) {
                renderSelectedRasa(result.selected_filters);
            }

            renderResep(result.data ?? [], ids.length);

        } catch (err) {
            console.error('[FilterSwipe] fetchResep error:', err);
            renderNetworkError(err);
        }
    }

    function renderSelectedRasa(filters) {
        if (!el.selectedRasaContainer) return;
        el.selectedRasaContainer.innerHTML = filters
            .map(f => `<div class="selected-chip">❤️ ${escapeHtml(f.title)}</div>`)
            .join('');
    }

    function renderResep(data, totalUserSelection) {
        if (!el.resepContainer) return;

        if (!data.length) {
            el.resepContainer.innerHTML = buildStateBox(
                'Tidak ada resep cocok',
                'Coba kombinasi rasa lain',
                true
            );
            if (el.resultInfoText) el.resultInfoText.innerText = '0 resep ditemukan';
            return;
        }

        if (el.resultInfoText) {
            el.resultInfoText.innerText = `${data.length} resep ditemukan`;
        }
        
        el.resepContainer.innerHTML = data
            .map(resep => buildResepCard(resep, totalUserSelection))
            .join('');
    }

    function buildResepCard(resep, totalUserSelection) {
        const imageUrl = resep.thumbnail
            ? escapeHtml(resep.thumbnail)
            : '/images/default-food.jpg';

        const chipsHtml = (resep.filters ?? [])
            .map(f => `<span class="resep-rasa-chip">❤️ ${escapeHtml(f.title)}</span>`)
            .join('');

        return `
            <article class="resep-card" role="listitem">
                <div class="resep-card__thumbnail">
                    <img src="${imageUrl}" alt="${escapeHtml(resep.title)}" loading="lazy">
                    <div class="resep-card__badge">
                        🔥 Cocok ${escapeHtml(String(resep.match_count ?? 0))}/${totalUserSelection} Rasa
                    </div>
                </div>
                <div class="resep-card__body">
                    <h3 class="resep-card__title">${escapeHtml(resep.title || 'Tanpa Judul')}</h3>
                    <div class="resep-card__meta">
                        <div class="meta-pill">
                            <span class="material-icons-round">schedule</span>
                            <span>${formatCookDuration(resep.cook_duration)}</span>
                        </div>
                        <div class="meta-pill meta-pill--star">
                            <span class="material-icons-round">star</span>
                            <span>${escapeHtml(String(resep.current_star ?? 0))}</span>
                        </div>
                        <div class="meta-pill meta-pill--orange">
                            <span class="material-icons-round">visibility</span>
                            <span>${escapeHtml(String(resep.views_count ?? 0))}</span>
                        </div>
                    </div>
                    ${chipsHtml ? `
                        <div class="resep-card__rasa">
                            <span class="resep-card__rasa-label">Rasa pada resep ini</span>
                            <div class="resep-card__rasa-list">${chipsHtml}</div>
                        </div>
                    ` : ''}
                    <div class="resep-card__author">
                        <span class="material-icons-round">person</span>
                        <span>${escapeHtml(resep.user?.name || 'Unknown')}</span>
                    </div>
                </div>
            </article>
        `;
    }

    function renderEmptySelection() {
        if (!el.resepContainer) return;
        el.resepContainer.innerHTML = buildStateBox(
            'Belum ada rasa dipilih',
            'Silakan tentukan pilihan rasa Anda terlebih dahulu',
            true
        );
        if (el.resultInfoText) el.resultInfoText.innerText = 'Belum ada rekomendasi';
    }

    function renderApiError() {
        if (!el.resepContainer) return;
        el.resepContainer.innerHTML = buildStateBox(
            'Gagal mengambil data',
            'Server tidak mengembalikan data yang valid',
            true
        );
        if (el.resultInfoText) el.resultInfoText.innerText = 'Gagal memuat';
    }

    function renderNetworkError(err) {
        if (!el.resepContainer) return;
        el.resepContainer.innerHTML = buildStateBox(
            'Network Error',
            err.message || 'Terjadi kesalahan pada jaringan server'
        );
        if (el.resultInfoText) el.resultInfoText.innerText = 'Network error';
    }

    function buildStateBox(title, subtitle, showActionBtn = false) {
        const actionBtnHtml = showActionBtn 
            ? `<div class="state-box__action">
                   <a href="/swipe" class="btn btn--primary-outline">Kembali Memilih Rasa</a>
               </div>` 
            : '';

        return `
            <div class="state-box" role="status">
                <h2>${escapeHtml(title)}</h2>
                <p>${escapeHtml(subtitle)}</p>
                ${actionBtnHtml}
            </div>
        `;
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatCookDuration(duration) {
        if (!duration) return '-';
        if (String(duration).includes(':')) {
            const parts = duration.split(':').map(Number);
            if (parts.length >= 2) {
                const [h, m] = parts;
                return h > 0 ? `${h} jam ${m} menit` : `${m} menit`;
            }
        }
        return `${escapeHtml(String(duration))} menit`;
    }
});