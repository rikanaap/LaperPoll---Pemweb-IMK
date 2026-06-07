// tambah-bahan.js

(function () {
    'use strict';

    // ── Parse data bahan dari blade ──────────────────────────────────────
    const bahanDataJson = document.getElementById('bahanData')?.textContent || '[]';
    let bahanList = [];
    try { bahanList = JSON.parse(bahanDataJson); } catch (e) { console.warn(e); }

    // ── Parse stok kulkas untuk notifikasi duplikat ──────────────────────
    const stokKulkasJson = document.getElementById('stokKulkasData')?.textContent || '{}';
    let stokKulkas = {};
    try { stokKulkas = JSON.parse(stokKulkasJson); } catch (e) { console.warn(e); }

    // ── Element refs ─────────────────────────────────────────────────────
    const searchInput      = document.getElementById('searchBahan');
    const dropdown         = document.getElementById('bahanDropdown');
    const clearBtn         = document.getElementById('clearSearch');
    const bahanIdInput     = document.getElementById('bahanId');
    const jumlahAngka      = document.getElementById('jumlahAngka');
    const boughtDateInput  = document.getElementById('boughtDate');
    const expiredDateInput = document.getElementById('expiredDate');
    const expiredChips     = document.getElementById('expiredChips');
    const expiredHint      = document.getElementById('expiredHint');
    const form             = document.getElementById('formTambahBahan');

    // Toggle tanggal
    const dateTypeToggleBtns = document.querySelectorAll('.tb-date-toggle-btn');
    const sectionBought  = document.getElementById('sectionBoughtDate');
    const sectionExpired = document.getElementById('sectionExpiredDate');

    let selectedBahan     = null;
    let defaultExpiryDays = null;
    let activeDateType    = 'bought'; // 'bought' | 'expired'

    // ── COUNTER (step 50 gram) ────────────────────────────────────────────
    document.getElementById('btnPlus')?.addEventListener('click', () => {
        const cur  = parseInt(jumlahAngka.value) || 0;
        const step = cur < 100 ? 10 : 50;
        jumlahAngka.value = cur + step;
        enforceBounds();
    });

    document.getElementById('btnMinus')?.addEventListener('click', () => {
        const cur  = parseInt(jumlahAngka.value) || 50;
        const step = cur <= 100 ? 10 : 50;
        jumlahAngka.value = Math.max(1, cur - step);
    });

    jumlahAngka?.addEventListener('input', enforceBounds);

    function enforceBounds() {
        const v = parseInt(jumlahAngka?.value);
        if (!v || v < 1) jumlahAngka.value = 1;
        if (v > 99999)   jumlahAngka.value = 99999;
    }

    // ── TOGGLE TIPE TANGGAL ───────────────────────────────────────────────
    const dateModeInput = document.getElementById('dateMode');

    dateTypeToggleBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            dateTypeToggleBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            activeDateType = btn.dataset.type;

            if (dateModeInput) dateModeInput.value = activeDateType === 'bought' ? 'beli' : 'expired';

            if (activeDateType === 'bought') {
                sectionBought.style.display  = '';
                sectionExpired.style.display = 'none';
                if (expiredDateInput) {
                    expiredDateInput.value = '';
                    const expiredTextEl = document.getElementById('expiredCalText');
                    if (expiredTextEl) { expiredTextEl.textContent = 'Pilih tanggal expired'; expiredTextEl.classList.remove('has-value'); }
                }
                if (boughtDateInput && !boughtDateInput.value) {
                    const todayISO = new Date().toISOString().split('T')[0];
                    boughtDateInput.value = todayISO;
                    if (boughtDateInput._calUpdate) boughtDateInput._calUpdate(todayISO);
                }
                setWrapHeight(false);
            } else {
                sectionBought.style.display  = 'none';
                sectionExpired.style.display = '';
                if (boughtDateInput) {
                    boughtDateInput.value = '';
                    const boughtTextEl = document.getElementById('boughtCalText');
                    if (boughtTextEl) { boughtTextEl.textContent = 'Pilih tanggal beli'; boughtTextEl.classList.remove('has-value'); }
                }
                if (selectedBahan?.has_expiry && defaultExpiryDays) {
                    showExpiredChips(defaultExpiryDays);
                    autoFillExpired(defaultExpiryDays);
                }
            }
        });
    });

    // ── DROPDOWN SEARCH ───────────────────────────────────────────────────
    function closeDropdown() {
        if (dropdown) dropdown.style.display = 'none';
    }

   function renderDropdown(results, query) {
    if (!dropdown) return;
    dropdown.innerHTML = '';

    results.forEach(b => {
        const li = document.createElement('li');
        li.textContent = b.nama;
        li.addEventListener('click', (e) => { e.stopPropagation(); selectBahan(b); });
        dropdown.appendChild(li);
    });

    // ── DESAIN BARU: ACTION ROW UNTUK BAHAN BARU ──
    if (results.length === 0 && query) {
        const li = document.createElement('li');
        li.className = 'tb-add-new';
        
        // Menggunakan struktur HTML baru yang lebih kokoh dan mudah di-styling via CSS
        li.innerHTML = `
            <span class="material-icons-round tb-add-icon">add_circle</span>
            <div class="tb-add-text-wrapper">
                <span class="tb-add-action-text">Tambah</span>
                <span class="tb-add-target-badge">"${query}"</span>
                <span class="tb-add-action-text">sebagai bahan baru</span>
            </div>
        `;
        
        li.addEventListener('click', (e) => { 
            e.stopPropagation(); 
            closeDropdown(); 
            showKonfirmasiModal(query); 
        });
        dropdown.appendChild(li);
    }

    dropdown.style.display = results.length > 0 || query ? 'block' : 'none';
}

    function selectBahan(bahan) {
        selectedBahan      = bahan;
        searchInput.value  = bahan.nama;
        bahanIdInput.value = bahan.id;
        if (clearBtn) clearBtn.style.display = 'block';
        closeDropdown();

        // ── NOTIFIKASI DUPLIKAT: cek apakah bahan sudah ada di kulkas ──
        const stokAda = parseInt(stokKulkas[bahan.id] ?? 0);
        const notifEl = document.getElementById('tbDuplikatNotif');
        if (stokAda > 0) {
            if (notifEl) {
                notifEl.innerHTML = `
                    <span class="material-icons-round">info_outline</span>
                    <span>Kamu sudah punya <strong>${stokAda} gram ${bahan.nama}</strong> di kulkas — ini akan ditambahkan sebagai pembelian baru.</span>
                `;
                notifEl.style.display = 'flex';
            }
        } else {
            if (notifEl) notifEl.style.display = 'none';
        }

        if (bahan.has_expiry && bahan.expired_expectancy_day) {
            defaultExpiryDays = bahan.expired_expectancy_day;
            if (expiredHint) expiredHint.textContent = `Rekomendasi: ${bahan.expired_expectancy_day} hari dari tanggal beli`;
            if (activeDateType === 'expired') {
                showExpiredChips(bahan.expired_expectancy_day);
                autoFillExpired(bahan.expired_expectancy_day);
            }
        } else {
            defaultExpiryDays = null;
            if (expiredChips) { expiredChips.innerHTML = ''; expiredChips.style.display = 'none'; }
            if (expiredHint) expiredHint.textContent = 'Isi tanggal kedaluwarsa bahan ini';
            setWrapHeight(false);
        }
    }

    // ── EXPIRED CHIPS ─────────────────────────────────────────────────────
    const dateSectionWrap = document.querySelector('.tb-date-section-wrap');

    function setWrapHeight(hasChips) {
        if (!dateSectionWrap) return;
        dateSectionWrap.style.minHeight = hasChips ? '12rem' : '9rem';
    }

    function showExpiredChips(defaultDays) {
        if (!expiredChips) return;
        expiredChips.style.display = 'flex';
        expiredChips.innerHTML = '';
        setWrapHeight(true);

        const daysArr = Array.from(new Set([defaultDays, 7, 14, 30])).sort((a, b) => a - b);
        daysArr.forEach(day => {
            const chip = document.createElement('button');
            chip.type      = 'button';
            chip.className = 'tb-chip-btn' + (day === defaultDays ? ' active' : '');
            chip.textContent = day === defaultDays ? `${day} hari (rekomendasi)` : `${day} hari`;
            chip.addEventListener('click', () => {
                expiredChips.querySelectorAll('.tb-chip-btn').forEach(c => c.classList.remove('active'));
                chip.classList.add('active');
                autoFillExpired(day);
            });
            expiredChips.appendChild(chip);
        });
    }

    function autoFillExpired(days) {
        if (!expiredDateInput) return;
        const base = new Date();
        base.setHours(0, 0, 0, 0);
        base.setDate(base.getDate() + days);
        const iso = base.toISOString().split('T')[0];
        expiredDateInput.value = iso;
        // Update custom calendar display if initialized
        if (expiredDateInput._calUpdate) expiredDateInput._calUpdate(iso);
    }

    // ── MODAL KONFIRMASI BAHAN BARU ───────────────────────────────────────
    function showKonfirmasiModal(nama) {
        let modal = document.getElementById('modalBahanBaru');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'modalBahanBaru';
            modal.innerHTML = `
                <div class="modal-overlay" id="modalOverlay"></div>
                <div class="modal-box">
                    <div class="modal-icon">
                        <span class="material-icons-round">help_outline</span>
                    </div>
                    <h3 class="modal-title font-jakarta font-bold" id="modalTitle"></h3>
                    <p class="modal-desc font-jakarta font-regular" id="modalDesc"></p>
                    <div class="modal-actions">
                        <button class="modal-btn-cancel font-jakarta font-semibold" id="modalCancel">Batal</button>
                        <button class="modal-btn-confirm font-jakarta font-bold" id="modalConfirm">Ya, Tambahkan</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            document.getElementById('modalCancel').addEventListener('click', hideModal);
            document.getElementById('modalOverlay').addEventListener('click', hideModal);
        }

        document.getElementById('modalTitle').textContent = `Tambah "${nama}" sebagai bahan baru?`;
        document.getElementById('modalDesc').textContent  = 'Bahan ini belum ada di database LaperPoll. Kamu tetap bisa menyimpannya ke kulkas.';

        const confirmBtn = document.getElementById('modalConfirm');
        const newConfirm = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirm, confirmBtn);
        newConfirm.addEventListener('click', () => simpanBahanBaru(nama));

        modal.style.display = 'flex';
    }

    function hideModal() {
        const modal = document.getElementById('modalBahanBaru');
        if (modal) modal.style.display = 'none';
    }

    function showToast(msg, isError = false) {
        const existing = document.querySelector('.tb-toast');
        if (existing) existing.remove();
        const toast = document.createElement('div');
        toast.className = 'tb-toast' + (isError ? ' toast-error' : '');
        toast.innerHTML = `<span class="material-icons-round">${isError ? 'error_outline' : 'check_circle'}</span> ${msg}`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3500);
    }

    async function simpanBahanBaru(nama) {
        const confirmBtn = document.getElementById('modalConfirm');
        if (confirmBtn) { confirmBtn.disabled = true; confirmBtn.textContent = 'Menyimpan...'; }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const res = await fetch('/api/bahans/baru', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ nama }),
            });

            if (!res.ok) throw new Error(`Server error ${res.status}`);

            const data = await res.json();
            hideModal();
            selectBahan(data);
            showToast(`"${data.nama}" berhasil ${data.status === 'created' ? 'ditambahkan ke database' : 'ditemukan'}`);
        } catch (err) {
            console.error(err);
            showToast('Gagal menyimpan bahan baru: ' + err.message, true);
        } finally {
            if (confirmBtn) { confirmBtn.disabled = false; confirmBtn.textContent = 'Ya, Tambahkan'; }
        }
    }

    // ── SEARCH INPUT EVENTS ───────────────────────────────────────────────
    searchInput?.addEventListener('input', (e) => {
        const q = e.target.value.trim();
        if (clearBtn) clearBtn.style.display = q ? 'block' : 'none';
        if (!q) { closeDropdown(); bahanIdInput.value = ''; selectedBahan = null; return; }
        const filtered = bahanList.filter(b => b.nama.toLowerCase().includes(q.toLowerCase())).slice(0, 8);
        renderDropdown(filtered, q);
    });

    clearBtn?.addEventListener('click', () => {
        searchInput.value  = '';
        bahanIdInput.value = '';
        clearBtn.style.display = 'none';
        closeDropdown();
        selectedBahan     = null;
        defaultExpiryDays = null;
        const notifEl = document.getElementById('tbDuplikatNotif');
        if (notifEl) notifEl.style.display = 'none';
        if (expiredChips)  { expiredChips.innerHTML = ''; expiredChips.style.display = 'none'; }
        if (expiredHint)   expiredHint.textContent = 'Isi tanggal kedaluwarsa bahan ini';
        searchInput.focus();
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.tb-search-wrap')) closeDropdown();
    });

    // ── VALIDASI SUBMIT ───────────────────────────────────────────────────
    form?.addEventListener('submit', (e) => {
        if (!bahanIdInput.value) {
            e.preventDefault();
            searchInput?.focus();
            searchInput?.classList.add('tb-input-error');
            showToast('Pilih nama bahan dari daftar terlebih dahulu.', true);
            return;
        }
        if (activeDateType === 'bought' && !boughtDateInput?.value) {
            e.preventDefault();
            boughtDateInput?.focus();
            showToast('Isi tanggal beli terlebih dahulu.', true);
            return;
        }
        if (activeDateType === 'expired' && !expiredDateInput?.value) {
            e.preventDefault();
            expiredDateInput?.focus();
            showToast('Isi tanggal expired terlebih dahulu.', true);
            return;
        }
    });

    // ── INIT ──────────────────────────────────────────────────────────────
    if (boughtDateInput && !boughtDateInput.value) {
        boughtDateInput.value = new Date().toISOString().split('T')[0];
    }
    // Sync calendar display after calendar IIFE also runs DOMContentLoaded
    setTimeout(() => {
        if (boughtDateInput?.value && boughtDateInput._calUpdate) {
            boughtDateInput._calUpdate(boughtDateInput.value);
        }
        if (expiredDateInput?.value && expiredDateInput._calUpdate) {
            expiredDateInput._calUpdate(expiredDateInput.value);
        }
    }, 50);

    if (bahanIdInput?.value && searchInput?.value) {
        const found = bahanList.find(b => b.id == bahanIdInput.value);
        if (found) { selectedBahan = found; defaultExpiryDays = found.expired_expectancy_day; }
    }

})();
// ── CUSTOM CALENDAR PICKER ────────────────────────────────────────────
(function() {
    'use strict';

    const BULAN = ['Januari','Februari','Maret','April','Mei','Juni',
                   'Juli','Agustus','September','Oktober','November','Desember'];
    const HARI  = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];

    function toISO(d) {
        return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
    }
    function formatDisplay(iso) {
        if (!iso) return '';
        const [y,m,d] = iso.split('-');
        return `${parseInt(d)} ${BULAN[parseInt(m)-1]} ${y}`;
    }
    function parseISO(iso) {
        const [y,m,d] = iso.split('-').map(Number);
        return new Date(y, m-1, d);
    }

    function createCalendar(opts) {
        // opts: { hiddenInputId, triggerId, textId, popupId, allowPast, minDate }
        const hiddenInput = document.getElementById(opts.hiddenInputId);
        const trigger     = document.getElementById(opts.triggerId);
        const textEl      = document.getElementById(opts.textId);
        const popup       = document.getElementById(opts.popupId);
        if (!hiddenInput || !trigger || !popup) return;

        const today   = new Date(); today.setHours(0,0,0,0);
        const todayISO = toISO(today);

        let curYear  = today.getFullYear();
        let curMonth = today.getMonth();
        let selected = hiddenInput.value ? hiddenInput.value : null;

        // If already has a value, set display
        if (selected) {
            textEl.textContent = formatDisplay(selected);
            textEl.classList.add('has-value');
        }

        function render() {
            const firstDay  = new Date(curYear, curMonth, 1).getDay(); // 0=Sun
            const daysInMon = new Date(curYear, curMonth+1, 0).getDate();
            // offset: Mon=0 start
            const offset = firstDay === 0 ? 6 : firstDay - 1;

            let html = `
                <div class="tb-cal-nav">
                    <button type="button" class="tb-cal-nav-btn" id="${opts.popupId}_prev">
                        <span class="material-icons-round">chevron_left</span>
                    </button>
                    <span class="tb-cal-month-label font-jakarta">${BULAN[curMonth]} ${curYear}</span>
                    <button type="button" class="tb-cal-nav-btn" id="${opts.popupId}_next">
                        <span class="material-icons-round">chevron_right</span>
                    </button>
                </div>
                <div class="tb-cal-grid">
            `;

            ['Sen','Sel','Rab','Kam','Jum','Sab','Min'].forEach(h => {
                html += `<span class="tb-cal-day-name font-jakarta">${h}</span>`;
            });

            for (let i = 0; i < offset; i++) html += `<span class="tb-cal-empty"></span>`;

            for (let day = 1; day <= daysInMon; day++) {
                const d   = new Date(curYear, curMonth, day);
                const iso = toISO(d);
                let cls   = 'tb-cal-cell font-jakarta';

                const isPast   = iso < todayISO;
                const isFuture = iso > todayISO;

                if (!opts.allowPast && isPast) {
                    cls += ' tb-cal-past';
                } else if (opts.futureOnly && !isFuture) {
                    cls += ' tb-cal-past';
                } else {
                    if (iso === todayISO) cls += ' tb-cal-today';
                    if (iso === selected)  cls += ' tb-cal-selected';
                }

                html += `<span class="${cls}" data-date="${iso}">${day}</span>`;
            }
            html += '</div>';
            popup.innerHTML = html;

            popup.querySelector(`#${opts.popupId}_prev`)?.addEventListener('click', e => {
                e.stopPropagation();
                if (--curMonth < 0) { curMonth = 11; curYear--; }
                render();
            });
            popup.querySelector(`#${opts.popupId}_next`)?.addEventListener('click', e => {
                e.stopPropagation();
                if (++curMonth > 11) { curMonth = 0; curYear++; }
                render();
            });

            popup.querySelectorAll('.tb-cal-cell:not(.tb-cal-past):not(.tb-cal-future-only)').forEach(cell => {
                cell.addEventListener('click', e => {
                    e.stopPropagation();
                    selected = cell.dataset.date;
                    hiddenInput.value = selected;
                    textEl.textContent = formatDisplay(selected);
                    textEl.classList.add('has-value');
                    closePopup();
                    // Trigger change event so tambah-bahan.js can react
                    hiddenInput.dispatchEvent(new Event('change'));
                });
            });
        }

        function openPopup() {
            // If current selected, show that month
            if (selected) {
                const d = parseISO(selected);
                curYear = d.getFullYear();
                curMonth = d.getMonth();
            }
            render();
            popup.style.display = '';
            trigger.classList.add('open');
        }

        function closePopup() {
            popup.style.display = 'none';
            trigger.classList.remove('open');
        }

        // Satu handler saja — hapus duplikat addEventListener sebelumnya
        let isOpen = false;
        trigger.onclick = (e) => {
            e.stopPropagation();
            isOpen = !isOpen;
            if (isOpen) { openPopup(); } else { closePopup(); }
            isOpen = popup.style.display !== 'none';
        };

        document.addEventListener('click', e => {
            if (!popup.contains(e.target) && e.target !== trigger && !trigger.contains(e.target)) {
                closePopup();
                isOpen = false;
            }
        });

        // expose for external update (e.g. when autoFillExpired is called)
        hiddenInput._calUpdate = (iso) => {
            selected = iso;
            textEl.textContent = formatDisplay(iso);
            textEl.classList.add('has-value');
        };
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Bought date calendar — allow past dates too
        createCalendar({
            hiddenInputId: 'boughtDate',
            triggerId:     'boughtCalTrigger',
            textId:        'boughtCalText',
            popupId:       'boughtCalPopup',
            allowPast:     true,
        });

        // Expired date calendar — future only
        createCalendar({
            hiddenInputId: 'expiredDate',
            triggerId:     'expiredCalTrigger',
            textId:        'expiredCalText',
            popupId:       'expiredCalPopup',
            allowPast:     false,
        });

        // Patch autoFillExpired to also update calendar display
        const origAutoFillExpired = window._tbAutoFillExpired;
    });

})();