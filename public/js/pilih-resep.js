// pilih-resep.js
// Kompatibel dengan meal-planner.js baru
// Format slot: ?tanggal=2026-05-27&meal_time=SA&max_kal=2000&used_kal=550

(function () {
'use strict';

const LABEL_WAKTU = { SA: 'Sarapan', SI: 'Makan Siang', MA: 'Makan Malam' };
const HARI        = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
const BULAN       = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];

// ── Parse slot dari query string ──────────────────────────────
// Format: ?tanggal=2026-05-27&meal_time=SA&max_kal=2000&used_kal=550
const params    = new URLSearchParams(window.location.search);
const slotDate  = params.get('tanggal')   || null;
const slotWaktu = params.get('meal_time') || null;
const maxKal    = parseInt(params.get('max_kal'))  || 0;
const usedKal   = parseInt(params.get('used_kal')) || 0;
const sisaKal   = maxKal > 0 ? Math.max(0, maxKal - usedKal) : 0;

// ── Header info slot ──────────────────────────────────────────
const slotLabel   = document.getElementById('slotLabel');
const slotWarning = document.getElementById('slotWarning');

if (slotDate && slotWaktu) {
    const d        = new Date(slotDate + 'T00:00:00');
    const hariNama = HARI[d.getDay()];
    const tgl      = `${d.getDate()} ${BULAN[d.getMonth()]} ${d.getFullYear()}`;
    const wLabel   = LABEL_WAKTU[slotWaktu] || slotWaktu;
    if (slotLabel) {
        slotLabel.innerHTML = `
            <span class="slot-pill font-jakarta font-semibold">
                <span class="material-icons-round">calendar_today</span>
                ${hariNama}, ${tgl}
            </span>
            <span class="slot-pill slot-pill-waktu font-jakarta font-semibold">
                <span class="material-icons-round">restaurant</span>
                ${wLabel}
            </span>
        `;
    }

    // ── Banner kalori (hanya tampil kalau ada target) ──────────
    if (maxKal > 0) {
        const kalBanner = document.createElement('div');
        kalBanner.id = 'prKaloriBanner';
        kalBanner.className = 'pr-kalori-banner font-jakarta';
        const pct = Math.min(100, Math.round((usedKal / maxKal) * 100));
        const overTarget = usedKal >= maxKal;
        kalBanner.innerHTML = `
            <div class="pr-kalori-banner-row">
                <span class="material-icons-round pr-kal-icon">${overTarget ? 'warning_amber' : 'local_fire_department'}</span>
                <div class="pr-kalori-banner-text">
                    <span class="pr-kal-label font-semibold">
                        ${overTarget
                            ? 'Target kalori hari ini sudah penuh!'
                            : `Sisa kalori hari ini: <strong>${sisaKal} kal</strong>`
                        }
                    </span>
                    <span class="pr-kal-sub font-regular">${usedKal} / ${maxKal} kal terpakai</span>
                </div>
            </div>
            <div class="pr-kal-track">
                <div class="pr-kal-fill ${overTarget ? 'pr-kal-over' : ''}" style="width:${pct}%"></div>
            </div>
        `;
        // Sisipkan setelah pr-header
        const header = document.querySelector('.pr-header');
        if (header) header.insertAdjacentElement('afterend', kalBanner);
    }
} else {
    if (slotLabel)   slotLabel.textContent = 'Pilih slot di Meal Planner terlebih dahulu.';
    if (slotWarning) slotWarning.style.display = 'flex';
}

// ── Data resep dari DB (injected via blade) ───────────────────
const allResep = window.resepData || [];

// ── Format durasi ─────────────────────────────────────────────
function formatDurasi(t) {
    if (!t) return null;
    const p = t.split(':').map(Number);
    const j = p[0]||0, m = p[1]||0;
    if (j>0&&m>0) return `${j}j ${m}mnt`;
    if (j>0) return `${j} jam`;
    if (m>0) return `${m} mnt`;
    return null;
}

// ── XSS helper ────────────────────────────────────────────────
function esc(s) {
    return String(s).replace(/[&<>"']/g, c =>
        ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])
    );
}

// ── Render ────────────────────────────────────────────────────
const resepList = document.getElementById('resepList');

