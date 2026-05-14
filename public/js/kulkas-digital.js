// kulkas-digital.js
document.addEventListener('DOMContentLoaded', () => {

    // ── CHIP COUNTER ─────────────────────────────────────────────────────
    function updateChipCounts(query = '') {
        const cards = document.querySelectorAll('.kd-card');
        const counts = { semua: 0, tersedia: 0, 'hampir-habis': 0 };

        cards.forEach(card => {
            const status = card.dataset.status;
            const nama   = card.dataset.nama || '';
            if (query && !nama.includes(query)) return;
            counts['semua']++;
            if (counts[status] !== undefined) counts[status]++;
        });

        document.querySelectorAll('.kd-chip').forEach(chip => {
            const filter = chip.dataset.filter;
            let countEl  = chip.querySelector('.kd-chip-count');
            if (!countEl) {
                countEl = document.createElement('span');
                countEl.className = 'kd-chip-count';
                chip.appendChild(countEl);
            }
            countEl.textContent = counts[filter] ?? 0;
        });
    }

    // ── FILTER CHIPS ──────────────────────────────────────────────────────
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

    // ── SEARCH ────────────────────────────────────────────────────────────
    document.getElementById('kdSearch')?.addEventListener('input', e => {
        applyFilter(e.target.value.trim().toLowerCase());
    });

    function applyFilter(q = '') {
        const cards = document.querySelectorAll('.kd-card');
        let visible = 0;

        cards.forEach(card => {
            const matchFilter = activeFilter === 'semua' || card.dataset.status === activeFilter;
            const matchSearch = !q || (card.dataset.nama || '').includes(q);

            card.style.display = (matchFilter && matchSearch) ? '' : 'none';
            if (matchFilter && matchSearch) visible++;
        });

        updateChipCounts(q);

        // Empty state saat search tidak ketemu
        const emptyState = document.querySelector('.kd-empty');
        if (emptyState) return;

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

    // ── EXPAND / COLLAPSE CARD ────────────────────────────────────────────
    document.getElementById('kdGrid')?.addEventListener('click', e => {
        const card = e.target.closest('.kd-card');
        if (!card) return;
        if (e.target.closest('form')) return;

        if (card.classList.contains('expanded')) {
            if (e.target.closest('.kd-close') || e.target.classList.contains('kd-close')) {
                collapseCard(card);
            }
        } else {
            document.querySelectorAll('.kd-card.expanded').forEach(c => collapseCard(c));
            expandCard(card);
        }
    });

    function expandCard(card) {
        card.querySelector('.kd-collapsed').style.display = 'none';
        card.querySelector('.kd-expanded').style.display  = 'flex';
        card.classList.add('expanded');
    }

    function collapseCard(card) {
        card.querySelector('.kd-collapsed').style.display = '';
        card.querySelector('.kd-expanded').style.display  = 'none';
        card.classList.remove('expanded');
    }

    // ── MODAL BAHAN KURANG ────────────────────────────────────────────────
    const modalKurang      = document.getElementById('modalBahanKurang');
    const modalKurangTitle = document.getElementById('modalKurangTitle');
    const modalKurangList  = document.getElementById('modalKurangList');
    const modalKurangClose = document.getElementById('modalKurangClose');
    const modalKurangOverlay = document.getElementById('modalKurangOverlay');

    document.querySelectorAll('.kd-resep-detail-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const nama   = btn.dataset.nama;
            const kurang = btn.dataset.kurang;

            modalKurangTitle.textContent = nama;
            modalKurangList.innerHTML = '';

            kurang.split(', ').forEach(bahan => {
                const li = document.createElement('li');
                li.textContent = bahan.trim();
                modalKurangList.appendChild(li);
            });

            modalKurang.style.display = 'flex';
        });
    });

    function closeModalKurang() {
        if (modalKurang) modalKurang.style.display = 'none';
    }

    modalKurangClose?.addEventListener('click', closeModalKurang);
    modalKurangOverlay?.addEventListener('click', closeModalKurang);

    // ── FLASH AUTO HIDE ───────────────────────────────────────────────────
    const flash = document.querySelector('.kd-flash');
    if (flash) setTimeout(() => flash.style.opacity = '0', 3000);

    // Init
    updateChipCounts();
});