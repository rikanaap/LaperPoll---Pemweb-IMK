// ============================================================
//  MEAL PLANNER — JS utama
//  Data disimpan ke server (bukan localStorage)
//  Revisi:
//  1. Kalori target disimpan per hari ke DB
//  2. Bar kalori merah tua saat melebihi batas
//  3. Hapus preset "Kemarin" & "Minggu lalu", ganti preset masa depan
//  4. Tab hari = tanggal dari range yang dipilih (dinamis)
// ============================================================

const iconWaktu  = { SA: 'wb_sunny', SI: 'restaurant', MA: 'bedtime' };
const labelWaktu = { SA: 'SARAPAN',  SI: 'MAKAN SIANG', MA: 'MAKAN MALAM' };
const mealTimes  = ['SA', 'SI', 'MA'];
const bulanNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
const hariNames  = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
const monthNames = ['Januari','Februari','Maret','April','Mei','Juni',
                    'Juli','Agustus','September','Oktober','November','Desember'];

// State global
let activeDateList = [];   // ['2026-05-16', '2026-05-17', ...]
let serverData     = {};   // { '2026-05-16': { planner_id, max_calorie, meals, total_kalori }, ... }
let rangeStart     = null;
let rangeEnd       = null;
let calPickStep    = 0;
let calViewMonth, calViewYear;

// ─── Helpers ──────────────────────────────────────────────────────────
function toISO(d) {
    return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
}
function parseISO(s) {
    const [y,m,d] = s.split('-').map(Number);
    return new Date(y, m-1, d);
}
function todayISO() { return toISO(new Date()); }

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

function showLoading(show) {
    document.getElementById('mpLoading')?.classList.toggle('hidden', !show);
}

// ─── REVISI 4: Build tabs dari array tanggal ──────────────────────────
function buildTabs(dates) {
    activeDateList = dates;
    const hariTabs   = document.getElementById('hariTabs');
    const hariLabels = document.getElementById('hariLabels');
    if (!hariTabs || !hariLabels) return;

    // Hapus radio + content lama
    hariTabs.querySelectorAll('input[name="hari"]').forEach(r => r.remove());
    hariTabs.querySelectorAll('.hari-content').forEach(c => c.remove());
    hariLabels.innerHTML = '';

    dates.forEach((iso, i) => {
        const d       = parseISO(iso);
        const tabId   = `tab-${i}`;
        const isToday = iso === todayISO();

        // Radio
        const radio   = document.createElement('input');
        radio.type    = 'radio';
        radio.name    = 'hari';
        radio.id      = tabId;
        if (i === 0) radio.checked = true;
        hariTabs.insertBefore(radio, hariLabels);

        // Label
        const label   = document.createElement('label');
        label.htmlFor = tabId;
        label.className = 'hari-label flex flex-col gap-0' + (isToday ? ' hari-label-today' : '');
        label.innerHTML = `
            <span class="hari-label-day font-jakarta font-semibold text-caption">${hariNames[d.getDay()]}</span>
            <span class="hari-label-date font-jakarta font-bold text-body">${d.getDate()}</span>
            <span class="hari-label-bulan font-jakarta" style="font-size:0.6rem;color:#B87C5A;">${bulanNames[d.getMonth()]}</span>
        `;
        hariLabels.appendChild(label);

        // Content
        const content    = document.createElement('div');
        content.className = 'hari-content';
        content.id       = `content-${i}`;
        hariTabs.appendChild(content);
    });

    // Bind radio change → update kalori tracker
    hariTabs.querySelectorAll('input[name="hari"]').forEach(radio => {
        radio.addEventListener('change', () => {
            updateKaloriTracker();
            cekTombolGenerate();
        });
    });
}

// ─── Ambil data dari server ───────────────────────────────────────────
async function loadData(start, end) {
    showLoading(true);
    try {
        const rows = await apiFetch(
            `${window.mpApiBase}?start=${start}&end=${end}`
        );
        serverData = {};
        rows.forEach(row => { serverData[row.tanggal] = row; });
        renderSemuaSlot();
        updateKaloriTracker();
        cekTombolGenerate();
    } catch (e) {
        console.error('Gagal load data:', e);
        showToast('Gagal memuat data. ' + e.message, true);
    } finally {
        showLoading(false);
    }
}