function renderResep(data) {
    if (!resepList) return;
    resepList.innerHTML = '';

    if (!data.length) {
        resepList.innerHTML = `
            <div class="resep-empty" style="grid-column:1/-1;">
                <div class="resep-empty-icon-wrap">
                    <span class="material-icons-round">search_off</span>
                </div>
                <p class="resep-empty-title font-jakarta font-bold">Resep tidak ditemukan</p>
                <p class="resep-empty-sub font-jakarta font-regular">Coba kata kunci lain</p>
            </div>
        `;
        return;
    }

    const noSlot = !slotDate || !slotWaktu;

    data.forEach(resep => {
        const card = document.createElement('div');
        card.className = 'resep-card' + (noSlot ? ' no-slot' : '');
        const dur  = formatDurasi(resep.cook_duration);

        const isAktif = !noSlot && window.resepAktifId && resep.id === window.resepAktifId;
        if (isAktif) card.classList.add('is-aktif');

        card.innerHTML = `
            <div class="resep-thumb">
                ${resep.thumbnail
                    ? `<img src="${esc(resep.thumbnail)}" alt="${esc(resep.nama)}">`
                    : `<span class="material-icons-round">restaurant</span>`
                }
                ${resep.kalori ? `<span class="resep-thumb-kal">${resep.kalori} kal</span>` : ''}
            </div>
            <div class="resep-detail">
                <div style="display:flex;align-items:center;gap:.4rem;">
                    <p class="resep-nama font-jakarta font-bold">${esc(resep.nama)}</p>
                    ${isAktif ? `<span class="pr-badge-aktif font-jakarta">Terpilih</span>` : ''}
                </div>
                <div class="resep-meta">
                    ${resep.kalori ? `
                    <span class="resep-kal-badge font-jakarta">
                        <span class="material-icons-round">local_fire_department</span>
                        ${resep.kalori} kal
                    </span>` : ''}
                    ${dur ? `
                    <span class="resep-meta-item font-jakarta">
                        <span class="material-icons-round">schedule</span>
                        ${dur}
                    </span>` : ''}
                </div>
            </div>
            <div class="resep-actions">
                ${resep.detail_url ? `
                <a href="${esc(resep.detail_url)}" target="_blank"
                   class="resep-info-btn" title="Lihat detail resep"
                   onclick="event.stopPropagation()">
                    <span class="material-icons-round">info_outline</span>
                </a>` : ''}
                <div class="resep-arrow-wrap">
                    <span class="material-icons-round resep-arrow">arrow_forward_ios</span>
                </div>
            </div>
        `;

        if (!noSlot) card.addEventListener('click', () => pilihResep(resep));
        resepList.appendChild(card);
    });
}

// ── Pilih resep → POST ke API → redirect ke meal planner ─────
let isSaving = false;


