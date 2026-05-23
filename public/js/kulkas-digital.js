// kulkas-digital.js
document.addEventListener('DOMContentLoaded', () => {

    // ── TOAST (dari atas, tidak geser layout) ────────────────────────
    function showToast(msg, type = 'success') {
        let toast = document.getElementById('kdToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'kdToast';
            document.body.appendChild(toast);
        }
        const icon = type === 'success' ? 'check_circle' : 'error_outline';
        toast.className = `kd-toast kd-toast-${type}`;
        toast.innerHTML = `<span class="material-icons-round">${icon}</span><span>${msg}</span>`;
        toast.classList.add('show');
        clearTimeout(toast._timer);
        toast._timer = setTimeout(() => toast.classList.remove('show'), 3000);
    }

    // Auto-show toast dari session (setelah redirect)
    const sessionToast = document.getElementById('kdSessionToast');
    if (sessionToast) {
        showToast(sessionToast.dataset.msg, 'success');
    }

    // ── CHIP COUNTER ─────────────────────────────────────────────────
    function updateChipCounts(query = '') {
        const cards  = document.querySelectorAll('.kd-card');
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

    // ── FILTER CHIPS ─────────────────────────────────────────────────
    const chips = document.querySelectorAll('.kd-chip');
    let activeFilter = 'semua';
    chips.forEach(chip => {
        chip.addEventListener('click', () => {
            chips.forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            activeFilter = chip.dataset.filter;
            applyFilter(document.getElementById('kdSearch')?.value.trim().toLowerCase() || '');
        });
    });

    // ── SEARCH ───────────────────────────────────────────────────────
    document.getElementById('kdSearch')?.addEventListener('input', e => {
        applyFilter(e.target.value.trim().toLowerCase());
    });

    function applyFilter(q = '') {
        const grid  = document.getElementById('kdGrid');
        const cards = grid ? grid.querySelectorAll('.kd-card') : [];
        if (cards.length === 0) return;

        let visible = 0;
        cards.forEach(card => {
            const matchFilter = activeFilter === 'semua' || card.dataset.status === activeFilter;
            const matchSearch = !q || (card.dataset.nama || '').includes(q);
            card.style.display = (matchFilter && matchSearch) ? '' : 'none';
            if (matchFilter && matchSearch) visible++;
        });

        updateChipCounts(q);

        let noResult = document.getElementById('kdNoResult');
        const shouldShow = visible === 0 && (q !== '' || activeFilter !== 'semua');
        if (shouldShow) {
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
                grid.appendChild(noResult);
            }
            noResult.style.display = '';
        } else {
            if (noResult) noResult.style.display = 'none';
        }
    }

    // ── EXPAND / COLLAPSE CARD ────────────────────────────────────────
    document.getElementById('kdGrid')?.addEventListener('click', e => {
        const card = e.target.closest('.kd-card');
        if (!card) return;
        if (e.target.closest('form') || e.target.closest('.kd-hapus-btn')) return;

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

    // ── MODAL KONFIRMASI HAPUS (custom, bukan alert bawaan) ──────────
    let hapusTarget = null;  // { formEl }

    const modalHapus        = document.getElementById('modalHapus');
    const modalHapusOverlay = document.getElementById('modalHapusOverlay');
    const modalHapusCancel  = document.getElementById('modalHapusCancel');
    const modalHapusConfirm = document.getElementById('modalHapusConfirm');

    // Tangkap semua tombol hapus di grid
    document.getElementById('kdGrid')?.addEventListener('click', e => {
        const hapusBtn = e.target.closest('.kd-hapus-btn');
        if (!hapusBtn) return;
        e.preventDefault();
        e.stopPropagation();

        hapusTarget = hapusBtn.closest('form');
        if (modalHapus) modalHapus.style.display = 'flex';
    });

    modalHapusCancel?.addEventListener('click', () => {
        if (modalHapus) modalHapus.style.display = 'none';
        hapusTarget = null;
    });
    modalHapusOverlay?.addEventListener('click', () => {
        if (modalHapus) modalHapus.style.display = 'none';
        hapusTarget = null;
    });
    modalHapusConfirm?.addEventListener('click', () => {
        if (hapusTarget) hapusTarget.submit();
    });

    // ── RENDER BAHAN DETAIL LIST ──────────────────────────────────────
    function renderBahanDetailList(bahanDetail, targetEl) {
        targetEl.innerHTML = '';
        bahanDetail.forEach(b => {
            const li = document.createElement('li');
            li.className = b.cukup ? 'bahan-cukup' : 'bahan-kurang';
            let gramInfo = '';
            if (b.butuh > 0) {
                gramInfo = b.punya > 0
                    ? `${b.punya}g / ${b.butuh}g`
                    : `Butuh ${b.butuh}g`;
            }
            li.innerHTML = `
                <span class="modal-bahan-nama">${b.nama}</span>
                ${gramInfo ? `<span class="modal-bahan-gram">${gramInfo}</span>` : ''}
            `;
            targetEl.appendChild(li);
        });
    }

    // ── MODAL BAHAN KURANG ────────────────────────────────────────────
    const modalKurang        = document.getElementById('modalBahanKurang');
    const modalKurangTitle   = document.getElementById('modalKurangTitle');
    const modalKurangList    = document.getElementById('modalKurangList');
    const modalKurangClose   = document.getElementById('modalKurangClose');
    const modalKurangOverlay = document.getElementById('modalKurangOverlay');

    function openModalKurang(nama, bahanDetail) {
        modalKurangTitle.textContent = nama;
        renderBahanDetailList(bahanDetail, modalKurangList);
        modalKurang.style.display = 'flex';
    }
    function closeModalKurang() { if (modalKurang) modalKurang.style.display = 'none'; }
    modalKurangClose?.addEventListener('click', closeModalKurang);
    modalKurangOverlay?.addEventListener('click', closeModalKurang);

    // ── MODAL KONFIRMASI MASAK ────────────────────────────────────────
    const modalMasak        = document.getElementById('modalMasak');
    const modalMasakTitle   = document.getElementById('modalMasakTitle');
    const modalMasakList    = document.getElementById('modalMasakBahanList');
    const modalMasakCancel  = document.getElementById('modalMasakCancel');
    const modalMasakConfirm = document.getElementById('modalMasakConfirm');
    const modalMasakOverlay = document.getElementById('modalMasakOverlay');
    const modalMasakLoading = document.getElementById('modalMasakLoading');

    let currentBahanIds  = [];
    let currentGramMap   = {};  // { bahan_id: gram_dibutuhkan }
    let currentResepId   = null;

    function openModalMasak(resepItem, bahanDetail) {
        currentResepId  = resepItem.dataset.resepId;
        currentBahanIds = (resepItem.dataset.bahanIds || '')
            .split(',').map(s => parseInt(s.trim())).filter(Boolean);

        // Bangun gram map dari bahanDetail
        currentGramMap = {};
        bahanDetail.forEach(b => { currentGramMap[b.id] = b.butuh; });

        modalMasakTitle.textContent = resepItem.dataset.resepNama;
        renderBahanDetailList(bahanDetail, modalMasakList);

        modalMasakLoading.style.display = 'none';
        modalMasakConfirm.disabled      = false;
        modalMasak.style.display        = 'flex';
    }
    function closeModalMasak() { if (modalMasak) modalMasak.style.display = 'none'; }
    modalMasakCancel?.addEventListener('click', closeModalMasak);
    modalMasakOverlay?.addEventListener('click', closeModalMasak);

    modalMasakConfirm?.addEventListener('click', async () => {
        if (!currentResepId || currentBahanIds.length === 0) return;

        modalMasakConfirm.disabled      = true;
        modalMasakLoading.style.display = 'block';

        try {
            const res = await fetch(PAKAI_RESEP_URL, {
                method : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept'      : 'application/json',
                },
                body: JSON.stringify({
                    resep_id    : parseInt(currentResepId),
                    bahan_ids   : currentBahanIds,
                    gram_dipakai: currentGramMap,   // ← kirim gram per bahan
                }),
            });

            const data = await res.json();

            if (data.success && data.redirect) {
                window.location.href = data.redirect;
            } else {
                showToast('Terjadi kesalahan, silakan coba lagi.', 'error');
                modalMasakConfirm.disabled      = false;
                modalMasakLoading.style.display = 'none';
            }
        } catch (err) {
            console.error(err);
            showToast('Gagal menghubungi server.', 'error');
            modalMasakConfirm.disabled      = false;
            modalMasakLoading.style.display = 'none';
        }
    });

    // ── KLIK ITEM RESEP ───────────────────────────────────────────────
    document.querySelectorAll('.kd-resep-item').forEach(item => {
        item.addEventListener('click', () => {
            const lengkap = item.dataset.lengkap === '1';
            const nama    = item.dataset.resepNama;
            let bahanDetail = [];
            try { bahanDetail = JSON.parse(item.dataset.bahanDetail || '[]'); } catch(e) {}

            if (lengkap) {
                openModalMasak(item, bahanDetail);
            } else {
                openModalKurang(nama, bahanDetail);
            }
        });
    });

    // Init
    updateChipCounts();
});