// ─── Render semua slot ────────────────────────────────────────────────
function renderSemuaSlot() {
    activeDateList.forEach((iso, i) => {
        const container = document.getElementById(`content-${i}`);
        if (!container) return;
        container.innerHTML = '';

        const dayData = serverData[iso] || { meals: { SA: null, SI: null, MA: null } };

        mealTimes.forEach(waktu => {
            const meal    = dayData.meals?.[waktu] ?? null;
            const section = document.createElement('div');
            section.className = 'meal-section flex flex-col gap-2';

            if (meal) {
                section.innerHTML = `
                    <div class="meal-section-header flex flex-row">
                        <span class="material-icons-round meal-icon">${iconWaktu[waktu]}</span>
                        <p class="font-jakarta font-bold text-caption meal-label">${labelWaktu[waktu]}</p>
                        <button class="meal-action font-jakarta font-bold text-caption hapus-btn"
                            data-detail-id="${meal.detail_id}">HAPUS</button>
                    </div>
                    <div class="meal-card flex flex-row gap-3">
                        <div class="meal-img-placeholder">
                            ${meal.thumbnail
                                ? `<img src="${meal.thumbnail}" alt="${meal.nama}" style="width:100%;height:100%;object-fit:cover;border-radius:0.5rem;">`
                                : ''}
                        </div>
                        <div class="meal-info flex flex-col gap-1">
                            <h2 class="font-jakarta font-semibold text-title2 text-secondary-normal">${meal.nama}</h2>
                            <p class="font-jakarta font-regular text-caption text-primary-darker">
                                ${labelWaktu[waktu]} · ${meal.kalori} kal
                                ${meal.durasi ? '· ' + meal.durasi : ''}
                            </p>
                        </div>
                    </div>
                `;
            } else {
                section.innerHTML = `
                    <div class="meal-section-header flex flex-row">
                        <span class="material-icons-round meal-icon">${iconWaktu[waktu]}</span>
                        <p class="font-jakarta font-bold text-caption meal-label">${labelWaktu[waktu]}</p>
                    </div>
                    <a href="${window.pilihResepUrl}?tanggal=${iso}&meal_time=${waktu}" class="slot-kosong flex flex-row gap-3">
                        <span class="material-icons-round slot-kosong-icon">add_circle_outline</span>
                        <p class="font-jakarta font-medium text-body text-primary-darker">Tambah resep</p>
                    </a>
                `;
            }

            container.appendChild(section);
        });
    });

    // Bind hapus
    document.querySelectorAll('.hapus-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            const detailId = e.currentTarget.dataset.detailId;
            if (!confirm('Hapus resep dari slot ini?')) return;
            try {
                await apiFetch(`${window.mpApiBase}/detail/${detailId}`, 'DELETE');
                // Update serverData lokal
                for (const iso of activeDateList) {
                    const day = serverData[iso];
                    if (!day) continue;
                    for (const wkt of mealTimes) {
                        if (day.meals[wkt]?.detail_id == detailId) {
                            day.meals[wkt]    = null;
                            day.total_kalori  = mealTimes.reduce((s,w) => s + (day.meals[w]?.kalori ?? 0), 0);
                        }
                    }
                }
                renderSemuaSlot();
                updateKaloriTracker();
                cekTombolGenerate();
            } catch (e) {
                showToast('Gagal menghapus: ' + e.message, true);
            }
        });
    });
}

// ─── Generate button ──────────────────────────────────────────────────
function cekTombolGenerate() {
    const btn = document.getElementById('generateNotaBtn');
    if (!btn) return;
    const adaData = activeDateList.some(iso => {
        const day = serverData[iso];
        return day && mealTimes.some(w => day.meals[w] !== null);
    });
    btn.style.opacity       = adaData ? '1'    : '0.5';
    btn.style.pointerEvents = adaData ? 'auto' : 'none';
}

document.getElementById('generateNotaBtn')?.addEventListener('click', async () => {
    if (!activeDateList.length) return;
    const start = activeDateList[0];
    const end   = activeDateList[activeDateList.length - 1];
    try {
        showLoading(true);
        const data = await apiFetch(`${window.mpApiBase}/generate-nota`, 'POST', { start, end });
        if (data.success) window.location.href = data.redirect;
    } catch (e) {
        showToast('Gagal generate nota: ' + e.message, true);
    } finally {
        showLoading(false);
    }
});

