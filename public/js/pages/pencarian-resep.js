(() => {
    const CONFIG = {
        mobileBreakpoint: 768,
        debounceDelay: 300,
    };

    const main = document.querySelector('main');
    if (!main) return;

    const page = main.dataset.page;
    const API = {
        searchUrl: main.dataset.searchUrl,
        bahanUrl: main.dataset.bahanUrl,
        filterUrl: main.dataset.filterUrl,
        searchPageUrl: main.dataset.searchPageUrl,
    };

    let activeController = null;

    const state = {
        selectedBahan: [],
        recipes: [],
        pagination: null,
        isMobile: window.innerWidth < CONFIG.mobileBreakpoint,
    };

    const getElement = (...selectors) => {
        for (const selector of selectors) {
            const element = document.querySelector(selector);
            if (element) return element;
        }
        return null;
    };

    const debounce = (callback, delay = 300) => {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => callback(...args), delay);
        };
    };

    const elements = {
        searchInput: getElement('#searchInput'),
        bahanList: getElement('.bahan-list'),
        btnApply: getElement('#terapkanBtn'),
        btnHapus: getElement('#hapusSemuaBtn'),
        selectedInfo: getElement('#selectedInfo'),
        chipsWrapper: getElement('#selectedChips', '#chipsContainer'),
        resultInfoText: getElement('#resultInfoText', '#resultInfo'),
        resepContainer: getElement('#resepContainer', '#resepList'),
        resultPlaceholder: getElement('#resultPlaceholder', '#emptyState'),
        loadingState: getElement('#loadingState'),
    };

    const api = {
        async fetchRecipes(page = 1) {
            try {
                if (activeController) activeController.abort();
                activeController = new AbortController();

                ui.showLoading();
                const params = new URLSearchParams();
                state.selectedBahan.forEach(bahan => params.append('bahan_ids[]', bahan.id));
                params.append('page', page);

                const response = await fetch(`${API.searchUrl}?${params.toString()}`, {
                    signal: activeController.signal
                });

                if (!response.ok) throw new Error('Failed fetch recipes');

                const result = await response.json();
                state.recipes = result.data || [];
                state.pagination = result.pagination || null;
                ui.renderRecipes();
            } catch (error) {
                if (error.name === 'AbortError') return;
                console.error(error);
                ui.showEmptyState('Terjadi kesalahan saat mengambil resep');
            } finally {
                ui.hideLoading();
            }
        },

        async fetchBahansByIds(ids = []) {
            try {
                if (!ids.length) return [];
                const response = await fetch(`${API.bahanUrl}?ids=${ids.join(',')}`);
                if (!response.ok) throw new Error('Failed fetch bahans');
                const result = await response.json();
                return result.data || [];
            } catch (error) {
                console.error(error);
                return [];
            }
        },
    };

    const templates = {
        recipeCard(resep) {
            const thumbnail = resep.thumbnail 
                ? `<img src="${resep.thumbnail}" alt="${resep.title}">`
                : `<div class="resep-banner-placeholder"><span class="material-icons-round">restaurant</span></div>`;

            const matchStatus = resep.match_percentage >= 80 ? '<div class="match-status excellent">Sangat Cocok</div>'
                : resep.match_percentage >= 50 ? '<div class="match-status good">Cocok</div>'
                : '<div class="match-status low">Kurang Cocok</div>';

            const missingContent = resep.missing_bahans?.length
                ? `<div class="missing-section">
                    <div class="missing-label"><span class="material-icons-round">kitchen</span><span>Bahan yang belum tersedia</span></div>
                    <div class="missing-chips">${resep.missing_bahans.map(item => `<div class="missing-chip">${item.nama}</div>`).join('')}</div>
                   </div>`
                : `<div class="perfect-match"><span class="material-icons-round">verified</span><span>Semua bahan tersedia 🎉</span></div>`;

            return `
                <div class="resep">
                    <div class="resep-banner">${thumbnail}</div>
                    <div class="resep-container-bottom">
                        <div class="resep-content">
                            <div class="resep-detail">
                                <div class="resep-top-header">
                                    <h1 class="resep-title">${resep.title}</h1>
                                    <span class="material-icons-round resep-arrow">chevron_right</span>
                                </div>
                                <div class="resep-meta-list">
                                    <div class="meta-item"><span class="material-icons-round">schedule</span><p>${resep.cook_duration || '-'}</p></div>
                                    <div class="meta-item"><span class="material-icons-round">star</span><p>${Number(resep.rating).toFixed(1)}</p></div>
                                    <div class="meta-item views"><span class="material-icons-round">visibility</span><p>${resep.views}</p></div>
                                </div>
                                <div class="match-wrapper">
                                    <div class="match-header">
                                        <div class="match-percent">
                                            <div class="match-percent-circle">${resep.match_percentage}%</div>
                                            <div class="match-percent-info">
                                                <h4>Tingkat Kecocokan</h4>
                                                <p>${resep.matched_bahan_count} dari ${resep.total_bahan_count} bahan tersedia</p>
                                            </div>
                                        </div>
                                        ${matchStatus}
                                    </div>
                                    ${missingContent}
                                </div>
                                <div class="resep-verified"><p class="user-name">${resep.author?.name || 'User'}</p></div>
                            </div>
                        </div>
                    </div>
                </div>`;
        },
    };

    const ui = {
        updateItemState() {
            document.querySelectorAll('.bahan-item').forEach(item => {
                const checkbox = item.querySelector('input');
                item.classList.toggle('active', checkbox.checked);
            });
        },

        renderSelectedInfo() {
            const total = state.selectedBahan.length;
            if (elements.selectedInfo) {
                elements.selectedInfo.textContent = `${total} bahan dipilih`;
                elements.selectedInfo.classList.toggle('hidden', total === 0);
            }
            [elements.btnApply, elements.btnHapus].forEach(btn => {
                if (!btn) return;
                btn.disabled = total === 0;
                btn.classList.toggle('disabled', total === 0);
            });
        },

        renderChips() {
            if (!elements.chipsWrapper) return;
            elements.chipsWrapper.innerHTML = state.selectedBahan.map(bahan => `
                <div class="chip">
                    <span>${bahan.nama}</span>
                    <span class="material-icons-round chip-close" data-id="${bahan.id}">close</span>
                </div>`).join('');
        },

        renderResultText() {
            if (!elements.resultInfoText) return;
            if (!state.selectedBahan.length) {
                elements.resultInfoText.textContent = 'Pilih bahan untuk melihat resep';
                return;
            }
            const names = state.selectedBahan.map(item => item.nama).join(', ');
            elements.resultInfoText.textContent = `Menampilkan rekomendasi resep berdasarkan: ${names}`;
        },

        renderRecipes() {
            if (!elements.resepContainer) return;
            if (!state.recipes.length) {
                ui.showEmptyState('Resep tidak ditemukan');
                return;
            }
            elements.resultPlaceholder?.classList.add('hidden');
            elements.resepContainer.classList.remove('hidden');
            elements.resepContainer.innerHTML = state.recipes.map(templates.recipeCard).join('');
        },

        resetRecipes() {
            if (!elements.resepContainer) return;
            elements.resepContainer.innerHTML = '';
            elements.resepContainer.classList.add('hidden');
            elements.resultPlaceholder?.classList.remove('hidden');
        },

        showLoading() { elements.loadingState?.classList.remove('hidden'); },
        hideLoading() { elements.loadingState?.classList.add('hidden'); },

        showEmptyState(message) {
            ui.resetRecipes();
            if (!elements.resultPlaceholder) return;
            elements.resultPlaceholder.innerHTML = `<span class="material-icons-round">restaurant_menu</span><h3>${message}</h3>`;
        },

        renderAll() {
            ui.updateItemState();
            ui.renderSelectedInfo();
            ui.renderChips();
            ui.renderResultText();
        }
    };

    const logic = {
        syncSelectedBahan() {
            state.selectedBahan = Array.from(document.querySelectorAll('.bahan-item input:checked')).map(input => ({
                id: input.dataset.id,
                nama: input.dataset.nama,
            }));

            ui.renderAll();
            if (!state.isMobile) {
                state.selectedBahan.length ? api.fetchRecipes() : ui.resetRecipes();
            }
        },

        async initFilterPage() {
            const params = new URLSearchParams(window.location.search);
            const bahan = params.get('bahan');
            if (!bahan) return ui.showEmptyState('Tidak ada bahan dipilih');

            const ids = bahan.split(',').filter(Boolean);
            state.selectedBahan = await api.fetchBahansByIds(ids);
            ui.renderAll();
            await api.fetchRecipes();
        },

        handleSearch: debounce(event => {
            const keyword = event.target.value.toLowerCase();
            document.querySelectorAll('.bahan-item').forEach(item => {
                const nama = item.querySelector('.bahan-nama').textContent.toLowerCase();
                item.style.display = nama.includes(keyword) ? 'flex' : 'none';
            });
        }, CONFIG.debounceDelay),
    };

    function initEvents() {
        elements.searchInput?.addEventListener('input', logic.handleSearch);

        elements.bahanList?.addEventListener('click', event => {
            const item = event.target.closest('.bahan-item');
            if (!item) return;

            const checkbox = item.querySelector('input');

            // kalau bukan checkbox → toggle manual
            if (!event.target.matches('input')) {
                event.preventDefault();
                checkbox.checked = !checkbox.checked;
            }

            logic.syncSelectedBahan();
        });

        elements.chipsWrapper?.addEventListener('click', async event => {
            if (!event.target.classList.contains('chip-close')) return;
            const id = event.target.dataset.id;
            state.selectedBahan = state.selectedBahan.filter(item => item.id != id);

            if (page === 'filter') {
                if (!state.selectedBahan.length) return window.location.href = API.searchPageUrl;
                const ids = state.selectedBahan.map(item => item.id);
                window.history.replaceState({}, '', `${API.filterUrl}?bahan=${ids.join(',')}`);
                ui.renderAll();
                await api.fetchRecipes();
                return;
            }

            document.querySelectorAll('.bahan-item input').forEach(input => {
                if (input.dataset.id === id) input.checked = false;
            });
            logic.syncSelectedBahan();
        });

        elements.btnHapus?.addEventListener('click', () => {
            document.querySelectorAll('.bahan-item input').forEach(input => input.checked = false);
            state.selectedBahan = [];
            logic.syncSelectedBahan();
        });

        elements.btnApply?.addEventListener('click', () => {
            if (!state.isMobile) return;
            const ids = state.selectedBahan.map(item => item.id);
            window.location.href = `${API.filterUrl}?bahan=${ids.join(',')}`;
        });

        window.addEventListener('resize', () => {
            state.isMobile = window.innerWidth < CONFIG.mobileBreakpoint;
        });
    }

    document.addEventListener('DOMContentLoaded', async () => {
        initEvents();
        if (page === 'search') logic.syncSelectedBahan();
        if (page === 'filter') await logic.initFilterPage();
    });
})();