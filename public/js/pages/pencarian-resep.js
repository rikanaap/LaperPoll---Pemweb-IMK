(() => {
    const CONFIG = {
        mobileBreakpoint: 768,
        debounceDelay: 400,
    };

    const main = document.querySelector('main');
    if (!main) return;

    const page = main.dataset.page;
    const API = {
        searchUrl:     main.dataset.searchUrl,
        bahanUrl:      main.dataset.bahanUrl,
        filterUrl:     main.dataset.filterUrl,
        searchPageUrl: main.dataset.searchPageUrl,
        renderUrl:     main.dataset.renderUrl, 
    };

    let activeController = null;

    const state = {
        selectedBahan:  [],
        recipes:        [],
        pagination:     null,
        keywordSearch:  '',
        isMobile:       window.innerWidth < CONFIG.mobileBreakpoint,
    };

    // ─── Utilities ────────────────────────────────────────────────────────────

    const getElement = (...selectors) => {
        for (const selector of selectors) {
            const el = document.querySelector(selector);
            if (el) return el;
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

    // ─── DOM References ───────────────────────────────────────────────────────

    const elements = {
        searchInput:       getElement('#searchInput'),
        bahanList:         getElement('.bahan-list'),
        btnApply:          getElement('#terapkanBtn'),
        btnHapus:          getElement('#hapusSemuaBtn'),
        selectedInfo:      getElement('#selectedInfo'),
        chipsWrapper:      getElement('#selectedChips', '#chipsContainer'),
        resultInfoText:    getElement('#resultInfoText', '#resultInfo'),
        resepContainer:    getElement('#resepContainer', '#resepList'),
        resultPlaceholder: getElement('#resultPlaceholder', '#emptyState'),
        loadingState:      getElement('#loadingState'),
    };

    // ─── API Layer ────────────────────────────────────────────────────────────

    const api = {
        async fetchRecipes(pageNo = 1) {
            try {
                if (activeController) activeController.abort();
                activeController = new AbortController();

                ui.showLoading();

                const params = new URLSearchParams({ page: pageNo });

                if (state.selectedBahan.length > 0) {
                    params.append('bahan', state.selectedBahan.map(b => b.id).join(','));
                }

                if (state.keywordSearch.trim() !== '') {
                    params.append('q', state.keywordSearch);
                }

                const response = await fetch(`${API.searchUrl}?${params}`, {
                    signal: activeController.signal,
                });

                if (!response.ok) throw new Error('Failed to fetch recipes');

                const result = await response.json();
                state.recipes    = result.data        || [];
                state.pagination = result.pagination  || null;

                await ui.renderRecipes();
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
                if (!response.ok) throw new Error('Failed to fetch bahans');

                const result = await response.json();
                return result.data || [];
            } catch (error) {
                console.error(error);
                return [];
            }
        },

        async renderCardsFromServer(reseps = []) {
            try {
                const response = await fetch(API.renderUrl, {
                    method:  'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    },
                    body: JSON.stringify({ reseps }),
                });

                if (!response.ok) throw new Error('Failed to render cards');

                const result = await response.json();
                return result.html || '';
            } catch (error) {
                console.error(error);
                return '';
            }
        },
    };

    // ─── UI Layer ─────────────────────────────────────────────────────────────

    const ui = {
        updateItemState() {
            if (page !== 'search') return;
            document.querySelectorAll('.bahan-item').forEach(item => {
                const checkbox = item.querySelector('input');
                if (checkbox) item.classList.toggle('active', checkbox.checked);
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
                const isEmpty = total === 0 && state.keywordSearch.trim() === '';
                btn.disabled = isEmpty;
                btn.classList.toggle('disabled', isEmpty);
            });
        },

        renderChips() {
            if (!elements.chipsWrapper) return;
            elements.chipsWrapper.innerHTML = state.selectedBahan
                .map(bahan => `
                    <div class="chip">
                        <span>${bahan.nama}</span>
                        <span class="material-icons-round chip-close" data-id="${bahan.id}">close</span>
                    </div>
                `).join('');
        },

        renderResultText() {
            if (!elements.resultInfoText) return;

            if (!state.selectedBahan.length && !state.keywordSearch) {
                elements.resultInfoText.textContent = 'Pilih bahan atau ketik kata kunci untuk melihat resep';
                return;
            }

            let text = 'Menampilkan hasil pencarian';
            if (state.keywordSearch) text += ` resep "${state.keywordSearch}"`;
            if (state.selectedBahan.length) {
                text += ` berdasarkan bahan: ${state.selectedBahan.map(b => b.nama).join(', ')}`;
            }
            elements.resultInfoText.textContent = text;
        },

        async renderRecipes() {
            if (!elements.resepContainer) return;

            if (!state.recipes.length) {
                ui.showEmptyState('Resep tidak ditemukan');
                return;
            }

            // Render via SSR — Blade component di server
            const html = await api.renderCardsFromServer(state.recipes);

            elements.resultPlaceholder?.classList.add('hidden');
            elements.resepContainer.classList.remove('hidden');
            elements.resepContainer.innerHTML = html;
        },

        resetRecipes() {
            if (!elements.resepContainer) return;
            elements.resepContainer.innerHTML = '';
            elements.resepContainer.classList.add('hidden');
            elements.resultPlaceholder?.classList.remove('hidden');
        },

        showLoading()  { elements.loadingState?.classList.remove('hidden'); },
        hideLoading()  { elements.loadingState?.classList.add('hidden'); },

        showEmptyState(message) {
            ui.resetRecipes();
            if (!elements.resultPlaceholder) return;
            elements.resultPlaceholder.classList.remove('hidden');
            elements.resultPlaceholder.innerHTML = `
                <span class="material-icons-round">restaurant_menu</span>
                <h3>${message}</h3>
            `;
        },

        renderAll() {
            ui.updateItemState();
            ui.renderSelectedInfo();
            ui.renderChips();
            ui.renderResultText();
        },
    };

    // ─── Logic Layer ──────────────────────────────────────────────────────────

    const logic = {
        syncSelectedBahan() {
            if (page !== 'search') return;

            state.selectedBahan = Array.from(
                document.querySelectorAll('.bahan-item input:checked')
            ).map(input => ({
                id:   parseInt(input.dataset.id, 10),
                nama: input.dataset.nama,
            }));

            ui.renderAll();

            if (!state.isMobile) {
                const hasFilter = state.selectedBahan.length > 0 || state.keywordSearch.trim() !== '';
                hasFilter ? api.fetchRecipes() : ui.resetRecipes();
            }
        },

        async initFilterPage() {
            const params    = new URLSearchParams(window.location.search);
            const bahanParam = params.get('bahan');
            const queryText  = params.get('q');

            if (queryText) {
                state.keywordSearch = queryText;
                if (elements.searchInput) elements.searchInput.value = queryText;
            }

            if (bahanParam) {
                const ids = bahanParam.split(',').filter(Boolean);

                // Optimistic: tampilkan dulu dengan nama "Memuat..."
                state.selectedBahan = ids.map(id => ({ id: parseInt(id, 10), nama: 'Memuat...' }));
                ui.renderAll();

                const verified = await api.fetchBahansByIds(ids);
                if (verified?.length > 0) state.selectedBahan = verified;
            } else {
                state.selectedBahan = [];
            }

            ui.renderAll();
            await api.fetchRecipes();
        },

        handleSearch: debounce(event => {
            const keyword      = event.target.value.trim();
            const lowerKeyword = keyword.toLowerCase();
            state.keywordSearch = keyword;

            ui.renderAll();

            // Filter list bahan di sidebar secara lokal
            document.querySelectorAll('.bahan-item').forEach(item => {
                const nama = item.querySelector('.bahan-nama')?.textContent.toLowerCase() || '';
                item.classList.toggle('hidden', !nama.includes(lowerKeyword));
            });

            document.querySelectorAll('.bahan-group').forEach(group => {
                const hasVisible = group.querySelectorAll('.bahan-item:not(.hidden)').length > 0;
                group.classList.toggle('hidden', !hasVisible);
            });

            if (!state.isMobile) {
                const hasFilter = keyword.length > 0 || state.selectedBahan.length > 0;
                hasFilter ? api.fetchRecipes() : ui.resetRecipes();
            }
        }, CONFIG.debounceDelay),
    };

    const handlers = {
        onSearchInput(event) {
            logic.handleSearch(event);
        },

        onBahanClick(event) {
            const item = event.target.closest('.bahan-item');
            if (!item) return;

            const checkbox = item.querySelector('input');
            if (!event.target.matches('input')) {
                event.preventDefault();
                checkbox.checked = !checkbox.checked;
            }
            logic.syncSelectedBahan();
        },

        async onChipsClick(event) {
            const chipClose = event.target.closest('.chip-close');
            if (!chipClose) return;

            const id = chipClose.dataset.id;
            state.selectedBahan = state.selectedBahan.filter(b => b.id != id);

            if (page === 'filter') {
                if (!state.selectedBahan.length && !state.keywordSearch) {
                    window.location.href = API.searchPageUrl;
                    return;
                }

                const newParams = new URLSearchParams();
                const ids = state.selectedBahan.map(b => b.id);
                if (ids.length) newParams.append('bahan', ids.join(','));
                if (state.keywordSearch) newParams.append('q', state.keywordSearch);

                window.history.replaceState({}, '', `${API.filterUrl}?${newParams}`);
                ui.renderAll();
                await api.fetchRecipes();
                return;
            }

            // page === 'search': uncheck checkbox yang sesuai
            document.querySelectorAll('.bahan-item input').forEach(input => {
                if (input.dataset.id == id) input.checked = false;
            });
            logic.syncSelectedBahan();
        },

        onBtnHapusClick() {
            document.querySelectorAll('.bahan-item input').forEach(input => input.checked = false);
            state.selectedBahan = [];
            state.keywordSearch = '';
            if (elements.searchInput) elements.searchInput.value = '';

            document.querySelectorAll('.bahan-item, .bahan-group').forEach(el => {
                el.classList.remove('hidden');
            });

            logic.syncSelectedBahan();
            if (page === 'filter') window.location.href = API.searchPageUrl;
        },

        onBtnApplyClick() {
            if (!state.isMobile) return;

            const ids = state.selectedBahan.map(b => b.id);
            const redirectParams = new URLSearchParams();
            if (ids.length) redirectParams.append('bahan', ids.join(','));
            if (state.keywordSearch) redirectParams.append('q', state.keywordSearch);

            window.location.href = `${API.filterUrl}?${redirectParams}`;
        },

        onWindowResize() {
            const wasMobile = state.isMobile;
            state.isMobile = window.innerWidth < CONFIG.mobileBreakpoint;

            if (wasMobile && !state.isMobile) {
                const hasFilter = state.selectedBahan.length > 0 || state.keywordSearch.trim() !== '';
                if (hasFilter) api.fetchRecipes();
            }
        },
    };

    function initEvents() {
        elements.searchInput?.addEventListener('input', handlers.onSearchInput);
        elements.bahanList?.addEventListener('click', handlers.onBahanClick);
        elements.chipsWrapper?.addEventListener('click', handlers.onChipsClick);
        elements.btnHapus?.addEventListener('click', handlers.onBtnHapusClick);
        elements.btnApply?.addEventListener('click', handlers.onBtnApplyClick);
        window.addEventListener('resize', handlers.onWindowResize);
    }

    document.addEventListener('DOMContentLoaded', () => {
        initEvents();

        if (page === 'search') {
            logic.syncSelectedBahan();
        }

        if (page === 'filter') {
            setTimeout(() => logic.initFilterPage(), 50);
        }
    });
})();