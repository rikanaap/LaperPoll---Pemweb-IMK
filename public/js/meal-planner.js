// meal-planner.js — Full Rewrite
// Kompatibel dengan MealPlannerController versi kamu (SA/SI/MA, apiBase)
(function () {
'use strict';

// ─── Constants ───────────────────────────────────────────────
const WAKTU   = ['SA', 'SI', 'MA'];
const ICON    = { SA: 'wb_sunny', SI: 'restaurant', MA: 'bedtime' };
const LABEL   = { SA: 'SARAPAN', SI: 'MAKAN SIANG', MA: 'MAKAN MALAM' };
const HARI    = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
const BULAN   = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
const BULAN_F = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

// ─── State ───────────────────────────────────────────────────
let activeDates   = [];     // ['2026-05-12', ...]
let activeIdx     = 0;      // index tab aktif
let serverData    = {};     // { '2026-05-12': { planner_id, max_calorie, total_kalori, meals } }
let rangeStart    = null;
let rangeEnd      = null;
let calStep       = 0;      // 0 = pilih start, 1 = pilih end
let calMonth, calYear;

// ─── Utils ───────────────────────────────────────────────────
const toISO  = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
const pad    = n => String(n).padStart(2,'0');
const today  = () => { const d = new Date(); d.setHours(0,0,0,0); return d; };
const todayISO = () => toISO(today());
function parseISO(s) { const [y,m,d] = s.split('-').map(Number); return new Date(y,m-1,d); }
function datesInRange(s, e) {
    const r=[], c=new Date(s); c.setHours(0,0,0,0);
    const end=new Date(e); end.setHours(23,59,59);
    while(c<=end){ r.push(toISO(new Date(c))); c.setDate(c.getDate()+1); }
    return r;
}
function fmtDate(d) {
    return `${HARI[d.getDay()]}, ${d.getDate()} ${BULAN_F[d.getMonth()]} ${d.getFullYear()}`;
}
function fmtLabel(d) {
    return `${d.getDate()} ${BULAN[d.getMonth()]}`;
}
function fmtRangeLabel() {
    if (!rangeStart) return 'Pilih tanggal';
    if (!rangeEnd || toISO(rangeStart) === toISO(rangeEnd)) return fmtLabel(rangeStart);
    return `${fmtLabel(rangeStart)} – ${fmtLabel(rangeEnd)}`;
}
function formatDurasi(t) {
    if (!t) return null;
    const p = t.split(':').map(Number);
    const j = p[0]||0, m = p[1]||0;
    if (j>0&&m>0) return `${j}j ${m}mnt`;
    if (j>0) return `${j} jam`;
    if (m>0) return `${m} mnt`;
    return null;
}

// ─── API ─────────────────────────────────────────────────────
async function api(url, method='GET', body=null) {
    const opts = { method, headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': window.MP.csrf, 'Accept':'application/json' } };
    if (body) opts.body = JSON.stringify(body);
    const res = await fetch(url, opts);
    if (!res.ok) { const e = await res.json().catch(()=>({})); throw new Error(e.message||`HTTP ${res.status}`); }
    return res.json();
}

// ─── Load data dari server ────────────────────────────────────
async function loadData(start, end) {
    setLoading(true);
    try {
        const rows = await api(`${window.MP.apiBase}?start=${start}&end=${end}`);
        serverData = {};
        rows.forEach(r => { serverData[r.tanggal] = r; });
        renderTabs();
        renderContent();
        updateKaloriUI();
        updateGenerateBtn();
    } catch(e) {
        toast('Gagal memuat data: ' + e.message, true);
    } finally {
        setLoading(false);
    }
}

// ─── Tabs ─────────────────────────────────────────────────────
function renderTabs() {
    const wrap = document.getElementById('mpTabsWrap');
    const tabs = document.getElementById('mpTabs');
    if (!wrap || !tabs) return;

    if (!activeDates.length) { wrap.style.display = 'none'; return; }
    wrap.style.display = '';
    tabs.innerHTML = '';

    activeDates.forEach((iso, i) => {
        const d       = parseISO(iso);
        const isToday = iso === todayISO();
        const btn     = document.createElement('button');
        btn.className = 'mp-tab' + (i===activeIdx?' active':'') + (isToday?' today':'');
        btn.innerHTML = `
            <span class="mp-tab-day">${HARI[d.getDay()]}</span>
            <span class="mp-tab-date">${d.getDate()}</span>
            <span class="mp-tab-month">${BULAN[d.getMonth()]}</span>
        `;
        btn.addEventListener('click', () => switchTab(i));
        tabs.appendChild(btn);
    });
}

function switchTab(idx) {
    activeIdx = idx;
    document.querySelectorAll('.mp-tab').forEach((b,i) => b.classList.toggle('active', i===idx));
    renderContent();
    updateKaloriUI();
}

// ─── Content (3 slot meal) ────────────────────────────────────
function renderContent() {
    const content = document.getElementById('mpContent');
    const empty   = document.getElementById('mpEmpty');
    if (!content) return;

    if (!activeDates.length) {
        content.style.display = 'none';
        if (empty) empty.style.display = '';
        return;
    }

    content.style.display = '';
    if (empty) empty.style.display = 'none';
    content.innerHTML = '';

    const iso     = activeDates[activeIdx];
    const dayData = serverData[iso] || { meals: {} };

    WAKTU.forEach(w => {
        const meal    = dayData.meals?.[w] || null;
        const section = document.createElement('div');
        section.className = 'mp-meal-section';

        const header = `
            <div class="mp-meal-header">
                <span class="material-icons-round mp-meal-icon">${ICON[w]}</span>
                <span class="mp-meal-label font-jakarta font-bold">${LABEL[w]}</span>
                ${meal ? `<button class="mp-meal-hapus font-jakarta font-bold" data-detail-id="${meal.detail_id}" data-iso="${iso}" data-w="${w}" data-nama="${meal.nama || 'resep ini'}">HAPUS</button>` : ''}
            </div>
        `;

        let body = '';
        if (meal) {
            const dur = formatDurasi(meal.durasi);
            body = `
                <div class="mp-meal-card">
                    <div class="mp-meal-thumb">
                        ${meal.thumbnail
                            ? `<img src="${meal.thumbnail}" alt="${meal.nama}">`
                            : `<span class="material-icons-round">restaurant</span>`
                        }
                    </div>
                    <div class="mp-meal-info">
                        <p class="mp-meal-nama">${meal.nama}</p>
                        <div class="mp-meal-meta">
                            <span class="mp-meal-meta-item">
                                <span class="material-icons-round">local_fire_department</span>
                                ${meal.kalori} kal
                            </span>
                            ${dur ? `<span class="mp-meal-meta-item"><span class="material-icons-round">schedule</span>${dur}</span>` : ''}
                        </div>
                    </div>
                </div>
            `;
        } else {
            const href = `${window.MP.pilihResepUrl}?tanggal=${iso}&meal_time=${w}`;
            body = `
                <a href="${href}" class="mp-slot-kosong">
                    <div class="mp-slot-plus">
                        <span class="material-icons-round">add</span>
                    </div>
                    <span class="mp-slot-text font-jakarta font-medium">Tambah resep</span>
                </a>
            `;
        }

        section.innerHTML = header + body;
        content.appendChild(section);
    });

    // Bind hapus — tampilkan modal konfirmasi dulu sebelum DELETE
    content.querySelectorAll('.mp-meal-hapus').forEach(btn => {
        btn.addEventListener('click', () => {
            const detailId  = btn.dataset.detailId;
            const iso2      = btn.dataset.iso;
            const w2        = btn.dataset.w;
            const namaResep = btn.dataset.nama  || 'resep ini';
            const namaSlot  = LABEL[w2] || w2;

            openHapusModal(detailId, iso2, w2, namaResep, namaSlot);
        });
    });
}


// ─── Modal Konfirmasi Hapus Meal ─────────────────────────────
function openHapusModal(detailId, iso, w, namaResep, namaSlot) {
    // Buat modal konfirmasi inline
    let overlay = document.getElementById('mpHapusOverlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'mpHapusOverlay';
        overlay.style.cssText = 'position:fixed;inset:0;z-index:500;background:rgba(0,0,0,0.45);display:flex;align-items:center;justify-content:center;padding:1rem;';
        document.body.appendChild(overlay);
    }
    overlay.innerHTML = `
        <div style="background:white;border-radius:1.25rem;padding:1.5rem;max-width:340px;width:100%;box-shadow:0 8px 32px rgba(0,0,0,0.18);">
            <p style="font-size:.875rem;font-weight:700;color:#2D1A11;margin-bottom:.5rem;" class="font-jakarta">Hapus dari jadwal?</p>
            <p style="font-size:.8rem;color:#6B5B54;line-height:1.5;margin-bottom:1.25rem;" class="font-jakarta">
                <strong>${namaResep}</strong> akan dihapus dari <strong>${namaSlot}</strong>.
                Tindakan ini tidak dapat dibatalkan.
            </p>
            <div style="display:flex;gap:.6rem;justify-content:flex-end;">
                <button id="mpHapusBatal"
                    style="padding:.55rem 1rem;border-radius:.75rem;border:1.5px solid #E0D3CA;background:white;font-size:.82rem;font-weight:600;cursor:pointer;color:#6B5B54;"
                    class="font-jakarta">Batal</button>
                <button id="mpHapusOke"
                    style="padding:.55rem 1rem;border-radius:.75rem;border:none;background:#DC2626;color:white;font-size:.82rem;font-weight:600;cursor:pointer;"
                    class="font-jakarta">Hapus</button>
            </div>
        </div>`;
    overlay.style.display = 'flex';

    document.getElementById('mpHapusBatal').onclick = () => { overlay.style.display = 'none'; };
    overlay.onclick = (e) => { if (e.target === overlay) overlay.style.display = 'none'; };
    document.getElementById('mpHapusOke').onclick   = () => doHapus(detailId, iso, w, overlay);
}

async function doHapus(detailId, iso, w, overlay) {
    const okeBtn = document.getElementById('mpHapusOke');
    if (okeBtn) { okeBtn.disabled = true; okeBtn.textContent = '...'; }
    try {
        const res = await api(`${window.MP.apiBase}/detail/${detailId}`, 'DELETE');
        overlay.style.display = 'none';
        if (serverData[iso]?.meals) {
            serverData[iso].meals[w] = null;
            // FIX: gunakan total_kalori dari server jika tersedia, bukan hitung lokal
            if (res?.total_kalori !== undefined) {
                serverData[iso].total_kalori = res.total_kalori;
            } else {
                serverData[iso].total_kalori = WAKTU.reduce(
                    (s,x) => s + (serverData[iso].meals[x]?.kalori || 0), 0
                );
            }
        }
        renderContent();
        updateKaloriUI();
        updateGenerateBtn();
        toast('Resep dihapus dari jadwal');
    } catch(e) {
        overlay.style.display = 'none';
        toast('Gagal menghapus: ' + e.message, true);
    }
}

// ─── Kalori UI ────────────────────────────────────────────────
function updateKaloriUI() {
    if (!activeDates.length) return;
    const iso      = activeDates[activeIdx];
    const dayData  = serverData[iso] || {};
    const target   = dayData.max_calorie || 0;
    const current  = dayData.total_kalori || 0;

    const wrap     = document.getElementById('mpKaloriWrap');
    const setBtn   = document.getElementById('mpSetKaloriBtn');
    const cur      = document.getElementById('mpKaloriCurrent');
    const tgt      = document.getElementById('mpKaloriTarget');
    const bar      = document.getElementById('mpBarFill');
    const overEl   = document.getElementById('mpKaloriOver');

    if (!wrap || !setBtn) return;

    if (target > 0) {
        wrap.style.display    = '';
        setBtn.style.display  = 'none';
        if (cur) cur.textContent = current;
        if (tgt) tgt.textContent = `${target} kal`;

        const melebihi    = current > target;
        const barFill     = document.getElementById('mpBarFill');
        const barOverflow = document.getElementById('mpBarOverflow');
        const label       = document.getElementById('mpBarLabel');

        if (melebihi) {
            // Bar oranye selalu penuh (opacity 1 = 100%)
            if (barFill) { barFill.style.opacity = '1'; }

            // Overlay gelap dari kanan:
            // current = 1× target → overlay 0% (tidak ada)
            // current = 2× target → overlay 100% (full gelap)
            // Lebih dari 2× target → tetap 100%
            const overflowRatio = Math.min((current - target) / target, 1); // 0.0 – 1.0
            const overflowPct   = overflowRatio * 100;
            if (barOverflow) {
                barOverflow.style.display = '';
                barOverflow.style.width   = overflowPct + '%';
            }

            // Label kelebihan kalori di bawah bar
            const lebih = current - target;
            if (label) { label.textContent = '+' + lebih + ' kal melebihi target'; label.style.color = '#DC2626'; }
        } else {
            // Bar oranye ngisi sesuai persen terisi
            const pct = (current / target) * 100;
            if (barFill)     { barFill.style.opacity = pct / 100; }
            if (barOverflow) { barOverflow.style.display = 'none'; barOverflow.style.width = '0%'; }
            if (label)       { label.textContent = Math.round(pct) + '%'; label.style.color = '#E65100'; }
        }

        if (overEl) overEl.style.display = melebihi ? 'flex' : 'none';
    } else {
        wrap.style.display   = 'none';
        setBtn.style.display = '';
    }
}

// ─── Modal kalori ─────────────────────────────────────────────
function openModal() {
    if (!activeDates.length) return;
    const iso  = activeDates[activeIdx];
    const d    = parseISO(iso);
    const dateEl = document.getElementById('mpModalDate');
    if (dateEl) dateEl.textContent = fmtDate(d);

    const input = document.getElementById('mpKaloriInput');
    if (input) {
        input.value = serverData[iso]?.max_calorie || '';
        // highlight chip yang cocok
        document.querySelectorAll('.mp-chip').forEach(c => {
            c.classList.toggle('active', parseInt(c.dataset.val) === (serverData[iso]?.max_calorie||0));
        });
    }

    document.getElementById('mpModalOverlay').style.display = 'flex';
}

function closeModal() {
    document.getElementById('mpModalOverlay').style.display = 'none';
}

document.getElementById('mpKaloriEdit')?.addEventListener('click', openModal);
document.getElementById('mpSetKaloriBtn')?.addEventListener('click', openModal);
document.getElementById('mpModalCancel')?.addEventListener('click', closeModal);
document.getElementById('mpModalClose')?.addEventListener('click', closeModal);
document.getElementById('mpModalOverlay')?.addEventListener('click', e => {
    if (e.target === document.getElementById('mpModalOverlay')) closeModal();
});

// Stepper
document.getElementById('mpStepMinus')?.addEventListener('click', () => {
    const inp = document.getElementById('mpKaloriInput');
    const v   = Math.max(100, (parseInt(inp.value)||0) - 100);
    inp.value = v; syncChips(v);
});
document.getElementById('mpStepPlus')?.addEventListener('click', () => {
    const inp = document.getElementById('mpKaloriInput');
    const v   = Math.min(9999, (parseInt(inp.value)||0) + 100);
    inp.value = v; syncChips(v);
});
document.getElementById('mpKaloriInput')?.addEventListener('input', e => {
    syncChips(parseInt(e.target.value)||0);
});

// Chips
document.querySelectorAll('.mp-chip').forEach(c => {
    c.addEventListener('click', () => {
        const v = parseInt(c.dataset.val);
        const inp = document.getElementById('mpKaloriInput');
        if (inp) inp.value = v;
        syncChips(v);
    });
});
function syncChips(val) {
    document.querySelectorAll('.mp-chip').forEach(c => {
        c.classList.toggle('active', parseInt(c.dataset.val) === val);
    });
}

// Save
document.getElementById('mpModalSave')?.addEventListener('click', async () => {
    const val = parseInt(document.getElementById('mpKaloriInput')?.value);
    if (!val || val < 100) { toast('Masukkan minimal 100 kal', true); return; }

    const btn = document.getElementById('mpModalSave');
    btn.disabled = true; btn.innerHTML = '<span class="material-icons-round">hourglass_empty</span> Menyimpan...';

    try {
        const iso  = activeDates[activeIdx];
        const data = await api(`${window.MP.apiBase}/kalori`, 'POST', { tanggal: iso, max_calorie: val });
        if (!serverData[iso]) serverData[iso] = { meals:{}, total_kalori:0 };
        serverData[iso].max_calorie = data.max_calorie;
        serverData[iso].planner_id  = data.planner_id;
        closeModal();
        updateKaloriUI();
        toast('Target kalori disimpan!');
    } catch(e) {
        toast('Gagal: ' + e.message, true);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<span class="material-icons-round">check</span> Simpan Target';
    }
});

// ─── Generate nota ────────────────────────────────────────────
function updateGenerateBtn() {
    const btn = document.getElementById('mpGenerateBtn');
    if (!btn) return;
    const ada = activeDates.some(iso => WAKTU.some(w => serverData[iso]?.meals?.[w]));
    btn.style.opacity       = ada ? '1'    : '0.45';
    btn.style.pointerEvents = ada ? 'auto' : 'none';
}

document.getElementById('mpGenerateBtn')?.addEventListener('click', async () => {
    if (!activeDates.length) return;
    const start = activeDates[0];
    const end   = activeDates[activeDates.length-1];
    const btn   = document.getElementById('mpGenerateBtn');
    btn.style.opacity = '0.7';
    try {
        const data = await api(`${window.MP.apiBase}/generate-nota`, 'POST', { start, end });
        if (data.success) window.location.href = data.redirect;
    } catch(e) {
        toast('Gagal generate nota: ' + e.message, true);
    } finally {
        btn.style.opacity = '1';
    }
});

// ─── Date picker ──────────────────────────────────────────────
const dateRangeBtn = document.getElementById('dateRangeBtn');
const dropdown     = document.getElementById('mpDropdown');
const backdrop     = document.getElementById('mpBackdrop');

function openDropdown() {
    dropdown.style.display = '';
    backdrop.style.display = '';
    document.getElementById('dateRangeChevron')?.classList.add('open');
    document.getElementById('dateRangeBtn')?.classList.add('active');
    renderCalendar();
}
function closeDropdown() {
    dropdown.style.display = 'none';
    backdrop.style.display = 'none';
    document.getElementById('dateRangeChevron')?.classList.remove('open');
    document.getElementById('dateRangeBtn')?.classList.remove('active');
}

dateRangeBtn?.addEventListener('click', () => {
    dropdown.style.display === 'none' ? openDropdown() : closeDropdown();
});
backdrop?.addEventListener('click', closeDropdown);

// Presets
document.querySelectorAll('.mp-preset').forEach(btn => {
    btn.addEventListener('click', async () => {
        const t   = new Date(); t.setHours(0,0,0,0);
        let s, e;
        switch(btn.dataset.preset) {
            case 'today':    s=e=new Date(t); break;
            case 'tomorrow': s=new Date(t); s.setDate(t.getDate()+1); e=new Date(s); break;
            case 'next7':    s=new Date(t); e=new Date(t); e.setDate(t.getDate()+6); break;
            case 'thisweek': {
                const dow=t.getDay()===0?6:t.getDay()-1;
                s=new Date(t); s.setDate(t.getDate()-dow);
                if(s<t) s=new Date(t);
                e=new Date(s); e.setDate(s.getDate()+6);
                break;
            }
            case 'thismonth': s=new Date(t); e=new Date(t.getFullYear(),t.getMonth()+1,0); break;
            default: return;
        }
        calStep=0; closeDropdown();
        await applyRange(s, e);
    });
});

async function applyRange(s, e) {
    rangeStart  = s; rangeEnd = e || s;
    activeDates = datesInRange(rangeStart, rangeEnd);
    activeIdx   = 0;
    document.getElementById('dateRangeLabel').textContent = fmtRangeLabel();

    // Simpan range ke sessionStorage biar tidak hilang saat redirect ke pilih-resep
    try {
        sessionStorage.setItem('mp_range', JSON.stringify({
            start: toISO(rangeStart),
            end  : toISO(rangeEnd),
        }));
    } catch(_) {}

    await loadData(toISO(rangeStart), toISO(rangeEnd));
}

// Calendar
function renderCalendar() {
    const cal = document.getElementById('mpCalendar');
    if (!cal) return;
    const tISO      = todayISO();
    const firstDay  = new Date(calYear, calMonth, 1).getDay();
    const daysInMon = new Date(calYear, calMonth+1, 0).getDate();
    const offset    = firstDay===0 ? 6 : firstDay-1;
    const dayNames  = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];

    let html = `
        <div class="mp-cal-nav">
            <button class="mp-cal-nav-btn" id="mpCalPrev">
                <span class="material-icons-round">chevron_left</span>
            </button>
            <span class="mp-cal-month font-jakarta font-bold">${BULAN_F[calMonth]} ${calYear}</span>
            <button class="mp-cal-nav-btn" id="mpCalNext">
                <span class="material-icons-round">chevron_right</span>
            </button>
        </div>
        <div class="mp-cal-grid">
    `;
    dayNames.forEach(d => { html+=`<span class="mp-cal-day-name font-jakarta">${d}</span>`; });
    for(let i=0;i<offset;i++) html+=`<span class="mp-cal-empty"></span>`;

    for(let day=1; day<=daysInMon; day++) {
        const d   = new Date(calYear, calMonth, day);
        const iso = toISO(d);
        const isPast = iso < tISO;
        let cls = 'mp-cal-cell font-jakarta';
        if (isPast) { cls += ' mp-cal-disabled'; }
        else {
            if (iso===tISO) cls += ' mp-cal-today';
            if (rangeStart && rangeEnd) {
                const ds=rangeStart.getTime(), de=rangeEnd.getTime(), dt=d.getTime();
                if(ds===dt||de===dt) cls+=' mp-cal-selected';
                else if(dt>ds&&dt<de) cls+=' mp-cal-in-range';
            } else if (rangeStart && toISO(rangeStart)===iso) {
                cls += ' mp-cal-selected';
            }
        }
        html+=`<span class="${cls}" data-date="${iso}">${day}</span>`;
    }
    html += '</div>';
    cal.innerHTML = html;

    document.getElementById('mpCalPrev')?.addEventListener('click', e => {
        e.stopPropagation();
        if(--calMonth<0){calMonth=11;calYear--;} renderCalendar();
    });
    document.getElementById('mpCalNext')?.addEventListener('click', e => {
        e.stopPropagation();
        if(++calMonth>11){calMonth=0;calYear++;} renderCalendar();
    });

    cal.querySelectorAll('.mp-cal-cell:not(.mp-cal-disabled):not(.mp-cal-empty)').forEach(cell => {
        cell.addEventListener('click', async e => {
            e.stopPropagation();
            const clicked = parseISO(cell.dataset.date);
            if (calStep===0 || (rangeStart&&rangeEnd)) {
                rangeStart=clicked; rangeEnd=null; calStep=1;
                document.getElementById('mpCalHint').textContent = 'Ketuk tanggal akhir';
                renderCalendar();
            } else {
                if(clicked<rangeStart){ rangeEnd=rangeStart; rangeStart=clicked; }
                else rangeEnd=clicked;
                calStep=0;
                document.getElementById('mpCalHint').textContent = 'Ketuk tanggal mulai';
                closeDropdown();
                await applyRange(rangeStart, rangeEnd);
            }
        });
    });
}

// ─── Loading ──────────────────────────────────────────────────
function setLoading(show) {
    const l = document.getElementById('mpLoading');
    const c = document.getElementById('mpContent');
    if (l) l.style.display = show ? '' : 'none';
    if (c && show) c.style.display = 'none';
}

// ─── Toast ───────────────────────────────────────────────────
let toastTimer;
function toast(msg, isErr=false) {
    const el   = document.getElementById('mpToast');
    const icon = document.getElementById('mpToastIcon');
    const txt  = document.getElementById('mpToastMsg');
    if (!el) return;
    el.className   = 'mp-toast' + (isErr ? ' error' : '');
    if (icon) icon.textContent = isErr ? 'error_outline' : 'check_circle';
    if (txt)  txt.textContent  = msg;
    el.style.display = '';
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => { el.style.display='none'; }, 3200);
}

// ─── Cek kembali dari pilih-resep ────────────────────────────
async function cekReturnFromPilihResep() {
    const p = new URLSearchParams(window.location.search);
    if (!p.get('added')) return;

    const date  = p.get('date');
    const waktu = p.get('meal_time');
    window.history.replaceState({}, '', window.location.pathname);
    if (!date) return;

    // Restore range dari sessionStorage yang disimpan sebelum redirect
    try {
        const saved = sessionStorage.getItem('mp_range');
        if (saved) {
            const { start, end } = JSON.parse(saved);
            rangeStart = parseISO(start);
            rangeEnd   = parseISO(end);
        }
    } catch(_) {}

    // Fallback: pakai tanggal yang baru ditambah
    if (!rangeStart) { rangeStart = parseISO(date); rangeEnd = parseISO(date); }

    await applyRange(rangeStart, rangeEnd);

    // Aktifkan tab tanggal yang baru ditambah resepnya
    const idx = activeDates.indexOf(date);
    if (idx >= 0) switchTab(idx);
    toast(`Resep berhasil ditambahkan ke ${LABEL[waktu] || waktu}!`);
}

// ─── Empty state btn & kalori edit bind ──────────────────────
document.getElementById('mpEmptyCta')?.addEventListener('click', openDropdown);

// ─── Init ────────────────────────────────────────────────────
(async function init() {
    const now = new Date();
    calMonth  = now.getMonth();
    calYear   = now.getFullYear();

    // Cek dulu apakah balik dari pilih-resep (ada ?added=1)
    const p = new URLSearchParams(window.location.search);
    if (p.get('added')) {
        // Restore range dari sessionStorage, baru cek return
        try {
            const saved = sessionStorage.getItem('mp_range');
            if (saved) {
                const { start, end } = JSON.parse(saved);
                rangeStart = parseISO(start);
                rangeEnd   = parseISO(end);
            }
        } catch(_) {}
        await cekReturnFromPilihResep();
    } else {
        // Normal load — cek sessionStorage dulu, fallback ke hari ini
        let defaultStart = today(), defaultEnd = today();
        try {
            const saved = sessionStorage.getItem('mp_range');
            if (saved) {
                const { start, end } = JSON.parse(saved);
                const s = parseISO(start), e = parseISO(end);
                // Hanya restore kalau rangenya masih valid (tidak semua di masa lalu)
                if (e >= today()) {
                    defaultStart = s < today() ? today() : s;
                    // FIX: pastikan end tidak lebih kecil dari start setelah geser
                    defaultEnd   = e < defaultStart ? new Date(defaultStart) : e;
                }
            }
        } catch(_) {}
        await applyRange(defaultStart, defaultEnd);
    }
})();

})();