// ── Kalori warning modal ──────────────────────────────────────
function showKaloriWarning(namaResep, kalResep, proyeksi, lebih, target) {
    return new Promise(resolve => {
        let modal = document.getElementById('prKaloriModal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'prKaloriModal';
            modal.style.cssText = 'position:fixed;inset:0;z-index:700;display:flex;align-items:center;justify-content:center;padding:1.5rem;background:rgba(0,0,0,0.5);';
            modal.innerHTML = `
                <div class="pr-kal-modal-box">
                    <div class="pr-kal-modal-icon">
                        <span class="material-icons-round">warning_amber</span>
                    </div>
                    <h3 class="pr-kal-modal-title font-jakarta font-bold" id="prKalModalTitle"></h3>
                    <p class="pr-kal-modal-desc font-jakarta font-regular" id="prKalModalDesc"></p>
                    <div class="pr-kal-modal-actions">
                        <button class="pr-kal-btn-cancel font-jakarta font-semibold" id="prKalBtnBatal">Batalkan</button>
                        <button class="pr-kal-btn-confirm font-jakarta font-bold" id="prKalBtnLanjut">
                            <span class="material-icons-round">check</span>
                            Tetap Pilih
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        document.getElementById('prKalModalTitle').textContent = `Kalori melebihi target!`;
        document.getElementById('prKalModalDesc').innerHTML =
            `<strong>${namaResep}</strong> memiliki <strong>${kalResep} kal</strong>.<br>` +
            `Total akan menjadi <strong>${proyeksi} kal</strong> dari target <strong>${target} kal</strong> ` +
            `(+${lebih} kal).<br><br>Tetap pilih resep ini?`;
        modal.style.display = 'flex';

        const cleanup = (result) => {
            modal.style.display = 'none';
            document.getElementById('prKalBtnLanjut').removeEventListener('click', onLanjut);
            document.getElementById('prKalBtnBatal').removeEventListener('click', onBatal);
            modal.removeEventListener('click', onOverlay);
            resolve(result);
        };
        const onLanjut  = () => cleanup(true);
        const onBatal   = () => cleanup(false);
        const onOverlay = (e) => { if (e.target === modal) cleanup(false); };

        document.getElementById('prKalBtnLanjut').addEventListener('click', onLanjut);
        document.getElementById('prKalBtnBatal').addEventListener('click', onBatal);
        modal.addEventListener('click', onOverlay);
    });
}

// ── Toast helper ──────────────────────────────────────────────
function showToast(msg) {
    let t = document.getElementById('prToast');
    if (!t) {
        t = document.createElement('div');
        t.id = 'prToast';
        t.style.cssText = 'position:fixed;bottom:5rem;left:50%;transform:translateX(-50%) translateY(2rem);' +
            'background:#DC2626;color:white;padding:.65rem 1.25rem;border-radius:2rem;font-size:.82rem;' +
            'font-family:var(--font-jakarta);font-weight:600;opacity:0;transition:all .3s;z-index:600;white-space:nowrap;';
        document.body.appendChild(t);
    }
    t.textContent = msg;
    t.style.opacity = '1';
    t.style.transform = 'translateX(-50%) translateY(0)';
    clearTimeout(t._timer);
    t._timer = setTimeout(() => {
        t.style.opacity = '0';
        t.style.transform = 'translateX(-50%) translateY(2rem)';
    }, 3000);
}

async function pilihResep(resep) {
    if (isSaving) return;

    // ── Warning kalori: tampilkan konfirmasi jika melewati target ──
    if (maxKal > 0 && resep.kalori > 0) {
        const proyeksi = usedKal + resep.kalori;
        if (proyeksi > maxKal) {
            const lebih = proyeksi - maxKal;
            const lanjut = await showKaloriWarning(resep.nama, resep.kalori, proyeksi, lebih, maxKal);
            if (!lanjut) return; // user batalkan
        }
    }

    isSaving = true;

    const overlay = document.getElementById('loadingOverlay');
    if (overlay) overlay.style.display = 'flex';

    try {
        const res = await fetch(`${window.MP.apiBase}/tambah`, {
            method : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.MP.csrf,
                'Accept'      : 'application/json',
            },
            body: JSON.stringify({
                tanggal  : slotDate,
                meal_time: slotWaktu,
                resep_id : resep.id,
            }),
        });

        const data = await res.json();

        if (data.success) {
            // Redirect balik ke meal planner dengan flag
            const p = new URLSearchParams({
                added    : '1',
                date     : slotDate,
                meal_time: slotWaktu,
            });
            window.location.href = `${window.MP.mealPlannerUrl}?${p.toString()}`;
        } else {
            showToast('Gagal menyimpan resep, coba lagi.');
            isSaving = false;
            if (overlay) overlay.style.display = 'none';
        }
    } catch (err) {
        console.error(err);
        showToast('Gagal menghubungi server.');
        isSaving = false;
        if (overlay) overlay.style.display = 'none';
    }
}

// ── Sort & Filter state ──────────────────────────────────────
let sortMode    = 'az';    // 'az' | 'kal-asc' | 'kal-desc' | 'dur-asc'
let searchQuery = '';

function getFiltered() {
    let data = [...allResep];
    if (searchQuery) data = data.filter(r => r.nama.toLowerCase().includes(searchQuery));
    if (sortMode === 'az')       data.sort((a,b) => a.nama.localeCompare(b.nama));
    if (sortMode === 'kal-asc')  data.sort((a,b) => (a.kalori||0) - (b.kalori||0));
    if (sortMode === 'kal-desc') data.sort((a,b) => (b.kalori||0) - (a.kalori||0));
    if (sortMode === 'dur-asc')  data.sort((a,b) => (a.cook_duration||'') < (b.cook_duration||'') ? -1 : 1);
    return data;
}

function applyFilter() {
    const filtered = getFiltered();
    renderResep(filtered);
    updateCount(allResep.length, filtered.length);
}

// ── Search ────────────────────────────────────────────────────
document.getElementById('searchResep')?.addEventListener('input', e => {
    searchQuery = e.target.value.toLowerCase().trim();
    applyFilter();
});

// ── Sort chips ────────────────────────────────────────────────
document.getElementById('prSortChips')?.addEventListener('click', e => {
    const chip = e.target.closest('[data-sort]');
    if (!chip) return;
    sortMode = chip.dataset.sort;
    document.querySelectorAll('#prSortChips [data-sort]').forEach(c => c.classList.remove('active'));
    chip.classList.add('active');
    applyFilter();
});

// ── Count helper ──────────────────────────────────────────────
function updateCount(total, filtered) {
    const bar = document.getElementById('prCountBar');
    const txt = document.getElementById('prCountText');
    if (!bar || !txt) return;
    bar.style.display = '';
    txt.textContent   = total === filtered
        ? `${total} resep tersedia`
        : `${filtered} dari ${total} resep`;
}

// ── Init ──────────────────────────────────────────────────────
applyFilter();

})();