// ─── REVISI 1+2: Kalori Tracker per hari ─────────────────────────────
function getActiveISO() {
    const checked = document.querySelector('input[name="hari"]:checked');
    if (!checked) return activeDateList[0] || todayISO();
    const idx = parseInt(checked.id.replace('tab-', ''));
    return activeDateList[idx] ?? todayISO();
}

function updateKaloriTracker() {
    const iso      = getActiveISO();
    const dayData  = serverData[iso];
    const target   = dayData?.max_calorie ?? 0;
    const current  = dayData?.total_kalori ?? 0;
    const tracker  = document.getElementById('kaloriTracker');
    if (!tracker) return;

    if (target === 0) {
        tracker.innerHTML = `
            <button class="kalori-atur-btn flex flex-row gap-1" id="kaloriAturBtnDynamic">
                <span class="material-icons-round">emoji_food_beverage</span>
                <span class="font-jakarta font-semibold text-caption">🔥 Atur Target Kalori Hari Ini</span>
            </button>
        `;
        document.getElementById('kaloriAturBtnDynamic')?.addEventListener('click', () => bukaModalKalori(iso));
        return;
    }

    // Full tracker UI
    if (!document.getElementById('kaloriNilai')) {
        tracker.innerHTML = `
            <div class="kalori-row flex flex-row gap-2">
                <span class="kalori-nilai font-jakarta font-bold text-h5" id="kaloriNilai"></span>
                <button class="kalori-edit-btn" id="kaloriEditBtn" title="Edit target kalori">
                    <span class="material-icons-round">edit</span>
                </button>
            </div>
            <div class="kalori-bar-track">
                <div class="kalori-bar-fill" id="kaloriBarFill" style="width:0%"></div>
            </div>
            <div class="kalori-alert hidden flex flex-row gap-1" id="kaloriAlert">
                <span class="material-icons-round kalori-alert-icon">warning_amber</span>
                <span class="font-jakarta font-semibold text-caption">Melebihi batas kalori hari ini!</span>
            </div>
        `;
        document.getElementById('kaloriEditBtn')?.addEventListener('click', () => bukaModalKalori(getActiveISO()));
    }

    const nilaiEl = document.getElementById('kaloriNilai');
    const barFill = document.getElementById('kaloriBarFill');
    const alertEl = document.getElementById('kaloriAlert');

    if (nilaiEl) nilaiEl.textContent = `${current} / ${target} kal`;

    const pct      = target > 0 ? Math.min((current / target) * 100, 100) : 0;
    const melebihi = current > target;

    if (barFill) {
        barFill.style.width = pct + '%';
        // REVISI 2: merah tua kalau melebihi, oranye kalau normal
        barFill.style.background = melebihi
            ? 'linear-gradient(90deg, #7F1D1D, #991B1B)'
            : 'linear-gradient(90deg, #E65100, #FF8A50)';
    }

    if (alertEl) alertEl.classList.toggle('hidden', !melebihi);
}

function bukaModalKalori(iso) {
    const modal = document.getElementById('kaloriModal');
    if (!modal) return;

    const tanggalEl = document.getElementById('kaloriModalTanggal');
    if (tanggalEl) {
        const d = parseISO(iso);
        tanggalEl.textContent = `${hariNames[d.getDay()]}, ${d.getDate()} ${bulanNames[d.getMonth()]} ${d.getFullYear()}`;
    }

    const inputEl = document.getElementById('kaloriInput');
    const existing = serverData[iso]?.max_calorie;
    if (inputEl) inputEl.value = existing || '';

    modal.classList.remove('hidden');

    // Rebind submit (hapus listener lama)
    const oldBtn = document.getElementById('kaloriSubmit');
    const newBtn = oldBtn.cloneNode(true);
    oldBtn.parentNode.replaceChild(newBtn, oldBtn);

    newBtn.addEventListener('click', async () => {
        const val = parseInt(document.getElementById('kaloriInput').value);
        if (!val || val < 100) {
            showToast('Masukkan kalori minimal 100.', true);
            return;
        }
        try {
            const data = await apiFetch(`${window.mpApiBase}/kalori`, 'POST', {
                tanggal:     iso,
                max_calorie: val,
            });
            // Update serverData lokal
            if (!serverData[iso]) serverData[iso] = { meals: { SA: null, SI: null, MA: null }, total_kalori: 0 };
            serverData[iso].max_calorie = data.max_calorie;
            serverData[iso].planner_id  = data.planner_id;

            modal.classList.add('hidden');
            updateKaloriTracker();
            showToast('Target kalori berhasil disimpan!');
        } catch (e) {
            showToast('Gagal menyimpan: ' + e.message, true);
        }
    });
}

