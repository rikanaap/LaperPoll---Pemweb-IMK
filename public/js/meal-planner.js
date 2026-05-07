// ============================================================
//  MEAL PLANNER — JS utama
//  Features: tab hari + tanggal, date range picker, kalori tracker
// ============================================================

const labelHari = {
    sen: "Senin", sel: "Selasa", rab: "Rabu",
    kam: "Kamis", jum: "Jumat", sab: "Sabtu", min: "Minggu"
};
const hariList = ["sen", "sel", "rab", "kam", "jum", "sab", "min"];
const waktuList = ["sarapan", "siang", "malam"];

const iconWaktu  = { sarapan: "wb_sunny",    siang: "restaurant", malam: "bedtime" };
const labelWaktu = { sarapan: "SARAPAN",     siang: "MAKAN SIANG", malam: "MAKAN MALAM" };

// ─── Storage key helpers ─────────────────────────────────────
function mealKey(dateStr, waktu) { return `meal_${dateStr}_${waktu}`; }
function kaloriTargetKey() { return "kalori_target"; }

// ─── Date range state ────────────────────────────────────────
let rangeStart = null;   // Date object
let rangeEnd   = null;   // Date object
let calPickStep = 0;     // 0=pick start, 1=pick end
let calViewMonth, calViewYear;

// Tanggal untuk masing-masing tab hari (diset oleh initWeekDates)
const tabDates = {};  // { sen: "2026-05-04", sel: ..., ... }

function toISO(d) {
    return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
}
function parseISO(s) {
    const [y,m,d] = s.split('-').map(Number);
    return new Date(y, m-1, d);
}

// ─── Init: set tanggal tab sesuai minggu hari ini (atau range) ─
function initWeekDates(anchorDate) {
    // anchor ke Senin minggu anchor
    const d = anchorDate ? new Date(anchorDate) : new Date();
    d.setHours(0,0,0,0);
    const dow = d.getDay(); // 0=Sun
    const diffToMon = (dow === 0) ? -6 : 1 - dow;
    const monday = new Date(d);
    monday.setDate(d.getDate() + diffToMon);

    hariList.forEach((h, i) => {
        const day = new Date(monday);
        day.setDate(monday.getDate() + i);
        tabDates[h] = toISO(day);
        const el = document.getElementById(`date-${h}`);
        if (el) el.textContent = day.getDate();
    });
}

// ─── Render semua slot ───────────────────────────────────────
function renderSemuaSlot() {
    hariList.forEach(hari => {
        const container = document.getElementById(`content-${hari}`);
        if (!container) return;
        container.innerHTML = "";
        const dateStr = tabDates[hari];

        waktuList.forEach(waktu => {
            const key  = mealKey(dateStr, waktu);
            const data = localStorage.getItem(key);

            const section = document.createElement("div");
            section.className = "meal-section flex flex-col gap-2";

            if (data) {
                const resep = JSON.parse(data);
                const kalNum = parseInt(resep.kalori) || 0;
                section.innerHTML = `
                    <div class="meal-section-header flex flex-row">
                        <span class="material-icons-round meal-icon">${iconWaktu[waktu]}</span>
                        <p class="font-jakarta font-bold text-caption meal-label">${labelWaktu[waktu]}</p>
                        <button class="meal-action font-jakarta font-bold text-caption hapus-btn"
                            data-key="${key}">HAPUS</button>
                    </div>
                    <div class="meal-card flex flex-row gap-3">
                        <div class="meal-img-placeholder"></div>
                        <div class="meal-info flex flex-col gap-1">
                            <h2 class="font-jakarta font-semibold text-title2 text-secondary-normal">${resep.nama}</h2>
                            <p class="font-jakarta font-regular text-caption text-primary-darker">${resep.waktu} · ${resep.kalori}</p>
                        </div>
                    </div>
                `;
            } else {
                section.innerHTML = `
                    <div class="meal-section-header flex flex-row">
                        <span class="material-icons-round meal-icon">${iconWaktu[waktu]}</span>
                        <p class="font-jakarta font-bold text-caption meal-label">${labelWaktu[waktu]}</p>
                    </div>
                    <a href="${window.pilihResepUrl}?slot=${dateStr}-${waktu}" class="slot-kosong flex flex-row gap-3">
                        <span class="material-icons-round slot-kosong-icon">add_circle_outline</span>
                        <p class="font-jakarta font-medium text-body text-primary-darker">Tambah resep</p>
                    </a>
                `;
            }

            container.appendChild(section);
        });
    });

    // Bind hapus buttons
    document.querySelectorAll(".hapus-btn").forEach(btn => {
        btn.addEventListener("click", (e) => {
            const key = e.target.dataset.key;
            localStorage.removeItem(key);
            renderSemuaSlot();
            updateKaloriTracker();
            cekTombolGenerate();
        });
    });
}

