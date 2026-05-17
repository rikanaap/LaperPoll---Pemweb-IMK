// pilih-resep.js
// Kompatibel dengan meal-planner.js baru
// Format slot: ?tanggal=2026-05-27&meal_time=SA

(function () {
'use strict';

const LABEL_WAKTU = { SA: 'Sarapan', SI: 'Makan Siang', MA: 'Makan Malam' };
const HARI        = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
const BULAN       = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];

// ── Parse slot dari query string ──────────────────────────────
// Format: ?tanggal=2026-05-27&meal_time=SA
const params   = new URLSearchParams(window.location.search);
const slotDate = params.get('tanggal')   || null;
const slotWaktu= params.get('meal_time') || null;

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
            <div class="resep-empty">
                <span class="material-icons-round resep-empty-icon">search_off</span>
                <p class="font-jakarta font-semibold">Resep tidak ditemukan</p>
                <p class="font-jakarta font-regular">Coba kata kunci lain</p>
            </div>
        `;
        return;
    }

    const noSlot = !slotDate || !slotWaktu;

    data.forEach(resep => {
        const card = document.createElement('div');
        card.className = 'resep-card' + (noSlot ? ' no-slot' : '');
        const dur  = formatDurasi(resep.cook_duration);

        card.innerHTML = `
            <div class="resep-card-content">
                <div class="resep-thumb">
                    ${resep.thumbnail
                        ? `<img src="${esc(resep.thumbnail)}" alt="${esc(resep.nama)}">`
                        : `<span class="material-icons-round">restaurant</span>`
                    }
                </div>
                <div class="resep-detail">
                    <p class="resep-nama font-jakarta font-semibold">${esc(resep.nama)}</p>
                    <div class="resep-meta">
                        ${resep.kalori ? `
                        <span class="resep-meta-item font-jakarta">
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
            </div>
            <span class="material-icons-round resep-arrow">arrow_forward_ios</span>
        `;

        if (!noSlot) card.addEventListener('click', () => pilihResep(resep));
        resepList.appendChild(card);
    });
}

// ── Pilih resep → POST ke API → redirect ke meal planner ─────
let isSaving = false;

async function pilihResep(resep) {
    if (isSaving) return;
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
            alert('Gagal menyimpan resep, coba lagi.');
            isSaving = false;
            if (overlay) overlay.style.display = 'none';
        }
    } catch (err) {
        console.error(err);
        alert('Gagal menghubungi server.');
        isSaving = false;
        if (overlay) overlay.style.display = 'none';
    }
}

// ── Search ────────────────────────────────────────────────────
document.getElementById('searchResep')?.addEventListener('input', e => {
    const q = e.target.value.toLowerCase().trim();
    renderResep(q ? allResep.filter(r => r.nama.toLowerCase().includes(q)) : allResep);
});

// ── Init ──────────────────────────────────────────────────────
renderResep(allResep);

})();