
(() => {
    const CONFIG = {
        mobileBreakpoint: 768,
        debounceDelay:    400,
    };

    const main = document.querySelector('main');
    if (!main) return;

    const page = main.dataset.page;

    const API = {
        searchUrl:     main.dataset.searchUrl,
        bahanUrl:      main.dataset.bahanUrl,
        filterUrl:     main.dataset.filterUrl,
        searchPageUrl: main.dataset.searchPageUrl,
    };

    let activeController = null;

    const state = {
        selectedBahan:  [],
        recipes:        [],
        pagination:     null,
        keywordSearch:  '',
        isMobile:       window.innerWidth < CONFIG.mobileBreakpoint,
    };

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
    const formatDuration = (timeStr) => {
        if (!timeStr || typeof timeStr !== 'string') return '-';

        const parts        = timeStr.replace(/\./g, ':').split(':');
        if (parts.length < 2) return timeStr;

        const totalMinutes = (parseInt(parts[0], 10) || 0) * 60
                           + (parseInt(parts[1], 10) || 0);

        if (totalMinutes === 0)   return '-';
        if (totalMinutes < 60)    return `${totalMinutes} Menit`;

        const hours   = Math.floor(totalMinutes / 60);
        const minutes = totalMinutes % 60;

        return minutes === 0
            ? `${hours} Jam`
            : `${hours} Jam ${minutes} Menit`;
    };

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

                if (!response.ok) throw new Error('Gagal mengambil data resep');

                const result    = await response.json();
                state.recipes   = result.data       || [];
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
            if (!ids.length) return [];

            try {
                const idsString  = ids.map(id => parseInt(id, 10)).join(',');
                const response   = await fetch(`${API.bahanUrl}?ids=${idsString}`);

                if (!response.ok) throw new Error('Gagal mengambil data bahan');

                const result = await response.json();
                return result.data || [];
            } catch (error) {
                console.error(error);
                return [];
            }
        },
    };

    // ─────────────────────────────────────────────
    // Templates
    // ─────────────────────────────────────────────
    const templates = {
        /**
         * Render card thumbnail — gambar atau placeholder icon.
         */
        _thumbnail(resep) {
            if (resep.thumbnail) {
                return `<img src="${resep.thumbnail}" alt="${resep.title}">`;
            }
            return `
                <div class="resep-banner-placeholder">
                    <span class="material-icons-round">restaurant</span>
                </div>`;
        },

        _cardMiddle(resep) {
            const isByBahan = resep.search_by_bahan === true && resep.total_bahan_count > 0;

            if (isByBahan) {
                return templates._matchSection(resep);
            }

            return `
                <div class="resep-preview-info" style="margin-top:12px;margin-bottom:8px;min-height:40px;">
                    <p class="preview-text" style="font-size:12px;color:#64748b;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                        ${resep.description || 'Yuk, intip resep lengkap dan cara mudah membuatnya di dapur lo!'}
                    </p>
                </div>`;
        },

        /**
         * Render match section (persentase kecocokan bahan).
         */
        _matchSection(resep) {
            const matchStatusHtml = resep.match_percentage >= 80
                ? `<div class="match-status excellent">Sangat Cocok</div>`
                : resep.match_percentage >= 50
                    ? `<div class="match-status good">Cocok</div>`
                    : `<div class="match-status low">Kurang Cocok</div>`;

            const missingHtml = resep.missing_bahans?.length
                ? `<div class="missing-section">
                       <div class="missing-label">
                           <span class="material-icons-round">kitchen</span>
                           <span>Bahan belum tersedia</span>
                       </div>
                       <div class="missing-chips">
                           ${resep.missing_bahans.map(item => `<div class="missing-chip">${item.nama}</div>`).join('')}
                       </div>
                   </div>`
                : `<div class="perfect-match">
                       <span class="material-icons-round">verified</span>
                       <span>Bahan lengkap! 🎉</span>
                   </div>`;

            return `
                <div class="match-wrapper">
                    <div class="match-header">
                        <div class="match-percent">
                            <div class="match-percent-circle">${resep.match_percentage}%</div>
                            <div class="match-percent-info">
                                <h4>Kecocokan</h4>
                                <p>${resep.matched_bahan_count} / ${resep.total_bahan_count} bahan terpenuhi</p>
                            </div>
                        </div>
                        ${matchStatusHtml}
                    </div>
                    ${missingHtml}
                </div>`;
        },

        /**
         * Render satu card resep lengkap.
         */
        recipeCard(resep) {
            return `
                <div class="resep">
                    <div class="resep-banner">
                        ${templates._thumbnail(resep)}
                    </div>
                    <div class="resep-container-bottom">
                        <div class="resep-content">
                            <div class="resep-detail">
                                <div class="resep-top-header">
                                    <h1 class="resep-title">${resep.title}</h1>
                                    <span class="material-icons-round resep-arrow">chevron_right</span>
                                </div>
                                <div class="resep-meta-list">
                                    <div class="meta-item">
                                        <span class="material-icons-round">schedule</span>
                                        <p>${formatDuration(resep.cook_duration)}</p>
                                    </div>
                                    <div class="meta-item">
                                        <span class="material-icons-round">star</span>
                                        <p>${Number(resep.rating).toFixed(1)}</p>
                                    </div>
                                    <div class="meta-item views">
                                        <span class="material-icons-round">visibility</span>
                                        <p>${resep.views || 0}</p>
                                    </div>
                                </div>
                                ${templates._cardMiddle(resep)}
                                <div class="resep-verified">
                                    <span class="material-icons-round">account_circle</span>
                                    <p class="user-name">${resep.author?.name || 'User'}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
        },
    };

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

            const isFormEmpty = total === 0 && state.keywordSearch.trim() === '';
            [elements.btnApply, elements.btnHapus].forEach(btn => {
                if (!btn) return;
                btn.disabled = isFormEmpty;
                btn.classList.toggle('disabled', isFormEmpty);
            });
        },

        renderChips() {
            if (!elements.chipsWrapper) return;

            elements.chipsWrapper.innerHTML = state.selectedBahan
                .map(bahan => `
                    <div class="chip">
                        <span>${bahan.nama}</span>
                        <span class="material-icons-round chip-close" data-id="${bahan.id}">close</span>
                    </div>`)
                .join('');
        },

        renderResultText() {
            if (!elements.resultInfoText) return;

            if (!state.selectedBahan.length && !state.keywordSearch) {
                elements.resultInfoText.textContent = 'Pilih bahan atau ketik kata kunci untuk melihat resep';
                return;
            }

            let text = 'Menampilkan hasil pencarian';

            if (state.keywordSearch) {
                text += ` resep "${state.keywordSearch}"`;
            }

            if (state.selectedBahan.length) {
                const names = state.selectedBahan.map(b => b.nama).join(', ');
                text += ` berdasarkan bahan: ${names}`;
            }

            elements.resultInfoText.textContent = text;
        },

        renderRecipes() {
            if (!elements.resepContainer) return;

            if (!state.recipes.length) {
                ui.showEmptyState('Resep tidak ditemukan');
                return;
            }

            elements.resultPlaceholder?.classList.add('hidden');
            elements.resepContainer.classList.remove('hidden');
            elements.resepContainer.innerHTML = state.recipes
                .map(templates.recipeCard)
                .join('');
        },

        resetRecipes() {
            if (!elements.resepContainer) return;
            elements.resepContainer.innerHTML = '';
            elements.resepContainer.classList.add('hidden');
            elements.resultPlaceholder?.classList.remove('hidden');
        },

        showLoading() {
            // Sembunyikan placeholder & result dulu sebelum loading tampil
            elements.resultPlaceholder?.classList.add('hidden');
            elements.resepContainer?.classList.add('hidden');
            elements.loadingState?.classList.remove('hidden');
        },

        hideLoading() {
            elements.loadingState?.classList.add('hidden');
        },

        showEmptyState(message) {
            ui.resetRecipes();
            if (!elements.resultPlaceholder) return;
            elements.resultPlaceholder.classList.remove('hidden');
            elements.resultPlaceholder.innerHTML = `
                <span class="material-icons-round">restaurant_menu</span>
                <h3>${message}</h3>`;
        },

        renderAll() {
            ui.updateItemState();
            ui.renderSelectedInfo();
            ui.renderChips();
            ui.renderResultText();
        },
    };

    const logic = {
        syncSelectedBahan() {
            if (page !== 'search') return;

            state.selectedBahan = Array
                .from(document.querySelectorAll('.bahan-item input:checked'))
                .map(input => ({
                    id:   parseInt(input.dataset.id, 10),
                    nama: input.dataset.nama,
                }));

            ui.renderAll();

            if (!state.isMobile) {
                const hasFilter = state.selectedBahan.length || state.keywordSearch.trim() !== '';
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

                // Tampilkan "Memuat..." sementara fetch berjalan
                state.selectedBahan = ids.map(id => ({ id: parseInt(id, 10), nama: 'Memuat...' }));
                ui.renderAll();

                const verifiedBahans = await api.fetchBahansByIds(ids);

                if (verifiedBahans.length > 0) {
                    state.selectedBahan = verifiedBahans;
                }
            } else {
                state.selectedBahan = [];
            }

            ui.renderAll();
            await api.fetchRecipes();
        },

        handleSearch: debounce((event) => {
            const keyword      = event.target.value.trim();
            const lowerKeyword = keyword.toLowerCase();

            state.keywordSearch = keyword;
            ui.renderAll();

            // Filter bahan-item secara lokal
            const bahanItems = document.querySelectorAll('.bahan-item');
            if (bahanItems.length > 0) {
                bahanItems.forEach(item => {
                    const name = item.querySelector('.bahan-nama')?.textContent.toLowerCase() || '';
                    item.classList.toggle('hidden', !name.includes(lowerKeyword));
                });

                document.querySelectorAll('.bahan-group').forEach(group => {
                    const hasVisible = group.querySelectorAll('.bahan-item:not(.hidden)').length > 0;
                    group.classList.toggle('hidden', !hasVisible);
                });
            }

            if (!state.isMobile) {
                const hasFilter = keyword.length > 0 || state.selectedBahan.length > 0;
                hasFilter ? api.fetchRecipes() : ui.resetRecipes();
            }
        }, CONFIG.debounceDelay),
    };

    // ─────────────────────────────────────────────
    // Event Handlers
    // ─────────────────────────────────────────────
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

            const removedId      = chipClose.dataset.id;
            state.selectedBahan  = state.selectedBahan.filter(item => item.id != removedId);

            if (page === 'filter') {
                const hasFilter = state.selectedBahan.length || state.keywordSearch;

                if (!hasFilter) {
                    window.location.href = API.searchPageUrl;
                    return;
                }

                const newParams = new URLSearchParams();
                const ids       = state.selectedBahan.map(item => item.id);

                if (ids.length)          newParams.append('bahan', ids.join(','));
                if (state.keywordSearch) newParams.append('q', state.keywordSearch);

                window.history.replaceState({}, '', `${API.filterUrl}?${newParams}`);
                ui.renderAll();
                await api.fetchRecipes();
                return;
            }

            // Page = search: uncheck checkbox yang sesuai
            document.querySelectorAll('.bahan-item input').forEach(input => {
                if (input.dataset.id == removedId) input.checked = false;
            });

            logic.syncSelectedBahan();
        },

        onBtnHapusClick() {
            document.querySelectorAll('.bahan-item input').forEach(input => {
                input.checked = false;
            });

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

            const redirectParams = new URLSearchParams();
            const ids = state.selectedBahan.map(item => item.id);

            if (ids.length)          redirectParams.append('bahan', ids.join(','));
            if (state.keywordSearch) redirectParams.append('q', state.keywordSearch);

            window.location.href = `${API.filterUrl}?${redirectParams}`;
        },

        onWindowResize() {
            const wasMobile  = state.isMobile;
            state.isMobile   = window.innerWidth < CONFIG.mobileBreakpoint;

            const switchToDesktop = wasMobile && !state.isMobile;
            const hasFilter       = state.selectedBahan.length || state.keywordSearch.trim() !== '';

            if (switchToDesktop && hasFilter) {
                api.fetchRecipes();
            }
        },
    };

    function initEvents() {
        elements.searchInput?.addEventListener('input',  handlers.onSearchInput);
        elements.bahanList?.addEventListener('click',    handlers.onBahanClick);
        elements.chipsWrapper?.addEventListener('click', handlers.onChipsClick);
        elements.btnHapus?.addEventListener('click',     handlers.onBtnHapusClick);
        elements.btnApply?.addEventListener('click',     handlers.onBtnApplyClick);
        window.addEventListener('resize',                handlers.onWindowResize);
    }

    document.addEventListener('DOMContentLoaded', () => {
        initEvents();

        if (page === 'search') {
            logic.syncSelectedBahan();
        }

        if (page === 'filter') {
            setTimeout(async () => {
                await logic.initFilterPage();
            }, 50);
        }
    });
})();