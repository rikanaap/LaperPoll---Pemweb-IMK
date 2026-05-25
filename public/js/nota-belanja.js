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

document.getElementById('filterApply')?.addEventListener('click', async () => {
    const start = filterStart?.value;
    const end   = filterEnd?.value;
    if (!start || !end) { toast('Isi kedua tanggal dulu.', true); return; }
    if (start > end)    { toast('Tanggal akhir tidak boleh sebelum tanggal mulai.', true); return; }

    // Re-sync cart dari range baru, lalu redirect
    const applyBtn = document.getElementById('filterApply');
    if (applyBtn) { applyBtn.disabled = true; applyBtn.textContent = 'Memuat...'; }

    try {
        const res = await fetch(window.nbGenerateUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept'      : 'application/json',
            },
            body: JSON.stringify({ start, end }),
        });
        const data = await res.json();
        if (data.success) {
            window.location.href = `${window.notaUrl}?start=${start}&end=${end}`;
        } else {
            toast(data.message || 'Tidak ada resep di rentang tanggal ini.', true);
            if (applyBtn) { applyBtn.disabled = false; applyBtn.innerHTML = '<span class="material-icons-round">search</span> Tampilkan'; }
        }
    } catch (err) {
        toast('Gagal memuat data: ' + err.message, true);
        if (applyBtn) { applyBtn.disabled = false; applyBtn.innerHTML = '<span class="material-icons-round">search</span> Tampilkan'; }
    }
});

// ── Init ──────────────────────────────────────────────────────
updateProgress();

})();
// ── CUSTOM CALENDAR PICKER (filter tanggal) ──────────────────
(function() {
    'use strict';

    const BULAN = ['Januari','Februari','Maret','April','Mei','Juni',
                   'Juli','Agustus','September','Oktober','November','Desember'];

    function toISO(d) {
        return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
    }
    function formatDisplay(iso) {
        if (!iso) return 'Pilih tanggal';
        const [y,m,d] = iso.split('-');
        return `${parseInt(d)} ${BULAN[parseInt(m)-1]} ${y}`;
    }

    function createNbCalendar(hiddenId, triggerId, textId, popupId) {
        const hidden  = document.getElementById(hiddenId);
        const trigger = document.getElementById(triggerId);
        const textEl  = document.getElementById(textId);
        const popup   = document.getElementById(popupId);
        if (!hidden || !trigger || !popup) return;

        const todayISO = toISO(new Date());
        let selected   = hidden.value || todayISO;
        let curYear    = parseInt(selected.split('-')[0]);
        let curMonth   = parseInt(selected.split('-')[1]) - 1;
        let isOpen     = false;

        // Init display
        textEl.textContent = formatDisplay(selected);

        function render() {
            const firstDay  = new Date(curYear, curMonth, 1).getDay();
            const daysInMon = new Date(curYear, curMonth + 1, 0).getDate();
            const offset    = firstDay === 0 ? 6 : firstDay - 1;

            let html = `
                <div class="nb-cal-nav">
                    <button type="button" class="nb-cal-nav-btn" id="${popupId}_prev">
                        <span class="material-icons-round">chevron_left</span>
                    </button>
                    <span class="nb-cal-month-label font-jakarta">${BULAN[curMonth]} ${curYear}</span>
                    <button type="button" class="nb-cal-nav-btn" id="${popupId}_next">
                        <span class="material-icons-round">chevron_right</span>
                    </button>
                </div>
                <div class="nb-cal-grid">
            `;

            ['Sen','Sel','Rab','Kam','Jum','Sab','Min'].forEach(h => {
                html += `<span class="nb-cal-day-name font-jakarta">${h}</span>`;
            });

            for (let i = 0; i < offset; i++) html += `<span></span>`;

            for (let day = 1; day <= daysInMon; day++) {
                const iso = `${curYear}-${String(curMonth+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
                let cls = 'nb-cal-cell font-jakarta';
                if (iso === todayISO)  cls += ' nb-cal-today';
                if (iso === selected)  cls += ' nb-cal-selected';
                html += `<span class="${cls}" data-date="${iso}">${day}</span>`;
            }
            html += '</div>';
            popup.innerHTML = html;

            popup.querySelector(`#${popupId}_prev`)?.addEventListener('click', e => {
                e.stopPropagation();
                if (--curMonth < 0) { curMonth = 11; curYear--; }
                render();
            });
            popup.querySelector(`#${popupId}_next`)?.addEventListener('click', e => {
                e.stopPropagation();
                if (++curMonth > 11) { curMonth = 0; curYear++; }
                render();
            });

            popup.querySelectorAll('.nb-cal-cell').forEach(cell => {
                cell.addEventListener('click', e => {
                    e.stopPropagation();
                    selected = cell.dataset.date;
                    hidden.value = selected;
                    textEl.textContent = formatDisplay(selected);
                    closePopup();
                });
            });
        }

        function openPopup() {
            const d = selected ? selected.split('-') : todayISO.split('-');
            curYear  = parseInt(d[0]);
            curMonth = parseInt(d[1]) - 1;
            render();
            popup.style.display = '';
            trigger.classList.add('open');
            isOpen = true;
        }

        function closePopup() {
            popup.style.display = 'none';
            trigger.classList.remove('open');
            isOpen = false;
        }

        trigger.onclick = e => {
            e.stopPropagation();
            isOpen ? closePopup() : openPopup();
        };

        document.addEventListener('click', e => {
            if (!popup.contains(e.target) && !trigger.contains(e.target)) {
                closePopup();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        createNbCalendar('filterStart', 'filterStartTrigger', 'filterStartText', 'filterStartPopup');
        createNbCalendar('filterEnd',   'filterEndTrigger',   'filterEndText',   'filterEndPopup');

        // Update display from preset buttons (already sets hidden input value)
        // We need to watch for preset changes and sync display
        document.querySelectorAll('.nb-preset-btn').forEach(btn => {
            const origClick = btn.onclick;
            btn.addEventListener('click', () => {
                setTimeout(() => {
                    const sVal = document.getElementById('filterStart')?.value;
                    const eVal = document.getElementById('filterEnd')?.value;
                    const sText = document.getElementById('filterStartText');
                    const eText = document.getElementById('filterEndText');
                    if (sText && sVal) sText.textContent = formatDisplay(sVal);
                    if (eText && eVal) eText.textContent = formatDisplay(eVal);
                }, 10);
            });
        });
    });
})();