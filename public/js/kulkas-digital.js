// kulkas-digital.js

document.addEventListener('DOMContentLoaded', () => {

    const tabs  = document.querySelectorAll('.filter-tab');
    const cards = document.querySelectorAll('.bahan-card');

    // ── FILTER TABS ──────────────────────────────────────────
    function applyFilter(filter) {
        const q = document.getElementById('searchKulkas')?.value.trim().toLowerCase() || '';
        cards.forEach(card => {
            const statusOk = filter === 'semua' || card.dataset.status === filter;
            const searchOk = q === '' || (card.dataset.nama || '').includes(q);
            card.style.display = (statusOk && searchOk) ? 'flex' : 'none';
        });
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => { t.classList.remove('active'); t.setAttribute('aria-selected', 'false'); });
            tab.classList.add('active');
            tab.setAttribute('aria-selected', 'true');
            applyFilter(tab.dataset.filter);
        });
    });

    // ── SEARCH ───────────────────────────────────────────────
    document.getElementById('searchKulkas')?.addEventListener('input', () => {
        const activeFilter = document.querySelector('.filter-tab.active')?.dataset.filter || 'semua';
        applyFilter(activeFilter);
    });

    // ── EXPAND CARD ──────────────────────────────────────────
    document.getElementById('bahanGrid')?.addEventListener('click', e => {
        const card = e.target.closest('.bahan-card');
        if (!card) return;
        if (e.target.closest('.hapus-btn') || e.target.closest('.hapus-form')) return;

        const isExpanded = card.classList.contains('expanded');
        document.querySelectorAll('.bahan-card.expanded').forEach(c => collapse(c));
        if (!isExpanded) expand(card);
    });

    document.addEventListener('click', e => {
        if (!e.target.closest('.bahan-card')) {
            document.querySelectorAll('.bahan-card.expanded').forEach(c => collapse(c));
        }
    });

    function expand(card) {
        card.classList.add('expanded');
        card.querySelector('.card-collapsed').style.display = 'none';
        card.querySelector('.card-expanded').style.display  = 'flex';
    }

    function collapse(card) {
        card.classList.remove('expanded');
        card.querySelector('.card-collapsed').style.display = 'flex';
        card.querySelector('.card-expanded').style.display  = 'none';
    }

    // ── FLASH AUTO HIDE ──────────────────────────────────────
    const flash = document.getElementById('flashMsg');
    if (flash) {
        setTimeout(() => {
            flash.style.transition = 'opacity 0.5s';
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 500);
        }, 3000);
    }
});