// Tutup modal kalori klik overlay
document.getElementById('kaloriModal')?.addEventListener('click', (e) => {
    if (e.target === document.getElementById('kaloriModal')) {
        document.getElementById('kaloriModal').classList.add('hidden');
    }
});

// Bind tombol atur awal
document.getElementById('kaloriAturBtnInit')?.addEventListener('click', () => bukaModalKalori(getActiveISO()));

// ─── Toast helper ─────────────────────────────────────────────────────
function showToast(msg, isError = false) {
    const old = document.querySelector('.mp-toast');
    if (old) old.remove();
    const t = document.createElement('div');
    t.className = 'mp-toast' + (isError ? ' mp-toast-error' : '');
    t.innerHTML = `<span class="material-icons-round">${isError ? 'error_outline' : 'check_circle'}</span> ${msg}`;
    document.body.appendChild(t);
    setTimeout(() => t?.remove(), 3500);
}

// ─── Date Range Picker ────────────────────────────────────────────────
const dateRangeBtn      = document.getElementById('dateRangeBtn');
const dateRangeDropdown = document.getElementById('dateRangeDropdown');
const dateRangeLabelEl  = document.getElementById('dateRangeLabel');
const calendarEl        = document.getElementById('dateRangeCalendar');

function formatLabel(d) {
    return `${d.getDate()} ${bulanNames[d.getMonth()]} ${d.getFullYear()}`;
}

function updateDateRangeLabel() {
    if (!dateRangeLabelEl) return;
    if (rangeStart && rangeEnd) {
        dateRangeLabelEl.textContent = toISO(rangeStart) === toISO(rangeEnd)
            ? formatLabel(rangeStart)
            : `${formatLabel(rangeStart)} – ${formatLabel(rangeEnd)}`;
    } else if (rangeStart) {
        dateRangeLabelEl.textContent = `${formatLabel(rangeStart)} – ...`;
    } else {
        dateRangeLabelEl.textContent = 'Pilih Rentang Tanggal';
    }
}

async function applyRange(start, end) {
    rangeStart = start;
    rangeEnd   = end;
    updateDateRangeLabel();

    // Bangun tanggal dalam range
    const dates = [];
    const cur   = new Date(start); cur.setHours(0,0,0,0);
    const endD  = new Date(end);   endD.setHours(0,0,0,0);
    while (cur <= endD) { dates.push(toISO(new Date(cur))); cur.setDate(cur.getDate()+1); }

    buildTabs(dates);
    await loadData(toISO(start), toISO(end));
}