// ─── Generate button ─────────────────────────────────────────
function cekTombolGenerate() {
    const generateBtn = document.querySelector(".generate-btn");
    if (!generateBtn) return;
    const activeTab = document.querySelector('input[name="hari"]:checked');
    if (!activeTab) return;
    const hariAktif = activeTab.id.replace("tab-", "");
    const dateStr   = tabDates[hariAktif];
    const adaData   = waktuList.some(w => localStorage.getItem(mealKey(dateStr, w)));
    generateBtn.style.opacity       = adaData ? "1"    : "0.5";
    generateBtn.style.pointerEvents = adaData ? "auto" : "none";
}

// ─── Kalori Tracker ──────────────────────────────────────────
function getKaloriTarget() {
    return parseInt(localStorage.getItem(kaloriTargetKey())) || 0;
}
function setKaloriTarget(val) {
    localStorage.setItem(kaloriTargetKey(), val);
}
function getKaloriHariIni() {
    const activeTab = document.querySelector('input[name="hari"]:checked');
    if (!activeTab) return 0;
    const hariAktif = activeTab.id.replace("tab-", "");
    const dateStr   = tabDates[hariAktif];
    let total = 0;
    waktuList.forEach(w => {
        const data = localStorage.getItem(mealKey(dateStr, w));
        if (data) {
            const resep = JSON.parse(data);
            total += parseInt(resep.kalori) || 0;
        }
    });
    return total;
}

function updateKaloriTracker() {
    const target = getKaloriTarget();
    const current = getKaloriHariIni();
    const nilaiEl = document.getElementById("kaloriNilai");
    const barFill = document.getElementById("kaloriBarFill");
    const alert   = document.getElementById("kaloriAlert");
    const tracker = document.getElementById("kaloriTracker");

    if (!nilaiEl) return;

    if (target === 0) {
        // Tampilkan tombol "Atur Kalori"
        tracker.innerHTML = `
            <button class="kalori-atur-btn flex flex-row gap-1" id="kaloriEditBtn2">
                <span class="material-icons-round">emoji_food_beverage</span>
                <span class="font-jakarta font-semibold text-caption">🔥 Atur Kalori Hari Ini!</span>
            </button>
        `;
        document.getElementById("kaloriEditBtn2")?.addEventListener("click", () => {
            document.getElementById("kaloriModal").classList.remove("hidden");
        });
        return;
    }

    // Restore full tracker HTML if it was replaced
    if (!nilaiEl || !document.getElementById("kaloriNilai")) return;
    nilaiEl.textContent = `${current}/${target}`;

    const pct = target > 0 ? Math.min((current / target) * 100, 100) : 0;
    barFill.style.width = pct + "%";

    const melebihi = current > target;
    barFill.classList.toggle("kalori-bar-over", melebihi);
    alert.classList.toggle("hidden", !melebihi);
}

// ─── Modal Kalori ─────────────────────────────────────────────
document.getElementById("kaloriEditBtn")?.addEventListener("click", () => {
    const modal = document.getElementById("kaloriModal");
    if (modal) {
        const t = getKaloriTarget();
        if (t) document.getElementById("kaloriInput").value = t;
        modal.classList.remove("hidden");
    }
});
document.getElementById("kaloriSubmit")?.addEventListener("click", () => {
    const val = parseInt(document.getElementById("kaloriInput").value);
    if (val && val > 0) {
        setKaloriTarget(val);
        document.getElementById("kaloriModal").classList.add("hidden");
        initKaloriTracker();
        updateKaloriTracker();
    }
});
document.getElementById("kaloriModal")?.addEventListener("click", (e) => {
    if (e.target === document.getElementById("kaloriModal")) {
        document.getElementById("kaloriModal").classList.add("hidden");
    }
});

function initKaloriTracker() {
    const target = getKaloriTarget();
    const tracker = document.getElementById("kaloriTracker");
    if (!tracker) return;

    if (target === 0) {
        tracker.innerHTML = `
            <button class="kalori-atur-btn flex flex-row gap-1" id="kaloriEditBtn2">
                <span class="material-icons-round">emoji_food_beverage</span>
                <span class="font-jakarta font-semibold text-caption">🔥 Atur Kalori Hari Ini!</span>
            </button>
        `;
        document.getElementById("kaloriEditBtn2")?.addEventListener("click", () => {
            document.getElementById("kaloriModal").classList.remove("hidden");
            document.getElementById("kaloriSubmit").onclick = function() {
                const val = parseInt(document.getElementById("kaloriInput").value);
                if (val && val > 0) {
                    setKaloriTarget(val);
                    document.getElementById("kaloriModal").classList.add("hidden");
                    restoreKaloriTracker();
                    updateKaloriTracker();
                }
            };
        });
    }
}

