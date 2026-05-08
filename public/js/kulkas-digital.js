// kulkas-digital.js
document.addEventListener('DOMContentLoaded', () => {

    // ── FILTER CHIPS ──────────────────────────────────────────
    const chips = document.querySelectorAll('.kd-chip');
    let activeFilter = 'semua';

    chips.forEach(chip => {
        chip.addEventListener('click', () => {
            chips.forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            activeFilter = chip.dataset.filter;
            applyFilter();
        });
    });

    // ── SEARCH ────────────────────────────────────────────────
    document.getElementById('kdSearch')?.addEventListener('input', e => {
        applyFilter(e.target.value.trim().toLowerCase());
    });

    function applyFilter(q = '') {
        const cards = document.querySelectorAll('.kd-card');
        let visible = 0;

        cards.forEach(card => {
            const status = card.dataset.status;
            const nama   = card.dataset.nama || '';
            const matchFilter = activeFilter === 'semua' || status === activeFilter;
            const matchSearch = !q || nama.includes(q);

            if (matchFilter && matchSearch) {
                card.style.display = '';
                visible++;
            } else {
                card.style.display = 'none';
            }
        });

        // Tampilkan empty state kalau tidak ada hasil
        const emptyState = document.querySelector('.kd-empty');
        if (emptyState) return; // sudah ada dari server (0 data)

        let noResult = document.getElementById('kdNoResult');
        if (visible === 0) {
            if (!noResult) {
                noResult = document.createElement('div');
                noResult.id = 'kdNoResult';
                noResult.className = 'kd-empty';
                noResult.style.gridColumn = '1 / -1';
                noResult.innerHTML = `
                    <span class="material-icons-round kd-empty-icon">search_off</span>
                    <p class="font-jakarta font-semibold kd-empty-title">Tidak ditemukan</p>
                    <p class="font-jakarta font-regular kd-empty-sub">Coba kata kunci lain</p>
                `;
                document.getElementById('kdGrid').appendChild(noResult);
            }
            noResult.style.display = '';
        } else if (noResult) {
            noResult.style.display = 'none';
        }
    }

    // ── EXPAND / COLLAPSE CARD ────────────────────────────────
    document.getElementById('kdGrid')?.addEventListener('click', e => {
        const card = e.target.closest('.kd-card');
        if (!card) return;

        // Klik tombol hapus → jangan expand
        if (e.target.closest('form')) return;

        const isExpanded = card.classList.contains('expanded');

        if (isExpanded) {
            // Klik tombol close
            if (e.target.closest('.kd-close') || e.target.classList.contains('kd-close')) {
                collapseCard(card);
            }
        } else {
            // Collapse semua card lain dulu
            document.querySelectorAll('.kd-card.expanded').forEach(c => collapseCard(c));
            expandCard(card);
        }
    });

    function expandCard(card) {
        const collapsed = card.querySelector('.kd-collapsed');
        const expanded  = card.querySelector('.kd-expanded');
        if (!collapsed || !expanded) return;
        collapsed.style.display = 'none';
        expanded.style.display  = 'flex';
        card.classList.add('expanded');
    }

    function collapseCard(card) {
        const collapsed = card.querySelector('.kd-collapsed');
        const expanded  = card.querySelector('.kd-expanded');
        if (!collapsed || !expanded) return;
        collapsed.style.display = '';
        expanded.style.display  = 'none';
        card.classList.remove('expanded');
    }

    // Flash message auto-hide
    const flash = document.querySelector('.kd-flash');
    if (flash) setTimeout(() => flash.style.opacity = '0', 3000);
});