function renderCalendar() {
    if (!calendarEl) return;
    if (calViewMonth == null) calViewMonth = new Date().getMonth();
    if (!calViewYear)         calViewYear  = new Date().getFullYear();

    const today       = todayISO();
    const firstDay    = new Date(calViewYear, calViewMonth, 1).getDay();
    const daysInMonth = new Date(calViewYear, calViewMonth + 1, 0).getDate();
    const startOffset = firstDay === 0 ? 6 : firstDay - 1;
    const dayNames    = ['Mo','Tu','We','Th','Fr','Sa','Su'];

    let html = `
        <div class="cal-nav flex flex-row">
            <button class="cal-nav-btn" id="calPrev"><span class="material-icons-round">chevron_left</span></button>
            <span class="font-jakarta font-semibold text-caption cal-month-label">${monthNames[calViewMonth]} ${calViewYear}</span>
            <button class="cal-nav-btn" id="calNext"><span class="material-icons-round">chevron_right</span></button>
        </div>
        <div class="cal-grid">
    `;
    dayNames.forEach(d => { html += `<span class="cal-day-name font-jakarta font-bold text-caption">${d}</span>`; });
    for (let i = 0; i < startOffset; i++) html += `<span class="cal-cell cal-empty"></span>`;

    for (let day = 1; day <= daysInMonth; day++) {
        const d   = new Date(calViewYear, calViewMonth, day);
        const iso = toISO(d);
        // REVISI 3: hari sebelum hari ini = disabled
        const isPast = iso < today;
        let cls = 'cal-cell font-jakarta text-body';
        if (isPast)      cls += ' cal-disabled';
        if (iso === today) cls += ' cal-today';

        if (!isPast && rangeStart && rangeEnd) {
            const ds = rangeStart.getTime(), de = rangeEnd.getTime(), dt = d.getTime();
            if (ds === dt || de === dt) cls += ' cal-selected';
            else if (dt > ds && dt < de) cls += ' cal-in-range';
        } else if (!isPast && rangeStart && toISO(rangeStart) === iso) {
            cls += ' cal-selected';
        }

        html += `<span class="${cls}" data-date="${iso}">${day}</span>`;
    }
    html += '</div>';
    calendarEl.innerHTML = html;

    document.getElementById('calPrev')?.addEventListener('click', (e) => {
        e.stopPropagation();
        if (calViewMonth-- === 0) { calViewMonth = 11; calViewYear--; }
        renderCalendar();
    });
    document.getElementById('calNext')?.addEventListener('click', (e) => {
        e.stopPropagation();
        if (++calViewMonth > 11) { calViewMonth = 0; calViewYear++; }
        renderCalendar();
    });

    calendarEl.querySelectorAll('.cal-cell:not(.cal-empty):not(.cal-disabled)').forEach(cell => {
        cell.addEventListener('click', async (e) => {
            e.stopPropagation();
            const clicked = parseISO(cell.dataset.date);
            if (calPickStep === 0 || (rangeStart && rangeEnd)) {
                rangeStart = clicked; rangeEnd = null; calPickStep = 1;
                updateDateRangeLabel(); renderCalendar();
            } else {
                if (clicked < rangeStart) { rangeEnd = rangeStart; rangeStart = clicked; }
                else { rangeEnd = clicked; }
                calPickStep = 0;
                dateRangeDropdown.classList.remove('open');
                await applyRange(rangeStart, rangeEnd);
            }
        });
    });
}

dateRangeBtn?.addEventListener('click', (e) => {
    e.stopPropagation();
    dateRangeDropdown.classList.toggle('open');
    if (dateRangeDropdown.classList.contains('open')) renderCalendar();
});
document.addEventListener('click', () => dateRangeDropdown?.classList.remove('open'));
dateRangeDropdown?.addEventListener('click', (e) => e.stopPropagation());

// ─── Preset buttons (REVISI 3: hapus kemarin & minggu lalu) ──────────
document.querySelectorAll('.preset-btn').forEach(btn => {
    btn.addEventListener('click', async (e) => {
        e.stopPropagation();
        const preset = btn.dataset.preset;
        const today  = new Date(); today.setHours(0,0,0,0);
        let s, end;

        if (preset === 'today') {
            s = end = new Date(today);
        } else if (preset === 'tomorrow') {
            s = end = new Date(today); s.setDate(today.getDate()+1); end = new Date(s);
        } else if (preset === 'thisweek') {
            const dow = today.getDay() === 0 ? 6 : today.getDay() - 1;
            s   = new Date(today); s.setDate(today.getDate() - dow);
            end = new Date(s); end.setDate(s.getDate() + 6);
            // Mulai dari hari ini kalau senin sudah lewat
            if (s < today) s = new Date(today);
        } else if (preset === 'next7') {
            s   = new Date(today);
            end = new Date(today); end.setDate(today.getDate() + 6);
        } else if (preset === 'thismonth') {
            s   = new Date(today);
            end = new Date(today.getFullYear(), today.getMonth()+1, 0);
        } else return;

        calPickStep = 0;
        dateRangeDropdown.classList.remove('open');
        await applyRange(s, end);
    });
});

document.getElementById('dateRangeReset')?.addEventListener('click', async (e) => {
    e.stopPropagation();
    rangeStart = rangeEnd = null; calPickStep = 0;
    updateDateRangeLabel();
    dateRangeDropdown.classList.remove('open');
    // Default balik ke hari ini
    const today = new Date(); today.setHours(0,0,0,0);
    await applyRange(today, today);
});

// ─── Init ─────────────────────────────────────────────────────────────
(async function init() {
    calViewMonth = new Date().getMonth();
    calViewYear  = new Date().getFullYear();
    // Default: tampilkan hari ini
    const today = new Date(); today.setHours(0,0,0,0);
    await applyRange(today, today);
})();