function restoreKaloriTracker() {
    const tracker = document.getElementById("kaloriTracker");
    if (!tracker) return;
    tracker.innerHTML = `
        <div class="kalori-row flex flex-row gap-2">
            <span class="kalori-nilai font-jakarta font-bold text-h5" id="kaloriNilai">0/0</span>
            <button class="kalori-edit-btn" id="kaloriEditBtn" title="Atur target kalori">
                <span class="material-icons-round">edit</span>
            </button>
        </div>
        <div class="kalori-bar-track">
            <div class="kalori-bar-fill" id="kaloriBarFill" style="width:0%"></div>
        </div>
        <div class="kalori-alert hidden flex flex-row gap-1" id="kaloriAlert">
            <span class="material-icons-round kalori-alert-icon">warning_amber</span>
            <span class="font-jakarta font-semibold text-caption">Melebihi Batas!</span>
        </div>
    `;
    document.getElementById("kaloriEditBtn")?.addEventListener("click", () => {
        const modal = document.getElementById("kaloriModal");
        if (modal) {
            const t = getKaloriTarget();
            if (t) document.getElementById("kaloriInput").value = t;
            modal.classList.remove("hidden");
        }
    });
}

// ─── Date Range Picker ────────────────────────────────────────
const dateRangeBtn      = document.getElementById("dateRangeBtn");
const dateRangeDropdown = document.getElementById("dateRangeDropdown");
const dateRangeLabel    = document.getElementById("dateRangeLabel");
const calendarEl        = document.getElementById("dateRangeCalendar");

function formatDisplayDate(d) {
    const months = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Ags","Sep","Okt","Nov","Des"];
    return `${d.getDate()} ${months[d.getMonth()]} ${String(d.getFullYear()).slice(-2)}`;
}

function updateDateRangeLabel() {
    if (rangeStart && rangeEnd) {
        dateRangeLabel.textContent = `${formatDisplayDate(rangeStart)} – ${formatDisplayDate(rangeEnd)}`;
    } else if (rangeStart) {
        dateRangeLabel.textContent = `${formatDisplayDate(rangeStart)} – ...`;
    } else {
        dateRangeLabel.textContent = "Pilih Rentang Tanggal";
    }
}

function renderCalendar() {
    const now = new Date();
    if (!calViewMonth && calViewMonth !== 0) calViewMonth = now.getMonth();
    if (!calViewYear) calViewYear = now.getFullYear();

    const monthNames = ["Januari","Februari","Maret","April","Mei","Juni",
                        "Juli","Agustus","September","Oktober","November","Desember"];
    const dayNames   = ["Mo","Tu","We","Th","Fr","Sa","Su"];

    const firstDay  = new Date(calViewYear, calViewMonth, 1).getDay();
    const daysInMonth = new Date(calViewYear, calViewMonth + 1, 0).getDate();
    const startOffset = firstDay === 0 ? 6 : firstDay - 1;

    let html = `
        <div class="cal-nav flex flex-row">
            <button class="cal-nav-btn" id="calPrev"><span class="material-icons-round">chevron_left</span></button>
            <span class="font-jakarta font-semibold text-caption cal-month-label">${monthNames[calViewMonth]} ${calViewYear}</span>
            <button class="cal-nav-btn" id="calNext"><span class="material-icons-round">chevron_right</span></button>
        </div>
        <div class="cal-grid">
    `;
    dayNames.forEach(d => { html += `<span class="cal-day-name font-jakarta font-bold text-caption">${d}</span>`; });

    for (let i = 0; i < startOffset; i++) {
        html += `<span class="cal-cell cal-empty"></span>`;
    }

    for (let day = 1; day <= daysInMonth; day++) {
        const d = new Date(calViewYear, calViewMonth, day);
        const iso = toISO(d);
        let cls = "cal-cell font-jakarta text-body";

        if (rangeStart && rangeEnd) {
            const ds = rangeStart.getTime(), de = rangeEnd.getTime(), dt = d.getTime();
            if (ds === dt || de === dt) cls += " cal-selected";
            else if (dt > ds && dt < de) cls += " cal-in-range";
        } else if (rangeStart && toISO(rangeStart) === iso) {
            cls += " cal-selected";
        }

        html += `<span class="${cls}" data-date="${iso}">${day}</span>`;
    }

    html += `</div>`;
    calendarEl.innerHTML = html;

    document.getElementById("calPrev")?.addEventListener("click", (e) => {
        e.stopPropagation();
        calViewMonth--;
        if (calViewMonth < 0) { calViewMonth = 11; calViewYear--; }
        renderCalendar();
    });
    document.getElementById("calNext")?.addEventListener("click", (e) => {
        e.stopPropagation();
        calViewMonth++;
        if (calViewMonth > 11) { calViewMonth = 0; calViewYear++; }
        renderCalendar();
    });

    calendarEl.querySelectorAll(".cal-cell:not(.cal-empty)").forEach(cell => {
        cell.addEventListener("click", (e) => {
            e.stopPropagation();
            const iso = cell.dataset.date;
            const clicked = parseISO(iso);
            if (calPickStep === 0 || (rangeStart && rangeEnd)) {
                rangeStart = clicked; rangeEnd = null; calPickStep = 1;
            } else {
                if (clicked < rangeStart) {
                    rangeEnd = rangeStart; rangeStart = clicked;
                } else {
                    rangeEnd = clicked;
                }
                calPickStep = 0;
                // Ketika range selesai → pindahkan tab ke minggu rangeStart
                initWeekDates(rangeStart);
                renderSemuaSlot();
                updateKaloriTracker();
                cekTombolGenerate();
                dateRangeDropdown.classList.remove("open");
            }
            updateDateRangeLabel();
            renderCalendar();
        });
    });
}

