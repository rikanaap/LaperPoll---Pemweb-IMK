document.addEventListener('DOMContentLoaded', () => {
    const el = {
        swipeCards:          document.getElementById('swipeCards'),
        likedContainer:      document.getElementById('likedContainer'),
        dislikedContainer:   document.getElementById('dislikedContainer'),
        mobileLiked:         document.getElementById('mobileLikedContainer'),
        mobileDisliked:      document.getElementById('mobileDislikedContainer'),
        counterText:         document.getElementById('counterText'),
        progressBar:         document.getElementById('progressBar'),
        mobileProgressBar:   document.getElementById('mobileProgressBar'),
        emptyState:          document.getElementById('emptyState'),
        likeBtn:             document.getElementById('likeBtn'),
        dislikeBtn:          document.getElementById('dislikeBtn'),
        historyDrawer:       document.getElementById('historyDrawer'),
        drawerHeader:        document.getElementById('drawerHeader'),
        drawerOverlay:       document.getElementById('drawerOverlay'),
        drawerArrow:         document.getElementById('drawerArrow'),
    };

    const MAX_LIKED       = 3;
    const SWIPE_THRESHOLD = 120;
    const SESSION_KEY    = 'swipeRasaState';
    const CARD_COLORS     = ['orange', 'rose', 'violet', 'teal', 'blue', 'amber'];

    const state = {
        cards:        [],
        disliked:     [],
        currentLiked: [],
        likedGroups:  [],
        redirecting:  false,
        drawerOpen:   false,
    };

    init();

    async function init() {
        loadState();
        await fetchRasa();
        renderCards();
        updateHistory();
        updateProgress();
        initDrawer();
        bindActionButtons();
    }

    function loadState() {
        try {
            const saved = JSON.parse(sessionStorage.getItem(SESSION_KEY) || '{}');
            state.disliked     = saved.disliked     ?? [];
            state.likedGroups  = saved.likedGroups  ?? [];
        } catch {
            // State tetap bersih jika parse gagal
        }
    }

    function saveState() {
        sessionStorage.setItem(SESSION_KEY, JSON.stringify({
            disliked:    state.disliked,
            likedGroups: state.likedGroups,
        }));
    }

    async function fetchRasa() {
        try {
            const res    = await fetch(window.swipeConfig.apiUrl);
            const result = await res.json();

            if (!result.success) return;

            const excludedIds = new Set(state.disliked.map(item => item.id));

            state.cards = result.data
                .filter(item => !excludedIds.has(item.id))
                .map(item => ({
                    ...item,
                    colorClass: pickColor(item.id),
                }));
        } catch (err) {
            console.error('[SwipeResep] fetchRasa error:', err);
        }
    }

    function pickColor(id) {
        return CARD_COLORS[id % CARD_COLORS.length];
    }

    function renderCards() {
        el.swipeCards.innerHTML = '';

        if (!state.cards.length) {
            el.emptyState.style.display = 'flex';
            el.swipeCards.appendChild(el.emptyState);

            if (state.currentLiked.length > 0 && !state.redirecting) {
                state.redirecting = true;
                navigateToFilterPage();
            }
            return;
        }

        el.emptyState.style.display = 'none';

        state.cards.slice(0, 3).forEach((rasa, index) => {
            const card = buildCardElement(rasa, index);
            el.swipeCards.appendChild(card);
        });
    }

    function buildCardElement(rasa, index) {
        const card = document.createElement('div');
        card.className = `swipe-card swipe-card--${rasa.colorClass}`;
        card.style.zIndex = 100 - index;
        card.setAttribute('role', 'article');
        card.setAttribute('aria-label', `Rasa: ${rasa.title}`);
        card.innerHTML = `
            <div class="swipe-card__icon-wrapper">
                <span class="material-icons-round">restaurant</span>
            </div>
            <h2 class="swipe-card__title">${escapeHtml(rasa.title ?? '-')}</h2>
            <p class="swipe-card__desc">${escapeHtml(rasa.description ?? '-')}</p>
        `;
        attachSwipeEvents(card, rasa);
        return card;
    }

    function attachSwipeEvents(card, rasa) {
        let startX   = 0;
        let currentX = 0;
        let dragging = false;

        const isTopCard = () => card === el.swipeCards.querySelector('.swipe-card:first-child');

        card.addEventListener('pointerdown', (e) => {
            if (!isTopCard() || state.redirecting) return;
            startX   = e.clientX;
            dragging = true;
            card.style.transition = 'none';
        });

        window.addEventListener('pointermove', (e) => {
            if (!dragging) return;
            currentX = e.clientX - startX;
            const clamped = Math.max(-180, Math.min(180, currentX));
            card.style.transform = `translateX(${clamped}px) rotate(${clamped / 18}deg)`;
        });

        window.addEventListener('pointerup', () => {
            if (!dragging) return;
            dragging = false;
            card.style.transition = '.3s ease';

            if (currentX > SWIPE_THRESHOLD) {
                animateSwipe(card, 'right', () => onLike(rasa));
            } else if (currentX < -SWIPE_THRESHOLD) {
                animateSwipe(card, 'left', () => onDislike(rasa));
            } else {
                card.style.transform = '';
            }
            currentX = 0;
        });
    }

    function animateSwipe(card, direction, callback) {
        const x      = direction === 'right' ? 420 : -420;
        const rotate = direction === 'right' ? 25 : -25;
        card.style.transform = `translateX(${x}px) rotate(${rotate}deg)`;
        card.style.opacity   = '0';
        setTimeout(callback, 200);
    }

    function onLike(rasa) {
        if (!rasa || state.redirecting) return;

        state.currentLiked.push(rasa);
        updateProgress();
        
        const shouldRedirect = state.currentLiked.length >= MAX_LIKED || state.cards.length === 1;

        if (shouldRedirect) {
            state.redirecting = true;
            const group = { id: Date.now(), items: [...state.currentLiked] };
            state.likedGroups.unshift(group);
            saveState();
            
            removeCardFromState(rasa.id);
            updateHistory();
            
            setTimeout(navigateToFilterPage, 500);
        } else {
            removeCardFromState(rasa.id);
            updateHistory();
        }
    }

    function onDislike(rasa) {
        if (!rasa || state.redirecting) return;
        state.disliked.push(rasa);
        removeCardFromState(rasa.id);
        saveState();
        updateHistory();
    }

    function removeCardFromState(id) {
        state.cards = state.cards.filter(item => item.id !== id);
        renderCards();
    }

    function navigateToFilterPage() {
        sessionStorage.setItem('selectedRasa', JSON.stringify(state.currentLiked));
        const ids = state.currentLiked.map(r => r.id).join(',');
        window.location.href = `${window.swipeConfig.redirectUrl}?filters=${ids}`;
    }

    function bindActionButtons() {
        el.likeBtn?.addEventListener('click', () => {
            if (state.redirecting) return;
            const rasa = state.cards[0];
            const card = el.swipeCards.querySelector('.swipe-card:first-child');
            if (rasa && card) animateSwipe(card, 'right', () => onLike(rasa));
        });

        el.dislikeBtn?.addEventListener('click', () => {
            if (state.redirecting) return;
            const rasa = state.cards[0];
            const card = el.swipeCards.querySelector('.swipe-card:first-child');
            if (rasa && card) animateSwipe(card, 'left', () => onDislike(rasa));
        });
    }

    /* ------------------------------------------------------------------ */
    /* 12. UPDATE HISTORY & CHIPS                                         */
    /* ------------------------------------------------------------------ */
    function updateHistory() {
        const likedHtml    = buildLikedHtml();
        const dislikedHtml = buildDislikedHtml();

        if (el.likedContainer)    el.likedContainer.innerHTML    = likedHtml;
        if (el.dislikedContainer) el.dislikedContainer.innerHTML = dislikedHtml;
        if (el.mobileLiked)       el.mobileLiked.innerHTML       = likedHtml;
        if (el.mobileDisliked)    el.mobileDisliked.innerHTML    = dislikedHtml;

        bindHistoryChipEvents();
    }

    function buildLikedHtml() {
        if (!state.likedGroups.length) {
            return '<p class="history-section__empty">Belum ada history rasa</p>';
        }
        return state.likedGroups.map(group => {
            const names = group.items.map(item => escapeHtml(item.title)).join(' • ');
            return `
                <button
                    class="history-chip history-chip--liked liked-group-chip"
                    data-ids="${group.items.map(i => i.id).join(',')}"
                    aria-label="Ulangi pilihan rasa: ${names}"
                >
                    ❤️ ${names}
                </button>
            `;
        }).join('');
    }

    function buildDislikedHtml() {
        if (!state.disliked.length) {
            return '<p class="history-section__empty">Belum ada rasa dilewati</p>';
        }
        return state.disliked.map(item => `
            <div class="history-chip history-chip--disliked">
                <span>❌ ${escapeHtml(item.title)}</span>
                <button
                    class="history-chip__remove remove-disliked"
                    data-id="${item.id}"
                    aria-label="Kembalikan rasa ${escapeHtml(item.title)}"
                >×</button>
            </div>
        `).join('');
    }

    function bindHistoryChipEvents() {
        document.querySelectorAll('.liked-group-chip').forEach(chip => {
            chip.addEventListener('click', () => {
                const ids = chip.dataset.ids;
                window.location.href = `${window.swipeConfig.redirectUrl}?filters=${ids}`;
            });
        });

        document.querySelectorAll('.remove-disliked').forEach(btn => {
            btn.addEventListener('click', () => {
                if (state.redirecting) return;
                restoreDisliked(Number(btn.dataset.id));
            });
        });
    }

    function restoreDisliked(id) {
        const item = state.disliked.find(i => i.id === id);
        if (!item) return;
        state.disliked = state.disliked.filter(i => i.id !== id);
        state.cards.unshift(item);
        saveState();
        updateHistory();
        renderCards();
    }

    function updateProgress() {
        const total   = state.currentLiked.length;
        const percent = (total / MAX_LIKED) * 100;

        if (el.counterText) el.counterText.innerText = `${total} / ${MAX_LIKED}`;
        if (el.progressBar) el.progressBar.style.width = `${percent}%`;
        if (el.mobileProgressBar) el.mobileProgressBar.style.width = `${percent}%`;
    }

    function initDrawer() {
        el.drawerHeader?.addEventListener('click', toggleDrawer);
        el.drawerOverlay?.addEventListener('click', closeDrawer);
    }

    function toggleDrawer() {
        state.drawerOpen = !state.drawerOpen;
        el.historyDrawer?.classList.toggle('is-open', state.drawerOpen);
        el.drawerOverlay?.classList.toggle('is-active', state.drawerOpen);
        if (el.drawerArrow) {
            el.drawerArrow.style.transform = state.drawerOpen ? 'rotate(180deg)' : 'rotate(0deg)';
        }
        document.body.style.overflow = state.drawerOpen ? 'hidden' : '';
    }

    function closeDrawer() {
        state.drawerOpen = false;
        el.historyDrawer?.classList.remove('is-open');
        el.drawerOverlay?.classList.remove('is-active');
        if (el.drawerArrow) el.drawerArrow.style.transform = 'rotate(0deg)';
        document.body.style.overflow = '';
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
});

document.addEventListener('click', function (e) {
    const card = e.target.closest('[data-detail-url]');
    if (card) {
        window.location.href = card.dataset.detailUrl;
    }
});