document.addEventListener('DOMContentLoaded', () => {
    const el = {
        swipeCards:       document.getElementById('swipeCards'),
        likedContainer:   document.getElementById('likedContainer'),
        dislikedContainer:document.getElementById('dislikedContainer'),
        mobileLiked:      document.getElementById('mobileLikedContainer'),
        mobileDisliked:   document.getElementById('mobileDislikedContainer'),
        counterText:      document.getElementById('counterText'),
        progressBar:      document.getElementById('progressBar'),
        mobileProgressBar:document.getElementById('mobileProgressBar'),
        emptyState:       document.getElementById('emptyState'),
        likeBtn:          document.getElementById('likeBtn'),
        dislikeBtn:       document.getElementById('dislikeBtn'),
        historyDrawer:    document.getElementById('historyDrawer'),
        drawerHeader:     document.getElementById('drawerHeader'),
        drawerOverlay:    document.getElementById('drawerOverlay'),
        drawerArrow:      document.getElementById('drawerArrow'),
    };

    const MAX_LIKED       = 3;
    const SWIPE_THRESHOLD = 100;
    const SESSION_KEY     = 'swipeRasaState';
    const CARD_COLORS     = ['orange', 'rose', 'violet', 'teal', 'blue', 'amber'];

    const state = {
        cards:        [],
        disliked:     [],
        currentLiked: [],
        likedGroups:  [],
        drag: {
            active:   false,
            startX:   0,
            currentX: 0,
            card:     null,
            rasa:     null,
        },
        redirecting: false,
        drawerOpen:  false,
    };

    async function init() {
        loadState();
        await fetchRasa();
        renderCards();
        updateHistory();
        updateProgress();
        initDragListeners();
        initHistoryEvents();
        initDrawer();
        bindActionButtons();
    }

    function loadState() {
        try {
            const saved       = JSON.parse(sessionStorage.getItem(SESSION_KEY) || '{}');
            state.disliked    = saved.disliked    ?? [];
            state.likedGroups = saved.likedGroups ?? [];
        } catch {
            state.disliked    = [];
            state.likedGroups = [];
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

            const excluded = new Set(state.disliked.map(d => d.id));

            state.cards = result.data
                .filter(item => !excluded.has(item.id))
                .map(item => ({ ...item, colorClass: CARD_COLORS[item.id % CARD_COLORS.length] }));
        } catch {
            state.cards = [];
        }
    }

    function renderCards() {
        el.swipeCards.innerHTML = '';

        if (!state.cards.length) {
            el.emptyState.style.display = 'flex';
            el.swipeCards.appendChild(el.emptyState);

            if (state.currentLiked.length > 0 && !state.redirecting) {
                state.redirecting = true;
                redirect();
            }
            return;
        }

        el.emptyState.style.display = 'none';

        state.cards.slice(0, 3).forEach((rasa, index) => {
            const card           = document.createElement('div');
            card.className       = `swipe-card swipe-card--${rasa.colorClass}`;
            card.style.zIndex    = 100 - index;
            card.dataset.rasaId  = rasa.id;
            card.setAttribute('role', 'article');
            card.setAttribute('aria-label', `Rasa: ${rasa.title}`);
            card.style.cursor = 'grab';
            card.innerHTML = `
                <div class="swipe-card__icon-wrapper" aria-hidden="true">
                    <span class="material-icons-round">restaurant</span>
                </div>
                <h2 class="swipe-card__title">${escape(rasa.title ?? '-')}</h2>
                <p class="swipe-card__desc">${escape(rasa.description ?? '-')}</p>
            `;
            el.swipeCards.appendChild(card);
        });
    }

    function initDragListeners() {
        el.swipeCards.addEventListener('pointerdown', (e) => {
            if (state.redirecting || state.drag.active) return;

            const topCard = el.swipeCards.querySelector('.swipe-card');
            if (!topCard) return;

            const rasa = state.cards.find(c => c.id === Number(topCard.dataset.rasaId));
            if (!rasa) return;

            state.drag = { active: true, startX: e.clientX, currentX: 0, card: topCard, rasa };
            topCard.style.transition = 'none';
            topCard.setPointerCapture(e.pointerId);
        });

        el.swipeCards.addEventListener('pointermove', (e) => {
            if (!state.drag.active) return;

            const delta           = e.clientX - state.drag.startX;
            const clamped         = Math.max(-200, Math.min(200, delta));
            state.drag.currentX   = delta;
            state.drag.card.style.transform = `translateX(${clamped}px) rotate(${clamped / 18}deg)`;
        });

        el.swipeCards.addEventListener('pointerup', () => {
            if (!state.drag.active) return;

            const { card, rasa, currentX } = state.drag;
            state.drag = { active: false, startX: 0, currentX: 0, card: null, rasa: null };
            card.style.transition = '.3s ease';

            if (currentX > SWIPE_THRESHOLD)       animateSwipe(card, 'right', () => onLike(rasa));
            else if (currentX < -SWIPE_THRESHOLD) animateSwipe(card, 'left',  () => onDislike(rasa));
            else                                  card.style.transform = '';
        });

        el.swipeCards.addEventListener('pointercancel', () => {
            if (!state.drag.active) return;
            const { card } = state.drag;
            state.drag = { active: false, startX: 0, currentX: 0, card: null, rasa: null };
            if (card) { card.style.transition = '.3s ease'; card.style.transform = ''; }
        });

        // Blokir click event di area swipe agar tidak ada navigasi tidak sengaja
        el.swipeCards.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }

    function animateSwipe(card, direction, cb) {
        card.style.transform = `translateX(${direction === 'right' ? 480 : -480}px) rotate(${direction === 'right' ? 28 : -28}deg)`;
        card.style.opacity   = '0';
        setTimeout(cb, 220);
    }

    function onLike(rasa) {
        if (!rasa || state.redirecting) return;

        state.currentLiked.push(rasa);
        updateProgress();

        const done = state.currentLiked.length >= MAX_LIKED || state.cards.length === 1;

        removeCard(rasa.id); 

        if (done) {
            state.redirecting = true;
            state.likedGroups.unshift({ id: Date.now(), items: [...state.currentLiked] });
            saveState();
            updateHistory();
            setTimeout(redirect, 400);
        } else {
            updateHistory();
        }
    }

    function onDislike(rasa) {
        if (!rasa || state.redirecting) return;
        state.disliked.push(rasa);
        removeCard(rasa.id);
        saveState();
        updateHistory();
    }

    function removeCard(id) {
        state.cards = state.cards.filter(c => c.id !== id);
        renderCards();
    }

    function redirect() {
        const ids = state.currentLiked.map(r => r.id).join(',');
        window.location.href = `${window.swipeConfig.redirectUrl}?filters=${ids}`;
    }



    function bindActionButtons() {
        el.likeBtn?.addEventListener('click', () => {
            if (state.redirecting || state.drag.active) return;
            const rasa = state.cards[0];
            const card = el.swipeCards.querySelector('.swipe-card');
            if (rasa && card) animateSwipe(card, 'right', () => onLike(rasa));
        });

        el.dislikeBtn?.addEventListener('click', () => {
            if (state.redirecting || state.drag.active) return;
            const rasa = state.cards[0];
            const card = el.swipeCards.querySelector('.swipe-card');
            if (rasa && card) animateSwipe(card, 'left', () => onDislike(rasa));
        });
    }

    function updateHistory() {
        const liked    = buildLikedHtml();
        const disliked = buildDislikedHtml();
        [el.likedContainer, el.mobileLiked].forEach(el => el && (el.innerHTML = liked));
        [el.dislikedContainer, el.mobileDisliked].forEach(el => el && (el.innerHTML = disliked));
    }

    function buildLikedHtml() {
        if (!state.likedGroups.length) {
            return '<p class="history-section__empty">Belum ada history rasa</p>';
        }

        return state.likedGroups.map(group => {
            const names = group.items.map(i => escape(i.title)).join(' • ');
            return `<button class="history-chip history-chip--liked liked-group-chip" data-ids="${group.items.map(i => i.id).join(',')}" type="button" aria-label="Ulangi: ${names}">❤️ ${names}</button>`;
        }).join('');
    }

    function buildDislikedHtml() {
        if (!state.disliked.length) {
            return '<p class="history-section__empty">Belum ada rasa dilewati</p>';
        }

        return state.disliked.map(item => `
            <div class="history-chip history-chip--disliked">
                <span>❌ ${escape(item.title)}</span>
                <button class="history-chip__remove remove-disliked" data-id="${item.id}" type="button" aria-label="Kembalikan ${escape(item.title)}">×</button>
            </div>
        `).join('');
    }

    function initHistoryEvents() {
        document.addEventListener('click', (e) => {
            const chip = e.target.closest('.liked-group-chip');
            if (chip) {
                window.location.href = `${window.swipeConfig.redirectUrl}?filters=${chip.dataset.ids}`;
                return;
            }

            const btn = e.target.closest('.remove-disliked');
            if (btn && !state.redirecting) {
                const id   = Number(btn.dataset.id);
                const item = state.disliked.find(d => d.id === id);
                if (!item) return;
                state.disliked = state.disliked.filter(d => d.id !== id);
                state.cards.push({ ...item, colorClass: CARD_COLORS[item.id % CARD_COLORS.length] });
                saveState();
                updateHistory();
                renderCards();
            }
        });
    }

    function updateProgress() {
        const pct = Math.min((state.currentLiked.length / MAX_LIKED) * 100, 100);
        if (el.counterText)        el.counterText.textContent       = `${state.currentLiked.length} / ${MAX_LIKED}`;
        if (el.progressBar)        el.progressBar.style.width       = `${pct}%`;
        if (el.mobileProgressBar)  el.mobileProgressBar.style.width = `${pct}%`;
    }

    function initDrawer() {
        el.drawerHeader?.addEventListener('click', () => setDrawer(!state.drawerOpen));
        el.drawerOverlay?.addEventListener('click', () => setDrawer(false));
    }

    function setDrawer(open) {
        state.drawerOpen = open;
        el.historyDrawer?.classList.toggle('is-open', open);
        el.drawerOverlay?.classList.toggle('is-active', open);
        if (el.drawerArrow) el.drawerArrow.style.transform = open ? 'rotate(180deg)' : 'rotate(0deg)';
        document.body.style.overflow = open ? 'hidden' : '';
    }

    function escape(str) {
        if (str == null) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    init();
});