dateRangeBtn?.addEventListener("click", (e) => {
    e.stopPropagation();
    dateRangeDropdown.classList.toggle("open");
    if (dateRangeDropdown.classList.contains("open")) renderCalendar();
});
document.addEventListener("click", () => {
    dateRangeDropdown?.classList.remove("open");
});
dateRangeDropdown?.addEventListener("click", (e) => e.stopPropagation());

// Preset buttons
document.querySelectorAll(".preset-btn").forEach(btn => {
    btn.addEventListener("click", (e) => {
        e.stopPropagation();
        const preset = btn.dataset.preset;
        const today = new Date(); today.setHours(0,0,0,0);
        if (preset === "today") {
            rangeStart = rangeEnd = new Date(today);
        } else if (preset === "yesterday") {
            const y = new Date(today); y.setDate(today.getDate()-1);
            rangeStart = rangeEnd = y;
        } else if (preset === "thisweek") {
            const dow = today.getDay() === 0 ? 6 : today.getDay()-1;
            rangeStart = new Date(today); rangeStart.setDate(today.getDate()-dow);
            rangeEnd   = new Date(rangeStart); rangeEnd.setDate(rangeStart.getDate()+6);
        } else if (preset === "lastweek") {
            const dow = today.getDay() === 0 ? 6 : today.getDay()-1;
            const thisM = new Date(today); thisM.setDate(today.getDate()-dow);
            rangeStart = new Date(thisM); rangeStart.setDate(thisM.getDate()-7);
            rangeEnd   = new Date(thisM); rangeEnd.setDate(thisM.getDate()-1);
        } else if (preset === "thismonth") {
            rangeStart = new Date(today.getFullYear(), today.getMonth(), 1);
            rangeEnd   = new Date(today.getFullYear(), today.getMonth()+1, 0);
        }
        calPickStep = 0;
        updateDateRangeLabel();
        initWeekDates(rangeStart);
        renderSemuaSlot();
        updateKaloriTracker();
        cekTombolGenerate();
        dateRangeDropdown.classList.remove("open");
    });
});

document.getElementById("dateRangeReset")?.addEventListener("click", (e) => {
    e.stopPropagation();
    rangeStart = rangeEnd = null; calPickStep = 0;
    updateDateRangeLabel();
    initWeekDates(new Date());
    renderSemuaSlot();
    updateKaloriTracker();
    cekTombolGenerate();
    dateRangeDropdown.classList.remove("open");
});

// ─── Tab change handler ───────────────────────────────────────
document.querySelectorAll('input[name="hari"]').forEach(radio => {
    radio.addEventListener("change", () => {
        updateKaloriTracker();
        cekTombolGenerate();
    });
});

// ─── Init ─────────────────────────────────────────────────────
(function init() {
    initWeekDates(new Date());
    renderSemuaSlot();
    initKaloriTracker();
    updateKaloriTracker();
    cekTombolGenerate();
    updateDateRangeLabel();
    calViewMonth = new Date().getMonth();
    calViewYear  = new Date().getFullYear();
})();