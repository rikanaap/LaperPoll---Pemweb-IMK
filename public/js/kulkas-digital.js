document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.filter-tab');
    const cards = document.querySelectorAll('.bahan-card');
    const searchInput = document.getElementById('searchKulkas');
    const grid = document.getElementById('bahanGrid');

    function updateView() {
        const activeFilter = document.querySelector('.filter-tab.active')?.dataset.filter || 'semua';
        const query = searchInput?.value.trim().toLowerCase() || '';

        cards.forEach(card => {
            const status = card.dataset.status;
            const nama = card.dataset.nama || '';

            const filterOk = activeFilter === 'semua' || status === activeFilter;
            const searchOk = query === '' || nama.includes(query);

            card.style.display = (filterOk && searchOk) ? 'flex' : 'none';
        });
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => {
                t.classList.remove('active');
                t.setAttribute('aria-selected', 'false');
            });

            tab.classList.add('active');
            tab.setAttribute('aria-selected', 'true');

            updateView();
        });
    });

    searchInput?.addEventListener('input', updateView);

    function expandCard(card) {
        const collapsed = card.querySelector('.card-collapsed');
        const expanded = card.querySelector('.card-expanded');

        if (!collapsed || !expanded) return;

        card.classList.add('expanded');
        collapsed.style.display = 'none';
        expanded.style.display = 'flex';
    }

    function collapseCard(card) {
        const collapsed = card.querySelector('.card-collapsed');
        const expanded = card.querySelector('.card-expanded');

        if (!collapsed || !expanded) return;

        card.classList.remove('expanded');
        collapsed.style.display = 'flex';
        expanded.style.display = 'none';
    }

    grid?.addEventListener('click', (e) => {
        const card = e.target.closest('.bahan-card');
        if (!card) return;
        if (e.target.closest('.hapus-btn') || e.target.closest('.hapus-form')) return;

        const isExpanded = card.classList.contains('expanded');

        document.querySelectorAll('.bahan-card.expanded').forEach(c => collapseCard(c));

        if (!isExpanded) expandCard(card);
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.bahan-card')) {
            document.querySelectorAll('.bahan-card.expanded').forEach(c => collapseCard(c));
        }
    });
});