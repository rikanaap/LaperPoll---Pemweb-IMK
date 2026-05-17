// nota-belanja.js
(function () {
'use strict';

// ── API helper ────────────────────────────────────────────────
async function api(url, method = 'GET', body = null) {
    const opts = {
        method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept'      : 'application/json',
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

// ── Toast ─────────────────────────────────────────────────────
let toastTimer;
function toast(msg, isErr = false) {
    const el   = document.getElementById('nbToast');
    const icon = document.getElementById('nbToastIcon');
    const txt  = document.getElementById('nbToastMsg');
    if (!el) return;
    el.className = 'nb-toast' + (isErr ? ' nb-toast-error' : '');
    if (icon) icon.textContent = isErr ? 'error_outline' : 'check_circle';
    if (txt)  txt.textContent  = msg;
    el.classList.remove('hidden');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => el.classList.add('hidden'), 3200);
}

// ── Update progress bar + sub text + hapus btn ───────────────
function updateProgress() {
    const allItems  = document.querySelectorAll('.nb-item');
    const doneItems = document.querySelectorAll('.nb-item.nb-item-done');
    const total     = allItems.length;
    const done      = doneItems.length;
    const pct       = total > 0 ? Math.round((done / total) * 100) : 0;

    const fillEl = document.getElementById('progressFill');
    const pctEl  = document.getElementById('progressPct');
    const subEl  = document.getElementById('progressSub');

    if (fillEl) {
        fillEl.style.width = pct + '%';
        fillEl.classList.toggle('nb-progress-fill-done', total > 0 && done === total);
    }
    if (pctEl) pctEl.textContent = pct + '%';
    if (subEl) subEl.textContent = `${done} dari ${total} item sudah dibeli`;

    // Hapus selesai btn
    const hapusWrap = document.getElementById('hapusSelesaiWrap');
    const hapusBtn  = document.getElementById('hapusSelesaiBtn');
    if (hapusWrap) hapusWrap.classList.toggle('hidden', done === 0);
    if (hapusBtn && done > 0) {
        hapusBtn.innerHTML = `
            <span class="material-icons-round">delete_sweep</span>
            Hapus ${done} bahan yang sudah dibeli
        `;
    }

    // Semua selesai
    if (total > 0 && done === total) toast('🎉 Semua bahan sudah dibeli!');
}

// ── Update counter per kategori ───────────────────────────────
function updateKatCount(card) {
    const items  = card.querySelectorAll('.nb-item');
    const katEl  = card.querySelector('[data-kat-count]');
    if (katEl) katEl.textContent = `${items.length} item`;
    // Kalau kategori kosong, hapus card-nya
    if (items.length === 0) card.remove();
}

// ── Toggle checkbox ───────────────────────────────────────────
document.getElementById('bahanList')?.addEventListener('change', async e => {
    const checkbox = e.target.closest('.nb-check');
    if (!checkbox) return;

    const label  = checkbox.closest('.nb-item');
    const itemId = label?.dataset.id;
    if (!itemId) return;

    const isChecked = checkbox.checked;

    // Optimistic UI
    label.classList.toggle('nb-item-done', isChecked);
    updateProgress();

    try {
        await api(`${window.nbApiToggle}/${itemId}`, 'PATCH');
    } catch (err) {
        // Rollback
        checkbox.checked = !isChecked;
        label.classList.toggle('nb-item-done', !isChecked);
        updateProgress();
        toast('Gagal menyimpan: ' + err.message, true);
    }
});

// ── Hapus semua yang sudah dibeli ────────────────────────────
document.getElementById('hapusSelesaiBtn')?.addEventListener('click', async () => {
    const doneEls = document.querySelectorAll('.nb-item.nb-item-done');
    if (doneEls.length === 0) return;
    if (!confirm(`Hapus ${doneEls.length} bahan yang sudah dibeli dari nota?`)) return;

    try {
        const data = await api(window.nbApiHapus, 'DELETE');

        // Hapus elemen dari DOM dengan bersih (tidak meninggalkan divider)
        doneEls.forEach(el => el.remove());

        // Update counter tiap kategori, hapus card kalau kosong
        document.querySelectorAll('.nb-kategori-card').forEach(card => updateKatCount(card));

        updateProgress();
        toast(`${data.deleted} bahan berhasil dihapus dari nota!`);

        // Kalau semua item habis → tampilkan empty state
        if (document.querySelectorAll('.nb-item').length === 0) {
            document.getElementById('bahanList').innerHTML = `
                <div class="nb-empty">
                    <div class="nb-empty-icon-wrap">
                        <span class="material-icons-round">receipt_long</span>
                    </div>
                    <p class="font-jakarta font-semibold nb-empty-title">Nota belanja kosong</p>
                    <p class="font-jakarta font-regular nb-empty-sub">Semua bahan sudah dibeli!</p>
                    <a href="${window.notaUrl}" class="nb-empty-cta font-jakarta font-semibold">
                        <span class="material-icons-round">refresh</span>
                        Refresh
                    </a>
                </div>
            `;
            document.getElementById('hapusSelesaiWrap')?.classList.add('hidden');
            document.getElementById('nbProgressCard')?.classList.add('hidden');
        }
    } catch (err) {
        toast('Gagal menghapus: ' + err.message, true);
    }
});

// ── Filter dropdown ───────────────────────────────────────────
const filterBtn      = document.getElementById('filterBtn');
const filterDropdown = document.getElementById('filterDropdown');
const filterStart    = document.getElementById('filterStart');
const filterEnd      = document.getElementById('filterEnd');

filterBtn?.addEventListener('click', e => {
    e.stopPropagation();
    filterDropdown?.classList.toggle('hidden');
});
document.addEventListener('click', e => {
    if (!e.target.closest('#filterDropdown') && !e.target.closest('#filterBtn')) {
        filterDropdown?.classList.add('hidden');
    }
});

// Preset buttons
document.querySelectorAll('.nb-preset-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const t   = new Date(); t.setHours(0,0,0,0);
        const iso = d => `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
        let s, end;

        switch (btn.dataset.preset) {
            case 'today':    s = end = new Date(t); break;
            case 'tomorrow': s = end = new Date(t); s.setDate(t.getDate()+1); end = new Date(s); break;
            case 'thisweek': {
                const dow = t.getDay()===0 ? 6 : t.getDay()-1;
                s = new Date(t); s.setDate(t.getDate()-dow);
                if (s < t) s = new Date(t);
                end = new Date(s); end.setDate(s.getDate()+6);
                break;
            }
            case 'next7': s = new Date(t); end = new Date(t); end.setDate(t.getDate()+6); break;
            default: return;
        }
        if (filterStart) filterStart.value = iso(s);
        if (filterEnd)   filterEnd.value   = iso(end);
    });
});

document.getElementById('filterApply')?.addEventListener('click', () => {
    const start = filterStart?.value;
    const end   = filterEnd?.value;
    if (!start || !end) { toast('Isi kedua tanggal dulu.', true); return; }
    if (start > end)    { toast('Tanggal akhir tidak boleh sebelum tanggal mulai.', true); return; }
    window.location.href = `${window.notaUrl}?start=${start}&end=${end}`;
});

// ── Init ──────────────────────────────────────────────────────
updateProgress();

})();