// nota-belanja.js

(function () {
    'use strict';

    // ── Helpers ────────────────────────────────────────────────────────
    async function apiFetch(url, method = 'GET', body = null) {
        const opts = {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
            },
        };
        if (body) opts.body = JSON.stringify(body);
        const res = await fetch(url, opts);
        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            throw new Error(err.message || `HTTP ${res.status}`);
        }
        return res.json();
    }

    function showToast(msg, isError = false) {
        const el = document.getElementById('nbToast');
        if (!el) return;
        el.textContent = msg;
        el.className   = 'nb-toast' + (isError ? ' nb-toast-error' : '');
        el.classList.remove('hidden');
        clearTimeout(el._timer);
        el._timer = setTimeout(() => el.classList.add('hidden'), 3000);
    }

    // ── Progress tracker ───────────────────────────────────────────────
    function updateProgress() {
        const all     = document.querySelectorAll('.nb-check');
        const checked = document.querySelectorAll('.nb-check:checked');
        const total   = all.length;
        const done    = checked.length;
        const pct     = total > 0 ? Math.round((done / total) * 100) : 0;

        const fillEl = document.getElementById('progressFill');
        const pctEl  = document.getElementById('progressPct');
        if (fillEl) fillEl.style.width = pct + '%';
        if (pctEl)  pctEl.textContent  = pct + '%';

        // REVISI 1: tampilkan tombol hapus kalau ada yang sudah dibeli
        const hapusWrap = document.getElementById('hapusSelesaiWrap');
        const hapusBtn  = document.getElementById('hapusSelesaiBtn');
        if (hapusWrap) {
            hapusWrap.classList.toggle('hidden', done === 0);
        }
        if (hapusBtn) {
            hapusBtn.innerHTML = `
                <span class="material-icons-round">delete_sweep</span>
                Hapus ${done} bahan yang sudah dibeli
            `;
        }

        // Kalau semua selesai → tampilkan pesan
        if (total > 0 && done === total) {
            fillEl?.classList.add('nb-progress-fill-done');
            showToast('🎉 Semua bahan sudah dibeli!');
        } else {
            fillEl?.classList.remove('nb-progress-fill-done');
        }
    }

    // ── Toggle check item (AJAX ke server) ────────────────────────────
    document.querySelectorAll('.nb-check').forEach(checkbox => {
        checkbox.addEventListener('change', async (e) => {
            const label  = e.target.closest('.nb-item');
            const itemId = label?.dataset.id;
            if (!itemId) return;

            // Optimistic UI
            label.classList.toggle('nb-item-done', e.target.checked);
            updateProgress();

            try {
                await apiFetch(`${window.nbApiToggle}/${itemId}`, 'POST');
            } catch (err) {
                // Rollback
                e.target.checked = !e.target.checked;
                label.classList.toggle('nb-item-done', e.target.checked);
                updateProgress();
                showToast('Gagal menyimpan: ' + err.message, true);
            }
        });
    });

    // ── REVISI 1: Hapus semua yang sudah dibeli ────────────────────────
    document.getElementById('hapusSelesaiBtn')?.addEventListener('click', async () => {
        const done = document.querySelectorAll('.nb-check:checked').length;
        if (done === 0) return;
        if (!confirm(`Hapus ${done} bahan yang sudah dibeli dari nota?`)) return;

        try {
            const data = await apiFetch(window.nbApiHapus, 'DELETE');
            // Hapus elemen dari DOM
            document.querySelectorAll('.nb-item.nb-item-done').forEach(el => {
                // Hapus divider sebelum/sesudahnya juga
                const prev = el.previousElementSibling;
                const next = el.nextElementSibling;
                if (next?.classList.contains('nb-item-divider')) next.remove();
                else if (prev?.classList.contains('nb-item-divider')) prev.remove();
                el.remove();
            });

            // Kalau kategori kosong, hapus card-nya
            document.querySelectorAll('.nb-kategori-card').forEach(card => {
                const items = card.querySelectorAll('.nb-item');
                if (items.length === 0) card.remove();
            });

            updateProgress();
            showToast(`${data.deleted} bahan berhasil dihapus dari nota!`);

            // Kalau semua kosong, tampilkan empty state
            const remaining = document.querySelectorAll('.nb-item').length;
            if (remaining === 0) {
                document.getElementById('bahanList').innerHTML = `
                    <div class="nb-empty">
                        <div class="nb-empty-icon-wrap">
                            <span class="material-icons-round">receipt_long</span>
                        </div>
                        <p class="font-jakarta font-semibold nb-empty-title">Nota belanja kosong</p>
                        <p class="font-jakarta font-regular nb-empty-sub">
                            Semua bahan sudah dibeli!
                        </p>
                        <a href="${window.notaUrl}" class="nb-empty-cta font-jakarta font-semibold">
                            <span class="material-icons-round">refresh</span>
                            Refresh
                        </a>
                    </div>
                `;
                document.getElementById('hapusSelesaiWrap')?.classList.add('hidden');
                document.querySelector('.nb-progress-card')?.classList.add('hidden');
            }
        } catch (err) {
            showToast('Gagal menghapus: ' + err.message, true);
        }
    });

    // ── REVISI 2: Filter tanggal ───────────────────────────────────────
    const filterBtn      = document.getElementById('filterBtn');
    const filterDropdown = document.getElementById('filterDropdown');
    const filterApply    = document.getElementById('filterApply');
    const filterStart    = document.getElementById('filterStart');
    const filterEnd      = document.getElementById('filterEnd');

    filterBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        filterDropdown?.classList.toggle('hidden');
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('#filterDropdown') && !e.target.closest('#filterBtn')) {
            filterDropdown?.classList.add('hidden');
        }
    });

    // Preset buttons
    document.querySelectorAll('.nb-preset-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const today = new Date(); today.setHours(0,0,0,0);
            const toISO = d => `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;

            let s, end;
            const preset = btn.dataset.preset;

            if (preset === 'today') {
                s = end = new Date(today);
            } else if (preset === 'tomorrow') {
                s = end = new Date(today); s.setDate(today.getDate()+1); end = new Date(s);
            } else if (preset === 'thisweek') {
                const dow = today.getDay() === 0 ? 6 : today.getDay() - 1;
                s   = new Date(today); s.setDate(today.getDate() - dow);
                end = new Date(s); end.setDate(s.getDate() + 6);
                if (s < today) s = new Date(today);
            } else if (preset === 'next7') {
                s   = new Date(today);
                end = new Date(today); end.setDate(today.getDate() + 6);
            }

            if (filterStart) filterStart.value = toISO(s);
            if (filterEnd)   filterEnd.value   = toISO(end);
        });
    });

    filterApply?.addEventListener('click', () => {
        const start = filterStart?.value;
        const end   = filterEnd?.value;
        if (!start || !end) { showToast('Isi kedua tanggal dulu.', true); return; }
        if (start > end) { showToast('Tanggal akhir tidak boleh sebelum tanggal mulai.', true); return; }
        // Redirect dengan query string
        window.location.href = `${window.notaUrl}?start=${start}&end=${end}`;
    });

    // ── Init progress ──────────────────────────────────────────────────
    updateProgress();

})();