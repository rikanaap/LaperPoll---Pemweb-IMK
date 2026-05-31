// favorit.js — LaperPoll

// ─── HAPUS FAVORIT ────────────────────────────────────────────────────────────
document.querySelectorAll('.fav-remove-btn').forEach(btn => {
    btn.addEventListener('click', async (e) => {
        e.preventDefault();
        e.stopPropagation();

        const resepId = btn.dataset.resepId;
        const card    = btn.closest('.fav-card-link');

        btn.classList.add('removing');
        btn.disabled = true;

        try {
            const res  = await fetch(`${TOGGLE_BASE_URL}/${resepId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
            });
            const data = await res.json();

            if (!data.isFavorite) {
                card.style.transition = 'opacity 0.3s, transform 0.3s';
                card.style.opacity    = '0';
                card.style.transform  = 'scale(0.9)';
                setTimeout(() => {
                    card.remove();
                    updateCount();
                    runFilter(); // refresh search setelah hapus
                }, 300);
            } else {
                btn.classList.remove('removing');
                btn.disabled = false;
            }
        } catch (err) {
            console.error('Gagal hapus favorit:', err);
            btn.classList.remove('removing');
            btn.disabled = false;
        }
    });
});

function updateCount() {
    const remaining = document.querySelectorAll('.fav-card-link').length;
    const subEl     = document.getElementById('favCount');
    if (subEl) subEl.textContent = `${remaining} resep tersimpan`;

    if (remaining === 0) {
        const grid = document.getElementById('favGrid');
        const toolbar = document.querySelector('.fav-toolbar');
        if (toolbar) toolbar.style.display = 'none';
        if (grid) {
            grid.outerHTML = `
                <div class="fav-empty">
                    <span class="material-icons-round fav-empty-icon">favorite_border</span>
                    <p class="fav-empty-title font-semibold">Belum ada resep favorit</p>
                    <p class="fav-empty-sub">Ketuk ikon hati di detail resep untuk menyimpannya.</p>
                    <a href="/" class="fav-empty-btn font-semibold">
                        <span class="material-icons-round">explore</span>
                        Jelajahi Resep
                    </a>
                </div>`;
        }
    }
}

// ─── SEARCH ───────────────────────────────────────────────────────────────────
const searchInput = document.getElementById('favSearch');
const searchClear = document.getElementById('favSearchClear');
const noResult    = document.getElementById('favNoResult');

searchInput?.addEventListener('input', () => {
    searchClear.style.display = searchInput.value ? 'flex' : 'none';
    runFilter();
});

searchClear?.addEventListener('click', () => {
    searchInput.value          = '';
    searchClear.style.display  = 'none';
    runFilter();
});

// ─── SORT ─────────────────────────────────────────────────────────────────────
const sortToggle  = document.getElementById('favSortToggle');
const sortLabel   = document.getElementById('favSortLabel');
const sortOptions = document.querySelectorAll('.fav-sort-option');
let currentSort   = 'newest';

sortToggle?.addEventListener('click', (e) => {
    e.stopPropagation();
    sortToggle.classList.toggle('open');
});

document.addEventListener('click', () => {
    sortToggle?.classList.remove('open');
});

sortOptions.forEach(opt => {
    opt.addEventListener('click', (e) => {
        e.stopPropagation();
        currentSort    = opt.dataset.sort;
        sortLabel.textContent = opt.textContent;
        sortOptions.forEach(o => o.classList.remove('active'));
        opt.classList.add('active');
        sortToggle.classList.remove('open');
        runFilter();
    });
});

// ─── FILTER + SORT LOGIC ──────────────────────────────────────────────────────
function runFilter() {
    const query = (searchInput?.value || '').toLowerCase().trim();
    const grid  = document.getElementById('favGrid');
    if (!grid) return;

    const cards = Array.from(grid.querySelectorAll('.fav-card-link'));
    let visible = 0;

    // Filter by search
    cards.forEach(card => {
        const title  = card.dataset.title  || '';
        const author = card.dataset.author || '';
        const match  = !query || title.includes(query) || author.includes(query);
        card.style.display = match ? '' : 'none';
        if (match) visible++;
    });

    // Sort visible cards
    const visibleCards = cards.filter(c => c.style.display !== 'none');
    visibleCards.sort((a, b) => {
        switch (currentSort) {
            case 'newest':  return new Date(b.dataset.date) - new Date(a.dataset.date);
            case 'oldest':  return new Date(a.dataset.date) - new Date(b.dataset.date);
            case 'rating':  return parseFloat(b.dataset.rating) - parseFloat(a.dataset.rating);
            case 'name':    return a.dataset.title.localeCompare(b.dataset.title, 'id');
            default:        return 0;
        }
    });

    // Re-append dalam urutan baru
    visibleCards.forEach(card => grid.appendChild(card));

    // No result state
    if (noResult) noResult.style.display = visible === 0 && query ? 'flex' : 'none';
}